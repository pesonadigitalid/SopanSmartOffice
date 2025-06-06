<?php
include_once "../config/connection.php";
$id = antiSQLInjection($_GET['id']);
$usr = antiSQLInjection($_GET['usr']);
$query = $db->get_results("SELECT * FROM tb_karyawan WHERE Usernm='$usr' AND IDKaryawan!='$id' ORDER BY IDKaryawan ASC");
if($query){
    $return = array();
    $i=0;
    foreach($query as $data){
        $i++;
        array_push($return,array("Usernm"=>$data->Usernm));
    }
    echo json_encode($return);
} else {
    echo json_encode(array());
}
