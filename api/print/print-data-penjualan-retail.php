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

$sales = $_GET['sales'];
$pelanggan = $_GET['pelanggan'];

if ($datestart != "" && $dateend != "") {
    $cond = "AND a.Tanggal BETWEEN '$datestartExp' AND '$dateendExp'";
    $subtitle = "Periode. " . $datestart . " - " . $dateend;
} else if ($datestart != "") {
    $cond = "AND a.Tanggal='$datestartExp'";
    $subtitle = "Periode. " . $datestart;
} else {
    $cond = "AND DATE_FORMAT(a.Tanggal,'%Y-%m') = '" . date("Y-m") . "'";
    $subtitle = "Periode. " . $bulan[date("m")] . " " . date("Y");
}

if ($sales != "") {
    $cond .= " AND a.IDSales='$sales'";

    $karyawan = newQuery("get_var", "SELECT Nama FROM tb_karyawan WHERE IDKaryawan='$sales'");
    $subtitle .= "; Sales. " . $karyawan;
}

if ($pelanggan != "") {
    $cond .= " AND a.IDPelanggan='$pelanggan'";

    $karyawan = newQuery("get_var", "SELECT NamaPelanggan FROM tb_pelanggan WHERE IDPelanggan='$pelanggan'");
    $subtitle .= "; Pelanggan. " . $karyawan;
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
                Jl. Tukad Batanghari No. 42<br />
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
        <h1 class="underline">** LAPORAN DATA SPB **</h1><?php echo $subtitle; ?>
    </div>
    <table class="tabelList2" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th width="100">No. SPB</th>
                <th width="100">Jenis</th>
                <th>Sales</th>
                <th>Pelanggan</th>
                <th width="120">Total Item</th>
                <th width="100">Total Nilai</th>
                <th width="100">PPN</th>
                <th width="100">Grand Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = newQuery("get_results", "SELECT a.*, b.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID, a.Jenis AS JenisSPB FROM tb_penjualan a, tb_pelanggan b WHERE a.IDPelanggan=b.IDPelanggan AND a.Tipe='1' AND DeletedDate IS NULL $cond ORDER BY IDPenjualan ASC");
            if ($query) {
                $i = 1;
                $totalItem = 0;
                $totalNilai = 0;
                $PPN = 0;
                $grandTotal = 0;
                foreach ($query as $data) {
                    $npelanggan = $db->get_var("SELECT NamaPelanggan FROM tb_pelanggan WHERE IDPelanggan='" . $data->IDPelanggan . "'");
                    $karyawan = newQuery("get_var", "SELECT Nama FROM tb_karyawan WHERE IDKaryawan='$data->CreatedBy'");
            ?>
                    <tr>
                        <td><strong><?php echo $data->NoPenjualan; ?></strong></td>
                        <td style="text-align: center;"><?php echo $data->JenisSPB; ?></td>
                        <td><?php echo $karyawan; ?></td>
                        <td><?php echo $npelanggan; ?></td>
                        <td style="text-align: center;"><?php echo $data->TotalItem; ?></td>
                        <td style="text-align: right;"><?php echo number_format($data->Total2, 2); ?></td>
                        <td style="text-align: right;"><?php echo number_format($data->PPN, 2); ?></td>
                        <td style="text-align: right;"><?php echo number_format($data->GrandTotal, 2); ?></td>
                    </tr>
            <?php
                    $i++;
                    $totalItem += $data->TotalItem;
                    $totalNilai += $data->Total2;
                    $PPN += $data->PPN;
                    $grandTotal += $data->GrandTotal;
                }
            } else {
                echo "<td colspan='7'>Tidak ada data yang dapat ditampilkan...</td>";
            }
            ?>
            <tr>
                <td colspan="4" style="text-align: right;"><strong>Total :</strong></td>
                <td style="text-align: center;"><?php echo $totalItem; ?></td>
                <td style="text-align: right;"><?php echo number_format($totalNilai, 2); ?></td>
                <td style="text-align: right;"><?php echo number_format($PPN, 2); ?></td>
                <td style="text-align: right;"><?php echo number_format($grandTotal, 2); ?></td>
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