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
$jenispo = antiSQLInjection($_GET['jenispo']);
$ispajak = antiSQLInjection($_GET['ispajak']);
$isld = antiSQLInjection($_GET['isld']);
$keterangan = antiSQLInjection($_GET['keterangan']);

if ($datestart != "" && $dateend != "") {
    $cond = "WHERE Tanggal BETWEEN '$datestartchange' AND '$dateendchange'";
} else if ($datestart != "") {
    $cond = "WHERE Tanggal='$datestartchange'";
} else {
    $cond = "WHERE DATE_FORMAT(Tanggal,'%Y-%m') = '" . date("Y-m") . "'";
}

if ($kode_proyek != "")
    $cond .= " AND IDProyek='$kode_proyek'";

/*if($supplier != "")
    $cond .= " AND IDSupplier='$supplier'";*/

if($supplier != "")
    $cond .= " AND IDSupplier='$supplier'";

if($jenispo != "")
    $cond .= " AND JenisPO='$jenispo'";

if($isld != "")
    $cond .= " AND IsLD='$isld'";

if($keterangan != "")
    $cond .= " AND (InvPembayaran LIKE '%$keterangan%' OR Keterangan LIKE '%$keterangan%')";

if($status!="") $cond .= " AND Completed='$status'";

$cond .= " AND IsPajak='1'";

if ($_GET['param'] == "pembelian")
    $sql = "SELECT * FROM tb_po WHERE NoPO NOT IN (SELECT NoPO FROM tb_pembelian) ORDER BY IDPO ASC";
else
    $sql = "SELECT *,DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_po $cond ORDER BY IDPO ASC";
//echo $sql;
$grandTotal = $db->get_var("SELECT SUM(GrandTotal) FROM tb_po $cond ORDER BY IDPO ASC");
if(!$grandTotal) $grandTotal = 0;
$sisa = $db->get_var("SELECT SUM(Sisa) FROM tb_po $cond ORDER BY IDPO ASC");
if(!$sisa) $sisa = 0;

$query = $db->get_results($sql);
if ($query) {
    $i = 0;
    foreach ($query as $data) {
        $i++;
        $created = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $data->CreatedBy . "'");
        if ($data->KodeProyek == ""){
            $kodeProyek = "";
            $kodeProyek2 = "";
        } else{
            $proyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='".$data->IDProyek."'");
            $kodeProyek = $data->KodeProyek;
            $kodeProyek2 = $proyek->KodeProyek."/".$proyek->Tahun;
        }

        if($data->JenisPO=="1")
            $jenis_po = "PO MATERIAL";
        else if($data->JenisPO=="2")
            $jenis_po = "PO TENAGA/SUBKON";
        else if($data->JenisPO=="3")
            $jenis_po = "PO OVERHEAD";
            
        $dataSupplier = $db->get_var("SELECT NamaPerusahaan FROM tb_supplier WHERE IDSupplier = '".$data->IDSupplier."'");

        $cek = $db->get_row("SELECT * FROM tb_penerimaan_stok WHERE NoPO='".$data->NoPO."'");
        if($cek){
            $allow = "0";
        } else {
            $cek = $db->get_row("SELECT * FROM tb_jurnal WHERE NoRef='".$data->IDPO."' AND Tipe='3'");
            if($cek){
                $allow = "0";
            } else {
                $allow = "1";
            }
        }
        
        if($data->FakturPajak!="")
            $fakturPajak = $data->FakturPajak;
        else
            $fakturPajak = "-";            
            
        array_push($PO, array(
            "IDPO" => $data->IDPO,
            "NoPO" => $data->NoPO,
            "No" => $i,
            "KodeProyek" => $kodeProyek2,
            "JenisPO" => $jenis_po,
            "Supplier" => $dataSupplier,
            "Tanggal" => $data->TanggalID,
            "PPN" => $data->PPN,
            "GrandTotal" => $data->GrandTotal,
            "PembayaranDP" => $data->PembayaranDP,
            "Keterangan" => $data->Keterangan,
            "Completed" => $data->Completed,
            "CreatedBy" => $created,
            "TextSelectBox" => $data->NoPO . " - " . $kodeProyek,
            "AllowEdit" => $allow,
            "FakturPajak" => $fakturPajak,
            "DaftarFakturPajak" => $data->DaftarFakturPajak));
    }
}

/* LOAD PROYEK */
$proyek = array();

/* LOAD SUPPLIER */
$supplier = array();
$query = $db->get_results("SELECT * FROM tb_supplier ORDER BY NamaPerusahaan ASC");
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
$all = $db->get_var("SELECT COUNT(*) FROM tb_po WHERE IsLD='$isld' AND IsPajak='$ispajak'"); if(!$all) $all='';
$new = $db->get_var("SELECT COUNT(*) FROM tb_po WHERE IsLD='$isld' AND IsPajak='$ispajak' AND Completed='0'"); if(!$new) $new='';
$completed = $db->get_var("SELECT COUNT(*) FROM tb_po WHERE IsLD='$isld' AND IsPajak='$ispajak' AND Completed='1'"); if(!$completed) $completed='';

echo json_encode(array("po" => $PO, "proyek" => $proyek, "supplier" => $supplier,"all"=>$all,"new"=>$new,"completed"=>$completed,"grandTotal"=>$grandTotal,"sisa"=>$sisa));
