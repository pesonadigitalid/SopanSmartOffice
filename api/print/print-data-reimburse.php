<?php
session_start();
include_once "../config/connection.php";
$bulan = array("01"=>"Januari","02"=>"Februari","03"=>"Maret","04"=>"April","05"=>"Mei","06"=>"Juni","07"=>"Juli","08"=>"Agustus","09"=>"September",10=>"Oktober","11"=>"November",12=>"Desember");

$datestart = $_GET['datestart'];
$expstart = explode("/",$datestart);
$datestartExp = $expstart[2]."-".$expstart[1]."-".$expstart[0];

$dateend = $_GET['dateend'];
$expend = explode("/",$dateend);
$dateendExp = $expend[2]."-".$expend[1]."-".$expend[0];

$jenis = $_GET['jenis'];
$stts_reimburse = $_GET['stts_reimburse'];

if ($datestart != "" && $dateend != "") {
    $cond = " AND a.Tanggal BETWEEN '$datestartExp' AND '$dateendExp'";
    $subtitle = "Periode. ".$datestart." - ".$dateend;
} else if ($datestart != "") {
    $cond = " AND a.Tanggal='$datestartExp'";
    $subtitle = "Periode. ".$datestart;
} else {
    $cond = " AND DATE_FORMAT(a.Tanggal,'%Y-%m') = '" . date("Y-m") . "'";
    $subtitle = "Periode : ".$bulan[date("m")]." ".date("Y");
}

if($jenis!="") $cond .= " AND a.Kategori='$jenis'";

if($stts_reimburse!="") $cond .= " AND a.Status='$stts_reimburse'";

$periode = "Periode : ".$bulan[date("m")]." ".date("Y");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <meta name="description" content=""/>
        <meta name="author" content="Yogi Pratama - Pesona Creative - 085737654543"/>
        
        <title>SOPAN Smart Office - Smart office for smart people</title>
        
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport"/>
        <link rel="stylesheet" href="print-style.css" media="all" type="text/css"/>
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
            <h1 class="underline">** LAPORAN DATA REIMBURSE **</h1><?php echo $subtitle; ?>
        </div>
        <table class="tabelList2" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>No. Reimburse</th>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>No. Kendaraan</th>
                    <th>Karyawan</th>
                    <th>Status</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = newQuery("get_results","SELECT a.NoReimburse, DATE_FORMAT(a.Tanggal, '%d/%m/%Y') AS TanggalID, a.Kategori, a.NoKendaraan, a.Status, a.TotalNilai, c.Nama FROM tb_reimburse a, tb_karyawan c WHERE a.IDKaryawan=c.IDKaryawan $cond ORDER BY a.`NoReimburse`");
                if($query){
                    $i=0;
                    $total = 0;
                    foreach($query as $data){
                        $i++;
                        $total += $data->TotalNilai;
                        if($data->Status=="0"){
                            $status = "Pending";
                        } else if($data->Status=="1"){
                            $status = "Disetujui HRD";
                        } else {
                            $status = "Terbayar oleh Finance";
                        }
                        ?>
                        <tr>
                            <td><?php echo $data->NoReimburse; ?></td>
                            <td><?php echo $data->TanggalID; ?></td>
                            <td><?php echo $data->Kategori; ?></td>
                            <td><?php echo $data->NoKendaraan; ?></td>
                            <td><?php echo $data->Nama; ?></td>
                            <td><?php echo $status; ?></td>
                            <td style="text-align: right;">Rp. <?php echo number_format($data->TotalNilai,2); ?></td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='7'>Tidak ada data yang dapat ditampilkan...</td></tr>";
                }
                ?>
                <tr>
                    <td colspan="6" style="text-align: right;"><strong>Total Reimburse :</strong></td>
                    <td style="text-align: right;">Rp. <?php echo number_format($total,2);?></td>
                </tr>
            </tbody>
        </table>
        <table class="asignment" style="margin-top: 20px;">
            <tr>
                <td class="center" width="60%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td class="center" width="40%">Mengetahui,<br /><br /><br /><br />( HRD )</td>
            </tr>
        </table>
        <script type="text/javascript">
            window.onload = function () { window.print(); }
        </script>
    </body>
</html>
</body>
</html>