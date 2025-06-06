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
    $cond = "Tanggal BETWEEN '$datestartExp' AND '$dateendExp'";
    $subtitle = "Periode. ".$datestart." - ".$dateend;
} else if ($datestart != "") {
    $cond = "Tanggal='$datestartExp'";
    $subtitle = "Periode. ".$datestart;
} else {
    $cond = "DATE_FORMAT(Tanggal,'%Y-%m') = '" . date("Y-m") . "'";
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
                    Jl. Tukad Batanghari No. 42 
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
            <h1 class="underline">** REKAP PO SUPPLIER **</h1><?php echo $subtitle; ?>
        </div>
        <table class="tabelList2" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th width="100">NO. PO</th>
                    <th width="150">SUPPLIER</th>
                    <th>ITEM</th>
                    <th width="100">QTY</th>
                    <th width="100">DITERIMA</th>
                    <th width="100">OUTSTANDING</th>
                    <th width="100">HARGA</th>
                    <th width="100">SUB TOTAL</th>
                    <!--<th width="80">TOTAL</th>
                    <th width="80">DISKON</th>
                    <th width="80">PPN</th>
                    <th width="80">GRAND TOTAL</th>
                    <th width="80">TERBAYAR</th>
                    <th width="80">PIUTANG</th>-->
                </tr>
            </thead>
            <tbody>
                <?php
                $query = newQuery("get_results","SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_po WHERE $cond AND JenisPO='1' AND DeletedDate IS NULL ORDER BY IDPO ASC");
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
                    foreach($query as $data){
                        $supplier = newQuery("get_row","SELECT * FROM tb_supplier WHERE IDSupplier='".$data->IDSupplier."'");
                        $qDetail = newQuery("get_results","SELECT * FROM tb_po_detail WHERE NoPO='".$data->NoPO."'");
                        if($qDetail){
                            $i=0;
                            foreach($qDetail as $dDetail){
                                $i++;
                                $dBarang = newQuery("get_row","SELECT * FROM tb_barang WHERE IDBarang='".$dDetail->IDBarang."'");
                                $isPaket = newQuery("get_results","SELECT a.*, b.* FROM tb_barang_child a, tb_barang b WHERE a.IDBarang=b.IDBarang AND a.IDParent='".$dDetail->IDBarang."'");
                                if($dBarang->IsBarang=="1" && !$isPaket){
                                    $terkirim = newQuery("get_var","SELECT SUM(Qty) FROM tb_penerimaan_stok_detail WHERE IDBarang='".$dDetail->IDBarang."' AND NoPenerimaanBarang IN (SELECT NoPenerimaanBarang FROM tb_penerimaan_stok WHERE NoPO='".$data->NoPO."')");
                                    if(!$terkirim) $terkirim=0;
                                    $totalTerkirim += $terkirim;
                                    $outstanding =  $dDetail->Qty - $terkirim;
                                    $totalSisa += $outstanding;
                                } else {
                                    $terkirim="";
                                    $outstanding="";
                                }

                                ?>
                                <tr>
                                    <?php if($i==1){ ?>
                                        <td style="text-align: center;"><strong><?php echo $data->NoPO;?></strong></td>
                                        <td><?php echo $supplier->NamaPerusahaan;?></td>
                                    <?php } else { ?>
                                        <td></td>
                                        <td></td>
                                    <?php } ?>

                                    <td><?php echo $dDetail->NamaBarang; ?></td>
                                    <td style="text-align: center;"><?php echo $dDetail->Qty; ?></td>
                                    <td style="text-align: center;"><?php echo $terkirim; ?></td>
                                    <td style="text-align: center;"><?php echo $outstanding; ?></td>
                                    <td style="text-align: center;"><?php echo number_format($dDetail->Harga,2); ?></td>
                                    <td style="text-align: right;"><?php echo number_format($dDetail->SubTotal,2); ?></td>

                                    <?php /*if($i==1){ ?>
                                        <td style="text-align: right;"><?php echo number_format($data->Total,2);?></td>
                                        <td style="text-align: right;"><?php echo number_format($data->Diskon,2);?></td>
                                        <td style="text-align: right;"><?php echo number_format($data->PPN,2);?></td>
                                        <td style="text-align: right;"><?php echo number_format($data->GrandTotal,2);?></td>
                                        <td style="text-align: right;"><?php echo number_format($data->Pembayaran,2);?></td>
                                        <td style="text-align: right;"><?php echo number_format($data->Sisa,2);?></td>
                                    <?php } else { ?>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    <?php }*/ ?>
                                </tr>
                                <?php
                                
                                if($isPaket){
                                    foreach($isPaket as $dataPaket){
                                        $dBarang = newQuery("get_row","SELECT * FROM tb_barang WHERE IDBarang='".$dataPaket->IDBarang."'");
                                        $terkirim = newQuery("get_var","SELECT SUM(Qty) FROM tb_penerimaan_stok_detail WHERE IDBarang='".$dataPaket->IDBarang."' AND NamaBarang LIKE '%".$dDetail->NamaBarang."%'
                                        AND NoPenerimaanBarang IN (SELECT NoPenerimaanBarang FROM tb_penerimaan_stok WHERE NoPO='".$data->NoPO."')");
                                        if(!$terkirim) $terkirim=0;
                                        $totalTerkirim += $terkirim;
                                        $outstanding =  $dDetail->Qty - $terkirim;
                                        $totalSisa += $outstanding;

                                        if($data->Completed=="1" && $outstanding>0){

                                        } else {
                                            ?>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td>* <?php echo $dBarang->Nama; ?></td>
                                                <td style="text-align: center;"><?php echo $dDetail->Qty; ?></td>
                                                <td style="text-align: center;"><?php echo $terkirim; ?></td>
                                                <td style="text-align: center;"><?php echo $outstanding; ?></td>
                                                <td style="text-align: center;"></td>
                                                <td style="text-align: center;"></td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                }
                            }
                        }
                        $i++;
                        $totalItem += $data->TotalItem;
                        $totalNilai += $data->Total;
                        $PPN += $data->PPN;
                        $grandTotal += $data->GrandTotal;
                        $diskon += $data->Diskon;
                        $sisa += $data->Pembayaran;
                        $pembayaran += $data->Sisa;
                        ?>
                        <tr class="highlight">
                            <td colspan='7' style="text-align: right;"><strong>TOTAL : </strong></td>
                            <td style="text-align: right;"><?php echo number_format($data->Total,2);?></td>
                        </tr>
                        <tr class="highlight">
                            <td colspan='3' style="text-align: right;"><strong>DISKON : </strong></td>
                            <td style="text-align: right;"><?php echo number_format($data->Diskon,2);?></td>
                            <td style="text-align: right;"><strong>PPN : </strong></td>
                            <td style="text-align: right;"><?php echo number_format($data->PPN,2);?></td>
                            <td style="text-align: right;"><strong>GRAND TOTAL : </strong></td>
                            <td style="text-align: right;"><?php echo number_format($data->GrandTotal,2);?></td>
                        </tr>
                        <tr class="highlight">
                            <td colspan='5' style="text-align: right;"><strong>T. PEMBAYARAN : </strong></td>
                            <td style="text-align: right;"><?php echo number_format($data->Pembayaran,2);?></td>
                            <td style="text-align: right;"><strong>SISA HUTANG : </strong></td>
                            <td style="text-align: right;"><?php echo number_format($data->Sisa,2);?></td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='8'>Tidak ada data yang dapat ditampilkan...</td></tr>";
                }
                ?>
                <!--
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>Total :</strong></td>
                    <td style="text-align: center;"><?php echo $totalItem;?></td>
                    <td style="text-align: center;"><?php echo $totalItem;?></td>
                    <td style="text-align: center;"><?php echo $totalItem;?></td>
                    <td style="text-align: right;"><?php echo number_format($totalNilai,2);?></td>
                    <td style="text-align: right;"><?php echo number_format($diskon,2);?></td>
                    <td style="text-align: right;"><?php echo number_format($PPN,2);?></td>
                    <td style="text-align: right;"><?php echo number_format($grandTotal,2);?></td>
                    <td style="text-align: right;"><?php echo number_format($pembayaran,2);?></td>
                    <td style="text-align: right;"><?php echo number_format($sisa,2);?></td>
                </tr>
                -->
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