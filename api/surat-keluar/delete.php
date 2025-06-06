<?php
include_once "../config/connection.php";

include_once "../library/class.awss3.php";
$AwsS3 = new AwsS3();

$idr = antiSQLInjection($_POST['idr']);

// unlink the old one
$data = $db->get_row("SELECT * FROM tb_surat_keluar WHERE IDSuratKeluar='$idr'");
if ($data) {
    if ($data->FileSurat != "")
        $AwsS3->deleteFile("surat_keluar/" . $data->FileSurat);
}

// $query = $db->query("DELETE FROM tb_surat_keluar WHERE IDSuratKeluar='$idr'");
$query = $db->query("UPDATE tb_surat_keluar SET Status='0', DateModified=NOW(), ModifiedBy='" . $_SESSION["uid"] . "' WHERE IDSuratKeluar='$idr'");
if ($query) {
    echo "1";
} else {
    echo "0";
}
