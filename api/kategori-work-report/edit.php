<?php
include_once "../config/connection.php";

$IDFileWorkReportCategory = antiSQLInjection($_POST['IDFileWorkReportCategory']);
$Nama = antiSQLInjection($_POST['Nama']);
$Status = antiSQLInjection($_POST['Status']);
if ($Status == "") $Status = 0;

$query = $db->query("UPDATE tb_work_report_file_category SET  Nama='$Nama', Status='$Status', ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW() WHERE IDFileWorkReportCategory='$IDFileWorkReportCategory'");
if ($query) {
    echo "1";
} else {
    echo "0";
}
