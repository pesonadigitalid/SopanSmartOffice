<?php
include_once "../config/connection.php";
$query = $db->get_results("SELECT * FROM tb_contact_category ORDER BY Nama ASC");
if($query){
    $return = array();
    $i=1;
    foreach($query as $data){
        $total = $db->get_var("SELECT COUNT(*) FROM tb_contact_category_user WHERE id_category='".$data->IDContactCategory."'");
        array_push($return,array("IDContactCategory"=>$data->IDContactCategory,"No"=>$i,"Nama"=>$data->Nama,"Total"=>$total));
    }
    echo json_encode($return);
} else {
    echo json_encode(array());
}
