<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_GET['id']);

$query = $db->get_row("SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_slip_gaji WHERE IDKaryawan='$id' ORDER BY IDKaryawan ASC");
if($query){
    $return = array("no_slip"=>$query->NoSlip,"tanggal"=>$query->TanggalID,"bulan"=>$query->GajiBulan,"tahun"=>$query->GajiTahun,"total_absen"=>$query->TotalAbsen,"nik"=>$query->NIK,"nama"=>$query->NamaKaryawan,"gaji_pokok"=>$query->GajiPokok,"total_uang_makan"=>$query->UangMakan,"total_uang_transport"=>$query->UangTransport,"uang_pulsa"=>$query->UangPulsa,"tunjangan_performance"=>$query->UangTunjanganPerformance,"total_gaji"=>$query->TotalGaji,"uang_makan_perhari"=>$query->UangMakanHarian,"uang_transport_perhari"=>$query->UangTransportHarian);
}
echo json_encode($return);