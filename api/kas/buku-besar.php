<?php
include_once "../config/connection.php";

$act = antiSQLInjection($_GET['act']);
switch ($act) {
        
    case "DataList":
        $type = antiSQLInjection($_GET['status']);
        $return = array();

        $dariTanggal = antiSQLInjection($_GET['datestart']);
        $sampaiTanggal = antiSQLInjection($_GET['dateend']);

        if($dariTanggal==""){
            $tgl = date("d");
            $bulan = date("m");
            $tahun = date("Y");
            $lastDay = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
            $dariTanggal = "01/$bulan/$tahun";
            $dariTanggalEN = "$tahun-$bulan-01";
            $sampaiTanggal = "$lastDay/$bulan/$tahun";
            $sampaiTanggalEN = "$tahun-$bulan-$lastDay";
            $condDate = " Tanggal BETWEEN '$dariTanggalEN' AND '$sampaiTanggalEN' ";
        } else {
            $exp = explode("/",$dariTanggal);
            $dariTanggalEN = $exp[2]."-".$exp[1]."-".$exp[0];
            $tgl = $exp[0];
            $bulan = $exp[1];
            $tahun = $exp[2];
            if($sampaiTanggal!=""){
                $exp = explode("/",$sampaiTanggal);
                $sampaiTanggalEN = $exp[2]."-".$exp[1]."-".$exp[0];
                $condDate = " Tanggal BETWEEN '$dariTanggalEN' AND '$sampaiTanggalEN' ";
            } else {
                $condDate = " Tanggal = '$dariTanggalEN'";
            }
        }
        $tanggalID = $dariTanggal;

        $cond = " AND a.IDRekening='112'";


        function getSaldoAwal($tanggal,$bulan,$tahun,$idRekening,$db){
            $batasan = $tahun."-01-01";
            if($tanggal!=""){
                $p = $tahun."-".$bulan."-".$tanggal;
                $cond = " AND DATE_FORMAT(Tanggal,'%Y-%m-%d') < '$p' AND DATE_FORMAT(Tanggal,'%Y-%m-%d') > '$batasan'";
            } else {
                $p = $tahun."-".$bulan;
                $cond = " AND DATE_FORMAT(Tanggal,'%Y-%m') < '$p' AND DATE_FORMAT(Tanggal,'%Y-%m-%d') > '$batasan'";
            }
            
                
            $dataRekening = $db->get_row("SELECT * FROM tb_master_rekening WHERE IDRekening='$idRekening'");
            $saldoAwal = $db->get_row("SELECT * FROM tb_saldo_awal WHERE IDRekening='$idRekening' and Tahun='$tahun'");
            
            if($saldoAwal) $saldoAwal=$saldoAwal->SaldoAwal; else $saldoAwal=0;
            $kredit=0;
            $debet=0;
            
            $debet = $db->get_var("SELECT SUM(Debet) FROM tb_jurnal_detail WHERE IDRekening='$idRekening' $cond");
            if(!$debet) $debet=0;
            $kredit = $db->get_var("SELECT SUM(Kredit) FROM tb_jurnal_detail WHERE IDRekening='$idRekening' $cond");
            if(!$kredit) $kredit=0;
            
            
            if($dataRekening->Posisi=='Debet'){
                $closing = $saldoAwal+$debet-$kredit;
            } else {
                $closing = $saldoAwal-$debet+$kredit;
            }

            return $closing;
        }

        $query = $db->get_results("SELECT a.*, b.Nama FROM tb_master_rekening a, tb_currency b WHERE a.IDCurrency=b.IDCurrency AND a.Tipe='D' $cond ORDER BY KodeRekening ASC");
        if($query){
            foreach($query as $data){
                if($data->IDCurrency>1) $add = " (".$data->Nama.")"; else $add="";
                
                $dataRekening = $db->get_row("SELECT * FROM tb_master_rekening WHERE IDRekening='".$data->IDRekening."'");
                $debet = getSaldoAwal($tgl,$bulan,$tahun,$data->IDRekening,$db);
                
                $debetK=0;
                $kreditK=0;
                $qRest = $db->get_results("SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail WHERE $condDate AND IDRekening='".$data->IDRekening."' ORDER BY Tanggal ASC, JurnalRef ASC");
                if($qRest){
                    foreach($qRest as $dRest){
                        $i++;
                        $debetK+=$dRest->Debet;
                        $kreditK+=$dRest->Kredit;
                        $dJurnal = $db->get_row("SELECT * FROM tb_jurnal WHERE IDJurnal='".$dRest->IDJurnal."'");
                    }
                }
                $closing = $debetK-$kreditK;
                $saldo = $debet;
                if($closing!=0 || $debet!=0){
                    $i=1;
                    array_push($return, array("No"=>$i,"Tanggal"=>$tanggalID,"Keterangan"=>"Saldo Awal","Debet"=>number_format($debet,2),"Kredit"=>number_format(0,2),"Closing"=>number_format($saldo,2)));
                    
                    $kredit=0;
                    $qRest = $db->get_results("SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail WHERE $condDate AND IDRekening='".$data->IDRekening."' ORDER BY Tanggal ASC, JurnalRef ASC");
                    if($qRest){
                        foreach($qRest as $dRest){
                            $i++;
                            $debet+=$dRest->Debet;
                            $kredit+=$dRest->Kredit;
                            $dJurnal = $db->get_row("SELECT * FROM tb_jurnal WHERE IDJurnal='".$dRest->IDJurnal."'");
                            $saldo = ($saldo+$dRest->Debet-$dRest->Kredit);

                            array_push($return, array("No"=>$i,"Tanggal"=>$dRest->TanggalID,"Keterangan"=>$dRest->Keterangan,"Debet"=>number_format($dRest->Debet,2),"Kredit"=>number_format($dRest->Kredit,2),"Closing"=>number_format($saldo,2)));
                        }
                    }
                    $closing = $debet-$kredit;
                }
            }
        }
        
        echo json_encode(array("data_buku_besar" => $return,"debet" => number_format($debet,2),"kredit" => number_format($kredit,2),"closing" => number_format($closing,2)));
        break;

    default:
        echo "";
}