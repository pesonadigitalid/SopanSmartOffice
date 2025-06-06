<?php
include_once "../config/connection.php";

include_once "../library/class.awss3.php";
$AwsS3 = new AwsS3();

$idr = antiSQLInjection($_POST['idr']);

// unlink the old one
$data = $db->get_row("SELECT * FROM tb_surat_masuk WHERE IDSuratMasuk='$idr'");
if ($data) {
    if ($data->FileSurat != "")
        $AwsS3->deleteFile("surat_masuk/" . $data->FileSurat);
}

// $query = $db->query("DELETE FROM tb_surat_masuk WHERE IDSuratMasuk='$idr'");
$query = $db->query("UPDATE tb_surat_masuk SET Status='0', DateModified=NOW(), ModifiedBy='" . $_SESSION["uid"] . "' WHERE IDSuratMasuk='$idr'");
if ($query) {
    echo "1";
} else {
    echo "0";
}
