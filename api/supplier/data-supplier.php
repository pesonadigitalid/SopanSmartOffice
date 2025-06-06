<?php
include_once "../config/connection.php";
$cat = antiSQLInjection($_GET['kategori']);
if ($cat != "0" && $cat != "") {
    $sql = "SELECT * FROM tb_supplier WHERE Kategori='" . $_GET['kategori'] . "' ORDER BY IDSupplier ASC";
} else {
    //$sql = "SELECT a.*, b.NamaDepartement, c.Nama AS Jenis FROM tb_supplier a, tb_departement b, tb_jenis_material c WHERE a.Kategori=b.IDDepartement AND a.Kategori2=c.IDMaterial ORDER BY a.IDSupplier ASC";  
    $sql = "SELECT * FROM tb_supplier ORDER BY IDSupplier ASC";
}
$query = $db->get_results($sql);
if ($query) {
    $return = array();
    $i = 0;
    foreach ($query as $data) {
        $i++;
        $departement = $db->get_var("SELECT NamaDepartement FROM tb_departement WHERE IDDepartement='" . $data->Kategori . "'");
        $jenis = $db->get_var("SELECT Nama FROM tb_jenis_material WHERE IDMaterial='" . $data->Kategori2 . "'");
        array_push($return, array("IDSupplier" => $data->IDSupplier, "No" => $i, "KodeSupplier" => $data->KodeSupplier, "Nama" => $data->NamaPerusahaan, "Provinsi" => $data->Provinsi, "NoTelp" => $data->NoTelp, "Email" => $data->Email, "NoFax" => $data->NoFax, "Departement" => $departement, "IDDepartement" => $data->Kategori, "JenisProduk" => $jenis));
    }
    echo json_encode($return);
} else {
    echo json_encode(array());
}
