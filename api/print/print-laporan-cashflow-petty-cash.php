<?php
session_start();
include_once "../config/connection.php";
$bulan = array("01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", "09" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember");

$datestart = $_GET['datestart'];
$expstart = explode("/", $datestart);
$datestartExp = $expstart[2] . "-" . $expstart[1] . "-" . $expstart[0];
$dateend = $_GET['dateend'];
$expend = explode("/", $dateend);
$dateendExp = $expend[2] . "-" . $expend[1] . "-" . $expend[0];

if ($datestart != "" && $dateend != "") {
    $cond = " AND (a.Tanggal BETWEEN '$datestartExp' AND '$dateendExp') ";
    $cond2 = " AND (Tanggal BETWEEN '$datestartExp' AND '$dateendExp') ";
    $subtitle = "Periode. " . $datestart . " - " . $dateend;
} else if ($datestart != "") {
    $cond = " AND a.Tanggal='$datestartExp' ";
    $cond2 = " AND Tanggal='$datestartExp' ";
    $subtitle = "Periode. " . $datestart;
} else {
    $cond = " AND DATE_FORMAT(a.Tanggal,'%Y-%m') = '" . date("Y-m") . "' ";
    $cond2 = " AND DATE_FORMAT(Tanggal,'%Y-%m') = '" . date("Y-m") . "' ";
    $subtitle = "Periode : " . $bulan[date("m")] . " " . date("Y");
}


$id_periode = $_GET['id_periode'];
$periode = newQuery("get_row", "SELECT * FROM tb_petty_cash_periode WHERE IDPettyCashPeriode='$id_periode'");
if ($periode) {
    $cond = " AND IDPettyCashPeriode='$id_periode' ";
    $cond2 = " AND IDPettyCashPeriode='$id_periode' ";
    $subtitle = "Periode : " . $periode->Tahun . " / " . $periode->Kode . " / " . $periode->Nama;
}

// $IDProyek = antiSQLInjection($_GET['IDProyek']);
$IDProyek = $periode->IDProyek;
$dProyek = newQuery("get_row", "SELECT * FROM tb_proyek WHERE IDProyek='$IDProyek'");
?>
<!DOCTYPE HTML>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="author" content="Yogi Pratama - Pesona Creative - 085737654543" />

    <title>SOPAN Smart Office - Smart office for smart people</title>

    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />
    <link rel="stylesheet" href="print-style.css" media="all" type="text/css" />
    <style media="all">
        table {
            width: 100%;
            border: none;
            border-collapse: collapse;
        }

        .tabelList2 th {
            background: #eee;
            border: solid 1px #ccc;
            padding: 5px;
            font-size: 9px;
        }

        .tabelList2 td {
            padding: 5px;
            border-bottom: dashed 1px #ccc;
            font-size: 8px;
        }
    </style>
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
        <h1 class="underline" style="text-transform: uppercase;">** LAPORAN CASH FLOW PETTY CASH**</h1>
        Proyek: <?php echo $dProyek->KodeProyek; ?>/<?php echo $dProyek->Tahun; ?> <?php echo $dProyek->NamaProyek; ?><br />
        <?php echo $subtitle; ?>
    </div>
    <table class="tabelList6" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th width="30">No.</th>
                <th width="140">No. Petty Cash</th>
                <th width="60">Tanggal</th>
                <th>Keterangan</th>
                <th width="100">Debet</th>
                <th width="100">Kredit</th>
                <th width="100">Saldo</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $saldo = 0;
            $i = 0;
            // $debet = newQuery("get_var", "SELECT SUM(a.Debet) FROM  tb_jurnal_detail a, tb_jurnal b WHERE a.IDJurnal=b.IDJurnal AND a.IDRekening='244' AND a.Debet>0 AND b.IDProyek='" . $IDProyek . "' AND a.Tanggal<'$datestart'");
            // if (!$debet) $debet = 0;

            // $kredit = newQuery("get_var", "SELECT SUM(GrandTotal) FROM tb_petty_cash WHERE IDProyek='" . $IDProyek . "' AND Tanggal<'$datestart' AND Status='2'");
            // if (!$kredit) $kredit = 0;

            // $saldo = $debet - $kredit;
            $saldo = $periode->Jumlah;
            ?>
            <tr>
                <td colspan="4" style="text-align: right;"><strong>Saldo Awal: </strong></td>
                <td></td>
                <td></td>
                <td style="text-align: right;"><?php echo number_format($saldo); ?></td>
            </tr>
            <?php
            // $query = newQuery("get_results", "SELECT * FROM (SELECT DATE_FORMAT(a.Tanggal, '%d/%m/%Y') AS Tanggal, a.Keterangan, a.Debet, a.Kredit FROM tb_jurnal_detail a, tb_jurnal b WHERE a.IDJurnal=b.IDJurnal AND a.IDRekening='244' AND a.Debet>0 AND b.IDProyek='" . $IDProyek . "' $cond UNION SELECT  DATE_FORMAT(Tanggal, '%d/%m/%Y') AS Tanggal, Keterangan, 0 AS Debet , GrandTotal AS Kredit FROM tb_petty_cash WHERE IDProyek='" . $IDProyek . "'  $cond2  AND Status='2') a ORDER BY Tanggal ASC");
            // $query = newQuery("get_results", "SELECT * FROM (SELECT DATE_FORMAT(a.Tanggal, '%d/%m/%Y') AS Tanggal, a.Keterangan, a.Debet, a.Kredit FROM tb_jurnal_detail a, tb_jurnal b WHERE a.IDJurnal=b.IDJurnal AND a.IDRekening='244' AND a.Debet>0 AND b.IDProyek='" . $IDProyek . "' $cond UNION SELECT  DATE_FORMAT(Tanggal, '%d/%m/%Y') AS Tanggal, Keterangan, 0 AS Debet , GrandTotal AS Kredit FROM tb_petty_cash WHERE IDProyek='" . $IDProyek . "'  $cond2  AND Status='2') a ORDER BY Tanggal ASC");
            $query = newQuery("get_results", "SELECT DATE_FORMAT(Tanggal, '%d/%m/%Y') AS Tanggal, Keterangan, 0 AS Debet , GrandTotal AS Kredit, Status, NoPettyCash FROM tb_petty_cash WHERE IDProyek='" . $IDProyek . "'  $cond2  ORDER BY Tanggal ASC");
            if ($query) {
                foreach ($query as $data) {
                    $i++;
                    $saldo = $saldo + $data->Debet - $data->Kredit;

                    $status = "";
                    if ($data->Status == "1") $status = " - <strong class='red'>NOT APPROVED</strong>";
            ?>
                    <tr>
                        <td><?php echo  $i; ?></td>
                        <td><?php echo $data->NoPettyCash; ?></td>
                        <td><?php echo $data->Tanggal; ?></td>
                        <td><?php echo $data->Keterangan . $status; ?></td>
                        <td style="text-align: right;"><?php echo number_format($data->Debet); ?></td>
                        <td style="text-align: right;"><?php echo number_format($data->Kredit); ?></td>
                        <td style="text-align: right;"><?php echo number_format($saldo); ?></td>
                    </tr>
            <?php
                }
            }
            ?>
            <tr>
                <td colspan="4" style="text-align: right;"><strong>Saldo Akhir: </strong></td>
                <td></td>
                <td></td>
                <td style="text-align: right;"><?php echo number_format($saldo); ?></td>
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
        setTimeout(function() {
            window.print();
        }, 1500);
    </script>
</body>

</html>