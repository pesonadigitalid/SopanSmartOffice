<?php
include_once "../config/connection.php";
include_once "../library/class.awss3.php";
$AwsS3 = new AwsS3();

$idr = antiSQLInjection($_POST['idr']);

$query = $db->query("DELETE FROM tb_work_report WHERE IDWorkReport='$idr'");
if ($query) {

    $qFiles = $db->get_results("SELECT * FROM tb_work_report_file WHERE IDWorkReport='$idr'");
    if ($qFiles) {
        foreach ($qFiles as $file) {
            $AwsS3->deleteFile("work_report/" . $file->File);
        }
    }
    $db->query("DELETE FROM tb_work_report_file WHERE IDWorkReport='$idr'");

    echo "1";
} else {
    echo "0";
}
