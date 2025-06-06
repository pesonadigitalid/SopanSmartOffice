<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_GET['id']);
$current = antiSQLInjection($_GET['current']);

$count = $db->get_var("SELECT COUNT(*) FROM tb_karyawan_finger WHERE IDKaryawan='$id'");
if ($count && $count > $current) {
    echo "1";
} else {
    echo "0";
}
