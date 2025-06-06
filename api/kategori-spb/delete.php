<?php
include_once "../config/connection.php";

include_once "../library/class.awss3.php";
$AwsS3 = new AwsS3();

$idr = antiSQLInjection($_POST['idr']);

$cek = $db->get_row("SELECT * FROM tb_penjualan_file_category WHERE IDPenjualanFileCategory='$idr'");
if (!$cek) {
    echo "0";
} else {
    $IDPenjualanFileCategory = $cek->IDPenjualanFileCategory;
    $query = $db->query("DELETE FROM tb_penjualan_file_category WHERE IDPenjualanFileCategory='$idr'");
    if ($query) {

        $queryFile = $db->get_results("SELECT * FROM tb_penjualan_file WHERE IDPenjualanFileCategory='$IDPenjualanFileCategory'");
        if ($queryFile) {
            foreach ($queryFile as $dataFile) {
                if ($dataFile->FileName != "")
                    $AwsS3->deleteFile("mms_penjualan/" . $dataFile->FileName);
            }
            $db->query("DELETE FROM tb_penjualan_file WHERE IDPenjualanFileCategory='$IDPenjualanFileCategory'");
        }

        echo "1";
    } else {
        echo "0";
    }
}
