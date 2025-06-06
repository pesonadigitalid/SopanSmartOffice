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
    <h1 class="blue newPage">Rekap Retur Tahunan Per-Proyek</h1>
    <h3 class="red">Periode Tahun: <?php echo $tahun; ?></h3>
    <table class="tbLabaRugi" style="width: auto">
        <thead>
            <tr>
                <td style="font-weight: bold;width: 150px;" class="red">Retur Bulan</td>
                <td style="text-align: center;font-weight: bold;border-bottom: solid 1px #333;padding-bottom: 5px;width:200px;" class="red">Grand Total</td>
            </tr>
            <tr>
        </thead>
        <tbody>
            <?php
            $GrandTotal = 0;
            $TotalProyek = [];
            for ($i = 1; $i <= 12; $i++) {
                if ($i < 10) $bul = "0" . $i;
                else $bul = $i;
                $total = 0;
            ?>
                <tr>
                    <td style="font-weight: bold;" class="red"><?php echo $bulan[$bul]; ?></td>
                    <td style="text-align: right;border-bottom: solid 1px #333;padding-bottom: 5px;">Rupiah</td>
                </tr>
                <?php
                $queryProyek = newQuery("get_results", "SELECT DISTINCT(a.IDProyek) AS IDProyek, b.KodeProyek, b.NamaProyek, b.Tahun FROM tb_audit a, tb_proyek b WHERE a.IDProyek=b.IDProyek AND a.IsLD='1' AND DATE_FORMAT(a.Tanggal,'%Y-%m')='$tahun-$bul' ORDER BY b.Tahun, b.KodeProyek");
                if ($queryProyek) {
                    foreach ($queryProyek as $data) {
                        $ReturProyek = newQuery("get_var", "SELECT SUM(GrandTotal) FROM tb_audit WHERE IDProyek='" . $data->IDProyek . "' AND IsLD='1' AND DATE_FORMAT(Tanggal,'%Y-%m')='$tahun-$bul'");
                        if (!$ReturProyek) $ReturProyek = 0;
                        $total += $ReturProyek;
                        $TotalProyek[$data->IDProyek] += $ReturProyek;
                ?>
                        <tr>
                            <td style="padding-left:20px;text-transform: uppercase;">
                                <?php echo $data->KodeProyek . " " . $data->Tahun . " - " . $data->NamaProyek; ?>
                            </td>
                            <td style="text-align: right"><?php echo number_format($ReturProyek, 2); ?></td>
                        </tr>
                <?php
                    }
                }
                $GrandTotal += $total;
                ?>
                <tr>

                    <td style="font-weight: bold;text-decoration:underline;" class="red">Total Retur <?php echo $bulan[$bul]; ?></td>
                    <td style="text-align: right;font-weight: bold;border-bottom: solid 1px #333;border-top: solid 1px #333;padding-bottom: 5px;padding-top: 5px;"><?php echo number_format($total, 2); ?></td>
                </tr>
            <?php
            }
            ?>
            <tr>
                <td class="labelHeader" style="text-decoration: underline; text-align: left !important;">Total Retur <?php echo $tahun; ?></td>
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