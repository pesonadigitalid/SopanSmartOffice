<?php
include_once "../config/connection.php";

$idkaryawan = antiSQLInjection($_POST['idkaryawan']);
$bulan = antiSQLInjection($_POST['bulan']);
$tahun = antiSQLInjection($_POST['tahun']);
$total_absen = antiSQLInjection($_POST['total_absen']);
$gaji_pokok = antiSQLInjection($_POST['gaji_pokok']);
$total_uang_makan = antiSQLInjection($_POST['total_uang_makan']);
$total_uang_transport = antiSQLInjection($_POST['total_uang_transport']);
$uang_pulsa = antiSQLInjection($_POST['uang_pulsa']);
$tunjangan_performance = antiSQLInjection($_POST['tunjangan_performance']);
$tunjangan_khusus = antiSQLInjection($_POST['tunjangan_khusus']);
$tunjangan_luar_kota = antiSQLInjection($_POST['tunjangan_luar_kota']);
$potongan_cuti = antiSQLInjection($_POST['potongan_cuti']);
$potongan_pinjaman = antiSQLInjection($_POST['potongan_pinjaman']);
$potongan_kasbon = antiSQLInjection($_POST['potongan_kasbon']);
$potongan_lain = antiSQLInjection($_POST['potongan_lain']);
$total_gaji = antiSQLInjection($_POST['total_gaji']);
$total_cuti_minus = antiSQLInjection($_POST['total_cuti_minus']);
$total1 = antiSQLInjection($_POST['total1']);
$total_potongan = antiSQLInjection($_POST['total_potongan']);
$uang_makan_perhari = antiSQLInjection($_POST['uang_makan_perhari']);
$uang_transport_perhari = antiSQLInjection($_POST['uang_transport_perhari']);
$keterangan = antiSQLInjection($_POST['keterangan']);
$uang_lembur = antiSQLInjection($_POST['uang_lembur']);
$uang_makan_lembur = antiSQLInjection($_POST['uang_makan_lembur']);
$total_alpha = antiSQLInjection($_POST['total_alpha']);
$potongan_alpha = antiSQLInjection($_POST['potongan_alpha']);
$potongan_jamsostek = antiSQLInjection($_POST['potongan_jamsostek']);
$tanggal = date("Y-m-d");

$datakaryawan = $db->get_row("SELECT * FROM tb_karyawan WHERE IDKaryawan='$idkaryawan' ORDER BY IDKaryawan");
$jabatan = $db->get_var("SELECT Jabatan FROM tb_jabatan WHERE IDJabatan='" . $datakaryawan->IDJabatan . "'");

$dataLast = $db->get_row("SELECT * FROM tb_slip_gaji WHERE GajiTahun='$tahun' AND Harian='0' ORDER BY NoSlip DESC");
if ($dataLast) {
    $last = substr($dataLast->NoSlip, -3);
    $last++;
    if ($last < 100 and $last >= 10)
        $last = "0" . $last;
    else if ($last < 10)
        $last = "00" . $last;
    $noslip = "PAY/LD/" . $tahun . "/" . $last;
} else {
    $noslip = "PAY/LD/" . $tahun . "/" . "001";
}

$query = $db->query("INSERT INTO tb_slip_gaji SET NoSlip='$noslip', IDKaryawan='$idkaryawan', NIK='" . $datakaryawan->NIK . "', NamaKaryawan='" . $datakaryawan->Nama . "', GajiBulan='$bulan', GajiTahun='$tahun', Jabatan='$jabatan', Alamat='" . $datakaryawan->AlamatSementara . "', Telp='" . $datakaryawan->NoTelp . "', GajiPokok='$gaji_pokok', UangMakanHarian='$uang_makan_perhari', UangTransportHarian='$uang_transport_perhari', TotalAbsen='$total_absen', UangMakan='$total_uang_makan', UangTransport='$total_uang_transport', UangPulsa='$uang_pulsa', UangTunjanganPerformance='$tunjangan_performance', UangTunjanganKhusus='$tunjangan_khusus', UangTunjanganLuarKota='$tunjangan_luar_kota', CutiMinus='$total_cuti_minus', PotonganPinjaman='$potongan_pinjaman', PotonganCutiMinus='$potongan_cuti', PotonganKasbon='$potongan_kasbon', PotonganLainLain='$potongan_lain', TotalPendapatan='$total1', TotalPotongan='$total_potongan', TotalGaji='$total_gaji', Keterangan='$keterangan', TotalAlpha='$total_alpha', PotonganAlpha='$potongan_alpha', UangLembur='$uang_lembur', UangMakanLembur='$uang_makan_lembur', DibuatOleh='" . $_SESSION["uid"] . "', CreatedBy='" . $_SESSION["uid"] . "', Tanggal='$tanggal', PotonganJamsostek='$potongan_jamsostek'");
if ($query) {
    echo "1";
} else {
    echo "0";
}
