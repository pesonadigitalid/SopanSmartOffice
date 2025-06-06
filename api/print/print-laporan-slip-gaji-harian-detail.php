<?php
session_start();
include_once "../config/connection.php";
$bulanList = array("01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", "09" => "September", 10 => "Oktober", "11" => "November", 12 => "Desember");

$datestart = $fungsi->ENDate($_GET['datestart']);
$dateend = $fungsi->ENDate($_GET['dateend']);
$proyek = antiSQLInjection($_GET['proyek']);

$cond = "AND PeriodeStart='$datestart' AND PeriodeEnd='$dateend'";

$subtitle = "Periode: " . $fungsi->IDDate($datestart) . " - " . $fungsi->IDDate($dateend);

if ($proyek != "") {
    $dp = newQuery("get_row", "SELECT * FROM tb_proyek WHERE IDProyek='$proyek'");
    if ($dp) {
        $dp = $dp->KodeProyek . "/" . $dp->Tahun . "/" . $dp->NamaProyek;
    }

    $cond .= " AND IDProyek='$proyek'";
    $subtitle = "<br/>Proyek: " . $dp;
} else if ($proyek == "0") {
    $subtitle = "<br/>Sub: Kantor / Maintenance ";
}

$first = strtotime($datestart);
$second = strtotime($dateend);
$datediff = $second - $first;
$dayDiff = round($datediff / (60 * 60 * 24)) + 1;
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="author" content="Yogi Pratama - Pesona Creative - 085737654543" />

    <title>SOPAN Smart Office - Smart office for smart people</title>

    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />
    <link rel="stylesheet" href="print-style.css" media="all" type="text/css" />
    <link href="../../themes/assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css" />
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
        <h1 class="underline">** LAPORAN SLIP GAJI HARIAN DETAIL **</h1><?php echo $subtitle; ?>
    </div>
    <table class="tabelList2 border-solid smallfont" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th width="100" rowspan="2">No. Slip</th>
                <th width="100" rowspan="2">Proyek</th>
                <th rowspan="2">Nama</th>
                <th width="80" rowspan="2">Jabatan</th>
                <th width="80" colspan="<?php echo $dayDiff; ?>">Tanggal</th>
                <th width="80" rowspan="2">THK</th>
                <th width="80" rowspan="2">GH</th>
                <th width="80" rowspan="2">SUB</th>
                <th width="80" rowspan="2">TUM</th>
                <th width="80" rowspan="2">TUK</th>
                <!-- <th width="80" rowspan="2">TUT</th> -->
                <th width="80" rowspan="2">TUL</th>
                <th width="80" rowspan="2">TUML</th>
                <th width="80" rowspan="2">TJ</th>
                <th width="80" rowspan="2">P</th>
                <th width="80" rowspan="2">T. Gaji</th>
            </tr>
            <tr>
                <?php
                $begin = new DateTime($datestart);
                $end = new DateTime(date('Y-m-d', strtotime($dateend . ' + 1 days')));

                $interval = DateInterval::createFromDateString('1 day');
                $period = new DatePeriod($begin, $interval, $end);

                foreach ($period as $dt) {
                ?>
                    <th width="10"><?php echo $dt->format("d/m"); ?></th>
                <?php
                }
                ?>
            </tr>
        </thead>
        <tbody>
            <?php
            $total = 0;
            $query = newQuery("get_results", "SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID, DATE_FORMAT(PeriodeStart,'%d/%m/%Y') AS PeriodeStartID, DATE_FORMAT(PeriodeEnd,'%d/%m/%Y') AS PeriodeEndID FROM tb_slip_gaji WHERE IDSlipGaji>0 $cond AND Harian='1' ORDER BY IDProyek, IDSlipGaji");
            $i = 0;
            if ($query) {
                foreach ($query as $data) {
                    $dProyek = "KANTOR / MAINTENANCE";
                    $proyek = newQuery("get_row", "SELECT * FROM tb_proyek WHERE IDProyek='$data->IDProyek'");
                    if ($proyek) {
                        $dProyek = $proyek->KodeProyek . "/" . $proyek->Tahun . "/" . $proyek->NamaProyek;
                    }

                    $karyawan = newQuery("get_row", "SELECT * FROM tb_karyawan WHERE IDKaryawan='" . $data->IDKaryawan . "'");
                    $jabatan = newQuery("get_var", "SELECT Jabatan FROM tb_jabatan WHERE IDJabatan='" . $karyawan->IDJabatan . "'");

                    $total += $data->TotalGaji;
                    $i++;
            ?>
                    <tr>
                        <td><?php echo $data->NoSlip; ?></td>
                        <td><?php echo $dProyek; ?></td>
                        <td><?php echo $karyawan->Nama; ?></td>
                        <td><?php echo $jabatan; ?></td>
                        <?php
                        $absenDetail = json_decode($data->AbsenDetail);
                        foreach ($absenDetail as $aDetail) {
                            if ($aDetail->TotalJamKerja > 0) {
                        ?>
                                <td style="text-align: center;">
                                    <i class="fa fa-check"></i>
                                </td>
                            <?php
                            } else {
                            ?>
                                <td></td>
                        <?php
                            }
                        }
                        ?>
                        <td style="text-align: center;"><?php echo number_format($data->TotalAbsen, 0); ?></td>
                        <td style="text-align: right;"><?php echo number_format($data->GajiPokokHarian, 0); ?></td>
                        <td style="text-align: right;"><?php echo number_format($data->GajiPokok, 0); ?></td>
                        <td style="text-align: right;"><?php echo number_format($data->UangMakan, 0); ?></td>
                        <td style="text-align: right;"><?php echo number_format($data->UangHadir, 0); ?></td>
                        <!-- <td style="text-align: right;"><?php echo number_format($data->UangTransport, 0); ?></td> -->
                        <td style="text-align: right;"><?php echo number_format($data->UangLembur, 0); ?></td>
                        <td style="text-align: right;"><?php echo number_format($data->UangMakanLembur, 0); ?></td>
                        <td style="text-align: right;"><?php echo number_format($data->UangTunjanganKhusus, 0); ?></td>
                        <td style="text-align: right;"><?php echo number_format($data->PotonganLainLain, 0); ?></td>
                        <td style="text-align: right;"><?php echo number_format($data->TotalGaji, 0); ?></td>
                    </tr>
            <?php
                }
            } else {
                echo "<tr><td colspan='".($dayDiff + 14)."'>Tidak ada data yang dapat ditampilkan...</td></tr>";
            }
            ?>
            <tr>
                <td colspan="<?php echo ($dayDiff + 13); ?>" style="text-align: right;"><strong>Total Gaji:</strong></td>
                <td style="text-align: right;"><?php echo number_format($total, 0); ?></td>
            </tr>
        </tbody>
    </table>
    <small>THK = Total Hari Kerja; GH = Gaji Harian; SUB = Sub Total; TUM = Total Uang Makan; TUK = Total Uang Kehadiran; TUL = Total Uang Lembur; TUML = Total Uang Makan Lembur; P = Potongan; T. Gaji = Total Gaji</small>
    <table class="asignment" style="margin-top: 20px;">
        <tr>
            <td class="center" width="20%">&nbsp;<br />Direktur<br /><br /><br />( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</td>
            <td class="center" width="20%">&nbsp;<br />Cost Control<br /><br /><br />( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</td>
            <td class="center" width="20%">&nbsp;<br />Keuangan<br /><br /><br />( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</td>
            <td class="center" width="20%">&nbsp;<br />PM<br /><br /><br />( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</td>
            <td class="center" width="20%">Mengetahui,<br />HRD<br /><br /><br />( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</td>
        </tr>
    </table>
    <script type="text/javascript">
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>
</body>

</html>