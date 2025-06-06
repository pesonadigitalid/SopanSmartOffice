<?php
session_start();
include_once "../config/connection.php";
$bulan = array("01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", "09" => "September", 10 => "Oktober", "11" => "November", 12 => "Desember");

$datestart = antiSQLInjection($_GET['datestart']);
$expstart = explode("/", $datestart);
$datestartchange = $expstart[2] . "-" . $expstart[1] . "-" . $expstart[0];
$dateend = antiSQLInjection($_GET['dateend']);
$expend = explode("/", $dateend);
$dateendchange = $expend[2] . "-" . $expend[1] . "-" . $expend[0];

$filterstatus = antiSQLInjection($_GET['filterstatus']);
$spb = antiSQLInjection($_GET['spb']);
$jenis = antiSQLInjection($_GET['jenis']);
$fpelanggan = antiSQLInjection($_GET['pelanggan']);
$fmarketing = antiSQLInjection($_GET['marketing']);

if ($datestart != "" && $dateend != "") {
    $cond = "WHERE a.Tanggal BETWEEN '$datestartchange' AND '$dateendchange' ";
    $subtitle = "Periode. " . $datestart . " - " . $dateend;
} else if ($datestart != "") {
    $cond = "WHERE a.Tanggal='$datestartchange' ";
    $subtitle = "Periode. " . $datestart;
} else {
    $cond = "WHERE DATE_FORMAT(a.Tanggal,'%Y-%m') = '" . date("Y-m") . "' ";
    $subtitle = "Periode : " . $bulan[date("m")] . " " . date("Y");
}

if ($filterstatus == "1") {
    $cond .= " AND a.Sisa<='0'";
    $subtitle .= "; Status: Lunas";
} else if ($filterstatus == "2") {
    $cond .= " AND a.Sisa>'100'";
    $subtitle .= "; Status: Piutang";
}

if ($spb != "")
    $cond .= " AND a.IDPenjualan='$spb' ";

if ($jenis != "")
    $cond .= " AND a.IsPajak='$jenis' ";

if ($fpelanggan != "")
    $cond .= " AND b.IDPelanggan='$fpelanggan' ";

if ($fmarketing != "") {
    $cond .= ($fmarketing == "490" || $fmarketing == "491")
        ? " AND (b.CreatedBy='$fmarketing' OR b.CreatedBy='1') "
        : " AND b.CreatedBy='$fmarketing' ";
    $marketing = newQuery("get_var", "SELECT Nama FROM tb_karyawan WHERE IDKaryawan='$fmarketing'");
    $subtitle .= "<br>Marketing : " . $marketing;
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
                Jl. Tukad Batanghari No. 42
                Denpasar 80225, Bali<br />
                Phone. +62 823-2800-1818<br />
                Email. mail.aristonbali@gmail.com<br />
                User : <?php echo $_SESSION["name"]; ?>
            </td>
            <td width="50%" align="right" class="bottom">
                Tanggal Cetak : <?php echo date("d/m/Y"); ?>
            </td>
        </tr>
    </table>
    <div class="laporanTitle">
        <h1 class="underline">** LAPORAN REKAP INVOICE **</h1><?php echo $subtitle; ?>
    </div>
    <table class="tabelList2" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width:20px">No</th>
                <th style="width:80px">No Invoice</th>
                <th style="width:70px">Tanggal</th>
                <th style="width:80px">No.SPB</th>
                <th style="width:80px">Kepada</th>
                <th>Keterangan</th>
                <th style="width:100px">Nominal Invoice</th>
                <th style="width:100px">Terbayar</th>
                <th style="width:100px">Sisa</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = newQuery("get_results", "SELECT a.*, DATE_FORMAT(a.Tanggal, '%d/%m/%Y') AS TanggalID, DATE_FORMAT(a.JatuhTempo, '%d/%m/%Y') AS JatuhTempoID, c.NamaPelanggan FROM tb_penjualan_invoice a, tb_penjualan b, tb_pelanggan c $cond AND a.IDPenjualan=b.IDPenjualan AND b.IDPelanggan=c.IDPelanggan ORDER BY a.Tanggal ASC");
            if ($query) {
                $i = 1;
                $t_GrandTotal = 0;
                $t_Sisa = 0;
                foreach ($query as $data) {
            ?>
                    <tr>
                        <td style="text-align: center;"><?php echo $i; ?></td>
                        <td><?php echo $data->NoInvoice; ?></td>
                        <td><?php echo $data->TanggalID; ?></td>
                        <td><?php echo $data->NoPenjualan; ?></td>
                        <td style="text-transform: uppercase"><?php echo $data->NamaPelanggan; ?></td>
                        <td><?php echo $data->Keterangan; ?></td>
                        <td style="text-align: right;"><?php echo number_format($data->GrandTotal); ?></td>
                        <td style="text-align: right;"><?php echo number_format(($data->GrandTotal - $data->Sisa)); ?></td>
                        <td style="text-align: right;"><?php echo number_format(($data->Sisa)); ?></td>
                    </tr>
            <?php
                    $i++;
                    $t_GrandTotal += $data->GrandTotal;
                    $t_Sisa2 += $data->Sisa;
                    $t_Sisa += ($data->GrandTotal - $data->Sisa);
                }
            } else {
                echo "<tr><td colspan='8'>Tidak ada data yang dapat ditampilkan...</td></tr>";
            }
            ?>
            <tr>
                <td colspan="6" style="text-align: right;"><strong>Total Invoice:</strong></td>
                <td style="text-align: right;"><?php echo number_format($t_GrandTotal); ?></td>
                <td style="text-align: right;"><?php echo number_format($t_Sisa); ?></td>
                <td style="text-align: right;"><?php echo number_format($t_Sisa2); ?></td>
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