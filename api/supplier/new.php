<?php
include_once "../config/connection.php";

$kode_supplier = antiSQLInjection($_POST['kode_supplier']);
$nama_perusahaan = antiSQLInjection($_POST['nama_perusahaan']);

$alamat = antiSQLInjection($_POST['alamat']);
$kota = antiSQLInjection($_POST['kota']);
$provinsi = antiSQLInjection($_POST['provinsi']);
$kode_pos = antiSQLInjection($_POST['kode_pos']);
$no_telp = antiSQLInjection($_POST['no_telp']);
$no_fax = antiSQLInjection($_POST['no_fax']);
$email = antiSQLInjection($_POST['email']);

$website = antiSQLInjection($_POST['website']);

$deskripsi = antiSQLInjection($_POST['deskripsi']);
$kategori = antiSQLInjection($_POST['kategori']);
$kategori21 = json_decode(antiSQLInjection($_POST['kategori2']));
$status = antiSQLInjection($_POST['status']);
$namakp1 = antiSQLInjection($_POST['namakp1']);
$jabatankp1 = antiSQLInjection($_POST['jabatankp1']);
$emailkp1 = antiSQLInjection($_POST['emailkp1']);
$hpkp1 = antiSQLInjection($_POST['hpkp1']);
$namakp2 = antiSQLInjection($_POST['namakp2']);
$jabatankp2 = antiSQLInjection($_POST['jabatankp2']);
$emailkp2 = antiSQLInjection($_POST['emailkp2']);
$hpkp2 = antiSQLInjection($_POST['hpkp2']);

$kategori2 = "";
foreach($kategori21 as $key => $value){
    if(isset($value) && $value=="true"){
        $kategori2 .= $key.",";
    }
}

$kategori2 = substr($kategori2, 0, -1);

$dataLast = $db->get_row("SELECT * FROM tb_supplier ORDER BY KodeSupplier DESC");
if($dataLast){
    $last = intval($dataLast->KodeSupplier);
    $last++;
    if($last<10000 and $last>=1000)
        $kode_supplier = "0".$last;
    else if($last<1000 and $last>=100)
        $kode_supplier = "00".$last;
    else if($last<100 and $last>=10)
        $kode_supplier = "000".$last;
    else if($last<10)
        $kode_supplier = "0000".$last;
} else {
    $kode_supplier = "00001"; 
}

if($status=="") $status="0";
if(strlen($kode_supplier)>10){
    echo "3";
} else {
    $cek = $db->get_row("SELECT * FROM tb_supplier WHERE KodeSupplier='$kode_supplier'");
    if($cek){
        echo "2";
    } else {
        $query = $db->query("INSERT INTO tb_supplier SET KodeSupplier='$kode_supplier',NamaPerusahaan='$nama_perusahaan', Alamat='$alamat', Kota='$kota', Provinsi='$provinsi', KodePos='$kode_pos', NoTelp='$no_telp', NoFax='$no_fax', Email='$email', Website='$website', Deskripsi='$deskripsi', Kategori='$kategori', Kategori2='$kategori2', Status='$status', NamaKP1='$namakp1', JabatanKP1='$jabatankp1', EmailKP1='$emailkp1', HPKP1='$hpkp1', NamaKP2='$namakp2', JabatanKP2='$jabatankp2', EmailKP2='$emailkp2', HPKP2='$hpkp2'");
        if($query){
            echo "1";
        } else {
            echo "0";
        }
    }
}