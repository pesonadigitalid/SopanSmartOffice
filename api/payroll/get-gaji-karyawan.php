<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_GET['id']);

$query = $db->get_row("SELECT * FROM tb_gaji_karyawan WHERE IDKaryawan='$id' AND Status='1' ORDER BY IDGaji ASC");
if($query){
    $return = array("gaji_pokok"=>$query->GajiPokok,"uang_makan"=>$query->UangMakan,"uang_transport"=>$query->UangTransport);
}
echo json_encode($return);