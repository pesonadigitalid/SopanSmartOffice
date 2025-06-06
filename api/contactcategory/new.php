<?php
include_once "../config/connection.php";

$nama = antiSQLInjection($_POST['nama']);
$query = $db->query("INSERT INTO tb_contact_category SET Nama='$nama'");
if($query){
    echo "1";
} else {
    echo "0";
}