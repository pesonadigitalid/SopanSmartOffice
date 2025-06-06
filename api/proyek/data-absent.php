<?php
include_once "../config/connection.php";
$datestart = antiSQLInjection($_GET['datestart']);
$expstart = explode("/", $datestart);
$datestartchange = $expstart[2] . "-" . $expstart[1] . "-" . $expstart[0];

$dateend = antiSQLInjection($_GET['dateend']);
$expend = explode("/", $dateend);
$dateendchange = $expend[2] . "-" . $expend[1] . "-" . $expend[0];

$kode_proyek = antiSQLInjection($_GET['kode_proyek']);
$karyawan = antiSQLInjection($_GET['karyawan']);

if ($datestart != "" && $dateend != "") {
    $cond = "WHERE DATE_FORMAT(Datetime,'%Y-%m-%d') BETWEEN '$datestartchange' AND '$dateendchange' ";
} else if ($datestart != "") {
    $cond = "WHERE DATE_FORMAT(Datetime,'%Y-%m-%d')='$datestartchange' ";
} else {
    $cond = "WHERE DATE_FORMAT(Datetime,'%Y-%m') = '" . date("Y-m") . "' ";
}

if ($kode_proyek != "")
    $cond .= " AND IDProyek='$kode_proyek' ";

if($karyawan != "")
    $cond .= " AND IDKaryawan='$karyawan' ";

$query = $db->get_results("SELECT *, DATE_FORMAT(`Datetime`,'%Y-%m-%d') AS Tanggal, DATE_FORMAT(Datetime, '%d/%m/%Y') AS TanggalAbsent FROM tb_proyek_absent $cond GROUP BY Tanggal, IDKaryawan, IDProyek");
$return = array();
if($query){
    $i=0;
    foreach($query as $data){
        $i++;
        $proyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='".$data->IDProyek."'");
        $karyawan = $db->get_row("SELECT * FROM tb_karyawan WHERE IDKaryawan='".$data->IDKaryawan."'");
        $JamAbsen = $db->get_var("SELECT DATE_FORMAT(Datetime, '%H:%i:%s') FROM tb_proyek_absent WHERE DATE_FORMAT(`Datetime`,'%Y-%m-%d')='".$data->Tanggal."' AND IDKaryawan='".$data->IDKaryawan."' AND IDProyek='".$data->IDProyek."' AND Jenis='1' ORDER BY IDAbsent DESC LIMIT 0,1");
        $JamAbsenPulang = $db->get_var("SELECT DATE_FORMAT(Datetime, '%H:%i:%s') FROM tb_proyek_absent WHERE DATE_FORMAT(`Datetime`,'%Y-%m-%d')='".$data->Tanggal."' AND IDKaryawan='".$data->IDKaryawan."' AND IDProyek='".$data->IDProyek."' AND Jenis='2' ORDER BY IDAbsent DESC LIMIT 0,1");
        array_push($return,array("No"=>$i,"TanggalAbsent"=>$data->TanggalAbsent,"Karyawan"=>$karyawan->Nama,"Proyek"=>$proyek->KodeProyek."/".$proyek->Tahun."/".$proyek->NamaProyek,"JamAbsen"=>$JamAbsen,"JamAbsenPulang"=>$JamAbsenPulang));
    }
}

$query = $db->get_results("SELECT * FROM tb_karyawan WHERE Status='1' AND IDKaryawan>1 ORDER BY Nama ASC");
$karyawan = array();
if($query){
    foreach($query as $data){
        array_push($karyawan, array("IDKaryawan"=>$data->IDKaryawan,"Nama"=>$data->Nama));
    }
}

$query = $db->get_results("SELECT * FROM tb_proyek ORDER BY Tahun ASC, KodeProyek ASC");
$proyek = array();
if($query){
    foreach($query as $data){
        array_push($proyek, array("IDProyek"=>$data->IDProyek,"Nama"=>$data->KodeProyek."/".$data->Tahun."/".$data->NamaProyek));
    }
}

echo json_encode(array("data"=>$return,"karyawan"=>$karyawan,"proyek"=>$proyek));