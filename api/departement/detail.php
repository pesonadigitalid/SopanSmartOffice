<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_GET['id']);

$query = $db->get_row("SELECT * FROM tb_departement WHERE IDDepartement='$id' ORDER BY IDDepartement ASC");
if($query){
    $return = array("nama"=>$query->NamaDepartement);
}
echo json_encode($return);