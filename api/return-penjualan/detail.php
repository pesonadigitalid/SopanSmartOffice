<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_GET['id']);

$query = $db->get_row("SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_return_penjualan WHERE NoReturn='$id' ORDER BY IDReturn ASC");
if($query){
    $user = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='".$query->CreatedBy."'");
    $pelanggan = $db->get_var("SELECT NamaPelanggan FROM tb_pelanggan WHERE IDPelanggan='".$query->IDPelanggan."'");
    if($query->Keterangan!="") $keterangan = $query->Keterangan; else $keterangan="-";
    $return = array("no_return"=>$query->NoReturn,"nopenjualan"=>$query->NoPenjualan,"tanggal"=>$query->TanggalID,"usrlogin"=>$user,"total"=>$query->Total,"diskon_persen"=>$query->DiskonPersen,"total2"=>$query->Total2,"grand_total"=>$query->GrandTotal,"pembayarandp"=>$query->PembayaranDP,"sisa"=>$query->Sisa,"keterangan"=>$keterangan,"pelanggan"=>$pelanggan,"metode_pembayaran"=>$query->MetodePembayaran1,"metode_pembayaran2"=>$query->MetodePembayaran2,"jatuhtempobg"=>$query->JatuhTempoBG,"nobg"=>$query->NoBG,"kembali"=>$query->Kembali,"total_pembayaran"=>$query->TotalPembayaran,"completed"=>$query->Completed,"total_qty"=>$query->TotalItem);
}


$detailCart = array();
$query = $db->get_results("SELECT * FROM tb_return_penjualan_detail WHERE NoReturn='$id' ORDER BY NoUrut ASC");
if($query){
    $i=0;
    foreach($query as $data){
        $i++;
        array_push($detailCart,array("NamaBarang"=>$data->NamaBarang,"Harga"=>$data->Harga,"No"=>$i,"Qty"=>$data->Qty,"SubTotal"=>$data->SubTotal,"Tipe"=>$data->Tipe,"SN"=>$data->SN));
    }
}


echo json_encode(array("detail"=>$return, "detailcart"=>$detailCart));