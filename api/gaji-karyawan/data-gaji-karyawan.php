<?php
include_once "../config/connection.php";
$id = antiSQLInjection($_GET['id_karyawan']);
$bulan = array(1=>"JAN",2=>"FEB",3=>"MAR",4=>"APR",5=>"MEI",6=>"JUN",7=>"JUL",8=>"AGT",9=>"SEP",10=>"OKT",11=>"NOV",12=>"DES");
$query = $db->get_results("SELECT * FROM tb_gaji_karyawan WHERE IDKaryawan='$id' ORDER BY IDGaji DESC");
if($query){
    $return = array();
    $i=0;
    $old = "";
    foreach($query as $data){
        $i++;
        if($i>1){
            if($data->EfektifBulan==0)
                $hasilbulan = 12;
            else
                $hasilbulan = $data->EfektifBulan;
        } else {
            $hasilbulan = $data->EfektifBulan;
        }
            
        if($data->Status=="1"){
            $periode = $bulan[$hasilbulan]." ".$data->EfektifTahun." - "."SEKARANG";
        } else {
            $periode = $bulan[$hasilbulan]." ".$data->EfektifTahun." - ".$old;
        }
        $old = $bulan[($hasilbulan-1)]." ".$data->EfektifTahun;
            
        
        $jabatan = $db->get_var("SELECT Jabatan FROM tb_jabatan WHERE IDJabatan='".$data->IDJabatan."'");
        array_push($return,array("No"=>$i,"IDGaji"=>$data->IDGaji,"Periode"=>$periode,"GajiPokok"=>$data->GajiPokok,"UangMakan"=>$data->UangMakan,"UangPulsa"=>$data->UangPulsa,"UangTransport"=>$data->UangTransport,"UangPerformance"=>$data->UangPerformance,"LainLain"=>$data->LainLain,"Total"=>$data->Total));
    }
    echo json_encode($return);
} else {
    echo json_encode(array());
}
