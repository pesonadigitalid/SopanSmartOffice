<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_GET['id']);

$query = $db->get_row("SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_pembelian WHERE NoPembelian='$id' ORDER BY IDPembelian ASC");
if($query){
    $proyek = $db->get_var("SELECT NamaProyek FROM tb_proyek WHERE IDProyek='".$query->IDProyek."'");
    $user = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='".$query->CreatedBy."'");
    $supplier = $db->get_var("SELECT NamaPerusahaan FROM tb_supplier WHERE IDSupplier='".$query->IDSupplier."'");
    if($query->Keterangan!="") $keterangan = $query->Keterangan; else $keterangan="-";
    $return = array("no_po"=>$query->NoPO,"no_pembelian"=>$query->NoPembelian,"proyek"=>$proyek,"tanggal"=>$query->TanggalID,"usrlogin"=>$user,"total"=>$query->Total,"diskon_persen"=>$query->DiskonPersen,"total2"=>$query->Total2,"ppn_persen"=>$query->PPNPersen,"grand_total"=>$query->GrandTotal,"pembayarandp"=>$query->PembayaranDP,"sisa"=>$query->Sisa,"keterangan"=>$keterangan,"supplier"=>$supplier,"metode_pembayaran"=>$query->MetodePembayaran1,"metode_pembayaran2"=>$query->MetodePembayaran2,"jatuhtempobg"=>$query->JatuhTempoBG,"nobg"=>$query->NoBG,"kembali"=>$query->Kembali);
}
echo json_encode($return);