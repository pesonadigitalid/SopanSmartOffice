<?php
include_once "../config/connection.php";

$id = $_POST['id'];

$status = antiSQLInjection($_POST['status']);
$keteranganStatus = antiSQLInjection($_POST['keteranganStatus']);

$sql = "UPDATE tb_notifikasi_service SET Status='$status', KeteranganStatus='$keteranganStatus', ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW() WHERE IDNotifikasi='$id'";

$query = $db->query($sql);
if ($query) {
    echo "1";
} else {
    echo "0";
}
