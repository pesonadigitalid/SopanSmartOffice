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

$proyek = $_GET['proyekId'];
$keterangan = $_GET['keterangan'];
$jenis = $_GET['jenis'];
$periode = $_GET['periode'];

$subtitle = "";

if ($datestart != "" && $dateend != "") {
    $cond = "WHERE Tanggal BETWEEN '$datestartExp' AND '$dateendExp'";
    $subtitle .= " <strong>Periode :</strong> $datestart s/d $dateend";
} else if ($datestart != "") {
    $cond = "WHERE Tanggal='$datestartExp'";
    $subtitle .= " <strong>Periode :</strong> $datestart";
} else {
    $cond = "WHERE DATE_FORMAT(Tanggal,'%Y-%m') = '" . date("Y-m") . "'";
    $subtitle .= " <strong>Periode :</strong> " . date("m/Y");
}

if ($proyek != "") {
    $cond .= " AND IDProyek='$proyek'";
    $d = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='$proyek'");
    $subtitle .= "<br/><strong>Proyek :</strong> " . $d->KodeProyek . "/" . $d->Tahun . " " . $d->NamaProyek;
}

if ($keterangan != "") {
    $cond .= " AND (NoPettyCash LIKE '%$keterangan%' OR Keterangan LIKE '%$keterangan%')";
}

if ($jenis != "") {
    $cond .= "AND Jenis='$jenis'";
}

if ($periode != "") {
    $cond .= "AND IDPettyCashPeriode='$periode'";
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
        <h1 class="underline">** REKAP DATA PETTY CASH <?php echo $subjenis; ?> **</h1><?php echo $subtitle; ?>
    </div>
    <table class="tabelList2" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th width="150">No. PC</th>
                <th width="60">Tanggal</th>
                <th width="80">Proyek</th>
                <th width="80">Periode</th>
                <th>Keterangan</th>
                <th width="100">Total Item</th>
                <th width="100">Grand Total</th>
                <th width="100">Status</th>
                <th width="100">Oleh</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = newQuery("get_results", "SELECT *,DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_petty_cash $cond  ORDER BY NoPettyCash ASC");
            if ($query) {
                $i = 1;
                $totalItem = 0;
                $totalQty = 0;
                $grandTotal = 0;
                foreach ($query as $data) {
                    $totalItem += $data->TotalItem;
                    $totalQty += $data->TotalQty;
                    $grandTotal += $data->GrandTotal;
                    $proyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='" . $data->IDProyek . "'");
                    $periode = $db->get_var("SELECT Nama FROM tb_petty_cash_periode WHERE IDPettyCashPeriode='" . $data->IDPettyCashPeriode . "'");
                    $user = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $data->CreatedBy . "'");

                    $status = "New";
                    if ($data->Status == '2') $status = "Approved";
            ?>
                    <tr>
                        <td style="text-align: center;"><strong><?php echo $data->NoPettyCash; ?></strong></td>
                        <td><?php echo $data->TanggalID; ?></td>
                        <td><?php if (!$proyek) echo "UMUM";
                            else echo $proyek->KodeProyek . "/" . $proyek->Tahun; ?></td>
                        <td><?php echo $periode; ?></td>
                        <td><?php echo $data->Keterangan; ?></td>
                        <td style="text-align: center;"><?php echo number_format($data->TotalItem); ?></td>
                        <td style="text-align: right;">Rp. <?php echo number_format($data->GrandTotal); ?></td>
                        <td><?php echo $status; ?></td>
                        <td><?php echo $user; ?></td>
                    </tr>
            <?php
                    $i++;
                }
            } else {
                echo "<td colspan='7'>Tidak ada data yang dapat ditampilkan...</td>";
            }
            ?>
            <tr>
                <td colspan="5" style="text-align: right;"><strong>Total :</strong></td>
                <td style="text-align: center;"><?php echo number_format($totalItem); ?></td>
                <td style="text-align: right;">Rp. <?php echo number_format($grandTotal); ?></td>
                <td></td>
                <td></td>
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