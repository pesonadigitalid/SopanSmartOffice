<?php
session_start();
include_once "../config/connection.php";
$bulan = array("01"=>"Januari","02"=>"Februari","03"=>"Maret","04"=>"April","05"=>"Mei","06"=>"Juni","07"=>"Juli","08"=>"Agustus","09"=>"September","10"=>"Oktober","11"=>"November","12"=>"Desember");

$datestart = $_GET['datestart'];
$expstart = explode("/",$datestart);
$datestartExp = $expstart[2]."-".$expstart[1]."-".$expstart[0];
$dateend = $_GET['dateend'];
$expend = explode("/",$dateend);
$dateendExp = $expend[2]."-".$expend[1]."-".$expend[0];

if ($datestart != "" && $dateend != "") {
    $cond = " AND (a.Tanggal BETWEEN '$datestartExp' AND '$dateendExp') ";
    $cond2 = " AND (b.Tanggal BETWEEN '$datestartExp' AND '$dateendExp') ";
    $cond3 = " AND (Tanggal BETWEEN '$datestartExp' AND '$dateendExp') ";
    $subtitle = "Periode. ".$datestart." - ".$dateend;
} else if ($datestart != "") {
    $cond = " AND a.Tanggal='$datestartExp' ";
    $cond2 = " AND b.Tanggal='$datestartExp' ";
    $cond3 = " AND Tanggal='$datestartExp' ";
    $subtitle = "Periode. ".$datestart;
} else {
    $cond = " AND DATE_FORMAT(a.Tanggal,'%Y-%m') = '" . date("Y-m") . "' ";
    $cond2 = " AND DATE_FORMAT(b.Tanggal,'%Y-%m') = '" . date("Y-m") . "' ";
    $cond3 = " AND DATE_FORMAT(Tanggal,'%Y-%m') = '" . date("Y-m") . "' ";
    $subtitle = "Periode : ".$bulan[date("m")]." ".date("Y");
}

$departement = $_GET['departement'];
if($departement!=""){
    $cond4 = " AND IDDepartement='$departement'";
    $namaDepartement = newQuery("get_var","SELECT NamaDepartement FROM tb_departement WHERE IDDepartement='$departement'");
}
?>
<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <meta name="description" content=""/>
        <meta name="author" content="Yogi Pratama - Pesona Creative - 085737654543"/>
        
        <title>SOPAN Smart Office - Smart office for smart people</title>
        
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport"/>
        <link rel="stylesheet" href="print-style.css" media="all" type="text/css"/>
        <style media="all">
            table {
                width: 100%;
                border: none;
                border-collapse: collapse;
            }
            .tabelList2 th {
                background: #eee;
                border: solid 1px #ccc;
                padding: 5px;
                font-size: 9px;
            }
            .tabelList2 td {
                padding: 5px;
                border-bottom: dashed 1px #ccc;
                font-size: 8px;
            }
        </style>
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
            <h1 class="underline" style="text-transform: uppercase;">** LAPORAN REKAP PROYEKSI PROYEK <?php echo $namaDepartement; ?>**</h1><?php echo $subtitle; ?>
        </div>
        <table class="tabelList2" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th rowspan="2">Proyek</th>
                    <th colspan="5">Pendapatan</th>
                    <th colspan="3">Biaya</th>
                    <th rowspan="2">L/R Proyek</th>
                </tr>
                <tr>
                    <th width="80">Invoice</th>
                    <th width="80">DPP</th>
                    <th width="80">PPN 10%</th>
                    <th width="80">PPH 2%</th>
                    <th width="80">Total Pajak</th>
                    <th width="80">Biaya Material</th>
                    <th width="80">Subkon / Tenaga</th>
                    <th width="80">Overhead</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $qProyek = newQuery("get_results","SELECT IDProyek, KodeProyek, Tahun, NamaProyek FROM tb_proyek WHERE 
                (IDProyek in (SELECT IDProyek FROM tb_proyek_invoice WHERE IDProyek>0 $cond3 ORDER BY IDProyek ASC) OR
                IDProyek in (SELECT IDProyek FROM tb_po WHERE IDProyek>0 $cond3 ORDER BY IDProyek ASC) OR
                IDProyek in (SELECT a.IDProyek FROM tb_jurnal a, tb_jurnal_detail b WHERE a.IDJurnal=b.IDJurnal AND b.Debet>0 $cond AND a.NoRef='' AND a.IDProyek>0) OR
                IDProyek in (SELECT b.IDProyek FROM tb_pengiriman_detail a, tb_pengiriman b WHERE a.NoPengiriman=b.NoPengiriman AND StokFrom='0' $cond2 GROUP BY a.NoPengiriman))
                $cond4
                ORDER BY Tahun ASC, KodeProyek ASC");
                if($qProyek){

                    $tpendapatan2 = 0;
                    $tdpp = 0;
                    $tppn10 = 0;
                    $tpph2 = 0;
                    $tmaterial1 = 0;
                    $ttenaga1 = 0;
                    $toverhead1 = 0;
                    $tprofit = 0;
                    $ttotalPajak = 0;

                    foreach($qProyek as $dProyek){

                        $id = $dProyek->IDProyek;
                        $urut = "";

                        $pendapatan1 = 0;
                        $pendapatan2 = 0;
                        $pendapatan3 = 0;

                        $ppn10 = 0;
                        $pph2 = 0;
                        $dpp = 0;
                        $totalPajak = 0;

                        $aPendapatan = array();
                        $query = $db->get_results("SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_proyek_invoice WHERE IDProyek='$id' $cond3 ORDER BY IDProyek ASC");
                        if($query){
                            foreach($query as $data){
                                if(($data->Sisa>0 && $data->Sisa<1) || $data->Sisa<0) $sisa = 0; else  $sisa = $data->Sisa;

                                //Pajak
                                $temp_dpp = 0;
                                $temp_ppn = 0;
                                $temp_pph = 0;
                                if($data->PPNPersen>0){
                                    $temp_dpp = $data->Jumlah;
                                    $temp_ppn = $data->PPN;
                                    $temp_pph = round($temp_dpp*0.02,2);
                                }

                                $pendapatan1 += $data->GrandTotal;
                                $pendapatan2 += ($data->GrandTotal-$data->Sisa);
                                $pendapatan3 += $sisa;

                                if($pendapatan2==0){
                                    $temp_dpp = 0;
                                    $temp_ppn = 0;
                                    $temp_pph = 0;
                                }
                                
                                $dpp += $temp_dpp;
                                $pph2 += $temp_pph;
                                $ppn10 += $temp_ppn;
                            }
                        }

                        $material1 = 0;
                        $material2 = 0;
                        $material3 = 0;

                        $aMaterial = array();
                        $query = $db->get_results("SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_po a, tb_supplier b WHERE a.IDProyek='$id' AND a.JenisPO='1' AND a.IDSupplier=b.IDSupplier $cond $urut");
                        // $query = $db->get_results("SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_po a, tb_supplier b WHERE a.IDProyek='$id' AND a.JenisPO='1' AND a.IDSupplier=b.IDSupplier ORDER BY b.NamaPerusahaan ASC");
                        if($query){
                            foreach($query as $data){
                                $supplier = $db->get_row("SELECT * FROM tb_supplier WHERE IDSupplier='".$data->IDSupplier."'");
                                if($supplier) $supplier = $supplier->NamaPerusahaan; else $supplier="-";
                                if(($data->Sisa>0 && $data->Sisa<1) || $data->Sisa<0) $sisa = 0; else  $sisa = $data->Sisa;
                                
                                $material1 += $data->GrandTotal;
                                $material2 += $data->TotalPembayaran;
                                $material3 += $sisa;
                            }
                        }

                        $query = $db->get_results("SELECT a.*, b.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal a, tb_jurnal_detail b WHERE a.IDJurnal=b.IDJurnal AND b.IDRekening='45' AND b.Debet>0 AND a.IDProyek='$id' $cond AND a.NoRef=''");
                        if($query){
                            foreach($query as $data){
                                if($data->NoRef==''){
                                    $cek = $db->get_row("SELECT * FROM tb_jurnal_detail WHERE IDJurnal='".$data->IDJurnal."' AND IDRekening!='".$data->IDRekening."'");
                                    if($cek->IDRekening!='132'){
                                        
                                        $material1 += $data->Debet;
                                        $material2 += $data->Debet;
                                        $material3 += 0;
                                    }
                                }
                            }
                        }

                        //Dari Pengiriman
                        $pengiriman1 = 0;
                        $pengiriman2 = 0;
                        $pengiriman3 = 0;

                        $aPengiriman = array();
                        $query = $db->get_results("SELECT a.*, DATE_FORMAT(b.Tanggal,'%d/%m/%Y') AS TanggalID, b.IDPengiriman FROM tb_pengiriman_detail a, tb_pengiriman b WHERE a.NoPengiriman=b.NoPengiriman AND b.IDProyek='$id' AND StokFrom='0' $cond2 GROUP BY a.NoPengiriman");
                        if($query){
                            foreach($query as $data){
                                $grandTotal = $db->get_var("SELECT SUM(SubTotal) FROM tb_pengiriman_detail a, tb_pengiriman b WHERE a.NoPengiriman=b.NoPengiriman AND b.IDProyek='$id' AND a.StokFrom='0' AND a.NoPengiriman='".$data->NoPengiriman."'");
                                if(!$grandTotal) $grandTotal=0;

                                $pengiriman1 += $grandTotal;
                                $pengiriman2 += $grandTotal;
                                $pengiriman3 += 0;
                            }
                        }

                        $tenaga1 = 0;
                        $tenaga2 = 0;
                        $tenaga3 = 0;

                        $aTenaga = array();
                        $query = $db->get_results("SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_po a, tb_supplier b WHERE a.IDProyek='$id' AND a.JenisPO='2' AND a.IDSupplier=b.IDSupplier $cond $urut");
                        // $query = $db->get_results("SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_po a, tb_supplier b WHERE a.IDProyek='$id' AND a.JenisPO='2' AND a.IDSupplier=b.IDSupplier ORDER BY b.NamaPerusahaan ASC");
                        if($query){
                            foreach($query as $data){
                                $supplier = $db->get_row("SELECT * FROM tb_supplier WHERE IDSupplier='".$data->IDSupplier."'");
                                if($supplier) $supplier = $supplier->NamaPerusahaan; else $supplier="-";
                                if(($data->Sisa>0 && $data->Sisa<1) || $data->Sisa<0) $sisa = 0; else  $sisa = $data->Sisa;
                                
                                $tenaga1 += $data->GrandTotal;
                                $tenaga2 += $data->TotalPembayaran;
                                $tenaga3 += $sisa;
                            }
                        }

                        $query = $db->get_results("SELECT a.*, b.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal a, tb_jurnal_detail b WHERE a.IDJurnal=b.IDJurnal AND b.IDRekening='45' AND b.Debet>0 AND a.IDProyek='$id' $cond AND a.NoRef=''");
                        if($query){
                            foreach($query as $data){
                                if($data->NoRef==''){
                                    $cek = $db->get_row("SELECT * FROM tb_jurnal_detail WHERE IDJurnal='".$data->IDJurnal."' AND IDRekening!='".$data->IDRekening."'");
                                    if($cek->IDRekening!='138' && $cek->IDRekening!='139'){
                                        
                                        $tenaga1 += $data->Debet;
                                        $tenaga2 += $data->Debet;
                                        $tenaga3 += 0;
                                    }
                                }
                            }
                        }

                        $overhead1 = 0;
                        $overhead2 = 0;
                        $overhead3 = 0;

                        $aOverhead = array();
                        $query = $db->get_results("SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_po a, tb_supplier b WHERE a.IDProyek='$id' AND a.JenisPO='3' AND a.IDSupplier=b.IDSupplier $cond $urut");
                        // $query = $db->get_results("SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_po a, tb_supplier b WHERE a.IDProyek='$id' AND a.JenisPO='3' AND a.IDSupplier=b.IDSupplier ORDER BY b.NamaPerusahaan ASC");
                        if($query){
                            foreach($query as $data){
                                $supplier = $db->get_row("SELECT * FROM tb_supplier WHERE IDSupplier='".$data->IDSupplier."'");
                                if($supplier) $supplier = $supplier->NamaPerusahaan; else $supplier="-";
                                if(($data->Sisa>0 && $data->Sisa<1) || $data->Sisa<0) $sisa = 0; else  $sisa = $data->Sisa;
                                
                                $overhead1 += $data->GrandTotal;
                                $overhead2 += $data->TotalPembayaran;
                                $overhead3 += $sisa;
                            }
                        }

                        $query = $db->get_results("SELECT a.*, b.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal a, tb_jurnal_detail b WHERE a.IDJurnal=b.IDJurnal AND (b.IDRekening IN (SELECT IDRekening FROM tb_master_rekening WHERE IDParent='73' OR IDParent='101' OR IDParent IN (SELECT IDRekening FROM tb_master_rekening WHERE IDParent='73' OR IDParent='101')) OR b.IDRekening = '138') AND b.Debet>0 AND a.IDProyek='$id' AND Tipe='0' $cond");
                        if($query){
                            foreach($query as $data){
                                
                                $overhead1 += $data->Debet;
                                $overhead2 += $data->Debet;
                                $overhead3 += 0;
                            }
                        }

                        $pengeluaran1 = $material1+$tenaga1+$overhead1+$pengiriman1;
                        $pengeluaran2 = $material2+$tenaga2+$overhead2+$pengiriman2;
                        $pengeluaran3 = $material3+$tenaga3+$overhead3+$pengiriman3;
                        $totalPajak = $pph2+$ppn10;
                        $profit = ($pendapatan2-$totalPajak)-$pengeluaran1;

                        $tpendapatan2 += $pendapatan2;
                        $tdpp += $dpp;
                        $tppn10 += $ppn10;
                        $tpph2 += $pph2;
                        $ttotalPajak += $totalPajak;
                        $tmaterial1 += ($material1+$pengiriman1);
                        $ttenaga1 += $tenaga1;
                        $toverhead1 += $overhead1;
                        $tprofit += $profit;
                        ?>
                        <tr>
                            <td class="labelHeader"><?php echo $dProyek->KodeProyek."/".$dProyek->Tahun."/".$dProyek->NamaProyek; ?></td>
                            <td style="text-align:right"><?php echo number_format($pendapatan2,2); ?></td>
                            <td style="text-align:right"><?php echo number_format($dpp,2); ?></td>
                            <td style="text-align:right"><?php echo number_format($ppn10,2); ?></td>
                            <td style="text-align:right"><?php echo number_format($pph2,2); ?></td>
                            <td style="text-align:right"><?php echo number_format($totalPajak,2); ?></td>
                            <td style="text-align:right"><?php echo number_format(($material1+$pengiriman1),2); ?></td>
                            <td style="text-align:right"><?php echo number_format($tenaga1,2); ?></td>
                            <td style="text-align:right"><?php echo number_format($overhead1,2); ?></td>
                            <td style="text-align:right;font-weight:bold;text-decoration:underline;"><?php echo number_format($profit,2); ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <td class="labelHeader"><strong>GRAND TOTAL</strong></td>
                        <td style="text-align:right;font-weight:bold;"><?php echo number_format($tpendapatan2,2); ?></td>
                        <td style="text-align:right;font-weight:bold;"><?php echo number_format($tdpp,2); ?></td>
                        <td style="text-align:right;font-weight:bold;"><?php echo number_format($tppn10,2); ?></td>
                        <td style="text-align:right;font-weight:bold;"><?php echo number_format($tpph2,2); ?></td>
                        <td style="text-align:right;font-weight:bold;"><?php echo number_format($ttotalPajak,2); ?></td>
                        <td style="text-align:right;font-weight:bold;"><?php echo number_format($tmaterial1,2); ?></td>
                        <td style="text-align:right;font-weight:bold;"><?php echo number_format($ttenaga1,2); ?></td>
                        <td style="text-align:right;font-weight:bold;"><?php echo number_format($toverhead1,2); ?></td>
                        <td style="text-align:right;font-weight:bold;text-decoration:underline;"><?php echo number_format($tprofit,2); ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <table class="asignment" style="margin-top: 20px;">
            <tr>
                <td class="center" width="60%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td class="center" width="40%">Mengetahui,<br /><br /><br /><br />( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</td>
            </tr>
        </table>
        <script type="text/javascript">
            setTimeout(function(){
                window.print();
            },1500);
        </script>
    </body>
</html>