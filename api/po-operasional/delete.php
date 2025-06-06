<?php
include_once "../config/connection.php";
$idr = antiSQLInjection($_POST['idr']);

$cek = $db->get_row("SELECT * FROM tb_penerimaan_stok WHERE NoPO='$idr'");
if($cek){
	echo "2";
} else {
	$query = $db->query("DELETE FROM tb_po WHERE NoPO='$idr'");
	if($query){
		$db->query("DELETE FROM tb_po_detail WHERE NoPO='$idr'");
	    echo "1";
	} else {
	    echo "0";
	}
}