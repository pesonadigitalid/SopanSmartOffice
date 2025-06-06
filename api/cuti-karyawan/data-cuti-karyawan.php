<?php
include_once "../config/connection.php";
$query = $db->get_results("SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_cuti ORDER BY IDCuti ASC");
if($query){
    $return = array();
    $i=0;
    foreach($query as $data){
        $i++;
        $karyawan = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='".$data->IDKaryawan."'");
        if($data->Status=="1") $status="Approved HRD"; else $status="Baru";
        array_push($return,array("IDCuti"=>$data->IDCuti,"No"=>$i,"Tanggal"=>$data->TanggalID,"Karyawan"=>$karyawan,"Keterangan"=>$data->Keterangan,"Status"=>$status));
    }
    echo json_encode($return);
} else {
    echo json_encode(array());
}
