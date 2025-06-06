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

if ($datestart != "" && $dateend != "") {
    $cond .= " AND a.DateCreated BETWEEN '$datestartchange' AND '$dateendchange' ";
} else if ($datestart != "") {
    $cond .= " AND a.DateCreated='$datestartchange' ";
} else {
    $cond .= " AND DATE_FORMAT(a.DateCreated,'%Y-%m') = '" . date("Y-m") . "' ";
}

if ($tipe != "") {
    $cond .= " AND a.JenisNotifikasi='$tipe' ";
}

if ($status != "") {
    $cond .= " AND a.Status='$status' ";
}

if ($_SESSION["IDJabatan"] == 15) { // teknisi
    $cond .= " AND a.IDKaryawan='" . $_SESSION["uid"] . "'";
}

$query = $db->get_results("SELECT a.*, b.NamaPelanggan AS NamaPelanggan, DATE_FORMAT(a.DateCreated,'%d/%m/%Y') AS Tanggal , DATE_FORMAT(a.TanggalAkhirMaintenance,'%d/%m/%Y') AS TanggalAkhirMaintenance FROM tb_notifikasi_service a, tb_pelanggan b WHERE a.IDPelanggan=b.IDPelanggan $cond ORDER BY a.IDNotifikasi ASC");
if ($query) {
    foreach ($query as $data) {
        if ($data->Status == "0") $data->Status = "DELETED";
        else if ($data->Status == "1") $data->Status = "NEW";
        else if ($data->Status == "2") $data->Status = "COMPLETED";
    }
} else {
    $query = array();
}

echo json_encode($query);
