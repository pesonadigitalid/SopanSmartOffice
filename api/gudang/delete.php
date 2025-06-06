<?php
include_once "../config/connection.php";
$idr = antiSQLInjection($_POST['idr']);

$cek = $db->get_row("SELECT * FROM tb_penerimaan_stok WHERE IDGudang='$idr'");
if($cek){
    echo "2";
} else {
    $query = $db->query("DELETE FROM tb_gudang WHERE IDGudang='$idr'");
    if($query){
        echo "1";
    } else {
        echo "0";
    }
}
