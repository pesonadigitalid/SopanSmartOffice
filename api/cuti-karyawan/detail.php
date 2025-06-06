<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_GET['id']);

$query = $db->get_row("SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_cuti WHERE IDCuti='$id' ORDER BY IDCuti ASC");
if($query){
    $return = array("tanggal"=>$query->TanggalID,"karyawan"=>$query->IDKaryawan,"keterangan"=>$query->Keterangan,"stts"=>$query->Status);
}
echo json_encode($return);