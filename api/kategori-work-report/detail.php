<?php
include_once "../config/connection.php";
$id = antiSQLInjection($_GET['id']);
$query = $db->get_row("SELECT * FROM tb_work_report_file_category WHERE IDFileWorkReportCategory='$id' ORDER BY IDFileWorkReportCategory ASC");
if ($query) {
    echo json_encode($query);
} else {
    echo json_encode(array());
}
