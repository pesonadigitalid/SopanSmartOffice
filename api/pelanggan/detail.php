<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_GET['id']);

$query = $db->get_row("SELECT * FROM tb_pelanggan WHERE IDPelanggan='$id' ORDER BY IDPelanggan ASC");
if($query){
    $return = array("id_pelanggan"=>$query->IDPelanggan,"kode_pelanggan"=>$query->KodePelanggan,"nama"=>$query->NamaPelanggan,"IDPelanggan"=>$query->IDPelanggan,"alamat"=>$query->Alamat,"kota"=>$query->Kota,"provinsi"=>$query->Provinsi,"kode_pos"=>$query->KodePos,"no_telp"=>$query->NoTelp,"no_fax"=>$query->NoFax,"email"=>$query->Email,"email2"=>$query->Email2,"website"=>$query->Website,"kontak_person"=>$query->KontakPerson,"hp"=>$query->HP,"kategori"=>$query->Kategori,"jenis"=>$query->Jenis,"status"=>$query->Status,"namakp1"=>$query->NamaKP1,"jabatankp1"=>$query->JabatanKP1,"emailkp1"=>$query->EmailKP1,"hpkp1"=>$query->HPKP1,"namakp2"=>$query->NamaKP2,"jabatankp2"=>$query->JabatanKP2,"emailkp2"=>$query->EmailKP2,"hpkp2"=>$query->HPKP2,"no_npwp"=>$query->NoNPWP,"nama_npwp"=>$query->NamaNPWP,"alamat_npwp"=>$query->AlamatNPWP);
}
echo json_encode($return);