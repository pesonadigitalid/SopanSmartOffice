<?php
session_start();
include_once "../config/connection.php";
$cond = " WHERE ";
$month = antiSQLInjection($_GET['month']);if($month<10) $month="0".$month;
$year = antiSQLInjection($_GET['year']);
$id = antiSQLInjection($_GET['id']);
$periode = "Periode : ".$month." - ".$year;
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <meta name="description" content=""/>
        <meta name="author" content="Yogi Pratama - Pesona Creative - 085737654543"/>
        
        <title>Aplikasi Pajak Paradise Bali Indah</title>
        
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport"/>
        <link rel="stylesheet" href="print-style.css" media="all" type="text/css"/>
    </head>
    <body>
        <table>
            <tr>
                <td width="50%" class="bottom">
                    <h1>CV. Solusi Pemanas Air Nusantara</h1>
                    Jl. Tukad Batanghari No. 42<br />
                    Denpasar 80225, Bali<br />
                    Phone. +62 823-2800-1818<br />
                    Email. mail.aristonbali@gmail.com<br />
                    User : <?php echo $_SESSION["name"]; ?>
                    </td>
                <td width="50%" align="right" class="bottom">
                    Tanggal Cetak : <?php echo date("d/m/Y"); ?>
                </td>
            </tr>
        </table>
        <div class="laporanTitle">
            <h1 class="underline">** LAPORAN PENJUALAN **</h1><?php echo $periode; ?>
        </div>
        <table class="tabelList2" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th width="90">Tour Name</th>
                    <th width="70">Ref</th>
                    <th>Periode</th>
                    <th width="50">Pax</th>
                    <th width="100">Price (USD)</th>
                    <th width="100">Price (RP)</th>
                    <th width="100">Total Biaya (RP)</th>
                    <th width="100">Margin (RP)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = newQuery("get_results","SELECT *, DATE_FORMAT(PeriodeStart,'%d %b') AS PeriodeStartID, DATE_FORMAT(PariodeAkhir,'%d %b') AS PariodeAkhirID FROM tb_master_transaksi WHERE '$year-$month' BETWEEN DATE_FORMAT(PeriodeStart,'%Y-%m') AND DATE_FORMAT(PariodeAkhir,'%Y-%m') ORDER BY IDMaster ASC");
                if($query){
                    $i=0;
                    $totalPack = 0;
                    $totalPriceUSD = 0;
                    $totalPriceIDR = 0;
                    $TotalBiaya = 0;
                    $Margin = 0;
                    foreach($query as $data){
                        $i++;
                        $totalPack += $data->Pax;
                        $totalPriceUSD += $data->HargaJualUSD;
                        $totalPriceIDR += $data->HargaJualIDR;
                        $totalBiaya += $data->TotalBiaya;
                        $totalMargin += $data->Margin;
                        ?>
                        <tr>
                            <td><?php echo $data->TourName; ?></td>
                            <td><?php echo $data->Ref; ?></td>
                            <td><?php echo $data->PeriodeStartID." - ".$data->PariodeAkhirID; ?></td>
                            <td><?php echo $data->Pax; ?></td>
                            <td><?php echo number_format($data->HargaJualUSD,2); ?></td>
                            <td><?php echo number_format($data->HargaJualIDR,2); ?></td>
                            <td><?php echo number_format($data->TotalBiaya,2); ?></td>
                            <td><?php echo number_format($data->Margin,2); ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <td colspan="3" style="text-align: right;"><strong>Total :</strong></td>
                        <td><strong><?php echo number_format($totalPack,0); ?></strong></td>
                        <td><strong><?php echo number_format($totalPriceUSD,2); ?></strong></td>
                        <td><strong><?php echo number_format($totalPriceIDR,2); ?></strong></td>
                        <td><strong><?php echo number_format($totalBiaya,2); ?></strong></td>
                        <td><strong><?php echo number_format($totalMargin,2); ?></strong></td>
                    </tr>
                    <?php
                } else {
                    echo "<td colspan='8'>Tidak ada data yang dapat ditampilkan...</td>";
                }
                ?>
            </tbody>
        </table>
        <table class="asignment" style="margin-top: 20px;">
            <tr>
                <td class="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td class="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td class="center">Mengetahui,<br /><br /><br /><br /><br /><br />(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
            </tr>
        </table>
        <script type="text/javascript">
            window.onload = function () { window.print(); }
        </script>
    </body>
</html>
</body>
</html>