<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_GET['id']);

$query = $db->get_row("SELECT * FROM tb_gaji_karyawan WHERE IDGaji='$id' ORDER BY IDGaji ASC");
if($query){
    if($query->EfektifBulan<10)
        $efektifBulan = "0".$query->EfektifBulan;
    else 
        $efektifBulan = $query->EfektifBulan;
        
    $return = array("idgaji"=>$query->IDGaji,"idkaryawan"=>$query->IDKaryawan,"efektif_bln"=>$efektifBulan,"efektif_thn"=>$query->EfektifTahun,"gaji_pokok"=>$query->GajiPokok,"uang_makan"=>$query->UangMakan,"uang_pulsa"=>$query->UangPulsa,"uang_transport"=>$query->UangTransport,"uang_performance"=>$query->UangPerformance,"uang_lain2"=>$query->LainLain);
}
echo json_encode($return);