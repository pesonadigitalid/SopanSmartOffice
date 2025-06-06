<?php
include_once "../config/connection.php";

include_once "../library/class.awss3.php";
$AwsS3 = new AwsS3();

$nosurat = antiSQLInjection($_POST['nosurat']);
$id_proyek = antiSQLInjection($_POST['id_proyek']);
$id_department = antiSQLInjection($_POST['id_department']);
$jenis = antiSQLInjection($_POST['jenis']);
$prihal = antiSQLInjection($_POST['prihal']);

$tanggal = antiSQLInjection($_POST['tanggal']);
$exp = explode("/", $tanggal);
$tanggal = $exp[2] . "-" . $exp[1] . "-" . $exp[0];

$deskripsi = antiSQLInjection($_POST['deskripsi']);

if ($_FILES['file_surat']) {
    $suratName = $AwsS3->uploadFileDirect("surat_masuk",  $_FILES['file_surat']);
    $sqlCond .= ", FileSurat='$suratName'";
}

do {
    $HashCode = HASH_PREFIX . md5(date("YmdHis") . rand(1, 1000000000));
    $cek = $db->get_row("SELECT * FROM tb_surat_masuk WHERE HashCode='$HashCode'");
} while ($cek);

// $dataLast = $db->get_row("SELECT * FROM tb_surat_masuk ORDER BY IDSuratMasuk DESC");
// if ($dataLast) {
//     $last = substr($dataLast->NoSurat, -5);
//     $last++;
//     if ($last < 10000 and $last >= 1000)
//         $last = "0" . $last;
//     else if ($last < 1000 and $last >= 100)
//         $last = "00" . $last;
//     else if ($last < 100 and $last >= 10)
//         $last = "000" . $last;
//     else if ($last < 10)
//         $last = "0000" . $last;
//     $no_surat = "LD/MKT/" . date("Y") . "/" . $last;
// } else {
//     $no_surat = "LD/MKT/" . date("Y") . "/00001";
// }

$query = $db->query("INSERT INTO tb_surat_masuk SET NoSurat='$nosurat', IDProyek='$id_proyek', IDDepartement='$id_department', Jenis='$jenis', Prihal='$prihal', Tanggal='$tanggal', Deskripsi='$deskripsi', HashCode='$HashCode', CreatedBy='" . $_SESSION["uid"] . "' $sqlCond");
if ($query) {
    echo "1";
} else {
    echo "0";
}
