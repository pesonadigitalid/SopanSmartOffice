<?php
session_start();
include_once "../config/connection.php";
$bulan = array("01"=>"Januari","02"=>"Februari","03"=>"Maret","04"=>"April","05"=>"Mei","06"=>"Juni","07"=>"Juli","08"=>"Agustus","09"=>"September",10=>"Oktober","11"=>"November",12=>"Desember");

$b = antiSQLInjection($_GET['bulan']);
$t = antiSQLInjection($_GET['tahun']);
$b2 = intval($b)-1;
if($b2<0){
    $t2 = $t-1;
    $b2 = 12;
} else {
    $t2 = $t;
}

if($b2<10){
    $b2 = "0".$b2;
}

$totalGajiMMS = $db->get_var("SELECT SUM(TotalGaji) FROM tb_slip_gaji WHERE GajiBulan='".intval($b)."' AND GajiTahun='$t'");
if(!$totalGajiMMS) $totalGajiMMS=0;
$totalGajiAwalMMS = $db->get_var("SELECT SUM(TotalPendapatan) FROM tb_slip_gaji WHERE GajiBulan='".intval($b)."' AND GajiTahun='$t'");
if(!$totalGajiAwalMMS) $totalGajiAwalMMS=0;
$totalPotonganMMS = $db->get_var("SELECT SELECT SUM(PotonganCutiMinus)+SUM(PotonganAlpha)+SUM(PotonganLainLain) FROM tb_slip_gaji WHERE GajiBulan='".intval($b)."' AND GajiTahun='$t'");
if(!$totalPotonganMMS) $totalPotonganMMS=0;
$totalPengembalianMMS = $db->get_var("SELECT SELECT SUM(PotonganHutang)+SUM(PotonganPinjaman)+SUM(PotonganKasbon) FROM tb_slip_gaji WHERE GajiBulan='".intval($b)."' AND GajiTahun='$t'");
if(!$totalPengembalianMMS) $totalPengembalianMMS=0;

$totalSetelahPotongan = $totalGajiAwalMMS-$totalPotonganMMS;
$totalGajiFinal = $totalSetelahPotongan-$totalPengembalianMMS;



$totalGajiLD = $db->get_var("SELECT SUM(TotalGaji) FROM tb_slip_gaji WHERE GajiBulan='".intval($b)."' AND GajiTahun='$t' AND IDKaryawan IN (SELECT IDKaryawan FROM tb_karyawan WHERE IDDepartement<>'4')");
if(!$totalGajiLD) $totalGajiLD=0;
$totalGajiAwalLD = $db->get_var("SELECT SUM(TotalPendapatan) FROM tb_slip_gaji WHERE GajiBulan='".intval($b)."' AND GajiTahun='$t' AND IDKaryawan IN (SELECT IDKaryawan FROM tb_karyawan WHERE IDDepartement<>'4')");
if(!$totalGajiAwalLD) $totalGajiAwalLD=0;
$totalPotonganLD = $db->get_var("SELECT SELECT SUM(PotonganCutiMinus)+SUM(PotonganAlpha)+SUM(PotonganLainLain) FROM tb_slip_gaji WHERE GajiBulan='".intval($b)."' AND GajiTahun='$t' AND IDKaryawan IN (SELECT IDKaryawan FROM tb_karyawan WHERE IDDepartement<>'4')");
if(!$totalPotonganLD) $totalPotonganLD=0;
$totalPengembalianLD = $db->get_var("SELECT SELECT SUM(PotonganHutang)+SUM(PotonganPinjaman)+SUM(PotonganKasbon) FROM tb_slip_gaji WHERE GajiBulan='".intval($b)."' AND GajiTahun='$t' AND IDKaryawan IN (SELECT IDKaryawan FROM tb_karyawan WHERE IDDepartement<>'4')");
if(!$totalPengembalianLD) $totalPengembalianLD=0;

$totalLDSetelahPotongan = $totalGajiAwalLD-$totalPotonganLD;
$totalLDGajiFinal = $totalLDSetelahPotongan-$totalPengembalianLD;
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <meta name="description" content=""/>
        <meta name="author" content="Yogi Pratama - Pesona Creative - 085737654543"/>
        
        <title>SOPAN Smart Office - Smart office for smart people</title>
        
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport"/>
        <link rel="stylesheet" href="print-style.css" media="all" type="text/css"/>
    </head>
    <body>
        <table>
            <tr style="padding-bottom: 100px !important;">
                <td width="80%" class="bottom" style="line-height: 1.5em">
                    <img src="print-logo.png" align="left" style="padding-right: 10px;margin-right: 20px;border-right: solid 2px #02b1e1;">
                    Jl. Tukad Citarum Blok I No. 7B Renon, Perum Surya Graha Asih <br />Denpasar Bali<br />
                    Phone : +62 361 - 238055<br />
                    Fax : +62 361 - 238055<br />
                    Email : mail@lintasdaya.com
                </td>
                <td width="20%" align="right" style="padding-top:20px">
                    <img src="print-logo2.png">
                </td>
            </tr>
        </table>
        <table style="margin-top:30px;">
            <tr>
                <td colspan="2"><h3 class="title-print" style="text-transform: uppercase;">REKAP GAJI MMS <?php echo $bulan[$b]." ".$t; ?></h3></td>
            </tr>
        </table>
        <div class="container-kwitansi">
            <h1>KWITANSI PEMBAYARAN</h1>
            <table class="tabel-desc">
                <tr>
                    <td style="width: 180px;">Telah Diterima Dari</td>
                    <td>:</td>
                    <td><strong>Solusi Pemanas Air Nusantara</strong></td>
                </tr>
                <tr>
                    <td style="width: 180px;">Uang Sejumlah</td>
                    <td>:</td>
                    <td><strong><?php echo number_format($totalGajiMMS); ?></strong></td>
                </tr>
                <tr>
                    <td style="width: 180px;">Untuk Pembayaran</td>
                    <td>:</td>
                    <td><strong style="text-transform: uppercase">GAJI BULAN <?php echo $bulan[$b]." ".$t; ?></strong><br />Periode : 26 <?php echo $bulan[$b2]." ".$t2; ?> s/d 25 <?php echo $bulan[$b]." ".$t; ?></td>
                </tr>
            </table>
            <table style="margin-top: 20px;">
                <tr>
                    <td>
                        <div class="total-kwitansi">
                            Rp. <?php echo number_format($totalGajiMMS); ?>
                        </div>
                    </td>
                    <td class="center">Denpasar, <?php echo date("d")." ".$bulan[date("m")]." ".date("Y"); ?><br /><br /><br /><br /><strong><ins>Ir. Lukito Pramono</ins></strong></td>
                </tr>
            </table>
        </div>
        <div class="summary-gaji-container">
            <h1>SUMMARY<br />GAJI BULAN <?php echo $bulan[$b]." ".$t; ?></h1>
            <table class="tabel-summary">
                <tr>
                    <td>1. PERINCIAN GAJI</td>
                    <td class="right" style="width: 170px;"><?php echo number_format($totalGajiAwalMMS); ?></td>
                </tr>
                <tr>
                    <td></td>
                    <td style="border-top: 1px dashed #000; width: 170px;" class="right"><strong><?php echo number_format($totalGajiAwalMMS); ?></strong></td>
                </tr>
                <tr>
                    <td class="jarak-atas">2. POTONGAN GAJI</td>
                    <td class="right" style="width: 170px;"><?php echo number_format($totalPotonganMMS); ?></td>
                </tr>
                <tr>
                    <td><strong>3. TOTAL 1 - 2</strong></td>
                    <td class="right box" style="width: 170px;"><strong><?php echo number_format($totalSetelahPotongan); ?></strong></td>
                </tr>
                <tr>
                    <td class="jarak-atas">4. PENGEMBALIAN PINJAMAN / HUTANG DARI PETTY CASH</td>
                    <td class="right" style="width: 170px;"><?php echo number_format($totalPengembalianMMS); ?></td>
                </tr>
                <tr>
                    <td><strong>5. TOTAL 3 - 4</strong></td>
                    <td style="border-top: 1px dashed #000; width: 170px;" class="right"><strong><?php echo number_format($totalGajiFinal); ?></strong></td>
                </tr>
            </table>
        </div>
        <table style="margin-top: 40px;">
            <tr>
                <td style="width: 450px !important;"></td>
                <td class="center">Denpasar, <?php echo date("d")." ".$bulan[date("m")]." ".date("Y"); ?><br /><br /><br /><br /><br /><br />(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
            </tr>
        </table>

        <table class="newPage">
            <tr style="padding-bottom: 100px !important;">
                <td width="80%" class="bottom" style="line-height: 1.5em">
                    <img src="print-logo.png" align="left" style="padding-right: 10px;margin-right: 20px;border-right: solid 2px #02b1e1;">
                    Jl. Tukad Citarum Blok I No. 7B Renon, Perum Surya Graha Asih <br />Denpasar Bali<br />
                    Phone : +62 361 - 238055<br />
                    Fax : +62 361 - 238055<br />
                    Email : mail@lintasdaya.com
                </td>
                <td width="20%" align="right" style="padding-top:20px">
                    <img src="print-logo2.png">
                </td>
            </tr>
        </table>
        <table style="margin-top:30px;">
            <tr>
                <td colspan="2"><h3 class="title-print" style="text-transform: uppercase;">REKAP GAJI LINTAS DAYA <?php echo $bulan[$b]." ".$t; ?></h3></td>
            </tr>
        </table>
        <div class="container-kwitansi">
            <h1>KWITANSI PEMBAYARAN</h1>
            <table class="tabel-desc">
                <tr>
                    <td style="width: 180px;">Telah Diterima Dari</td>
                    <td>:</td>
                    <td><strong>LINTAS DAYA</strong></td>
                </tr>
                <tr>
                    <td style="width: 180px;">Uang Sejumlah</td>
                    <td>:</td>
                    <td><strong><?php echo number_format($totalGajiLD); ?></strong></td>
                </tr>
                <tr>
                    <td style="width: 180px;">Untuk Pembayaran</td>
                    <td>:</td>
                    <td><strong style="text-transform: uppercase">GAJI BULAN <?php echo $bulan[$b]." ".$t; ?></strong><br />Periode : 26 <?php echo $bulan[$b2]." ".$t2; ?> s/d 25 <?php echo $bulan[$b]." ".$t; ?></td>
                </tr>
            </table>
            <table style="margin-top: 20px;">
                <tr>
                    <td>
                        <div class="total-kwitansi">
                            Rp. <?php echo number_format($totalGajiLD); ?>
                        </div>
                    </td>
                    <td class="center">Denpasar, <?php echo date("d")." ".$bulan[date("m")]." ".date("Y"); ?><br /><br /><br /><br /><strong><ins>Ir. Lukito Pramono</ins></strong></td>
                </tr>
            </table>
        </div>
        <div class="summary-gaji-container">
            <h1>SUMMARY<br />GAJI BULAN <?php echo $bulan[$b]." ".$t; ?></h1>
            <table class="tabel-summary">
                <tr>
                    <td>1. PERINCIAN GAJI</td>
                    <td class="right" style="width: 170px;"><?php echo number_format($totalGajiAwalLD); ?></td>
                </tr>
                <tr>
                    <td></td>
                    <td style="border-top: 1px dashed #000; width: 170px;" class="right"><strong><?php echo number_format($totalGajiAwalLD); ?></strong></td>
                </tr>
                <tr>
                    <td class="jarak-atas">2. POTONGAN GAJI</td>
                    <td class="right" style="width: 170px;"><?php echo number_format($totalPotonganLD); ?></td>
                </tr>
                <tr>
                    <td><strong>3. TOTAL 1 - 2</strong></td>
                    <td class="right box" style="width: 170px;"><strong><?php echo number_format($totalLDSetelahPotongan); ?></strong></td>
                </tr>
                <tr>
                    <td class="jarak-atas">4. PENGEMBALIAN PINJAMAN / HUTANG DARI PETTY CASH</td>
                    <td class="right" style="width: 170px;"><?php echo number_format($totalPengembalianLD); ?></td>
                </tr>
                <tr>
                    <td><strong>5. TOTAL 3 - 4</strong></td>
                    <td style="border-top: 1px dashed #000; width: 170px;" class="right"><strong><?php echo number_format($totalLDGajiFinal); ?></strong></td>
                </tr>
            </table>
        </div>
        <table style="margin-top: 40px;">
            <tr>
                <td style="width: 450px !important;"></td>
                <td class="center">Denpasar, <?php echo date("d")." ".$bulan[date("m")]." ".date("Y"); ?><br /><br /><br /><br /><br /><br />(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
            </tr>
        </table>
        <script type="text/javascript">
            window.onload = function () { window.print(); }
        </script>
    </body>
</html>
</body>
</html>