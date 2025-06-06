<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_GET['id']);

$query = $db->get_row("SELECT *, DATE_FORMAT(JatuhTempoUsaha, '%d/%m/%Y') AS JatuhTempoUsahaID FROM tb_asset WHERE IDAsset='$id' ORDER BY IDAsset ASC");
if($query){
    $namaCategory = $db->get_var("SELECT Nama FROM tb_asset_category WHERE IDAssetCategory='".$query->IDAssetCategory."' ORDER BY IDAssetCategory");
    $return = array("nama"=>$query->Nama,"category"=>$query->IDAssetCategory,"kode_asset"=>$query->KodeAsset,"deskripsi"=>$query->Deskripsi,"no_ijin_usaha"=>$query->NoIjinUsaha,"jatuh_tempo_usaha"=>$query->JatuhTempoUsahaID,"stts_asset"=>$query->Status);
}
echo json_encode($return);