<?php
include_once "../config/connection.php";

$id_karyawan = antiSQLInjection($_POST['id_karyawan']);
$efektif_bln = antiSQLInjection($_POST['efektif_bln']);
$efektif_thn = antiSQLInjection($_POST['efektif_thn']);
$gaji_pokok = antiSQLInjection($_POST['gaji_pokok']);
$uang_makan = antiSQLInjection($_POST['uang_makan']);
$uang_pulsa = antiSQLInjection($_POST['uang_pulsa']);
$uang_transport = antiSQLInjection($_POST['uang_transport']);
$uang_performance = antiSQLInjection($_POST['uang_performance']);
$uang_lain2 = antiSQLInjection($_POST['uang_lain2']);
$id = antiSQLInjection($_POST['id']);

$total =$gaji_pokok+$uang_makan+$uang_pulsa+$uang_transport+$uang_performance+$uang_lain2;

if($id!=""){
    $sql = "UPDATE tb_gaji_karyawan SET IDKaryawan='$id_karyawan', EfektifBulan='$efektif_bln', EfektifTahun='$efektif_thn', GajiPokok='$gaji_pokok', UangMakan='$uang_makan', UangPulsa='$uang_pulsa', UangTransport='$uang_transport', UangPerformance='$uang_performance', LainLain='$uang_lain2', Total='$total' WHERE IDGaji='$id'";
    $query = $db->query($sql);
    if($query){
        echo "1";
    } else {
        echo "0";
    }
} else {
    $sql = "INSERT INTO tb_gaji_karyawan SET IDKaryawan='$id_karyawan', EfektifBulan='$efektif_bln', EfektifTahun='$efektif_thn', GajiPokok='$gaji_pokok', UangMakan='$uang_makan', UangPulsa='$uang_pulsa', UangTransport='$uang_transport', UangPerformance='$uang_performance', LainLain='$uang_lain2', Total='$total', Status='1'";
    $query = $db->query($sql);
    if($query){
        $lastID = mysql_insert_id();
        $update = $db->query("UPDATE tb_gaji_karyawan SET Status='0' WHERE IDKaryawan='$id_karyawan' AND IDGaji!='$lastID'");
        echo "1";
    } else {
        echo "0";
    }
}

