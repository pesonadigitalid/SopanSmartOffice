<?php
include_once "../config/connection.php";
$id = antiSQLInjection($_GET['id']);
$query = $db->get_results("SELECT * FROM tb_po_detail WHERE NoPO='$id' ORDER BY NoUrut ASC");
if($query){
    $return = array();
    $i=0;
    foreach($query as $data){
        $i++;
        array_push($return,array("NamaBarang"=>$data->NamaBarang,"Harga"=>$data->Harga,"No"=>$i,"Qty"=>$data->Qty,"SubTotal"=>$data->SubTotal));
    }
    echo json_encode($return);
} else {
    echo json_encode(array());
}
