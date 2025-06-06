<?php
include_once "../config/connection.php";

include_once "../library/class.awss3.php";
$AwsS3 = new AwsS3();

$id = antiSQLInjection($_POST['id']);
$id_proyek = antiSQLInjection($_POST['id_proyek']);
$nosurat = antiSQLInjection($_POST['nosurat']);
$id_department = antiSQLInjection($_POST['id_department']);
$jenis = antiSQLInjection($_POST['jenis']);
$prihal = antiSQLInjection($_POST['prihal']);

$tanggal = antiSQLInjection($_POST['tanggal']);
$exp = explode("/", $tanggal);
$tanggal = $exp[2] . "-" . $exp[1] . "-" . $exp[0];

$deskripsi = antiSQLInjection($_POST['deskripsi']);

if ($_FILES['file_surat']) {
    // unlink the old one
    $data = $db->get_row("SELECT * FROM tb_surat_masuk WHERE IDSuratMasuk='$id'");
    if ($data) {
        if ($data->FileSurat != "")
            $AwsS3->deleteFile("surat_masuk/" . $data->FileSurat);
    }

    $suratName = $AwsS3->uploadFileDirect("surat_masuk",  $_FILES['file_surat']);
    $sqlCond .= ", FileSurat='$suratName'";
}

$query = $db->query("UPDATE tb_surat_masuk SET NoSurat='$nosurat', IDProyek='$id_proyek', IDDepartement='$id_department', Jenis='$jenis', Prihal='$prihal', Tanggal='$tanggal', Deskripsi='$deskripsi', DateModified=NOW(), ModifiedBy='" . $_SESSION["uid"] . "' $sqlCond WHERE IDSuratMasuk='$id'");
if ($query) {
    echo "1";
} else {
    echo "0";
}
