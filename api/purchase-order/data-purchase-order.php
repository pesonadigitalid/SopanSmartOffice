<?php
include_once "../config/connection.php";

$PO = array();

$status = antiSQLInjection($_GET['status']);

/* LOAD PO */
$datestart = antiSQLInjection($_GET['datestart']);
$expstart = explode("/", $datestart);
$datestartchange = $expstart[2] . "-" . $expstart[1] . "-" . $expstart[0];

$dateend = antiSQLInjection($_GET['dateend']);
$expend = explode("/", $dateend);
$dateendchange = $expend[2] . "-" . $expend[1] . "-" . $expend[0];

$supplier = antiSQLInjection($_GET['supplier']);
$kategori = antiSQLInjection($_GET['kategori']);
$ispajak = antiSQLInjection($_GET['ispajak']);
$nopo = antiSQLInjection($_GET['nopo']);

if ($datestart != "" && $dateend != "") {
    $cond = "WHERE Tanggal BETWEEN '$datestartchange' AND '$dateendchange'";
} else if ($datestart != "") {
    $cond = "WHERE Tanggal='$datestartchange'";
} else {
    $cond = "WHERE DATE_FORMAT(Tanggal,'%Y-%m') = '" . date("Y-m") . "'";
}

/*if($supplier != "")
    $cond .= " AND IDSupplier='$supplier'";*/

if ($supplier != "")
    $cond .= " AND IDSupplier='$supplier'";

if ($kategori != "") {
    if ($kategori == "1")
        $cond .= " AND IDPenjualan>'0'";
    else
        $cond .= " AND (IDPenjualan='0' OR IDPenjualan='' OR IDPenjualan IS NULL)";
}

if ($ispajak != "")
    $cond .= " AND IsPajak='$ispajak'";

if ($nopo != "")
    $cond .= " AND NoPO LIKE '%$nopo%'";

if ($status != "") $cond .= "AND Completed='$status'";

if ($_GET['param'] == "pembelian")
    $sql = "SELECT * FROM tb_po WHERE NoPO NOT IN (SELECT NoPO FROM tb_pembelian) ORDER BY IDPO ASC";
else
    $sql = "SELECT *,DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_po $cond ORDER BY IDPO ASC";


$grandTotal = $db->get_var("SELECT SUM(GrandTotal) FROM tb_po $cond AND DeletedDate IS NULL ORDER BY IDPO ASC");
if (!$grandTotal) $grandTotal = 0;
$sisa = $db->get_var("SELECT SUM(Sisa) FROM tb_po $cond AND DeletedDate IS NULL ORDER BY IDPO ASC");
if (!$sisa) $sisa = 0;

$query = $db->get_results($sql);
if ($query) {
    $i = 0;
    foreach ($query as $data) {
        $i++;
        $created = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $data->CreatedBy . "'");

        if ($data->JenisPO == "1")
            $jenis_po = "PO MATERIAL";
        else if ($data->JenisPO == "2")
            $jenis_po = "PO TENAGA/SUBKON";
        else if ($data->JenisPO == "3")
            $jenis_po = "PO OVERHEAD";

        $dataSupplier = $db->get_var("SELECT NamaPerusahaan FROM tb_supplier WHERE IDSupplier = '" . $data->IDSupplier . "'");

        $cek = $db->get_row("SELECT * FROM tb_penerimaan_stok WHERE NoPO='" . $data->NoPO . "'");
        if ($cek) {
            $allow = "0";
        } else {
            $cek = $db->get_row("SELECT * FROM tb_jurnal WHERE NoRef='" . $data->IDPO . "' AND Tipe='3'");
            if ($cek) {
                $allow = "0";
            } else {
                $allow = "1";
            }
        }

        $NoPenjualan = $db->get_var("SELECT NoPenjualan FROM tb_penjualan WHERE IDPenjualan='$data->IDPenjualan'");

        array_push($PO, array(
            "IDPO" => $data->IDPO,
            "NoPO" => $data->NoPO,
            "No" => $i,
            "JenisPO" => $jenis_po,
            "Supplier" => $dataSupplier,
            "Tanggal" => $data->TanggalID,
            "GrandTotal" => $data->GrandTotal,
            "PembayaranDP" => $data->PembayaranDP,
            "Keterangan" => $data->Keterangan,
            "Completed" => $data->Completed,
            "CreatedBy" => $created,
            "TextSelectBox" => $data->NoPO,
            "AllowEdit" => $allow,
            "NoPenjualan" => $NoPenjualan
        ));
    }
}

/* LOAD SUPPLIER */
$supplier = array();
$query = $db->get_results("SELECT * FROM tb_supplier ORDER BY NamaPerusahaan ASC");
if ($query) {
    $return = array();
    $i = 0;
    foreach ($query as $data) {
        array_push($supplier, array(
            "IDSupplier" => $data->IDSupplier,
            "NamaSupplier" => $data->NamaPerusahaan
        ));
    }
}

//GRAB ALL TOTAL DATA
$all = $db->get_var("SELECT COUNT(*) FROM tb_po WHERE IsPajak='$ispajak'");
if (!$all) $all = '';
$new = $db->get_var("SELECT COUNT(*) FROM tb_po WHERE IsPajak='$ispajak' AND Completed='0'");
if (!$new) $new = '';
$completed = $db->get_var("SELECT COUNT(*) FROM tb_po WHERE IsPajak='$ispajak' AND Completed='1'");
if (!$completed) $completed = '';

echo json_encode(array("po" => $PO, "supplier" => $supplier, "all" => $all, "new" => $new, "completed" => $completed, "grandTotal" => $grandTotal, "sisa" => $sisa));
