<?php
include_once "../config/connection.php";
$query = $db->get_results("SELECT * FROM tb_gudang ORDER BY Nama ASC");
if($query){
    $return = array();
    $i=0;
    foreach($query as $data){
        $i++;
        array_push($return,array("IDGudang"=>$data->IDGudang,"No"=>$i,"Nama"=>$data->Nama,"IsDefault"=>$data->IsDefault));
    }
    echo json_encode($return);
} else {
    echo json_encode(array());
}
