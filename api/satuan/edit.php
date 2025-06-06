<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_POST['id']);
$nama = antiSQLInjection($_POST['nama']);

$query = $db->query("UPDATE tb_satuan SET Nama='$nama' WHERE IDSatuan='$id'");
if($query){
    echo "1";
} else {
    echo "0";
}