<?php
include_once "../config/connection.php";
$idr = antiSQLInjection($_POST['idr']);

$data = $db->get_row("SELECT * FROM tb_penjualan_invoice_penerimaan WHERE IDInvoice='$idr'");
if($data->Status==="1"){
    echo "2";
} else {
    $query = $db->query("DELETE FROM tb_penjualan_invoice WHERE IDInvoice='$idr'");
    if($query){
        $query = $db->query("DELETE FROM tb_penjualan_invoice_detail WHERE IDInvoice='$idr'");
        echo "1";
    } else {
        echo "0";
    }
}