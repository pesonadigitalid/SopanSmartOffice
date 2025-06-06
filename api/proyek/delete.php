<?php
include_once "../config/connection.php";
$idr = antiSQLInjection($_POST['idr']);

$cek = $db->get_row("SELECT * FROM tb_po WHERE IDProyek='$idr'");
$cek2 = $db->get_row("SELECT * FROM tb_proyek_invoice WHERE IDProyek='$idr'");
$cek3 = $db->get_row("SELECT * FROM tb_jurnal WHERE IDProyek='$idr'");

if($cek || $cek2 || $cek3){
    echo "2";
} else {
    $query = $db->query("DELETE FROM tb_proyek WHERE IDProyek='$idr'");
    if($query){
        echo "1";
    } else {
        echo "0";
    }
}