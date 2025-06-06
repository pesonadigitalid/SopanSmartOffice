<?php
include_once "../config/connection.php";
$id = $_GET['id'];

$karyawanArray = array();
$spbArray = array();
$pelangganArray = array();

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

$query = $db->get_results("SELECT a.*, b.NamaPelanggan FROM tb_penjualan a, tb_pelanggan b WHERE a.IDPelanggan=b.IDPelanggan ORDER BY a.NoPenjualan ASC");
if ($query) {
    $i = 0;
    foreach ($query as $data) {
        $i++;
        array_push($spbArray, array("No" => $i, "IDPenjualan" => $data->IDPenjualan, "IDPelanggan" => $data->IDPelanggan, "NoPenjualan" => $data->NoPenjualan, "NamaPelanggan" => $data->NamaPelanggan));
    }
}

$query = $db->get_results("SELECT * FROM tb_pelanggan WHERE Status='1' ORDER BY NamaPelanggan");
if ($query) {
    $i = 1;
    foreach ($query as $data) {
        array_push($pelangganArray, array("IDPelanggan" => $data->IDPelanggan, "No" => $i, "NamaPelanggan" => $data->NamaPelanggan));
    }
}

$return = array("workScheduleArray" => $workScheduleArray, "karyawanArray" => $karyawanArray, "spbArray" => $spbArray, "pelangganArray" => $pelangganArray);
echo json_encode($return);
