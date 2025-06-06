<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_POST['id']);
$nama = antiSQLInjection($_POST['nama']);

if($isparent=="") $isparent="0";

$query = $db->query("UPDATE tb_contact_category SET Nama='$nama' WHERE IDContactCategory='$id'");
if($query){
    echo "1";
} else {
    echo "0";
}