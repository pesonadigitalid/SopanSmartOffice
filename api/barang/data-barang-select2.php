<?php
session_start();
include_once "../config/connection.php";
$q = antiSQLInjection($_GET['q']);
$id = antiSQLInjection($_GET['id']);

$i=0;
$return = array();

$query = $db->get_results("SELECT * FROM tb_barang WHERE IDBarang!='$id' AND (KodeBarang LIKE '%$q%' OR Nama LIKE '%$q%')");
if($query){
    foreach($query as $data){
        array_push($return,array("id"=>$i,"text"=>$data->KodeBarang." - ".$data->Nama,"Kode"=>$data->KodeBarang,"Nama"=>$data->Nama,"IDBarang"=>$data->IDBarang,"Harga"=>$data->TotalHarga,"Kategori"=>"Paket","USD"=>"0","Tipe"=>1,"Biaya"=>$data->BasicHarga));
        $i++;
    }
}
/*
$query = $db->get_results("SELECT a.*, b.Nama AS Kategori FROM tb_activity a, tb_activity_category b WHERE a.IDActivityCategory=b.IDActivityCategory AND (a.Kode LIKE '%$q%' OR a.Nama LIKE '%$q%') ORDER BY a.IDActivity ASC limit 0, 10");
if($query){
    foreach($query as $data){
        array_push($return,array("id"=>$i,"text"=>$data->Kode." - ".$data->Nama,"Kode"=>$data->Kode,"Nama"=>$data->Nama,"IDActivity"=>"1".$data->IDActivity,"Harga"=>$data->Harga,"Kategori"=>$data->Kategori,"USD"=>$data->USD,"Tipe"=>2));
        $i++;
    }
}
*/
echo json_encode($return);

//$id = substr($id,1);