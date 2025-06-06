<?php
include_once "../config/connection.php";
$bulan = antiSQLInjection($_GET['bulan']);
$tahun = antiSQLInjection($_GET['tahun']);
$status = antiSQLInjection($_GET['status']);
$return = array();

$cond = "WHERE IDReimburse>0 ";

if($bulan!="" || $tahun!=""){
    if($bulan!="") $cond .= " AND DATE_FORMAT(Tanggal, '%m') = '$bulan'";
    //if($bulan!="" && $tahun!="") $cond .= " AND";
    if($tahun!="") $cond .= " AND DATE_FORMAT(Tanggal, '%Y') = '$tahun'";
}

if($status!="") $cond2="AND Status='$status'";

$query = $db->get_results("SELECT * FROM tb_reimburse $cond $cond2 ORDER BY IDReimburse ASC");
if($query){
    $i=1;
    foreach($query as $data){
        $category = $db->get_var("SELECT Nama FROM tb_asset_category WHERE IDAssetCategory='".$data->IDAssetCategory."'");
        $karyawan = $db->get_row("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='".$data->IDKaryawan."'");
        if($data->Status=="1") $status="Approved"; else if($data->Status=="2") $status="Terbayar"; else $status="Baru";
        array_push($return,array("IDReimburse"=>$data->IDReimburse,"NoReimburse"=>$data->NoReimburse,"NoKendaraan"=>$data->NoKendaraan,"No"=>$i,"Kategori"=>$data->Kategori,"TotalNilai"=>$data->TotalNilai,"Status"=>$status,"Karyawan"=>$karyawan->Nama,"Bank"=>$karyawan->NamaBank1,"NoRek"=>$karyawan->NoRekening1));
        $i++;
    }
}

$all = $db->get_var("SELECT COUNT(*) FROM tb_reimburse $cond "); if(!$all) $all='';
$new = $db->get_var("SELECT COUNT(*) FROM tb_reimburse $cond AND Status='0'"); if(!$new) $new='';
$approved = $db->get_var("SELECT COUNT(*) FROM tb_reimburse $cond AND Status='1'"); if(!$approved) $approved='';
$completed = $db->get_var("SELECT COUNT(*) FROM tb_reimburse $cond AND Status='2'"); if(!$completed) $completed='';

echo json_encode(array("data" => $return,"all"=>$all,"new"=>$new,"approved"=>$approved,"completed"=>$completed));