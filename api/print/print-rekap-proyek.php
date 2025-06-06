<?php
session_start();
include_once "../config/connection.php";
$bulan = array("01"=>"Januari","02"=>"Februari","03"=>"Maret","04"=>"April","05"=>"Mei","06"=>"Juni","07"=>"Juli","08"=>"Agustus","09"=>"September",10=>"Oktober","11"=>"November",12=>"Desember");

$b = antiSQLInjection($_GET['bulan']);
$t = antiSQLInjection($_GET['tahun']);
$iddepartement = antiSQLInjection($_GET['departement']);
if($iddepartement==""){
    $dArray = array("1","3","5");
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
                <td colspan="2"><h3 class="title-print" style="text-transform: uppercase;font-size: 14px !important;margin-bottom: 20px;">Laporan Rekap Proyek Periode <?php echo $t; ?></h3></td>
            </tr>
        </table>
        <table class="tabelList7 border-solid" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th style="width:10px">NO</th>
                    <th style="width:80px">DEPARTEMENT</th>
                    <th style="width:10px">NO</th>
                    <th style="width:80px">KODE PROYEK</th>
                    <th>NAMA PROYEK</th>
                    <th style="width:120px">NO. KONTRAK</th>
                    <th style="width:70px">NILAI PROYEK</th>
                    <th style="width:70px">PPN</th>
                    <th style="width:70px">TOTAL KONTRAK</th>
                    <th style="width:70px">TOTAL VO</th>
                    <th style="width:70px">NILAI AKHIR</th>
                </tr>
            </thead>
                <?php
                if($iddepartement==""){
                    $i = 0;
                    foreach ($dArray as $id){
                        $i++;
                        $departement = $db->get_var("SELECT NamaDepartement FROM tb_departement WHERE IDDepartement='$id'");
                        $queryProyek = $db->get_results("SELECT * FROM tb_proyek WHERE Tahun='$t' AND IDDepartement='$id' ORDER BY Tahun, KodeProyek");
                        $nominal = 0;
                        $ppn = 0;
                        $total = 0;
                        $vo = 0;
                        $grand = 0;
                        if($queryProyek){
                            $j=0;
                            $prev="";
                            foreach($queryProyek as $dataProyek){
                                $j++;
                                ?>
                                <tr>
                                    <td style="text-align: center;"><?php if($prev!=$departement) echo $i; ?></td>
                                    <td><?php if($prev!=$departement) echo $departement; ?></td>
                                    <td style="text-align: center;"><?php echo $j; ?></td>
                                    <td style="text-align: center;"><?php echo $dataProyek->KodeProyek; ?>/<?php echo $dataProyek->Tahun; ?></td>
                                    <td><?php echo $dataProyek->NamaProyek; ?></td>
                                    <td style="text-align: center;"><?php echo $dataProyek->NoKontrak; ?></td>
                                    <td style="text-align: right;"><?php echo number_format($dataProyek->Nominal); ?></td>
                                    <td style="text-align: right;"><?php echo number_format($dataProyek->PPN); ?></td>
                                    <td style="text-align: right;"><?php echo number_format($dataProyek->Total2); ?></td>
                                    <td style="text-align: right;"><?php echo number_format($dataProyek->GrandTotalVO); ?></td>
                                    <td style="text-align: right;"><?php echo number_format($dataProyek->GrandTotal); ?></td>
                                </tr>
                                <?php
                                $nominal += $dataProyek->Nominal;
                                $ppn += $dataProyek->PPN;
                                $total += $dataProyek->Total2;
                                $vo += $dataProyek->GrandTotalVO;
                                $grand += $dataProyek->GrandTotal;
                                if($prev!=$departement) $prev=$departement;
                            }
                        }
                        ?>
                        <tr>
                            <th colspan="6" style="text-align: right;">GRAND TOTAL: </th>
                            <th style="text-align: right;"><?php echo number_format($nominal); ?></th>
                            <th style="text-align: right;"><?php echo number_format($ppn); ?></th>
                            <th style="text-align: right;"><?php echo number_format($total); ?></th>
                            <th style="text-align: right;"><?php echo number_format($vo); ?></th>
                            <th style="text-align: right;"><?php echo number_format($grand); ?></th>
                        </tr>
                        <?php
                    }
                } else {
                    $departement = $db->get_var("SELECT NamaDepartement FROM tb_departement WHERE IDDepartement='$iddepartement'");
                    $queryProyek = $db->get_results("SELECT * FROM tb_proyek WHERE Tahun='$t' AND IDDepartement='$iddepartement'");
                    $nominal = 0;
                    $ppn = 0;
                    $total = 0;
                    $vo = 0;
                    $grand = 0;
                    if($queryProyek){
                        $j=0;
                        $prev="";
                        foreach($queryProyek as $dataProyek){
                            $j++;
                            ?>
                            <tr>
                                <td style="text-align: center;"><?php if($prev!=$departement) echo $i; ?></td>
                                <td><?php if($prev!=$departement) echo $departement; ?></td>
                                <td style="text-align: center;"><?php echo $j; ?></td>
                                <td style="text-align: center;"><?php echo $dataProyek->KodeProyek; ?>/<?php echo $dataProyek->Tahun; ?></td>
                                <td><?php echo $dataProyek->NamaProyek; ?></td>
                                <td style="text-align: center;"><?php echo $dataProyek->NoKontrak; ?></td>
                                <td style="text-align: right;"><?php echo number_format($dataProyek->Nominal); ?></td>
                                <td style="text-align: right;"><?php echo number_format($dataProyek->PPN); ?></td>
                                <td style="text-align: right;"><?php echo number_format($dataProyek->Total2); ?></td>
                                <td style="text-align: right;"><?php echo number_format($dataProyek->GrandTotalVO); ?></td>
                                <td style="text-align: right;"><?php echo number_format($dataProyek->GrandTotal); ?></td>
                            </tr>
                            <?php
                            $nominal += $dataProyek->Nominal;
                            $ppn += $dataProyek->PPN;
                            $total += $dataProyek->Total2;
                            $vo += $dataProyek->GrandTotalVO;
                            $grand += $dataProyek->GrandTotal;
                            if($prev!=$departement) $prev=$departement;
                        }
                    }
                    ?>
                    <tr>
                        <th colspan="6" style="text-align: right;">GRAND TOTAL: </th>
                        <th style="text-align: right;"><?php echo number_format($nominal); ?></th>
                        <th style="text-align: right;"><?php echo number_format($ppn); ?></th>
                        <th style="text-align: right;"><?php echo number_format($total); ?></th>
                        <th style="text-align: right;"><?php echo number_format($vo); ?></th>
                        <th style="text-align: right;"><?php echo number_format($grand); ?></th>
                    </tr>
                    <?php
                }
                ?>
            <tbody>
            </tbody>
        </table>
        <table class="asignment" style="margin-top: 20px;">
            <tr>
                <td class="center" width="60%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td class="center" width="40%">Mengetahui,<br /><br /><br /><br />( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</td>
            </tr>
        </table>
        <script type="text/javascript">
            window.onload = function () { window.print(); }
        </script>
    </body>
</html>
</body>
</html>