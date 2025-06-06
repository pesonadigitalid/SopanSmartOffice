<?php
include_once "../config/connection.php";
$bln = date("m");
$thn = date("Y");
$bulan = antiSQLInjection($_GET['bulan']);
$tahun = antiSQLInjection($_GET['tahun']);
$status = antiSQLInjection($_GET['status']);

$cond = "WHERE GajiBulan='$bulan' AND GajiTahun='$tahun'";

$return = array();

$query = $db->get_results("SELECT * FROM tb_slip_gaji $cond AND Status='$status' AND Harian='0' ORDER BY IDKaryawan");
if($query){
    $i=0;
    foreach($query as $data){
        $i++;

        $karyawan = $db->get_row("SELECT * FROM tb_karyawan WHERE IDKaryawan='".$data->IDKaryawan."'");
        $jabatan = $db->get_var("SELECT Jabatan FROM tb_jabatan WHERE IDJabatan='".$karyawan->IDJabatan."'");
        array_push($return,array("IDSlipGaji"=>$data->IDSlipGaji,"IDKaryawan"=>$data->IDKaryawan,"No"=>$i,"NIK"=>$karyawan->NIK,"Nama"=>$karyawan->Nama,"Jabatan"=>$jabatan,"NIK"=>$karyawan->NIK,"Nama"=>$karyawan->Nama,"Payroll"=>$status,"Payroll"=>$data->Status,"TotalGaji"=>$data->TotalGaji));
    }
}

$new = $db->get_var("SELECT COUNT(*) FROM tb_slip_gaji $cond AND Status='0' AND Harian='0'"); if(!$new) $new='';
$approved = $db->get_var("SELECT COUNT(*) FROM tb_slip_gaji $cond AND Status='1' AND Harian='0'"); if(!$approved) $approved='';
echo json_encode(array("data" => $return,"new"=>$new,"approved"=>$approved));