<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_GET['id']);

$query = $db->get_row("SELECT * FROM tb_contact_category WHERE IDContactCategory='$id' ORDER BY IDContactCategory ASC");
if($query){
    $return = array("Nama"=>$query->Nama,"IDContactCategory"=>$query->IDContactCategory);
}
echo json_encode($return);