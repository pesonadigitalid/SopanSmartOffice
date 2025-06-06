<?php
include_once "../config/connection.php";
$jenis = antiSQLInjection($_GET['jenis']);
if($jenis!="") $cond = " AND IDJenis='$jenis'";
$query = $db->get_results("SELECT * FROM tb_barang WHERE IDBarang IS NOT NULL $cond ORDER BY IDBarang ASC");
if($query){
    $return = array();
    $i=0;
    foreach($query as $data){
        $i++;
        $jenis = $db->get_var("SELECT Nama FROM tb_jenis_material WHERE IDMaterial='".$data->IDJenis."'");
        array_push($return,array("IDBarang"=>$data->IDBarang,"No"=>$i,"KodeBarang"=>$data->KodeBarang,"Nama"=>$data->Nama,"StokGudang"=>$data->StokGudang,"StokPurchasing"=>$data->StokPurchasing,"Jenis"=>$jenis));
    }
    echo json_encode($return);
} else {
    echo json_encode(array());
}
