<?php
session_start();
include_once "../config/connection.php";

$tanggal = antiSQLInjection($_POST['tanggal']);
$exp = explode("/",$tanggal);
$tanggal = $exp[2]."-".$exp[1]."-".$exp[0];

$karyawan = antiSQLInjection($_POST['karyawan']);
$keterangan = antiSQLInjection($_POST['keterangan']);
$stts = antiSQLInjection($_POST['stts']);

$query = $db->query("INSERT INTO tb_cuti SET IDKaryawan='$karyawan', Tanggal='$tanggal', Keterangan='$keterangan', Status='$stts', CreatedBy='".$_SESSION["uid"]."', ModifiedBy='".$_SESSION["uid"]."', DateModified=NOW()");
if($query){
    echo "1";
} else {
    echo "0";
}