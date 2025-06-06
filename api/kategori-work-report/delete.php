<?php
include_once "../config/connection.php";

include_once "../library/class.awss3.php";
$AwsS3 = new AwsS3();

$idr = antiSQLInjection($_POST['idr']);

$cek = $db->get_row("SELECT * FROM tb_work_report_file_category WHERE IDFileWorkReportCategory='$idr'");
if (!$cek) {
    echo "0";
} else {
    $IDFileWorkReportCategory = $cek->IDFileWorkReportCategory;
    $query = $db->query("DELETE FROM tb_work_report_file_category WHERE IDFileWorkReportCategory='$idr'");
    if ($query) {

//        $queryFile = $db->get_results("SELECT * FROM tb_penjualan_file WHERE IDFileWorkReportCategory='$IDFileWorkReportCategory'");
//        if ($queryFile) {
//            foreach ($queryFile as $dataFile) {
//                if ($dataFile->FileName != "")
//                    $AwsS3->deleteFile("mms_penjualan/" . $dataFile->FileName);
//            }
//            $db->query("DELETE FROM tb_penjualan_file WHERE IDFileWorkReportCategory='$IDFileWorkReportCategory'");
//        }

        echo "1";
    } else {
        echo "0";
    }
}
