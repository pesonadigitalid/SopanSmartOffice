<?php
include_once "../config/connection.php";

$workScheduleArray = array();

$cond = "";

$datestart = antiSQLInjection($_GET['datestart']);
$expstart = explode("/", $datestart);
$datestartchange = $expstart[2] . "-" . $expstart[1] . "-" . $expstart[0];

$dateend = antiSQLInjection($_GET['dateend']);
$expend = explode("/", $dateend);
$dateendchange = $expend[2] . "-" . $expend[1] . "-" . $expend[0];

$tipe = antiSQLInjection($_GET['tipe']);
$status = antiSQLInjection($_GET['status']);
$spb = antiSQLInjection($_GET['spb']);
$karyawan = antiSQLInjection($_GET['karyawan']);

if ($datestart != "" && $dateend != "") {
    $cond .= " AND a.Tanggal BETWEEN '$datestartchange' AND '$dateendchange' ";
} else if ($datestart != "") {
    $cond .= " AND a.Tanggal='$datestartchange' ";
} else {
    $cond .= " AND DATE_FORMAT(a.Tanggal,'%Y-%m') = '" . date("Y-m") . "' ";
}

if ($tipe != "") {
    $cond .= " AND a.Tipe='$tipe' ";
}

if ($status != "") {
    $cond .= " AND a.Status='$status' ";
}

if ($spb != "") {
    $cond .= " AND a.RefID='$spb' ";
}

if ($karyawan != "") {
    $cond .= " AND FIND_IN_SET('$karyawan', IDsKaryawan)";
}

if ($_SESSION["IDJabatan"] == 15) { // teknisi
    $cond .= " AND a.IDKaryawan='" . $_SESSION["uid"] . "'";
}

$query = $db->get_results("SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_work_schedule a WHERE a.IDWorkSchedule IS NOT NULL $cond ORDER BY a.IDWorkSchedule ASC");
if ($query) {
    $i = 1;
    foreach ($query as $data) {
        $status = "";
        if ($data->Status == "0") $status = "In-Progress";
        if ($data->Status == "1") $status = "Completed";

        $tipe = "";
        if ($data->Tipe == "1") $tipe = "Pemasangan Unit Water Heater";
        if ($data->Tipe == "2") $tipe = "Service / Maintenance Unit Water Heater";
        if ($data->Tipe == "3") $tipe = "Survey Unit Water Heater";
        if ($data->Tipe == "4") $tipe = "Pengiriman Unit";

        $karyawan = $db->get_results("SELECT Nama FROM tb_karyawan WHERE IDKaryawan IN (" . $data->IDsKaryawan . ")");
        $karyawans = trim(implode(", ", array_map(function ($k) {
            return $k->Nama;
        }, $karyawan)));

        $pelanggan = $db->get_var("SELECT NamaPelanggan FROM tb_pelanggan WHERE IDPelanggan='" . $data->IDPelanggan . "'");
        $spb = $db->get_var("SELECT NoPenjualan FROM tb_penjualan WHERE IDPenjualan='" . $data->RefID . "'");

        array_push($workScheduleArray, array("IDWorkSchedule" => $data->IDWorkSchedule, "NoWorkSchedule" => $data->NoWorkSchedule, "No" => $i, "Tipe" => $data->Tipe, "NamaTipe" =>  $tipe, "NoSPB" => $spb, "Pelanggan" => $pelanggan, "Tanggal" => $data->TanggalID, "Karyawan" => $karyawans, "Judul" => $data->Judul, "Keterangan" => $data->Keterangan, "Keterangan" => $data->Keterangan, "Status" => $status));
        $i++;
    }
}

$spbArray = array();
$query = $db->get_results("SELECT a.*, b.NamaPelanggan FROM tb_penjualan a, tb_pelanggan b WHERE a.IDPelanggan=b.IDPelanggan ORDER BY a.NoPenjualan ASC");
if ($query) {
    $i = 0;
    foreach ($query as $data) {
        $i++;
        array_push($spbArray, array("No" => $i, "IDPenjualan" => $data->IDPenjualan, "IDPelanggan" => $data->IDPelanggan, "NoPenjualan" => $data->NoPenjualan, "NamaPelanggan" => $data->NamaPelanggan));
    }
}

$karyawanArray = array();
$query = $db->get_results("SELECT * FROM tb_karyawan WHERE IDKaryawan>1 AND Status='1' ORDER BY Nama");
if ($query) {
    $i = 0;
    foreach ($query as $data) {
        $i++;
        if ($data->Status == "1") $status = "Aktif";
        else $status = "Non Aktif";
        $jabatan = $db->get_var("SELECT Jabatan FROM tb_jabatan WHERE IDJabatan='" . $data->IDJabatan . "'");
        array_push($karyawanArray, array("No" => $i, "NIK" => $data->NIK, "Nama" => $data->Nama, "Status" => $data->StatusLainnya, "Jabatan" => $jabatan, "StatusK" => $status, "IDKaryawan" => $data->IDKaryawan, "CardNumber" => $data->CardNumber));
    }
}

$return = array("workScheduleArray" => $workScheduleArray, "spbArray" => $spbArray, "karyawanArray" => $karyawanArray);
echo json_encode($return);
