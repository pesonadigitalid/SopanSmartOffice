<?php
include_once "../config/connection.php";
$idr = antiSQLInjection($_POST['idr']);

$cek = $db->get_results("SELECT a.*,b.* FROM tb_asset a, tb_assign_detail b WHERE a.`IDAsset`=b.`IDAsset` AND b.`IDAssign`='$idr'");
if ($cek) {
    foreach ($cek as $data) {
        $updateAsset = $db->query("UPDATE tb_asset SET IDKaryawan=(NULL), IDProyek=(NULL), DateModified=NOW() WHERE IDAsset='" . $data->IDAsset . "'");
    }
    // $query = $db->query("DELETE a.*,b.* FROM tb_assign a,tb_assign_detail b WHERE a.IDAssign='$idr' AND b.IDAssign='$idr'");
    $query = $db->query("UPDATE tb_assign SET Status='2', DateModified=NOW(), ModifiedBy='" . $_SESSION["uid"] . "' WHERE IDAssign='$idr'");
    if ($query) {
        echo "1";
    } else {
        echo "0";
    }
} else {
    $query = $db->query("UPDATE tb_assign SET Status='2', DateModified=NOW(), ModifiedBy='" . $_SESSION["uid"] . "' WHERE IDAssign='$idr'");
    if ($query) {
        echo "1";
    } else {
        echo "0";
    }
}