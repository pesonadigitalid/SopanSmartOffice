<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_GET['id']);

$query = $db->get_row("SELECT * FROM tb_jenis_material WHERE IDMaterial='$id' ORDER BY IDMaterial ASC");
if($query){
    $return = array("parent"=>$query->Parent,"nama"=>$query->Nama,"isparent"=>$query->IsParent);
}
echo json_encode($return);