<?php
session_start();
include_once "../config/connection.php";
include_once "../library/class.cuticalculation.php";

$bulan = array("01"=>"Januari","02"=>"Februari","03"=>"Maret","04"=>"April","05"=>"Mei","06"=>"Juni","07"=>"Juli","08"=>"Agustus","09"=>"September",10=>"Oktober","11"=>"November",12=>"Desember");
$return = array();
$karyawanArray = array();
$tahun = $_GET['tahun'];
$karyawan = $_GET['karyawan'];
if($tahun!="") $tahun = $tahun; else $tahun = date("Y");
if($karyawan!="") $cond = "AND IDKaryawan='$karyawan'";

$cutiCalculation = new CutiCalculation();
$cutiCalculation->calcHolidayWithoutSunday();
if($karyawan!="") $cutiCalculation->generateKalendarCutiKaryawan($tahun,$karyawan);

function countSisaCuti(){
    global $return;
    global $jmlCutiTahunan;

    $sisaCuti = $jmlCutiTahunan;
    foreach ($return as $index => $value){
        $return[$index]['JumlahCutiTahunan'] = $sisaCuti;
        $sisaCuti = $sisaCuti - $return[$index]['CutiTahunan'];
        $return[$index]['SisaCuti'] = $sisaCuti;
    }
}

$jmlCutiTahunan = $db->get_var("SELECT value FROM tb_system_config WHERE label='JUMLAHCUTITAHUNAN'");

for($i=1;$i<=12;$i++){
    if($i<10) $bln = "0".$i; else $bln = $i;
    $totalCutiTahunan = $cutiCalculation->getTotalCutiBulananKaryawan($tahun,$bln,$karyawan,"CUTI TAHUNAN");
    $totalSakit = $cutiCalculation->getTotalCutiBulananKaryawan($tahun,$bln,$karyawan,"SAKIT");
    $totalCutiSpecial = $cutiCalculation->getTotalCutiBulananKaryawan($tahun,$bln,$karyawan,"CUTI SPECIAL");
    $totalAlpha = $cutiCalculation->getTotalCutiBulananKaryawan($tahun,$bln,$karyawan,"ALPHA");
    array_push($return,array("Bulan"=>$bulan[$bln],"JumlahCutiTahunan"=>0,"CutiTahunan"=>$totalCutiTahunan,"CutiSakit"=>$totalSakit,"CutiSpecial"=>$totalCutiSpecial,"CutiAlpha"=>$totalAlpha,"Total"=>0,"SisaCuti"=>0));
}

countSisaCuti();

$query = $db->get_results("SELECT * FROM tb_karyawan WHERE IDKaryawan>1 AND Status='1' ORDER BY Nama ASC");
if($query){
    foreach($query as $data){
        array_push($karyawanArray,array("IDKaryawan"=>$data->IDKaryawan,"Nama"=>$data->Nama));
    }
}

if($karyawan=="") $return=array();

echo json_encode(array("data"=>$return,"karyawanArray"=>$karyawanArray));

