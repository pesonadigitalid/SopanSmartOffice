<?php
include_once "../config/connection.php";
$idr = antiSQLInjection($_POST['idr']);

$dataReimburse = $db->get_row("SELECT * FROM tb_reimburse WHERE IDReimburse='$idr'");
if($dataReimburse){
    if($dataReimburse->Kategori=="Reimburse Proyek" && $dataReimburse->Status=="1")
        $db->query("UPDATE tb_proyek SET PengeluaranOverHead=(PengeluaranOverHead-".$dataReimburse->TotalNilai.") WHERE IDProyek='".$dataReimburse->IDProyek."'");
}

$query = $db->query("DELETE FROM tb_reimburse WHERE IDReimburse='$idr'");
if($query){
    $db->query("DELETE FROM tb_jurnal_detail WHERE JurnalRef='".$dataReimburse->NoReimburse."'");
    echo "1";
} else {
    echo "0";
}