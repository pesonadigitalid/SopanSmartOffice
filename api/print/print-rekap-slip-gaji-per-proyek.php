<?php
session_start();
include_once "../config/connection.php";
$bulan = array("01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", "09" => "September", 10 => "Oktober", "11" => "November", 12 => "Desember");

$datestart = $fungsi->ENDate($_GET['datestart']);
$dateend = $fungsi->ENDate($_GET['dateend']);

$cond = "AND (a.PeriodeEnd BETWEEN '$datestart' AND '$dateend')";
$cond2 = "AND (PeriodeEnd BETWEEN '$datestart' AND '$dateend')";
$subtitle = "Periode: " . $fungsi->IDDate($datestart) . " - " . $fungsi->IDDate($dateend);
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
        <h1 class="underline">** LAPORAN REKAP GAJI HARIAN PER PROYEK **</h1><?php echo $subtitle; ?>
    </div>
    <table class="tabelList2" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th width="20">No.</th>
                <th width="100">Tahun</th>
                <th width="80">Kode Proyek</th>
                <th>Nama</th>
                <th width="100">Total Gaji</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total = 0;
            $i = 1;
            $TotalGaji = newQuery("get_var", "SELECT SUM(TotalGaji) FROM tb_slip_gaji WHERE IDSlipGaji>0 $cond2 AND IDProyek='0'");
            if (!$TotalGaji) $TotalGaji = 0;
            $total += $TotalGaji;
            if ($TotalGaji > 0) {
            ?>
                <tr>
                    <td style="text-align: center;"><?php echo $i; ?></td>
                    <td style="text-align: center;">-</td>
                    <td style="text-align: center;">-</td>
                    <td>UMUM/KANTOR/MAINTENANCE</td>
                    <td style="text-align: right;"><?php echo number_format($TotalGaji, 2); ?></td>
                </tr>
                <?php
            }

            $query = newQuery("get_results", "SELECT a.IDProyek, b.KodeProyek, b.Tahun, b.NamaProyek  FROM tb_slip_gaji a, tb_proyek b WHERE a.IDProyek=b.IDProyek $cond AND a.Harian='1' GROUP BY a.IDProyek ORDER BY b.Tahun, b.KodeProyek");
            if ($query) {
                foreach ($query as $data) {
                    $TotalGaji = newQuery("get_var", "SELECT SUM(TotalGaji) FROM tb_slip_gaji WHERE IDSlipGaji>0 $cond2 AND IDProyek='$data->IDProyek'");
                    if (!$TotalGaji) $TotalGaji = 0;
                    $total += $TotalGaji;
                    $i++;
                ?>
                    <tr>
                        <td style="text-align: center;"><?php echo $i; ?></td>
                        <td style="text-align: center;"><?php echo $data->Tahun; ?></td>
                        <td style="text-align: center;"><?php echo $data->KodeProyek; ?></td>
                        <td><?php echo $data->NamaProyek; ?></td>
                        <td style="text-align: right;"><?php echo number_format($TotalGaji, 2); ?></td>
                    </tr>
            <?php
                }
            } else if ($total == 0) {
                echo "<tr><td colspan='5'>Tidak ada data yang dapat ditampilkan...</td></tr>";
            }
            ?>
            <tr>
                <td colspan="4" style="text-align: right;"><strong>Total Gaji:</strong></td>
                <td style="text-align: right;"><?php echo number_format($total, 2); ?></td>
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
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>
</body>

</html>