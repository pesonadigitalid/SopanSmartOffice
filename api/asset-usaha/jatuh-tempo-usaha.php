<?php
include_once "../config/connection.php";

$assetArray = array();
$query = $db->get_results("SELECT *, DATE_FORMAT(JatuhTempoUsaha,'%d/%m/%Y') AS JatuhTempoUsahaID FROM tb_asset WHERE Jenis='Ijin-Usaha' AND Status='1' ORDER BY JatuhTempoUsaha ASC");
if($query){
    $i=1;
    foreach($query as $data){
        $karyawan = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='".$data->IDKaryawan."'");
        $category = $db->get_var("SELECT Nama FROM tb_asset_category WHERE IDAssetCategory='".$data->IDAssetCategory."'");

        $now = time(); 
        $your_date = strtotime($data->JatuhTempoUsaha);
        $datediff = $your_date - $now;
        $DueDate = floor($datediff / (60 * 60 * 24));
        $DueDate = ($DueDate);

        array_push($assetArray,array("IDAsset"=>$data->IDAsset,"No"=>$i,"KodeAsset"=>$data->KodeAsset,"Category"=>$category,"Nama"=>$data->Nama,"IDKaryawan"=>$data->IDKaryawan,"Karyawan"=>$karyawan,"Unit"=>$data->Unit,"JatuhTempoUsaha"=>$data->JatuhTempoUsahaID,"DueDate"=>$DueDate));
        $i++;
    }
}

echo json_encode($assetArray);