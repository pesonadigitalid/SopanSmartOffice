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
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <meta name="description" content=""/>
        <meta name="author" content="Yogi Pratama - Pesona Creative - 085737654543"/>
        
        <title>MMS - Smart Office</title>
        
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
                <td colspan="2"><h3 class="title-print" style="text-transform: uppercase;font-size: 14px !important;margin-bottom: 20px;">GAJI KARYAWAN <?php echo $bulan[$b]." ".$t; ?></h3></td>
            </tr>
        </table>
        <table class="tabelList6 border-solid" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th style="width:20px">NO</th>
                    <th>NAMA</th>
                    <th style="width:100px">NO REKENING</th>
                    <th style="width:100px">NOMINAL</th>
                    <th style="width:100px">NO. SLIP</th>
                </tr>
            </thead>
            <tbody>
                <?php
                //LD
                $i = 0;
                $query = $db->get_results("SELECT * FROM tb_karyawan WHERE IDDepartement<>'4' AND IDKaryawan>1 ORDER BY Nama ASC");
                if($query){
                    foreach($query as $data){
                        $i++;
                        $gaji = $db->get_row("SELECT * FROM tb_slip_gaji WHERE GajiBulan='".intval($b)."' AND GajiTahun='$t' AND IDKaryawan='".$data->IDKaryawan."'");
                        ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo $data->Nama; ?></td>
                            <td style="text-align: center;"><?php echo $data->NoRekening1; ?></td>
                            <td style="text-align: right;"><?php echo number_format($gaji->TotalGaji); ?></td>
                            <td style="text-align: center;"><?php echo $gaji->NoSlip; ?></td>
                        </tr>
                        <?php

                        if (!($i % 5)) {
                            ?>
                            <tr><td colspan="5" style="height: 15px;"></td></tr>
                            <?php
                        }
                    }
                }
                ?>
                <tr><td colspan="5" style="height: 15px;"></td></tr>
                <?php
                //MMS
                $i = 0;
                $query = $db->get_results("SELECT * FROM tb_karyawan ORDER BY Nama ASC");
                if($query){
                    foreach($query as $data){
                        $i++;
                        $gaji = $db->get_row("SELECT * FROM tb_slip_gaji WHERE GajiBulan='".intval($b)."' AND GajiTahun='$t' AND IDKaryawan='".$data->IDKaryawan."'");
                        ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo $data->Nama; ?></td>
                            <td style="text-align: center;"><?php echo $data->NoRekening1; ?></td>
                            <td style="text-align: right;"><?php echo number_format($gaji->TotalGaji); ?></td>
                            <td style="text-align: center;"><?php echo $gaji->NoSlip; ?></td>
                        </tr>
                        <?php
                        if (!($i % 5)) {
                            ?>
                            <tr><td colspan="5" style="height: 15px;"></td></tr>
                            <?php
                        }
                    }
                }
                ?>
            </tbody>
        </table>
        <table class="asignment" style="margin-top: 20px;">
            <tr>
                <td class="center" width="60%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td class="center" width="40%">Mengetahui,<br /><br /><br /><br />( Sumyartini )</td>
            </tr>
        </table>
        <script type="text/javascript">
            window.onload = function () { window.print(); }
        </script>
    </body>
</html>
</body>
</html>