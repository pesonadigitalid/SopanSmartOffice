<?php
include_once "../config/connection.php";
include_once "../library/class.stok.php";

$stok = new Stok($db);

$idr = antiSQLInjection($_POST['idr']);
$remark = antiSQLInjection($_POST['remark']);

if ($stok->CheckAllowDeleteAuditStokGudang($idr)) {
    $stok->DeleteAuditStokGudang($idr);
    $db->query("UPDATE tb_audit SET Status='2', DeletedRemark='$remark', DeletedDate=NOW(), DeletedBy='" . $_SESSION['uid'] . "', ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW() WHERE NoAudit='$idr'");
    echo "1";
} else {
    echo "2";
}
