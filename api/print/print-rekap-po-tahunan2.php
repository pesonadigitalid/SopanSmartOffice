<?php
session_start();
include_once "../config/connection.php";
$bulan = array("01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", "09" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember");

$tahun = $_GET['tahun'];
?>
<!DOCTYPE HTML>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="author" content="Yogi Pratama - Pesona Creative - 085737654543" />

    <title>SOPAN Smart Office - Smart office for smart people</title>

    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />
    <link rel="stylesheet" href="print-style-acc.css" media="all" type="text/css" />
    <style media="all">
        body {
            font-size: 10px !important;
        }
    </style>
</head>

<body class="center">
    <h1 class="blue newPage">Rekap PO Tahunan <?php echo $subtitle; ?></h1>
    <h3 class="red">Periode Tahun: <?php echo $tahun; ?></h3>
    <table class="tbLabaRugi" style="width: auto">
        <thead>
            <tr>
                <td style="font-weight: bold;width: 150px;" class="red">PO Bulan</td>
                <td style="text-align: right;font-weight: bold;border-bottom: solid 1px #333;padding-bottom: 5px;width:100px;" class="red">Total PO PPN</td>
                <td style="text-align: right;font-weight: bold;border-bottom: solid 1px #333;padding-bottom: 5px;width:100px;" class="red">Total PO NON-PPN</td>
                <td style="text-align: right;font-weight: bold;border-bottom: solid 1px #333;padding-bottom: 5px;width:100px;" class="red">Total PO</td>
            </tr>
            <tr>
                <td style="font-weight: bold;" class="red"><?php echo $bulan[$bul]; ?></td>
                <td style="text-align: right;border-bottom: solid 1px #333;padding-bottom: 5px;">Rupiah</td>
                <td style="text-align: right;border-bottom: solid 1px #333;padding-bottom: 5px;">Rupiah</td>
                <td style="text-align: right;border-bottom: solid 1px #333;padding-bottom: 5px;">Rupiah</td>
            </tr>
            <tr>
        </thead>
        <tbody>
            <?php
            $GrandTotal = 0;
            $GrandTotalPajak = 0;
            $GrandTotalNonPajak = 0;
            for ($i = 1; $i <= 12; $i++) {
                if ($i < 10) $bul = "0" . $i;
                else $bul = $i;

                $TotalPOBulananPajak = newQuery("get_var", "SELECT SUM(GrandTotal) FROM tb_po WHERE IsLD='1' AND IsPajak='1' AND (JenisPO='1' OR JenisPO='2' OR JenisPO='3') AND DATE_FORMAT(Tanggal,'%Y-%m')='$tahun-$bul'");
                if (!$TotalPOBulananPajak) $TotalPOBulananPajak = 0;
                $TotalPOBulananNonPajak = newQuery("get_var", "SELECT SUM(GrandTotal) FROM tb_po WHERE IsLD='1' AND IsPajak='0' AND (JenisPO='1' OR JenisPO='2' OR JenisPO='3') AND DATE_FORMAT(Tanggal,'%Y-%m')='$tahun-$bul'");
                if (!$TotalPOBulananNonPajak) $TotalPOBulananNonPajak = 0;
                $TotalPOBulanan = $TotalPOBulananPajak + $TotalPOBulananNonPajak;

                $GrandTotalPajak += $TotalPOBulananPajak;
                $GrandTotalNonPajak += $TotalPOBulananNonPajak;
                $GrandTotal += $TotalPOBulanan;
            ?>
                <tr>
                    <td style="font-weight: bold;" class="red"><?php echo $bulan[$bul]; ?></td>
                    <td style="text-align: right;font-weight: bold;border-bottom: solid 1px #333;padding-bottom: 5px;padding-top: 5px;"><?php echo number_format($TotalPOBulananPajak, 2); ?></td>
                    <td style="text-align: right;font-weight: bold;border-bottom: solid 1px #333;padding-bottom: 5px;padding-top: 5px;"><?php echo number_format($TotalPOBulananNonPajak, 2); ?></td>
                    <td style="text-align: right;font-weight: bold;border-bottom: solid 1px #333;padding-bottom: 5px;padding-top: 5px;"><?php echo number_format($TotalPOBulanan, 2); ?></td>
                </tr>
            <?php
            }
            ?>
            <tr>
                <td class="labelHeader" style="text-decoration: underline; text-align: left !important;">Total PO <?php echo $tahun; ?></td>
                <td style="text-align: right;font-weight: bold"><?php echo number_format($GrandTotalPajak, 2); ?></td>
                <td style="text-align: right;font-weight: bold"><?php echo number_format($GrandTotalNonPajak, 2); ?></td>
                <td style="text-align: right;font-weight: bold"><?php echo number_format($GrandTotal, 2); ?></td>
            </tr>
        </tbody>
    </table>
    <table class="asignment" style="margin-top: 20px;">
        <tr>
            <td class="center" width="60%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td class="center" width="40%">Mengetahui,<br /><br /><br /><br />( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</td>
        </tr>
    </table>
    <script type="text/javascript">
        setTimeout(function() {
            window.print();
        }, 1500);
    </script>
</body>

</html>