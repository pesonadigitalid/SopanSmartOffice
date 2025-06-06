<?php
include_once "../config/connection.php";
$month = antiSQLInjection($_GET['month']);
if($month<10) $month="0".$month;
$year = antiSQLInjection($_GET['year']);
$key = antiSQLInjection($_GET['key']);
if($key!=""){
    $query = newQuery("get_results","SELECT *, DATE_FORMAT(PeriodeStart,'%d %b') AS PeriodeStartID, DATE_FORMAT(PariodeAkhir,'%d %b') AS PariodeAkhirID FROM tb_master_transaksi WHERE TourName LIKE '%$key%' OR Ref LIKE '%$key%' ORDER BY IDMaster ASC");
} else {
    $query = newQuery("get_results","SELECT *, DATE_FORMAT(PeriodeStart,'%d %b') AS PeriodeStartID, DATE_FORMAT(PariodeAkhir,'%d %b') AS PariodeAkhirID FROM tb_master_transaksi WHERE '$year-$month' BETWEEN DATE_FORMAT(PeriodeStart,'%Y-%m') AND DATE_FORMAT(PariodeAkhir,'%Y-%m') ORDER BY IDMaster ASC");
}
if($query){
    $return = array();
    $i=0;
    $totalPack = 0;
    $totalPriceUSD = 0;
    $totalPriceIDR = 0;
    $TotalBiaya = 0;
    $Margin = 0;
    foreach($query as $data){
        $i++;
        $totalPack += $data->Pax;
        $totalPriceUSD += $data->HargaJualUSD;
        $totalPriceIDR += $data->HargaJualIDR;
        $totalBiaya += $data->TotalBiaya;
        $totalMargin += $data->Margin;
        array_push($return,array("No"=>$i,"IDMaster"=>$data->IDMaster,"TransactionID"=>$data->TransactionID,"TourName"=>$data->TourName,"Ref"=>$data->Ref,"PeriodeStart"=>$data->PeriodeStartID,"PariodeAkhir"=>$data->PariodeAkhirID,"Pax"=>$data->Pax,"HargaJualUSD"=>$data->HargaJualUSD,"HargaJualIDR"=>$data->HargaJualIDR,"TotalBiaya"=>$data->TotalBiaya,"Margin"=>$data->Margin));
    }
    echo json_encode(array("totalPack"=>$totalPack,"totalPriceUSD"=>$totalPriceUSD,"totalPriceIDR"=>$totalPriceIDR,"totalBiaya"=>$totalBiaya,"totalMargin"=>$totalMargin,"data"=>$return));
} else {
    echo json_encode(array());
}
