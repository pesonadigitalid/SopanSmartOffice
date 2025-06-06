<?php
session_start();
include_once "../config/connection.php";
$id = $_GET['id'];

$bulan = array("01"=>"Januari","02"=>"Februari","03"=>"Maret","04"=>"April","05"=>"Mei","06"=>"Juni","07"=>"Juli","08"=>"Agustus","09"=>"September","10"=>"Oktober","11"=>"November","12"=>"Desember");
$dataPO = newQuery("get_row","SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_po WHERE NoPO='".$id."'");
//$dataProyek = newQuery("get_row","SELECT * FROM tb_proyek WHERE IDProyek='".$dataPO->IDProyek."'");
$dataSupplier = newQuery("get_row","SELECT * FROM tb_supplier WHERE IDSupplier='".$dataPO->IDSupplier."'");
$tanggalExp = explode("-",$dataPO->Tanggal);
$proyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='".$dataPO->IDProyek."'");


function terbilang ($angka) {
    $angka = (float)$angka;
    $bilangan = array('','Satu','Dua','Tiga','Empat','Lima','Enam','Tujuh','Delapan','Sembilan','Sepuluh','Sebelas');
    if ($angka < 12) {
        return $bilangan[$angka];
    } else if ($angka < 20) {
        return $bilangan[$angka - 10] . ' Belas';
    } else if ($angka < 100) {
        $hasil_bagi = (int)($angka / 10);
        $hasil_mod = $angka % 10;
        return trim(sprintf('%s Puluh %s', $bilangan[$hasil_bagi], $bilangan[$hasil_mod]));
    } else if ($angka < 200) { return sprintf('Seratus %s', terbilang($angka - 100));
    } else if ($angka < 1000) { $hasil_bagi = (int)($angka / 100); $hasil_mod = $angka % 100; return trim(sprintf('%s Ratus %s', $bilangan[$hasil_bagi], terbilang($hasil_mod)));
    } else if ($angka < 2000) { return trim(sprintf('Seribu %s', terbilang($angka - 1000)));
    } else if ($angka < 1000000) { $hasil_bagi = (int)($angka / 1000); $hasil_mod = $angka % 1000; return sprintf('%s Ribu %s', terbilang($hasil_bagi), terbilang($hasil_mod));
    } else if ($angka < 1000000000) { $hasil_bagi = (int)($angka / 1000000); $hasil_mod = $angka % 1000000; return trim(sprintf('%s Juta %s', terbilang($hasil_bagi), terbilang($hasil_mod)));
    } else if ($angka < 1000000000000) { $hasil_bagi = (int)($angka / 1000000000); $hasil_mod = fmod($angka, 1000000000); return trim(sprintf('%s Milyar %s', terbilang($hasil_bagi), terbilang($hasil_mod)));
    } else if ($angka < 1000000000000000) { $hasil_bagi = $angka / 1000000000000; $hasil_mod = fmod($angka, 1000000000000); return trim(sprintf('%s Triliun %s', terbilang($hasil_bagi), terbilang($hasil_mod)));
    } else {
        return 'Data Salah';
    }
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
            <tr style="padding-bottom: 100px !important;">
                <td width="80%" class="bottom" style="line-height: 1.5em;padding-top:7px;">
                    <img src="print-logo-sopan.png" align="left" style="padding-right: 10px;margin-right: 20px;border-right: solid 2px #e11b22;margin-top:-7px;width: 200px;height: 85px;object-fit: contain;">
                    Jl. Tukad Batanghari No. 42<br />
                    Denpasar 80225, Bali<br />
                    Phone. +62 823-2800-1818<br />
                    Email. mail.aristonbali@gmail.com
                </td>
                <td width="20%" align="right" style="padding-top:20px">
                
                </td>
            </tr>
        </table>
        <table style="margin-top:30px;">
            <tr>
                <td colspan="2"><h3 class="title-print">PURCHASE ORDER</h3></td>
            </tr>
            <tr>
                <td width="50%" class="bottom" style="padding-top: 20px !important;">
                    SUPPLIER :<br />
                    <strong><?php if($dataSupplier) echo $dataSupplier->NamaPerusahaan; else echo "UMUM"; ?></strong><br />
                    <?php if($dataSupplier->Alamat!="" && $dataSupplier->Alamat!="-")  echo $dataSupplier->Alamat."<br />"; ?>
                    <?php if($dataSupplier->Kota!="" && $dataSupplier->Kota!="-") echo $dataSupplier->Kota; ?> <?php if($dataSupplier->Provinsi!="" && $dataSupplier->Provinsi!="-") echo $dataSupplier->Provinsi; ?> <?php if($dataSupplier->KodePos!="" && $dataSupplier->KodePos!="-") echo $dataSupplier->KodePos; ?>
                </td>
                <td width="50%" align="right" style="padding-top: 20px !important;">
                    <div class="sideLabel">
                        <label>NO. PO</label><br />
                        <label>TANGGAL</label><br />
                        <label>KODE PROYEK</label><br />
                        <label>NAMA PROYEK</label><br />
                    </div>
                    <div class="sideText">
                        <span><?php echo $dataPO->NoPO; ?></span><br />
                        <span><?php echo $dataPO->TanggalID; ?></span><br />
                        <span><?php if($proyek) echo $dataPO->KodeProyek." / ".$proyek->Tahun; else echo "UMUM"; ?></span><br />
                        <span><?php if($proyek) echo $proyek->NamaProyek; else echo "UMUM"; ?></span><br />
                    </div>
                </td>
            </tr>
        </table>
        <table class="tabelList2 border-solid" cellpadding="0" cellspacing="0" style="margin-top: 40px;">
            <thead>
                <tr>
                    <th width="30">No.</th>
                    <th>Nama Barang</th>
                    <th width="100">Harga</th>
                    <th width="40">Qty</th>
                    <th width="60">Satuan</th>
                    <th width="100">Sub Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $query = $db->get_results("SELECT * FROM tb_po_detail WHERE NoPO='$id' ORDER BY NoUrut ASC");
                    if($query){
                        $i=1;
                        $qty = 0;
                        $total = 0;
                        foreach($query as $data){
                            if($dataPO->JenisPO=="1")
                                $satuan = $db->get_var("SELECT a.Nama FROM tb_satuan a, tb_barang b WHERE a.`IDSatuan`=b.`IDSatuan` AND b.`IDBarang`='".$data->IDBarang."'");
                            else
                                $satuan = $data->Satuan;
                            ?>
                            <tr class="border-bottom">
                                <td style="text-align: center;"><?php echo $i; ?></td>
                                <td><?php echo $data->NamaBarang; ?></td>
                                <td style="text-align: right;"><?php echo number_format($data->Harga); ?></td>
                                <td style="text-align: center;"><?php echo $data->Qty; ?></td>
                                <td style="text-align: center;"><?php echo $satuan; ?></td>
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
                    <td colspan="5" style="text-align: right;"><strong>SUB TOTAL</strong></td>
                    <td style="text-align: right;"><?php echo number_format($dataPO->Total);?></td>
                </tr>
                <tr>
                    <td colspan="5" style="text-align: right;"><strong>DISKON <?php if($dataPO->DiskonPersen>0) echo number_format($dataPO->DiskonPersen)."%";?></strong></td>
                    <td style="text-align: right;"><?php echo number_format($dataPO->Diskon);?></td>
                </tr>
                <tr>
                    <td colspan="5" style="text-align: right;"><strong>TOTAL</strong></td>
                    <td style="text-align: right;"><?php echo number_format($dataPO->GrandTotal);?></td>
                </tr>
                <tr>
                    <td colspan="5" style="text-align: right;"><strong>PPN <?php if($dataPO->PPNPersen>0) echo number_format($dataPO->PPNPersen)."%";?></strong></td>
                    <td style="text-align: right;"><?php echo number_format($dataPO->PPN);?></td>
                </tr>
                <tr>
                    <td colspan="5" style="text-align: right;"><strong>GRAND TOTAL</strong></td>
                    <td style="text-align: right;"><?php echo number_format($dataPO->GrandTotal);?></td>
                </tr>
            </tbody>
        </table>
        <table class="asignment" style="margin-top: 10px;">
            <tr>
                <td colspan="3">
                    <table width="100%">
                        <tr>
                            <td width="130"><strong>Keterangan</strong></td>
                            <td width="10">:</td>
                            <td><?php echo $dataPO->Keterangan; ?></td>
                        </tr>
                        <tr>
                            <td width="130"><strong>Terbilang</strong></td>
                            <td width="10">:</td>
                            <td><?php echo terbilang($dataPO->GrandTotal);?> Rupiah</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="vertical-align: top;">
                    <table width="100%">
                        <tr>
                            <td width="130"><strong>Pembayaran</strong></td>
                            <td width="10">:</td>
                            <td><?php if($dataPO->InvPembayaran!="") echo nl2br($dataPO->InvPembayaran); else echo "-";?></td>
                        </tr>
                        <tr>
                            <td><strong>Bank</strong></td>
                            <td>:</td>
                            <td><?php if($dataPO->InvBank!="") echo $dataPO->InvBank; else echo "-";?></td>
                        </tr>
                        <tr>
                            <td><strong>Delivery</strong></td>
                            <td>:</td>
                            <td><?php if($dataPO->InvDelivery!="") echo $dataPO->InvDelivery; else echo "-";?></td>
                        </tr>
                        <tr>
                            <td><strong>Expedisi</strong></td>
                            <td>:</td>
                            <td><?php if($dataPO->InvExpedisi!="") echo $dataPO->InvExpedisi; else echo "-";?></td>
                        </tr>
                        <tr>
                            <td><strong>Alamat Pengiriman</strong></td>
                            <td>:</td>
                            <td><?php if($dataPO->InvAlamatPengiriman!="") echo $dataPO->InvAlamatPengiriman; else echo "-";?></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td width="30%" class="center" style="padding-top:15px"><strong>Cost Control</strong><br /><br /><br /><br /><strong>(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</strong>
                </td><td class="center" width="30%" style="padding-top:15px"><strong>Purchasing</strong><br /><br /><br /><br /><strong>(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</strong>
                </td><td class="center" width="30%" style="padding-top:15px"><strong>Direktur</strong><br /><br /><br /><br /><strong>( Ir. Lukito Pramono )</strong>
                </td>
            </tr>
        </table>
        <script type="text/javascript">
            window.onload = function () { window.print(); }
        </script>
    </body>
</html>