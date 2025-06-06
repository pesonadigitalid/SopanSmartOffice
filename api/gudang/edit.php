<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_POST['id']);
$nama = antiSQLInjection($_POST['nama']);
$is_default = antiSQLInjection($_POST['is_default']);
$is_default = !is_null($is_default) ? $is_default : 0;

if($is_default == 1) {
    $db->query("UPDATE tb_gudang SET IsDefault='0'");
}

$query = $db->query("UPDATE tb_gudang SET Nama='$nama', IsDefault='$is_default' WHERE IDGudang='$id'");
if($query){
    echo "1";
} else {
    echo "0";
}
