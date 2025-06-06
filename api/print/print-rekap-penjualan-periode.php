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

if ($datestart != "" && $dateend != "") {
    $cond = "AND a.Tanggal BETWEEN '$datestartExp' AND '$dateendExp'";
    $subtitle = "Periode. ".$datestart." - ".$dateend;
} else if ($datestart != "") {
    $cond = "AND a.Tanggal='$datestartExp'";
    $subtitle = "Periode. ".$datestart;
} else {
    $cond = "AND DATE_FORMAT(a.Tanggal,'%Y-%m') = '" . date("Y-m") . "'";
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
            <h1 class="underline">** LAPORAN PENJUALAN **</h1><?php echo $subtitle; ?>
        </div>
        <table class="tabelList2" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th width="100">NO. INVOICE</th>
                    <th width="60">TANGGAL</th>
                    <th width="100">NO. SPB</th>
                    <th width="100">JENIS</th>
                    <th width="150">PELANGGAN</th>
                    <th>ITEM</th>
                    <th width="60">QTY</th>
                    <th width="80">SUB TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = newQuery("get_results","SELECT a.*, b.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID, b.Jenis AS JenisSPB FROM tb_penjualan_invoice a, tb_penjualan b WHERE a.IDPenjualan=b.IDPenjualan $cond ORDER BY IDInvoice ASC");
                if($query){
                    $i=1;
                    $totalItem = 0;
                    $totalTerkirim = 0;
                    $totalSisa = 0;
                    $totalNilai = 0;
                    $PPN = 0;
                    $grandTotal = 0;
                    $diskon = 0;
                    $sisa = 0;
                    $pembayaran = 0;
                    $totalQty = 0;
                    $totalTerbayar = 0;
                    $totalSisa = 0;
                    foreach($query as $data){
                        $totalQty = 0;
                        $npelanggan = newQuery("get_var","SELECT NamaPelanggan FROM tb_pelanggan WHERE IDPelanggan='".$data->IDPelanggan."'");
                        $row = newQuery("get_var","SELECT COUNT(*) FROM tb_penjualan_invoice_detail WHERE IDInvoice='".$data->IDInvoice."'");

                        $qDetail = newQuery("get_results","SELECT * FROM tb_penjualan_invoice_detail  WHERE IDInvoice='".$data->IDInvoice."'");
                        if($qDetail){
                            $j=0;
                            foreach($qDetail as $dDetail){
                                $totalQty += $dDetail->Qty;
                                $j++;
                                ?>
                                <tr>
                                    <td><?php if($j==1) echo $data->NoInvoice; ?></td>
                                    <td><?php if($j==1) echo $data->TanggalID; ?></td>
                                    <td><?php if($j==1) echo $data->NoPenjualan; ?></td>
                                    <td><?php if($j==1) echo $data->JenisSPB; ?></td>
                                    <td><?php if($j==1) echo $npelanggan; ?></td>
                                    <td><?php echo $dDetail->NamaBarangDisplay; ?></td>
                                    <td style="text-align: right;"><?php echo number_format($dDetail->Qty,2); ?></td>
                                    <td style="text-align: right;"><?php echo number_format($dDetail->SubTotal,2); ?></td>      
                                </tr>
                                <?php
                            }
                        }
                        $i++;
                        ?>
                        <tr class="highlight">
                            <td colspan='6' style="text-align: right;"><strong>Total : </strong></td>
                            <td style="text-align: right;"><?php echo number_format($totalQty,2);?></td>
                            <td style="text-align: right;"><?php echo number_format($data->Total,2);?></td>
                        </tr>
                        <tr class="highlight">
                            <td style="text-align: right;"></td>
                            <td style="text-align: right;"><strong>Diskon <?php if($data->DiskonPersen>0) echo number_format($data->DiskonPersen,2)." %";?> : </strong></td>
                            <td style="text-align: left;"><?php echo number_format($data->Diskon,2);?></td>
                            <td style="text-align: right;"><strong>PPN <?php if($data->PPNPersen>0) echo number_format($data->PPNPersen,2)." %";?>  : </strong></td>
                            <td style="text-align: left;"><?php echo number_format($data->PPN,2);?></td>
                            <td style="text-align: right;"><strong>Grand Total : </strong></td>
                            <td></td>
                            <td style="text-align: right;"><?php echo number_format($data->GrandTotal,2);?></td>
                        </tr>
                        <tr class="highlight">
                            <td style="text-align: right;"></td>
                            <td style="text-align: right;"></td>
                            <td style="text-align: right;"></td>
                            <td style="text-align: right;"><strong style="text-decoration: underline;">Terbayar : </strong></td>
                            <td style="text-align: left;"><?php echo number_format(($data->GrandTotal-$data->Sisa),2);?></td>
                            <td style="text-align: right;"><strong style="text-decoration: underline;">Piutang : </strong></td>
                            <td></td>
                            <td style="text-align: right;"><?php echo number_format($data->Sisa,2);?></td>
                        </tr>
                        <?php
                        $grandTotal += $data->GrandTotal;
                        $totalTerbayar += ($data->GrandTotal-$data->Sisa);
                        $totalSisa += $data->Sisa;
                    }
                }
                ?>
                <tr class="highlight2">
                    <td colspan='6' style="text-align: right;"><strong>TOTAL NILAI PENJUALAN : </strong></td>
                    <td style="text-align: right;"></td>
                    <td style="text-align: right;"><?php echo number_format($grandTotal,2);?></td>
                </tr>
                <tr class="highlight2">
                    <td colspan='6' style="text-align: right;"><strong>TOTAL NILAI TERBAYAR : </strong></td>
                    <td style="text-align: right;"></td>
                    <td style="text-align: right;"><?php echo number_format($totalTerbayar,2);?></td>
                </tr>
                <tr class="highlight2">
                    <td colspan='6' style="text-align: right;"><strong>TOTAL NILAI PIUTANG : </strong></td>
                    <td style="text-align: right;"></td>
                    <td style="text-align: right;"><?php echo number_format($totalSisa,2);?></td>
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