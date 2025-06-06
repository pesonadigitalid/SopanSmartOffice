<?php
session_start();
include_once "../config/connection.php";
include_once "../library/class.absencalculation.php";

$karyawanArray = array();

$start = $fungsi->ENDate($_GET['start']);
$end = $fungsi->ENDate($_GET['end']);
$karyawan = $_GET['karyawan'];

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
        array_push($karyawanArray, array("IDKaryawan" => $data->IDKaryawan, "Nama" => $data->Nama));
    }
}

$absen = new AbsenCalculation();
if (!$isKaryawanProyek) {
    $absen->calcHolidayWithoutSunday($tahun);
    $return = $absen->generateAbsentPeriodeKaryawanHarian($start, $end, $karyawan, true);
} else {
    $return = $absen->generateAbsentPeriodeKaryawanHarian($start, $end, $karyawan);
}

$totalCutiTahunan = $absen->getTotalCutiTahunanPeriodeKaryawan($start, $end, $karyawan);
$totalCutiSakit = $absen->getTotalCutiSakitPeriodeKaryawan($start, $end, $karyawan);
$totalCutiSpecial = $absen->getTotalCutiSpecialPeriodeKaryawan($start, $end, $karyawan);
$totalCutiAlpha = $absen->getTotalAlphaPeriodeKaryawan($start, $end, $karyawan);
$totalCutiTugasKeluar = $absen->getTotalTugasPeriodeKaryawan($start, $end, $karyawan);
$totalJamKerja = $absen->getTotalJamKerjaPeriodeKaryawan($start, $end, $karyawan);
$totalJamLembur = $absen->getTotalJamLemburPeriodeKaryawan($start, $end, $karyawan);
$totalHariKerja = $absen->getTotalHariKerjaPeriodeKaryawan($start, $end, $karyawan);
$totalTerlambat = $absen->getTotalTerlambatPeriode($start, $end, $karyawan);

$proyekArray = array();
$query = $db->get_results("SELECT * FROM tb_proyek WHERE Status='2' ORDER BY Tahun DESC, KodeProyek ASC");
if ($query) {
    foreach ($query as $data) {
        array_push($proyekArray, array("IDProyek" => $data->IDProyek, "KodeProyek" => $data->KodeProyek, "Tahun" => $data->Tahun, "NamaProyek" => $data->NamaProyek));
    }
}

echo json_encode(array("data" => $return, "totalCutiTahunan" => $totalCutiTahunan, "totalCutiSakit" => $totalCutiSakit, "totalCutiSpecial" => $totalCutiSpecial, "totalCutiAlpha" => $totalCutiAlpha, "totalCutiTugasKeluar" => $totalCutiTugasKeluar, "totalJamKerja" => $totalJamKerja, "totalJamLembur" => $totalJamLembur, "totalHariKerja" => $totalHariKerja, "totalTerlambat" => $totalTerlambat, "karyawanArray" => $karyawanArray, "proyekArray" => $proyekArray));
