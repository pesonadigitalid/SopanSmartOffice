<?php
include_once "../config/connection.php";

$IDPenjualanFileCategory = antiSQLInjection($_POST['IDPenjualanFileCategory']);
$Nama = antiSQLInjection($_POST['Nama']);
$Status = antiSQLInjection($_POST['Status']);
if ($Status == "") $Status = 0;

$query = $db->query("UPDATE tb_penjualan_file_category SET  Nama='$Nama', Status='$Status', ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW() WHERE IDPenjualanFileCategory='$IDPenjualanFileCategory'");
if ($query) {
    echo "1";
} else {
    echo "0";
}
