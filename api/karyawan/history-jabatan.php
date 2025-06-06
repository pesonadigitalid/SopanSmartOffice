<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_GET['id']);
$query = $db->get_results("SELECT a.*, b.Jabatan FROM tb_history_jabatan a, tb_jabatan b WHERE a.IDJabatan=b.IDJabatan AND a.IDKaryawan='$id' ORDER BY a.IDHistory ASC");
$return = array();
if($query){
	foreach($query as $data){
		array_push($return, array("jabatan"=>$data->Jabatan,"periode_awal"=>$data->PeriodeAwal,"periode_akhir"=>$data->PeriodeAkhir));
	}
}
echo json_encode($return);