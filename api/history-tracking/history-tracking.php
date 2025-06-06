<?php
session_start();
include_once "../config/connection.php";

$act = antiSQLInjection($_GET['act']);
switch ($act) {
    case "DataList":
        $datestart = antiSQLInjection($_GET['datestart']);
        $expstart = explode("/",$datestart);
        $datestartchange = $expstart[2]."-".$expstart[1]."-".$expstart[0];

        $dateend = antiSQLInjection($_GET['dateend']);
        $expend = explode("/",$dateend);
        $dateendchange = $expend[2]."-".$expend[1]."-".$expend[0];
        
        $serial_number = antiSQLInjection($_GET['serial_number']);

        if ($datestart != "" && $dateend != "") {
            $cond = "WHERE TanggalPerbaikan BETWEEN '$datestartchange' AND '$dateendchange'";
        } else if ($datestart != "") {
            $cond = "WHERE TanggalPerbaikan='$datestartchange'";
        } else {
            $cond = "WHERE DATE_FORMAT(TanggalPerbaikan,'%Y-%m') = '" . date("Y-m") . "'";
        }
        
        if($serial_number!="") $cond .= " AND SerialNumber='$serial_number'";
        
        $query = $db->get_results("SELECT *, DATE_FORMAT(TanggalPerbaikan, '%d/%m/%Y') AS TanggalPerbaikanID FROM tb_history_tracking $cond ORDER BY IDHistoryTracking ASC");
        if($query){
            $i=0;
            $return = array();
            foreach($query as $data){                 
                $i++;
                $barang = $db->get_row("SELECT *, DATE_FORMAT(Garansi, '%d/%m/%Y') AS GaransiID FROM tb_penjualan_surat_jalan_detail WHERE SN='".$data->SerialNumber."' ORDER BY IDetail");
                array_push($return,array("IDHistoryTracking"=>$data->IDHistoryTracking,"TanggalPerbaikan"=>$data->TanggalPerbaikanID,"SerialNumber"=>$data->SerialNumber,"DetailPerbaikan"=>$data->DetailPerbaikan,"NamaBarang"=>$barang->NamaBarang,"Garansi"=>$barang->GaransiID,"No"=>$i));
            }
            echo json_encode($return);
        } else {
            echo json_encode(array());
        }
    break;
    
    case "NewRecord":
        $tgl_perbaikan = antiSQLInjection($_POST['tgl_perbaikan']);
        $exp = explode("/",$tgl_perbaikan);
        $tgl_perbaikan = $exp[2]."-".$exp[1]."-".$exp[0];
        
        $serial_number = antiSQLInjection($_POST['serial_number']);
        $detail_perbaikan = antiSQLInjection($_POST['detail_perbaikan']);
        
        $cek = $db->get_row("SELECT * FROM tb_penjualan_surat_jalan_detail WHERE SN='$serial_number'");
        if($cek){
            $idSuratJalan = $db->get_var("SELECT IDSuratJalan FROM tb_penjualan_surat_jalan WHERE NoSuratJalan='".$cek->NoSuratJalan."'");
            $query = $db->query("INSERT INTO tb_history_tracking SET IDSuratJalan='$idSuratJalan', SerialNumber='$serial_number', DetailPerbaikan='$detail_perbaikan', TanggalPerbaikan='$tgl_perbaikan', CreatedBy='".$_SESSION["uid"]."'");
            if($query){
                echo "1";
            } else {
                echo "0";
            }
        } else {
            echo "2";
        }
    break;
    
    case "LoadAllRequirement":
        $karyawanArray = array();
        
        $query = $db->get_results("SELECT * FROM tb_karyawan ORDER BY Nama ASC");
        if($query){
            $i=0;
            foreach($query as $data){
                $i++;
                array_push($karyawanArray,array("IDKaryawan"=>$data->IDKaryawan,"Nama"=>$data->Nama,"No"=>$i));
            }
        }
        $return = array("karyawanArray"=>$karyawanArray);
        echo json_encode($return);
    break;
    
    case "Detail":
        $id = $_GET['id'];
        $query = $db->get_row("SELECT *, DATE_FORMAT(TanggalPerbaikan, '%d/%m/%Y') AS TanggalPerbaikanID FROM tb_history_tracking WHERE IDHistoryTracking='$id' ORDER BY IDHistoryTracking ASC");
        if($query){
            $return = array("tgl_perbaikan"=>$query->TanggalPerbaikanID,"serial_number"=>$query->SerialNumber,"detail_perbaikan"=>$query->DetailPerbaikan);
        }
        echo json_encode($return);
    break;
    
    case "EditRecord":
        $id = antiSQLInjection($_POST['id']);
        
        $tgl_perbaikan = antiSQLInjection($_POST['tgl_perbaikan']);
        $exp = explode("/",$tgl_perbaikan);
        $tgl_perbaikan = $exp[2]."-".$exp[1]."-".$exp[0];
        
        $detail_perbaikan = antiSQLInjection($_POST['detail_perbaikan']);
        
        $query = $db->query("UPDATE tb_history_tracking SET DetailPerbaikan='$detail_perbaikan', TanggalPerbaikan='$tgl_perbaikan' WHERE IDHistoryTracking='$id'");
        if($query){
            echo "1";
        } else {
            echo "0";
        }
    break;
    
    case "DeleteRecord":
        $idr = antiSQLInjection($_POST['idr']);
        $query = $db->query("DELETE FROM tb_history_tracking WHERE IDHistoryTracking='$idr'");
        if($query){
            echo "1";
        } else {
            echo "0";
        }
    break;
    default:
        echo "";
}