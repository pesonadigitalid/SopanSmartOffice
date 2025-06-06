<?php
include_once "../config/connection.php";

$barang = array();
$proyek = array();
$supplier = array();

$supplier = antiSQLInjection($_GET['supplier']);

if ($supplier == "") {
} else {
    $query = $db->get_results("SELECT * FROM tb_penerimaan_stok_detail WHERE NoPenerimaanBarang IN (SELECT NoPenerimaanBarang FROM tb_penerimaan_stok WHERE NoPO IN (SELECT NoPO FROM tb_po WHERE IDSupplier='$supplier')) GROUP BY IDBarang");
    if ($query) {
        $i = 0;
        foreach ($query as $data) {
            $i++;
            $dbarang = $db->get_row("SELECT * FROM tb_barang WHERE IDBarang='" . $data->IDBarang . "'");
            array_push($barang, array("IDBarang" => $data->IDBarang, "KodeBarang" => $dbarang->KodeBarang, "Nama" => $dbarang->Nama, "No" => $i, "IsSerialize" => $dbarang->IsSerialize, "Limit" => $limit, "HPP" => $dbarang->Harga, "IsPaket" => 0));
        }
    }
}

//GRAB ALL DATA PROYEK
$query = $db->get_results("SELECT a.*, b.NamaPelanggan, c.NamaDepartement FROM tb_proyek a, tb_pelanggan b, tb_departement c WHERE a.IDClient=b.IDPelanggan AND a.IDDepartement=c.IDDepartement AND a.Status='2' ORDER BY a.IDProyek, a.KodeProyek, a.Tahun, a.NamaProyek ASC");
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
//$query = $db->get_results("SELECT a.*, b.NamaDepartement, c.Nama AS Jenis FROM tb_supplier a, tb_departement b, tb_jenis_material c WHERE a.Kategori=b.IDDepartement AND a.Kategori2=c.IDMaterial AND a.Kategori='2' ORDER BY a.NamaPerusahaan ASC");
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
