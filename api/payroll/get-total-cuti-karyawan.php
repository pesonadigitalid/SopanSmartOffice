<?php
include_once "../config/connection.php";
include_once "../library/class.absencalculation.php";
include_once "../library/class.cuticalculation.php";

$tahun = antiSQLInjection($_GET['tahun']);
$bulan = antiSQLInjection($_GET['bulan']);
$karyawan = antiSQLInjection($_GET['karyawan']);
$uang_makan_perhari = antiSQLInjection($_GET['uang_makan_perhari']);

$jmlCutiTahunan = $db->get_var("SELECT value FROM tb_system_config WHERE label='JUMLAHCUTITAHUNAN'");
$terbayar = $db->get_var("SELECT SUM(CutiMinus) FROM tb_slip_gaji WHERE IDKaryawan='$karyawan' AND GajiBulan<='$bulan' AND GajiTahun='$tahun'");
if(!$terbayar) $terbayar = 0;

$absen = new AbsenCalculation();
$absen->calcHolidayWithoutSunday($tahun);
$absen->generateAbsentBulananKaryawan($tahun,$bulan,$karyawan);
$totalHariKerja = $absen->getTotalHariKerjaBulanKaryawan($tahun,$bulan,$karyawan);
$totalJamLemburNormal = $absen->getTotalJamLemburNormal($tahun,$bulan,$karyawan);
$totalJamLemburHoliday = $absen->getTotalJamLemburHoliday($tahun,$bulan,$karyawan);
$totalLemburHari = $absen->getTotalLemburHariBulanan($tahun,$bulan,$karyawan);
$totalUangMakanLembur = $absen->getTotalUangMakanLembur($tahun,$bulan,$karyawan,$uang_makan_perhari);

$cutiCalculation = new CutiCalculation();
$cutiCalculation->calcHolidayWithoutSunday($tahun);
if($karyawan!="") $cutiCalculation->generateKalendarCutiKaryawan($tahun,$karyawan);

$totalCutiTahunan = $cutiCalculation->getTotalCutiKaryawanSetahun($tahun,$bulan,$karyawan,'CUTI TAHUNAN');
$totalAlpha = $absen->getTotalAlphaBulanKaryawan($tahun,$bulan,$karyawan);
// $totalCutiTahunan = $db->get_var("SELECT SUM(JumlahHari) FROM tb_cuti WHERE DATE_FORMAT(DariTanggal,'%Y-%m')<='$tahun-$bulan' AND DATE_FORMAT(DariTanggal,'%Y')>='$tahun'");
// if(!$totalCutiTahunan) $totalCutiTahunan=0;

$minus = $jmlCutiTahunan-$totalCutiTahunan+$terbayar;
if($minus>0) $minus=0; else $minus = abs($minus);

echo json_encode(array("Minus"=>$minus, "TotalHariKerja"=>$totalHariKerja, "TotalJamLemburNormal"=>$totalJamLemburNormal, "TotalJamLemburHoliday"=>$totalJamLemburHoliday, "TotalAlpha"=>$totalAlpha, "TotalLemburHari"=>$totalLemburHari, "TotalUangMakanLembur"=>$totalUangMakanLembur));