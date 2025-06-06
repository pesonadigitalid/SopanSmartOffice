<?php
include_once "../config/connection.php";
$idr = antiSQLInjection($_POST['idr']);

$db->query("UPDATE tb_penjualan SET IsComplete='0' WHERE IDPenjualan='$idr'");
echo "1";