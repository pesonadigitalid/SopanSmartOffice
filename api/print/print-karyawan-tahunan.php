<?php
session_start();
include_once "../config/connection.php";
$bulan = array("01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", "09" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember");

$tahun = $_GET['tahun'];
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="author" content="Yogi Pratama - Pesona Creative - 085737654543" />

    <title>MMS - Smart Office</title>

    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />
    <link rel="stylesheet" href="print-style.css" media="all" type="text/css" />
</head>

<body>
    <table>
        <tr>
            <td width="50%" class="bottom">
                <h1>CV. Solusi Pemanas Air Nusantara</h1>
                Jl. Tukad Yeh Aya No.70b, Panjer, Denpasar Selatan, Kota Denpasar, Bali 80234<br />
                Telp. (0361) 8497915, Fax. -<br />
                User : <?php echo $_SESSION["name"]; ?>
            </td>
            <td width="50%" align="right" class="bottom">
                Tanggal Cetak : <?php echo date("d/m/Y"); ?>
            </td>
        </tr>
    </table>
    <div class="laporanTitle">
        <h1 class="underline">** REKAP KARYAWAN KELUAR MASUK TAHUNAN **</h1>
        Periode: <?php echo $tahun; ?>
    </div>
    <table class="tabelList2" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width:20px">No</th>
                <th style="width:100px">Bulan</th>
                <th>Jumlah Karyawan</th>
                <th>Bergabung</th>
                <th>Keluar</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            $aJumlahKaryawan = array();
            $aBergabung = array();
            $aKeluar = array();
            $aTotal = array();
            foreach ($bulan as $key => $value) {
                $i++;
                $startDate = "$tahun-$key-01";
                $endDate = "$tahun-$key-" . cal_days_in_month(CAL_GREGORIAN, $key, $tahun);;
                $jumlahKaryawan = newQuery("get_var", "SELECT COUNT(*) FROM tb_karyawan WHERE concat(TahunMasuk, '-', BulanMasuk, '-', TahunMasuk) < '$startDate' AND Status='1' AND IDKaryawan>1");
                if (!$jumlahKaryawan) $jumlahKaryawan = 0;

                $bergabung = newQuery("get_var", "SELECT COUNT(*) FROM tb_karyawan WHERE concat(TahunMasuk, '-', BulanMasuk, '-', TahunMasuk) BETWEEN '$startDate' AND '$endDate' AND Status='1' AND IDKaryawan>1");
                if (!$bergabung) $bergabung = 0;

                $keluar = newQuery("get_var", "SELECT COUNT(*) FROM tb_karyawan WHERE TanggalResign BETWEEN '$startDate' AND '$endDate' AND Status='0' AND IDKaryawan>1");
                if (!$keluar) $keluar = 0;

                $total = $jumlahKaryawan + $bergabung - $keluar;

                array_push($aJumlahKaryawan, $jumlahKaryawan);
                array_push($aBergabung, $bergabung);
                array_push($aKeluar, $keluar);
                array_push($aTotal, $total);
            ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $value; ?></td>
                    <td><?php echo number_format($jumlahKaryawan); ?></td>
                    <td><?php echo number_format($bergabung); ?></td>
                    <td><?php echo number_format($keluar); ?></td>
                    <td><?php echo number_format($total); ?></td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>


    <div id="container" style="margin-top:40px;width: 800px;margin: 40px auto;"></div>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script type="text/javascript">
        Highcharts.chart('container', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Rekap Karyawan Keluar Masuk Tahunan'
            },
            subtitle: {
                text: 'Periode: <?php echo $tahun; ?>'
            },
            xAxis: {
                categories: [
                    'Jan',
                    'Feb',
                    'Mar',
                    'Apr',
                    'May',
                    'Jun',
                    'Jul',
                    'Aug',
                    'Sep',
                    'Oct',
                    'Nov',
                    'Dec'
                ],
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Jumlah Karyawan'
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y}</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: [{
                name: 'Jumlah Karyawan',
                data: [<?php echo implode(",", $aJumlahKaryawan); ?>]

            }, {
                name: 'Bergabung',
                data: [<?php echo implode(",", $aBergabung); ?>]

            }, {
                name: 'Keluar',
                data: [<?php echo implode(",", $aKeluar); ?>]

            }, {
                name: 'Total',
                data: [<?php echo implode(",", $aTotal); ?>]

            }]
        });

        setTimeout(function() {
            window.print();
        }, 1500);
    </script>
</body>

</html>