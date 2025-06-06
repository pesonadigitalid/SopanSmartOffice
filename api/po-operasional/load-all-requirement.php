<?php
include_once "../config/connection.php";

$barang = array();
$proyek = array();
$supplier = array();

//GRAB ALL DATA BARANG
$query = $db->get_results("SELECT a.*, b.NamaPerusahaan, c.Nama AS Jenis FROM tb_barang a, tb_supplier b, tb_jenis_material c, tb_departement d WHERE a.IDSupplier=b.IDSupplier AND a.IDJenis=c.IDMaterial $cond ORDER BY a.Nama ASC");
if ($query) {
    $i = 0;
    foreach ($query as $data) {
        $i++;
        array_push($barang, array("IDBarang" => $data->IDBarang, "KodeBarang" => $data->KodeBarang, "Nama" => $data->Nama, "No" => $i, "Kategori" => "", "Supplier" => $data->NamaPerusahaan, "Harga" => number_format($data->Harga), "Jenis" => $data->Jenis));
    }
}

//GRAB ALL DATA PROYEK
$query = $db->get_results("SELECT a.*, b.NamaPelanggan, c.NamaDepartement FROM tb_proyek a, tb_pelanggan b, tb_departement c WHERE a.IDClient=b.IDPelanggan AND a.IDDepartement=c.IDDepartement AND a.Status='2' ORDER BY a.IDProyek ASC");
if ($query) {
    $return = array();
    $i = 0;
    foreach ($query as $data) {
        $i++;
        $client = $db->get_var("SELECT NamaPelanggan FROM tb_pelanggan WHERE IDPelanggan='" . $data->IDClient . "'");
        if ($data->Status == "0") $status = "Tender";
        else if ($data->Status == "1") $status = "Fail";
        else if ($data->Status == "2") $status = "Process";
        else $status = "Complete";
        array_push($proyek, array("IDProyek" => $data->IDProyek, "Tahun" => $data->Tahun, "No" => $i, "NamaClient" => $client, "KodeProyek" => $data->KodeProyek, "NamaProyek" => $data->NamaProyek, "Status" => $status, "Departement" => $data->NamaDepartement));
    }
}

//GRAB ALL DATA SUPPLIER
$query = $db->get_results("SELECT a.*, b.NamaDepartement FROM tb_supplier a, tb_departement b WHERE a.Kategori=b.IDDepartement ORDER BY a.NamaPerusahaan ASC");
if ($query) {
    $return = array();
    $i = 0;
    foreach ($query as $data) {
        $i++;
        array_push($supplier, array("IDSupplier" => $data->IDSupplier, "No" => $i, "KodeSupplier" => $data->KodeSupplier, "Nama" => $data->NamaPerusahaan, "Provinsi" => $data->Provinsi, "NoTelp" => $data->NoTelp, "NoFax" => $data->NoFax, "Departement" => $data->NamaDepartement, "JenisProduk" => $data->Jenis));
    }
}

$return = array("barang" => $barang, "proyek" => $proyek, "supplier" => $supplier);
echo json_encode($return);
