<?php
include_once "../config/connection.php";

$parent = antiSQLInjection($_POST['parent']);
$nama = antiSQLInjection($_POST['nama']);
$isparent = antiSQLInjection($_POST['isparent']);

$cek = $db->query("SELECT * FROM tb_jenis_material WHERE Nama='$nama'");
if ($cek) {
    echo "2";
} else {
    if ($isparent == "") $isparent = "0";
    $query = $db->query("INSERT INTO tb_jenis_material SET Parent='$parent', Nama='$nama', IsParent='$isparent'");
    if ($query) {
        echo "1";
    } else {
        echo "0";
    }
}
