<?php
include_once "../config/connection.php";
$id = antiSQLInjection($_GET['id_proyek']);

$query = $db->get_results("SELECT *,DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID,DATE_FORMAT(JatuhTempo, '%d/%m/%Y') AS JatuhTempoID FROM tb_penjualan_invoice WHERE IDPenjualan='$id' ORDER BY Tanggal ASC");
$penjualan = $db->get_row("SELECT a.*,DATE_FORMAT(a.Tanggal, '%d/%m/%Y') AS TanggalID, b.KodePelanggan, b.NamaPelanggan FROM tb_penjualan a, tb_pelanggan b WHERE a.`IDPelanggan`=b.`IDPelanggan` AND a.IDPenjualan='$id'");

$dpenjualan = array("NoPenjualan" => $penjualan->NoPenjualan, "Tanggal" => $penjualan->TanggalID, "NamaPelanggan" => $penjualan->NamaPelanggan, "KodePelanggan" => $penjualan->KodePelanggan);

$grandTotalInvoice = $db->get_var("SELECT SUM(GrandTotal) FROM tb_penjualan_invoice WHERE IDPenjualan='$id'");
if (!$grandTotalInvoice) $grandTotalInvoice = 0;
$piutangProgress = $db->get_var("SELECT SUM(Sisa) FROM tb_penjualan_invoice WHERE IDPenjualan='$id'");
if (!$piutangProgress) $piutangProgress = 0;
$sisaPenagihan = $penjualan->GrandTotal - $grandTotalInvoice;
if ($query) {
    $return = array();
    $i = 0;
    foreach ($query as $data) {
        $i++;

        if ($data->Sisa < 0 && $data->Sisa > -1) {
            $data->Sisa = 0;
        }

        array_push($return, array("IDInvoice" => $data->IDInvoice, "NoInvoice" => $data->NoInvoice, "NoInv" => $data->NoInv, "NoFakturPajak" => $data->NoFakturPajak, "No" => $i, "NamaPelanggan" => $penjualan->NamaPelanggan, "KodePelanggan" => $penjualan->KodePelanggan, "Tanggal" => $data->TanggalID, "JatuhTempo" => $data->JatuhTempoID, "Jumlah" => number_format($data->Jumlah), "PPNPersen" => number_format($data->PPNPersen), "PPN" => number_format($data->PPN), "GrandTotal" => number_format($data->GrandTotal), "Sisa" => number_format($data->Sisa), "Status" => $data->Status, "Keterangan" => $data->Keterangan));
    }
    echo json_encode(array("data" => $return, "PiutangProgress" => $piutangProgress, "GrandTotal" => $penjualan->GrandTotal, "GrandTotalInvoice" => $grandTotalInvoice, "SisaPenagihan" => $sisaPenagihan, "DetailPenjualan" => $dpenjualan));
} else {
    echo json_encode(array("data" => array(), "PiutangProgress" => $piutangProgress, "GrandTotal" => $penjualan->GrandTotal, "GrandTotalInvoice" => $grandTotalInvoice, "SisaPenagihan" => $sisaPenagihan, "DetailPenjualan" => $dpenjualan));
}
