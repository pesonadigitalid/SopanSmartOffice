<?php
include_once "../config/connection.php";

include_once "../library/class.awss3.php";
include_once "../library/class.fungsi.php";
$fungsi = new Fungsi();
$AwsS3 = new AwsS3();

$work_schedule = antiSQLInjection($_POST['work_schedule']);

$tanggal = antiSQLInjection($_POST['tanggal']);
$exp = explode("/", $tanggal);
$tanggal = $exp[2] . "-" . $exp[1] . "-" . $exp[0];

$keterangan = antiSQLInjection($_POST['keterangan']);
$is_completed = antiSQLInjection($_POST['is_completed']);

$foto_array = $_FILES['foto_array']['name'];
$category_file = antiSQLInjection($_POST['category_file_array']);

$no_work_report = $fungsi->GetNoFaktur($_POST['tanggal'], "tb_work_report", "NoWorkReport", "WR/SPN/");

$sql = "INSERT INTO tb_work_report SET IDWorkSchedule='$work_schedule', NoWorkReport='$no_work_report', Tanggal='$tanggal', Keterangan='$keterangan', CreatedBy='" . $_SESSION["uid"] . "'";
//echo $sql;
$query = $db->query($sql);
if ($query) {
    $IDWorkReport = $db->get_var("SELECT LAST_INSERT_ID()");

    $db->query("UPDATE tb_work_schedule SET status='$is_completed' WHERE IDWorkSchedule='$work_schedule'");

    foreach ($foto_array as $key => $foto) {
        if($_FILES['foto_array']['name'][$key]) {
            $file_tmp = $_FILES['foto_array']['tmp_name'][$key];
            $file_name = $_FILES['foto_array']['name'][$key];

            $fotoName = $AwsS3->uploadFileDirect2("work_report", $file_tmp, $file_name);
            $db->query("INSERT INTO tb_work_report_file SET IDWorkReport='".$IDWorkReport."', IDFileWorkReportCategory='".$category_file[$key]."', File='$fotoName'");
        }
    }
    echo "1";
} else {
    echo "0";
}
