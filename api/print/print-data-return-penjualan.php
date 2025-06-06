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

$supplier = $_GET['supplier'];

if ($datestart != "" && $dateend != "") {
    $cond = "WHERE Tanggal BETWEEN '$datestartExp' AND '$dateendExp'";
} else if ($datestart != "") {
    $cond = "WHERE Tanggal='$datestartExp'";
} else {
    $cond = "WHERE DATE_FORMAT(Tanggal,'%Y-%m') = '" . date("Y-m") . "'";
}

if($supplier != "")
    $cond .= " AND IDSupplier='$supplier'";
    
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
                    Jl. Tukad Batanghari No. 42<br />
                    Denpasar 80225, Bali<br />
                    Phone. +62 823-2800-1818<br />
                    Email. mail.aristonbali@gmail.com<br />
                    User : <?php echo $_SESSION["name"]; ?>
                    </td>
                <td width="50%" align="right" class="bottom">
                    Tanggal Cetak : <?php echo date("d/m/Y"); ?>
                </td>
            </tr>
        </table>
        <div class="laporanTitle">
            <h1 class="underline">** LAPORAN DATA RETURN PELANGGAN **</h1><?php echo $subtitle; ?>
        </div>
        <table class="tabelList2" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th width="150">No. Return Pelanggan</th>
                    <th>Pelanggan</th>
                    <th width="120">Total Item</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = newQuery("get_results","SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_return_penjualan $cond ORDER BY IDReturn ASC");
                if($query){
                    $i=1;
                    $totalItem = 0;
                    foreach($query as $data){
                        $npelanggan = $db->get_var("SELECT NamaPelanggan FROM tb_pelanggan WHERE IDPelanggan='".$data->IDPelanggan."'");
                        $gtotalItem = $db->get_var("SELECT SUM(Qty) FROM tb_return_penjualan_detail WHERE NoReturn='".$data->NoReturn."'");
                        ?>
                        <tr>
                            <td style="text-align: center;"><strong><?php echo $data->NoReturn;?></strong></td>
                            <td><?php echo $npelanggan;?></td>
                            <td style="text-align: center;"><?php echo $gtotalItem;?></td>
                        </tr>
                        <?php
                        $i++;
                        $totalItem += $gtotalItem;
                    }
                } else {
                    echo "<td colspan='3'>Tidak ada data yang dapat ditampilkan...</td>";
                }
                ?>
                <tr>
                    <td colspan="2" style="text-align: right;"><strong>Total :</strong></td>
                    <td style="text-align: center;"><?php echo $totalItem;?></td>
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
            window.onload = function () { window.print(); }
        </script>
    </body>
</html>
</body>
</html>