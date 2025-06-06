<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_POST['id']);

$noinv = antiSQLInjection($_POST['noinv']);
$NoFakturPajak = antiSQLInjection($_POST['NoFakturPajak']);

$jatuh_tempo = antiSQLInjection($_POST['jatuh_tempo']);
$expjt = explode("/", $jatuh_tempo);
$jatuh_tempo = $expjt[2] . "-" . $expjt[1] . "-" . $expjt[0];

$keterangan = antiSQLInjection($_POST['keterangan']);
$note1 = antiSQLInjection($_POST['note1']);
$note2 = antiSQLInjection($_POST['note2']);
$sign = antiSQLInjection($_POST['sign']);
$npwp = antiSQLInjection($_POST['npwp']);
$isPajak = antiSQLInjection($_POST['isPajak']);

$cek = $db->get_row("SELECT * FROM tb_penjualan_invoice WHERE NoInvoice='$noinv' AND IDInvoice<>'$id'");
if ($cek && $noinv != '') {
    echo json_encode(array("status" => 0, "msg" => "No Invoice yang anda masukan telah digunakan pada invoice lain."));
} else {
    $query = $db->query("UPDATE tb_penjualan_invoice SET NoInvoice='$noinv', NoFakturPajak='$NoFakturPajak', JatuhTempo='$jatuh_tempo', Keterangan='$keterangan', Note1='$note1', Note2='$note2', Sign='$sign', NPWP='$npwp', IsPajak='$isPajak' WHERE IDInvoice='$id'");
    if ($query) {
        $db->query("UPDATE tb_penjualan_invoice_detail SET NoInvoice='$noinv' WHERE IDInvoice='$id'");
        echo json_encode(array("status" => 1, "msg" => "Sukses"));
    } else {
        echo json_encode(array("status" => 0, "msg" => "Invoice gagal disimpan. Silahkan coba kembali nanti."));
    }
}
