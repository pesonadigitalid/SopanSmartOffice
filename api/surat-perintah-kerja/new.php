<?php
include_once "../config/connection.php";

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

$dataLast = $db->get_row("SELECT * FROM tb_surat_perintah_kerja ORDER BY IDSPK DESC");
if($dataLast){
    $last = substr($dataLast->NoSPK,-5);
    $last++;
    if($last<10000 and $last>=1000)
        $last = "0".$last;
    else if($last<1000 and $last>=100)
        $last = "00".$last;
    else if($last<100 and $last>=10)
        $last = "000".$last;
    else if($last<10)
        $last = "0000".$last;
    $no_spk = "LD/SPK/".date("Y")."/".$last;  
} else {
    $no_spk = "LD/SPK/".date("Y")."/00001"; 
}

$query = $db->query("INSERT INTO tb_surat_perintah_kerja SET NoSPK='$no_spk', Tanggal='$tanggal', IDKaryawan='$karyawan', NamaPerusahaan='$nama_perusahaan', Alamat='$alamat', Telp='$no_telp', RencanaTugas='$rencana_tugas', TanggalMulai='$tgl_mulai', TanggalAkhir='$tgl_akhir', JamMulai='$jam_mulai', JamAkhir='$jam_akhir', Catatan='$catatan', Status='$statusspk', CreatedBy='$iduser'");
if($query){
    echo "1";
} else {
    echo "0";
}