<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_GET['id']);

$barang = array();
$query = $db->get_results("SELECT * FROM tb_barang WHERE Kategori='" . $_SESSION["departement"] . "' AND IDParent='0' ORDER BY Nama ASC");
if ($query) {
    foreach ($query as $data) {
        array_push($barang, array("IDBarang" => $data->IDBarang, "KodeBarang" => $data->KodeBarang, "Nama" => $data->Nama));
    }
}

$material = array();
$query = $db->get_results("SELECT * FROM tb_jenis_material WHERE IDMaterial IS NOT NULL ORDER BY Nama ASC");
if ($query) {
    foreach ($query as $data) {
        array_push($material, array("IDMaterial" => $data->IDMaterial, "Nama" => $data->Nama));
    }
}

$departement = array();
$query = $db->get_results("SELECT * FROM tb_departement ORDER BY NamaDepartement ASC");
if ($query) {
    foreach ($query as $data) {
        $i++;
        array_push($departement, array("IDDepartement" => $data->IDDepartement, "NamaDepartement" => $data->NamaDepartement));
    }
}

$satuan = array();
$query = $db->get_results("SELECT * FROM tb_satuan ORDER BY Nama ASC");
if ($query) {
    foreach ($query as $data) {
        array_push($satuan, array("IDSatuan" => $data->IDSatuan, "Nama" => $data->Nama));
    }
}

$supplier = array();
$query = $db->get_results("SELECT * FROM tb_supplier ORDER BY NamaPerusahaan ASC");
if ($query) {
    foreach ($query as $data) {
        array_push($supplier, array("IDSupplier" => $data->IDSupplier, "NamaPerusahaan" => $data->NamaPerusahaan));
    }
}

$detail = array();
$query = $db->get_row("SELECT * FROM tb_barang WHERE IDBarang='$id' ORDER BY IDBarang ASC");
if ($query) {
    $detail = array("kode_barang" => $query->KodeBarang, "nama" => $query->Nama, "kategori" => $query->Kategori, "jenis" => $query->IDJenis, "supplier" => $query->IDSupplier, "harga" => $query->Harga, "hargaPublish" => $query->HargaPublish, "diskonPersen" => $query->DiskonPersen, "hargajual" => $query->HargaJual, "margin" => $query->Margin, "hargajualgrosir" => $query->HargaJualGrosir, "margingrosir" => $query->MarginGrosir, "satuan" => $query->IDSatuan, "parent" => $query->IDParent, "isSerial" => $query->IsSerialize, "isBarang" => $query->IsBarang, "foto1" => $query->Foto1, "foto2" => $query->Foto2, "foto3" => $query->Foto3, "libCode" => $query->LibCode, "isSellingProduct" => $query->IsSellingProduct, "IsBarangPPN" => $query->IsBarangPPN, "PPNPersen" => $query->PPNPersen, "DPP" => $query->DPP, "IsNotifiedService6" => $query->IsNotifiedService6, "IsNotifiedService12" => $query->IsNotifiedService12, "IsNotifiedService18" => $query->IsNotifiedService18);
}

$barang_child = array();
$query = $db->get_results("SELECT a.*, b.KodeBarang, b.Nama, b.Harga FROM tb_barang_child a, tb_barang b WHERE a.IDBarang=b.`IDBarang` AND a.IDParent='$id' ORDER BY IDBarangChildren ASC");
if ($query) {
    $i = 1;
    foreach ($query as $data) {
        array_push($barang_child, array("IDBarangChildren" => $data->IDBarangChildren, "No" => $i, "KodeBarang" => $data->KodeBarang, "Nama" => $data->Nama, "Qty" => $data->Qty, "Harga" => $data->HargaPublish, "Total" => ($data->Qty * $data->HargaPublish)));
        $i++;
    }
}

echo json_encode(array("barang" => $barang, "material" => $material, "departement" => $departement, "satuan" => $satuan, "supplier" => $supplier, "detail" => $detail, "barang_child" => $barang_child));
