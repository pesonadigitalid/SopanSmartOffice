<?php
include_once "../config/connection.php";
$cat = antiSQLInjection($_GET['kategori']);
if ($cat != "0" && $cat != "") {
    $sql = "SELECT a.*, b.NamaDepartement FROM tb_pelanggan a, tb_departement b WHERE a.Kategori=b.IDDepartement AND a.Kategori='$cat' ORDER BY a.IDPelanggan ASC";
} else {
    $sql = "SELECT a.*, b.NamaDepartement FROM tb_pelanggan a, tb_departement b WHERE a.Kategori=b.IDDepartement ORDER BY IDPelanggan ASC";
}
$query = $db->get_results($sql);
if ($query) {
    $return = array();
    $i = 0;
    foreach ($query as $data) {
        $i++;

        $pelangganFile = array();
        if ($cat == "4") {
            $qFileCategory = $db->get_results("SELECT * FROM tb_pelanggan_file_category ORDER BY IDFileCategory ASC");
            if ($qFileCategory) {
                foreach ($qFileCategory as $dFileCategory) {
                    $totalFile = $db->get_var("SELECT COUNT(*) FROM tb_pelanggan_file WHERE IDFileCategory='$dFileCategory->IDFileCategory' AND IDPelanggan='$data->IDPelanggan'");
                    if (!$totalFile) $totalFile = 0;

                    array_push($pelangganFile, intval($totalFile));
                }
            }
        }

        array_push($return, array("IDPelanggan" => $data->IDPelanggan, "No" => $i, "KodePelanggan" => $data->KodePelanggan, "Nama" => $data->NamaPelanggan, "Provinsi" => $data->Provinsi, "NoTelp" => $data->NoTelp, "Jenis" => $data->Jenis, "Departement" => $data->NamaDepartement, "IDDepartement" => $data->Kategori, "PelangganFile" => $pelangganFile));
    }
    echo json_encode($return);
} else {
    echo json_encode(array());
}
