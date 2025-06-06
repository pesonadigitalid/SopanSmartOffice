<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_POST['id']);

$tanggal = antiSQLInjection($_POST['tanggal']);
$exptgl = explode("/",$tanggal);
$tanggal = $exptgl[2]."-".$exptgl[1]."-".$exptgl[0];

$jatuh_tempo = antiSQLInjection($_POST['jatuh_tempo']);
$expjt = explode("/",$jatuh_tempo);
$jatuh_tempo = $expjt[2]."-".$expjt[1]."-".$expjt[0];

$jumlah = str_replace(",","",antiSQLInjection($_POST['jumlah']));

if($status=="") $status="0";
$query = $db->query("UPDATE tb_proyek_invoice SET Tanggal='$tanggal', JatuhTempo='$jatuh_tempo', Jumlah='$jumlah' WHERE IDInvoice='$id'");
if($query){
    echo "1";
} else {
    echo "0";
}