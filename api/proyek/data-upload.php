<?php
include_once "../config/connection.php";
$id = antiSQLInjection($_GET['id']);

$query = $db->get_results("SELECT a.*, b.Nama, DATE_FORMAT(a.DateCreated,'%d/%m/%Y') AS DateCreatedID FROM tb_proyek_file a, tb_karyawan b WHERE a.`CreatedBy`=b.`IDKaryawan` AND a.IDProyek='$id'");
if($query){
    $return = array();
    foreach($query as $data){
        array_push($return,array("IDProyekFile"=>$data->IDProyekFile,"FileType"=>$data->FileType,"Name"=>$data->Name,"FileName"=>$data->FileName,"Nama"=>$data->Nama,"DateCreated"=>$data->DateCreatedID));
    }
    echo json_encode($return);
} else {
    echo json_encode(array());
}
