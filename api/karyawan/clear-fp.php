<?php
include_once "../config/connection.php";

$idk = antiSQLInjection($_POST['idk']);

$query = $db->query("DELETE FROM tb_karyawan_finger WHERE IDKaryawan='$idk'");
if($query){
    echo "1";
} else {
    echo "0";
}