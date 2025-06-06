<?php
include_once "../config/connection.php";
$idr = antiSQLInjection($_POST['idr']);

$query = $db->query("DELETE FROM tb_contact_category WHERE IDContactCategory='$idr'");
if($query){
    echo "1";
} else {
    echo "0";
}