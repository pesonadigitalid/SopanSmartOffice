<?php
include_once "../config/connection.php";
$nama = antiSQLInjection($_POST['nama']);
$query = $db->query("INSERT INTO tb_departement SET NamaDepartement='$nama'");
if($query){
    echo "1";
} else {
    echo "0";
}