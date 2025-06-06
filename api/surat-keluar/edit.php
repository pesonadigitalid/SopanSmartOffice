<?php
include_once "../config/connection.php";

include_once "../library/class.awss3.php";
$AwsS3 = new AwsS3();

$id = antiSQLInjection($_POST['id']);
$id_proyek = antiSQLInjection($_POST['id_proyek']);
$id_department = antiSQLInjection($_POST['id_department']);
$jenis = antiSQLInjection($_POST['jenis']);
$prihal = antiSQLInjection($_POST['prihal']);

$tanggal = antiSQLInjection($_POST['tanggal']);
$exp = explode("/", $tanggal);
$tanggal = $exp[2] . "-" . $exp[1] . "-" . $exp[0];

$deskripsi = antiSQLInjection($_POST['deskripsi']);

if ($_FILES['file_surat']) {
    // unlink the old one
    $data = $db->get_row("SELECT * FROM tb_surat_keluar WHERE IDSuratKeluar='$id'");
    if ($data) {
        if ($data->FileSurat != "")
            $AwsS3->deleteFile("surat_keluar/" . $data->FileSurat);
    }
    $suratName = $AwsS3->uploadFileDirect("surat_keluar",  $_FILES['file_surat']);
    $sqlCond .= ", FileSurat='$suratName'";
}

$query = $db->query("UPDATE tb_surat_keluar SET IDProyek='$id_proyek', IDDepartement='$id_department', Jenis='$jenis', Prihal='$prihal', Tanggal='$tanggal', Deskripsi='$deskripsi', DateModified=NOW(), ModifiedBy='" . $_SESSION["uid"] . "' $sqlCond WHERE IDSuratKeluar='$id'");
if ($query) {
    echo "1";
} else {
    echo "0";
}
