<?php
session_start();
include_once "../config/connection.php";
include_once "../library/class.absencalculation.php";

$bulan = array("01"=>"Januari","02"=>"Februari","03"=>"Maret","04"=>"April","05"=>"Mei","06"=>"Juni","07"=>"Juli","08"=>"Agustus","09"=>"September",10=>"Oktober","11"=>"November",12=>"Desember");
$karyawanArray = array();

$tahun = $_GET['tahun'];
$bulan = $_GET['bulan'];
$karyawan = $_GET['karyawan'];

if($tahun!="") $tahun = $tahun; else $tahun = date("Y");
if($bulan!="") $bulan = $bulan; else $bulan = date("m");

if($karyawan!="") $cond = "AND IDKaryawan='$karyawan'";
        
$query = $db->get_results("SELECT * FROM tb_karyawan WHERE IDKaryawan>1 AND Status='1' AND StatusKaryawan<>'Harian' ORDER BY Nama ASC");
if($query){
    foreach($query as $data){
        array_push($karyawanArray,array("IDKaryawan"=>$data->IDKaryawan,"Nama"=>$data->Nama));
    }
}

$absen = new AbsenCalculation();
$absen->calcHolidayWithoutSunday($tahun);
$return = $absen->generateAbsentBulananKaryawan($tahun,$bulan,$karyawan);

$totalCutiTahunan = $absen->getTotalCutiTahunanBulanKaryawan($tahun,$bulan,$karyawan);
$totalCutiSakit = $absen->getTotalCutiSakitBulanKaryawan($tahun,$bulan,$karyawan);
$totalCutiSpecial = $absen->getTotalCutiSpecialBulanKaryawan($tahun,$bulan,$karyawan);
$totalCutiAlpha = $absen->getTotalAlphaBulanKaryawan($tahun,$bulan,$karyawan);
$totalCutiTugasKeluar = $absen->getTotalTugasBulanKaryawan($tahun,$bulan,$karyawan);
$totalJamKerja = $absen->getTotalJamKerjaBulanKaryawan($tahun,$bulan,$karyawan);
$totalJamLembur = $absen->getTotalJamLemburBulanKaryawan($tahun,$bulan,$karyawan);
$totalHariKerja = $absen->getTotalHariKerjaBulanKaryawan($tahun,$bulan,$karyawan);
$totalTerlambat = $absen->getTotalTerlambat($tahun,$bulan,$karyawan);

echo json_encode(array("data"=>$return,"totalCutiTahunan"=>$totalCutiTahunan,"totalCutiSakit"=>$totalCutiSakit,"totalCutiSpecial"=>$totalCutiSpecial,"totalCutiAlpha"=>$totalCutiAlpha,"totalCutiTugasKeluar"=>$totalCutiTugasKeluar,"totalJamKerja"=>$totalJamKerja,"totalJamLembur"=>$totalJamLembur,"totalHariKerja"=>$totalHariKerja,"totalTerlambat"=>$totalTerlambat,"karyawanArray"=>$karyawanArray));

