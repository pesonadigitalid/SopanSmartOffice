<?php
include_once "../config/connection.php";

include_once "../library/class.awss3.php";
$AwsS3 = new AwsS3();

$id = $_POST['id'];

$kode_asset = antiSQLInjection($_POST['kode_asset']);
$category = antiSQLInjection($_POST['category']);
$nama = antiSQLInjection($_POST['nama']);
$deskripsi = antiSQLInjection($_POST['deskripsi']);
$no_ijin_usaha = antiSQLInjection($_POST['no_ijin_usaha']);

$jatuh_tempo_usaha = antiSQLInjection($_POST['jatuh_tempo_usaha']);
$exp = explode("/", $jatuh_tempo_usaha);
$jatuh_tempo_usaha = $exp[2] . "-" . $exp[1] . "-" . $exp[0];

$stts_asset = antiSQLInjection($_POST['stts_asset']);

$cek = $db->get_row("SELECT * FROM tb_asset WHERE KodeAsset='$kode_asset' AND Jenis='Ijin-Usaha' AND IDAsset!='$id'");
if ($cek) {
    echo "2";
} else {
    if (strlen($kode_asset) > 20) {
        echo "3";
    } else {
        $sql = "UPDATE tb_asset SET KodeAsset='$kode_asset', IDAssetCategory='$category', Nama='$nama', Deskripsi='$deskripsi', NoIjinUsaha='$no_ijin_usaha', JatuhTempoUsaha='$jatuh_tempo_usaha', Status='$stts_asset', ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW()";

        if ($_FILES['file_usaha'] != "") {
            $cekImg = newQuery("get_var", "SELECT FileUsaha FROM tb_asset WHERE IDAsset='$id'");
            if ($cekImg) {
                $AwsS3->deleteFile("asset_photo/" . $cekImg);
            }

            $file_usaha = $AwsS3->uploadFileDirect("asset_photo",  $_FILES['file_usaha']);
            $sql .= ", FileUsaha='$file_usaha'";
        }

        $sql .= " WHERE IDAsset='$id'";

        $query = $db->query($sql);
        if ($query) {
            echo "1";
        } else {
            echo "0";
        }
    }
}
