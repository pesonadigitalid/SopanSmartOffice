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

$stts_reimburse = $_GET['stts_reimburse'];
if($stts_reimburse!=""){
    $cond2 = "AND Status='$stts_reimburse'";
} else $cond2 = "";

if ($datestart != "" && $dateend != "") {
    $cond = "AND Tanggal BETWEEN '$datestartExp' AND '$dateendExp'";
    $subtitle = "Periode. ".$datestart." - ".$dateend;
} else if ($datestart != "") {
    $cond = "AND Tanggal='$datestartExp'";
    $subtitle = "Periode. ".$datestart;
} else {
    $cond = "AND DATE_FORMAT(Tanggal,'%Y-%m') = '" . date("Y-m") . "'";
    $subtitle = "Periode : ".$bulan[date("m")]." ".date("Y");
}
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
            <h1 class="underline">** LAPORAN REIMBURSE KENDARAAN **</h1><?php echo $subtitle; ?>
        </div>
        <table class="tabelList2" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th width="70">Kode Asset</th>
                    <th width="90">No. Kendaraan</th>
                    <th>Nama Kendaraan</th>
                    <th width="100">BBM</th>
                    <th width="100">Service</th>
                    <th width="100">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $bbm = 0;
                $service = 0;
                $total = 0;
                $query = newQuery("get_results","SELECT * FROM tb_asset WHERE Jenis='Kendaraan' ORDER BY KodeAsset ASC");
                if($query){
                    foreach($query as $data){
                        $sumBBM = newQuery("get_var","SELECT SUM(TotalNilai) FROM tb_reimburse WHERE NoKendaraan='".$data->NoKendaraan."' $cond2 AND Kategori='Reimburse BBM' $cond");
                        $sumService = newQuery("get_var","SELECT SUM(TotalNilai) FROM tb_reimburse WHERE NoKendaraan='".$data->NoKendaraan."' $cond2 AND Kategori='Reimburse Service' $cond");
                        $sumJumlah = $sumBBM+$sumService;
                        $bbm += $sumBBM;
                        $service += $sumService;
                        $total += $sumJumlah;
                        ?>
                        <tr>
                            <td><?php echo $data->KodeAsset; ?></td>
                            <td><?php echo $data->NoKendaraan; ?></td>
                            <td><?php echo $data->Nama; ?></td>
                            <td style="text-align: right;"><?php echo number_format($sumBBM,2); ?></td>
                            <td style="text-align: right;"><?php echo number_format($sumService,2); ?></td>
                            <td style="text-align: right;"><?php echo number_format($sumJumlah,2); ?></td>
                        </tr>
                        <?php
                    }
                } else {
                    $nodata = 1;
                }

                $query = newQuery("get_results","SELECT DISTINCT(NoKendaraan) FROM tb_reimburse WHERE NoKendaraan IS NOT NULL AND NoKendaraan NOT IN (SELECT NoKendaraan FROM tb_asset WHERE NoKendaraan IS NOT NULL) $cond");
                if($query){
                    foreach($query as $data){
                        $sumBBM = newQuery("get_var","SELECT SUM(TotalNilai) FROM tb_reimburse WHERE NoKendaraan='".$data->NoKendaraan."' $cond2 AND (Kategori='Reimburse BBM Non-Asset' OR Kategori='Reimburse BBM') $cond");
                        $sumService = newQuery("get_var","SELECT SUM(TotalNilai) FROM tb_reimburse WHERE NoKendaraan='".$data->NoKendaraan."' $cond2 AND (Kategori='Reimburse Service Non-Asset' OR Kategori='Reimburse Service') $cond");
                        $sumJumlah = $sumBBM+$sumService;
                        $bbm += $sumBBM;
                        $service += $sumService;
                        $total += $sumJumlah;
                        ?>
                        <tr>
                            <td><?php echo "NON/ASSET"; ?></td>
                            <td><?php echo $data->NoKendaraan; ?></td>
                            <td><?php echo "NON/ASSET"; ?></td>
                            <td style="text-align: right;"><?php echo number_format($sumBBM,2); ?></td>
                            <td style="text-align: right;"><?php echo number_format($sumService,2); ?></td>
                            <td style="text-align: right;"><?php echo number_format($sumJumlah,2); ?></td>
                        </tr>
                        <?php
                    }
                } else {
                    $nodata2 = 1;
                }


                if($nodata == 1 && $nodata2 == 1) {
                    echo "<tr><td colspan='6'>Tidak ada data yang dapat ditampilkan...</td></tr>";
                }
                ?>
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>Total Reimburse:</strong></td>
                    <td style="text-align: right;"><?php echo number_format($bbm,2);?></td>
                    <td style="text-align: right;"><?php echo number_format($service,2);?></td>
                    <td style="text-align: right;"><?php echo number_format($total,2);?></td>
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