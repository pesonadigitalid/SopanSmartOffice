<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_GET['id']);

$query = $db->get_row("SELECT * FROM tb_supplier WHERE IDSupplier='$id' ORDER BY IDSupplier ASC");
if($query){
    $kategori2 = array();
    if($query->Kategori2!=""){
        $exp = explode(",", $query->Kategori2);
        foreach($exp as $data){
            $kategori2[$data] = true;
        }
    }
    $return = array("id_supplier"=>$query->IDSupplier,"kode_supplier"=>$query->KodeSupplier,"nama_perusahaan"=>$query->NamaPerusahaan,"IDSupplier"=>$query->IDSupplier,"alamat"=>$query->Alamat,"kota"=>$query->Kota,"provinsi"=>$query->Provinsi,"kode_pos"=>$query->KodePos,"no_telp"=>$query->NoTelp,"no_fax"=>$query->NoFax,"email"=>$query->Email,"website"=>$query->Website,"deskripsi"=>$query->Deskripsi,"kategori"=>$query->Kategori,"kategori2"=>$kategori2,"jenis"=>$query->Jenis,"status"=>$query->Status,"namakp1"=>$query->NamaKP1,"jabatankp1"=>$query->JabatanKP1,"emailkp1"=>$query->EmailKP1,"hpkp1"=>$query->HPKP1,"namakp2"=>$query->NamaKP2,"jabatankp2"=>$query->JabatanKP2,"emailkp2"=>$query->EmailKP2,"hpkp2"=>$query->HPKP2);
}
echo json_encode($return);