<?php
include_once "../config/connection.php";
$id_karyawan = antiSQLInjection($_GET['id_karyawan']);

$query = $db->query("UPDATE tb_karyawan SET Foto='' WHERE IDKaryawan='$id_karyawan'");
if($query){
    echo "1";
} else {
    echo "0";
}