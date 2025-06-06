<?php
session_start();
include_once "../config/connection.php";
$bulanList = array("01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", "09" => "September", 10 => "Oktober", "11" => "November", 12 => "Desember");

$datestart = $fungsi->ENDate($_GET['datestart']);
$dateend = $fungsi->ENDate($_GET['dateend']);
$proyek = antiSQLInjection($_GET['proyek']);

$cond = "AND (PeriodeEnd BETWEEN '$datestart' AND '$dateend')";
if ($proyek != "") $cond .= " AND IDProyek='$proyek'";

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
        <h1 class="underline">** LAPORAN SLIP GAJI HARIAN **</h1><?php echo $subtitle; ?>
    </div>
    <table class="tabelList2" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th width="20">No.</th>
                <th width="100">No. Slip</th>
                <th width="80">Tanggal</th>
                <th>Nama</th>
                <th width="80">Jabatan</th>
                <th width="100">Proyek</th>
                <th width="120">Periode</th>
                <th width="100">Total Gaji</th>
                <th width="80">No. Rekening</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total = 0;
            $query = newQuery("get_results", "SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID, DATE_FORMAT(PeriodeStart,'%d/%m/%Y') AS PeriodeStartID, DATE_FORMAT(PeriodeEnd,'%d/%m/%Y') AS PeriodeEndID FROM tb_slip_gaji WHERE IDSlipGaji>0 $cond AND Harian='1' ORDER BY IDSlipGaji");
            $i = 0;
            if ($query) {
                foreach ($query as $data) {
                    $dProyek = "KANTOR/MAINTENANCE";
                    $proyek = newQuery("get_row", "SELECT * FROM tb_proyek WHERE IDProyek='$data->IDProyek'");
                    if ($proyek) {
                        $dProyek = $proyek->KodeProyek . "/" . $proyek->Tahun . "/" . $proyek->NamaProyek;
                    }

                    $noRek = "-";
                    if ($karyawan->NamaBank1 != "" && $karyawan->NoRekening1 != "") {
                        $noRek = $karyawan->NamaBank1 . " / " . $karyawan->NoRekening1;
                    } else if ($karyawan->NamaBank2 != "" && $karyawan->NoRekening2 != "") {
                        $noRek = $karyawan->NamaBank2 . " / " . $karyawan->NoRekening2;
                    }

                    $karyawan = newQuery("get_row", "SELECT * FROM tb_karyawan WHERE IDKaryawan='" . $data->IDKaryawan . "'");
                    $jabatan = newQuery("get_var", "SELECT Jabatan FROM tb_jabatan WHERE IDJabatan='" . $karyawan->IDJabatan . "'");

                    $total += $data->TotalGaji;
                    $i++;
            ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $data->NoSlip; ?></td>
                        <td><?php echo $data->TanggalID; ?></td>
                        <td><?php echo $karyawan->Nama; ?></td>
                        <td><?php echo $jabatan; ?></td>
                        <td><?php echo $dProyek; ?></td>
                        <td><?php echo $data->PeriodeStartID . " - " . $data->PeriodeEndID; ?></td>
                        <td style="text-align: right;"><?php echo number_format($data->TotalGaji, 2); ?></td>
                        <td><?php echo $noRek; ?></td>
                    </tr>
            <?php
                }
            } else {
                echo "<tr><td colspan='9'>Tidak ada data yang dapat ditampilkan...</td></tr>";
            }
            ?>
            <tr>
                <td colspan="7" style="text-align: right;"><strong>Total Gaji:</strong></td>
                <td style="text-align: right;"><?php echo number_format($total, 2); ?></td>
                <td></td>
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