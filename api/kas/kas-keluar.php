<?php
include_once "../config/connection.php";

$act = antiSQLInjection($_GET['act']);
switch ($act) {
        
    case "DataList":
        $type = antiSQLInjection($_GET['status']);
        $return = array();

        $datestart = antiSQLInjection($_GET['datestart']);
        $expstart = explode("/",$datestart);
        $datestartchange = $expstart[2]."-".$expstart[1]."-".$expstart[0];

        $dateend = antiSQLInjection($_GET['dateend']);
        $expend = explode("/",$dateend);
        $dateendchange = $expend[2]."-".$expend[1]."-".$expend[0];

        if ($datestart != "" && $dateend != "") {
            $cond = "WHERE Tanggal BETWEEN '$datestartchange' AND '$dateendchange'";
        } else if ($datestart != "") {
            $cond = "WHERE Tanggal='$datestartchange'";
        } else {
            $cond = "WHERE DATE_FORMAT(Tanggal,'%Y-%m') = '" . date("Y-m") . "'";
        }

        $query = $db->get_results("SELECT *,DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_kas_keluar $cond ORDER BY NoBukti ASC");
        if($query){
            $i=0;
            foreach($query as $data){
                $i++;
                $created = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='".$data->CreatedBy."'");
                array_push($return,array("IDKasKeluar"=>$data->IDKasKeluar,"NoBukti"=>$data->NoBukti,"No"=>$i,"Tanggal"=>$data->TanggalID,"Jumlah"=>$data->Jumlah,"Keterangan"=>$data->Keterangan,"DariRekening"=>$data->DariRekening,"KeRekening"=>$data->KeRekening,"ContactPerson"=>$data->ContactPerson,"CreatedBy"=>$created));
            }
        }
        
        echo json_encode(array("pembayaran" => $return));
        break;

    case "LoadAllRequirement":
        $dariRekening = array();
        $keRekening = array();

        $query = $db->get_results("SELECT * FROM tb_master_rekening WHERE (IDParent='73' OR IDParent IN (SELECT IDRekening FROM tb_master_rekening WHERE IDParent='73')) AND Tipe='D'");
        if($query){
            foreach($query as $data){
                array_push($keRekening, array("IDRekening"=>$data->IDRekening,"KodeRekening"=>$data->KodeRekening,"NamaRekening"=>$data->NamaRekening));
            }
        }

        $query = $db->get_results("SELECT * FROM tb_master_rekening WHERE IDRekening='112' AND Tipe='D'");
        if($query){
            foreach($query as $data){
                array_push($dariRekening, array("IDRekening"=>$data->IDRekening,"KodeRekening"=>$data->KodeRekening,"NamaRekening"=>$data->NamaRekening));
            }
        }

        $return = array("dariRekening"=>$dariRekening,"keRekening"=>$keRekening);
        echo json_encode($return);
        break;

    case "InsertNew":
        $tanggal = antiSQLInjection($_POST['tanggal']);
        $exp = explode("/",$tanggal);
        $tanggal = $exp[2]."-".$exp[1]."-".$exp[0];
        $tanggalCond = $exp[2]."-".$exp[1];
        $tanggalCond2 = "/SPN/".$exp[2]."/".$exp[1]."/";

        $jumlah_pembayaran = antiSQLInjection($_POST['jumlah_pembayaran']);
        $darirekening = antiSQLInjection($_POST['darirekening']);
        $kerekening = antiSQLInjection($_POST['kerekening']);
        $keterangan = antiSQLInjection($_POST['keterangan']);
        $cp = antiSQLInjection($_POST['cp']);

        $dataLast = $db->get_row("SELECT * FROM tb_kas_keluar WHERE DATE_FORMAT(Tanggal,'%Y-%m')='".$tanggalCond."' ORDER BY NoBukti DESC");
        if($dataLast){
            $last = substr($dataLast->NoBukti,-3);
            $last++;
            if($last<100 and $last>=10)
                $last = "0".$last;
            else if($last<10)
                $last = "00".$last;
            $nobukti = "KK".$tanggalCond2.$last;
        } else {
            $nobukti = "KK".$tanggalCond2."001";
        }

        $query = $db->query("INSERT INTO tb_kas_keluar SET NoBukti='$nobukti', Tanggal='$tanggal', Jumlah='$jumlah_pembayaran', Keterangan='$keterangan', DariRekening='$darirekening', KeRekening='$kerekening', CreatedBy='".$_SESSION["uid"]."', ContactPerson='$cp'");
        if($query){
            echo "1";
            $id = $db->get_var("SELECT LAST_INSERT_ID()");

            $dataLast = newQuery("get_row","SELECT * FROM tb_jurnal WHERE DATE_FORMAT(Tanggal,'%Y-%m')='".$tanggalCond."' ORDER BY NoJurnal DESC");
            if($dataLast) $last = substr($dataLast->NoJurnal,-5); else $last=0;
            do{
                $last++;
                if($last<10000 and $last>=1000)
                    $last = "0".$last;
                else if($last<1000 and $last>=100)
                    $last = "00".$last;
                else if($last<100 and $last>=10)
                    $last = "000".$last;
                else if($last<10)
                    $last = "0000".$last;
                $noTransaksi = "01-".$_GET['tahun'].$_GET['bulan'].$last;
                $checkNoTransaksi = newQuery("get_row","SELECT * FROM tb_jurnal WHERE NoJurnal='$noTransaksi'");
            } while($checkNoTransaksi);
            
            $db->query("INSERT INTO tb_jurnal SET NoJurnal='$noTransaksi', NoBukti='$nobukti', NoRef='$id', Tanggal='$tanggal', Debet='$jumlah_pembayaran', Kredit='$jumlah_pembayaran', CreatedBy='".$_SESSION["uid"]."', DateModified=NOW(), Keterangan='$keterangan', IDProyek='', Tipe='0'");
            $id = $db->get_var("SELECT LAST_INSERT_ID()");

            //Save to Jurnal Detail
            $db->query("INSERT INTO tb_jurnal_detail SET IDJurnal='$id', IDRekening='$kerekening', JurnalRef='$nobukti', Tanggal='$tanggal', Pos='Debet', Keterangan='$keterangan', Debet='$jumlah_pembayaran', Kredit='0', Closing='0', MataUang='1', Kurs='0'");

            $db->query("INSERT INTO tb_jurnal_detail SET IDJurnal='$id', IDRekening='$darirekening', JurnalRef='$nobukti', Tanggal='$tanggal', Pos='Kredit', Keterangan='$keterangan', Debet='0', Kredit='$jumlah_pembayaran', Closing='0', MataUang='1', Kurs='0'");
        } else {
            echo "0";
        }
        break;
        
    case "Delete":
        $idr = antiSQLInjection($_POST['idr']);
        $data = $db->get_row("SELECT * FROM tb_kas_keluar WHERE IDKasKeluar='$idr'");

        $db->query("DELETE FROM tb_kas_keluar WHERE IDKasKeluar='$idr'");
        $db->query("DELETE FROM tb_jurnal_detail WHERE JurnalRef='".$data->NoBukti."'");
        $db->query("DELETE FROM tb_jurnal WHERE NoBukti='".$data->NoBukti."'");

        echo "1";
        break;

    case "loadData":
        $id = antiSQLInjection($_GET['id']);

        $dariRekening = array();
        $keRekening = array();
        $detail = array();

        $query = $db->get_results("SELECT * FROM tb_master_rekening WHERE (IDParent='73' OR IDParent IN (SELECT IDRekening FROM tb_master_rekening WHERE IDParent='73')) AND Tipe='D'");
        if($query){
            foreach($query as $data){
                array_push($keRekening, array("IDRekening"=>$data->IDRekening,"KodeRekening"=>$data->KodeRekening,"NamaRekening"=>$data->NamaRekening));
            }
        }

        $query = $db->get_results("SELECT * FROM tb_master_rekening WHERE IDRekening='112' AND Tipe='D'");
        if($query){
            foreach($query as $data){
                array_push($dariRekening, array("IDRekening"=>$data->IDRekening,"KodeRekening"=>$data->KodeRekening,"NamaRekening"=>$data->NamaRekening));
            }
        }

        $data = $db->get_row("SELECT *,DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_kas_keluar WHERE NoBukti='$id'");
        if($data){
            $detail = array("IDKasKeluar"=>$data->IDKasKeluar,"NoBukti"=>$data->NoBukti,"No"=>$i,"Tanggal"=>$data->TanggalID,"Jumlah"=>$data->Jumlah,"Keterangan"=>$data->Keterangan,"DariRekening"=>$data->DariRekening,"KeRekening"=>$data->KeRekening,"ContactPerson"=>$data->ContactPerson);
        }

        $return = array("dariRekening"=>$dariRekening,"keRekening"=>$keRekening,"detail"=>$detail);
        echo json_encode($return);
        break;

    case "Edit":
        $tanggal = antiSQLInjection($_POST['tanggal']);
        $exp = explode("/",$tanggal);
        $tanggal = $exp[2]."-".$exp[1]."-".$exp[0];

        $jumlah_pembayaran = antiSQLInjection($_POST['jumlah_pembayaran']);
        $darirekening = antiSQLInjection($_POST['darirekening']);
        $kerekening = antiSQLInjection($_POST['kerekening']);
        $keterangan = antiSQLInjection($_POST['keterangan']);
        $cp = antiSQLInjection($_POST['cp']);

        $nobukti = antiSQLInjection($_POST['no_bukti']);

        $query = $db->query("UPDATE tb_kas_keluar SET Tanggal='$tanggal', Jumlah='$jumlah_pembayaran', Keterangan='$keterangan', DariRekening='$darirekening', KeRekening='$kerekening', CreatedBy='".$_SESSION["uid"]."', ContactPerson='$cp' WHERE NoBukti='$nobukti'");
        if($query){
            echo "1";
            
            $db->query("UPDATE tb_jurnal SET Tanggal='$tanggal', Debet='$jumlah_pembayaran', Kredit='$jumlah_pembayaran', CreatedBy='".$_SESSION["uid"]."', DateModified=NOW(), Keterangan='$keterangan', IDProyek='', Tipe='0' WHERE NoBukti='$nobukti'");

            //Save to Jurnal Detail
            $db->query("UPDATE tb_jurnal_detail SET IDRekening='$kerekening', Tanggal='$tanggal', Pos='Debet', Keterangan='$keterangan', Debet='$jumlah_pembayaran', Kredit='0', Closing='0', MataUang='1', Kurs='0' WHERE JurnalRef='$nobukti'");

            $db->query("UPDATE tb_jurnal_detail SET IDRekening='$darirekening', Tanggal='$tanggal', Pos='Kredit', Keterangan='$keterangan', Debet='0', Kredit='$jumlah_pembayaran', Closing='0', MataUang='1', Kurs='0' WHERE JurnalRef='$nobukti'");
        } else {
            echo "0";
        }
        break;
    
    default:
        echo "";
}