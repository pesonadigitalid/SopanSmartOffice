<?php
include_once "../config/connection.php";

$id_parent = antiSQLInjection($_POST['id_parent']);
$id_barang = antiSQLInjection($_POST['id_barang']);
$qty = antiSQLInjection($_POST['qty']);

$HargaPublish = $db->get_var("SELECT HargaPublish FROM tb_barang WHERE IDBarang='$id_barang'");
if (!$HargaPublish) $HargaPublish = 0;

$query = $db->query("INSERT INTO tb_barang_child SET IDParent='$id_parent', IDBarang='$id_barang', Qty='$qty', HargaPublish='$HargaPublish'");
if ($query) {
    echo "1";
} else {
    echo "0";
}
