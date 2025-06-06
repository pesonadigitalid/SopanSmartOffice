<?php
include_once "../config/connection.php";
$idr = antiSQLInjection($_POST['idr']);

$query = $db->query("UPDATE tb_reimburse SET Status='2' WHERE IDReimburse='$idr'");
if($query){
    echo "1";
} else {
    echo "0";
}