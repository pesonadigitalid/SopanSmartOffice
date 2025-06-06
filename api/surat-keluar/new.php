<?php
include_once "../config/connection.php";

include_once "../library/class.awss3.php";
$AwsS3 = new AwsS3();

// $nosurat = antiSQLInjection($_POST['nosurat']);
$id_proyek = antiSQLInjection($_POST['id_proyek']);
$id_department = antiSQLInjection($_POST['id_department']);
$jenis = antiSQLInjection($_POST['jenis']);
$prihal = antiSQLInjection($_POST['prihal']);

$tanggal = antiSQLInjection($_POST['tanggal']);
$exp = explode("/", $tanggal);
$tanggal = $exp[2] . "-" . $exp[1] . "-" . $exp[0];
$tahun = $exp[2];
$bulan = $exp[1];

$deskripsi = antiSQLInjection($_POST['deskripsi']);
$bulanRomawi = array(1 => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");

if ($_FILES['file_surat']) {
    $suratName = $AwsS3->uploadFileDirect("surat_keluar",  $_FILES['file_surat']);
    $sqlCond .= ", FileSurat='$suratName'";
}

do {
    $HashCode = HASH_PREFIX . md5(date("YmdHis") . rand(1, 1000000000));
    $cek = $db->get_row("SELECT * FROM tb_surat_keluar WHERE HashCode='$HashCode'");
} while ($cek);

$kodeSuratDepartement = $db->get_var("SELECT PrefixSuratKeluar FROM tb_departement WHERE IDDepartement='$id_department'");
if (!$kodeSuratDepartement) $kodeSuratDepartement = "GENERAL";

$dataLast = $db->get_row("SELECT * FROM tb_surat_keluar WHERE DATE_FORMAT(Tanggal,'%Y')='$tahun' ORDER BY IDSuratKeluar DESC");
if ($dataLast) {
    $last = substr($dataLast->NoSurat, 0, 3);
    $last++;
    if ($last < 100 and $last >= 10)
        $last = "0" . $last;
    else if ($last < 10)
        $last = "00" . $last;
    $no_surat =  $last . "/" . $kodeSuratDepartement . "/" . $bulanRomawi[intval($bulan)] . "/" . $tahun;
} else {
    $no_surat =  "001/" . $kodeSuratDepartement . "/" . $bulanRomawi[intval($bulan)] . "/" . $tahun;
}

$query = $db->query("INSERT INTO tb_surat_keluar SET NoSurat='$no_surat', IDProyek='$id_proyek', IDDepartement='$id_department', Jenis='$jenis', Prihal='$prihal', Tanggal='$tanggal', Deskripsi='$deskripsi', HashCode='$HashCode', CreatedBy='" . $_SESSION["uid"] . "' $sqlCond");
if ($query) {
    echo "1";
} else {
    echo "0";
}
