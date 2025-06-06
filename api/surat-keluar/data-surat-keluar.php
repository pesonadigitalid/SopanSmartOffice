<?php
include_once "../config/connection.php";

$datestart = antiSQLInjection($_GET['datestart']);
$expstart = explode("/", $datestart);
$datestartchange = $expstart[2] . "-" . $expstart[1] . "-" . $expstart[0];

$dateend = antiSQLInjection($_GET['dateend']);
$expend = explode("/", $dateend);
$dateendchange = $expend[2] . "-" . $expend[1] . "-" . $expend[0];

$keyword = antiSQLInjection($_GET['keyword']);
$kode_proyek = antiSQLInjection($_GET['kode_proyek']);

if ($datestart != "" && $dateend != "") {
    $cond = "WHERE Tanggal BETWEEN '$datestartchange' AND '$dateendchange'";
} else if ($datestart != "") {
    $cond = "WHERE Tanggal='$datestartchange'";
} else {
    $cond = "WHERE DATE_FORMAT(Tanggal,'%Y-%m') = '" . date("Y-m") . "'";
}

if ($kode_proyek != "") {
    $cond .= " AND IDProyek='$kode_proyek'";
}

if ($keyword != "") {
    $cond .= " AND (Prihal LIKE '%$keyword%' OR Deskripsi LIKE '%$keyword%')";
}

$query = $db->get_results("SELECT *,DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_surat_keluar $cond AND Status>'0' ORDER BY IDSuratKeluar DESC");
$return = array();
if ($query) {
    $i = 0;
    foreach ($query as $data) {
        $i++;
        $proyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='" . $data->IDProyek . "'");
        $departement = $db->get_var("SELECT NamaDepartement FROM tb_departement WHERE IDDepartement='" . $data->IDDepartement . "'");
        array_push($return, array("IDSuratKeluar" => $data->IDSuratKeluar, "No" => $i, "NoSurat" => $data->NoSurat, "Proyek" => $proyek->NamaProyek, "KodeProyek" => $proyek->KodeProyek, "TahunProyek" => $proyek->Tahun, "Tanggal" => $data->TanggalID, "Prihal" => $data->Prihal, "FileSurat" => $data->FileSurat, "Deskripsi" => $data->Deskripsi, "Jenis" => $data->Jenis, "Departement" => $departement));
    }
}

$proyek = array();
$query = $db->get_results("SELECT a.*, b.NamaPelanggan, c.NamaDepartement FROM tb_proyek a, tb_pelanggan b, tb_departement c WHERE a.IDClient=b.IDPelanggan AND a.IDDepartement=c.IDDepartement AND (a.Status='2' || a.Status='3') ORDER BY a.Tahun ASC, a.KodeProyek ASC");
if ($query) {
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

echo json_encode(array("data" => $return, "proyek" => $proyek));
