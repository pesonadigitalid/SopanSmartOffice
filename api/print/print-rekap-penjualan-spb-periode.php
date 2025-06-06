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

if ($datestart != "" && $dateend != "") {
    $cond = "Tanggal BETWEEN '$datestartExp' AND '$dateendExp'";
    $subtitle = "Periode. " . $datestart . " - " . $dateend;
} else if ($datestart != "") {
    $cond = "Tanggal='$datestartExp'";
    $subtitle = "Periode. " . $datestart;
} else {
    $cond = "DATE_FORMAT(Tanggal,'%Y-%m') = '" . date("Y-m") . "'";
    $subtitle = "Periode : " . $bulan[date("m")] . " " . date("Y");
}

if ($sales != "") {
    $cond .= " AND CreatedBy='$sales'";

    $karyawan = newQuery("get_var", "SELECT Nama FROM tb_karyawan WHERE IDKaryawan='$sales'");
    $subtitle .= "; Sales. " . $karyawan;
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
        <h1 class="underline">** LAPORAN PENJUALAN (SPB) **</h1><?php echo $subtitle; ?>
    </div>
    <table class="tabelList2" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th width="100">NO. SPB</th>
                <th width="100">JENIS</th>
                <th width="60">TANGGAL</th>
                <th width="100">SALES</th>
                <th width="150">PELANGGAN</th>
                <th width="60">TOTAL</th>
                <th width="60">DISC</th>
                <th width="60">PPN</th>
                <th width="80">GRAND TOTAL</th>
                <th width="80">B. MATERIAL</th>
                <th width="80">B. NON-MATERIAL</th>
                <th width="80">GROSS PROFIT</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = newQuery("get_results", "SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_penjualan WHERE $cond AND DeletedDate IS NULL ORDER BY IDPenjualan,Tanggal ASC");
            if ($query) {
                $i = 1;
                $totalNilai = 0;
                $PPN = 0;
                $grandTotal = 0;
                $diskon = 0;
                $TotalBiayaMaterial = 0;
                $TotalBiayaNonMaterial = 0;
                $TotalProfit = 0;
                foreach ($query as $data) {
                    $npelanggan = newQuery("get_var", "SELECT NamaPelanggan FROM tb_pelanggan WHERE IDPelanggan='" . $data->IDPelanggan . "'");
                    $karyawan = newQuery("get_var", "SELECT Nama FROM tb_karyawan WHERE IDKaryawan='$data->CreatedBy'");

                    $BiayaMaterial = 0;
                    $qp = newQuery("get_results", "SELECT * FROM tb_penjualan_surat_jalan WHERE IDPenjualan='" . $data->IDPenjualan . "' ORDER BY IDSuratJalan DESC");
                    if ($qp) {
                        foreach ($qp as $dp) {
                            $TotalHPPDetail = 0;
                            $q = newQuery("get_results", "SELECT * FROM tb_penjualan_surat_jalan_detail WHERE NoSuratJalan='" . $dp->NoSuratJalan . "'");
                            if ($q) {
                                foreach ($q as $d) {
                                    if ($d->IsInstallasi) {
                                        $HPPAudit = newQuery("get_var", "SELECT SUM(GrandTotal) FROM tb_audit WHERE IDPenjualan=" . $data->IDPenjualan . "'");
                                        if (!$HPPAudit) $HPPAudit = 0;
                                        $HPPAudit = abs($HPPAudit);
                                        $TotalHPPDetail += $HPPAudit;
                                    } else {
                                        $TotalHPPDetail += $d->SubTotalHPP;
                                    }
                                }
                            }
                            $BiayaMaterial += $TotalHPPDetail;
                        }
                    }

                    $BiayaNonMaterial = 0;
                    $qp = newQuery("get_results", "SELECT a.*, d.NamaPerusahaan, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_po a, tb_po_detail b, tb_barang c, tb_supplier d WHERE a.NoPO=b.NoPO AND b.IDBarang=c.IDBarang AND a.IDSupplier=d.IDSupplier AND c.IsBarang='2' AND a.IDPenjualan='" . $data->IDPenjualan . "' GROUP BY NoPO ORDER BY d.NamaPerusahaan");
                    if ($qp) {
                        foreach ($qp as $dp) {
                            $BiayaNonMaterial += $dp->GrandTotal;
                        }
                    }

                    $GrossProfit = $data->GrandTotal - $BiayaMaterial - $BiayaNonMaterial;
            ?>
                    <tr>
                        <td><?php echo $data->NoPenjualan; ?></td>
                        <td><?php echo $data->Jenis; ?></td>
                        <td><?php echo $data->TanggalID; ?></td>
                        <td><?php echo $karyawan; ?></td>
                        <td><?php echo $npelanggan; ?></td>
                        <td style="text-align: right;"><?php echo number_format($data->Total, 2); ?></td>
                        <td style="text-align: right;"><?php echo number_format($data->Diskon, 2); ?></td>
                        <td style="text-align: right;"><?php echo number_format($data->PPN, 2); ?></td>
                        <td style="text-align: right;"><?php echo number_format($data->GrandTotal, 2); ?></td>
                        <td style="text-align: right;"><?php echo number_format($BiayaMaterial, 2); ?></td>
                        <td style="text-align: right;"><?php echo number_format($BiayaNonMaterial, 2); ?></td>
                        <td style="text-align: right;"><?php echo number_format($GrossProfit, 2); ?></td>
                    </tr>
            <?php
                    $i++;
                    $totalNilai += $data->Total;
                    $PPN += $data->PPN;
                    $grandTotal += $data->GrandTotal;
                    $diskon += $data->Diskon;
                    $TotalBiayaMaterial += $BiayaMaterial;
                    $TotalBiayaNonMaterial += $BiayaNonMaterial;
                    $TotalProfit += $GrossProfit;
                }
            }
            ?>
            <tr class="highlight2">
                <td colspan='5' style="text-align: right;"><strong>TOTAL NILAI PENJUALAN : </strong></td>
                <td style="text-align: right;"><?php echo number_format($totalNilai, 2); ?></td>
                <td style="text-align: right;"><?php echo number_format($diskon, 2); ?></td>
                <td style="text-align: right;"><?php echo number_format($PPN, 2); ?></td>
                <td style="text-align: right;"><?php echo number_format($grandTotal, 2); ?></td>
                <td style="text-align: right;"><?php echo number_format($TotalBiayaMaterial, 2); ?></td>
                <td style="text-align: right;"><?php echo number_format($TotalBiayaNonMaterial, 2); ?></td>
                <td style="text-align: right;"><?php echo number_format($TotalProfit, 2); ?></td>
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