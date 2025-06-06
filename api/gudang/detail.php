<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_GET['id']);

$query = $db->get_row("SELECT * FROM tb_gudang WHERE IDGudang='$id' ORDER BY IDGudang ASC");
if($query){
    $return = array("id_gudang"=>$query->IDGudang,"nama"=>$query->Nama,"is_default"=>$query->IsDefault);
}
echo json_encode($return);
