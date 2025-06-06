<?php
include_once "../config/connection.php";

$no_audit = antiSQLInjection($_POST['no_audit']);
$id_penjualan = antiSQLInjection($_POST['id_penjualan']);

$db->query("UPDATE tb_audit SET IDPenjualan='$id_penjualan' WHERE NoAudit='$no_audit'");
echo json_encode(array("res" => 0, "mes" => "Audit berhasil diperbaharui!"));
