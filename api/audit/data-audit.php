<?php
include_once "../config/connection.php";

$dataAudit = array();
$status = $_GET['status'];

/* LOAD PO */
$datestart = $_GET['datestart'];
$expstart = explode("/", $datestart);
$datestartchange = $expstart[2] . "-" . $expstart[1] . "-" . $expstart[0];

$dateend = $_GET['dateend'];
$expend = explode("/", $dateend);
$dateendchange = $expend[2] . "-" . $expend[1] . "-" . $expend[0];

if ($datestart != "" && $dateend != "") {
    $cond = "WHERE Tanggal BETWEEN '$datestartchange' AND '$dateendchange'";
} else if ($datestart != "") {
    $cond = "WHERE Tanggal='$datestartchange'";
} else {
    $cond = "WHERE DATE_FORMAT(Tanggal,'%Y-%m') = '" . date("Y-m") . "'";
}

$sql = "SELECT *,DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_audit $cond AND (Status='1' OR Status='2') ORDER BY NoAudit ASC";

$query = $db->get_results($sql);
if ($query) {
    $i = 0;
    foreach ($query as $data) {
        $i++;
        $created = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $data->CreatedBy . "'");
        $gudang = $db->get_row("SELECT * FROM tb_gudang WHERE IDGudang='" . $data->IDGudang . "'");

        array_push($dataAudit, array(
            "IDAudit" => $data->IDAudit,
            "NoAudit" => $data->NoAudit,
            "Gudang" => $gudang->Nama,
            "No" => $i,
            "Tanggal" => $data->TanggalID,
            "TotalItem" => $data->TotalItem,
            "Keterangan" => $data->Keterangan,
            "CreatedBy" => $created,
            "Status" => $data->Status
        ));
    }
}

echo json_encode(array("data" => $dataAudit));
