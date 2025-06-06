<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_POST['id']);
$nama = antiSQLInjection($_POST['nama']);
$jenis = antiSQLInjection($_POST['jenis']);

if ($isparent == "") $isparent = "0";

$query = $db->query("UPDATE tb_asset_category SET Nama='$nama', Jenis='$jenis' WHERE IDAssetCategory='$id'");
if ($query) {
    echo "1";
} else {
    echo "0";
}
