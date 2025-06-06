<?php
include_once "../config/connection.php";
$query = $db->get_results("SELECT * FROM tb_user ORDER BY IDUser ASC");
if($query){
    $return = array();
    $i=0;
    foreach($query as $data){
        $i++;
        if($data->Level=="1") $level="Administrator"; else $level="Operator";
        if($data->Status=="1") $status="Aktif"; else $level="Non Aktif";
        array_push($return,array("No"=>$i,"Nama"=>$data->Nama,"IDUser"=>$data->IDUser,"Level"=>$level,"Status"=>$status));
    }
    echo json_encode($return);
} else {
    echo json_encode(array());
}
