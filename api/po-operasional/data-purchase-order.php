<?php
include_once "../config/connection.php";

$PO = array();

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

if ($kode_proyek != "")
    $cond .= " AND IDProyek='$kode_proyek'";

if($supplier != "")
    $cond .= " AND IDSupplier='$supplier'";

if($status!="") $cond.="AND Completed='$status'";

if ($_GET['param'] == "pembelian")
    $sql = "SELECT * FROM tb_po WHERE NoPO NOT IN (SELECT NoPO FROM tb_pembelian) ORDER BY IDPO ASC";
else
    $sql = "SELECT *,DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_po $cond ORDER BY IDPO ASC";

$query = $db->get_results($sql);
if ($query) {
    $i = 0;
    foreach ($query as $data) {
        $i++;
        $created = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $data->CreatedBy . "'");
        if ($data->KodeProyek == "")
            $kodeProyek = "";
        else
            $kodeProyek = $data->KodeProyek;
            
        $dataSupplier = $db->get_var("SELECT NamaPerusahaan FROM tb_supplier WHERE IDSupplier = '".$data->IDSupplier."'");
            
            
        array_push($PO, array(
            "IDPO" => $data->IDPO,
            "NoPO" => $data->NoPO,
            "No" => $i,
            "KodeProyek" => $data->KodeProyek,
            "Supplier" => $dataSupplier,
            "Tanggal" => $data->TanggalID,
            "GrandTotal" => $data->GrandTotal,
            "PembayaranDP" => $data->PembayaranDP,
            "Keterangan" => $data->Keterangan,
            "Completed" => $data->Completed,
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
$supplier = array();
$query = $db->get_results("SELECT * FROM tb_supplier WHERE Kategori='2' ORDER BY IDSupplier ASC");
if ($query) {
    $return = array();
    $i = 0;
    foreach ($query as $data) {
        array_push($supplier, array(
            "IDSupplier" => $data->IDSupplier,
            "NamaSupplier" => $data->NamaPerusahaan));
    }
}

//GRAB ALL TOTAL DATA
$all = $db->get_var("SELECT COUNT(*) FROM tb_po"); if(!$all) $all='';
$new = $db->get_var("SELECT COUNT(*) FROM tb_po WHERE Completed='0'"); if(!$new) $new='';
$completed = $db->get_var("SELECT COUNT(*) FROM tb_po WHERE Completed='1'"); if(!$completed) $completed='';

echo json_encode(array("po" => $PO, "proyek" => $proyek, "supplier" => $supplier,"all"=>$all,"new"=>$new,"completed"=>$completed));
