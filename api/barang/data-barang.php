<?php
include_once "../config/connection.php";
if ($_GET['departement'] && !$_GET['id_jenis']) $cond = "AND a.Kategori='" . $_GET['departement'] . "'";
else if ($_GET['id_jenis'] && !$_GET['departement']) $cond = "AND a.IDJenis='" . $_GET['id_jenis'] . "'";
else if ($_GET['id_jenis'] && $_GET['departement']) $cond = "AND a.IDJenis='" . $_GET['id_jenis'] . "' AND a.Kategori='" . $_GET['departement'] . "'";
$query = $db->get_results("SELECT a.*, b.NamaPerusahaan, c.Nama AS Jenis FROM tb_barang a, tb_supplier b, tb_jenis_material c WHERE a.IDSupplier=b.IDSupplier AND a.IDJenis=c.IDMaterial $cond ORDER BY a.IDBarang ASC");
if ($query) {
    $return = array();
    $i = 0;
    foreach ($query as $data) {
        $i++;
        array_push($return, array("IDBarang" => $data->IDBarang, "KodeBarang" => $data->KodeBarang, "Nama" => $data->Nama, "No" => $i, "Kategori" => "", "IDDepartement" => $data->Kategori, "Supplier" => $data->NamaPerusahaan, "Harga" => number_format($data->Harga), "Jenis" => $data->Jenis));
    }
    echo json_encode($return);
} else {
    echo json_encode(array());
}
