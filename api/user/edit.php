<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_POST['id']);
$nama = antiSQLInjection($_POST['nama']);
$email = antiSQLInjection($_POST['email']);
$hp = antiSQLInjection($_POST['hp']);
$level = antiSQLInjection($_POST['level']);
$usernm = antiSQLInjection($_POST['usernm']);
$passwd = antiSQLInjection($_POST['passwd']);
$status = antiSQLInjection($_POST['status']);

$cek = $db->get_row("SELECT * FROM tb_user WHERE Usernm='$usernm' AND IDUser!='$id'");
if($cek){
    echo "2";
} else {
    $query = "UPDATE tb_user SET Nama='$nama', Email='$email', HP='$hp', Level='$level', Usernm='$usernm', Status='$status'";
    
    if($passwd!="") $query .= ", Passwd='".md5($passwd)."'";
    if($id!="") $query .= " WHERE IDUser='$id'";
    echo $query;
    if($db->query($query)){
        echo "1";
    } else {
        echo "0";
    }
}