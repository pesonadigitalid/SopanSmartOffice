<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_GET['id']);

$query = $db->get_row("SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_return_po WHERE NoReturn='$id' ORDER BY IDReturn ASC");
if($query){
    $user = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='".$query->CreatedBy."'");
    $supplier = $db->get_var("SELECT NamaPerusahaan FROM tb_supplier WHERE IDSupplier='".$query->IDSupplier."'");
    if($query->Keterangan!="") $keterangan = $query->Keterangan; else $keterangan="-";
    $return = array("no_return"=>$query->NoReturn,"no_return_konsumen"=>$query->NoReturnKonsumen,"tanggal"=>$query->TanggalID,"usrlogin"=>$user,"total"=>$query->Total,"diskon_persen"=>$query->DiskonPersen,"total2"=>$query->Total2,"ppn_persen"=>$query->PPNPersen,"grand_total"=>$query->GrandTotal,"pembayarandp"=>$query->PembayaranDP,"sisa"=>$query->Sisa,"keterangan"=>$keterangan,"supplier"=>$supplier,"metode_pembayaran"=>$query->MetodePembayaran1,"metode_pembayaran2"=>$query->MetodePembayaran2,"jatuhtempobg"=>$query->JatuhTempoBG,"nobg"=>$query->NoBG,"kembali"=>$query->Kembali,"total_pembayaran"=>$query->TotalPembayaran,"ongkos_kirim"=>$query->OngkosKirim,"completed"=>$query->Completed,"TotalItem"=>$query->TotalItem);
}


$detailCart = array();
$query = $db->get_results("SELECT * FROM tb_return_po_detail WHERE NoReturn='$id' ORDER BY NoUrut ASC");
if($query){
    $i=0;
    foreach($query as $data){
        $i++;
        array_push($detailCart,array("NamaBarang"=>$data->NamaBarang,"Harga"=>$data->Harga,"No"=>$i,"Qty"=>$data->Qty,"SubTotal"=>$data->SubTotal,"SN"=>$data->SN));
    }
}

$dataPembayaran = array();
$query = $db->get_results("SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_pembayaran WHERE NoPO='$id' ORDER BY IDPembayaran ASC");
if($query){
    $i=0;
    foreach($query as $data){
        $i++;
        array_push($dataPembayaran,array("NoPembayaran"=>$data->NoPembayaran,"Tanggal"=>$data->TanggalID,"No"=>$i,"Jumlah"=>$data->Jumlah));
    }
}


echo json_encode(array("detail"=>$return, "masterpenerimaan"=>$masterpenerimaan, "detailpenerimaan"=>$detailpenerimaan, "detailcart"=>$detailCart, "dataPembayaran"=>$dataPembayaran));