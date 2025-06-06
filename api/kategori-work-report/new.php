<?php
include_once "../config/connection.php";

$Nama = antiSQLInjection($_POST['Nama']);
$Status = antiSQLInjection($_POST['Status']);
if ($Status == "") $Status = 0;

$query = $db->query("INSERT INTO tb_work_report_file_category SET Nama='$Nama', Status='$Status', CreatedBy='" . $_SESSION["uid"] . "'");
if ($query) {
    echo "1";
} else {
    echo "0";
}
