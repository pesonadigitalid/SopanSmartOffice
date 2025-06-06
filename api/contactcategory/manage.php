<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_POST['id']);
$jenis = antiSQLInjection($_POST['jenis']);
$idC = antiSQLInjection($_POST['idC']);

$cek = $db->get_row("SELECT * FROM tb_contact_category_user WHERE type='$jenis' AND id='$id' AND id_category='$idC'");
if($cek){
    $query = $db->query("DELETE FROM tb_contact_category_user WHERE type='$jenis' AND id='$id' AND id_category='$idC'");
    if($query){
        echo "2";
    } else {
        echo "3";
    }
} else {
    $query = $db->query("INSERT INTO tb_contact_category_user SET type='$jenis', id='$id', id_category='$idC'");
    if($query){
        echo "1";
    } else {
        echo "0";
    }
}
