<?php
include_once "../config/connection.php";

$kode_pelanggan = antiSQLInjection($_POST['kode_pelanggan']);
$nama = antiSQLInjection($_POST['nama']);
$alamat = antiSQLInjection($_POST['alamat']);
$kota = antiSQLInjection($_POST['kota']);
$provinsi = antiSQLInjection($_POST['provinsi']);
$kode_pos = antiSQLInjection($_POST['kode_pos']);
$no_telp = antiSQLInjection($_POST['no_telp']);
$no_fax = antiSQLInjection($_POST['no_fax']);
$email = antiSQLInjection($_POST['email']);

$website = antiSQLInjection($_POST['website']);

$kategori = antiSQLInjection($_POST['kategori']);
$jenis = antiSQLInjection($_POST['jenis']);
$status = antiSQLInjection($_POST['status']);
$namakp1 = antiSQLInjection($_POST['namakp1']);
$jabatankp1 = antiSQLInjection($_POST['jabatankp1']);
$emailkp1 = antiSQLInjection($_POST['emailkp1']);
$hpkp1 = antiSQLInjection($_POST['hpkp1']);
$namakp2 = antiSQLInjection($_POST['namakp2']);
$jabatankp2 = antiSQLInjection($_POST['jabatankp2']);
$emailkp2 = antiSQLInjection($_POST['emailkp2']);
$hpkp2 = antiSQLInjection($_POST['hpkp2']);

$dataLast = $db->get_row("SELECT * FROM tb_pelanggan ORDER BY KodePelanggan DESC");
if($dataLast){
    $last = intval($dataLast->KodePelanggan);
    $last++;
    if($last<10000 and $last>=1000)
        $kode_pelanggan = "0".$last;
    else if($last<1000 and $last>=100)
        $kode_pelanggan = "00".$last;
    else if($last<100 and $last>=10)
        $kode_pelanggan = "000".$last;
    else if($last<10)
        $kode_pelanggan = "0000".$last;
} else {
    $kode_pelanggan = "00001"; 
}

if($status=="") $status="0";
if(strlen($kode_pelanggan)>10){
    echo "3";
} else {
    $cek = $db->get_row("SELECT * FROM tb_pelanggan WHERE KodePelanggan='$kode_pelanggan'");
    if($cek){
        echo "2";
    } else {
        $cek = $db->get_row("SELECT * FROM tb_pelanggan WHERE NamaPelanggan='$nama'");
        if($cek){
            echo "4";
        } else {
            $query = $db->query("INSERT INTO tb_pelanggan SET KodePelanggan='$kode_pelanggan',NamaPelanggan='$nama', Alamat='$alamat', Kota='$kota', Provinsi='$provinsi', KodePos='$kode_pos', NoTelp='$no_telp', NoFax='$no_fax', Email='$email', Website='$website', Kategori='$kategori', Status='$status', Jenis='$jenis', NamaKP1='$namakp1', JabatanKP1='$jabatankp1', EmailKP1='$emailkp1', HPKP1='$hpkp1', NamaKP2='$namakp2', JabatanKP2='$jabatankp2', EmailKP2='$emailkp2', HPKP2='$hpkp2'");
            if($query){
                echo "1";
            } else {
                echo "0";
            }
        }
    }
}