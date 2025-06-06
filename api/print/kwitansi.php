<?php
session_start();
include_once "../config/connection.php";
$id = $_GET['id'];
$bulan = array(01=>"Januari",02=>"Februari",03=>"Maret",04=>"April",05=>"Mei",06=>"Juni",07=>"Juli",08=>"Agustus",09=>"September",10=>"Oktober",11=>"November",12=>"Desember");
$periode = "Periode : ".$bulan[date("m")]." ".date("Y");
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
            <tr>
                <td width="30%" class="middle title-company" align="center" style="border: 2px solid #000; height: 50px;">
                    <h1>CV. Solusi Pemanas Air Nusantara</h1>
                </td>
                <td width="40%" align="center" class="title-kwitansi">
                    <h2 class="underline">KWITANSI</h2>
                    <h2>RECEIPT</h2>
                </td>
                <td width="30%" class="bottom">
                    <div class="info-container">
                        <label class="n-info">
                            <p><span class="n-divider">No</span><span><em><strong>Number</strong></em></span></p>
                        </label>
                        <label class="titik-dua">:</label>
                        <label class="v-info">PJ20170200001</label>
                    </div>
                    <div class="info-container">
                        <label class="n-info">
                            <p><span class="n-divider">Tanggal</span><span><em><strong>Date</strong></em></span></p>
                        </label>
                        <label class="titik-dua">:</label>
                        <label class="v-info">22/02/2017</label>
                    </div>
                </td>
            </tr>
        </table>
        <div class="data-container-kwitansi">
            <table>
                <tr>
                    <td style="width: 120px !important;"><p><span class="n-divider">Sudah terima dari</span><span><em><strong>Received From</strong></em></span></p></td>
                    <td class="nopadding" style="width: 10px;">:</td>
                    <td>asdf</td>
                </tr>
                <tr>
                    <td class="nopaddingtop" style="width: 120px !important;"><p><span class="n-divider">Banyaknya</span><span><em><strong>Amount</strong></em></span></p></td>
                    <td class="nopadding" style="width: 10px;">:</td>
                    <td class="nopaddingtop">asdf</td>
                </tr>
            </table>
            <div class="divider-black"></div>
            <table>
                <tr class="bigheight">
                    <td style="width: 120px !important;"><p><span class="n-divider">Untuk Pembayaran</span><span><em><strong>For Payment</strong></em></span></p></td>
                    <td class="nopadding" style="width: 10px;">:</td>
                    <td>asdf</td>
                </tr>
            </table>
            <div class="divider-black"></div>
            <table>
                <tr>
                    <td width="50%">
                        <div class="side-price">
                            <div class="side-price2">
                                <h4>$/Rp. 100,000,000.00</h4>
                            </div>
                        </div>
                        <div class="pilihan-container">
                            <span class="sub-pil">
                                <span class="border-check"></span> CASH
                            </span>
                            <span class="sub-pil">
                                <span class="border-check"></span> CHEQUE
                            </span>
                            <span class="sub-pil">
                                <span class="border-check"></span> BILYET GIRO
                            </span>
                        </div>
                        <div class="info-container2">
                            <label class="n-info">
                                <p><span class="n-divider">BANK</span><span><em><strong>Bank</strong></em></span></p>
                            </label>
                            <label class="titik-dua">:</label>
                            <label class="v-info">PJ20170200001</label>
                        </div>
                        <div class="info-container2">
                            <label class="n-info">
                                <p><span class="n-divider">Nomor</span><span><em><strong>Number</strong></em></span></p>
                            </label>
                            <label class="titik-dua">:</label>
                            <label class="v-info">PJ20170200001</label>
                        </div>
                        <div class="info-container2">
                            <label class="n-info">
                                <p><span class="n-divider">Tanggal</span><span><em><strong>Date</strong></em></span></p>
                            </label>
                            <label class="titik-dua">:</label>
                            <label class="v-info">22/02/2017</label>
                        </div>
                    </td>
                    <td width="45%" style="padding-left: 5%;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Denpasar, 19 September 2016<br /><br /><br /><br /><br /><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
                    </td>
                </tr>
            </table>
            <div class="divider-black" style="margin-bottom: 0 !important;"></div>
            <table>
                <tr>
                    <td colspan="2" align="center" style="padding: 5px !important; border-bottom: 1px solid #000;"><strong>Kwitansi ini akan dianggap Sah, Setelah pembayaran dengan Bilyet Giro/Cheque tsb. dapat diuangkan.</strong></td>
                </tr>
                <tr>
                    <td colspan="2" align="center" style="padding: 5px !important;"><em>This receipt will be cleared after Bilyet/Cheque can be cleared.</em></td>
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