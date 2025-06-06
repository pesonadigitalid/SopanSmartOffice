<?php
include_once "../config/connection.php";
$id = antiSQLInjection($_GET['id']);
$query = $db->get_results("SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_po WHERE NoPO='$id' ORDER BY IDPO ASC");
if($query){
    $return = array();
    $i=0;
    foreach($query as $data){
        $i++;
        array_push($return,array("total"=>$data->Total,"diskon_persen"=>$data->DiskonPersen,"total2"=>$data->Total2,"ppn_persen"=>$data->PPNPersen,"grand_total"=>$data->GrandTotal,"pembayarandp"=>$data->PembayaranDP,"sisa"=>$data->Sisa,"keterangan"=>$keterangan));
    }
    echo json_encode($return);
} else {
    echo json_encode(array());
}
