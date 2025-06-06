<?php
include_once "../config/connection.php";
$idr = antiSQLInjection($_POST['idr']);

$allow = 1;
$dataPembelian = $db->get_row("SELECT * FROM tb_pembelian WHERE NoPembelian='$idr'");
$query = $db->get_results("SELECT * FROM tb_pembelian_detail WHERE NoPembelian='$idr'");
if($query){
	foreach($query as $data){
		if($dataPembelian->NoPO=="")
			$sql = "SELECT * FROM tb_stok_gudang WHERE IDBarang='".$data->IDBarang."' AND IDPembelian='".$dataPembelian->IDPembelian."'";
		else
			$sql = "SELECT * FROM tb_stok_purchasing WHERE IDBarang='".$data->IDBarang."' AND IDPembelian='".$dataPembelian->IDPembelian."'";
		$dataStok = $db->get_row($sql);
		if($dataStok){
			if($dataStok->Stok != $dataStok->SisaStok){
				$allow = 0;
				break;
			}
		}
	}
}

if($allow==0){
	echo "2";
} else {
	$query = $db->query("DELETE a.*, b.* FROM tb_pembelian a, tb_pembelian_detail b WHERE a.NoPembelian='$idr' AND b.NoPembelian='$idr'");
	if($query){
	    echo "1";
	} else {
	    echo "0";
	}
}