<?php
session_start();

include_once "../config/connection.php";
$bulan = array("01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", "09" => "September", 10 => "Oktober", "11" => "November", 12 => "Desember");

$tipe = $_GET['tipe'];
if ($tipe == "1") $sub = "GUDANG";
else $sub = "PURCHASING";

$datestart = $_GET['datestart'];
$expstart = explode("/", $datestart);
$datestartExp = $expstart[2] . "-" . $expstart[1] . "-" . $expstart[0];

$dateend = $_GET['dateend'];
$expend = explode("/", $dateend);
$dateendExp = $expend[2] . "-" . $expend[1] . "-" . $expend[0];

if ($datestart != "" && $dateend != "") {
    $cond = "AND a.Tanggal BETWEEN '$datestartExp' AND '$dateendExp'";
    $subtitle = "Periode. " . $datestart . " - " . $dateend;
} else if ($datestart != "") {
    die('Silahkan lengkapi Periode Awal dan Akhir!');
} else {
    die('Silahkan lengkapi Periode Awal dan Akhir!');
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
        <h1 class="underline">** LAPORAN STOK BARANG <?php echo $sub; ?> **</h1><?php echo $subtitle; ?>
    </div>
    <?php if ($tipe == "1") { ?>
        <table class="tabelList2" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th width="30">No</th>
                    <th width="100">Kode Barang</th>
                    <th>Nama Barang</th>
                    <th width="130">Jenis</th>
                    <th width="80">Stok Awal</th>
                    <th width="80">Stok In</th>
                    <th width="80">Stok Out</th>
                    <th width="80">Stok Akhir</th>
                    <th width="100">Nilai</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $totalHPP = 0;
                $query = newQuery("get_results", "SELECT DISTINCT(a.IDBarang), b.Nama, b.KodeBarang, b.StokGudang, b.IDJenis, c.Nama AS NamaJenis FROM tb_kartu_stok_gudang a, tb_barang b, tb_jenis_material c WHERE a.IDBarang=b.IDBarang AND b.IDJenis=c.IDMaterial AND b.Kategori<>'4' AND a.Tanggal<'$dateendExp' AND a.StokAkhir IS NOT NULL ORDER BY c.Nama, b.Nama ASC");
                if ($query) {
                    $i = 1;
                    foreach ($query as $data) {
                        $jumlahStok = $db->get_var("SELECT SUM(SisaStok) FROM tb_stok_gudang WHERE IDBarang='" . $data->IDBarang . "'");
                        if (!$jumlahStok) $jumlahStok = 0;

                        if ($data->StokGudang != $jumlahStok) $style = "color:red;";
                        else $style = "";

                        $stokAwal = 0;
                        $HPPStokAwal = 0;
                        $qStokAwal = newQuery("get_results", "SELECT * FROM tb_kartu_stok_gudang WHERE IDBarang='" . $data->IDBarang . "' AND Tanggal<'$datestartExp' AND StokAkhir IS NOT NULL");
                        if ($qStokAwal) {
                            foreach ($qStokAwal as $dStokAwal) {
                                $stokAwal += $dStokAwal->StokPenyesuaian;
                                $exp = explode(" ", $dStokAwal->Keterangan);

                                if ($exp[0] == "Audit") {
                                    $Detail = newQuery("get_row", "SELECT * FROM tb_audit WHERE IDAudit='" . $dStokAwal->ID . "'");
                                    $HPPAudit = newQuery("get_var", "SELECT SubTotal FROM tb_audit_detail WHERE NoAudit='" . $Detail->NoAudit . "' AND IDBarang='" . $dStokAwal->IDBarang . "'");
                                    if (!$HPPAudit) $HPPAudit = 0;
                                    $HPPStokAwal += $HPPAudit;
                                } else if ($exp[0] == "Penerimaan") {
                                    $Detail = newQuery("get_row", "SELECT c.NoPenerimaanBarang , a.NoPO, b.NamaPerusahaan FROM tb_po a, tb_supplier b, tb_penerimaan_stok c WHERE a.`NoPO`=c.`NoPO` AND a.`IDSupplier`=b.`IDSupplier` AND c.`IDPenerimaan`='" . $dStokAwal->ID . "'");
                                    $HPPPenerimaan = newQuery("get_var", "SELECT (HPP*Qty) FROM tb_penerimaan_stok_detail WHERE NoPenerimaanBarang='" . $Detail->NoPenerimaanBarang . "' AND IDBarang='" . $dStokAwal->IDBarang . "'");
                                    if (!$HPPPenerimaan) $HPPPenerimaan = 0;
                                    $HPPStokAwal += $HPPPenerimaan;
                                } else if ($exp[0] == "Pengiriman") {
                                    $Detail = newQuery("get_row", "SELECT * FROM tb_pengiriman WHERE IDPengiriman='" . $dStokAwal->ID . "'");
                                    $HPPDO = newQuery("get_var", "SELECT SubTotal FROM tb_pengiriman_detail WHERE NoPengiriman='" . $Detail->NoPengiriman . "' AND IDBarang='" . $dStokAwal->IDBarang . "'");
                                    if (!$HPPDO) $HPPDO = 0;
                                    $HPPStokAwal -= $HPPDO;
                                }
                            }
                        }

                        $stokIn = 0;
                        $HPPStokIn = 0;
                        $qstokIn = newQuery("get_results", "SELECT * FROM tb_kartu_stok_gudang WHERE IDBarang='" . $data->IDBarang . "' AND Tanggal BETWEEN '$datestartExp' AND '$dateendExp' AND StokPenyesuaian>0 AND StokAkhir IS NOT NULL");
                        if ($qstokIn) {
                            foreach ($qstokIn as $dStokIn) {
                                $stokIn += $dStokIn->StokPenyesuaian;
                                $exp = explode(" ", $dStokIn->Keterangan);

                                if ($exp[0] == "Audit") {
                                    $Detail = newQuery("get_row", "SELECT * FROM tb_audit WHERE IDAudit='" . $dStokIn->ID . "'");
                                    $HPPAudit = newQuery("get_var", "SELECT SubTotal FROM tb_audit_detail WHERE NoAudit='" . $Detail->NoAudit . "' AND IDBarang='" . $dStokIn->IDBarang . "'");
                                    if (!$HPPAudit) $HPPAudit = 0;
                                    $HPPStokIn += $HPPAudit;
                                } else if ($exp[0] == "Penerimaan") {
                                    $Detail = newQuery("get_row", "SELECT c.NoPenerimaanBarang , a.NoPO, b.NamaPerusahaan FROM tb_po a, tb_supplier b, tb_penerimaan_stok c WHERE a.`NoPO`=c.`NoPO` AND a.`IDSupplier`=b.`IDSupplier` AND c.`IDPenerimaan`='" . $dStokIn->ID . "'");
                                    $HPPPenerimaan = newQuery("get_var", "SELECT (HPP*Qty) FROM tb_penerimaan_stok_detail WHERE NoPenerimaanBarang='" . $Detail->NoPenerimaanBarang . "' AND IDBarang='" . $dStokIn->IDBarang . "'");
                                    if (!$HPPPenerimaan) $HPPPenerimaan = 0;
                                    $HPPStokIn += $HPPPenerimaan;
                                } else if ($exp[0] == "Pengiriman") {
                                    $Detail = newQuery("get_row", "SELECT * FROM tb_pengiriman WHERE IDPengiriman='" . $dStokIn->ID . "'");
                                    $HPPDO = newQuery("get_var", "SELECT SubTotal FROM tb_pengiriman_detail WHERE NoPengiriman='" . $Detail->NoPengiriman . "' AND IDBarang='" . $dStokIn->IDBarang . "'");
                                    if (!$HPPDO) $HPPDO = 0;
                                    $HPPStokIn -= $HPPDO;
                                }
                            }
                        }

                        $stokOut = 0;
                        $HPPStokOut = 0;
                        $qstokOut = newQuery("get_results", "SELECT * FROM tb_kartu_stok_gudang WHERE IDBarang='" . $data->IDBarang . "' AND Tanggal BETWEEN '$datestartExp' AND '$dateendExp' AND StokPenyesuaian<0 AND StokAkhir IS NOT NULL");
                        if ($qstokOut) {
                            foreach ($qstokOut as $dStokOut) {
                                $stokOut += $dStokOut->StokPenyesuaian;
                                $exp = explode(" ", $dStokIn->Keterangan);

                                if ($exp[0] == "Audit") {
                                    $Detail = newQuery("get_row", "SELECT * FROM tb_audit WHERE IDAudit='" . $dStokOut->ID . "'");
                                    $HPPAudit = newQuery("get_var", "SELECT SubTotal FROM tb_audit_detail WHERE NoAudit='" . $Detail->NoAudit . "' AND IDBarang='" . $dStokOut->IDBarang . "'");
                                    if (!$HPPAudit) $HPPAudit = 0;
                                    $HPPStokOut += $HPPAudit;
                                } else if ($exp[0] == "Penerimaan") {
                                    $Detail = newQuery("get_row", "SELECT c.NoPenerimaanBarang , a.NoPO, b.NamaPerusahaan FROM tb_po a, tb_supplier b, tb_penerimaan_stok c WHERE a.`NoPO`=c.`NoPO` AND a.`IDSupplier`=b.`IDSupplier` AND c.`IDPenerimaan`='" . $dStokOut->ID . "'");
                                    $HPPPenerimaan = newQuery("get_var", "SELECT (HPP*Qty) FROM tb_penerimaan_stok_detail WHERE NoPenerimaanBarang='" . $Detail->NoPenerimaanBarang . "' AND IDBarang='" . $dStokOut->IDBarang . "'");
                                    if (!$HPPPenerimaan) $HPPPenerimaan = 0;
                                    $HPPStokOut += $HPPPenerimaan;
                                } else if ($exp[0] == "Pengiriman") {
                                    $Detail = newQuery("get_row", "SELECT * FROM tb_pengiriman WHERE IDPengiriman='" . $dStokOut->ID . "'");
                                    $HPPDO = newQuery("get_var", "SELECT SubTotal FROM tb_pengiriman_detail WHERE NoPengiriman='" . $Detail->NoPengiriman . "' AND IDBarang='" . $dStokOut->IDBarang . "'");
                                    if (!$HPPDO) $HPPDO = 0;
                                    $HPPStokOut -= $HPPDO;
                                }
                            }
                        }

                        $stokAkhir = $stokAwal + $stokIn + $stokOut;
                        $Nilai = $HPPStokAwal + $HPPStokIn + $HPPStokOut;
                        if ($Nilai < 0) $Nilai = 0;
                        if ($stokAkhir == 0) $Nilai = 0;

                        // if ($stokAkhir < 0 && $Nilai > 0) var_dump($data->Nama);

                        if ($Nilai > 0) {
                ?>
                            <tr>
                                <td style="text-align: center;"><?php echo $i; ?></td>
                                <td style="text-align: center;<?php echo $style; ?>"><?php echo $data->KodeBarang; ?></td>
                                <td><?php echo $data->Nama; ?></td>
                                <td><?php echo $data->NamaJenis; ?></td>
                                <td style="text-align: right;"><?php echo number_format($stokAwal, 0); ?></td>
                                <td style="text-align: right;"><?php echo number_format($stokIn, 0); ?></td>
                                <td style="text-align: right;"><?php echo number_format($stokOut, 0); ?></td>
                                <td style="text-align: right;"><?php echo number_format($stokAkhir, 0); ?></td>
                                <td style="text-align: right;"><?php echo number_format($Nilai, 2); ?></td>
                            </tr>
                    <?php
                        }
                        $totalHPP += $Nilai;
                        $i++;
                    }
                    ?>
                    <tr>
                        <td style="text-align: right;" colspan="8"><strong>Total Nilai Persediaan : </strong></td>
                        <td style="text-align: right;"><?php echo number_format($totalHPP, 2); ?></td>
                    </tr>
                <?php
                } else {
                    echo "<td colspan='6'>Tidak ada data yang dapat ditampilkan...</td>";
                }
                ?>
            </tbody>
        </table>
    <?php } else { ?>
        <table class="tabelList2" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th width="30">No</th>
                    <th width="70">Kode Barang</th>
                    <th width="120">Nama Barang</th>
                    <th width="100">Jenis</th>
                    <th width="100">Stok Purchasing</th>
                    <th width="100">Total Nilai</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = newQuery("get_results", "SELECT * FROM tb_barang WHERE IDBarang>0 AND Kategori<>'4' ORDER BY IDBarang ASC");
                if ($query) {
                    $i = 1;
                    foreach ($query as $data) {
                        if ($data->StokPurchasing > 0) {
                            $jenis = $db->get_var("SELECT Nama FROM tb_jenis_material WHERE IDMaterial='" . $data->IDJenis . "'");
                            $jumlahStok = $db->get_var("SELECT SUM(SisaStok) FROM tb_stok_purchasing WHERE IDBarang='" . $data->IDBarang . "'");
                            if (!$jumlahStok) $jumlahStok = 0;
                            $hpp = $db->get_var("SELECT SUM((Harga*SisaStok)) FROM tb_stok_purchasing WHERE IDBarang='" . $data->IDBarang . "' AND SisaStok>0");
                            if ($data->StokGudang != $jumlahStok) $style = "color:red;";
                            else $style = "";
                ?>
                            <tr>
                                <td style="text-align: center;"><?php echo $i; ?></td>
                                <td style="text-align: center;"><?php echo $data->KodeBarang; ?></td>
                                <td><?php echo $data->Nama; ?></td>
                                <td><?php echo $jenis; ?></td>
                                <td style="text-align: center;"><?php echo $data->StokPurchasing; ?></td>
                                <td style="text-align: right;"><?php echo number_format($hpp, 2); ?></td>
                            </tr>
                    <?php
                            $totalHPP += $hpp;
                            $i++;
                        }
                    }
                    ?>
                    <tr>
                        <td style="text-align: right;" colspan="5"><strong>Total Nilai Persediaan : </strong></td>
                        <td style="text-align: right;"><?php echo number_format($totalHPP, 2); ?></td>
                    </tr>
                <?php
                } else {
                    echo "<td colspan='6'>Tidak ada data yang dapat ditampilkan...</td>";
                }
                ?>
            </tbody>
        </table>
    <?php } ?>

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