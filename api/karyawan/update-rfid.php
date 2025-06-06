<?php
include_once "../config/connection.php";

$key = antiSQLInjection($_GET['key']);
$idk = antiSQLInjection($_GET['idk']);

$cek = $db->get_row("SELECT * FROM tb_karyawan WHERE CardNumber='$key' AND IDKaryawan!='$idk'");
if($cek){
    echo "2";
} else {
    $query = $db->query("UPDATE tb_karyawan SET CardNumber='$key' WHERE IDKaryawan='$idk'");
    if($query){
        echo "1";
    } else {
        echo "0";
    }
}