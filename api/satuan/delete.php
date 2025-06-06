<?php
include_once "../config/connection.php";
$idr = antiSQLInjection($_POST['idr']);

$cek = $db->get_row("SELECT * FROM tb_barang WHERE IDSatuan='$idr'");
if($cek){
    echo "2";
} else {
    $query = $db->query("DELETE FROM tb_satuan WHERE IDSatuan='$idr'");
    if($query){
        echo "1";
    } else {
        echo "0";
    }
}