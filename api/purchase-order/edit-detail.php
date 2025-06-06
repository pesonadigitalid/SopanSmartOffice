<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_POST['id']);

$inv_pembayaran = antiSQLInjection($_POST['inv_pembayaran']);
$inv_bank = antiSQLInjection($_POST['inv_bank']);
$inv_delivery = antiSQLInjection($_POST['inv_delivery']);
$inv_expedisi = antiSQLInjection($_POST['inv_expedisi']);
$inv_alamat_pengiriman = antiSQLInjection($_POST['inv_alamat_pengiriman']);
$spb = antiSQLInjection($_POST['spb']);
$kategori = antiSQLInjection($_POST['kategori']);
$completed = antiSQLInjection($_POST['completed']);
$completedFakturPajak = antiSQLInjection($_POST['completedFakturPajak']);

if ($spb > 0) $kategori = "Stok Purchasing";
if (($spb == "0" || $spb == "") && $kategori == "Stok Purchasing") $kategori == "Stok Gudang";

$query = $db->query("UPDATE tb_po SET InvPembayaran='$inv_pembayaran', InvBank='$inv_bank', InvDelivery='$inv_delivery', InvExpedisi='$inv_expedisi', InvAlamatPengiriman='$inv_alamat_pengiriman', IDPenjualan='$spb', Kategori='$kategori', Completed='$completed', CompletedFakturPajak='$completedFakturPajak' WHERE NoPO='$id'");
if ($query) {
    echo "1";
} else {
    echo "0";
}