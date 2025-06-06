<?php
include_once "../config/connection.php";

$nama = antiSQLInjection($_POST['nama']);
$jenis = antiSQLInjection($_POST['jenis']);
$query = $db->query("INSERT INTO tb_asset_category SET Nama='$nama', Jenis='$jenis'");
if ($query) {
    echo "1";
} else {
    echo "0";
}
