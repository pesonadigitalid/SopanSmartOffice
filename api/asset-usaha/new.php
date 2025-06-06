<?php
include_once "../config/connection.php";

include_once "../library/class.awss3.php";
$AwsS3 = new AwsS3();

$kode_asset = antiSQLInjection($_POST['kode_asset']);
$category = antiSQLInjection($_POST['category']);
$nama = antiSQLInjection($_POST['nama']);
$deskripsi = antiSQLInjection($_POST['deskripsi']);
$no_ijin_usaha = antiSQLInjection($_POST['no_ijin_usaha']);

$jatuh_tempo_usaha = antiSQLInjection($_POST['jatuh_tempo_usaha']);
$exp = explode("/", $jatuh_tempo_usaha);
$jatuh_tempo_usaha = $exp[2] . "-" . $exp[1] . "-" . $exp[0];

$stts_asset = antiSQLInjection($_POST['stts_asset']);

//$idCategori = $db->get_var("SELECT IDAssetCategory FROM tb_asset_category WHERE Nama='$category' ORDER BY IDAssetCategory");

if (strlen($kode_asset) > 20) {
    echo "3";
} else {
    $cek = $db->get_row("SELECT * FROM tb_asset WHERE KodeAsset='$kode_asset' AND Jenis='Ijin-Usaha'");
    if ($cek) {
        echo "2";
    } else {
        $sql = "INSERT INTO tb_asset SET KodeAsset='$kode_asset', IDAssetCategory='$category', Nama='$nama', Deskripsi='$deskripsi', NoIjinUsaha='$no_ijin_usaha', JatuhTempoUsaha='$jatuh_tempo_usaha', Status='$stts_asset', Jenis='Ijin-Usaha', ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW()";

        if ($_FILES['file_usaha'] != "") {
            $file_usaha = $AwsS3->uploadFileDirect("asset_photo",  $_FILES['file_usaha']);
            $sql .= ", FileUsaha='$file_usaha'";
        }
        // echo $sql;
        $query = $db->query($sql);
        if ($query) {
            echo "1";
        } else {
            echo "0";
        }
    }
}
