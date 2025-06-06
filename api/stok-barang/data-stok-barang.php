<?php
include_once "../config/connection.php";
$jenis = antiSQLInjection($_GET['jenis']);
if($jenis!="") $cond = " AND IDJenis='$jenis'";

$dataStok = array();
$dataMaterial = array();

$query = $db->get_results("SELECT * FROM tb_barang WHERE IDBarang IS NOT NULL $cond ORDER BY IDBarang ASC");
if($query){
    $i=0;
    foreach($query as $data){
        $i++;
        $jenis = $db->get_var("SELECT Nama FROM tb_jenis_material WHERE IDMaterial='".$data->IDJenis."'");
        array_push($dataStok,array("IDBarang"=>$data->IDBarang,"No"=>$i,"KodeBarang"=>$data->KodeBarang,"Nama"=>$data->Nama,"StokGudang"=>$data->StokGudang,"StokPurchasing"=>$data->StokPurchasing,"Jenis"=>$jenis));
    }
} 

$query = $db->get_results("SELECT * FROM tb_jenis_material ORDER BY Parent ASC");
if($query){
    $i=0;
    foreach($query as $data){
        $i++;
        if($data->Parent=="0"){
            $parent="ROOT";
        } else {
            $parent = $db->get_var("SELECT Nama FROM tb_jenis_material WHERE IDMaterial='".$data->Parent."'");
        }
        array_push($dataMaterial,array("IDMaterial"=>$data->IDMaterial,"No"=>$i,"Parent"=>$parent,"Nama"=>$data->Nama));
    }
}

$return = array("dataStok"=>$dataStok,"dataMaterial"=>$dataMaterial); 
echo json_encode($return);
