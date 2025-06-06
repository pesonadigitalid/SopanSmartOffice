<?php
include_once "../config/connection.php";
$idr = antiSQLInjection($_POST['idr']);
$remark = antiSQLInjection($_POST['remark']);

$cek = $db->get_row("SELECT * FROM tb_penerimaan_stok WHERE NoPO='$idr' AND DeletedDate IS NULL");
$data = $db->get_row("SELECT * FROM tb_po WHERE NoPO='$idr'");
if ($cek) {
	echo "2";
} else {
	$query = $db->query("UPDATE tb_po SET Completed='2', DeletedRemark='$remark', DeletedDate=NOW(), DeletedBy='" . $_SESSION['uid'] . "' WHERE NoPO='$idr'");
	if ($query) {
		// $db->query("DELETE FROM tb_po_detail WHERE NoPO='$idr'");
		echo "1";
	} else {
		echo "0";
	}
}
