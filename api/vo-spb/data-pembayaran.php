<?php
include_once "../config/connection.php";
$id = antiSQLInjection($_GET['id']);

$dataPembayaran = array();
$dataSummary = array();
$terbayar = 0;

$query = $db->get_results("SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_jurnal WHERE NoRef='$id' AND Tipe='1' ORDER BY NoBukti ASC, Tanggal ASC");
if ($query) {
    $i = 0;
    foreach ($query as $data) {
        $detail = $db->get_var("SELECT a.NamaRekening FROM tb_master_rekening a, tb_jurnal_detail b WHERE a.IDRekening=b.IDRekening AND b.IDJurnal='1' AND b.Debet>0");
        $i++;
        $terbayar += $data->Debet;
        array_push($dataPembayaran, array("NoPembayaran" => $data->NoBukti, "Tanggal" => $data->TanggalID, "No" => $i, "Jumlah" => $data->Debet, "Bank" => $detail));
    }
}

$query = $db->get_row("SELECT * FROM tb_proyek_invoice WHERE IDInvoice='$id'");
$dataSummary = array("GrandTotal" => $query->GrandTotal, "Sisa" => $query->Sisa, "Terbayar" => $terbayar, "DetailPembayaran" => $dataPembayaran);

echo json_encode($dataSummary);
