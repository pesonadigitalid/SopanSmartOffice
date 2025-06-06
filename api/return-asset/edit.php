<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_POST['id']);
$kode_asset = antiSQLInjection($_POST['kode_asset']);
$category = antiSQLInjection($_POST['category']);
$nama = antiSQLInjection($_POST['nama']);
$deskripsi = antiSQLInjection($_POST['deskripsi']);

$query = $db->query("UPDATE tb_asset SET KodeAsset='$kode_asset', IDAssetCategory='$category', Nama='$nama', Deskripsi='$deskripsi', ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW() WHERE IDAsset='$id'");
if ($query) {
    echo "1";
} else {
    echo "0";
}
