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

$supplier = $_GET['supplier'];
$nopo = $_GET['nopo'];

$subtitle = "";

if ($datestart != "" && $dateend != "") {
    $cond = "WHERE a.Tanggal BETWEEN '$datestartExp' AND '$dateendExp'";
    $subtitle .= " <strong>Periode :</strong> $datestart s/d $dateend";
} else if ($datestart != "") {
    $cond = "WHERE a.Tanggal='$datestartExp'";
    $subtitle .= " <strong>Periode :</strong> $datestart";
} else {
    $cond = "WHERE DATE_FORMAT(a.Tanggal,'%Y-%m') = '" . date("Y-m") . "'";
    $subtitle .= " <strong>Periode :</strong> " . date("m/Y");
}

if ($supplier != "") {
    $cond .= " AND b.IDSupplier='$supplier'";
    $d = $db->get_row("SELECT * FROM tb_supplier WHERE IDSupplier='$supplier'");
    $subtitle .= " <strong>Supplier :</strong> " . $d->NamaPerusahaan;
}

if ($nopo != "") {
    $cond .= " AND a.NoPO='$nopo'";
    $subtitle .= " <strong>No. PO :</strong> " . $nopo;
}

$periode = "Periode : " . $bulan[date("m")] . " " . date("Y");
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
        <h1 class="underline">** REKAP FAKTUR PAJAK PO <?php echo $subjenis; ?>**</h1><?php echo $subtitle; ?>
    </div>
    <table class="tabelList2" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width:20px">No</th>
                <th style="width:140px">No. PO</th>
                <th style="width:120px">No. Faktur</th>
                <th style="width:80px">Tanggal</th>
                <th style="width:140px">Supplier</th>
                <th>Keterangan</th>
                <th style="width:80px">Nilai</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = newQuery("get_results", "SELECT a.*, DATE_FORMAT(a.`Tanggal`, '%d/%m/%Y') AS TanggalID, b.IDPO, b.NoPO, c.NamaPerusahaan FROM tb_po_faktur_pajak a, tb_po b, tb_supplier c $cond AND a.`IDPO`=b.`IDPO` AND b.`IDSupplier`=c.`IDSupplier` AND b.IsLD='1' ORDER BY Tanggal ASC");
            if ($query) {
                $i = 1;
                $totalNilai = 0;
                foreach ($query as $data) {
            ?>
                    <tr>
                        <td style="text-align: center;"><?php echo $i; ?></td>
                        <td><?php echo $data->NoPO; ?></td>
                        <td><?php echo $data->NoFaktur; ?></td>
                        <td><?php echo $data->TanggalID; ?></td>
                        <td><?php echo $data->NamaPerusahaan; ?></td>
                        <td><?php echo $data->Keterangan; ?></td>
                        <td style="text-align: right;"><?php echo number_format($data->Nilai, 2); ?></td>
                    </tr>
            <?php
                    $i++;
                    $totalNilai += $data->Nilai;
                }
            } else {
                echo "<td colspan='7'>Tidak ada data yang dapat ditampilkan...</td>";
            }
            ?>

            <tr>
                <td colspan="6" style="text-align: right;"><strong>Total :</strong></td>
                <td style="text-align: right;"><?php echo number_format($totalNilai, 2); ?></td>
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