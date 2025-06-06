<?php
include_once "../config/connection.php";
$id = $_GET['id'];

$workScheduleArray = array();
$workReportKategoriArray = array();

$cond = "";
if($_SESSION["IDJabatan"] == 15) { // teknisi
    $cond .= " AND a.IDKaryawan='".$_SESSION["uid"]."'";
}

$query = $db->get_results("SELECT a.*, b.Nama AS NamaKaryawan, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_work_schedule a, tb_karyawan b WHERE a.IDKaryawan=b.IDKaryawan $cond ORDER BY a.IDWorkSchedule ASC");
if ($query) {
    $i = 1;
    foreach ($query as $data) {
        $pelanggan = $db->get_var("SELECT NamaPelanggan FROM tb_pelanggan WHERE IDPelanggan='".$data->IDPelanggan."'");
        $spb = $db->get_var("SELECT NoPenjualan FROM tb_penjualan WHERE IDPenjualan='".$data->RefID."'");
        array_push($workScheduleArray, array("IDWorkSchedule" => $data->IDWorkSchedule, "NoWorkSchedule" => $data->NoWorkSchedule, "No" => $i, "Tipe" => $data->Tipe, "NamaTipe" => $data->Tipe == 1 ? 'PEMASANGAN SPB' : 'MAINTENANCE', "NoSPB" => $spb, "Pelanggan" => $pelanggan, "Tanggal" => $data->TanggalID, "Karyawan" => $data->NamaKaryawan, "Judul" => $data->Judul, "Keterangan" => $data->Keterangan));
        $i++;
    }
}

$query = $db->get_results("SELECT * FROM tb_work_report_file_category ORDER BY Nama ASC");
if ($query) {
    $i = 1;
    foreach ($query as $data) {
        $i++;
        $data->No = $i;
        $data->Status = ($data->Status == "1") ? true : false;

        array_push($workReportKategoriArray, $data);
    }
}

$return = array("workScheduleArray" => $workScheduleArray, "workReportKategoriArray" => $workReportKategoriArray);
echo json_encode($return);
