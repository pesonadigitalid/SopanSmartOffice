<?php
include_once "../config/connection.php";
$idr = antiSQLInjection($_POST['idr']);

$cek = $db->get_row("SELECT * FROM tb_karyawan WHERE IDJabatan='$idr'");
if ($cek) {
    echo "2";
} else {
    $isSystemRequired = $db->get_row("SELECT * FROM tb_jabatan WHERE IDJabatan='$idr' AND SystemRequired='1'");
    if ($isSystemRequired) {
        echo "3";
    } else {
        $query = $db->query("DELETE FROM tb_jabatan WHERE IDJabatan='$idr'");
        if ($query) {
            echo "1";
        } else {
            echo "0";
        }
    }
}