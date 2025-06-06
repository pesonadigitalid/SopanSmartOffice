<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_GET['id']);

$query = $db->get_row("SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID, DATE_FORMAT(TanggalMulai, '%d/%m/%Y') AS TanggalMulaiID, DATE_FORMAT(TanggalAkhir, '%d/%m/%Y') AS TanggalAkhirID FROM tb_surat_perintah_kerja WHERE IDSPK='$id' ORDER BY IDSPK ASC");
if($query){
    $karyawan = $db->get_row("SELECT * FROM tb_karyawan WHERE IDKaryawan='".$query->IDKaryawan."'");
    $jabatan = $db->get_var("SELECT Jabatan FROM tb_jabatan WHERE IDJabatan='".$karyawan->IDJabatan."'");
    $return = array("no_spk"=>$query->NoSPK,"tanggal"=>$query->TanggalID,"karyawan"=>$query->IDKaryawan,"nik"=>$karyawan->NIK,"bagian"=>$jabatan,"nama_perusahaan"=>$query->NamaPerusahaan,"alamat"=>$query->Alamat,"no_telp"=>$query->Telp,"rencana_tugas"=>$query->RencanaTugas,"tgl_mulai"=>$query->TanggalMulaiID,"tgl_akhir"=>$query->TanggalAkhirID,"jam_mulai"=>$query->JamMulai,"jam_akhir"=>$query->JamAkhir,"catatan"=>$query->Catatan,"statusspk"=>$query->Status);
}
echo json_encode($return);