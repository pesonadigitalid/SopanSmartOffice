<?php
include_once "../config/connection.php";

include_once "../library/class.fungsi.php";
$fungsi = new Fungsi();

$tipe = antiSQLInjection($_POST['tipe']);
$spb = antiSQLInjection($_POST['spb']);
$pelanggan = antiSQLInjection($_POST['pelanggan']);
$karyawan = antiSQLInjection($_POST['karyawan']);
$karyawan_ids = antiSQLInjection($_POST['karyawan_ids']);

$tanggal = antiSQLInjection($_POST['tanggal']);
$exp = explode("/", $tanggal);
$tanggal = $exp[2] . "-" . $exp[1] . "-" . $exp[0];

$judul = antiSQLInjection($_POST['judul']);
$keterangan = antiSQLInjection($_POST['keterangan']);
$pic_pelanggan = antiSQLInjection($_POST['pic_pelanggan']);
$jenis_unit = antiSQLInjection($_POST['jenis_unit']);
$no_tangki = antiSQLInjection($_POST['no_tangki']);
$no_panel_a = antiSQLInjection($_POST['no_panel_a']);
$no_panel_b = antiSQLInjection($_POST['no_panel_b']);
$no_panel_c = antiSQLInjection($_POST['no_panel_c']);
$no_tangki_heatpump = antiSQLInjection($_POST['no_tangki_heatpump']);
$no_outdoor_heatpump = antiSQLInjection($_POST['no_outdoor_heatpump']);
$status = antiSQLInjection($_POST['status']);

$no_work_schedule = $fungsi->GetNoFaktur($_POST['tanggal'], "tb_work_schedule", "NoWorkSchedule", "WO/SPN/");

$sql = "INSERT INTO tb_work_schedule SET Tipe='$tipe', RefID='$spb', NoWorkSchedule='$no_work_schedule', IDPelanggan='$pelanggan', IDKaryawan='$karyawan', IDsKaryawan='$karyawan_ids', Tanggal='$tanggal', Judul='$judul', Keterangan='$keterangan', Status='$status', CreatedBy='" . $_SESSION["uid"] . "', PICPelanggan='$pic_pelanggan', JenisUnit='$jenis_unit', NoTangki='$no_tangki', NoPanelA='$no_panel_a', NoPanelB='$no_panel_b', NoPanelC='$no_panel_c', NoTangkiHeatpump='$no_tangki_heatpump', NoOutdoorHeatpump='$no_outdoor_heatpump'";

//echo $sql;
$query = $db->query($sql);
if ($query) {
    echo "1";
} else {
    echo "0";
}
