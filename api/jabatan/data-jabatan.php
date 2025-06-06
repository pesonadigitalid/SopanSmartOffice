<?php
include_once "../config/connection.php";
$query = $db->get_results("SELECT * FROM tb_jabatan ORDER BY IDJabatan ASC");
if($query){
    $return = array();
    $i=0;
    foreach($query as $data){
        $i++;
        array_push($return,array("No"=>$i,"Jabatan"=>$data->Jabatan,"IDJabatan"=>$data->IDJabatan));
    }
    echo json_encode($return);
} else {
    echo json_encode(array());
}
