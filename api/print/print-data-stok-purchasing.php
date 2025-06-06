<?php
session_start();
include_once "../config/connection.php";
$bulan = array("01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", "09" => "September", 10 => "Oktober", "11" => "November", 12 => "Desember");

$periode = "Sampai dengan " . date("d/m/Y");

$jenis = $_GET['jenis'];
if ($jenis != "") $cond .= " AND a.IDJenis='$jenis'";
$gudang = $_GET['gudang'];
if ($gudang != "") {
    $cond .= " AND c.IDGudang='$gudang'";
    $namaGudang = newQuery("get_var", "SELECT Nama FROM tb_gudang WHERE IDGudang='$gudang'");
    $periode .= "<br/>STOK: " . $namaGudang;
}
$spb = $_GET['spb'];
if ($spb != "") {
    $cond .= " AND d.IDPenjualan='$spb'";
    $namaSPB = newQuery("get_var", "SELECT NoPenjualan FROM tb_penjualan WHERE IDPenjualan='$spb'");
    $periode .= "<br/>SPB: " . $namaSPB;
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
        <h1 class="underline">** LAPORAN STOK PURCHASING **</h1><?php echo $periode; ?>
    </div>
    <table class="tabelList2" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th width="30">No</th>
                <th width="70">Kode Barang</th>
                <th width="120">Nama Barang</th>
                <th width="100">Jenis</th>
                <th width="100">Stok</th>
                <th width="100">HPP</th>
                <th width="100">Total Nilai</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = newQuery("get_results", "SELECT a.*, b.Nama AS Jenis, c.*, SUM(c.SisaStok) AS TotalSisaStok FROM tb_barang a, tb_jenis_material b, tb_stok_purchasing c, tb_penjualan d WHERE a.IDJenis=b.IDMaterial AND a.IDBarang>0 AND a.IDBarang=c.IDBarang AND c.SisaStok>0 AND c.IDPenjualan=d.IDPenjualan $cond GROUP BY c.IDBarang ORDER BY b.Nama ASC, a.Nama ASC, a.IDBarang ASC");
            if ($query) {
                $i = 1;
                $grandTotal = 0;
                foreach ($query as $data) {
                    $subTotal = $data->TotalSisaStok * $data->HPP;
                    $grandTotal += $subTotal;
            ?>
                    <tr>
                        <td style="text-align: center;"><?php echo $i; ?></td>
                        <td style="text-align: center;"><?php echo $data->KodeBarang; ?></td>
                        <td><?php echo $data->Nama; ?></td>
                        <td><?php echo $data->Jenis; ?></td>
                        <td style="text-align: center;"><?php echo number_format($data->TotalSisaStok); ?></td>
                        <td style="text-align: right;"><?php echo number_format($data->HPP); ?></td>
                        <td style="text-align: right;"><?php echo number_format($subTotal); ?></td>
                    </tr>
                <?php
                    $i++;
                }
                ?>
                <tr>
                    <td colspan="6" style="text-align: right;font-weight: bold">Grand Total HPP: </td>
                    <td style="text-align: right;font-weight: bold"><?php echo number_format($grandTotal); ?></td>
                </tr>
            <?php
            } else {
                echo "<td colspan='7'>Tidak ada data yang dapat ditampilkan...</td>";
            }
            ?>
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