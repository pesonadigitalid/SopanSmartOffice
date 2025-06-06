<?php
session_start();
include_once "../config/connection.php";

function changeMonthNameID($val){
        switch($val){
            case "1":
                $return = "Januari";
                break;
            case "2":
                $return = "Februari";
                break;
            case "3":
                $return = "Maret";
                break;
            case "4":
                $return = "April";
                break;
            case "5":
                $return = "Mei";
                break;
            case "6":
                $return = "Juni";
                break;
            case "7":
                $return = "Juli";
                break;
            case "8":
                $return = "Agustus";
                break;
            case "9":
                $return = "September";
                break;
            case "10":
                $return = "Oktober";
                break;
            case "11":
                $return = "November";
                break;
            case "12":
                $return = "Desember";
                break;
        }
        
        switch($val){
            case "January":
                $return = "Januari";
                break;
            case "February":
                $return = "Februari";
                break;
            case "March":
                $return = "Maret";
                break;
            case "April":
                $return = "April";
                break;
            case "Mey":
                $return = "Mei";
                break;
            case "June":
                $return = "Juni";
                break;
            case "July":
                $return = "Juli";
                break;
            case "August":
                $return = "Agustus";
                break;
            case "September":
                $return = "September";
                break;
            case "October":
                $return = "Oktober";
                break;
            case "November":
                $return = "November";
                break;
            case "December":
                $return = "Desember";
                break;
        }
        
        return $return;
    }

$bulan = ($_GET['bulan']);
$tahun = ($_GET['tahun']);
$departement = 4;

$status = 1;
if($bulan=="" && $tahun==""){
    $bulan = date("m");
    $tahun = date("Y");
}

$dName = $db->get_var("SELECT NamaDepartement FROM tb_departement WHERE IDDepartement='$departement'");

$periode = $tahun."-".$bulan;
$condDate = " AND DATE_FORMAT(a.Tanggal,'%Y-%m')='$periode'";
$tanggal = $tahun."-".$bulan."-01";
$tanggalID = "01/".$bulan."/".$tahun;
$tanggalDisplay = changeMonthNameID($bulan)." ".$tahun;

function CheckSaldo($IDRekening,$condDate,$departement,$tipe){
    global  $db;
    $total = 0;
    $queryDetail = $db->get_results("SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDDepartement`='$departement' AND a.`IDRekening`='".$IDRekening."' $condDate ORDER BY Tanggal ASC, JurnalRef ASC");
    if($queryDetail){
        foreach($queryDetail as $dataDetail){
            if($tipe=="KREDIT")
                $total += $dataDetail->Kredit;
            else
                $total += $dataDetail->Debet;
        }
    }
    if($total>0) return ""; else return number_format($total, 2);
}

?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content=""/>
    <meta name="author" content="Yogi Pratama - Pesona Creative - 085737654543"/>
    <title>Laporan Laba Rugi MMS</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport"/>
    <link rel="stylesheet" href="print-style-acc.css" media="all"/>
</head>

<body class="center">
    <h1 class="blue">Laba Rugi MMS</h1>
    <h3 class="red"><?php echo $tanggalDisplay; ?></h3>
    <table class="tbLabaRugi" style="max-width: 500px">
        <tr>
            <td width="400"></td>
            <td width="100" style="text-align: right;font-weight: bold;border-bottom: solid 1px #333;padding-bottom: 5px;" class="red">Saldo</td>
        </tr>
        <tr>
            <td class="labelHeader">Pendapatan</td>
            <td style="text-align: right;">Rupiah</td>
        </tr>
        <?php
        $labarugi = 0;
        $pendapatan = 0;
        $biaya = 0;
        $query = $db->get_results("SELECT * FROM `tb_master_rekening` WHERE IDRekening='153' ORDER BY NamaRekening ASC");
        if($query){
            foreach($query as $data){
                if($data->Tipe=="D"){
                    ?>
                    <tr>
                        <td class="labelHeader2 deep1"><?php echo $data->KodeRekening."&nbsp;&nbsp;&nbsp;".ucwords(strtolower($data->NamaRekening)); ?></td>
                        <td style="text-align: right;"><?php echo CheckSaldo($data->IDRekening,$condDate,$departement,"KREDIT"); ?></td>
                    </tr>
                    <?php
                    $queryDetail = $db->get_results("SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDDepartement`='$departement' AND a.`IDRekening`='".$data->IDRekening."' $condDate ORDER BY Tanggal ASC, JurnalRef ASC");
                    if($queryDetail){
                        foreach($queryDetail as $dataDetail){
                            $jurnal = newQuery("get_row","SELECT * FROM tb_jurnal WHERE IDJurnal='".$dataDetail->IDJurnal."'");
                            if($jurnal->NoRef!='' && $jurnal->Tipe=='4'){
                                $invoice = newQuery("get_row","SELECT * FROM tb_penjualan_invoice WHERE IDInvoice='".$jurnal->NoRef."'");
                                if($invoice){
                                    if($invoice->PPNPersen>0){
                                        $ppn = $dataDetail->Kredit*$invoice->PPNPersen/100;
                                        $totalppn += $ppn;
                                    }
                                }
                            }
                            $pendapatan += $dataDetail->Kredit;
                            ?>
                            <tr>
                                <td class="deep2">
                                    <table>
                                        <tr>
                                            <td width="80"><?php echo $dataDetail->TanggalID; ?></td>
                                            <td><?php echo $dataDetail->Keterangan; ?></td>
                                        </tr>
                                    </table>
                                </td>
                                <td style="text-align: right;"><?php echo number_format($dataDetail->Kredit,2); ?></td>
                            </tr>
                            <?php
                        }
                    }
                } else {
                    $querySub = $db->get_results("SELECT * FROM `tb_master_rekening` WHERE IDParent='".$data->IDRekening."' ORDER BY NamaRekening ASC");
                    if($querySub){
                        foreach($querySub as $dataSub){
                            if($dataSub->Tipe=="D"){
                                ?>
                                <tr>
                                    <td class="labelHeader2 deep1"><?php echo $dataSub->KodeRekening."&nbsp;".ucwords(strtolower($dataSub->NamaRekening)); ?></td>
                                    <td style="text-align: right;"><?php echo CheckSaldo($dataSub->IDRekening,$condDate,$departement,"KREDIT"); ?></td>
                                </tr>
                                <?php
                                $queryDetail = $db->get_results("SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDDepartement`='$departement' AND a.`IDRekening`='".$dataSub->IDRekening."' $condDate ORDER BY Tanggal ASC, JurnalRef ASC");
                                if($queryDetail){
                                    foreach($queryDetail as $dataDetail){
                                        $jurnal = newQuery("get_row","SELECT * FROM tb_jurnal WHERE IDJurnal='".$dataDetail->IDJurnal."'");
                                        if($jurnal->NoRef!='' && $jurnal->Tipe=='4'){
                                            $invoice = newQuery("get_row","SELECT * FROM tb_penjualan_invoice WHERE IDInvoice='".$jurnal->NoRef."'");
                                            if($invoice){
                                                if($invoice->PPNPersen>0){
                                                    $ppn = $dataDetail->Kredit*$invoice->PPNPersen/100;
                                                    $totalppn += $ppn;
                                                }
                                            }
                                        }
                                        $pendapatan += $dataDetail->Kredit;
                                        ?>
                                        <tr>
                                            <td class="deep2">
                                                <table>
                                                    <tr>
                                                        <td width="80"><?php echo $dataDetail->TanggalID; ?></td>
                                                        <td><?php echo $dataDetail->Keterangan; ?></td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td style="text-align: right;"><?php echo number_format($dataDetail->Kredit,2); ?></td>
                                        </tr>
                                        <?php
                                    }
                                }
                            }
                        }
                    }
                }
            }
        $pendapatanbruto = $pendapatan;
        $pendapatan = $pendapatanbruto-$totalppn;
            ?>
            <tr>
                <td class="labelHeader">Total Pendapatan</td>
                <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($pendapatanbruto,2); ?></td>
            </tr>
            <?php
        }
        ?>
        <tr>
            <td class="labelHeader">Pajak Pendapatan</td>
            <td style="text-align: right;"></td>
        </tr>
        <tr>
            <td class="deep1">Pajak Pendapatan Periode <?php echo $tanggalDisplay;?> </td>
            <td style="text-align: right;"><?php echo number_format($totalppn,2); ?></td>
        </tr>
        <tr>
            <td class="labelHeader">Total Pajak Pendapatan</td>
            <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($totalppn,2); ?></td>
        </tr>
        <tr>
            <td class="labelHeader">Total Total Pendapatan Bersih</td>
            <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($pendapatan,2); ?></td>
        </tr>
        <tr>
            <td class="labelHeader"></td>
            <td style="text-align: right;"></td>
        </tr>
        <tr>
            <td class="labelHeader">Biaya</td>
            <td style="text-align: right;"></td>
        </tr>
        <?php
        $query = $db->get_results("SELECT * FROM `tb_master_rekening` WHERE IDParent='73' ORDER BY NamaRekening ASC");
        if($query){
            foreach($query as $data){
                if($data->Tipe=="D"){
                    ?>
                    <tr>
                        <td class="labelHeader2 deep1"><?php echo $data->KodeRekening."&nbsp;&nbsp;&nbsp;".ucwords(strtolower($data->NamaRekening)); ?></td>
                        <td style="text-align: right;"><?php echo CheckSaldo($data->IDRekening,$condDate,$departement,"DEBET"); ?></td>
                    </tr>
                    <?php
                    $queryDetail = $db->get_results("SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDDepartement`='$departement' AND a.`IDRekening`='".$data->IDRekening."' $condDate ORDER BY Tanggal ASC, JurnalRef ASC");
                    if($queryDetail){
                        foreach($queryDetail as $dataDetail){
                            $biaya += $dataDetail->Debet;
                            ?>
                            <tr>
                                <td class="deep2">
                                    <table>
                                        <tr>
                                            <td width="80"><?php echo $dataDetail->TanggalID; ?></td>
                                            <td><?php echo $dataDetail->Keterangan; ?></td>
                                        </tr>
                                    </table>
                                </td>
                                <td style="text-align: right;"><?php echo number_format($dataDetail->Debet,2); ?></td>
                            </tr>
                            <?php
                        }
                    }
                } else {
                    $querySub = $db->get_results("SELECT * FROM `tb_master_rekening` WHERE IDParent='".$data->IDRekening."' ORDER BY NamaRekening ASC");
                    if($querySub){
                        foreach($querySub as $dataSub){
                            if($dataSub->Tipe=="D"){
                                ?>
                                <tr>
                                    <td class="labelHeader2 deep1"><?php echo $dataSub->KodeRekening."&nbsp;&nbsp;&nbsp;".ucwords(strtolower($dataSub->NamaRekening)); ?></td>
                                    <td style="text-align: right;"><?php echo CheckSaldo($dataSub->IDRekening,$condDate,$departement,"DEBET"); ?></td>
                                </tr>
                                <?php
                                $queryDetail = $db->get_results("SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal_detail a, tb_jurnal b WHERE a.`IDJurnal`=b.`IDJurnal` AND b.`IDDepartement`='$departement' AND a.`IDRekening`='".$dataSub->IDRekening."' $condDate ORDER BY Tanggal ASC, JurnalRef ASC");
                                if($queryDetail){
                                    foreach($queryDetail as $dataDetail){
                                        $biaya += $dataDetail->Debet;
                                        ?>
                                        <tr>
                                            <td class="deep2">
                                                <table>
                                                    <tr>
                                                        <td width="80"><?php echo $dataDetail->TanggalID; ?></td>
                                                        <td><?php echo $dataDetail->Keterangan; ?></td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td style="text-align: right;"><?php echo number_format($dataDetail->Debet,2); ?></td>
                                        </tr>
                                        <?php
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $labarugi = $pendapatan-$biaya;
            $total = $pendapatan+$biaya;
            ?>
            <tr>
                <td class="labelHeader">Total Biaya</td>
                <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($biaya,2); ?></td>
            </tr>
            <tr>
                <td class="labelHeader">Laba/Rugi</td>
                <td style="text-align: right;font-weight: bold;border-top: solid 1px #333;padding-bottom: 5px;" class="blue"><?php echo number_format($labarugi,2); ?></td>
            </tr>
            <?php
        }
        ?>
    </table><br><br>
    <div id="chartContainer" style="width:500px; height:500px; margin: 0 auto;"></div>
    <script type="text/javascript" src="../../themes/assets/plugins/jquery/jquery-1.11.1.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-3d.js"></script>
    <script type="text/javascript" >
    $(function () {
        Highcharts.chart('chartContainer', {
            chart: {
                type: 'pie',
                options3d: {
                    enabled: true,
                    alpha: 45
                }
            },
            title: {
                text: 'Grafik Laba Rugi'
            },
            plotOptions: {
                pie: {
                    innerSize: 100,
                    depth: 45
                }
            },
            series: [{
                name: 'Persentase (%)',
                data: [
                    ['Pendapatan', <?php echo round(($pendapatan/$total*100),2); ?>],
                    ['Biaya', <?php echo round(($biaya/$total*100),2); ?>]
                ]
            }],
            credits: {
              enabled: false
            }
        });
    });
    </script>
    <script type="text/javascript">
        setTimeout(function(){
            window.print();
        },1500);
    </script>
</body>
</html>