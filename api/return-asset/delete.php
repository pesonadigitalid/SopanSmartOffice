<?php
include_once "../config/connection.php";
$idr = antiSQLInjection($_POST['idr']);

$allow = true;

$cek = $db->get_results("SELECT a.IDAsset,b.*,c.IDKaryawan,c.IDReturn FROM tb_asset a, tb_return_asset_detail b, tb_return_asset c WHERE a.`IDAsset`=b.`IDAsset` AND b.`IDReturn`=c.`IDReturn` AND b.`IDReturn`='$idr'");
if ($cek) {
    foreach ($cek as $data) {
        $cekBarang = $db->get_row("SELECT * FROM tb_asset WHERE IDAsset='$data->IDAsset' AND IDKaryawan>'0'");
        if ($cekBarang) {
            $allow = false;
        }
    }
}

if ($allow) {
    $cek = $db->get_results("SELECT a.IDAsset,b.*,c.IDKaryawan,c.IDReturn FROM tb_asset a, tb_return_asset_detail b, tb_return_asset c WHERE a.`IDAsset`=b.`IDAsset` AND b.`IDReturn`=c.`IDReturn` AND b.`IDReturn`='$idr'");
    if ($cek) {
        foreach ($cek as $data) {
            $updateAsset = $db->query("UPDATE tb_asset SET IDKaryawan='" . $data->IDKaryawan . "', ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW() WHERE IDAsset='" . $data->IDAsset . "'");
        }
        $query = $db->query("DELETE a.*,b.* FROM tb_return_asset a,tb_return_asset_detail b WHERE a.IDReturn='$idr' AND b.IDReturn='$idr'");
        if ($query) {
            echo "1";
        } else {
            echo "0";
        }
    } else {
        echo "2";
    }
} else {
    echo "3";
}
