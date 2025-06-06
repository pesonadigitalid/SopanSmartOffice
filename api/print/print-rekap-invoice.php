<?php
session_start();
include_once "../config/connection.php";
$bulan = array("01"=>"Januari","02"=>"Februari","03"=>"Maret","04"=>"April","05"=>"Mei","06"=>"Juni","07"=>"Juli","08"=>"Agustus","09"=>"September",10=>"Oktober","11"=>"November",12=>"Desember");

$datestart = antiSQLInjection($_GET['datestart']);
$expstart = explode("/",$datestart);
$datestartExp = $expstart[2]."-".$expstart[1]."-".$expstart[0];

$dateend = antiSQLInjection($_GET['dateend']);
$expend = explode("/",$dateend);
$dateendExp = $expend[2]."-".$expend[1]."-".$expend[0];

$kode_proyek = antiSQLInjection($_GET['kode_proyek']);
$departement = antiSQLInjection($_GET['departement']);
$tipe_invoice = antiSQLInjection($_GET['tipe_invoice']);

$pelanggan = antiSQLInjection($_GET['pelanggan']);

if ($datestart != "" && $dateend != "") {
    $cond = "WHERE Tanggal BETWEEN '$datestartExp' AND '$dateendExp' ";
    $subtitle = "Periode. ".$datestart." - ".$dateend;
} else if ($datestart != "") {
    $cond = "WHERE Tanggal='$datestartExp' ";
    $subtitle = "Periode. ".$datestart;
} else {
    $cond = "WHERE DATE_FORMAT(Tanggal,'%Y-%m') = '" . date("Y-m") . "' ";
    $subtitle = "Periode : ".$bulan[date("m")]." ".date("Y");
}

if ($kode_proyek != "")
    $cond .= " AND IDProyek='$kode_proyek' ";
else if ($pelanggan != "")
    $cond .= " AND IDProyek IN (SELECT IDProyek FROM tb_proyek WHERE IDClient='$pelanggan') ";

if($departement != "")
    $cond .= " AND IDProyek IN (SELECT IDProyek FROM tb_proyek WHERE IDDepartement='$departement') ";
    
if($tipe_invoice=="INVOICE PPN")
    //tb_proyek.PPNPersen>'0' 
    $cond .= " AND PPNPersen>'0' ";
else if($tipe_invoice=="INVOICE NON-PPN")
    //tb_proyek.PPNPersen='0' 
    $cond .= " AND PPNPersen='0' ";
    
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
            <h1 class="underline">** LAPORAN REKAP INVOICE **</h1><?php echo $subtitle; ?>
        </div>
        <table class="tabelList2" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th style="width:20px">No</th>
                    <th style="width:80px">No Invoice</th>
                    <th style="width:70px">Tanggal</th>
                    <th style="width:80px">Faktur Pajak</th>
                    <th style="width:80px">No.Proyek</th>
                    <th style="width:80px">Kepada</th>
                    <th>Keterangan</th>
                    <th style="width:100px">Nominal Invoice</th>
                    <th style="width:100px">Terbayar</th>
                    <th style="width:80px">No Bukti</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = newQuery("get_results","SELECT *,DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID, DATE_FORMAT(JatuhTempo, '%d/%m/%Y') AS JatuhTempoID FROM tb_penjualan_invoice $cond ORDER BY Tanggal ASC");
                if($query){
                    $i=1;
                    $t_GrandTotal = 0;
                    $t_Sisa = 0;
                    foreach($query as $data){
                        $proyek = newQuery("get_row","SELECT * FROM tb_proyek WHERE IDProyek='".$data->IDProyek."'");
                        $pelanggan = newQuery("get_row","SELECT * FROM tb_pelanggan WHERE IDPelanggan='".$proyek->IDClient."'");
                        $no_bukti = newQuery("get_var","SELECT NoBukti FROM tb_jurnal WHERE NoRef='".$data->IDInvoice."' AND Tipe='1'");
                        ?>
                        <tr>
                            <td style="text-align: center;"><?php echo $i; ?></td>
                            <td><?php echo $data->NoInv; ?></td>
                            <td><?php echo $data->TanggalID; ?></td>
                            <td><?php echo $data->NoFakturPajak; ?></td>
                            <td><?php echo $proyek->KodeProyek."/".$proyek->Tahun; ?></td>
                            <td><?php echo $pelanggan->NamaPelanggan; ?></td>
                            <td><?php echo $data->Keterangan; ?></td>
                            <td style="text-align: right;"><?php echo number_format($data->GrandTotal); ?></td>
                            <td style="text-align: right;"><?php echo number_format(($data->GrandTotal-$data->Sisa)); ?></td>
                            <td style="text-align: center;"><?php echo $no_bukti; ?></td>
                        </tr>
                        <?php
                        $i++;
                        $t_GrandTotal += $data->GrandTotal;
                        $t_Sisa += ($data->GrandTotal-$data->Sisa);
                    }
                } else {
                    echo "<tr><td colspan='8'>Tidak ada data yang dapat ditampilkan...</td></tr>";
                }
                ?>
                <tr>
                    <td colspan="7" style="text-align: right;"><strong>Total Invoice:</strong></td>
                    <td style="text-align: right;"><?php echo number_format($t_GrandTotal);?></td>
                    <td style="text-align: right;"><?php echo number_format($t_Sisa);?></td>
                    <td style="text-align: right;"></td>
                </tr>
            </tbody>
        </table>
        <table class="asignment" style="margin-top: 20px;">
            <tr>
                <td class="center" width="60%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td class="center" width="40%">Mengetahui,<br /><br /><br /><br />( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</td>
            </tr>
        </table>
        <script type="text/javascript">
            window.onload = function () { window.print(); }
        </script>
    </body>
</html>
</body>
</html>