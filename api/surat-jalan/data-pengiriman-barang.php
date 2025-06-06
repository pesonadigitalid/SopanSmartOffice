<?php
include_once "../config/connection.php";
$datestart = antiSQLInjection($_GET['datestart']);
$expstart = explode("/",$datestart);
$datestartchange = $expstart[2]."-".$expstart[1]."-".$expstart[0];    

$dateend = antiSQLInjection($_GET['dateend']);
$expend = explode("/",$dateend);
$dateendchange = $expend[2]."-".$expend[1]."-".$expend[0];

$kode_proyek = antiSQLInjection($_GET['kode_proyek']);

if($datestart!="" && $dateend!="") {
    $cond = "WHERE Tanggal BETWEEN '$datestartchange' AND '$dateendchange'";
    if($kode_proyek!=""){
        $cond .= "AND KodeProyek='$kode_proyek'";
    }
} else if($datestart!="") {
    $cond = "WHERE Tanggal='$datestartchange'";
    if($kode_proyek!=""){
        $cond .= "AND KodeProyek='$kode_proyek'";
    }
} else if($kode_proyek!="") {
    $cond = "WHERE KodeProyek='$kode_proyek'";
}

$query = $db->get_results("SELECT *,DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_pengiriman $cond ORDER BY IDPengiriman DESC");
if($query){
    $return = array();
    $i=0;
    foreach($query as $data){
        $i++;
        if($data->KodeProyek=="") $kodeProyek="UMUM"; else $kodeProyek=$data->KodeProyek;
        //if($data->Status=="0") $status="Tender"; else if($data->Status=="1") $status="Fail"; else if($data->Status=="2") $status="Process"; else $status="Complete";
        array_push($return,array("IDPengiriman"=>$data->IDPengiriman,"NoPengiriman"=>$data->NoPengiriman,"No"=>$i,"KodeProyek"=>$kodeProyek,"Tanggal"=>$data->TanggalID,"Total"=>$data->Total,"Status"=>$data->Status,"CreatedBy"=>$created));
    }
    echo json_encode($return);
} else {
    echo json_encode(array());
}
