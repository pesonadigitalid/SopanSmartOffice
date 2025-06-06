<?php
session_start();
include_once "../config/connection.php";
include_once "../library/class.absencalculation.php";

$bulan = array("01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", "09" => "September", 10 => "Oktober", "11" => "November", 12 => "Desember");
$karyawanArray = array();

$tahun = $_GET['tahun'];
$bulan = $_GET['bulan'];
$karyawan = $_GET['karyawan'];

if ($tahun != "") $tahun = $tahun;
else $tahun = date("Y");
if ($bulan != "") $bulan = $bulan;
else $bulan = date("m");

$isKaryawanProyek = true;
$statusKaryawan = "";
$statusLainnya = "";
if ($karyawan != "") {
    $cond = "AND IDKaryawan='$karyawan'";
    $dkaryawan = $db->get_row("SELECT * FROM tb_karyawan WHERE IDKaryawan='$karyawan'");
    if ($dkaryawan) {
        $statusKaryawan = $dkaryawan->StatusKaryawan;
        $statusLainnya = $dkaryawan->StatusLainnya;
        if ($statusKaryawan == 'Harian'
            && ($statusLainnya == 'Kantor' || $statusLainnya == 'Maintenance')
        ) {
            $isKaryawanProyek = false;
        }
    }
}

$query = $db->get_results("SELECT * FROM tb_karyawan WHERE IDKaryawan>1 AND Status='1' AND StatusKaryawan='Harian' ORDER BY Nama ASC");
if ($query) {
    foreach ($query as $data) {
        array_push($karyawanArray, array("IDKaryawan" => $data->IDKaryawan, "IDProyek" => $data->IDProyek, "Nama" => $data->Nama));
    }
}

$absen = new AbsenCalculation();

if (!$isKaryawanProyek) {
    $absen->calcHolidayWithoutSunday($tahun);
    $return = $absen->generateAbsentBulananKaryawanHarian($tahun, $bulan, $karyawan, true);
} else {
    $return = $absen->generateAbsentBulananKaryawanHarian($tahun, $bulan, $karyawan);
}

$totalCutiTahunan = $absen->getTotalCutiTahunanBulanKaryawan($tahun, $bulan, $karyawan);
$totalCutiSakit = $absen->getTotalCutiSakitBulanKaryawan($tahun, $bulan, $karyawan);
$totalCutiSpecial = $absen->getTotalCutiSpecialBulanKaryawan($tahun, $bulan, $karyawan);
$totalCutiAlpha = $absen->getTotalAlphaBulanKaryawan($tahun, $bulan, $karyawan);
$totalCutiTugasKeluar = $absen->getTotalTugasBulanKaryawan($tahun, $bulan, $karyawan);
$totalJamKerja = $absen->getTotalJamKerjaBulanKaryawan($tahun, $bulan, $karyawan);
$totalJamLembur = $absen->getTotalJamLemburBulanKaryawan($tahun, $bulan, $karyawan);
$totalHariKerja = $absen->getTotalHariKerjaBulanKaryawan($tahun, $bulan, $karyawan);
$totalTerlambat = $absen->getTotalTerlambat($tahun, $bulan, $karyawan);

$proyekArray = array();
$query = $db->get_results("SELECT * FROM tb_proyek WHERE Status='2' ORDER BY Tahun DESC, KodeProyek ASC");
if ($query) {
    foreach ($query as $data) {
        array_push($proyekArray, array("IDProyek" => $data->IDProyek, "KodeProyek" => $data->KodeProyek, "Tahun" => $data->Tahun, "NamaProyek" => $data->NamaProyek));
    }
}

echo json_encode(array("data" => $return, "totalCutiTahunan" => $totalCutiTahunan, "totalCutiSakit" => $totalCutiSakit, "totalCutiSpecial" => $totalCutiSpecial, "totalCutiAlpha" => $totalCutiAlpha, "totalCutiTugasKeluar" => $totalCutiTugasKeluar, "totalJamKerja" => $totalJamKerja, "totalJamLembur" => $totalJamLembur, "totalHariKerja" => $totalHariKerja, "totalTerlambat" => $totalTerlambat, "karyawanArray" => $karyawanArray, "proyekArray" => $proyekArray, "statusKaryawan" => $statusKaryawan, "statusLainnya" => $statusLainnya));
