<?php
session_start();
include_once "../config/connection.php";
$id = $_GET['id'];
$bulan = array("01"=>"Januari","02"=>"Februari","03"=>"Maret","04"=>"April","05"=>"Mei","06"=>"Juni","07"=>"Juli","08"=>"Agustus","09"=>"September",10=>"Oktober","11"=>"November",12=>"Desember");
$periode = "Periode : ".$bulan[date("m")]." ".date("Y");
$dataMaster = $db->get_row("SELECT *,DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_penjualan WHERE IDPenjualan='$id' AND Tipe='2' ORDER BY IDPenjualan");
$pelanggan = $db->get_row("SELECT NamaPelanggan,KodePelanggan FROM tb_pelanggan WHERE IDPelanggan='".$dataMaster->IDPelanggan."'");
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
                <td width="30%" class="bottom">
                    <h1>CV. Solusi Pemanas Air Nusantara</h1>
                    Jl. Tukad Batanghari No. 42<br />
                    Denpasar 80225, Bali<br />
                    Phone. +62 823-2800-1818<br />
                    Email. mail.aristonbali@gmail.com<br />
                    User : <?php echo $_SESSION["name"]; ?>
                    </td>
                <td width="40%" class="center"><h1 class="underline">** PENJUALAN RETAIL **</h1>Tanggal Cetak : <?php echo date("d/m/Y"); ?></td>
                <td width="30%">
                    No. Penjualan : <?php echo $dataMaster->NoPenjualan; ?><br />
                    Tanggal : <?php echo $dataMaster->TanggalID; ?><br />
                    Kode Pelanggan : <?php echo $pelanggan->KodePelanggan; ?><br />
                    Pelanggan : <?php echo $pelanggan->NamaPelanggan; ?><br />
                </td>
            </tr>
        </table>
        <table class="tabelList2" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th width="30">No.</th>
                    <th colspan="2">Nama Barang</th>
                    <th width="130">Serial Number</th>
                    <th width="100">Harga</th>
                    <th width="20">Qty</th>
                    <th width="100">Sub Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $query = $db->get_results("SELECT * FROM tb_penjualan_detail WHERE NoPenjualan='".$dataMaster->NoPenjualan."' ORDER BY NoUrut ASC");
                    if($query){
                        $i=1;
                        $qty = 0;
                        $total = 0;
                        foreach($query as $data){
                            ?>
                            <tr class="border-bottom">
                                <td><?php echo $i; ?></td>
                                <td colspan="2"><?php echo $data->NamaBarang; ?></td>
                                <td><?php echo $data->SN; ?></td>
                                <td style="text-align: center;"><?php echo number_format($data->Harga); ?></td>
                                <td style="text-align: center;"><?php echo $data->Qty; ?></td>
                                <td style="text-align: right;"><?php echo number_format($data->SubTotal); ?></td>
                            </tr>
                            <?php
                            $i++;
                            $qty+=$data->Qty;
                            $total+=$data->SubTotal;
                        }
                    }
                ?>
                <tr>
                    <td colspan="4" rowspan="4"><strong>Keterangan :</strong><br /> <?php echo $datapo->Keterangan; ?></td>
                    <td align="right" width="100"><strong>Total :</strong></td>
                    <td style="text-align: center;"><?php echo $qty;?></td>
                    <td style="text-align: right;"><?php echo number_format($total);?></td>
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