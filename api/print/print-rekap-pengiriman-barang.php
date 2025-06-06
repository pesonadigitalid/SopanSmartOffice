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

$jenis_stok = $_GET['jenis_stok'];
$id_proyek = $_GET['id_proyek'];

$subtitle = "";

if ($datestart != "" && $dateend != "") {
    $cond = " Tanggal BETWEEN '$datestartExp' AND '$dateendExp'";
    $subtitle .= " <strong>Periode :</strong> $datestart s/d $dateend";
} else if ($datestart != "") {
    $cond = " Tanggal='$datestartExp'";
    $subtitle .= " <strong>Periode :</strong> $datestart";
} else {
    $cond = " DATE_FORMAT(Tanggal,'%Y-%m') = '" . date("Y-m") . "'";
    $subtitle .= " <strong>Periode :</strong> ".date("m/Y");
}

if ($jenis_stok != "" && $jenis_stok != "undefined"){
    // $cond4 .= " AND a.StokFrom='$jenis_stok'";
    $cond2 = " AND StokFrom='$jenis_stok'";
    if($jenis_stok == 1)
        $subtitle .= " <strong>(Hanya Pengiriman Stok Purchasing)</strong>";
    else
        $subtitle .= " <strong>(Hanya Pengiriman Stok Gudang)</strong> ";
}

if($id_proyek>0){
    $cond3 = "WHERE IDProyek='$id_proyek'";
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
            <h1 class="underline">** LAPORAN PENGIRIMAN BARANG PROYEK **</h1><?php echo $subtitle; ?>
        </div>
        <?php
        $qProyek = newQuery("get_results","SELECT * FROM tb_proyek $cond3");
        if($qProyek){
            foreach($qProyek as $dProyek){
                $i = 0;
                $numKar = newQuery("get_var","SELECT COUNT(*) FROM tb_pengiriman WHERE $cond AND IDProyek='".$dProyek->IDProyek."'");
                if($numKar>0){
                    $totalNilai = 0;
                    $totalQty = 0;
                    ?>
                    <table class="tabelList6" cellpadding="0" cellspacing="0">
                        <thead>
                            <tr>
                                <th colspan="6" style="text-align: left">PROYEK : <?php echo $dProyek->KodeProyek; ?>/<?php echo $dProyek->Tahun; ?> - <?php echo $dProyek->NamaProyek; ?></th>
                            </tr>
                            <tr>
                                <th width="5">No.</th>
                                <th width="80">Kode Barang</th>
                                <th>Barang</th>
                                <th width="50">Qty</th>
                                <th width="80">Satuan</th>
                                <th width="100">Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = newQuery("get_results","SELECT *, SUM(Qty) AS TotalQty, SUM(Qty*Harga) AS TotalSub FROM tb_pengiriman_detail WHERE NoPengiriman IN (SELECT NoPengiriman FROM tb_pengiriman WHERE $cond AND IDProyek='".$dProyek->IDProyek."') $cond2 GROUP BY IDBarang ORDER BY NamaBarang");
                            if($query){
                                foreach($query as $data){
                                    if($data->TotalQty>0){
                                        $i++;
                                        $dBarang = newQuery("get_row","SELECT * FROM tb_barang WHERE IDBarang='".$data->IDBarang."'");
                                        $status = newQuery("get_var","SELECT Nama FROM tb_satuan WHERE IDSatuan='".$dBarang->IDSatuan."'");
                                        $totalQty += $data->TotalQty;
                                        $totalNilai += $data->TotalSub;
                                        ?>
                                        <tr>
                                            <td><?php echo $i; ?></td>
                                            <td style="text-align: center"><?php echo $dBarang->KodeBarang; ?></td>
                                            <td><?php echo $dBarang->Nama; ?></td>
                                            <td style="text-align: right"><?php echo number_format($data->TotalQty,2); ?></td>
                                            <td><?php echo $status; ?></td>
                                            <td style="text-align: right">Rp. <?php echo number_format($data->TotalSub,2); ?></td>
                                        </tr>
                                        <?php
                                    }
                                }
                            } else {
                                echo "<tr><td colspan='7'>Tidak ada pengiriman untuk proyek ini...</td></tr>";
                            }
                            ?>
                        </tbody>
                        <tfooter>
                            <tr>
                                <th colspan="3" style="text-align: right;">Total Pengiriman: </th>
                                <th style="text-align: right"><?php echo number_format($totalQty,2); ?></th>
                                <th style="text-align: right">-</th>
                                <th style="text-align: right">Rp. <?php echo number_format($totalNilai,2); ?></th>
                            </tr>
                        </tfooter>
                    </table><br/>
                    <?php
                }
            }
        }
        ?>
        <table class="asignment" style="margin-top: 20px;">
            <tr>
                <td class="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td class="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td class="center">Mengetahui,<br /><br /><br /><br /><br /><br />(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
            </tr>
        </table>
        <script type="text/javascript">
            window.onload = function () { window.print(); }
        </script>
    </body>
</html>
</body>
</html>