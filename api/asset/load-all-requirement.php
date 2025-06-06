<?php
include_once "../config/connection.php";
$id = $_GET['id'];

$dataAssign = array();

//GRAB ALL DATA ASSIGN
$query = $db->get_results("SELECT a.*,b.*, DATE_FORMAT(a.Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_assign a, tb_assign_detail b WHERE a.IDAssign=b.IDAssign AND b.IDAsset='$id' AND a.Status<2 ORDER BY b.IDAssignDetail ASC");
if ($query) {
    $i = 1;
    foreach ($query as $data) {
        $karyawan = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $data->IDKaryawan . "'");
        if (!$karyawan) $karyawan = "UMUM";
        $proyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='" . $data->IDProyek . "'");
        if ($proyek != "") $proyekName = $proyek->KodeProyek . " / " . $proyek->Tahun . " / " . $proyek->NamaProyek;
        else $proyekName = "-";
        array_push($dataAssign, array("IDAssign" => $data->IDAssign, "NoAssign" => $data->NoAssign, "No" => $i, "Tanggal" => $data->TanggalID, "Status" => $data->Status, "Karyawan" => $karyawan, "TotalItem" => $data->TotalItem, "Proyek" => $proyekName));
        $i++;
    }
}

$return = array("dataAssign" => $dataAssign);
echo json_encode($return);
