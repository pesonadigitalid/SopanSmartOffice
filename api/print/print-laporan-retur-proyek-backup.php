<?php
session_start();
include_once "../config/connection.php";
$bulan = array("01"=>"Januari","02"=>"Februari","03"=>"Maret","04"=>"April","05"=>"Mei","06"=>"Juni","07"=>"Juli","08"=>"Agustus","09"=>"September",10=>"Oktober","11"=>"November",12=>"Desember");

$id_proyek = $_GET['id_proyek'];
$datestart = $_GET['datestart'];
$expstart = explode("/",$datestart);
$datestartExp = $expstart[2]."-".$expstart[1]."-".$expstart[0];

$dateend = $_GET['dateend'];
$expend = explode("/",$dateend);
$dateendExp = $expend[2]."-".$expend[1]."-".$expend[0];

if ($datestart != "" && $dateend != "") {
    $cond = " Tanggal BETWEEN '$datestartExp' AND '$dateendExp'";
    $subtitle = "Periode. ".$datestart." - ".$dateend;
} else if ($datestart != "") {
    $cond = " Tanggal='$datestartExp'";
    $subtitle = "Periode. ".$datestart;
} else {
    $cond = "DATE_FORMAT(Tanggal,'%Y-%m') = '" . date("Y-m") . "'";
    $subtitle = "Periode : ".$bulan[date("m")]." ".date("Y");
}

if($id_proyek>0){
    $cond2 = "WHERE IDProyek='$id_proyek'";
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
        <style>
        table, tr, td, th, tbody, thead, tfoot {
            page-break-inside: avoid !important;
        }
        body {
        counter-reset: section;
        }

        @page {
            @bottom-left {
                content: counter(page);
             }
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
            <h1 class="underline">** LAPORAN RETUR BARANG PROYEK **</h1>
            <?php echo $subtitle; ?>
        </div>
        <?php
        $qDepartement = newQuery("get_results","SELECT * FROM tb_proyek $cond2");
        if($qDepartement){
            foreach($qDepartement as $dDepartement){
                $i = 0;
                $numKar = newQuery("get_var","SELECT COUNT(*) FROM tb_audit WHERE $cond AND IDProyek='".$dDepartement->IDProyek."'");
                if($numKar>0 || $id_proyek==$dDepartement->IDProyek){
                    $totalNilai = 0;
                    $totalQty = 0;
                    ?>
                    <table class="tabelList6" cellpadding="0" cellspacing="0">
                        <thead>
                            <tr>
                                <th colspan="10" style="text-align: left">PROYEK : <?php echo $dDepartement->KodeProyek; ?>/<?php echo $dDepartement->Tahun; ?> - <?php echo $dDepartement->NamaProyek; ?></th>
                            </tr>
                            <tr>
                                <th width="5">No.</th>
                                <th width="70">Tanggal</th>
                                <th width="120">No. Audit/Retur</th>
                                <th width="80">Kode Barang</th>
                                <th>Barang</th>
                                <th width="50">Qty</th>
                                <th width="100">Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = newQuery("get_results","SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_audit WHERE $cond AND IDProyek='".$dDepartement->IDProyek."'");
                            if($query){
                                foreach($query as $data){
                                    $qDetail = newQuery("get_results","SELECT * FROM tb_audit_detail WHERE NoAudit='".$data->NoAudit."'");
                                    if($qDetail){
                                        foreach($qDetail as $dDetail){
                                            $i++;
                                            $dBarang = newQuery("get_row","SELECT * FROM tb_barang WHERE IDBarang='".$dDetail->IDBarang."'");
                                            $totalQty += $dDetail->SPGudang;
                                            $totalNilai += $dDetail->SubTotal
                                            ?>
                                            <tr>
                                                <td><?php echo $i; ?></td>
                                                <td><?php echo $data->TanggalID; ?></td>
                                                <td><?php echo $data->NoAudit; ?></td>
                                                <td><?php echo $dBarang->KodeBarang; ?></td>
                                                <td><?php echo $dBarang->Nama; ?></td>
                                                <td style="text-align: right"><?php echo number_format($dDetail->SPGudang,2); ?></td>
                                                <td style="text-align: right"><?php echo number_format($dDetail->SubTotal,2); ?></td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                }
                            } else {
                                echo "<tr><td colspan='7'>Tidak ada retur dalam proyek ini...</td></tr>";
                            }
                            ?>
                        </tbody>
                        <tfooter>
                            <tr>
                                <th colspan="5" style="text-align: right;">JUMLAH RETUR: </th>
                                <th style="text-align: right"><?php echo number_format($totalQty,2); ?></th>
                                <th style="text-align: right"><?php echo number_format($totalNilai,2); ?></th>
                            </tr>
                        </tfooter>
                    </table><br/>
                    <?php
                }
            }
        }
        ?>
        
        <table class="asignment" style="margin-top: 20px;">
            <tr>
                <td class="center" width="60%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td class="center" width="40%">Mengetahui,<br /><br /><br /><br />( Iluh )</td>
            </tr>
        </table>
        <script type="text/javascript">
            window.onload = function () { window.print(); }
        </script>
    </body>
</html>
</body>
</html>