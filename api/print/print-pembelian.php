<?php
session_start();
include_once "../config/connection.php";
$id = $_GET['id'];
$bulan = array("01"=>"Januari","02"=>"Februari","03"=>"Maret","04"=>"April","05"=>"Mei","06"=>"Juni","07"=>"Juli","08"=>"Agustus","09"=>"September",10=>"Oktober","11"=>"November",12=>"Desember");
$periode = "Periode : ".$bulan[date("m")]." ".date("Y");
$datapembelian = $db->get_row("SELECT *,DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_pembelian WHERE NoPembelian='$id' ORDER BY NoPembelian");
if($datapembelian->NoPO=="0" || $datapembelian->IDProyek=="0") {
    $proyek="UMUM";
    $nopo="UMUM";
} else {
    $proyek = $db->get_var("SELECT NamaProyek FROM tb_proyek WHERE IDProyek='".$datapembelian->IDProyek."'");
    $nopo=$datapembelian->NoPO;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <meta name="description" content=""/>
        <meta name="author" content="Yogi Pratama - Pesona Creative - 085737654543"/>
        
        <title>SOPAN Smart Office - Integrated System</title>
        
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport"/>
        <link rel="stylesheet" href="print-style.css" media="all" type="text/css"/>
    </head>
    <body>
        <table>
            <tr>
                <td width="30%" class="bottom">
                    <h1>CV. Solusi Pemanas Air Nusantara</h1>
                    Jl. Tukad Yeh Aya No.70b, Panjer, Denpasar Selatan, Kota Denpasar, Bali 80234<br />
                    Telp. (0361) 8497915, Fax. -<br />
                    User : <?php echo $_SESSION["name"]; ?>
                    </td>
                <td width="40%" class="center"><h1 class="underline">** INVOICE PEMBELIAN **</h1>Tanggal Cetak : <?php echo date("d/m/Y"); ?></td>
                <td width="30%">
                    No. Pembelian : <?php echo $datapembelian->NoPembelian; ?><br />
                    Tanggal Pembelian : <?php echo $datapembelian->TanggalID; ?><br />
                    No. PO : <?php echo $nopo; ?><br />
                    Proyek : <?php echo $proyek; ?><br />
                </td>
            </tr>
        </table>
        <table class="tabelList2" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th width="30">No.</th>
                    <th colspan="2">Nama Barang</th>
                    <th width="100">Harga</th>
                    <th width="20">Qty</th>
                    <th width="100">Sub Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $query = $db->get_results("SELECT * FROM tb_pembelian_detail WHERE NoPembelian='$id' ORDER BY NoUrut ASC");
                    if($query){
                        $i=1;
                        $qty = 0;
                        $total = 0;
                        foreach($query as $data){
                            ?>
                            <tr class="border-bottom">
                                <td><?php echo $i; ?></td>
                                <td colspan="2"><?php echo $data->NamaBarang; ?></td>
                                <td><?php echo number_format($data->Harga); ?></td>
                                <td><?php echo $data->Qty; ?></td>
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
                    <td colspan="3" rowspan="4"><strong>Keterangan :</strong><br /> <?php echo $datapo->Keterangan; ?></td>
                    <td align="right" width="100"><strong>Total :</strong></td>
                    <td><?php echo $qty;?></td>
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