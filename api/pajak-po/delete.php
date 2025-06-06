<?php
include_once "../config/connection.php";
$idr = antiSQLInjection($_POST['idr']);

$cek = $db->get_row("SELECT * FROM tb_proyek WHERE IDClient='$idr'");
if($cek){
    echo "0";
} else {
    $query = $db->query("DELETE FROM tb_pelanggan WHERE IDPelanggan='$idr'");
    if($query){
        echo "1";
    } else {
        echo "0";
    }
}