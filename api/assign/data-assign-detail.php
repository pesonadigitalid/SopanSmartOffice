<?php
include_once "../config/connection.php";
$id = antiSQLInjection($_GET['id']);
$query = $db->get_results("SELECT * FROM tb_assign_detail WHERE IDAssign='$id' ORDER BY IDAsset ASC");
if($query){
    $return = array();
    $i=1;
    foreach($query as $data){
        $category = $db->get_var("SELECT Nama FROM tb_asset_category WHERE IDAssetCategory='".$data->IDAssetCategory."'");
        array_push($return,array("IDAssignDetail"=>$data->IDAssignDetail,"KodeAsset"=>$data->KodeAsset,"Nama"=>$data->Nama));
    }
    echo json_encode($return);
} else {
    echo json_encode(array());
}
