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
    $cond = " AND a.DateCreated BETWEEN '$datestartExp' AND '$dateendExp'";
    $subtitle = "Periode. ".$datestart." - ".$dateend;
} else if ($datestart != "") {
    $cond = " AND a.DateCreated='$datestartExp'";
    $subtitle = "Periode. ".$datestart;
} else {
    $cond = " AND DATE_FORMAT(a.DateCreated,'%Y-%m') = '" . date("Y-m") . "'";
    $subtitle = "Periode : ".$bulan[date("m")]." ".date("Y");
}
$periode = "Periode : ".$bulan[date("m")]." ".date("Y");



$filter = $_GET['filter'];
$keyword = trim($_GET['keyword']);

if($filter=="1"){
    $cond .= " AND b.Nama LIKE '%$keyword%'";
} else if($filter=="2"){
    $skipAudit = true;
    $skipPenerimaan = true;
    $skipSJ = false;
    $cond2 = " AND c.NoSuratJalan = '$keyword'";
} else if($filter=="3"){
    $skipAudit = true;
    $skipPenerimaan = true;
    $skipSJ = false;
    $cond2 = " AND a.NoPenjualan = '$keyword'";
} else if($filter=="4"){
    $skipAudit = true;
    $skipPenerimaan = true;
    $skipSJ = false;
    $cond2 = " AND b.NamaPelanggan LIKE '%$keyword%'";
} else if($filter=="5"){
    $skipAudit = true;
    $skipPenerimaan = false;
    $skipSJ = true;
    $cond2 = " AND b.NamaPerusahaan LIKE '%$keyword%'";
} else if($filter=="6"){
    $skipAudit = true;
    $skipPenerimaan = false;
    $skipSJ = true;
    $cond2 = " AND a.NoPO = '$keyword'";
}
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
            <h1 class="underline">** STOK KELUAR MASUK **</h1><?php echo $subtitle; ?>
        </div>
        <table class="tabelList2" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th width="20">NO.</th>
                    <th width="80">TANGGAL</th>
                    <th>BARANG</th>
                    <th width="60">Stok Awal</th>
                    <th width="60">Stok In</th>
                    <th width="60">Stok Out</th>
                    <th width="80">Stok Akhir</th>
                    <th width="120">No. Referensi</th>
                    <th width="120">No. SPB</th>
                    <th width="80">HPP</th>
                    <th width="80">Harga Jual</th>
                    <th width="150">Pelanggan / Supplier</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = newQuery("get_results","SELECT a.*, b.Nama, DATE_FORMAT(a.DateCreated,'%d/%m/%Y') AS TanggalID FROM tb_kartu_stok_gudang a, tb_barang b WHERE a.IDBarang=b.IDBarang AND b.IsBarang='1' $cond  ORDER BY DateCreated ASC");
                if($query){
                    $i=0;
                    $arraySN = array();
                    foreach($query as $data){
                        $exp = explode(" ",$data->Keterangan);
                        $barang = newQuery("get_row","SELECT * FROM tb_barang WHERE IDBarang='".$data->IDBarang."'");
                        $IsSerialize = false;
                        if($barang->IsSerialize=="1") $IsSerialize = true;
                        $SN = "";
                        $Sisa = abs($data->StokPenyesuaian);

                        if($exp[0]=="Audit"){
                            if($skipAudit) continue;
                            $Detail = newQuery("get_row","SELECT * FROM tb_audit WHERE IDAudit='".$data->ID."'");
                            $NoPO = $Detail->NoAudit;
                            $Supplier="AUDIT STOK";

                            if($IsSerialize){
                                $q = newQuery("get_results","SELECT * FROM tb_audit_detail WHERE IDBarang='".$data->IDBarang."' AND NoAudit='".$Detail->NoAudit."'");
                                if($q){
                                    foreach($q as $d){
                                        if(!isset($arraySN["AU".$data->IDBarang])){
                                            $arraySN["AU".$data->IDBarang] = array();
                                        }

                                        if(!in_array($d->SN, $arraySN["AU".$data->IDBarang])){
                                            $SN .= $d->SN.", ";
                                            array_push($arraySN["AU".$data->IDBarang], $d->SN);

                                            $Sisa--;
                                            if($Sisa==0) break;
                                        }
                                    }
                                    $SN = substr($SN,0,-2);
                                }
                            }
                            $hpp = newQuery("get_var","SELECT Harga FROM tb_audit_detail WHERE NoAudit='".$Detail->NoAudit."' AND IDBarang='".$data->IDBarang."'");
                            if(!$hpp) $hpp=0;
                            $harga_jual = 0;
                            $no_spb = "";
                        } else if($exp[0]=="Penerimaan"){
                            if($skipPenerimaan) continue;

                            $Detail = newQuery("get_row","SELECT c.NoPenerimaanBarang , a.NoPO, b.NamaPerusahaan FROM tb_po a, tb_supplier b, tb_penerimaan_stok c WHERE a.`NoPO`=c.`NoPO` AND a.`IDSupplier`=b.`IDSupplier` AND c.`IDPenerimaan`='".$data->ID."' $cond2");
                            $NoPO = $Detail->NoPO;
                            $Supplier = strtoupper($Detail->NamaPerusahaan);

                            if($IsSerialize){
                                $q = newQuery("get_results","SELECT * FROM tb_penerimaan_stok_detail WHERE IDBarang='".$data->IDBarang."' AND NoPenerimaanBarang='".$Detail->NoPenerimaanBarang."'");
                                if($q){
                                    foreach($q as $d){
                                        if(!isset($arraySN["PO".$data->IDBarang])){
                                            $arraySN["PO".$data->IDBarang] = array();
                                        }

                                        if(!in_array($d->SN, $arraySN["PO".$data->IDBarang])){
                                            $SN .= $d->SN.", ";
                                            array_push($arraySN["PO".$data->IDBarang], $d->SN);

                                            $Sisa--;
                                            if($Sisa==0) break;
                                        }
                                    }
                                    $SN = substr($SN,0,-2);
                                }
                            }

                            $hpp = newQuery("get_var","SELECT HPP FROM tb_penerimaan_stok_detail WHERE NoPenerimaanBarang='".$Detail->NoPenerimaanBarang."' AND IDBarang='".$data->IDBarang."'");
                            if(!$hpp) $hpp=0;
                            $harga_jual = 0;
                            $no_spb = "";
                        } else if($exp[0]=="Pengiriman"){
                            if($skipSJ) continue;

                            $Detail = newQuery("get_row","SELECT c.NoSuratJalan, b.NamaPelanggan, a.NoPenjualan FROM tb_penjualan a, tb_pelanggan b, tb_penjualan_surat_jalan c WHERE a.`IDPenjualan`=c.`IDPenjualan` AND b.`IDPelanggan`=a.`IDPelanggan` AND c.`IDSuratJalan`='".$data->ID."' $cond2");

                            if(!$Detail) continue;

                            $NoPO = $Detail->NoSuratJalan;
                            $Supplier = strtoupper($Detail->NamaPelanggan);

                            if($IsSerialize){
                                $q = newQuery("get_results","SELECT * FROM tb_penjualan_surat_jalan_detail WHERE IDBarang='".$data->IDBarang."' AND NoSuratJalan='".$Detail->NoSuratJalan."'");
                                if($q){
                                    foreach($q as $d){
                                        if(!isset($arraySN["SJ".$data->IDBarang])){
                                            $arraySN["SJ".$data->IDBarang] = array();
                                        }

                                        if(!in_array($d->SN, $arraySN["SJ".$data->IDBarang])){
                                            $SN .= $d->SN.", ";
                                            array_push($arraySN["SJ".$data->IDBarang], $d->SN);

                                            $Sisa--;
                                            if($Sisa==0) break;
                                        }
                                    }
                                    $SN = substr($SN,0,-2);
                                }
                            }

                            $dp = newQuery("get_row","SELECT * FROM tb_penjualan_surat_jalan_detail WHERE NoSuratJalan='".$Detail->NoSuratJalan."' AND IDBarang='".$data->IDBarang."'");
                            if($dp){
                                $hpp = $dp->SubTotalHPP;
                                $harga_jual = $dp->SubTotal;
                            } else {
                                $hpp = 0;
                                $harga_jual = 0;
                            }
                            $no_spb = $Detail->NoPenjualan;
                        }
                        $i++;
                        
                        $stokAwal = newQuery("get_var","SELECT SUM(StokPenyesuaian) FROM tb_kartu_stok_gudang WHERE IDBarang='".$data->IDBarang."' AND Tanggal<='".$data->Tanggal."' AND IDStokGudang<'".$data->IDStokGudang."' GROUP BY IDBarang");
                        if(!$stokAwal) $stokAwal=0;
                        $stokAkhir = $stokAwal + $data->StokPenyesuaian;

                        if($stokAkhir<0 && $barang->StokGudang==0){
                            $stokAkhir = $barang->StokGudang;
                            $stokAwal = abs($data->StokPenyesuaian);
                        }
                        ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo $data->TanggalID; ?></td>
                            <td><?php echo $data->Nama." ".$SN; ?></td>
                            <td style="text-align:center"><?php echo number_format($stokAwal,2); ?></td>
                            <?php if($data->StokPenyesuaian>0){ ?>
                                <td style="text-align:center"><?php echo number_format($data->StokPenyesuaian,2); ?></td>
                                <td style="text-align:center"></td>
                            <?php } else { ?>
                                <td style="text-align:center"></td>
                                <td style="text-align:center"><?php echo number_format($data->StokPenyesuaian,2); ?></td>
                            <?php } ?>
                            <td style="text-align:center"><?php echo number_format($stokAkhir,2); ?></td>
                            <td><?php echo $NoPO; ?></td>
                            <td><?php echo $no_spb; ?></td>
                            <td style="text-align:center"><?php echo number_format($hpp,2); ?></td>
                            <td style="text-align:center"><?php echo number_format($harga_jual,2); ?></td>
                            <td><?php echo $Supplier; ?></td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='12'>Tidak ada data yang dapat ditampilkan</td></tr>";
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