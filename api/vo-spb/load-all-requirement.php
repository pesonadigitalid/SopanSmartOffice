<?php
include_once "../config/connection.php";
$id = antiSQLInjection($_GET['id_penjualan']);

$spb = $db->get_row("SELECT a.*, b.NamaPelanggan FROM tb_penjualan a, tb_pelanggan b WHERE a.IDPelanggan=b.IDPelanggan AND a.IDPenjualan='" . $id . "'");
$dSPB = array("NoPenjualan" => $spb->NoPenjualan, "NamaPelanggan" => $spb->NamaPelanggan, "PPNPersen" => $spb->PPNPersen, "DiskonPersen" => $spb->DiskonPersen);

$barangArray = array();
$query = $db->get_results("SELECT * FROM tb_barang ORDER BY Nama");
if ($query) {
    $i = 0;
    foreach ($query as $data) {
        $i++;
        
        $HPP = $data->Harga;
        $HargaJual = $data->HargaJual;
        $Diskon = 0;
        $HargaDiskon = $data->HargaJual;
        $EditableDiskonAndPrice = true;
        $barangDetail = $db->get_row("SELECT a.* FROM tb_penjualan_detail a, tb_penjualan b WHERE a.NoPenjualan=b.NoPenjualan AND a.IDBarang='$data->IDBarang' AND b.IDPenjualan='$id'");
        if ($barangDetail) {
            $HPP = $barangDetail->HargaBeli;
            $HargaJual = $barangDetail->Harga;
            $Diskon = $barangDetail->Diskon;
            $HargaDiskon = $barangDetail->HargaDiskon;
            $EditableDiskonAndPrice = false;
        }

        array_push($barangArray, array("IDBarang" => $data->IDBarang, "KodeBarang" => $data->KodeBarang, "Nama" => $data->Nama, "No" => $i, "Harga" => $HPP, "HargaJual" => $HargaJual, "IsSerialize" => "0", "Limit" => "1000000", "HPP" => $data->Harga, "HPPReal" => 0, "Diskon" => $Diskon, "HargaDiskon" => $HargaDiskon, "EditableDiskonAndPrice" => $EditableDiskonAndPrice));
    }
}

$return = array("barang" => $barangArray, "spb" => $dSPB);
echo json_encode($return);
