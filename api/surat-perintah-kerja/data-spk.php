<?php
include_once "../config/connection.php";

$datestart = antiSQLInjection($_GET['datestart']);
$expstart = explode("/", $datestart);
$datestartchange = $expstart[2] . "-" . $expstart[1] . "-" . $expstart[0];

$dateend = antiSQLInjection($_GET['dateend']);
$expend = explode("/", $dateend);
$dateendchange = $expend[2] . "-" . $expend[1] . "-" . $expend[0];

$karyawan = antiSQLInjection($_GET['karyawan']);

if ($datestart != "" && $dateend != "") {
    $cond = "WHERE Tanggal BETWEEN '$datestartchange' AND '$dateendchange'";
} else if ($datestart != "") {
    $cond = "WHERE Tanggal='$datestartchange'";
} else {
    $cond = "WHERE DATE_FORMAT(Tanggal,'%Y-%m') = '" . date("Y-m") . "'";
}

if ($karyawan != "")
    $cond .= " AND IDKaryawan='$karyawan'";

$SPK = array();
$query = $db->get_results("SELECT *, DATE_FORMAT(TanggalMulai, '%d/%m/%Y') AS TanggalMulaiID, DATE_FORMAT(TanggalAkhir, '%d/%m/%Y') AS TanggalAkhirID FROM tb_surat_perintah_kerja $cond ORDER BY IDSPK ASC");
if($query){
    $i=0;
    foreach($query as $data){
        $i++;
        $karyawan = $db->get_row("SELECT a.Nama, b.Jabatan FROM tb_karyawan a, tb_jabatan b WHERE a.IDJabatan=b.IDJabatan AND a.IDKaryawan='".$data->IDKaryawan."'");
        
        if($data->Status=="0") $status="Baru"; else if($data->Status=="1") $status="Approved by Director"; else $status="Finished by Employee";
        array_push($SPK,array("IDSPK"=>$data->IDSPK,"NoSPK"=>$data->NoSPK,"TanggalMulai"=>$data->TanggalMulaiID,"TanggalAkhir"=>$data->TanggalAkhirID,"Karyawan"=>$karyawan->Nama,"Bagian"=>$karyawan->Jabatan,"No"=>$i,"NamaPerusahaan"=>$data->NamaPerusahaan,"Status"=>$status));
    }
}

$Karyawan = array();
$query2 = $db->get_results("SELECT * FROM tb_karyawan WHERE IDKaryawan>1 ORDER BY Nama ASC");
if($query2){
    $i=0;
    foreach($query2 as $data2){
        $i++;
        array_push($Karyawan,array("IDKaryawan"=>$data2->IDKaryawan,"Nama"=>$data2->Nama));
    }
}

$return = array("SPK"=>$SPK, "Karyawan"=>$Karyawan);
echo json_encode($return);