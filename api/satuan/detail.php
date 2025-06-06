<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_GET['id']);

$query = $db->get_row("SELECT * FROM tb_satuan WHERE IDSatuan='$id' ORDER BY IDSatuan ASC");
if($query){
    $return = array("id_satuan"=>$query->IDSatuan,"nama"=>$query->Nama);
}
echo json_encode($return);