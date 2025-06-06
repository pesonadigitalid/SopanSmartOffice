<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_GET['id']);

$query = $db->get_row("SELECT * FROM tb_asset_category WHERE IDAssetCategory='$id' ORDER BY IDAssetCategory ASC");
if ($query) {
    $return = array("nama" => $query->Nama, "jenis" => $query->Jenis, "IDAssetCategory" => $query->IDAssetCategory);
}
echo json_encode($return);
