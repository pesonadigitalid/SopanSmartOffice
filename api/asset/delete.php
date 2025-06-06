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
        if ($cekImg->Foto1 != "")
            $AwsS3->deleteFile("asset_photo/" . $cekImg->Foto1);
        if ($cekImg->Foto2 != "")
            $AwsS3->deleteFile("asset_photo/" . $cekImg->Foto2);
        if ($cekImg->Foto3 != "")
            $AwsS3->deleteFile("asset_photo/" . $cekImg->Foto3);

        if ($cekImg->File1 != "")
            $AwsS3->deleteFile("asset_file/" . $cekImg->File1);
        if ($cekImg->File2 != "")
            $AwsS3->deleteFile("asset_file/" . $cekImg->File2);
        if ($cekImg->File3 != "")
            $AwsS3->deleteFile("asset_file/" . $cekImg->File3);
        if ($cekImg->File4 != "")
            $AwsS3->deleteFile("asset_file/" . $cekImg->File4);
        if ($cekImg->File5 != "")
            $AwsS3->deleteFile("asset_file/" . $cekImg->File5);

        $query = $db->query("DELETE FROM tb_asset WHERE IDAsset='$idr'");
        if ($query) {

            echo "1";
        } else {
            echo "0";
        }
    }
}
