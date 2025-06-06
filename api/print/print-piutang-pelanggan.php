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

$cond = "";
$cond2 = "";
if ($datestart != "" && $dateend != "") {
    $subtitle = "Periode. " . $datestart . " - " . $dateend;
    $cond = " AND (b.Tanggal BETWEEN '$datestartExp' AND '$dateendExp') ";
    $cond2 = " AND (Tanggal BETWEEN '$datestartExp' AND '$dateendExp') ";
} else {
    $subtitle = "";
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="author" content="Yogi Pratama - Pesona Creative - 085737654543" />

    <title>SOPAN Smart Office - Integrated System</title>

    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />
    <link rel="stylesheet" href="print-style.css" media="all" type="text/css" />
</head>

<body>
    <table>
        <tr>
            <td width="50%" class="bottom">
                <strong>CV. SOLUSI PEMANAS AIR NUSANTARA</strong><br />
                Jl. Tukad Batanghari No. 42<br />
                Denpasar 80225, Bali<br />
                Phone. +62 823-2800-1818<br />
                Email. mail.aristonbali@gmail.com
            </td>
            <td width="50%" align="right" class="bottom">
                Tanggal Cetak : <?php echo date("d/m/Y"); ?>
            </td>
        </tr>
    </table>
    <div class="laporanTitle">
        <h1 class="underline">** REKAP PIUTANG PELANGGAN **</h1><?php echo $subtitle; ?>
    </div>
    <table class="tabelList2" cellpadding="0" cellspacing="0" style="margin-bottom: 30px;">
        <thead>
            <tr>
                <th width="20">No.</th>
                <th width="40">Kode</th>
                <th width="200">Nama</th>
                <!-- <th width="200">SPB</th> -->
                <th>Invoice</th>
                <th width="80">Total Piutang</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query2 = newQuery("get_results", "SELECT a.*, b.* FROM tb_pelanggan a, tb_penjualan_invoice b, tb_penjualan c WHERE a.IDPelanggan=c.IDPelanggan AND b.IDPenjualan=c.IDPenjualan AND b.Sisa>0 AND c.DeletedDate IS NULL $cond GROUP BY KodePelanggan ORDER BY a.KodePelanggan ASC");
            if ($query2) {
                $i = 1;
                $grandTotal = 0;
                foreach ($query2 as $data) {
                    $TotalSisa = 0;
                    $query3 = newQuery("get_results", "SELECT * FROM tb_penjualan_invoice WHERE IDPenjualan IN (SELECT IDPenjualan FROM tb_penjualan WHERE IDPelanggan='$data->IDPelanggan' AND DeletedDate IS NULL) AND Sisa>0 $cond2 ORDER BY IDPenjualan");
            ?>
                    <tr>
                        <td style="text-align: center;"><strong><?php echo $i; ?></strong></td>
                        <td style="text-align: center;"><strong><?php echo $data->KodePelanggan; ?></strong></td>
                        <td><?php echo $data->NamaPelanggan; ?></td>
                        <td>
                            <?php
                            if ($query3) {
                                foreach ($query3 as $d) {
                                    echo $d->NoPenjualan . " - " . $d->NoInvoice . " (" . number_format($d->Sisa) . ")<br/>";
                                    $TotalSisa += $d->Sisa;
                                }
                            }
                            ?>
                        </td>
                        <td style="text-align: right;"><?php echo number_format($TotalSisa); ?></td>
                    </tr>
            <?php
                    $i++;
                    $grandTotal += $TotalSisa;
                }
            } else {
                echo "<td colspan='5'>Tidak ada data yang dapat ditampilkan...</td>";
            }
            ?>
            <tr>
                <td colspan="4" style="text-align: right;"><strong>Total :</strong></td>
                <td style="text-align: right;"><?php echo number_format($grandTotal, 0); ?></td>
            </tr>
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
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>
</body>

</html>