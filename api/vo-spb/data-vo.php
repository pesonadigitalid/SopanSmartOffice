<?php
include_once "../config/connection.php";
$id = antiSQLInjection($_GET['id_penjualan']);

$query = $db->get_results("SELECT *,DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_penjualan_vo WHERE IDPenjualan='$id' ORDER BY NoVO ASC");
$spb = $db->get_row("SELECT a.*, b.NamaPelanggan FROM tb_penjualan a, tb_pelanggan b WHERE a.IDPelanggan=b.IDPelanggan AND a.IDPenjualan='" . $id . "'");
$dSPB = array("NoPenjualan" => $spb->NoPenjualan, "NamaPelanggan" => $spb->NamaPelanggan);

$grandTotalInvoice = $db->get_var("SELECT SUM(GrandTotal) FROM tb_penjualan_invoice WHERE IDPenjualan='$id'");
if (!$grandTotalInvoice) $grandTotalInvoice = 0;

$piutangProgress = $db->get_var("SELECT SUM(Sisa) FROM tb_penjualan_invoice WHERE IDPenjualan='$id'");
if (!$piutangProgress) $piutangProgress = 0;

$sisaPenagihan = $spb->GrandTotal - $grandTotalInvoice;

if ($query) {

    // Reset Grand Total Akhir VO
    $GrandTotalSPBAkhir = $spb->GrandTotal;
    $qVO = $db->get_results("SELECT * FROM tb_penjualan_vo WHERE IDPenjualan='$id' ORDER BY IDPenjualanVO DESC");
    if ($qVO) {
        foreach ($qVO as $dVO) {
            $db->query("UPDATE tb_penjualan_vo SET GrandTotalSPBAkhir='$GrandTotalSPBAkhir' WHERE IDPenjualanVO='$dVO->IDPenjualanVO'");
            $GrandTotalSPBAkhir -= $dVO->GrandTotal;
        }
    }

    echo json_encode(array("data" => $query, "PiutangProgress" => $piutangProgress, "GrandTotal" => $spb->GrandTotal, "GrandTotalInvoice" => $grandTotalInvoice, "SisaPenagihan" => $sisaPenagihan, "DetailSPB" => $dSPB));
} else {
    echo json_encode(array("data" => array(), "PiutangProgress" => $piutangProgress, "GrandTotal" => $spb->GrandTotal, "GrandTotalInvoice" => $grandTotalInvoice, "SisaPenagihan" => $sisaPenagihan, "DetailSPB" => $dSPB));
}
