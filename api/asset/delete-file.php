<?php
include_once "../config/connection.php";

include_once "../library/class.awss3.php";
$AwsS3 = new AwsS3();

$id = antiSQLInjection($_POST['id']);
$field = antiSQLInjection($_POST['field']);

$cekImg = newQuery("get_row", "SELECT * FROM tb_asset WHERE IDAsset='$id'");
if ($cekImg) {
    if($field == "Foto1" || $field == "Foto2" || $field == "Foto3")
        $folderName = "asset_photo";
    else
        $folderName = "asset_file";

    if ($cekImg->$field != "")
        $AwsS3->deleteFile($folderName . "/" . $cekImg->$field);

    $query = $db->query("UPDATE tb_asset SET $field=NULL WHERE IDAsset='$id'");
    if ($query) {
        echo "1";
    } else {
        echo "0";
    }
}
