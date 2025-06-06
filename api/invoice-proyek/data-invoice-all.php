<?php
include_once "../config/connection.php";
$datestart = antiSQLInjection($_GET['datestart']);
$expstart = explode("/", $datestart);
$datestartchange = $expstart[2] . "-" . $expstart[1] . "-" . $expstart[0];
$dateend = antiSQLInjection($_GET['dateend']);
$expend = explode("/", $dateend);
$dateendchange = $expend[2] . "-" . $expend[1] . "-" . $expend[0];
$kode_proyek = antiSQLInjection($_GET['kode_proyek']);
$departement = antiSQLInjection($_GET['departement']);
$filterstatus = antiSQLInjection($_GET['filterstatus']);

$spb = antiSQLInjection($_GET['spb']);
$jenis = antiSQLInjection($_GET['jenis']);
$fpelanggan = antiSQLInjection($_GET['pelanggan']);
$fmarketing = antiSQLInjection($_GET['marketing']);



if ($datestart != "" && $dateend != "") {
    $cond = "WHERE a.Tanggal BETWEEN '$datestartchange' AND '$dateendchange' ";
} else if ($datestart != "") {
    $cond = "WHERE a.Tanggal='$datestartchange' ";
} else {
    $cond = "WHERE DATE_FORMAT(a.Tanggal,'%Y-%m') = '" . date("Y-m") . "' ";
}

if ($kode_proyek != "")
    $cond .= " AND a.IDProyek='$kode_proyek' ";

if ($departement != "")
    $cond .= " AND a.IDProyek IN (SELECT IDProyek FROM tb_proyek WHERE IDDepartement='$departement') ";

if ($filterstatus == "1")
    $cond .= " AND a.Sisa<='0'";
else if ($filterstatus == "2")
    $cond .= " AND a.Sisa>'100'";

if ($spb != "")
    $cond .= " AND a.IDPenjualan='$spb' ";

if ($jenis != "")
    $cond .= " AND a.IsPajak='$jenis' ";

if ($fpelanggan != "")
    $cond .= " AND b.IDPelanggan='$fpelanggan' ";

if ($fmarketing != "") {
    $cond .= ($fmarketing == "490" || $fmarketing == "491")
        ? " AND (b.CreatedBy='$fmarketing' OR b.CreatedBy='1') "
        : " AND b.CreatedBy='$fmarketing' ";
}

$query = $db->get_results("SELECT a.*, DATE_FORMAT(a.Tanggal, '%d/%m/%Y') AS TanggalID, DATE_FORMAT(a.JatuhTempo, '%d/%m/%Y') AS JatuhTempoID, c.NamaPelanggan FROM tb_penjualan_invoice a, tb_penjualan b, tb_pelanggan c $cond AND a.IDPenjualan=b.IDPenjualan AND b.IDPelanggan=c.IDPelanggan ORDER BY a.Tanggal ASC");
$return = array();
if ($query) {
    $i = 0;
    foreach ($query as $data) {
        $i++;
        array_push($return, array("IDInvoice" => $data->IDInvoice, "NoInvoice" => $data->NoInvoice, "NoInv" => $data->NoInvoice, "NoFakturPajak" => $data->NoFakturPajak, "No" => $i, "NamaProyek" => $proyek->NamaProyek, "KodeProyek" => $proyek->KodeProyek, "Tanggal" => $data->TanggalID, "JatuhTempo" => $data->JatuhTempoID, "Jumlah" => number_format($data->Jumlah), "PPNPersen" => number_format($data->PPNPersen), "PPN" => number_format($data->PPN), "GrandTotal" => number_format($data->GrandTotal), "Sisa" => number_format(($data->Sisa < 0) ? 0 : $data->Sisa), "Status" => $data->Status, "Keterangan" => $data->Keterangan, "Pelanggan" => $data->NamaPelanggan, "SPB" => $data->NoPenjualan));
    }
}

$penjualan = array();
$query = $db->get_results("SELECT a.*, b.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_penjualan a, tb_pelanggan b WHERE a.IDPelanggan=b.IDPelanggan ORDER BY IDPenjualan ASC");
if ($query) {
    $i = 0;
    foreach ($query as $data) {
        $i++;
        array_push($penjualan, array("IDPenjualan" => $data->IDPenjualan, "NoPenjualan" => $data->NoPenjualan, "IDPelanggan" => $data->IDPelanggan, "Pelanggan" => $data->NamaPelanggan, "Tanggal" => $data->TanggalID, "TotalItem" => $data->TotalItem, "Total" => $data->Total, "Diskon" => $data->Diskon, "DiskonPersen" => $data->DiskonPersen, "Total2" => $data->Total2, "PPN" => $data->PPN, "PPNPersen" => $data->PPNPersen, "GrandTotal" => $data->GrandTotal, "Status" => $data->Status, "Keterangan" => $data->Keterangan, "TotalPembayaran" => $data->TotalPembayaran, "Sisa" => (($data->Sisa < 0) ? 0 : $data->Sisa), "No" => $i));
    }
}

//GRAB ALL TOTAL DATA
$all = $db->get_var("SELECT COUNT(*) FROM tb_penjualan_invoice");
if (!$all) $all = '';
$lunas = $db->get_var("SELECT COUNT(*) FROM tb_penjualan_invoice WHERE Sisa='0'");
if (!$lunas) $lunas = '';
$hutang = $db->get_var("SELECT COUNT(*) FROM tb_penjualan_invoice WHERE Sisa>0");
if (!$hutang) $hutang = '';


$pelanggan = array();
$query = $db->get_results("SELECT * FROM tb_pelanggan ORDER BY NamaPelanggan ASC");
if ($query) {
    $i = 0;
    foreach ($query as $data) {
        $i++;
        array_push($pelanggan, array("IDPelanggan" => $data->IDPelanggan, "NamaPelanggan" => $data->NamaPelanggan));
    }
}

$marketing = array();
$query = $db->get_results("SELECT * FROM tb_karyawan a, tb_penjualan b WHERE a.`IDKaryawan`=b.`CreatedBy` AND a.IDKaryawan!='1' GROUP BY a.`IDKaryawan` ORDER BY a.Nama");
if ($query) {
    $i = 0;
    foreach ($query as $data) {
        $i++;
        array_push($marketing, array("IDKaryawan" => $data->IDKaryawan, "Nama" => $data->Nama));
    }
}

echo json_encode(array("data" => $return, "all" => $all, "lunas" => $lunas, "hutang" => $hutang, "penjualan" => $penjualan, "pelanggan" => $pelanggan, "marketing" => $marketing));
