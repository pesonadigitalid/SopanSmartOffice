<?php
session_start();
include_once "../config/connection.php";
include_once "../library/class.absencalculation.php";
$bulan2 = array("01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", "09" => "September", 10 => "Oktober", "11" => "November", 12 => "Desember");

$bulan = $_GET['datestart'];
$tahun = $_GET['dateend'];
$jenis = $_GET['jenis'];
$subtitle = "Periode : " . $bulan2[$bulan] . " " . $tahun;

if ($jenis == "0") $subtitle .= "<br/>Staff Kantor";
else if ($jenis == "1") $subtitle .= "<br/>Tenaga Harian";

$bulan2 = intval($bulan) - 1;
if ($bulan2 < 10) $bulan2 = "0" . $bulan2;

if ($bulan == "01") {
    $tahun2 = $tahun - 1;
    $bulan2 = "12";
} else {
    $tahun2 = $tahun;
}

$absen = new AbsenCalculation();
$absen->calcHolidayWithoutSunday($tahun);

$query = newQuery("get_results", "SELECT * FROM tb_karyawan WHERE Status='1' ORDER BY Nama ASC");
if ($query) {
    foreach ($query as $data) {
        $karyawan = $data->IDKaryawan;
        $return = $absen->generateAbsentBulananKaryawan($tahun, $bulan, $karyawan);
    }
}
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
        <h1 class="underline">** LAPORAN DATA ABSEN KARYAWAN **</h1><?php echo $subtitle; ?>
    </div>
    <table class="tabelList6 border-solid" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th width="10">No</th>
                <th>Karyawan</th>
                <?php
                $col = 0;
                for ($i = 26; $i <= cal_days_in_month(CAL_GREGORIAN, $bulan2, $tahun2); $i++) {
                    $col++;
                    $tanggal = $tahun2 . "-" . $bulan2 . "-" . $i;
                    if ($absen->isDateHoliday($tanggal)) {
                        $class = "red";
                    } else {
                        $class = "false";
                    }
                    echo "<th width='10' class='center $class'>$i</th>";
                }
                for ($i = 1; $i <= 25; $i++) {
                    $col++;
                    $tanggal = $tahun . "-" . $bulan . "-" . $i;
                    if ($absen->isDateHoliday($tanggal)) {
                        $class = "red";
                    } else {
                        $class = "";
                    }
                    echo "<th width='10' class='center $class'>$i</th>";
                }
                $col = $col + 9;
                ?>
                <th width="10">HK</th>
                <th width="10">C</th>
                <th width="10">S</th>
                <th width="10">A</th>
                <th width="10">K</th>
                <th width="10">TKK</th>
                <th width="10">HKK</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($jenis == "" || $jenis == "0") {
                $query = newQuery("get_results", "SELECT * FROM tb_karyawan WHERE Status='1' AND IDKaryawan>1 AND IDDepartement<>'4' AND StatusKaryawan<>'Harian' ORDER BY Nama ASC");
                if ($query) {
                    $num = 1;
            ?>
                    <tr>
                        <td colspan="<?php echo $col; ?>"><strong>Lintas Daya</strong></td>
                    </tr>
                    <?php
                    foreach ($query as $data) {
                        $karyawan = $data->IDKaryawan;
                    ?>
                        <tr>
                            <td><?php echo $num; ?></td>
                            <td><?php echo $data->Nama; ?></td>
                            <?php
                            for ($i = 26; $i <= cal_days_in_month(CAL_GREGORIAN, $bulan2, $tahun2); $i++) {
                                $tanggal = $tahun2 . "-" . $bulan2 . "-" . $i;
                                $dataAbsen = newQuery("get_row", "SELECT * FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='" . $karyawan . "' AND Tanggal='$tanggal'");

                                $status = "";
                                if ($dataAbsen) {
                                    if ($dataAbsen->TotalJamKerja > 0) {
                                        $status = "m";
                                    } else if ($dataAbsen->CutiTahunan > 0) {
                                        $status = "c";
                                    } else if ($dataAbsen->CutiSakit > 0) {
                                        $status = "s";
                                    } else if ($dataAbsen->CutiAlpha > 0) {
                                        $status = "a";
                                    } else if ($dataAbsen->CutiSpecial > 0) {
                                        $status = "k";
                                    }
                                }
                                echo "<td width='10' class='center'>$status</td>";
                            }
                            for ($i = 1; $i <= 25; $i++) {
                                $tanggal = $tahun . "-" . $bulan . "-" . $i;
                                $dataAbsen = newQuery("get_row", "SELECT * FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='" . $karyawan . "' AND Tanggal='$tanggal'");

                                $status = "";
                                if ($dataAbsen) {
                                    if ($dataAbsen->TotalJamKerja > 0) {
                                        $status = "m";
                                    } else if ($dataAbsen->CutiTahunan > 0) {
                                        $status = "c";
                                    } else if ($dataAbsen->CutiSakit > 0) {
                                        $status = "s";
                                    } else if ($dataAbsen->CutiAlpha > 0) {
                                        $status = "a";
                                    } else if ($dataAbsen->CutiSpecial > 0) {
                                        $status = "k";
                                    } else if ($dataAbsen->CutiTugasKeluar > 0) {
                                        $status = "ttk";
                                    }
                                }
                                echo "<td width='10' class='center'>$status</td>";
                            }

                            $totalCutiTahunan = $absen->getTotalCutiTahunanBulanKaryawan($tahun, $bulan, $karyawan);
                            $totalCutiSakit = $absen->getTotalCutiSakitBulanKaryawan($tahun, $bulan, $karyawan);
                            $totalCutiSpecial = $absen->getTotalCutiSpecialBulanKaryawan($tahun, $bulan, $karyawan);
                            $totalCutiAlpha = $absen->getTotalAlphaBulanKaryawan($tahun, $bulan, $karyawan);
                            $totalCutiTugasKeluar = $absen->getTotalTugasBulanKaryawan($tahun, $bulan, $karyawan);
                            $totalHariKerjaKaryawan = $absen->getTotalHariKerjaBulanKaryawan($tahun, $bulan, $karyawan);
                            $totalHariKerja = $absen->getTotalHariKerjaBulanan($tahun, $bulan);
                            echo "<td width='10' class='center'>$totalHariKerja</td>";
                            echo "<td width='10' class='center'>$totalCutiTahunan</td>";
                            echo "<td width='10' class='center'>$totalCutiSakit</td>";
                            echo "<td width='10' class='center'>$totalCutiAlpha</td>";
                            echo "<td width='10' class='center'>$totalCutiSpecial</td>";
                            echo "<td width='10' class='center'>$totalCutiTugasKeluar</td>";
                            echo "<td width='10' class='center'>$totalHariKerjaKaryawan</td>";
                            ?>
                        </tr>
                        <?php
                        if (!($num % 5)) {
                        ?>
                            <tr>
                                <td colspan="<?php echo $col; ?>" style="height: 15px;"></td>
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
                        <td colspan="<?php echo $col; ?>" style="height: 15px;"></td>
                    </tr>
                    <tr>
                        <td colspan="<?php echo $col; ?>"><strong>MMS</strong></td>
                    </tr>
                    <?php
                    foreach ($query as $data) {
                        $karyawan = $data->IDKaryawan;
                    ?>
                        <tr>
                            <td><?php echo $num; ?></td>
                            <td><?php echo $data->Nama; ?></td>
                            <?php
                            for ($i = 26; $i <= cal_days_in_month(CAL_GREGORIAN, $bulan2, $tahun2); $i++) {
                                $tanggal = $tahun2 . "-" . $bulan2 . "-" . $i;
                                $dataAbsen = newQuery("get_row", "SELECT * FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='" . $karyawan . "' AND Tanggal='$tanggal'");

                                $status = "";
                                if ($dataAbsen) {
                                    if ($dataAbsen->TotalJamKerja > 0) {
                                        $status = "m";
                                    } else if ($dataAbsen->CutiTahunan > 0) {
                                        $status = "c";
                                    } else if ($dataAbsen->CutiSakit > 0) {
                                        $status = "s";
                                    } else if ($dataAbsen->CutiAlpha > 0) {
                                        $status = "a";
                                    } else if ($dataAbsen->CutiSpecial > 0) {
                                        $status = "k";
                                    }
                                }
                                echo "<td width='10' class='center'>$status</td>";
                            }
                            for ($i = 1; $i <= 25; $i++) {
                                $tanggal = $tahun . "-" . $bulan . "-" . $i;
                                $dataAbsen = newQuery("get_row", "SELECT * FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='" . $karyawan . "' AND Tanggal='$tanggal'");

                                $status = "";
                                if ($dataAbsen) {
                                    if ($dataAbsen->TotalJamKerja > 0) {
                                        $status = "m";
                                    } else if ($dataAbsen->CutiTahunan > 0) {
                                        $status = "c";
                                    } else if ($dataAbsen->CutiSakit > 0) {
                                        $status = "s";
                                    } else if ($dataAbsen->CutiAlpha > 0) {
                                        $status = "a";
                                    } else if ($dataAbsen->CutiSpecial > 0) {
                                        $status = "k";
                                    }
                                }
                                echo "<td width='10' class='center'>$status</td>";
                            }

                            $totalCutiTahunan = $absen->getTotalCutiTahunanBulanKaryawan($tahun, $bulan, $karyawan);
                            $totalCutiSakit = $absen->getTotalCutiSakitBulanKaryawan($tahun, $bulan, $karyawan);
                            $totalCutiSpecial = $absen->getTotalCutiSpecialBulanKaryawan($tahun, $bulan, $karyawan);
                            $totalCutiAlpha = $absen->getTotalAlphaBulanKaryawan($tahun, $bulan, $karyawan);
                            $totalCutiTugasKeluar = $absen->getTotalTugasBulanKaryawan($tahun, $bulan, $karyawan);
                            $totalHariKerjaKaryawan = $absen->getTotalHariKerjaBulanKaryawan($tahun, $bulan, $karyawan);
                            $totalHariKerja = $absen->getTotalHariKerjaBulanan($tahun, $bulan);
                            echo "<td width='10' class='center'>$totalHariKerja</td>";
                            echo "<td width='10' class='center'>$totalCutiTahunan</td>";
                            echo "<td width='10' class='center'>$totalCutiSakit</td>";
                            echo "<td width='10' class='center'>$totalCutiAlpha</td>";
                            echo "<td width='10' class='center'>$totalCutiSpecial</td>";
                            echo "<td width='10' class='center'>$totalCutiTugasKeluar</td>";
                            echo "<td width='10' class='center'>$totalHariKerjaKaryawan</td>";
                            ?>
                        </tr>
                        <?php
                        if (!($num % 5)) {
                        ?>
                            <tr>
                                <td colspan="<?php echo $col; ?>" style="height: 15px;"></td>
                            </tr>
                    <?php
                        }
                        $num++;
                    }
                }
            }

            if ($jenis == "" || $jenis == "1") {
                $query = newQuery("get_results", "SELECT * FROM tb_karyawan WHERE Status='1' AND IDKaryawan>1 AND StatusKaryawan='Harian' ORDER BY Nama ASC");
                if ($query) {
                    $num = 1;
                    ?>
                    <tr>
                        <td colspan="<?php echo $col; ?>" style="height: 15px;"></td>
                    </tr>
                    <tr>
                        <td colspan="<?php echo $col; ?>"><strong>Karyawan Harian</strong></td>
                    </tr>
                    <?php
                    foreach ($query as $data) {
                        $karyawan = $data->IDKaryawan;
                    ?>
                        <tr>
                            <td><?php echo $num; ?></td>
                            <td><?php echo $data->Nama; ?></td>
                            <?php
                            for ($i = 26; $i <= cal_days_in_month(CAL_GREGORIAN, $bulan2, $tahun2); $i++) {
                                $tanggal = $tahun2 . "-" . $bulan2 . "-" . $i;
                                $dataAbsen = newQuery("get_row", "SELECT * FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='" . $karyawan . "' AND Tanggal='$tanggal'");

                                $status = "";
                                if ($dataAbsen) {
                                    if ($dataAbsen->TotalJamKerja > 0) {
                                        $status = "m";
                                    } else if ($dataAbsen->CutiTahunan > 0) {
                                        $status = "c";
                                    } else if ($dataAbsen->CutiSakit > 0) {
                                        $status = "s";
                                    } else if ($dataAbsen->CutiAlpha > 0) {
                                        $status = "a";
                                    } else if ($dataAbsen->CutiSpecial > 0) {
                                        $status = "k";
                                    }
                                }
                                echo "<td width='10' class='center'>$status</td>";
                            }
                            for ($i = 1; $i <= 25; $i++) {
                                $tanggal = $tahun . "-" . $bulan . "-" . $i;
                                $dataAbsen = newQuery("get_row", "SELECT * FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='" . $karyawan . "' AND Tanggal='$tanggal'");

                                $status = "";
                                if ($dataAbsen) {
                                    if ($dataAbsen->TotalJamKerja > 0) {
                                        $status = "m";
                                    } else if ($dataAbsen->CutiTahunan > 0) {
                                        $status = "c";
                                    } else if ($dataAbsen->CutiSakit > 0) {
                                        $status = "s";
                                    } else if ($dataAbsen->CutiAlpha > 0) {
                                        $status = "a";
                                    } else if ($dataAbsen->CutiSpecial > 0) {
                                        $status = "k";
                                    }
                                }
                                echo "<td width='10' class='center'>$status</td>";
                            }

                            $totalCutiTahunan = $absen->getTotalCutiTahunanBulanKaryawan($tahun, $bulan, $karyawan);
                            $totalCutiSakit = $absen->getTotalCutiSakitBulanKaryawan($tahun, $bulan, $karyawan);
                            $totalCutiSpecial = $absen->getTotalCutiSpecialBulanKaryawan($tahun, $bulan, $karyawan);
                            $totalCutiAlpha = $absen->getTotalAlphaBulanKaryawan($tahun, $bulan, $karyawan);
                            $totalCutiTugasKeluar = $absen->getTotalTugasBulanKaryawan($tahun, $bulan, $karyawan);
                            $totalHariKerjaKaryawan = $absen->getTotalHariKerjaBulanKaryawan($tahun, $bulan, $karyawan);
                            $totalHariKerja = $absen->getTotalHariKerjaBulanan($tahun, $bulan);
                            echo "<td width='10' class='center'>$totalHariKerja</td>";
                            echo "<td width='10' class='center'>$totalCutiTahunan</td>";
                            echo "<td width='10' class='center'>$totalCutiSakit</td>";
                            echo "<td width='10' class='center'>$totalCutiAlpha</td>";
                            echo "<td width='10' class='center'>$totalCutiSpecial</td>";
                            echo "<td width='10' class='center'>$totalCutiTugasKeluar</td>";
                            echo "<td width='10' class='center'>$totalHariKerjaKaryawan</td>";
                            ?>
                        </tr>
                        <?php
                        if (!($num % 5)) {
                        ?>
                            <tr>
                                <td colspan="<?php echo $col; ?>" style="height: 15px;"></td>
                            </tr>
            <?php
                        }
                        $num++;
                    }
                }
            }
            ?>
        </tbody>
    </table>
    <p style="font-style: italic;"><b>Mark:</b> <b>HK:</b> Hari Kerja Satu Bulan, <b>C:</b> Cuti Tahunan, <b>S:</b> Sakit, <b>A:</b> Alpha, <b>K:</b> Cuti Special/Khusus, <b>TKK:</b> Tugas Keluar Kantor, <b>HKK:</b> Jumlah Hari Kerja Karyawan</p>
    <table class="asignment" style="margin-top: 10px;">
        <tr>
            <td class="center" width="60%"></td>
            <td class="center" width="40%">Mengetahui,<br /><br /><br /><br />( HRD )</td>
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