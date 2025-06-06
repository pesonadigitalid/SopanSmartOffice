<?php
include_once "../config/connection.php";
$query = $db->get_results("SELECT * FROM tb_departement ORDER BY IDDepartement ASC");
if($query){
    $return = array();
    $i=0;
    foreach($query as $data){
        $i++;
        array_push($return,array("IDDepartement"=>$data->IDDepartement,"No"=>$i,"NamaDepartement"=>$data->NamaDepartement));
    }
    echo json_encode($return);
} else {
    echo json_encode(array());
}
