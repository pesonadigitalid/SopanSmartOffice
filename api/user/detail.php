<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_GET['id']);

$query = $db->get_row("SELECT * FROM tb_user WHERE IDUser='$id' ORDER BY IDUser ASC");
if($query){
    $return = array("nama"=>$query->Nama,"IDUser"=>$query->IDUser,"level_choosen"=>$query->Level,"statusUser"=>$query->Status,"emailusr"=>$query->Email,"notelp"=>$query->HP,"usrname"=>$query->Usernm);
}
echo json_encode($return);