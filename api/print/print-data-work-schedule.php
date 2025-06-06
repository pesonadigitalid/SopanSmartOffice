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

$tipe = $_GET['tipe'];
$status = $_GET['status'];
$spb = $_GET['spb'];
$karyawan = $_GET['karyawan'];

$subtitle = "";

if ($datestart != "" && $dateend != "") {
    $cond = "AND Tanggal BETWEEN '$datestartExp' AND '$dateendExp'";
    $subtitle .= " <strong>Periode :</strong> $datestart s/d $dateend";
} else if ($datestart != "") {
    $cond = "AND Tanggal='$datestartExp'";
    $subtitle .= " <strong>Periode :</strong> $datestart";
} else {
    $cond = "AND DATE_FORMAT(Tanggal,'%Y-%m') = '" . date("Y-m") . "'";
    $subtitle .= " <strong>Periode :</strong> " . date("m/Y");
}

if ($tipe != "") {
    $cond .= " AND Tipe='$tipe'";
    $subtitle .= " <strong>Tipe :</strong> " . ($tipe == "1" ? "Pemasangan Unit Water Heater" : ($tipe == "2" ? "Service / Maintenance Unit Water Heater" : "Survey Unit Water Heater"));
}

if ($spb != "") {
    $cond .= " AND RefID='$spb'";
    $d = $db->get_row("SELECT * FROM tb_penjualan WHERE IDPenjualan='$spb'");
    $subtitle .= " <strong>SPB :</strong> " . $d->NoPenjualan;
}

if ($status != "") {
    $cond .= " AND Status='$status'";
}

if ($karyawan != "") {
    $cond .= " AND FIND_IN_SET('$karyawan', a.IDsKaryawan)";
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

    <title>Lintas Daya Smart Office - Smart office for smart people</title>

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
        <h1 class="underline">** REKAP WORK ORDER <?php echo $subjenis; ?>**</h1><?php echo $subtitle; ?>
    </div>
    <table class="tabelList2" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width:20px">No</th>
                <th style="width:150px">Work Order</th>
                <th style="width:150px">Tgl. Order</th>
                <th style="width:180px">Tipe</th>
                <th>SPB / Pelanggan</th>
                <th style="width:180px">Teknisi</th>
                <th style="width:150px">Tgl. Kunjungan</th>
                <th style="width:120px">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = $db->get_results("SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_work_schedule a WHERE a.NoWorkSchedule IS NOT NULL $cond ORDER BY a.IDWorkSchedule ASC");
            $i = 1;
            if ($query) {
                foreach ($query as $data) {
                    $status = $data->Status == "0" ? "In-Progress" : "Completed";
                    $tipe = $data->Tipe == "1" ? "Pemasangan Unit Water Heater" : ($data->Tipe == "2" ? "Service / Maintenance Unit Water Heater" : "Survey Unit Water Heater");
                    $pelanggan = $db->get_var("SELECT NamaPelanggan FROM tb_pelanggan WHERE IDPelanggan='" . $data->IDPelanggan . "'");
                    $spb = $db->get_var("SELECT NoPenjualan FROM tb_penjualan WHERE IDPenjualan='" . $data->RefID . "'");
                    $tglKunjungan = $db->get_var("SELECT DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_work_report WHERE IDWorkSchedule='" . $data->IDWorkSchedule . "'");
                    if (!$tglKunjungan) $tglKunjungan = "-";

                    $karyawan = $db->get_results("SELECT Nama FROM tb_karyawan WHERE IDKaryawan IN (" . $data->IDsKaryawan . ")");
                    $karyawans = trim(implode(", ", array_map(function ($k) {
                        return $k->Nama;
                    }, $karyawan)));
            ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $data->NoWorkSchedule; ?></td>
                        <td style="text-align: center;"><?php echo $data->TanggalID; ?></td>
                        <td><?php echo $tipe; ?></td>
                        <td><?php echo $spb . " / " . $pelanggan; ?></td>
                        <td><?php echo $karyawans; ?></td>
                        <td style="text-align: center;"><?php echo $tglKunjungan; ?></td>
                        <td style="text-align: center;"><?php echo $status; ?></td>
                    </tr>
            <?php
                    $i++;
                }
            } else {
                echo "<td colspan='8'>Tidak ada data yang dapat ditampilkan...</td>";
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