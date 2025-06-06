<?php
session_start();
include_once "../config/connection.php";

$proyek = $_GET['proyek'];
if ($proyek != "") {
    $cond = " AND a.IDProyek='$proyek'";
    $p = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='$proyek'");
    $periode = "Proyek : " . $p->KodeProyek . "/" . $p->Tahun . " " . $p->NamaProyek;
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
        <h1 class="underline">** OUTSTANDING PENGIRIMAN PO PROYEK**</h1><?php echo $periode; ?>
    </div>
    <table class="tabelList2" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width:20px">No</th>
                <th style="width:100px">No. PO</th>
                <th style="width:80px">Tanggal</th>
                <th>Proyek</th>
                <th style="width:100px">Qty</th>
                <th style="width:100px">Terkirim</th>
                <th style="width:100px">Sisa</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = newQuery("get_results", "SELECT a.NoPO, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID, e.Stok, e.SisaStok, f.KodeProyek, f.Tahun, f.NamaProyek FROM tb_po a, tb_po_detail b, tb_penerimaan_stok c, tb_penerimaan_stok_detail d, tb_stok_purchasing e, tb_proyek f
            WHERE a.NoPO=b.NoPO AND a.NoPO=c.NoPO AND c.NoPenerimaanBarang=d.NoPenerimaanBarang AND c.IDPenerimaan=e.IDPenerimaan AND d.IDBarang=e.IDBarang AND e.SisaStok>0 AND a.IDProyek=f.IDProyek $cond ORDER BY f.Tahun, f.KodeProyek");
            if ($query) {
                $i = 1;
                foreach ($query as $data) {
            ?>
                    <tr>
                        <td style="text-align: center;"><?php echo $i ?></td>
                        <td style="text-align: center;"><strong><?php echo $data->NoPO; ?></strong></td>
                        <td><?php echo $data->TanggalID; ?></td>
                        <td><?php echo $data->KodeProyek . "/" . $data->Tahun . " " . $data->NamaProyek; ?></td>
                        <td style="text-align: center"><?php echo $data->Stok; ?></td>
                        <td style="text-align: center"><?php echo $data->Stok - $data->SisaStok; ?></td>
                        <td style="text-align: center"><?php echo $data->SisaStok; ?></td>
                    </tr>
            <?php
                    $i++;
                }
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