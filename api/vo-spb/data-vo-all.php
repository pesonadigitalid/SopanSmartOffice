<?php
include_once "../config/connection.php";
$datestart = antiSQLInjection($_GET['datestart']);
$expstart = explode("/", $datestart);
$datestartchange = $expstart[2] . "-" . $expstart[1] . "-" . $expstart[0];

$dateend = antiSQLInjection($_GET['dateend']);
$expend = explode("/", $dateend);
$dateendchange = $expend[2] . "-" . $expend[1] . "-" . $expend[0];

$kode_proyek = antiSQLInjection($_GET['kode_proyek']);

if ($datestart != "" && $dateend != "") {
    $cond = "WHERE Tanggal BETWEEN '$datestartchange' AND '$dateendchange' ";
} else if ($datestart != "") {
    $cond = "WHERE Tanggal='$datestartchange' ";
} else {
    $cond = "WHERE DATE_FORMAT(Tanggal,'%Y-%m') = '" . date("Y-m") . "' ";
}

if ($kode_proyek != "")
    $cond .= " AND IDProyek='$kode_proyek' ";

$query = $db->get_results("SELECT *,DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_proyek_vo $cond ORDER BY NoVO ASC");
$return = array();
if($query){
    $i=0;
    foreach($query as $data){
        $i++;
        $proyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='".$data->IDProyek."'");
        $proyek = $proyek->KodeProyek." / ".$proyek->Tahun." / ".$proyek->NamaProyek;
        array_push($return,array("IDVO"=>$data->IDVO,"IDProyek"=>$data->IDProyek,"NoVO"=>$data->NoVO,"No"=>$i,"Tanggal"=>$data->TanggalID,"Keterangan"=>$data->Keterangan,"NilaiVO"=>$data->NilaiVO,"NilaiAkhirProyek"=>$data->NilaiAkhirProyek,"Proyek"=>$proyek));
    }
}

$query = $db->get_results("SELECT * FROM tb_proyek ORDER BY Tahun ASC, KodeProyek ASC");
$proyek = array();
if($query){
    foreach($query as $data){
        array_push($proyek, array("IDProyek"=>$data->IDProyek,"Nama"=>$data->KodeProyek."/".$data->Tahun."/".$data->NamaProyek));
    }
}

echo json_encode(array("data"=>$return,"proyek"=>$proyek));