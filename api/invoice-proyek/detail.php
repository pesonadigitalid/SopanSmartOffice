<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_GET['id']);

$query = $db->get_row("SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID, DATE_FORMAT(JatuhTempo, '%d/%m/%Y') AS JatuhTempoID FROM tb_penjualan_invoice WHERE IDInvoice='$id' ORDER BY IDInvoice ASC");
if ($query) {
    $IDSuratJalan = $query->IDSuratJalan;
    $penjualan = $db->get_row("SELECT * FROM tb_penjualan WHERE IDPenjualan='" . $query->IDPenjualan . "'");
    $pelanggan = $db->get_row("SELECT * FROM tb_pelanggan WHERE IDPelanggan='" . $penjualan->IDPelanggan . "'");

    $sj = $query->IDSuratJalan;
    $surat_jalan = array();
    if ($sj != "") {
        $exp = explode(", ", $sj);
        foreach ($exp as $dK) {
            $var = trim(str_replace(",", "", $dK));
            array_push($surat_jalan, $var);
        }
    }

    if ($penjualan->DP == $query->TOP) {
        $top = "DP " . $query->TOP . "%";
    } else if ($penjualan->TerminI == $query->TOP) {
        $top = "Termin I " . $query->TOP . "%";
    } else if ($penjualan->TerminII == $query->TOP) {
        $top = "Termin II " . $query->TOP . "%";
    } else if ($penjualan->TerminIII == $query->TOP) {
        $top = "Termin III " . $query->TOP . "%";
    } else if ($penjualan->TerminIV == $query->TOP) {
        $top = "Pelunasan " . $query->TOP . "%";
    }

    $cart = array();
    $qCart = $db->get_results("SELECT * FROM tb_penjualan_invoice_detail WHERE IDInvoice='$id'");
    if ($qCart) {
        $i = 0;
        foreach ($qCart as $dCart) {
            if ($dCart->SN != "") $sn = 1;
            else $sn = 0;
            $i++;
            array_push($cart, array("NoUrut" => $i, "IDBarang" => $dCart->IDBarang, "NamaBarang" => $dCart->NamaBarang, "NamaBarangDisplay" => $dCart->NamaBarangDisplay, "QtyBarang" => $dCart->Qty, "SNBarang" => $dCart->SN, "IsSerialize" => $sn, "Limit" => $dCart->Qty, "Harga" => $dCart->Harga, "HPP" => $dCart->HargaBeli, "Margin" => $dCart->Margin, "SubTotal" => $dCart->SubTotal, "Diskon" => $dCart->Diskon, "HargaDiskon" => $dCart->HargaDiskon));
        }
    }

    $return = array("id_penjualan" => $query->IDPenjualan, "tanggal" => $query->TanggalID, "jatuh_tempo" => $query->JatuhTempoID, "per_penagihan" => $query->JumlahPersen, "jumlah" => $query->Jumlah, "PPNPersen" => $query->PPNPersen, "PPN" => $query->PPN, "GrandTotal" => $query->GrandTotal, "Sisa" => $query->Sisa, "Keterangan" => $query->Keterangan, "NoPenjualan" => $penjualan->NoPenjualan, "Pelanggan" => $pelanggan->KodePelanggan . " " . $pelanggan->NamaPelanggan, "noinv" => $query->NoInvoice, "Terbilang" => $query->Terbilang, "Note1" => $query->Note1, "Note2" => $query->Note2, "Sign" => $query->Sign, "NPWP" => $query->NPWP, "IsPajak" => $query->IsPajak, "DiskonPersen" => $query->DiskonPersen, "Diskon" => $query->Diskon, "Jumlah2" => $query->Jumlah2, "surat_jalan" => $surat_jalan, "top" => $top, "cart" => $cart, "nilaiJual2" => $penjualan->GrandTotal, "NoFakturPajak" => $query->NoFakturPajak);
}

$penjualan = array();
$surat_jalan = array();

$query = $db->get_results("SELECT * FROM tb_penjualan ORDER BY NoPenjualan ASC");
if ($query) {
    foreach ($query as $data) {
        $penagihan = $db->get_var("SELECT SUM(GrandTotal) FROM tb_penjualan_invoice WHERE IDPenjualan='" . $data->IDPenjualan . "'");
        if (!$penagihan) $penagihan = 0;
        $sisa = $data->GrandTotal - $penagihan;
        array_push($penjualan, array("IDPenjualan" => $data->IDPenjualan, "NoPenjualan" => $data->NoPenjualan, "nilaiJual" => $data->GrandTotal, "totalPenagihan" => $penagihan, "SisaPenagihan" => $sisa));
    }
}

$explode = explode(", ", $IDSuratJalan);
foreach ($explode as $data) {
    $data = trim(str_replace(",", "", $data));
    $query = $db->get_row("SELECT * FROM tb_penjualan_surat_jalan WHERE IDSuratJalan='$data'");
    array_push($surat_jalan, array("IDSuratJalan" => $query->IDSuratJalan, "NoSuratJalan" => $query->NoSuratJalan, "IDPenjualan" => $query->IDPenjualan, "NoPenjualan" => $query->NoPenjualan, "GrandTotal" => $query->GrandTotal, "IsInvoiced" => 1));
}

$return = array("detail" => $return, "surat_jalan" => $surat_jalan, "penjualan" => $penjualan);

echo json_encode($return);
