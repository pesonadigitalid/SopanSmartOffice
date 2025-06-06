<?php
include_once "../config/connection.php";

$nama = antiSQLInjection($_POST['nama']);
$is_default = antiSQLInjection($_POST['is_default']);
$is_default = !is_null($is_default) ? $is_default : 0;

if($is_default == 1) {
    $db->query("UPDATE tb_gudang SET IsDefault='0'");
}

$query = $db->query("INSERT INTO tb_gudang SET Nama='$nama', IsDefault='$is_default'");
if($query){
    echo "1";
} else {
    echo "0";
}
