<?php
include_once "../config/connection.php";
$query = $db->get_results("SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_po ORDER BY IDPO DESC");
if($query){
    $return = array();
    $i=0;
    foreach($query as $data){
        $i++;
        if($data->FakturPajak=="") $FakturPajak = "-"; else $FakturPajak = $data->FakturPajak;
        array_push($return,array("NoPO"=>$data->NoPO,"No"=>$i,"Tanggal"=>$data->TanggalID,"FakturPajak"=>$FakturPajak));
    }
    echo json_encode($return);
} else {
    echo json_encode(array());
}
