<?php
include_once "../config/connection.php";

$id_assign = antiSQLInjection($_POST['id_assign']);
$id_asset = antiSQLInjection($_POST['id_asset']);
$kode_asset = antiSQLInjection($_POST['kode_asset']);
$nama = antiSQLInjection($_POST['nama']);

$query = $db->query("INSERT INTO tb_assign_detail SET IDAssign='$id_assign', IDAsset='$id_asset', KodeAsset='$kode_asset', Nama='$nama'");
if($query){
    echo "1";
} else {
    echo "0";
}