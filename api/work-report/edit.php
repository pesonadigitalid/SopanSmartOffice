<?php
include_once "../config/connection.php";

include_once "../library/class.awss3.php";
$AwsS3 = new AwsS3();

$id = $_POST['id'];

$work_schedule = antiSQLInjection($_POST['work_schedule']);

$tanggal = antiSQLInjection($_POST['tanggal']);
$exp = explode("/", $tanggal);
$tanggal = $exp[2] . "-" . $exp[1] . "-" . $exp[0];

$keterangan = antiSQLInjection($_POST['keterangan']);
$is_completed = antiSQLInjection($_POST['is_completed']);

$file_id_array = antiSQLInjection($_POST['file_id_array']);
$foto_array = $_FILES['foto_array']['name'];
$category_file = antiSQLInjection($_POST['category_file_array']);

$sql = "UPDATE tb_work_report SET IDWorkSchedule='$work_schedule', Tanggal='$tanggal', Keterangan='$keterangan', ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW()";

$sql .= " WHERE IDWorkReport='" . $id . "'";

//echo $sql;
$query = $db->query($sql);
if ($query) {
    $db->query("UPDATE tb_work_schedule SET status='$is_completed' WHERE IDWorkSchedule='$work_schedule'");

    $ids = "";
    foreach ($file_id_array as $key => $fileId) {
        $cekFile = $db->get_row("SELECT * FROM tb_work_report_file WHERE IDWorkReportFile='" . $fileId . "'");
        if ($cekFile) {
            if ($_FILES['foto_array']['name'][$key]) {
                $file_tmp = $_FILES['foto_array']['tmp_name'][$key];
                $file_name = $_FILES['foto_array']['name'][$key];

                $fotoName = $AwsS3->uploadFileDirect2("work_report", $file_tmp, $file_name);
                $AwsS3->deleteFile("work_report/" . $cekFile->File);
                $db->query("UPDATE tb_work_report_file SET IDFileWorkReportCategory='" . $category_file[$key] . "', File='$fotoName' WHERE IDWorkReportFile='" . $fileId . "'");
            } else {
                $db->query("UPDATE tb_work_report_file SET IDFileWorkReportCategory='" . $category_file[$key] . "' WHERE IDWorkReportFile='" . $fileId . "'");
            }
            $ids .= "$fileId,";
        } else {
            if ($_FILES['foto_array']['name'][$key]) {
                $file_tmp = $_FILES['foto_array']['tmp_name'][$key];
                $file_name = $_FILES['foto_array']['name'][$key];

                $fotoName = $AwsS3->uploadFileDirect2("work_report", $file_tmp, $file_name);
                $AwsS3->deleteFile("work_report/" . $cekFile->File);
                $db->query("INSERT INTO tb_work_report_file SET IDFileWorkReportCategory='" . $category_file[$key] . "', IDWorkReport='" . $id . "', File='$fotoName'");
            }
            $IDWorkReportFile = $db->get_var("SELECT LAST_INSERT_ID()");

            $ids .= "$IDWorkReportFile,";
        }
    }
    $ids = substr($ids, 0, -1);

    $qRemovedFiles = $db->get_results("SELECT * FROM tb_work_report_file WHERE IDWorkReport='$id' AND IDWorkReportFile NOT IN ($ids)");
    if ($qRemovedFiles) {
        foreach ($qRemovedFiles as $file) {
            $AwsS3->deleteFile("work_report/" . $file->File);
        }
    }
    $db->query("DELETE FROM tb_work_report_file WHERE IDWorkReportFile NOT IN ($ids) AND IDWorkReport='$id'");

    if (!$file_id_array) {
        $qFiles = $db->get_results("SELECT * FROM tb_work_report_file WHERE IDWorkReport='$id'");
        if ($qFiles) {
            foreach ($qFiles as $file) {
                $AwsS3->deleteFile("work_report/" . $file->File);
            }
        }
        $db->query("DELETE FROM tb_work_report_file WHERE IDWorkReport='$id'");
    }

    echo "1";
} else {
    echo "0";
}
