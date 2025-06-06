<?php
include_once "../config/connection.php";

$key = antiSQLInjection($_GET['key']);

$cek = $db->get_row("SELECT * FROM tb_karyawan WHERE CardNumber='$key'");
if($cek){
    if($cek->Status=="1")
        echo json_encode($cek);
    else
        echo "2";
} else {
    echo "0";
}