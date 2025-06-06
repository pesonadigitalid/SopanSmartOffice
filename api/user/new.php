<?php
include_once "../config/connection.php";

$nama = antiSQLInjection($_POST['nama']);
$email = antiSQLInjection($_POST['email']);
$hp = antiSQLInjection($_POST['hp']);
$level = antiSQLInjection($_POST['level']);
$usernm = antiSQLInjection($_POST['usernm']);
$passwd = antiSQLInjection($_POST['passwd']);
$status = antiSQLInjection($_POST['status']);

$cek = $db->get_row("SELECT * FROM tb_user WHERE Usernm='$usernm'");
if($cek){
    echo "2";
} else {
    $query = $db->query("INSERT INTO tb_user SET Nama='$nama', Email='$email', HP='$hp', Level='$level', Usernm='$usernm', Passwd='".md5($passwd)."', Status='$status'");
    if($query){
        echo "1";
    } else {
        echo "0";
    }
}