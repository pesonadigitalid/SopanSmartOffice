<?php
include_once "../config/connection.php";


$id_gudang = antiSQLInjection($_GET['id_gudang']);
$barang = array();

//GRAB ALL DATA BARANG
if ($id_gudang != "") {
    $query = $db->get_results("SELECT a.*, b.NamaPerusahaan, c.Nama AS Jenis FROM tb_barang a, tb_supplier b, tb_jenis_material c WHERE a.IDSupplier=b.IDSupplier AND a.IDJenis=c.IDMaterial $cond AND a.IsBarang='1' ORDER BY a.Nama ASC");
    if ($query) {
        $i = 0;
        foreach ($query as $data) {
            $stokGudang = $db->get_var("SELECT SUM(SisaStok) FROM tb_stok_gudang WHERE IDGudang='$id_gudang' AND IDBarang='$data->IDBarang'");
            if (!$stokGudang) $stokGudang = 0;
            $stokPurchasing = $db->get_var("SELECT SUM(SisaStok) FROM tb_stok_purchasing WHERE IDGudang='$id_gudang' AND IDBarang='$data->IDBarang'");
            if (!$stokPurchasing) $stokPurchasing = 0;
            $i++;
            $isPaket = $db->get_results("SELECT a.*, b.* FROM tb_barang_child a, tb_barang b WHERE a.IDBarang=b.IDBarang AND a.IDParent='" . $data->IDBarang . "'");
            if (!$isPaket) {
                array_push($barang, array("IDBarang" => $data->IDBarang, "KodeBarang" => $data->KodeBarang, "Nama" => $data->Nama, "No" => $i, "Kategori" => $data->NamaDepartement, "Supplier" => $data->NamaPerusahaan, "Harga" => round($data->Harga), "Jenis" => $data->Jenis, "StokGudang" => $stokGudang, "StokPurchasing" => $stokPurchasing, "IsSerialize" => $data->IsSerialize));
            }
        }
    }
}

//GRAB ALL DATA PENJUALAN
// $query = $db->get_results("SELECT a.*, b.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_penjualan a, tb_pelanggan b WHERE a.IDPelanggan=b.IDPelanggan AND a.Tipe='1' ORDER BY IDPenjualan ASC");
$penjualanArray = array();
// if ($query) {
//     $i = 0;
//     foreach ($query as $data) {
//         $i++;
//         array_push($penjualanArray, array("IDPenjualan" => $data->IDPenjualan, "NoPenjualan" => $data->NoPenjualan, "IDPelanggan" => $data->IDPelanggan, "Pelanggan" => $data->NamaPelanggan, "Tanggal" => $data->TanggalID, "IsComplete" => $data->IsComplete, "No" => $i));
//     }
// }

$dataGudang = $db->get_results("SELECT * FROM tb_gudang ORDER BY Nama ASC");
if (!$dataGudang) $dataGudang = array();

$return = array("barang" => $barang, "penjualan" => $penjualanArray, "gudang" => $dataGudang);
echo json_encode($return);
