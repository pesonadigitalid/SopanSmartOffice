<?php
include_once "../config/connection.php";
$id = antiSQLInjection($_GET['id']);
$query = $db->get_results("SELECT * FROM tb_po_detail WHERE NoPO='$id' ORDER BY NoUrut ASC");
if($query){
    $return = array();
    $i=0;
    foreach($query as $data){
        $i++;
        $nama_barang = $db->get_var("SELECT Nama FROM tb_barang WHERE IDBarang='".$data->IDBarang."'");
        array_push($return,array("NoUrut"=>$data->NoUrut,"IDBarang"=>$data->IDBarang,"NamaBarang"=>$nama_barang,"Qty"=>$data->Qty,"Harga"=>$data->Harga,"SubTotal"=>$data->SubTotal));
    }
    echo json_encode($return);
} else {
    echo json_encode(array());
}
