<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_POST['id']);
$nama = antiSQLInjection($_POST['nama']);

$query = $db->query("UPDATE tb_jabatan SET Jabatan='$nama' WHERE IDJabatan='$id'");
if($query){
    echo "1";
} else {
    echo "0";
}