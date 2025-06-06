<?php
include_once "../config/connection.php";

include_once "../library/class.awss3.php";
$AwsS3 = new AwsS3();

$idr = antiSQLInjection($_POST['idr']);

$cek = $db->get_row("SELECT * FROM tb_pelanggan_file_category WHERE IDFileCategory='$idr'");
if (!$cek) {
    echo "0";
} else {
    $query = $db->query("DELETE FROM tb_pelanggan_file_category WHERE IDFileCategory='$idr'");
    if ($query) {

        $queryFile = $db->get_results("SELECT * FROM tb_pelanggan_file WHERE IDFileCategory='$IDFileCategory'");
        if ($queryFile) {
            foreach ($queryFile as $dataFile) {
                if ($dataFile->FileName != "")
                    $AwsS3->deleteFile("mms_pelanggan/" . $dataFile->FileName);
            }
            $db->query("DELETE FROM tb_pelanggan_file WHERE IDFileCategory='$IDFileCategory'");
        }

        echo "1";
    } else {
        echo "0";
    }
}
