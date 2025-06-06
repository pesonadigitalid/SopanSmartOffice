<?php
include_once "../config/connection.php";

$barang = array();
if ($_GET['departement'] && !$_GET['id_jenis']) $cond = "AND a.Kategori='" . $_GET['departement'] . "'";
else if ($_GET['id_jenis'] && !$_GET['departement']) $cond = "AND a.IDJenis='" . $_GET['id_jenis'] . "'";
else if ($_GET['id_jenis'] && $_GET['departement']) $cond = "AND a.IDJenis='" . $_GET['id_jenis'] . "' AND a.Kategori='" . $_GET['departement'] . "'";

if ($_GET['nama']) $cond .= " AND a.Nama LIKE '%" . $_GET['nama'] . "%'";

$query = $db->get_results("SELECT a.*, b.NamaPerusahaan, c.Nama AS Jenis FROM tb_barang a, tb_supplier b, tb_jenis_material c WHERE a.IDSupplier=b.IDSupplier AND a.IDJenis=c.IDMaterial $cond ORDER BY a.IDBarang ASC");
if ($query) {
    $i = 0;
    foreach ($query as $data) {
        $i++;
        array_push($barang, array("IDBarang" => $data->IDBarang, "KodeBarang" => $data->KodeBarang, "Nama" => $data->Nama, "No" => $i, "Kategori" => "", "IDDepartement" => $data->Kategori, "Supplier" => $data->NamaPerusahaan, "Harga" => number_format($data->Harga), "Jenis" => $data->Jenis));
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

echo json_encode(array("barang" => $barang, "material" => $material, "departement" => $departement));
