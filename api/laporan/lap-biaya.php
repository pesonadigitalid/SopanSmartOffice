<?php
include_once "../config/connection.php";
$month = antiSQLInjection($_GET['month']);
if($month<10) $month="0".$month;
$year = antiSQLInjection($_GET['year']);
$idb = antiSQLInjection($_GET['idb']);

$query = newQuery("get_results","SELECT
    `tb_master_transaksi`.`TourName`
    , `tb_master_transaksi`.`Ref`
    , `tb_master_transaksi`.`PeriodeStart`
    , DATE_FORMAT(`tb_master_transaksi`.`PeriodeStart`,'%d %b') AS PeriodeStartID
    , `tb_master_transaksi`.`PariodeAkhir`
    , DATE_FORMAT(`tb_master_transaksi`.`PariodeAkhir`,'%d %b') AS PariodeAkhirID
    , `tb_activity`.`Kode`
    , `tb_activity`.`Nama`
    , `tb_detail_transaksi`.`IDDetail`
    , `tb_detail_transaksi`.`IDActivity`
    , `tb_detail_transaksi`.`Harga`
    , `tb_detail_transaksi`.`USD`
    , `tb_detail_transaksi`.`Rate`
    , `tb_detail_transaksi`.`HargaReal`
    , `tb_detail_transaksi`.`Pax` AS Unit
    , `tb_detail_transaksi`.`TotalBiaya`
    , `tb_master_transaksi`.`Pax` AS PaxUnit
FROM
    `tb_activity`
    INNER JOIN `tb_detail_transaksi` 
        ON (`tb_activity`.`IDActivity` = `tb_detail_transaksi`.`IDActivity`)
    INNER JOIN `tb_master_transaksi` 
        ON (`tb_master_transaksi`.`IDMaster` = `tb_detail_transaksi`.`IDMaster`)
WHERE '$year-$month' BETWEEN DATE_FORMAT(tb_master_transaksi.PeriodeStart,'%Y-%m') AND DATE_FORMAT(tb_master_transaksi.PariodeAkhir,'%Y-%m') AND
`tb_detail_transaksi`.`IDActivity` IN (SELECT IDActivity FROM tb_activity WHERE IDActivityCategory='$idb')
ORDER BY `tb_detail_transaksi`.`IDActivity` ASC");

if($query){
    $return = array();
    $i=0;
    $totalPack = 0;
    $totalPriceIDR = 0;
    
    $hargaTotalUSD=0;
    $hargaTotalIDR=0;
    $lastID=0;
    $totalPPN=0;
        
    foreach($query as $data){
        $i++;
        $totalPriceIDR += ($data->HargaReal*$data->Unit);
        if($data->USD=="1"){
             $hargaUSD = $data->Harga;
             $rate = $data->Rate;
             $hargaIDR = $data->HargaReal;
        } else {
             $hargaUSD = 0;
             $rate = 0;
             $hargaIDR = $data->HargaReal;
        }
        
        if($lastID!=$data->IDActivity){
            if($i>1){
                array_push($return,array("No"=>$i,"SubTotal"=>1,"TourName"=>$data->TourName,"Ref"=>$data->Ref,"PeriodeStart"=>$data->PeriodeStartID,"PariodeAkhir"=>$data->PariodeAkhirID,"PaxUnit"=>$totalPack,"Kode"=>$data->Kode,"Nama"=>$data->Nama,"Unit"=>$data->Unit,"hargaUSD"=>0,"USD"=>0,"rate"=>0,"hargaIDR"=>0,"hargaIDR"=>0,"totalHargaUSD"=>$hargaTotalUSD,"totalHargaIDR"=>$hargaTotalIDR));
                $hargaTotalUSD=0;
                $hargaTotalIDR=0;
                $totalPack = 0;
            }
            $hargaTotalUSD += ($hargaUSD*$data->Unit);
            $hargaTotalIDR += ($hargaIDR*$data->Unit);
            $totalPack += $data->PaxUnit;
        } else {
            $hargaTotalUSD += ($hargaUSD*$data->Unit);
            $hargaTotalIDR += ($hargaIDR*$data->Unit);
            $totalPack += $data->PaxUnit;
        }
        $lastID = $data->IDActivity;
        
        array_push($return,array("No"=>$i,"SubTotal"=>0,"TourName"=>$data->TourName,"Ref"=>$data->Ref,"PeriodeStart"=>$data->PeriodeStartID,"PariodeAkhir"=>$data->PariodeAkhirID,"PaxUnit"=>$data->PaxUnit,"Kode"=>$data->Kode,"Nama"=>$data->Nama,"Unit"=>$data->Unit,"hargaUSD"=>$hargaUSD,"USD"=>$data->USD,"rate"=>$rate,"hargaIDR"=>$hargaIDR,"hargaIDR"=>$hargaIDR,"totalHargaUSD"=>0,"totalHargaIDR"=>0));
    }
    $totalPPN = $totalPriceIDR*10/100;
    array_push($return,array("No"=>$i,"SubTotal"=>1,"TourName"=>$data->TourName,"Ref"=>$data->Ref,"PeriodeStart"=>$data->PeriodeStartID,"PariodeAkhir"=>$data->PariodeAkhirID,"PaxUnit"=>$data->PaxUnit,"Kode"=>$data->Kode,"Nama"=>$data->Nama,"Unit"=>$data->Unit,"hargaUSD"=>0,"USD"=>0,"rate"=>0,"hargaIDR"=>0,"hargaIDR"=>0,"totalHargaUSD"=>$hargaTotalUSD,"totalHargaIDR"=>$hargaTotalIDR));
    echo json_encode(array("totalPriceIDR"=>$totalPriceIDR,"totalPPN"=>$totalPPN,"data"=>$return));
} else {
    echo json_encode(array());
}
