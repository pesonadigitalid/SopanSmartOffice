<?php
include_once "../config/connection.php";
$idr = antiSQLInjection($_POST['idr']);

$query = $db->query("DELETE FROM tb_proyek_file WHERE IDProyekFile='$idr'");
if($query){
    echo "1";
} else {
    echo "0";
}