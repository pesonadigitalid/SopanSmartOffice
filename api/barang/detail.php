<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_GET['id']);

$query = $db->get_row("SELECT * FROM tb_barang WHERE IDBarang='$id' ORDER BY IDBarang ASC");
if($query){
    $return = array("kode_barang"=>$query->KodeBarang,"nama"=>$query->Nama,"kategori"=>$query->Kategori,"jenis"=>$query->IDJenis,"supplier"=>$query->IDSupplier,"harga"=>$query->Harga);
}
echo json_encode($return);