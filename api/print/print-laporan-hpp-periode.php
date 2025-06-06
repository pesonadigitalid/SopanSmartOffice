<?php
session_start();
include_once "../config/connection.php";

$datestart = $_GET['datestart'];
$expstart = explode("/", $datestart);
$datestartExp = $expstart[2] . "-" . $expstart[1] . "-" . $expstart[0];

$dateend = $_GET['dateend'];
$expend = explode("/", $dateend);
$dateendExp = $expend[2] . "-" . $expend[1] . "-" . $expend[0];

if ($datestart != "" && $dateend != "") {
    $subtitle = "Periode. " . $datestart . " - " . $dateend;
} else {
    die("Terjadi kesalahan.");
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
        <h1 class="underline">** LAPORAN HPP PER-PERIODE **</h1><?php echo $subtitle; ?>
    </div>
    <table class="tabelList2" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th width="10">NO</th>
                <th width="60">TANGGAL</th>
                <th>KETERANGAN</th>
                <th width="150">TOTAL</th>
                <th width="150">SALDO</th>
            </tr>
        </thead>
        <tbody>
            <?php

            $saldoAwalAudit = newQuery("get_var", "SELECT SUM(a.Qty*c.Harga) 
                    FROM tb_audit_detail_log a, tb_audit b, tb_stok_gudang c
                    WHERE a.IDAudit=b.IDAudit AND a.IDStok=c.IDStokGudang
                    AND a.IDBarang IN (SELECT IDBarang FROM tb_barang WHERE IsBarang<>'2')
                    AND a.IDBarang NOT IN (SELECT DISTINCT(IDParent) FROM tb_barang_child)
                    AND b.Tanggal<'$datestartExp'
                    AND b.DeletedDate IS NULL;");
            if (!$saldoAwalAudit) $saldoAwalAudit = 0;


            $saldoAwalPenerimaan = newQuery("get_var", "SELECT SUM(a.Stok*a.Harga) 
                    FROM tb_stok_gudang a, tb_penerimaan_stok b
                    WHERE a.IDPenerimaan=b.IDPenerimaan
                    AND a.IDBarang IN (SELECT IDBarang FROM tb_barang WHERE IsBarang<>'2')
                    AND a.IDBarang NOT IN (SELECT DISTINCT(IDParent) FROM tb_barang_child)
                    AND b.NoPO IN (SELECT NoPO FROM tb_po)
                    AND b.Tanggal<'$datestartExp'
                    AND b.DeletedDate IS NULL;");
            if (!$saldoAwalPenerimaan) $saldoAwalPenerimaan = 0;


            $saldoAwalPengiriman = newQuery("get_var", "SELECT SUM(a.Qty*c.Harga) 
                    FROM tb_penjualan_surat_jalan_detail a, tb_penjualan_surat_jalan b, tb_stok_gudang c
                    WHERE a.NoSuratJalan=b.NoSuratJalan AND a.IDStok=c.IDStokGudang
                    AND a.IDBarang IN (SELECT IDBarang FROM tb_barang WHERE IsBarang<>'2')
                    AND a.IDBarang NOT IN (SELECT DISTINCT(IDParent) FROM tb_barang_child)
                    AND b.Tanggal<'$datestartExp'
                    AND b.DeletedDate IS NULL;");
            if (!$saldoAwalPengiriman) $saldoAwalPengiriman = 0;

            $saldoAwal = ($saldoAwalAudit + $saldoAwalPenerimaan) - $saldoAwalPengiriman;
            $saldoAkhir = $saldoAwal;
            ?>
            <tr>
                <td></td>
                <td><?php echo $datestart; ?></td>
                <td>SALDO AWAL</td>
                <td></td>
                <td style="text-align: right;"><?php echo number_format($saldoAwal, 2); ?></td>
            </tr>
            <?php

            $query = newQuery("get_results", "SELECT * FROM
            (
            (SELECT '1' AS Tipe, IDAudit AS ID, NoAudit AS NoRef, '' AS NoRef2, Tanggal, GrandTotal FROM tb_audit WHERE Tanggal BETWEEN '$datestartExp' AND '$dateendExp')
            UNION
            (SELECT '2' AS Tipe, a.IDPenerimaan AS ID, a.NoPenerimaanBarang AS NoRef, a.NoPO AS NoRef2, a.Tanggal, a.TotalHPP AS GrandTotal 
            FROM tb_penerimaan_stok a, tb_po b WHERE a.NoPO=b.NoPO AND a.DeletedDate IS NULL AND a.Tanggal BETWEEN '$datestartExp' AND '$dateendExp')
            UNION
            (SELECT '3' AS Tipe, IDSuratJalan AS ID, NoSuratJalan AS NoRef, NoPenjualan AS NoRef2, Tanggal, TotalHPPReal AS GrandTotal 
            FROM tb_penjualan_surat_jalan WHERE DeletedDate IS NULL AND Tanggal BETWEEN '$datestartExp' AND '$dateendExp')
            ) AS new_table
            ORDER BY Tanggal ASC");
            if ($query) {
                $i = 0;
                foreach ($query as $data) {
                    $i++;

                    $exp = explode("-", $data->Tanggal);
                    $TanggalID = $exp[2] . "/" . $exp[1] . "/" . $exp[0];

                    $keterangan = "";
                    $grandTotal = $data->GrandTotal;
                    if ($data->Tipe == "1") {
                        $keterangan = "AUDIT " . $data->NoRef;
                        $grandTotal = newQuery("get_var", "SELECT SUM(a.Qty*c.Harga) 
                        FROM tb_audit_detail_log a, tb_audit b, tb_stok_gudang c
                        WHERE a.IDAudit=b.IDAudit AND a.IDStok=c.IDStokGudang
                        AND a.IDBarang IN (SELECT IDBarang FROM tb_barang WHERE IsBarang<>'2')
                        AND a.IDBarang NOT IN (SELECT DISTINCT(IDParent) FROM tb_barang_child)
                        AND b.IDAudit='" . $data->ID . "';");
                        if (!$grandTotal) $grandTotal = 0;
                    } else if ($data->Tipe == "2") {
                        $supp = newQuery("get_var", "SELECT a.NamaPerusahaan FROM tb_supplier a, tb_po b WHERE a.IDSupplier=b.IDSupplier AND b.NoPO='" . $data->NoRef2 . "'");
                        $keterangan = "PENERIMAAN STOK - " . $data->NoRef . " - PO " . $data->NoRef2 . " - " . $supp;
                        $grandTotal = newQuery("get_var", "SELECT SUM(a.Stok*a.Harga) 
                        FROM tb_stok_gudang a, tb_penerimaan_stok b
                        WHERE a.IDPenerimaan=b.IDPenerimaan
                        AND a.IDBarang IN (SELECT IDBarang FROM tb_barang WHERE IsBarang<>'2')
                        AND a.IDBarang NOT IN (SELECT DISTINCT(IDParent) FROM tb_barang_child)
                        AND b.NoPO IN (SELECT NoPO FROM tb_po)
                        AND b.IDPenerimaan='" . $data->ID . "';");
                        if (!$grandTotal) $grandTotal = 0;
                    } else if ($data->Tipe == "3") {
                        $supp = newQuery("get_var", "SELECT a.NamaPelanggan FROM tb_pelanggan a, tb_penjualan b WHERE a.IDPelanggan=b.IDPelanggan AND b.NoPenjualan='" . $data->NoRef2 . "'");
                        $keterangan = "SURAT JALAN - " . $data->NoRef . ". - SPB " . $data->NoRef2 . " - " . $supp;
                        $grandTotal = newQuery("get_var", "SELECT SUM(a.Qty*c.Harga) 
                        FROM tb_penjualan_surat_jalan_detail a, tb_penjualan_surat_jalan b, tb_stok_gudang c
                        WHERE a.NoSuratJalan=b.NoSuratJalan AND a.IDStok=c.IDStokGudang
                        AND a.IDBarang IN (SELECT IDBarang FROM tb_barang WHERE IsBarang<>'2')
                        AND a.IDBarang NOT IN (SELECT DISTINCT(IDParent) FROM tb_barang_child)
                        AND b.IDSuratJalan='" . $data->ID . "';");
                        if (!$grandTotal) $grandTotal = 0;
                        $grandTotal = -1 * abs($data->GrandTotal);
                    }

                    $saldoAkhir += $grandTotal;

            ?>
                    <tr>
                        <td style="text-align: center;"><?php echo $i; ?></td>
                        <td style="text-align: center;"><?php echo $TanggalID; ?></td>
                        <td><?php echo $keterangan; ?></td>
                        <td style="text-align: right;"><?php echo number_format($grandTotal, 2); ?></td>
                        <td style="text-align: right;"><?php echo number_format($saldoAkhir, 2); ?></td>
                    </tr>
            <?php
                }
            }
            ?>
            <tr>
                <td></td>
                <td><?php echo $dateend; ?></td>
                <td>SALDO AKHIR</td>
                <td></td>
                <td style="text-align: right;"><?php echo number_format($saldoAkhir, 2); ?></td>
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