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

$subtitle = "";

if ($datestart != "" && $dateend != "") {
    $cond = "WHERE b.Tanggal BETWEEN '$datestartExp' AND '$dateendExp'";
    $subtitle .= " <strong>Periode :</strong> $datestart s/d $dateend";
} else if ($datestart != "") {
    $cond = "WHERE b.Tanggal='$datestartExp'";
    $subtitle .= " <strong>Periode :</strong> $datestart";
} else {
    $cond = "WHERE DATE_FORMAT(b.Tanggal,'%Y-%m') = '" . date("Y-m") . "'";
    $subtitle .= " <strong>Periode :</strong> ".date("m/Y");
}

if ($jenis_stok != "" && $jenis_stok != "undefined"){
    $cond .= " AND a.StokFrom='$jenis_stok'";
    $cond2 = " AND StokFrom='$jenis_stok'";
    if($jenis_stok == 1)
        $subtitle .= " <strong>(Hanya Pengiriman Stok Purchasing)</strong>";
    else
        $subtitle .= " <strong>(Hanya Pengiriman Stok Gudang)</strong> ";
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
        <table class="tabelList2" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th width="100">No.Pengiriman</th>
                    <th width="100">Tanggal</th>
                    <th>Proyek</th>
                    <th width="100">Grand Total</th>
                    <th width="80">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = newQuery("get_results","SELECT a.*, b.*, DATE_FORMAT(b.Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_pengiriman b, tb_pengiriman_detail a $cond AND a.NoPengiriman=b.NoPengiriman");
                if($query){
                    $grandtotal = 0;
                    foreach($query as $data){
                        $gTotal = newQuery("get_var","SELECT SUM(SubTotal) FROM tb_pengiriman_detail WHERE NoPengiriman='".$data->NoPengiriman."' $cond2");
                        if(!$gTotal) $gTotal = 0;
                        $grandtotal += $gTotal;
                        $proyek = newQuery("get_row","SELECT * FROM tb_proyek WHERE IDProyek='".$data->IDProyek."'");

                        ?>
                        <tr>
                            <td><strong><?php echo $data->NoPengiriman;?></strong></td>
                            <td style="text-align: center;"><?php echo $data->TanggalID;?></td>
                            <td><?php echo $proyek->KodeProyek."/".$proyek->Tahun."/".$proyek->NamaProyek;?></td>
                            <td style="text-align: right;"><?php echo number_format($gTotal,0);?></td>
                            <td style="text-align: center;"><?php echo $data->Status;?></td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <td colspan="3" style="text-align: right;"><strong>GRAND TOTAL PENGIRIMAN :</strong></td>
                        <td style="text-align: right;font-weight: bold;"><?php echo number_format($grandtotal,0);?></td>
                        <td style="text-align: right;font-weight: bold;"></td>
                    </tr>
                    <?php
                } else {
                    ?>
                    <tr><td colspan="5">Tidak ada data yang dapat ditampilkan.</td></tr>
                    <?php
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
            window.onload = function () { window.print(); }
        </script>
    </body>
</html>
</body>
</html>