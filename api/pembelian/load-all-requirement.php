<?php
include_once "../config/connection.php";

$barang = array();
$po = array();
$proyek = array();
$supplier = array();

//GRAB ALL DATA BARANG
$query = $db->get_results("SELECT a.*, b.NamaPerusahaan, c.Nama AS Jenis FROM tb_barang a, tb_supplier b, tb_jenis_material c WHERE a.IDSupplier=b.IDSupplier AND a.IDJenis=c.IDMaterial $cond ORDER BY a.IDBarang ASC");
if ($query) {
    $i = 0;
    foreach ($query as $data) {
        $i++;
        array_push($barang, array("IDBarang" => $data->IDBarang, "KodeBarang" => $data->KodeBarang, "Nama" => $data->Nama, "No" => $i, "Kategori" => "", "Supplier" => $data->NamaPerusahaan, "Harga" => number_format($data->Harga), "Jenis" => $data->Jenis));
    }
}

//GRAB ALL DATA PO
$query = $db->get_results("SELECT * FROM tb_po WHERE NoPO NOT IN (SELECT NoPO FROM tb_pembelian) ORDER BY IDPO");
if ($query) {
    $return = array();
    $i = 0;
    foreach ($query as $data) {
        $i++;
        $created = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $data->CreatedBy . "'");
        if ($data->KodeProyek == "") $kodeProyek = "UMUM";
        else $kodeProyek = $data->KodeProyek;
        array_push($po, array("IDPO" => $data->IDPO, "NoPO" => $data->NoPO, "No" => $i, "KodeProyek" => $data->KodeProyek, "Tanggal" => $data->TanggalID, "GrandTotal" => $data->GrandTotal, "PembayaranDP" => $data->PembayaranDP, "CreatedBy" => $created, "TextSelectBox" => $data->NoPO . " - " . $kodeProyek));
    }
}

//GRAB ALL DATA PROYEK
$query = $db->get_results("SELECT a.*, b.NamaPelanggan, c.NamaDepartement FROM tb_proyek a, tb_pelanggan b, tb_departement c WHERE a.IDClient=b.IDPelanggan AND a.IDDepartement=c.IDDepartement AND a.Status='2' ORDER BY a.IDProyek DESC");
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
$query = $db->get_results("SELECT a.*, b.NamaDepartement FROM tb_supplier a, tb_departement b WHERE a.Kategori=b.IDDepartement ORDER BY a.IDSupplier ASC");
if ($query) {
    $return = array();
    $i = 0;
    foreach ($query as $data) {
        $i++;
        array_push($supplier, array("IDSupplier" => $data->IDSupplier, "No" => $i, "KodeSupplier" => $data->KodeSupplier, "Nama" => $data->NamaPerusahaan, "Provinsi" => $data->Provinsi, "NoTelp" => $data->NoTelp, "NoFax" => $data->NoFax, "Departement" => $data->NamaDepartement, "JenisProduk" => $data->Jenis));
    }
}

$return = array("barang" => $barang, "po" => $po, "proyek" => $proyek, "supplier" => $supplier);
echo json_encode($return);
