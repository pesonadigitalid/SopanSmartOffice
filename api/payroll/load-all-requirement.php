<?php
include_once "../config/connection.php";
include_once "../library/class.absencalculation.php";

$bulan = array("01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", "09" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember");

$b = antiSQLInjection($_GET['bulan']);
$tahun = antiSQLInjection($_GET['tahun']);
$realBulan = $bulan[$b];

$karyawan = array();
$absen = new AbsenCalculation();
$absen->calcHolidayWithoutSunday($tahun);
$totalHariKerjaBulan = $absen->getTotalHariKerjaBulanan($tahun, $b);
$jmlCutiTahunan = $db->get_var("SELECT value FROM tb_system_config WHERE label='JUMLAHCUTITAHUNAN'");
$query = $db->get_results("SELECT * FROM tb_karyawan WHERE IDKaryawan NOT IN (SELECT IDKaryawan FROM tb_slip_gaji WHERE GajiBulan='$b' AND GajiTahun='$tahun') AND Status='1' AND IDKaryawan>1 AND StatusKaryawan<>'Harian' ORDER BY Nama ASC");
if ($query) {
    foreach ($query as $data) {
        $gaji = $db->get_row("SELECT * FROM tb_gaji_karyawan WHERE IDKaryawan='" . $data->IDKaryawan . "' ORDER BY IDGaji DESC");
        $isSM = 0;
        if ($data->IDJabatan == '5' || $data->IDJabatan == '6' || $data->IDJabatan == '7') $isSM = 1;
        array_push($karyawan, array("IDKaryawan" => $data->IDKaryawan, "NamaKaryawan" => $data->Nama, "TotalHariKerja" => $totalHariKerja, "GajiPokok" => $gaji->GajiPokok, "UangMakan" => $gaji->UangMakan, "UangPulsa" => $gaji->UangPulsa, "UangTransport" => $gaji->UangTransport, "UangPerformance" => $gaji->UangPerformance, "LainLain" => $gaji->LainLain, "TotalJamLemburNormal" => $totalJamLemburNormal, "TotalJamLemburHoliday" => $totalJamLemburHoliday, "isSM" => $isSM));
    }
}

echo json_encode(array("karyawan" => $karyawan, "totalHariKerjaBulan" => $totalHariKerjaBulan, "bulan" => $realBulan));
