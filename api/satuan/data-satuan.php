<?php
include_once "../config/connection.php";
$query = $db->get_results("SELECT * FROM tb_satuan ORDER BY IDSatuan ASC");
if($query){
    $return = array();
    $i=0;
    foreach($query as $data){
        $i++;
        array_push($return,array("IDSatuan"=>$data->IDSatuan,"No"=>$i,"Nama"=>$data->Nama));
    }
    echo json_encode($return);
} else {
    echo json_encode(array());
}
