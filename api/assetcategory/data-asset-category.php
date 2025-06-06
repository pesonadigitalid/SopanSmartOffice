<?php
include_once "../config/connection.php";
$jenis = antiSQLInjection($_GET['jenis']);
if ($jenis != "") $cond = " WHERE Jenis='$jenis' ";
$query = $db->get_results("SELECT * FROM tb_asset_category $cond ORDER BY IDAssetCategory ASC");
if ($query) {
    $return = array();
    $i = 1;
    foreach ($query as $data) {
        array_push($return, array("IDAssetCategory" => $data->IDAssetCategory, "No" => $i, "Nama" => $data->Nama, "Jenis" => $data->Jenis));
        $i++;
    }
    echo json_encode($return);
} else {
    echo json_encode(array());
}
