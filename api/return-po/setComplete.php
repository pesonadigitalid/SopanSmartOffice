<?php
include_once "../config/connection.php";
$idr = antiSQLInjection($_POST['idr']);

$db->query("UPDATE tb_po SET Completed='1' WHERE NoPO='$idr'");
echo "1";