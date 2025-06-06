<?php
include_once "../config/connection.php";
$idr = antiSQLInjection($_POST['idr']);

$query = $db->query("DELETE a.*, b.* FROM tb_pengiriman a, tb_pengiriman_detail b WHERE a.NoPengiriman='$idr' AND b.NoPengiriman='$idr'");
if($query){
    echo "1";
} else {
    echo "0";
}