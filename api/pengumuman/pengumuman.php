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

        if ($datestart != "" && $dateend != "") {
            $cond = "WHERE DATE_FORMAT(DateCreated,'%Y-%m-%d') BETWEEN '$datestartchange' AND '$dateendchange'";
        } else if ($datestart != "") {
            $cond = "WHERE DATE_FORMAT(DateCreated,'%Y-%m-%d')='$datestartchange'";
        } else {
            $cond = "WHERE DATE_FORMAT(DateCreated,'%Y-%m') = '" . date("Y-m") . "'";
        }
        
        $query = $db->get_results("SELECT *,DATE_FORMAT(DateCreated, '%d/%m/%Y') AS DateCreatedID FROM tb_pengumuman $cond ORDER BY IDPengumuman DESC");
        if($query){
            $i=0;
            $return = array();
            foreach($query as $data){                 
                $i++;
                array_push($return,array("IDPengumuman"=>$data->IDPengumuman,"Judul"=>$data->Judul,"Keterangan"=>$data->Keterangan,"DateCreated"=>$data->DateCreatedID,"No"=>$i));
            }
            echo json_encode($return);
        } else {
            echo json_encode(array());
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
    
    case "NewRecord":
        $judul = antiSQLInjection($_POST['judul']);
        $keterangan = antiSQLInjection($_POST['keterangan']);
        
        $query = $db->query("INSERT INTO tb_pengumuman SET Judul='$judul', Keterangan='$keterangan', CreatedBy='".$_SESSION["uid"]."'");
        if($query){
            echo "1";
        } else {
            echo "0";
        }
    break;
    
    case "DeleteRecord":
        $idr = antiSQLInjection($_POST['idr']);
        $query = $db->query("DELETE FROM tb_pengumuman WHERE IDPengumuman='$idr'");
        if($query){
            echo "1";
        } else {
            echo "0";
        }
    break;
    default:
        echo "";
}