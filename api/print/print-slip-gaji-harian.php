<?php
session_start();
include_once "../config/connection.php";
include_once "../library/class.absencalculation.php";
$id = $_GET['id'];

$bulan = array("1" => "Januari", "2" => "Februari", "3" => "Maret", "4" => "April", "5" => "Mei", "6" => "Juni", "7" => "Juli", "8" => "Agustus", "9" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember");

$dataSlip = $db->get_row("SELECT *, DATE_FORMAT(DateCreated,'%d/%m/%Y') AS TanggalID FROM tb_slip_gaji WHERE IDSlipGaji='$id'");
$karyawan = $db->get_row("SELECT * FROM tb_karyawan WHERE IDKaryawan='" . $dataSlip->IDKaryawan . "'");
$jabatan = $db->get_var("SELECT Jabatan FROM tb_jabatan WHERE IDJabatan='" . $karyawan->IDJabatan . "'");
$created = $db->get_row("SELECT * FROM tb_karyawan WHERE IDKaryawan='" . $dataSlip->CreatedBy . "'");

function terbilang($angka)
{
    $angka = (float)$angka;
    $bilangan = array('', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas');
    if ($angka < 12) {
        return $bilangan[$angka];
    } else if ($angka < 20) {
        return $bilangan[$angka - 10] . ' Belas';
    } else if ($angka < 100) {
        $hasil_bagi = (int)($angka / 10);
        $hasil_mod = $angka % 10;
        return trim(sprintf('%s Puluh %s', $bilangan[$hasil_bagi], $bilangan[$hasil_mod]));
    } else if ($angka < 200) {
        return sprintf('Seratus %s', terbilang($angka - 100));
    } else if ($angka < 1000) {
        $hasil_bagi = (int)($angka / 100);
        $hasil_mod = $angka % 100;
        return trim(sprintf('%s Ratus %s', $bilangan[$hasil_bagi], terbilang($hasil_mod)));
    } else if ($angka < 2000) {
        return trim(sprintf('Seribu %s', terbilang($angka - 1000)));
    } else if ($angka < 1000000) {
        $hasil_bagi = (int)($angka / 1000);
        $hasil_mod = $angka % 1000;
        return sprintf('%s Ribu %s', terbilang($hasil_bagi), terbilang($hasil_mod));
    } else if ($angka < 1000000000) {
        $hasil_bagi = (int)($angka / 1000000);
        $hasil_mod = $angka % 1000000;
        return trim(sprintf('%s Juta %s', terbilang($hasil_bagi), terbilang($hasil_mod)));
    } else if ($angka < 1000000000000) {
        $hasil_bagi = (int)($angka / 1000000000);
        $hasil_mod = fmod($angka, 1000000000);
        return trim(sprintf('%s Milyar %s', terbilang($hasil_bagi), terbilang($hasil_mod)));
    } else if ($angka < 1000000000000000) {
        $hasil_bagi = $angka / 1000000000000;
        $hasil_mod = fmod($angka, 1000000000000);
        return trim(sprintf('%s Triliun %s', terbilang($hasil_bagi), terbilang($hasil_mod)));
    } else {
        return 'Data Salah';
    }
}

$start = strtotime($dataSlip->PeriodeStart);
$end = strtotime($dataSlip->PeriodeEnd);
$datediff = $end - $start;
$totalHariKerjaBulan =  round($datediff / (60 * 60 * 24)) + 1;
$totalHariKerja = $dataSlip->TotalAbsen;
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="author" content="Yogi Pratama - Pesona Creative - 085737654543" />

    <title>SOPAN Smart Office - Integrated System</title>

    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />
    <link rel="stylesheet" href="print-style.css" media="all" type="text/css" />
    <style type="text/css" media="all">
        body,
        * {
            font-size: 12px !important;
        }
    </style>
</head>

<body>
    <table>
        <tr>
            <td width="30%" class="bottom">
                <h1>CV. Solusi Pemanas Air Nusantara</h1>
                Jl. Tukad Citarum Blok I No. 7B Renon<br />Perum Surya Graha Asih, Denpasar Bali<br />
            </td>
            <td width="40%" class="center">
                <h1 class="underline" style="font-size: 16px !important">** SLIP GAJI HARIAN **</h1>
                Periode : <?php echo $fungsi->IDDate($dataSlip->PeriodeStart) . " - " . $fungsi->IDDate($dataSlip->PeriodeEnd); ?>
            </td>
            <td width="30%">
                Nomor : <?php echo $dataSlip->NoSlip; ?><br />
                Tanggal : <?php echo $dataSlip->TanggalID; ?><br />
                Dicetak Oleh : <?php echo $created->Nama; ?><br />
            </td>
        </tr>
    </table>
    <table class="tabelList3" cellpadding="0" cellspacing="0" style="margin-top:10px;margin-bottom:10px">
        <tr>
            <td width="50%">
                <label>NIK </label>: <?php echo $karyawan->NIK; ?><br />
                <label>Nama Karyawan </label>: <?php echo $karyawan->Nama; ?><br />
                <label>Hari Kerja Periode </label>: <?php echo $totalHariKerjaBulan; ?> Hari
            </td>
            <td width="50%">
                <label>Jabatan </label>: <?php echo $jabatan; ?><br />
                <label>No Telp </label>: <?php echo $karyawan->NoTelp; ?><br />
                <label>Hari Kerja Karyawan </label>: <?php echo $dataSlip->TotalAbsen; ?> Hari
            </td>
        </tr>
    </table>

    <table class="tabelList4" cellpadding="0" cellspacing="0" style="margin-top:10px;margin-bottom:10px">
        <tr>
            <td width="50%">
                <strong>PENDAPATAN</strong><br />
                <?php if ($karyawan->StatusLainnya == "Proyek") { ?>
                    <label>1. Gaji Pokok </label>: &nbsp;&nbsp;Rp. <?php echo number_format($dataSlip->GajiPokok, 0); ?><br />
                    <label></label>: &nbsp;&nbsp;(Rp. <?php echo number_format(($dataSlip->GajiPokokHarian), 0); ?> x <?php echo $dataSlip->TotalAbsenGapok; ?> Hari)<br />
                    <label>2. Uang Makan </label>: &nbsp;&nbsp;Rp. <?php echo number_format($dataSlip->UangMakan, 0); ?><br />
                    <label>3. Gaji Lembur </label>: &nbsp;&nbsp;Rp. <?php echo number_format($dataSlip->UangLembur, 0); ?><br />
                    <label></label>: &nbsp;&nbsp;(Rp. <?php echo number_format(($dataSlip->UangLemburPerJam), 0); ?> x <?php echo $dataSlip->TotalJamLembur; ?> Jam)<br />
                    <label>4. Tunjangan Khusus </label>: &nbsp;&nbsp;Rp. <?php echo number_format($dataSlip->UangTunjanganKhusus, 0); ?><br />
                <?php } else { ?>
                    <label>1. Gaji Pokok </label>: &nbsp;&nbsp;Rp. <?php echo number_format($dataSlip->GajiPokok, 0); ?><br />
                    <label></label>: &nbsp;&nbsp;(Rp. <?php echo number_format(($dataSlip->GajiPokokHarian), 0); ?> x <?php echo $dataSlip->TotalAbsenGapok; ?> Hari)<br />
                    <label>2. Uang Makan </label>: &nbsp;&nbsp;Rp. <?php echo number_format($dataSlip->UangMakan, 0); ?><br />
                    <label>3. Uang Hadir </label>: &nbsp;&nbsp;Rp. <?php echo number_format($dataSlip->UangHadir, 0); ?><br />
                    <label>4. Gaji Lembur </label>: &nbsp;&nbsp;Rp. <?php echo number_format($dataSlip->UangLembur, 0); ?><br />
                    <label></label>: &nbsp;&nbsp;(Rp. <?php echo number_format(($dataSlip->UangLemburPerJam), 0); ?> x <?php echo $dataSlip->TotalJamLembur; ?> Jam)<br />
                    <label>5. Uang Makan Lembur </label>: &nbsp;&nbsp;Rp. <?php echo number_format($dataSlip->UangMakanLembur, 0); ?><br />
                    <label>6. Tunjangan Khusus </label>: &nbsp;&nbsp;Rp. <?php echo number_format($dataSlip->UangTunjanganKhusus, 0); ?><br />
                <?php } ?>
                <label><strong style="text-decoration: underline;">TOTAL PENDAPATAN</strong> </label>: &nbsp;&nbsp;<strong>Rp. <?php echo number_format($dataSlip->TotalPendapatan, 0); ?></strong>
            </td>
            <td width="50%">
                <strong>POTONGAN</strong><br />
                <!-- <label>1. Potongan Pinjaman </label>: &nbsp;&nbsp;Rp. <?php echo number_format($dataSlip->PotonganPinjaman, 0); ?><br />
                <label>2. Potongan Kasbon </label>: &nbsp;&nbsp;Rp. <?php echo number_format($dataSlip->PotonganKasbon, 0); ?><br />
                <label>3. Potongan Jamsostek </label>: &nbsp;&nbsp;Rp. <?php echo number_format($dataSlip->PotonganJamsostek, 0); ?><br /> -->
                <label>1. Potongan Lain-lain </label>: &nbsp;&nbsp;Rp. <?php echo number_format($dataSlip->PotonganLainLain, 0); ?><br />
                <label><strong style="text-decoration: underline;">TOTAL POTONGAN</strong> </label>: &nbsp;&nbsp;<strong>Rp. <?php echo number_format($dataSlip->TotalPotongan, 0); ?></strong><br />
            </td>
        </tr>
        <tr>
            <td>
                <label><strong style="text-decoration: underline;">TOTAL GAJI</strong></label><br />&nbsp;&nbsp;<strong style="font-size: 14px !important;">Rp. <?php echo number_format($dataSlip->TotalGaji, 0); ?></strong><br />
                <span style="font-style: italic;">(<?php echo terbilang($dataSlip->TotalGaji); ?> Rupiah)</span><br />

                <label><strong style="text-decoration: underline;">KETERANGAN</strong></label><br />
                <?php if ($dataSlip->Keterangan != "") echo $dataSlip->Keterangan;
                else echo "-"; ?>
            </td>
            <td class="center" style="vertical-align: middle;">Mengetahui,<br /><br /><br /><br />( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )
            </td>
        </tr>
    </table>
    <script type="text/javascript">
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>
</body>

</html>