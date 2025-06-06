<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_GET['id']);

$query = $db->get_row("SELECT * FROM tb_jabatan WHERE IDJabatan='$id' ORDER BY IDJabatan ASC");
if($query){
    $return = array("id_jabatan"=>$query->IDJabatan,"nama"=>$query->Jabatan);
}
echo json_encode($return);