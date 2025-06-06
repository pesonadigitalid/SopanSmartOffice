<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_POST['id']);
$nama = antiSQLInjection($_POST['nama']);
$query = $db->query("UPDATE tb_departement SET NamaDepartement='$nama' WHERE IDDepartement='$id'");
if($query){
    echo "1";
} else {
    echo "0";
}