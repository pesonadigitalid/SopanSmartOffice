<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_GET['id']);

$query = $db->get_row("SELECT * FROM tb_pembelian_detail WHERE NoPO='$id' ORDER BY NoUrut ASC");
if($query){
    $nama_barang = $db->get_var("SELECT Nama FROM tb_barang WHERE IDBarang='".$query->IDBarang."'");
    if($query->Keterangan!="") $keterangan = $query->Keterangan; else $keterangan="-";
    $return = array("id_barang"=>$query->NoPO,"no_pembelian"=>$query->NoPembelian,"tanggal"=>$query->TanggalID,"usrlogin"=>$user,"total"=>$query->Total,"diskon_persen"=>$query->DiskonPersen,"total2"=>$query->Total2,"ppn_persen"=>$query->PPNPersen,"grand_total"=>$query->GrandTotal,"pembayarandp"=>$query->PembayaranDP,"sisa"=>$query->Sisa,"keterangan"=>$keterangan);
}
echo json_encode($return);