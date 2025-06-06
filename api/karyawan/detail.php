<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_GET['id']);
$query = $db->get_row("SELECT a.*, DATE_FORMAT(a.TglLahir,'%d/%m/%Y') AS TglLahirID,b.Jabatan FROM tb_karyawan a, tb_jabatan b WHERE a.IDJabatan=b.IDJabatan AND a.IDKaryawan='$id' ORDER BY a.IDKaryawan ASC");
if($query){
    $return = array("nik"=>$query->NIK,"nama_jabatan"=>$query->Jabatan,"thn_masuk"=>$query->TahunMasuk,"IDKaryawan"=>$query->IDKaryawan,"nama"=>$query->Nama,"jenis_kelamin"=>$query->JenisKelamin,"alamat_sementara"=>$query->AlamatSementara,"alamat_ktp"=>$query->AlamatKTP,"no_telp"=>$query->NoTelp,"email"=>$query->EmailPribadi,"stts_karyawan"=>$query->StatusKaryawan,"agama"=>$query->Agama,"stts_lainnya"=>$query->StatusLainnya,"tahunmasuk"=>$query->TahunMasuk,"jabatan"=>$query->IDJabatan,"nama_ayah"=>$query->NamaAyah,"alamat_ayah"=>$query->AlamatAyah,"no_telp_ayah"=>$query->NoTelpAyah,"nama_ibu"=>$query->NamaIbu,"alamat_ibu"=>$query->AlamatIbu,"no_telp_ibu"=>$query->NoTelpIbu,"nama_suami"=>$query->NamaSuami,"alamat_suami"=>$query->AlamatSuami,"no_telp_suami"=>$query->NoTelpSuami,"nama_wali"=>$query->NamaWali,"alamat_wali"=>$query->AlamatWali,"no_telp_wali"=>$query->NoTelpWali,"usrname"=>$query->Usernm,"martial_stts"=>$query->MartialStatus,"statusUser"=>$query->Status,"bln_masuk"=>$query->BulanMasuk,"foto"=>$query->Foto,"namabank"=>$query->NamaBank1,"norekening"=>$query->NoRekening1,"tempat_lahir"=>$query->TempatLahir,"tanggal_lahir"=>$query->TglLahirID,"pendidikan_terakhir"=>$query->PendidikanTerakhir,"jumlah_anak"=>$query->JumlahAnak);
}
echo json_encode($return);