<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_POST['id']);

$tanggal = antiSQLInjection($_POST['tanggal']);
$exp = explode("/",$tanggal);
$tanggal = $exp[2]."-".$exp[1]."-".$exp[0];

$bulan = antiSQLInjection($_POST['bulan']);
$tahun = antiSQLInjection($_POST['tahun']);
$total_absen = antiSQLInjection($_POST['total_absen']);
$nik = antiSQLInjection($_POST['nik']);
$nama = antiSQLInjection($_POST['nama']);
$gaji_pokok = antiSQLInjection($_POST['gaji_pokok']);
$total_uang_makan = antiSQLInjection($_POST['total_uang_makan']);
$total_uang_transport = antiSQLInjection($_POST['total_uang_transport']);
$uang_pulsa = antiSQLInjection($_POST['uang_pulsa']);
$tunjangan_performance = antiSQLInjection($_POST['tunjangan_performance']);
$potongan = antiSQLInjection($_POST['potongan']);
$potongan_lain2 = antiSQLInjection($_POST['potongan_lain2']);
$total_gaji = antiSQLInjection($_POST['total_gaji']);
$idkaryawan = antiSQLInjection($_POST['idkaryawan']);
$uang_makan_perhari = antiSQLInjection($_POST['uang_makan_perhari']);
$uang_transport_perhari = antiSQLInjection($_POST['uang_transport_perhari']);
$uploaded = antiSQLInjection($_POST['uploaded']); 

$datakaryawan = $db->get_row("SELECT * FROM tb_karyawan WHERE IDKaryawan='$idkaryawan' ORDER BY IDKaryawan");
$jabatan = $db->get_var("SELECT Jabatan FROM tb_jabatan WHERE IDJabatan='".$datakaryawan->IDJabatan."'");

$query = $db->query("UPDATE tb_slip_gaji SET IDKaryawan='$idkaryawan', NIK='".$datakaryawan->NIK."', NamaKaryawan='$nama', Tanggal='$tanggal', GajiBulan='$bulan', GajiTahun='$tahun', Jabatan='$jabatan', Alamat='".$datakaryawan->AlamatKTP."', Telp='".$datakaryawan->NoTelp."', GajiPokok='$gaji_pokok', UangMakanHarian='$uang_makan_perhari', UangTransportHarian='$uang_transport_perhari', TotalAbsen='$total_absen', UangMakan='$total_uang_makan', UangTransport='$total_uang_transport', UangPulsa='$uang_pulsa', UangTunjanganPerformance='$tunjangan_performance', TotalGaji='$total_gaji', DibuatOleh='$uploaded' WHERE IDKaryawan='$id'");
if($query){
    echo "1";
} else {
    echo "0";
}