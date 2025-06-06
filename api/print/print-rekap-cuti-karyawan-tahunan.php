<?php
session_start();
include_once "../config/connection.php";
include_once "../library/class.cuticalculation.php";

$bulan = array("01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", "09" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember");

$cutiCalculation = new CutiCalculation();
$cutiCalculation->calcHolidayWithoutSunday();

$tahun = $_GET['tahun'];
$jenis = $_GET['jenis'];

if ($jenis == "CutiTahunan") {
    $title = "Cuti Tahunan";
    $jenis = "CUTI TAHUNAN";
} else if ($jenis == "CutiSpecial") {
    $title = "Cuti Special Tahunan";
    $jenis = "CUTI SPECIAL";
} else if ($jenis == "CutiSakit") {
    $title = "Cuti Sakit Tahunan";
    $jenis = "SAKIT";
} else if ($jenis == "CutiAlpha") {
    $title = "Alpha Tahunan";
    $jenis = "ALPHA";
}
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
    <link rel="stylesheet" href="print-style.css" media="all" type="text/css" />
    <style media="all">
        body {
            font-size: 10px !important;
        }
    </style>
</head>

<body class="center">
    <h1 class="blue newPage" style="margin-bottom:0">Rekap <?php echo $title; ?> Karyawan</h1>
    <p>Periode Tahun: <?php echo $tahun; ?></p><br />
    <table class="tabelList6 border-solid" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th width="10">No</th>
                <th>Karyawan</th>
                <?php
                foreach ($bulan as $key => $value) {
                    echo "<th width='30' class='center'>" . substr($value, 0, 3) . "</th>";
                }
                ?>
                <th width="10">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = newQuery("get_results", "SELECT * FROM tb_karyawan WHERE Status='1' AND IDKaryawan>1 AND IDDepartement<>'4' AND StatusKaryawan<>'Harian' ORDER BY Nama ASC");
            if ($query) {
                $num = 1;
            ?>
                <tr>
                    <td colspan="15" style="text-align: left"><strong>Lintas Daya</strong></td>
                </tr>
                <?php
                foreach ($query as $data) {
                    $karyawan = $data->IDKaryawan;
                    $cutiCalculation->generateKalendarCutiKaryawan($tahun, $karyawan);
                    $totalCuti = 0;
                ?>
                    <tr>
                        <td><?php echo $num; ?></td>
                        <td style="text-align: left"><?php echo $data->Nama; ?></td>
                        <?php
                        foreach ($bulan as $key => $value) {
                            $t = $tahun . '-' . $key;

                            $cuti = $cutiCalculation->getTotalCutiBulananKaryawan($tahun, $key, $karyawan, $jenis);
                            if (!$cuti) $cuti = 0;

                            $totalCuti += $cuti;
                            echo "<td width='30' class='center'>" . ($cuti) . "</td>";
                        }
                        ?>
                        <td><?php echo $totalCuti; ?></td>
                    </tr>
                    <?php
                    if (!($num % 5)) {
                    ?>
                        <tr>
                            <td colspan="15" style="height: 15px;text-align: left"></td>
                        </tr>
                <?php
                    }
                    $num++;
                }
            }

            $query = newQuery("get_results", "SELECT * FROM tb_karyawan WHERE Status='1' AND IDKaryawan>1 AND StatusKaryawan<>'Harian' ORDER BY Nama ASC");
            if ($query) {
                $num = 1;
                ?>
                <tr>
                    <td colspan="15" style="height: 15px;"></td>
                </tr>
                <tr>
                    <td colspan="15" style="text-align: left"><strong>MMS</strong></td>
                </tr>
                <?php
                foreach ($query as $data) {
                    $karyawan = $data->IDKaryawan;
                    $cutiCalculation->generateKalendarCutiKaryawan($tahun, $karyawan);
                    $totalCuti = 0;
                ?>
                    <tr>
                        <td><?php echo $num; ?></td>
                        <td style="text-align: left"><?php echo $data->Nama; ?></td>
                        <?php
                        foreach ($bulan as $key => $value) {
                            $t = $tahun . '-' . $key;

                            $cuti = $cutiCalculation->getTotalCutiBulananKaryawan($tahun, $key, $karyawan, $jenis);
                            if (!$cuti) $cuti = 0;

                            $totalCuti += $cuti;
                            echo "<td width='30' class='center'>" . ($cuti) . "</td>";
                        }
                        ?>
                        <td><?php echo $totalCuti; ?></td>
                    </tr>
                    <?php
                    if (!($num % 5)) {
                    ?>
                        <tr>
                            <td colspan="15" style="height: 15px;text-align: left"></td>
                        </tr>
            <?php
                    }
                    $num++;
                }
            }

            /*
            $query = newQuery("get_results", "SELECT * FROM tb_karyawan WHERE Status='1' AND IDKaryawan>1 AND StatusKaryawan='Harian' ORDER BY Nama ASC");
            if ($query) {
                $num = 1;
                ?>
                <tr>
                    <td colspan="15" style="height: 15px;"></td>
                </tr>
                <tr>
                    <td colspan="15" style="text-align: left"><strong>Karyawan Harian</strong></td>
                </tr>
                <?php
                foreach ($query as $data) {
                    $karyawan = $data->IDKaryawan;
                ?>
                    <tr>
                        <td><?php echo $num; ?></td>
                        <td style="text-align: left"><?php echo $data->Nama; ?></td>
                        <?php
                        foreach ($bulan as $key => $value) {
                            echo "<td width='30' class='center'></td>";
                        }
                        ?>
                        <td></td>
                    </tr>
                    <?php
                    if (!($num % 5)) {
                    ?>
                        <tr>
                            <td colspan="15" style="height: 15px;text-align: left"></td>
                        </tr>
            <?php
                    }
                    $num++;
                }
            }
            */
            ?>
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