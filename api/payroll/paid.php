<?php
include_once "../config/connection.php";
$idr = antiSQLInjection($_POST['idr']);

$query = $db->query("UPDATE tb_slip_gaji SET Status='1' WHERE IDSlipGaji='$idr'");
if($query){
    echo "1";
    $id = $idr;
    //export slip gaji
    include_once '../export/export-slip-gaji.php';
} else {
    echo "0";
}