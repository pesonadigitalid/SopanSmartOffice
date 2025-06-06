<?php
include_once "../config/connection.php";
$id = antiSQLInjection($_GET['id']);
$query = $db->get_results("SELECT * FROM tb_karyawan WHERE IDKaryawan='$id' ORDER BY IDKaryawan ASC");
if($query){
    $return = array();
    $i=0;
    foreach($query as $data){
        $i++;
        if($data->Status=="1") $status="Aktif"; else $status="Non Aktif";
        $jabatan = $db->get_var("SELECT Jabatan FROM tb_jabatan WHERE IDJabatan='".$data->IDJabatan."'");
        array_push($return,array("No"=>$i,"NIK"=>$data->NIK,"Nama"=>$data->Nama,"Status"=>$data->StatusLainnya,"Jabatan"=>$jabatan,"StatusK"=>$status,"IDKaryawan"=>$data->IDKaryawan));
    }
    echo json_encode($return);
} else {
    echo json_encode(array());
}
