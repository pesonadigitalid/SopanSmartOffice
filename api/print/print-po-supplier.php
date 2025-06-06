<?php
session_start();
include_once "../config/connection.php";
$bulan = array("01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", "09" => "September", 10 => "Oktober", "11" => "November", 12 => "Desember");

$datestart = $_GET['datestart'];
$expstart = explode("/", $datestart);
$datestartExp = $expstart[2] . "-" . $expstart[1] . "-" . $expstart[0];

$dateend = $_GET['dateend'];
$expend = explode("/", $dateend);
$dateendExp = $expend[2] . "-" . $expend[1] . "-" . $expend[0];

if ($datestart != "" && $dateend != "") {
    $cond = "tb_po.Tanggal BETWEEN '$datestartExp' AND '$dateendExp'";
    $subtitle = "Periode. " . $datestart . " - " . $dateend;
} else if ($datestart != "") {
    $cond = "tb_po.Tanggal='$datestartExp'";
    $subtitle = "Periode. " . $datestart;
} else {
    $cond = "DATE_FORMAT(tb_po.Tanggal,'%Y-%m') = '" . date("Y-m") . "'";
    $subtitle = "Periode : " . $bulan[date("m")] . " " . date("Y");
}
$periode = "Periode : " . $bulan[date("m")] . " " . date("Y");
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
    <h1 class="blue newPage">Rekap PO Supplier</h1>
    <h3 class="red"><?php echo $subtitle; ?></h3>
    <table class="tbLabaRugi" style="width: 100%">
        <thead>
            <tr>
                <td style="font-weight: bold;width: 30px;" class="red">No.</td>
                <td style="font-weight: bold;width: 50px;" class="red">Kode</td>
                <td style="font-weight: bold;" class="red">Supplier</td>
                <td style="text-align: right;font-weight: bold;border-bottom: solid 1px #333;padding-bottom: 5px;width:100px;" class="red">Total Belanja</td>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = newQuery("get_results", "SELECT
                            `tb_supplier`.`KodeSupplier`
                            , `tb_supplier`.`NamaPerusahaan`
                            , SUM(`tb_po`.`GrandTotal`) AS TotalBelanja
                        FROM
                            `lintasdayadb`.`tb_po`
                            INNER JOIN `lintasdayadb`.`tb_supplier` 
                        	ON (`tb_po`.`IDSupplier` = `tb_supplier`.`IDSupplier`)
                        WHERE $cond AND `tb_po`.IsLD = '1'
                         AND (JenisPO='1' OR JenisPO='2' OR JenisPO='3')
                        GROUP BY tb_po.`IDSupplier` ORDER BY SUM(`tb_po`.`GrandTotal`) DESC");
            if ($query) {
                $i = 0;
                $total = 0;
                foreach ($query as $data) {
                    $i++;
                    $total += $data->TotalBelanja;
            ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $data->KodeSupplier; ?></td>
                        <td><?php echo $data->NamaPerusahaan; ?></td>
                        <td style="text-align: right;font-weight: bold;border-bottom: solid 1px #333;padding-bottom: 5px;padding-top: 5px;">Rp. <?php echo number_format($data->TotalBelanja, 2); ?></td>
                    </tr>
            <?php
                }
            } else {
                echo "<tr><td colspan='4'>Tidak ada data yang dapat ditampilkan...</td></tr>";
            }
            ?>
            <tr>
                <td colspan="3" class="labelHeader" style="text-decoration: underline; text-align: left !important;">Total Belanja</td>
                <td style="text-align: right;font-weight: bold"> <?php echo number_format($total, 2); ?></td>
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