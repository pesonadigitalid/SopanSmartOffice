<?php
include_once "../config/connection.php";

include_once "../library/class.awss3.php";
$AwsS3 = new AwsS3();

$idr = antiSQLInjection($_POST['idr']);

$cekImg = newQuery("get_row", "SELECT * FROM tb_asset WHERE IDAsset='$idr'");
if ($cekImg) {
    if ($cekImg->IDKaryawan > 0) {
        echo "0";
    } else {
        if ($cekImg->FileUsaha != "")
            $AwsS3->deleteFile("asset_photo/" . $cekImg->FileUsaha);

        $query = $db->query("DELETE FROM tb_asset WHERE IDAsset='$idr'");
        if ($query) {

            echo "1";
        } else {
            echo "0";
        }
    }
}
