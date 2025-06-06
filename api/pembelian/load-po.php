<?php
include_once "../config/connection.php";
$id = antiSQLInjection($_GET['id']);
$query = $db->get_row("SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_po WHERE NoPO='$id' ORDER BY IDPO ASC");
if($query){
    $return = array();
	$return["total"] = $query->Total;
	$return["diskon_persen"] = $query->DiskonPersen;
	$return["total2"] = $query->Total2;
	$return["ppn_persen"] = $query->PPNPersen;
	$return["grand_total"] = $query->GrandTotal;
	$return["pembayarandp"] = $query->PembayaranDP;
	$return["sisa"] = $query->Sisa;
	$return["keterangan"] = $query->Keterangan;

	$queryDetail = $db->get_results("SELECT * FROM tb_po_detail WHERE NoPO='$id' ORDER BY NoUrut ASC");
	$i=0;
	$arrayCart = array();
	if($queryDetail){
	    foreach($queryDetail as $dataDetail){
	        $i++;
	        $nama_barang = $db->get_var("SELECT Nama FROM tb_barang WHERE IDBarang='".$dataDetail->IDBarang."'");
	        $arrayCart[$dataDetail->IDBarang] = array("NoUrut"=>$dataDetail->NoUrut,"IDBarang"=>$dataDetail->IDBarang,"NamaBarang"=>$nama_barang,"Qty"=>$dataDetail->Qty,"Harga"=>$dataDetail->Harga,"SubTotal"=>$dataDetail->SubTotal);
	    }
    }
	$return["Cart"] = $arrayCart;
	
    echo json_encode($return);
} else {
    echo json_encode(array());
}
