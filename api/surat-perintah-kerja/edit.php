<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_POST['id']);
$tanggal = antiSQLInjection($_POST['tanggal']);
$exp = explode('/',$tanggal);
$tanggal = $exp[2]."-".$exp[1]."-".$exp[0];

$karyawan = antiSQLInjection($_POST['karyawan']);
$nama_perusahaan = antiSQLInjection($_POST['nama_perusahaan']);
$alamat = antiSQLInjection($_POST['alamat']);
$no_telp = antiSQLInjection($_POST['no_telp']);
$rencana_tugas = antiSQLInjection($_POST['rencana_tugas']);

$tgl_mulai = antiSQLInjection($_POST['tgl_mulai']);
$exptglmulai = explode('/',$tgl_mulai);
$tgl_mulai = $exptglmulai[2]."-".$exptglmulai[1]."-".$exptglmulai[0];

$tgl_akhir = antiSQLInjection($_POST['tgl_akhir']);
$exptglakhir = explode('/',$tgl_akhir);
$tgl_akhir = $exptglakhir[2]."-".$exptglakhir[1]."-".$exptglakhir[0];

$jam_mulai = antiSQLInjection($_POST['jam_mulai']);
$jam_akhir = antiSQLInjection($_POST['jam_akhir']);
$catatan = antiSQLInjection($_POST['catatan']);
$statusspk = antiSQLInjection($_POST['statusspk']);
$iduser = antiSQLInjection($_POST['iduser']);

$query = $db->query("UPDATE tb_surat_perintah_kerja SET Tanggal='$tanggal', IDKaryawan='$karyawan', NamaPerusahaan='$nama_perusahaan', Alamat='$alamat', Telp='$no_telp', RencanaTugas='$rencana_tugas', TanggalMulai='$tgl_mulai', TanggalAkhir='$tgl_akhir', JamMulai='$jam_mulai', JamAkhir='$jam_akhir', Catatan='$catatan', Status='$statusspk', CreatedBy='$iduser' WHERE IDSPK='$id'");
if($query){
    echo "1";
} else {
    echo "0";
}