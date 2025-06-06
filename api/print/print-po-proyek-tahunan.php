<?php
session_start();
include_once "../config/connection.php";
$bulan = array("01"=>"Januari","02"=>"Februari","03"=>"Maret","04"=>"April","05"=>"Mei","06"=>"Juni","07"=>"Juli","08"=>"Agustus","09"=>"September","10"=>"Oktober","11"=>"November","12"=>"Desember");

$tahun = $_GET['tahun'];
$pajakpo = $_GET['pajakpo'];

if($pajakpo!="") $cond = " AND IsPajak='$pajakpo'";
else $cond = " AND (IsPajak='1' OR IsPajak='0')";

function returnQueryPOUmum($JenisPO, $Bulan){
    global $tahun;
    global $pajakpo;

    if($pajakpo!="") $cond = " AND IsPajak='$pajakpo'";
    else $cond = " AND (IsPajak='1' OR IsPajak='0')";
    
    return "SELECT sum(GrandTotal) FROM tb_po WHERE JenisPO='$JenisPO' AND IsLD='1' AND IDProyek='0' AND (IDDepartement='0' OR IDDepartement IS NULL) AND DATE_FORMAT(Tanggal,'%Y-%m')='$tahun-$Bulan' $cond";
}

function returnQueryPODepartement($JenisPO, $Departement, $Bulan){
    global $tahun;
    global $pajakpo;

    if($pajakpo!="") $cond = " AND IsPajak='$pajakpo'";
    else $cond = " AND (IsPajak='1' OR IsPajak='0')";
    
    return "SELECT sum(GrandTotal) FROM tb_po WHERE JenisPO='$JenisPO' AND IsLD='1' AND IDProyek='0' AND IDDepartement='".$Departement."' AND DATE_FORMAT(Tanggal,'%Y-%m')='$tahun-$Bulan' $cond";
}

function returnQueryTotalPODepartement($JenisPO, $Bulan){
    global $tahun;
    global $pajakpo;

    if($pajakpo!="") $cond = " AND IsPajak='$pajakpo'";
    else $cond = " AND (IsPajak='1' OR IsPajak='0')";
    
    return "SELECT sum(GrandTotal) FROM tb_po WHERE JenisPO='$JenisPO' AND IsLD='1' AND IDProyek='0' AND DATE_FORMAT(Tanggal,'%Y-%m')='$tahun-$Bulan' $cond";
}

function returnQueryPOProyek($JenisPO, $IDProyek, $Bulan){
    global $tahun;
    global $pajakpo;

    if($pajakpo!="") $cond = " AND IsPajak='$pajakpo'";
    else $cond = " AND (IsPajak='1' OR IsPajak='0')";
    
    return "SELECT sum(GrandTotal) FROM tb_po WHERE JenisPO='$JenisPO' AND IsLD='1' AND IDProyek='".$IDProyek."' AND DATE_FORMAT(Tanggal,'%Y-%m')='$tahun-$Bulan' $cond";
}

function returnQueryTotalPOProyek($JenisPO, $Bulan){
    global $tahun;
    global $pajakpo;

    if($pajakpo!="") $cond = " AND IsPajak='$pajakpo'";
    else $cond = " AND (IsPajak='1' OR IsPajak='0')";
    
    return "SELECT sum(GrandTotal) FROM tb_po WHERE JenisPO='$JenisPO' AND IsLD='1' AND IDProyek<>'0' AND DATE_FORMAT(Tanggal,'%Y-%m')='$tahun-$Bulan' $cond";
}

function returnQueryTotalPOPeriode($JenisPO, $Bulan){
    global $tahun;
    global $pajakpo;

    if($pajakpo!="") $cond = " AND IsPajak='$pajakpo'";
    else $cond = " AND (IsPajak='1' OR IsPajak='0')";
    
    return "SELECT sum(GrandTotal) FROM tb_po WHERE JenisPO='1' AND IsLD='1' AND DATE_FORMAT(Tanggal,'%Y-%m')='$tahun-$Bulan' $cond";
}

function returnQueryListProyekPeriod($BulanAwal, $BulanAkhir){
    global $tahun;
    global $pajakpo;

    if($pajakpo!="") $cond = " AND IsPajak='$pajakpo'";
    else $cond = " AND (IsPajak='1' OR IsPajak='0')";

    return "SELECT DISTINCT(a.IDProyek), b.KodeProyek, b.Tahun  FROM tb_po a, tb_proyek b WHERE a.IDProyek=b.IDProyek AND DATE_FORMAT(a.Tanggal,'%Y-%m') BETWEEN '$tahun-$BulanAwal' AND '$tahun-$BulanAkhir' $cond ORDER BY b.Tahun ASC, b.KodeProyek ASC";
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
        <link rel="stylesheet" href="print-style-acc.css" media="all" type="text/css"/>
        <style media="all">
        table td{
            font-size: 10px !important;
            line-height: 10px !important;
        }
        table thead td{
            font-size: 10px !important;
        }
        .jenisPO{
            padding-right: 10px;
            font-size:10px;
            border-bottom: solid 1px #ccc;
            text-align: center !important;
        }
        .labelHeader{
            font-size: 10px !important;
            width: 40px !important;
        }
        .highlight{
            background: #eee;
        }
        .highlight2{
            background: #ddd;
        }
        .highlight22{
            background: #ccc;
        }

        .right{
            text-align: right !important;
            padding-right:5px;
        }

        .bold{
            font-weight: bold;
        }
        .month{
            border-left: solid 1px #ccc;
        }
        .spacer {
            height: 5px !important;
        }
        </style>
    </head>

<body class="center">
<h1 class="blue newPage">Rekap PO Umum dan Proyek Tahunan</h1>
    <h3 class="red">Periode: Januari - April <?php echo $tahun; ?></h3>
    <table class="tbLabaRugi" style="width: auto">
    <thead>
    <tr>
        <td class="labelHeader" style="padding-right:20px">Periode</td>
        <?php
        for($i=1;$i<=4;$i++){
            if($i<10) $b = "0".$i; else $b = $i;
            if($i==1) $class=""; else $class="month";
            ?><td style="text-align:center;" colspan="4" class="bold <?php echo $class; ?>"><?php echo $bulan[$b]; ?></td><?php
        }
        ?>
    </tr>
    <tr>
        <td class="labelHeader" style="padding-right:20px">Jenis. PO</td>
        <?php
        for($i=1;$i<=4;$i++){
            if($i<10) $b = "0".$i; else $b = $i;
            ?>
            <td class="jenisPO">Material</td>
            <td class="jenisPO highlight">Tenaga</td>
            <td class="jenisPO">Overhead</td>
            <td class="jenisPO highlight bold">Total</td>
            <?php
        }
        ?>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="labelHeader" style="text-decoration: underline">PO. Umum</td>
        <td colspan="15"></td>
    </tr>
    <!-- PO UMUM -->
    <tr>
    <td class="labelHeader">Umum</td>
        <?php
        for($i=1;$i<=4;$i++){
            if($i<10) $b = "0".$i; else $b = $i;

            $poMaterial = newQuery("get_var", returnQueryPOUmum(1, $b));
            if(!$poMaterial) $poMaterial=0;
            $poTenaga = newQuery("get_var", returnQueryPOUmum(2, $b));
            if(!$poTenaga) $poTenaga=0;
            $poOverhead = newQuery("get_var", returnQueryPOUmum(3, $b));
            if(!$poOverhead) $poOverhead=0;

            $poTotal = $poMaterial+$poTenaga+$poOverhead;
            ?>
            <td class="right"><?php echo number_format($poMaterial,2); ?></td>
            <td class="right highlight"><?php echo number_format($poTenaga,2); ?></td>
            <td class="right"><?php echo number_format($poOverhead,2); ?></td>
            <td class="right highlight bold"><?php echo number_format($poTotal,2); ?></td>
            <?php
        }
        ?>
    </tr>
    <?php
    $query = newQuery("get_results","SELECT * FROM tb_departement ORDER BY NamaDepartement");
    if($query){
        foreach($query as $dataDepartement){
            ?>
            <tr>
            <td class="labelHeader"><?php echo $dataDepartement->NamaDepartement; ?></td>
                <?php
                for($i=1;$i<=4;$i++){
                    if($i<10) $b = "0".$i; else $b = $i;

                    $poMaterial = newQuery("get_var", returnQueryPODepartement(1, $dataDepartement->IDDepartement, $b));
                    if(!$poMaterial) $poMaterial=0;
                    $poTenaga = newQuery("get_var", returnQueryPODepartement(2, $dataDepartement->IDDepartement, $b));
                    if(!$poTenaga) $poTenaga=0;
                    $poOverhead = newQuery("get_var", returnQueryPODepartement(3, $dataDepartement->IDDepartement, $b));
                    if(!$poOverhead) $poOverhead=0;

                    $poTotal = $poMaterial+$poTenaga+$poOverhead;
                    ?>
                    <td class="right"><?php echo number_format($poMaterial,2); ?></td>
                    <td class="right highlight"><?php echo number_format($poTenaga,2); ?></td>
                    <td class="right"><?php echo number_format($poOverhead,2); ?></td>
                    <td class="right highlight bold"><?php echo number_format($poTotal,2); ?></td>
                    <?php
                }
                ?>
            </tr>
            <?php
        }
    }
    ?>
    <tr>
    <td class="labelHeader" style="text-decoration: underline">Total PO. Umum/Departement:</td>
    <?php
    for($i=1;$i<=4;$i++){
        if($i<10) $b = "0".$i; else $b = $i;

        $poMaterial = newQuery("get_var", returnQueryTotalPODepartement(1, $b));
        if(!$poMaterial) $poMaterial=0;
        $poTenaga = newQuery("get_var", returnQueryTotalPODepartement(2, $b));
        if(!$poTenaga) $poTenaga=0;
        $poOverhead = newQuery("get_var", returnQueryTotalPODepartement(3, $b));
        if(!$poOverhead) $poOverhead=0;

        $poTotal = $poMaterial+$poTenaga+$poOverhead;
        ?>
        <td class="right bold highlight2"><?php echo number_format($poMaterial,2); ?></td>
        <td class="right bold highlight2"><?php echo number_format($poTenaga,2); ?></td>
        <td class="right bold highlight2"><?php echo number_format($poOverhead,2); ?></td>
        <td class="right highlight22 bold" style="text-decoration: underline"><?php echo number_format($poTotal,2); ?></td>
        <?php
    }
    ?>
    </tr>
    <tr>
        <td class="labelHeader spacer" style="text-decoration: underline"></td>
        <td colspan="15"></td>
    </tr>
    <tr>
        <td class="labelHeader" style="text-decoration: underline">PO. Proyek</td>
        <td colspan="15"></td>
    </tr>
    <!-- PO Proyek -->
    <?php
    $query = newQuery("get_results", returnQueryListProyekPeriod("01","04"));
    if($query){
        foreach($query as $dataProyek){
            ?>
            <tr>
            <td class="labelHeader"><?php echo $dataProyek->KodeProyek."/".$dataProyek->Tahun; ?></td>
                <?php
                for($i=1;$i<=4;$i++){
                    if($i<10) $b = "0".$i; else $b = $i;

                    $poMaterial = newQuery("get_var", returnQueryPOProyek(1, $dataProyek->IDProyek, $b));
                    if(!$poMaterial) $poMaterial=0;
                    $poTenaga = newQuery("get_var", returnQueryPOProyek(2, $dataProyek->IDProyek, $b));
                    if(!$poTenaga) $poTenaga=0;
                    $poOverhead = newQuery("get_var", returnQueryPOProyek(3, $dataProyek->IDProyek, $b));
                    if(!$poOverhead) $poOverhead=0;

                    $poTotal = $poMaterial+$poTenaga+$poOverhead;
                    ?>
                    <td class="right"><?php echo number_format($poMaterial,2); ?></td>
                    <td class="right highlight"><?php echo number_format($poTenaga,2); ?></td>
                    <td class="right"><?php echo number_format($poOverhead,2); ?></td>
                    <td class="right highlight bold"><?php echo number_format($poTotal,2); ?></td>
                    <?php
                }
                ?>
            </tr>
            <?php
        }
    }
    ?>
    <tr>
    <td class="labelHeader" style="text-decoration: underline">Total PO. Proyek:</td>
    <?php
    for($i=1;$i<=4;$i++){
        if($i<10) $b = "0".$i; else $b = $i;

        $poMaterial = newQuery("get_var", returnQueryTotalPOProyek(1, $b));
        if(!$poMaterial) $poMaterial=0;
        $poTenaga = newQuery("get_var", returnQueryTotalPOProyek(2, $b));
        if(!$poTenaga) $poTenaga=0;
        $poOverhead = newQuery("get_var", returnQueryTotalPOProyek(3, $b));
        if(!$poOverhead) $poOverhead=0;

        $poTotal = $poMaterial+$poTenaga+$poOverhead;
        ?>
        <td class="right bold highlight2"><?php echo number_format($poMaterial,2); ?></td>
        <td class="right bold highlight2"><?php echo number_format($poTenaga,2); ?></td>
        <td class="right bold highlight2"><?php echo number_format($poOverhead,2); ?></td>
        <td class="right highlight22 bold" style="text-decoration: underline"><?php echo number_format($poTotal,2); ?></td>
        <?php
    }
    ?>
    </tr>
    <tr>
        <td class="labelHeader spacer" style="text-decoration: underline"></td>
        <td colspan="15"></td>
    </tr>
    <tr>
    <td class="labelHeader" style="text-decoration: underline">Total  PO. Jan-Apr :</td>
    <?php
    for($i=1;$i<=4;$i++){
        if($i<10) $b = "0".$i; else $b = $i;

        $poMaterial = newQuery("get_var", returnQueryTotalPOPeriode(1, $b));
        if(!$poMaterial) $poMaterial=0;
        $poTenaga = newQuery("get_var", returnQueryTotalPOPeriode(2, $b));
        if(!$poTenaga) $poTenaga=0;
        $poOverhead = newQuery("get_var", returnQueryTotalPOPeriode(3, $b));
        if(!$poOverhead) $poOverhead=0;

        $poTotal = $poMaterial+$poTenaga+$poOverhead;
        ?>
        <td class="right bold "><?php echo number_format($poMaterial,2); ?></td>
        <td class="right bold "><?php echo number_format($poTenaga,2); ?></td>
        <td class="right bold "><?php echo number_format($poOverhead,2); ?></td>
        <td class="right highlight22 bold" style="text-decoration: underline"><?php echo number_format($poTotal,2); ?></td>
        <?php
    }
    ?>
    </tr>
    </tbody>
    </table>


    <h1 class="blue newPage">Rekap PO Umum dan Proyek Tahunan</h1>
    <h3 class="red">Periode: Mei - Agustus <?php echo $tahun; ?></h3>
    <table class="tbLabaRugi" style="width: auto">
    <thead>
    <tr>
        <td class="labelHeader" style="padding-right:20px">Periode</td>
        <?php
        for($i=5;$i<=8;$i++){
            if($i<10) $b = "0".$i; else $b = $i;
            if($i==5) $class=""; else $class="month";
            ?><td style="text-align:center;" colspan="4" class="bold <?php echo $class; ?>"><?php echo $bulan[$b]; ?></td><?php
        }
        ?>
    </tr>
    <tr>
        <td class="labelHeader" style="padding-right:20px">Jenis. PO</td>
        <?php
        for($i=5;$i<=8;$i++){
            if($i<10) $b = "0".$i; else $b = $i;
            ?>
            <td class="jenisPO">Material</td>
            <td class="jenisPO highlight">Tenaga</td>
            <td class="jenisPO">Overhead</td>
            <td class="jenisPO highlight bold">Total</td>
            <?php
        }
        ?>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="labelHeader" style="text-decoration: underline">PO. Umum</td>
        <td colspan="15"></td>
    </tr>
    <!-- PO UMUM -->
    <tr>
    <td class="labelHeader">Umum</td>
        <?php
        for($i=5;$i<=8;$i++){
            if($i<10) $b = "0".$i; else $b = $i;

            $poMaterial = newQuery("get_var",returnQueryPOUmum(1, $b));
            if(!$poMaterial) $poMaterial=0;
            $poTenaga = newQuery("get_var", returnQueryPOUmum(2, $b));
            if(!$poTenaga) $poTenaga=0;
            $poOverhead = newQuery("get_var", returnQueryPOUmum(3, $b));
            if(!$poOverhead) $poOverhead=0;

            $poTotal = $poMaterial+$poTenaga+$poOverhead;
            ?>
            <td class="right"><?php echo number_format($poMaterial,2); ?></td>
            <td class="right highlight"><?php echo number_format($poTenaga,2); ?></td>
            <td class="right"><?php echo number_format($poOverhead,2); ?></td>
            <td class="right highlight bold"><?php echo number_format($poTotal,2); ?></td>
            <?php
        }
        ?>
    </tr>
    <?php
    $query = newQuery("get_results","SELECT * FROM tb_departement ORDER BY NamaDepartement");
    if($query){
        foreach($query as $dataDepartement){
            ?>
            <tr>
            <td class="labelHeader"><?php echo $dataDepartement->NamaDepartement; ?></td>
                <?php
                for($i=5;$i<=8;$i++){
                    if($i<10) $b = "0".$i; else $b = $i;

                    $poMaterial = newQuery("get_var", returnQueryPODepartement(1, $dataDepartement->IDDepartement, $b));
                    if(!$poMaterial) $poMaterial=0;
                    $poTenaga = newQuery("get_var", returnQueryPODepartement(2, $dataDepartement->IDDepartement, $b));
                    if(!$poTenaga) $poTenaga=0;
                    $poOverhead = newQuery("get_var", returnQueryPODepartement(3, $dataDepartement->IDDepartement, $b));
                    if(!$poOverhead) $poOverhead=0;

                    $poTotal = $poMaterial+$poTenaga+$poOverhead;
                    ?>
                    <td class="right"><?php echo number_format($poMaterial,2); ?></td>
                    <td class="right highlight"><?php echo number_format($poTenaga,2); ?></td>
                    <td class="right"><?php echo number_format($poOverhead,2); ?></td>
                    <td class="right highlight bold"><?php echo number_format($poTotal,2); ?></td>
                    <?php
                }
                ?>
            </tr>
            <?php
        }
    }
    ?>
    <tr>
    <td class="labelHeader" style="text-decoration: underline">Total PO. Umum/Departement:</td>
    <?php
    for($i=5;$i<=8;$i++){
        if($i<10) $b = "0".$i; else $b = $i;

        $poMaterial = newQuery("get_var", returnQueryTotalPODepartement(1, $b));
        if(!$poMaterial) $poMaterial=0;
        $poTenaga = newQuery("get_var", returnQueryTotalPODepartement(2, $b));
        if(!$poTenaga) $poTenaga=0;
        $poOverhead = newQuery("get_var", returnQueryTotalPODepartement(3, $b));
        if(!$poOverhead) $poOverhead=0;

        $poTotal = $poMaterial+$poTenaga+$poOverhead;
        ?>
        <td class="right bold highlight2"><?php echo number_format($poMaterial,2); ?></td>
        <td class="right bold highlight2"><?php echo number_format($poTenaga,2); ?></td>
        <td class="right bold highlight2"><?php echo number_format($poOverhead,2); ?></td>
        <td class="right highlight22 bold" style="text-decoration: underline"><?php echo number_format($poTotal,2); ?></td>
        <?php
    }
    ?>
    </tr>
    <tr>
        <td class="labelHeader spacer" style="text-decoration: underline"></td>
        <td colspan="15"></td>
    </tr>
    <tr>
        <td class="labelHeader" style="text-decoration: underline">PO. Proyek</td>
        <td colspan="15"></td>
    </tr>
    <!-- PO Proyek -->
    <?php
    $query = newQuery("get_results", returnQueryListProyekPeriod("05","08"));
    if($query){
        foreach($query as $dataProyek){
            ?>
            <tr>
            <td class="labelHeader"><?php echo $dataProyek->KodeProyek."/".$dataProyek->Tahun; ?></td>
                <?php
                for($i=5;$i<=8;$i++){
                    if($i<10) $b = "0".$i; else $b = $i;

                    $poMaterial = newQuery("get_var", returnQueryPOProyek(1, $dataProyek->IDProyek, $b));
                    if(!$poMaterial) $poMaterial=0;
                    $poTenaga = newQuery("get_var", returnQueryPOProyek(2, $dataProyek->IDProyek, $b));
                    if(!$poTenaga) $poTenaga=0;
                    $poOverhead = newQuery("get_var", returnQueryPOProyek(3, $dataProyek->IDProyek, $b));
                    if(!$poOverhead) $poOverhead=0;

                    $poTotal = $poMaterial+$poTenaga+$poOverhead;
                    ?>
                    <td class="right"><?php echo number_format($poMaterial,2); ?></td>
                    <td class="right highlight"><?php echo number_format($poTenaga,2); ?></td>
                    <td class="right"><?php echo number_format($poOverhead,2); ?></td>
                    <td class="right highlight bold"><?php echo number_format($poTotal,2); ?></td>
                    <?php
                }
                ?>
            </tr>
            <?php
        }
    }
    ?>
    <tr>
    <td class="labelHeader" style="text-decoration: underline">Total PO. Proyek:</td>
    <?php
    for($i=5;$i<=8;$i++){
        if($i<10) $b = "0".$i; else $b = $i;

        $poMaterial = newQuery("get_var", returnQueryTotalPOProyek(1, $b));
        if(!$poMaterial) $poMaterial=0;
        $poTenaga = newQuery("get_var", returnQueryTotalPOProyek(2, $b));
        if(!$poTenaga) $poTenaga=0;
        $poOverhead = newQuery("get_var", returnQueryTotalPOProyek(3, $b));
        if(!$poOverhead) $poOverhead=0;

        $poTotal = $poMaterial+$poTenaga+$poOverhead;
        ?>
        <td class="right bold highlight2"><?php echo number_format($poMaterial,2); ?></td>
        <td class="right bold highlight2"><?php echo number_format($poTenaga,2); ?></td>
        <td class="right bold highlight2"><?php echo number_format($poOverhead,2); ?></td>
        <td class="right highlight22 bold" style="text-decoration: underline"><?php echo number_format($poTotal,2); ?></td>
        <?php
    }
    ?>
    </tr>
    <tr>
        <td class="labelHeader spacer" style="text-decoration: underline"></td>
        <td colspan="15"></td>
    </tr>
    <tr>
    <td class="labelHeader" style="text-decoration: underline">Total PO. Mei-Agst :</td>
    <?php
    for($i=5;$i<=8;$i++){
        if($i<10) $b = "0".$i; else $b = $i;

        $poMaterial = newQuery("get_var", returnQueryTotalPOPeriode(1, $b));
        if(!$poMaterial) $poMaterial=0;
        $poTenaga = newQuery("get_var", returnQueryTotalPOPeriode(2, $b));
        if(!$poTenaga) $poTenaga=0;
        $poOverhead = newQuery("get_var", returnQueryTotalPOPeriode(3, $b));
        if(!$poOverhead) $poOverhead=0;

        $poTotal = $poMaterial+$poTenaga+$poOverhead;
        ?>
        <td class="right bold "><?php echo number_format($poMaterial,2); ?></td>
        <td class="right bold "><?php echo number_format($poTenaga,2); ?></td>
        <td class="right bold "><?php echo number_format($poOverhead,2); ?></td>
        <td class="right highlight22 bold" style="text-decoration: underline"><?php echo number_format($poTotal,2); ?></td>
        <?php
    }
    ?>
    </tr>
    </tbody>
    </table>


    <h1 class="blue newPage">Rekap PO Umum dan Proyek Tahunan</h1>
    <h3 class="red">Periode: Oktober - Desember <?php echo $tahun; ?></h3>
    <table class="tbLabaRugi" style="width: auto">
    <thead>
    <tr>
        <td class="labelHeader" style="padding-right:20px">Periode</td>
        <?php
        for($i=9;$i<=12;$i++){
            if($i<10) $b = "0".$i; else $b = $i;
            if($i==5) $class=""; else $class="month";
            ?><td style="text-align:center;" colspan="4" class="bold <?php echo $class; ?>"><?php echo $bulan[$b]; ?></td><?php
        }
        ?>
    </tr>
    <tr>
        <td class="labelHeader" style="padding-right:20px">Jenis. PO</td>
        <?php
        for($i=9;$i<=12;$i++){
            if($i<10) $b = "0".$i; else $b = $i;
            ?>
            <td class="jenisPO">Material</td>
            <td class="jenisPO highlight">Tenaga</td>
            <td class="jenisPO">Overhead</td>
            <td class="jenisPO highlight bold">Total</td>
            <?php
        }
        ?>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="labelHeader" style="text-decoration: underline">PO. Umum</td>
        <td colspan="15"></td>
    </tr>
    <!-- PO UMUM -->
    <tr>
    <td class="labelHeader">Umum</td>
        <?php
        for($i=9;$i<=12;$i++){
            if($i<10) $b = "0".$i; else $b = $i;

            $poMaterial = newQuery("get_var",returnQueryPOUmum(1, $b));
            if(!$poMaterial) $poMaterial=0;
            $poTenaga = newQuery("get_var", returnQueryPOUmum(2, $b));
            if(!$poTenaga) $poTenaga=0;
            $poOverhead = newQuery("get_var", returnQueryPOUmum(3, $b));
            if(!$poOverhead) $poOverhead=0;

            $poTotal = $poMaterial+$poTenaga+$poOverhead;
            ?>
            <td class="right"><?php echo number_format($poMaterial,2); ?></td>
            <td class="right highlight"><?php echo number_format($poTenaga,2); ?></td>
            <td class="right"><?php echo number_format($poOverhead,2); ?></td>
            <td class="right highlight bold"><?php echo number_format($poTotal,2); ?></td>
            <?php
        }
        ?>
    </tr>
    <?php
    $query = newQuery("get_results","SELECT * FROM tb_departement ORDER BY NamaDepartement");
    if($query){
        foreach($query as $dataDepartement){
            ?>
            <tr>
            <td class="labelHeader"><?php echo $dataDepartement->NamaDepartement; ?></td>
                <?php
                for($i=9;$i<=12;$i++){
                    if($i<10) $b = "0".$i; else $b = $i;

                    $poMaterial = newQuery("get_var", returnQueryPODepartement(1, $dataDepartement->IDDepartement, $b));
                    if(!$poMaterial) $poMaterial=0;
                    $poTenaga = newQuery("get_var", returnQueryPODepartement(2, $dataDepartement->IDDepartement, $b));
                    if(!$poTenaga) $poTenaga=0;
                    $poOverhead = newQuery("get_var", returnQueryPODepartement(3, $dataDepartement->IDDepartement, $b));
                    if(!$poOverhead) $poOverhead=0;

                    $poTotal = $poMaterial+$poTenaga+$poOverhead;
                    ?>
                    <td class="right"><?php echo number_format($poMaterial,2); ?></td>
                    <td class="right highlight"><?php echo number_format($poTenaga,2); ?></td>
                    <td class="right"><?php echo number_format($poOverhead,2); ?></td>
                    <td class="right highlight bold"><?php echo number_format($poTotal,2); ?></td>
                    <?php
                }
                ?>
            </tr>
            <?php
        }
    }
    ?>
    <tr>
    <td class="labelHeader" style="text-decoration: underline">Total PO. Umum/Departement:</td>
    <?php
    for($i=9;$i<=12;$i++){
        if($i<10) $b = "0".$i; else $b = $i;

        $poMaterial = newQuery("get_var", returnQueryTotalPODepartement(1, $b));
        if(!$poMaterial) $poMaterial=0;
        $poTenaga = newQuery("get_var", returnQueryTotalPODepartement(2, $b));
        if(!$poTenaga) $poTenaga=0;
        $poOverhead = newQuery("get_var", returnQueryTotalPODepartement(3, $b));
        if(!$poOverhead) $poOverhead=0;

        $poTotal = $poMaterial+$poTenaga+$poOverhead;
        ?>
        <td class="right bold highlight2"><?php echo number_format($poMaterial,2); ?></td>
        <td class="right bold highlight2"><?php echo number_format($poTenaga,2); ?></td>
        <td class="right bold highlight2"><?php echo number_format($poOverhead,2); ?></td>
        <td class="right highlight22 bold" style="text-decoration: underline"><?php echo number_format($poTotal,2); ?></td>
        <?php
    }
    ?>
    </tr>
    <tr>
        <td class="labelHeader spacer" style="text-decoration: underline"></td>
        <td colspan="15"></td>
    </tr>
    <tr>
        <td class="labelHeader" style="text-decoration: underline">PO. Proyek</td>
        <td colspan="15"></td>
    </tr>
    <!-- PO Proyek -->
    <?php
    $query = newQuery("get_results", returnQueryListProyekPeriod("09","12"));
    if($query){
        foreach($query as $dataProyek){
            ?>
            <tr>
            <td class="labelHeader"><?php echo $dataProyek->KodeProyek."/".$dataProyek->Tahun; ?></td>
                <?php
                for($i=9;$i<=12;$i++){
                    if($i<10) $b = "0".$i; else $b = $i;

                    $poMaterial = newQuery("get_var", returnQueryPOProyek(1, $dataProyek->IDProyek, $b));
                    if(!$poMaterial) $poMaterial=0;
                    $poTenaga = newQuery("get_var", returnQueryPOProyek(2, $dataProyek->IDProyek, $b));
                    if(!$poTenaga) $poTenaga=0;
                    $poOverhead = newQuery("get_var", returnQueryPOProyek(3, $dataProyek->IDProyek, $b));
                    if(!$poOverhead) $poOverhead=0;

                    $poTotal = $poMaterial+$poTenaga+$poOverhead;
                    ?>
                    <td class="right"><?php echo number_format($poMaterial,2); ?></td>
                    <td class="right highlight"><?php echo number_format($poTenaga,2); ?></td>
                    <td class="right"><?php echo number_format($poOverhead,2); ?></td>
                    <td class="right highlight bold"><?php echo number_format($poTotal,2); ?></td>
                    <?php
                }
                ?>
            </tr>
            <?php
        }
    }
    ?>
    <tr>
    <td class="labelHeader" style="text-decoration: underline">Total PO. Proyek:</td>
    <?php
    for($i=9;$i<=12;$i++){
        if($i<10) $b = "0".$i; else $b = $i;

        $poMaterial = newQuery("get_var", returnQueryTotalPOProyek(1, $b));
        if(!$poMaterial) $poMaterial=0;
        $poTenaga = newQuery("get_var", returnQueryTotalPOProyek(2, $b));
        if(!$poTenaga) $poTenaga=0;
        $poOverhead = newQuery("get_var", returnQueryTotalPOProyek(3, $b));
        if(!$poOverhead) $poOverhead=0;

        $poTotal = $poMaterial+$poTenaga+$poOverhead;
        ?>
        <td class="right bold highlight2"><?php echo number_format($poMaterial,2); ?></td>
        <td class="right bold highlight2"><?php echo number_format($poTenaga,2); ?></td>
        <td class="right bold highlight2"><?php echo number_format($poOverhead,2); ?></td>
        <td class="right highlight22 bold" style="text-decoration: underline"><?php echo number_format($poTotal,2); ?></td>
        <?php
    }
    ?>
    </tr>
    <tr>
        <td class="labelHeader spacer" style="text-decoration: underline"></td>
        <td colspan="15"></td>
    </tr>
    <tr>
    <td class="labelHeader" style="text-decoration: underline">Total PO. Okt-Des :</td>
    <?php
    for($i=9;$i<=12;$i++){
        if($i<10) $b = "0".$i; else $b = $i;

        $poMaterial = newQuery("get_var", returnQueryTotalPOPeriode(1, $b));
        if(!$poMaterial) $poMaterial=0;
        $poTenaga = newQuery("get_var", returnQueryTotalPOPeriode(2, $b));
        if(!$poTenaga) $poTenaga=0;
        $poOverhead = newQuery("get_var", returnQueryTotalPOPeriode(3, $b));
        if(!$poOverhead) $poOverhead=0;

        $poTotal = $poMaterial+$poTenaga+$poOverhead;
        ?>
        <td class="right bold "><?php echo number_format($poMaterial,2); ?></td>
        <td class="right bold "><?php echo number_format($poTenaga,2); ?></td>
        <td class="right bold "><?php echo number_format($poOverhead,2); ?></td>
        <td class="right highlight22 bold" style="text-decoration: underline"><?php echo number_format($poTotal,2); ?></td>
        <?php
    }
    ?>
    </tr>
    </tbody>
    </table>


    <h1 class="blue newPage">Rekap Grand Total PO Umum dan Proyek Tahunan</h1>
    <h3 class="red">Periode: <?php echo $tahun; ?></h3>
    <table class="tbLabaRugi" style="width: auto">
    <thead>
    <tr>
        <td class="labelHeader" style="padding-right:20px">Periode</td>
        <td style="text-align:center;" colspan="4" class="bold"><?php echo $tahun; ?></td>
    </tr>
    <tr>
        <td class="labelHeader" style="padding-right:20px">Jenis. PO</td>
        <td class="jenisPO">Material</td>
        <td class="jenisPO highlight">Tenaga</td>
        <td class="jenisPO">Overhead</td>
        <td class="jenisPO highlight bold">Total</td>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="labelHeader" style="text-decoration: underline">PO. Umum</td>
        <td colspan="15"></td>
    </tr>
    <!-- PO UMUM -->
    <tr>
    <td class="labelHeader">Umum</td>
        <?php
        $poMaterial = newQuery("get_var","SELECT sum(GrandTotal) FROM tb_po WHERE JenisPO='1' AND IsLD='1' AND IDProyek='0' AND (IDDepartement='0' OR IDDepartement IS NULL) AND DATE_FORMAT(Tanggal,'%Y')='$tahun' $cond");
        if(!$poMaterial) $poMaterial=0;
        $poTenaga = newQuery("get_var","SELECT sum(GrandTotal) FROM tb_po WHERE JenisPO='2' AND IsLD='1' AND IDProyek='0' AND (IDDepartement='0' OR IDDepartement IS NULL) AND DATE_FORMAT(Tanggal,'%Y')='$tahun' $cond");
        if(!$poTenaga) $poTenaga=0;
        $poOverhead = newQuery("get_var","SELECT sum(GrandTotal) FROM tb_po WHERE JenisPO='3' AND IsLD='1' AND IDProyek='0' AND (IDDepartement='0' OR IDDepartement IS NULL) AND DATE_FORMAT(Tanggal,'%Y')='$tahun' $cond");
        if(!$poOverhead) $poOverhead=0;

        $poTotal = $poMaterial+$poTenaga+$poOverhead;
        ?>
        <td class="right"><?php echo number_format($poMaterial,2); ?></td>
        <td class="right highlight"><?php echo number_format($poTenaga,2); ?></td>
        <td class="right"><?php echo number_format($poOverhead,2); ?></td>
        <td class="right highlight bold"><?php echo number_format($poTotal,2); ?></td>
    </tr>
    <?php
    $query = newQuery("get_results","SELECT * FROM tb_departement ORDER BY NamaDepartement");
    if($query){
        foreach($query as $dataDepartement){
            ?>
            <tr>
            <td class="labelHeader"><?php echo $dataDepartement->NamaDepartement; ?></td>
                <?php
                $poMaterial = newQuery("get_var","SELECT sum(GrandTotal) FROM tb_po WHERE JenisPO='1' AND IsLD='1' AND IDProyek='0' AND IDDepartement='".$dataDepartement->IDDepartement."' AND DATE_FORMAT(Tanggal,'%Y')='$tahun' $cond");
                if(!$poMaterial) $poMaterial=0;
                $poTenaga = newQuery("get_var","SELECT sum(GrandTotal) FROM tb_po WHERE JenisPO='2' AND IsLD='1' AND IDProyek='0' AND IDDepartement='".$dataDepartement->IDDepartement."' AND DATE_FORMAT(Tanggal,'%Y')='$tahun' $cond");
                if(!$poTenaga) $poTenaga=0;
                $poOverhead = newQuery("get_var","SELECT sum(GrandTotal) FROM tb_po WHERE JenisPO='3' AND IsLD='1' AND IDProyek='0' AND IDDepartement='".$dataDepartement->IDDepartement."' AND DATE_FORMAT(Tanggal,'%Y')='$tahun' $cond");
                if(!$poOverhead) $poOverhead=0;

                $poTotal = $poMaterial+$poTenaga+$poOverhead;
                ?>
                <td class="right"><?php echo number_format($poMaterial,2); ?></td>
                <td class="right highlight"><?php echo number_format($poTenaga,2); ?></td>
                <td class="right"><?php echo number_format($poOverhead,2); ?></td>
                <td class="right highlight bold"><?php echo number_format($poTotal,2); ?></td>
            </tr>
            <?php
        }
    }
    ?>
    <tr>
    <td class="labelHeader" style="text-decoration: underline">Total PO. Umum/Departement:</td>
    <?php
    $poMaterial = newQuery("get_var","SELECT sum(GrandTotal) FROM tb_po WHERE JenisPO='1' AND IsLD='1' AND IDProyek='0' AND DATE_FORMAT(Tanggal,'%Y')='$tahun' $cond");
    if(!$poMaterial) $poMaterial=0;
    $poTenaga = newQuery("get_var","SELECT sum(GrandTotal) FROM tb_po WHERE JenisPO='2' AND IsLD='1' AND IDProyek='0' AND DATE_FORMAT(Tanggal,'%Y')='$tahun' $cond");
    if(!$poTenaga) $poTenaga=0;
    $poOverhead = newQuery("get_var","SELECT sum(GrandTotal) FROM tb_po WHERE JenisPO='3' AND IsLD='1' AND IDProyek='0' AND DATE_FORMAT(Tanggal,'%Y')='$tahun' $cond");
    if(!$poOverhead) $poOverhead=0;

    $poTotal = $poMaterial+$poTenaga+$poOverhead;
    ?>
    <td class="right bold highlight2"><?php echo number_format($poMaterial,2); ?></td>
    <td class="right bold highlight2"><?php echo number_format($poTenaga,2); ?></td>
    <td class="right bold highlight2"><?php echo number_format($poOverhead,2); ?></td>
    <td class="right highlight22 bold" style="text-decoration: underline"><?php echo number_format($poTotal,2); ?></td>
    </tr>
    <tr>
        <td class="labelHeader spacer" style="text-decoration: underline"></td>
        <td colspan="15"></td>
    </tr>
    <tr>
        <td class="labelHeader" style="text-decoration: underline">PO. Proyek</td>
        <td colspan="15"></td>
    </tr>
    <!-- PO Proyek -->
    <?php
    $query = newQuery("get_results", returnQueryListProyekPeriod("01","12"));
    if($query){
        foreach($query as $dataProyek){
            ?>
            <tr>
            <td class="labelHeader"><?php echo $dataProyek->KodeProyek."/".$dataProyek->Tahun; ?></td>
                <?php
                $poMaterial = newQuery("get_var","SELECT sum(GrandTotal) FROM tb_po WHERE JenisPO='1' AND IsLD='1' AND IDProyek='".$dataProyek->IDProyek."' AND DATE_FORMAT(Tanggal,'%Y')='$tahun' $cond");
                if(!$poMaterial) $poMaterial=0;
                $poTenaga = newQuery("get_var","SELECT sum(GrandTotal) FROM tb_po WHERE JenisPO='2' AND IsLD='1' AND IDProyek='".$dataProyek->IDProyek."' AND DATE_FORMAT(Tanggal,'%Y')='$tahun' $cond");
                if(!$poTenaga) $poTenaga=0;
                $poOverhead = newQuery("get_var","SELECT sum(GrandTotal) FROM tb_po WHERE JenisPO='3' AND IsLD='1' AND IDProyek='".$dataProyek->IDProyek."' AND DATE_FORMAT(Tanggal,'%Y')='$tahun' $cond");
                if(!$poOverhead) $poOverhead=0;

                $poTotal = $poMaterial+$poTenaga+$poOverhead;
                ?>
                <td class="right"><?php echo number_format($poMaterial,2); ?></td>
                <td class="right highlight"><?php echo number_format($poTenaga,2); ?></td>
                <td class="right"><?php echo number_format($poOverhead,2); ?></td>
                <td class="right highlight bold"><?php echo number_format($poTotal,2); ?></td>
            </tr>
            <?php
        }
    }
    ?>
    <tr>
    <td class="labelHeader" style="text-decoration: underline">Total PO. Proyek:</td>
    <?php
    $poMaterial = newQuery("get_var","SELECT sum(GrandTotal) FROM tb_po WHERE JenisPO='1' AND IsLD='1' AND IDProyek<>'0' AND DATE_FORMAT(Tanggal,'%Y')='$tahun' $cond");
    if(!$poMaterial) $poMaterial=0;
    $poTenaga = newQuery("get_var","SELECT sum(GrandTotal) FROM tb_po WHERE JenisPO='2' AND IsLD='1' AND IDProyek<>'0' AND DATE_FORMAT(Tanggal,'%Y')='$tahun' $cond");
    if(!$poTenaga) $poTenaga=0;
    $poOverhead = newQuery("get_var","SELECT sum(GrandTotal) FROM tb_po WHERE JenisPO='3' AND IsLD='1' AND IDProyek<>'0' AND DATE_FORMAT(Tanggal,'%Y')='$tahun' $cond");
    if(!$poOverhead) $poOverhead=0;

    $poTotal = $poMaterial+$poTenaga+$poOverhead;
    ?>
    <td class="right bold highlight2"><?php echo number_format($poMaterial,2); ?></td>
    <td class="right bold highlight2"><?php echo number_format($poTenaga,2); ?></td>
    <td class="right bold highlight2"><?php echo number_format($poOverhead,2); ?></td>
    <td class="right highlight22 bold" style="text-decoration: underline"><?php echo number_format($poTotal,2); ?></td>
    </tr>
    <tr>
        <td class="labelHeader spacer" style="text-decoration: underline"></td>
        <td colspan="15"></td>
    </tr>
    <tr>
    <td class="labelHeader" style="text-decoration: underline">Total PO. <?php echo $tahun; ?> :</td>
    <?php
    $poMaterial = newQuery("get_var","SELECT sum(GrandTotal) FROM tb_po WHERE JenisPO='1' AND IsLD='1' AND DATE_FORMAT(Tanggal,'%Y')='$tahun' $cond");
    if(!$poMaterial) $poMaterial=0;
    $poTenaga = newQuery("get_var","SELECT sum(GrandTotal) FROM tb_po WHERE JenisPO='2' AND IsLD='1' AND DATE_FORMAT(Tanggal,'%Y')='$tahun' $cond");
    if(!$poTenaga) $poTenaga=0;
    $poOverhead = newQuery("get_var","SELECT sum(GrandTotal) FROM tb_po WHERE JenisPO='3' AND IsLD='1' AND DATE_FORMAT(Tanggal,'%Y')='$tahun' $cond");
    if(!$poOverhead) $poOverhead=0;

    $poTotal = $poMaterial+$poTenaga+$poOverhead;
    ?>
    <td class="right bold "><?php echo number_format($poMaterial,2); ?></td>
    <td class="right bold "><?php echo number_format($poTenaga,2); ?></td>
    <td class="right bold "><?php echo number_format($poOverhead,2); ?></td>
    <td class="right highlight22 bold" style="text-decoration: underline"><?php echo number_format($poTotal,2); ?></td>
    </tr>
    </tbody>
    </table>
    <script type="text/javascript">
        setTimeout(function(){
            window.print();
        },1500);
    </script>
</body>
</html>