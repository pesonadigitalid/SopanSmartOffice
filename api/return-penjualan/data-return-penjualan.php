<?php
include_once "../config/connection.php";

$returnPenjualan = array();

$status = antiSQLInjection($_GET['status']);

/* LOAD PO */
$datestart = antiSQLInjection($_GET['datestart']);
$expstart = explode("/", $datestart);
$datestartchange = $expstart[2] . "-" . $expstart[1] . "-" . $expstart[0];

$dateend = antiSQLInjection($_GET['dateend']);
$expend = explode("/", $dateend);
$dateendchange = $expend[2] . "-" . $expend[1] . "-" . $expend[0];

$kode_proyek = antiSQLInjection($_GET['kode_proyek']);
$supplier = antiSQLInjection($_GET['supplier']);

if ($datestart != "" && $dateend != "") {
    $cond = "WHERE Tanggal BETWEEN '$datestartchange' AND '$dateendchange'";
} else if ($datestart != "") {
    $cond = "WHERE Tanggal='$datestartchange'";
} else {
    $cond = "WHERE DATE_FORMAT(Tanggal,'%Y-%m') = '" . date("Y-m") . "'";
}

if($supplier != "")
    $cond .= " AND IDSupplier='$supplier'";

$query = $db->get_results("SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_return_penjualan $cond ORDER BY IDReturn ASC");
if ($query) {
    $i = 0;
    foreach ($query as $data) {
        $i++;
        $created = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $data->CreatedBy . "'");
            
        if($data->IDPelanggan!="") 
            $pelanggan = $db->get_var("SELECT NamaPelanggan FROM tb_pelanggan WHERE IDPelanggan='".$data->IDPelanggan."'");
        else
            $pelanggan = "-";
            
        array_push($returnPenjualan, array(
            "IDReturn" => $data->IDReturn,
            "NoReturn" => $data->NoReturn,
            "No" => $i,
            "Pelanggan" => $pelanggan,
            "NoReturnKonsumen" => $data->NoReturnKonsumen,
            "Supplier" => $dataSupplier,
            "Tanggal" => $data->TanggalID,
            "PembayaranDP" => $data->PembayaranDP,
            "GrandTotal" => $data->GrandTotal,
            "TotalItem" => $data->TotalItem,
            "CreatedBy" => $created,
            "TextSelectBox" => $data->NoPO . " - " . $kodeProyek));
    }
}

/* LOAD PROYEK */
$proyek = array();
$query = $db->get_results("SELECT a.*, b.NamaPelanggan, c.NamaDepartement FROM tb_proyek a, tb_pelanggan b, tb_departement c WHERE a.IDClient=b.IDPelanggan AND a.IDDepartement=c.IDDepartement AND a.Status='2' ORDER BY a.IDProyek ASC");
if ($query) {
    $return = array();
    $i = 0;
    foreach ($query as $data) {
        array_push($proyek, array(
            "IDProyek" => $data->IDProyek,
            "Tahun" => $data->Tahun,
            "No" => $i,
            "NamaClient" => $client,
            "KodeProyek" => $data->KodeProyek,
            "NamaProyek" => $data->NamaProyek,
            "Status" => $status,
            "Departement" => $data->NamaDepartement));
    }
}

/* LOAD SUPPLIER */
$penjualan = array();
$query = $db->get_results("SELECT * FROM tb_penjualan ORDER BY NoPenjualan ASC");
if ($query) {
    $return = array();
    $i = 0;
    foreach ($query as $data) {
        array_push($supplier, array(
            "IDPenjualan" => $data->IDPenjualan,
            "IDPelanggan" => $data->IDPelanggan,
            "NoPenjualan" => $data->NoPenjualan));
    }
}

//GRAB ALL TOTAL DATA
$all = $db->get_var("SELECT COUNT(*) FROM tb_return_penjualan"); if(!$all) $all='';

echo json_encode(array("returnPenjualan" => $returnPenjualan, "proyek" => $proyek, "penjualan" => $penjualan,"all"=>$all,"new"=>$new,"completed"=>$completed));
