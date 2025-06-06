<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_GET['id']);

$query = $db->get_row("SELECT *,DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_assign WHERE IDAssign='$id' ORDER BY NoAssign ASC");
if($query){
    $return = array("idassign"=>$query->IDAssign,"noassign"=>$query->NoAssign,"tanggal"=>$query->TanggalID,"karyawan"=>$query->IDKaryawan,"total_item"=>$query->TotalItem);
}
echo json_encode($return);