<?php
session_start();
include_once "../config/connection.php";

$id = $_GET['id'];
$bulan = array("01"=>"Januari","02"=>"Februari","03"=>"Maret","04"=>"April","05"=>"Mei","06"=>"Juni","07"=>"Juli","08"=>"Agustus","09"=>"September","10"=>"Oktober","11"=>"November","12"=>"Desember");
$periode = "Periode : ".$bulan[date("m")]." ".date("Y");

$invoice = $db->get_row("SELECT * FROM tb_kas_keluar WHERE IDKasKeluar='$id'");
    
$exp = explode("-",$invoice->Tanggal);
$tanggalID = $exp[2]." ".$bulan[$exp[1]]." ".$exp[0];

function terbilang ($angka) {
    $angka = (float)$angka;
    $bilangan = array('','Satu','Dua','Tiga','Empat','Lima','Enam','Tujuh','Delapan','Sembilan','Sepuluh','Sebelas');
    if ($angka < 12) {
        return $bilangan[$angka];
    } else if ($angka < 20) {
        return $bilangan[$angka - 10] . ' Belas';
    } else if ($angka < 100) {
        $hasil_bagi = (int)($angka / 10);
        $hasil_mod = $angka % 10;
        return trim(sprintf('%s Puluh %s', $bilangan[$hasil_bagi], $bilangan[$hasil_mod]));
    } else if ($angka < 200) { return sprintf('Seratus %s', terbilang($angka - 100));
    } else if ($angka < 1000) { $hasil_bagi = (int)($angka / 100); $hasil_mod = $angka % 100; return trim(sprintf('%s Ratus %s', $bilangan[$hasil_bagi], terbilang($hasil_mod)));
    } else if ($angka < 2000) { return trim(sprintf('Seribu %s', terbilang($angka - 1000)));
    } else if ($angka < 1000000) { $hasil_bagi = (int)($angka / 1000); $hasil_mod = $angka % 1000; return sprintf('%s Ribu %s', terbilang($hasil_bagi), terbilang($hasil_mod));
    } else if ($angka < 1000000000) { $hasil_bagi = (int)($angka / 1000000); $hasil_mod = $angka % 1000000; return trim(sprintf('%s Juta %s', terbilang($hasil_bagi), terbilang($hasil_mod)));
    } else if ($angka < 1000000000000) { $hasil_bagi = (int)($angka / 1000000000); $hasil_mod = fmod($angka, 1000000000); return trim(sprintf('%s Milyar %s', terbilang($hasil_bagi), terbilang($hasil_mod)));
    } else if ($angka < 1000000000000000) { $hasil_bagi = $angka / 1000000000000; $hasil_mod = fmod($angka, 1000000000000); return trim(sprintf('%s Triliun %s', terbilang($hasil_bagi), terbilang($hasil_mod)));
    } else {
        return 'Data Salah';
    }
}
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
        <link rel="stylesheet" href="print-style2.css" media="all"/>
    </head>
    <body>
        <table>
            <tr>
                <td width="30%" class="middle title-company" align="center" style="border: 2px solid #000; height: 50px;">
                    <h1 style="line-height: 25px;">CV. Solusi Pemanas Air Nusantara</h1>
                </td>
                <td width="40%" align="center" class="title-kwitansi">
                    <h2 style="font-size: 20px !important;letter-spacing: 0px;margin-top: 40px;">BUKTI KAS KELUAR</h2>
                </td>
                <td width="30%" class="bottom">
                    <div class="info-container2">
                        <label class="n-info">
                            <p><span class="n-divider">No</span><span><em><strong>Number</strong></em></span></p>
                        </label>
                        <label class="titik-dua">:</label>
                        <label class="v-info"><?php echo $invoice->NoBukti; ?></label>
                    </div>
                    <div class="info-container2">
                        <label class="n-info">
                            <p><span class="n-divider">Tanggal</span><span><em><strong>Date</strong></em></span></p>
                        </label>
                        <label class="titik-dua">:</label>
                        <label class="v-info"><?php echo $tanggalID; ?></label>
                    </div>
                </td>
            </tr>
        </table>
        <div class="data-container-kwitansi">
            <table>
                <tr>
                    <td style="width: 120px !important;"><p><span class="n-divider">Dibayarkan kepada:</span><span><em><strong>Paid to</strong></em></span></p></td>
                    <td class="nopadding" style="width: 10px;">:</td>
                    <td><strong><?php echo $invoice->ContactPerson; ?></strong></td>
                </tr>
                <tr>
                    <td class="nopaddingtop" style="width: 120px !important;"><p><span class="n-divider">Banyaknya</span><span><em><strong>Amount</strong></em></span></p></td>
                    <td class="nopadding" style="width: 10px;">:</td>
                    <td class="nopaddingtop"><?php echo terbilang($invoice->Jumlah); ?> Rupiah</td>
                </tr>
            </table>
            <div class="divider-black"></div>
            <table>
                <tr class="bigheight">
                    <td style="width: 120px !important;"><p><span class="n-divider">Untuk Pembayaran</span><span><em><strong>For Payment</strong></em></span></p></td>
                    <td class="nopadding" style="width: 10px;">:</td>
                    <td><?php echo $invoice->Keterangan; ?></td>
                </tr>
            </table>
            <div class="divider-black"></div>
            <table>
                <tr>
                    <td width="50%">
                        <div class="side-price">
                            <div class="side-price2">
                                <h4>Rp. <?php echo number_format($invoice->Jumlah,2); ?></h4>
                            </div>
                        </div>
                    </td>
                    <td width="30%" style="padding-left: 10%;padding-right:10%;text-align: center;">
                        Denpasar, <?php echo $tanggalID; ?>
                        <br /><br /><br /><br /><br /><br />(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)<br /><br /><br /><br /></td>
                    </td>
                </tr>
            </table>
        </div>
        <script type="text/javascript">
            window.onload = function () { window.print(); }
        </script>
    </body>
</html>
</body>
</html>