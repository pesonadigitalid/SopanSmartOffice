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

$tipe = $_GET['tipe'];

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

if($tipe=="108") {
    $jenis = "Wika";
    $subtitle .= ". Khusus Produk Wika";
} else if($tipe=="114") {
    $jenis = "Asialing";
    $subtitle .= ". Khusus Produk Asialing";
} else if($tipe=="109") {
    $jenis = "Voksel";
    $subtitle .= ". Khusus Produk Voksel";
} else if($tipe=="205") {
    $jenis = "Mitsubishi";
    $subtitle .= ". Khusus Produk Mitsubishi";
} else if($tipe=="198") {
    $jenis = "Daikin";
    $subtitle .= ". Khusus Produk Daikin";
} else {
    if(trim($tipe)=="Sparepart") $tipe="Sparepart & Service";
    if(trim($tipe)=="Instalasi Plumbing") $tipe="Instalasi Plumbing & Elektrikal";
    $jenis = $tipe;
    $subtitle .= ". Khusus ".$tipe;
}

if($jenis!="Sparepart & Service" && $jenis!="Instalasi Plumbing & Elektrikal"){
    $cond2 = "AND (b.IDBarang IN (SELECT IDBarang FROM tb_barang WHERE IDSupplier='$tipe'))";
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
            <h1 class="underline">** REKAP SPB **</h1><?php echo $subtitle; ?>
        </div>
        <table class="tabelList2" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th width="10">NO.</th>
                    <th width="100">TANGGAL</th>
                    <th width="150">SPB</th>
                    <th>ITEM</th>
                    <th width="150">PELANGGAN</th>
                    <th width="100">TOTAL</th>
                    <th width="100">MARKETING</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // $query = newQuery("get_results","SELECT b.NoPenjualan, b.IDBarang FROM tb_penjualan_detail b, tb_penjualan a WHERE 
                //                     a.`NoPenjualan`=b.`NoPenjualan` AND
                //                     (a.Tanggal BETWEEN '$datestartExp' AND '$dateendExp') AND
                //                     a.Jenis='$jenis' AND 
                //                     (b.IDBarang IN (SELECT IDBarang FROM tb_barang WHERE IDSupplier='$tipe')) GROUP BY b.NoPenjualan ORDER BY b.NoPenjualan, b.NoUrut");
                $query = newQuery("get_results","SELECT b.NoPenjualan, b.IDBarang FROM tb_penjualan_detail b, tb_penjualan a WHERE 
                                    a.`NoPenjualan`=b.`NoPenjualan` AND
                                    (a.Tanggal BETWEEN '$datestartExp' AND '$dateendExp') AND
                                    a.Jenis='$jenis' $cond2 AND a.DeletedDate IS NULL GROUP BY b.NoPenjualan ORDER BY b.NoPenjualan, b.NoUrut");
                if($query){
                    $i=1;
                    $total = 0;
                    foreach($query as $data){
                        $dataSPB = newQuery("get_row","SELECT a.*, b.NamaPelanggan, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_penjualan a, tb_pelanggan b WHERE a.IDPelanggan=b.IDPelanggan AND a.NoPenjualan='".$data->NoPenjualan."'");
                        $firstBarang = newQuery("get_row","SELECT * FROM tb_penjualan_detail WHERE NoPenjualan='".$data->NoPenjualan."' AND IDBarang IN (SELECT IDBarang FROM tb_barang WHERE IDSupplier='$tipe') ORDER BY NoUrut ASC");
                        if(!$firstBarang) $firstBarang = newQuery("get_row","SELECT a.* FROM tb_penjualan_detail a, tb_barang b WHERE a.IDBarang=b.IDBarang AND a.NoPenjualan='".$data->NoPenjualan."' AND b.IsBarang='2' ORDER BY NoUrut ASC");
                        if(!$firstBarang) $firstBarang = newQuery("get_row","SELECT a.* FROM tb_penjualan_detail a, tb_barang b WHERE a.IDBarang=b.IDBarang AND a.NoPenjualan='".$data->NoPenjualan."' ORDER BY NoUrut ASC");
                        $barang = newQuery("get_row","SELECT * FROM tb_barang WHERE IDBarang='".$firstBarang->IDBarang."'");
                        $karyawan = newQuery("get_row","SELECT * FROM tb_karyawan WHERE IDKaryawan='".$dataSPB->CreatedBy."'");
                        $jenis = newQuery("get_var","SELECT Nama FROM tb_jenis_material WHERE IDMaterial='".$barang->IDJenis."'");

                        $exp = explode("/", $dataSPB->NoPenjualan);
                        $total += $dataSPB->GrandTotal;
                        ?>
                        <tr>
                            <td><?php if($dataSPB) echo $i; ?></td>
                            <td style="text-align: center;"><?php echo $dataSPB->TanggalID; ?></td>
                            <td><?php echo $dataSPB->NoPenjualan; ?></td>
                            <td><?php echo $barang->Nama; ?></td>
                            <td><?php echo $dataSPB->NamaPelanggan; ?></td>
                            <td style="text-align: right;"><?php if($dataSPB) echo number_format($dataSPB->GrandTotal, 2); else echo "-"; ?></td>
                            <td style="text-align: center;"><?php echo $exp[1]; ?></td>
                        </tr>
                        <?php
                        if($dataSPB) $i++;
                    }
                    ?>
                    <tr class="highlight">
                        <td colspan='5' style="text-align: right;"><strong>Total Penjualan : </strong></td>
                        <td style="text-align: right;"><?php echo number_format($total,2);?></td>
                        <td style="text-align: right;"></td>
                    </tr>
                    <?php
                } else {
                    echo "<tr><td colspan='7'>Tidak ada data yang dapat ditampilkan</td></tr>";
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