<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_POST['id']);
$parent = antiSQLInjection($_POST['parent']);
$nama = antiSQLInjection($_POST['nama']);
$isparent = antiSQLInjection($_POST['isparent']);

if($isparent=="") $isparent="0";

$query = $db->query("UPDATE tb_jenis_material SET Parent='$parent', Nama='$nama', IsParent='$isparent' WHERE IDMaterial='$id'");
if($query){
    echo "1";
} else {
    echo "0";
}