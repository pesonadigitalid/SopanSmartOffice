<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_POST['id']);
$tanggal = antiSQLInjection($_POST['tanggal']);
$exp = explode("/",$tanggal);
$tanggal = $exp[2]."-".$exp[1]."-".$exp[0];

$karyawan = antiSQLInjection($_POST['karyawan']);
$keterangan = antiSQLInjection($_POST['keterangan']);
$stts = antiSQLInjection($_POST['stts']);

if($status=="") $status="0";
if(strlen($kode_supplier)>10){
    echo "2";
} else {
    $query = $db->query("UPDATE tb_cuti SET IDKaryawan='$karyawan', Tanggal='$tanggal', Keterangan='$keterangan', Status='$stts', ModifiedBy='".$_SESSION["uid"]."', DateModified=NOW() WHERE IDCuti='$id'");
    if($query){
        echo "1";
    } else {
        echo "0";
    }
}