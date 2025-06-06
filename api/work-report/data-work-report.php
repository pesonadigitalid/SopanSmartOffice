<?php
include_once "../config/connection.php";

$workReportArray = array();
$workScheduleArray = array();

$cond = "";

$datestart = antiSQLInjection($_GET['datestart']);
$expstart = explode("/", $datestart);
$datestartchange = $expstart[2] . "-" . $expstart[1] . "-" . $expstart[0];

$dateend = antiSQLInjection($_GET['dateend']);
$expend = explode("/", $dateend);
$dateendchange = $expend[2] . "-" . $expend[1] . "-" . $expend[0];

$work_schedule = antiSQLInjection($_GET['work_schedule']);

if ($datestart != "" && $dateend != "") {
    $cond .= " AND a.Tanggal BETWEEN '$datestartchange' AND '$dateendchange' ";
} else if ($datestart != "") {
    $cond .= " AND a.Tanggal='$datestartchange' ";
} else {
    $cond .= " AND DATE_FORMAT(a.Tanggal,'%Y-%m') = '" . date("Y-m") . "' ";
}

if ($work_schedule != "") {
    $cond .= " AND a.IDWorkSchedule='$work_schedule' ";
}

if ($_SESSION["IDJabatan"] == 15) { // teknisi
    $cond .= " AND b.IDKaryawan='" . $_SESSION["uid"] . "' AND a.CreatedBy='" . $_SESSION["uid"] . "'";
}

$query = $db->get_results("SELECT a.*, b.IDKaryawan, b.Judul, b.NoWorkSchedule, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID, b.RefID, b.IDPelanggan FROM tb_work_report a, tb_work_schedule b WHERE a.IDWorkSchedule=b.IDWorkSchedule $cond ORDER BY a.IDWorkReport ASC");
if ($query) {
    $i = 1;
    foreach ($query as $data) {
        $spb = $db->get_var("SELECT NoPenjualan FROM tb_penjualan WHERE IDPenjualan='" . $data->RefID . "'");
        $pelanggan = $db->get_var("SELECT NamaPelanggan FROM tb_pelanggan WHERE IDPelanggan='" . $data->IDPelanggan . "'");
        $judul = $spb . " / " . $pelanggan;
        $karyawan = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $data->IDKaryawan . "'");
        array_push($workReportArray, array("IDWorkReport" => $data->IDWorkReport, "NoWorkReport" => $data->NoWorkReport, "IDWorkSchedule" => $data->IDWorkSchedule, "NoWorkSchedule" => $data->NoWorkSchedule, "Judul" => $judul, "No" => $i, "Karyawan" => $karyawan, "Tanggal" => $data->TanggalID));
        $i++;
    }
}

$query = $db->get_results("SELECT a.*, b.Nama AS NamaKaryawan, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_work_schedule a, tb_karyawan b WHERE a.IDKaryawan=b.IDKaryawan ORDER BY a.IDWorkSchedule ASC");
if ($query) {
    $i = 1;
    foreach ($query as $data) {
        $spb = $db->get_var("SELECT NoPenjualan FROM tb_penjualan WHERE IDPenjualan='" . $data->RefID . "'");
        $pelanggan = $db->get_var("SELECT NamaPelanggan FROM tb_pelanggan WHERE IDPelanggan='" . $data->IDPelanggan . "'");
        $judul = $spb . " / " . $pelanggan;
        array_push($workScheduleArray, array("IDWorkSchedule" => $data->IDWorkSchedule, "NoWorkSchedule" => $data->NoWorkSchedule, "No" => $i, "Judul" => $judul));
        $i++;
    }
}

$return = array("workReportArray" => $workReportArray, "workScheduleArray" => $workScheduleArray);
echo json_encode($return);
