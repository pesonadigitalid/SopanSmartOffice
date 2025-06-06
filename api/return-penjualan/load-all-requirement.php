<?php
include_once "../config/connection.php";

$barang = array();
$proyek = array();
$penjualan = array();

$no_penjualan = antiSQLInjection($_GET['no_penjualan']);

if($no_penjualan==""){
    
} else {
    $query = $db->get_results("SELECT * FROM tb_penjualan_detail WHERE NoPenjualan='$no_penjualan'");
    if($query){
        $i=0;
        foreach($query as $data){
            $i++;
            $isPaket = $db->get_results("SELECT a.*, b.* FROM tb_barang_child a, tb_barang b WHERE a.IDBarang=b.IDBarang AND a.IDParent='".$data->IDBarang."'");
            if($isPaket){
                foreach($isPaket as $dataPaket){
                    if($dataPaket->IsSerialize=="0"){
                        $stokTerkirim = $db->get_var("SELECT SUM(Qty) FROM tb_return_penjualan_detail WHERE NoReturn IN (SELECT NoReturn FROM tb_return_penjualan WHERE NoPenjualan='$no_penjualan') AND IDBarang='".$dataPaket->IDBarang."' AND IsPaket='1'");
                        if(!$stokTerkirim) $stokTerkirim = 0;
                        $stokPenjualan = $db->get_var("SELECT SUM(Qty) FROM tb_penjualan_detail WHERE NoPenjualan='".$data->NoPenjualan."' AND IDBarang='".$data->IDBarang."'");
                        $limit = $stokPenjualan-$stokTerkirim;
                    } else $limit = 1;
                    if($limit>0){
                        array_push($barang,array("IDBarang"=>$dataPaket->IDBarang,"KodeBarang"=>$dataPaket->KodeBarang,"Nama"=>$dataPaket->Nama." - Paket ".$data->NamaBarang,"No"=>$i,"Kategori"=>$dataPaket->NamaDepartement,"Supplier"=>$dataPaket->NamaPerusahaan,"Harga"=>number_format($dataPaket->Harga),"Jenis"=>$dataPaket->Jenis,"IsSerialize"=>$dataPaket->IsSerialize,"Limit"=>$limit,"HPP"=>0,"IsPaket"=>1));
                    }
                }
            } else {
                if($data->IsSerialize=="0"){
                    $stokTerkirim = $db->get_var("SELECT SUM(Qty) FROM tb_return_penjualan_detail WHERE NoReturn IN (SELECT NoReturn FROM tb_return_penjualan WHERE NoPenjualan='$no_penjualan') AND IDBarang='".$dataPaket->IDBarang."' AND IsPaket='0'");
                    if(!$stokTerkirim) $stokTerkirim = 0;
                    $stokPenjualan = $db->get_var("SELECT SUM(Qty) FROM tb_penjualan_detail WHERE NoPenjualan='".$data->NoPenjualan."' AND IDBarang='".$data->IDBarang."'");
                    $limit = $stokPenjualan-$stokTerkirim;
                } else $limit = 1;
                $hpp = $db->get_var("SELECT Harga FROM tb_penjualan_detail WHERE NoPenjualan='".$data->NoPenjualan."' AND IDBarang='".$data->IDBarang."'");
                if($limit>0){
                    array_push($barang,array("IDBarang"=>$data->IDBarang,"KodeBarang"=>$data->KodeBarang,"Nama"=>$data->Nama,"No"=>$i,"Kategori"=>$data->NamaDepartement,"Supplier"=>$data->NamaPerusahaan,"Harga"=>number_format($data->Harga),"Jenis"=>$data->Jenis,"IsSerialize"=>$data->IsSerialize,"Limit"=>$limit,"HPP"=>$hpp,"IsPaket"=>0));
                }
            }
        }
    }
}

//GRAB ALL DATA PROYEK
$query = $db->get_results("SELECT a.*, b.NamaPelanggan, c.NamaDepartement FROM tb_proyek a, tb_pelanggan b, tb_departement c WHERE a.IDClient=b.IDPelanggan AND a.IDDepartement=c.IDDepartement AND a.Status='2' ORDER BY a.IDProyek, a.KodeProyek, a.Tahun, a.NamaProyek ASC");
if($query){
    $return = array();
    $i=0;
    foreach($query as $data){
        $i++;
        $client = $db->get_var("SELECT NamaPelanggan FROM tb_pelanggan WHERE IDPelanggan='".$data->IDClient."'");
        if($data->Status=="0") $status="Tender"; else if($data->Status=="1") $status="Fail"; else if($data->Status=="2") $status="Process"; else $status="Complete";
        array_push($proyek,array("IDProyek"=>$data->IDProyek,"Tahun"=>$data->Tahun,"No"=>$i,"NamaClient"=>$client,"KodeProyek"=>$data->KodeProyek,"NamaProyek"=>$data->NamaProyek,"Status"=>$status,"Departement"=>$data->NamaDepartement));
    }
}

//GRAB ALL DATA SUPPLIER
//$query = $db->get_results("SELECT a.*, b.NamaDepartement, c.Nama AS Jenis FROM tb_supplier a, tb_departement b, tb_jenis_material c WHERE a.Kategori=b.IDDepartement AND a.Kategori2=c.IDMaterial AND a.Kategori='2' ORDER BY a.NamaPerusahaan ASC");
$penjualan = array();
$query = $db->get_results("SELECT * FROM tb_penjualan ORDER BY NoPenjualan ASC");
if ($query) {
    $return = array();
    $i = 0;
    foreach ($query as $data) {
        $pelanggan = $db->get_var("SELECT NamaPelanggan FROM tb_pelanggan WHERE IDPelanggan='".$data->IDPelanggan."'");
        array_push($penjualan,array("IDPenjualan"=>$data->IDPenjualan,"IDPelanggan"=>$data->IDPelanggan,"NamaPelanggan"=>$pelanggan,"NoPenjualan"=>$data->NoPenjualan));
    }
}

$return = array("barang"=>$barang,"proyek"=>$proyek,"penjualan"=>$penjualan);
echo json_encode($return);