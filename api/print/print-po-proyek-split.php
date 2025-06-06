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
    $cond = "tb_po.Tanggal BETWEEN '$datestartExp' AND '$dateendExp'";
    $subtitle = "Periode. ".$datestart." - ".$dateend;
} else if ($datestart != "") {
    $cond = "tb_po.Tanggal='$datestartExp'";
    $subtitle = "Periode. ".$datestart;
} else {
    $cond = "DATE_FORMAT(tb_po.Tanggal,'%Y-%m') = '" . date("Y-m") . "'";
    $subtitle = "Periode : ".$bulan[date("m")]." ".date("Y");
}

$jenispo = $_GET['jenispo'];
if($jenispo != ""){
    $cond .= " AND tb_po.JenisPO='$jenispo'";
    if($jenispo=="1") $subjenis = "PO MATERIAL";
    else if($jenispo=="2") $subjenis = "PO TENAGA/SUBKON";
    else if($jenispo=="3") $subjenis = "PO OVERHEAD";
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
            <h1 class="underline">** LAPORAN BELANJA PO PROYEK <?PHP echo $subjenis; ?>**</h1><?php echo $subtitle; ?>
        </div>
        <table class="tabelList2" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th width="20">NO</th>
                    <th width="100">KODE PROYEK</th>
                    <th>NAMA</th>
                    <th width="120">PO PPN</th>
                    <th width="120">PO NON-PPN</th>
                    <th width="120">TOTAL BELANJA</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = newQuery("get_results","SELECT DISTINCT(`tb_proyek`.IDProyek), `tb_proyek`.`KodeProyek` , `tb_proyek`.`Tahun` , `tb_proyek`.`NamaProyek`
                FROM
                    `lintasdayadb`.`tb_po`
                    INNER JOIN `lintasdayadb`.`tb_proyek` 
                        ON (`tb_po`.`IDProyek` = `tb_proyek`.`IDProyek`)
                WHERE $cond AND `tb_po`.IsLD = '1'
                GROUP BY tb_po.`IDProyek` ORDER BY `tb_proyek`.`Tahun` ASC, `tb_proyek`.`KodeProyek` ASC");
                if($query){
                    $i=0;
                    $total = 0;
                    foreach($query as $data){
                        $i++;
                        $ppn = newQuery("get_var","SELECT SUM(`tb_po`.`GrandTotal`) AS TotalBelanja FROM `lintasdayadb`.`tb_po` WHERE $cond AND `tb_po`.IsLD = '1' AND `tb_po`.IsPajak = '1' AND `tb_po`.IDProyek = '".$data->IDProyek."'");
                        if(!$ppn) $ppn=0;
                        $nonppn = newQuery("get_var","SELECT SUM(`tb_po`.`GrandTotal`) AS TotalBelanja FROM `lintasdayadb`.`tb_po` WHERE $cond AND `tb_po`.IsLD = '1' AND `tb_po`.IsPajak = '0' AND `tb_po`.IDProyek = '".$data->IDProyek."'");
                        if(!$nonppn) $nonppn=0;
                        $total += $ppn+$nonppn;
                        ?>
                        <tr>
                            <td style="text-align: center;"><?php echo $i; ?></td>
                            <td style="text-align: center;"><?php echo $data->KodeProyek."/".$data->Tahun; ?></td>
                            <td><?php echo $data->NamaProyek; ?></td>
                            <td style="text-align: right;">Rp. <?php echo number_format($ppn,2); ?></td>
                            <td style="text-align: right;">Rp. <?php echo number_format($nonppn,2); ?></td>
                            <td style="text-align: right;">Rp. <?php echo number_format(($ppn+$nonppn),2); ?></td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='6'>Tidak ada data yang dapat ditampilkan...</td></tr>";
                }
                ?>
                <tr>
                    <td colspan="5" style="text-align: right;"><strong>TOTAL BELANJA :</strong></td>
                    <td style="text-align: right;"><?php echo number_format($total,2);?></td>
                </tr>
            </tbody>
        </table>
        <table class="asignment" style="margin-top: 20px;">
            <tr>
                <td class="center" width="60%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td class="center" width="40%">Mengetahui,<br /><br /><br /><br />( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</td>
            </tr>
        </table>
        <script type="text/javascript">
            window.onload = function () { window.print(); }
        </script>
    </body>
</html>
</body>
</html>