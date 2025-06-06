<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_GET['id']);

$query = $db->get_row("SELECT *, DATE_FORMAT(JatuhTempoSamsat, '%d/%m/%Y') AS JatuhTempoSamsatID, DATE_FORMAT(TanggalBeli, '%d/%m/%Y') AS TanggalID FROM tb_asset WHERE IDAsset='$id' ORDER BY IDAsset ASC");
if($query){
    $namaCategory = $db->get_var("SELECT Nama FROM tb_asset_category WHERE IDAssetCategory='".$query->IDAssetCategory."' ORDER BY IDAssetCategory");
    $return = array("nama"=>$query->Nama,"foto1"=>$query->Foto1,"foto2"=>$query->Foto2,"foto3"=>$query->Foto3,"category"=>$query->IDAssetCategory,"kode_asset"=>$query->KodeAsset,"deskripsi"=>$query->Deskripsi,"karyawan"=>$query->IDKaryawan,"jns_kendaraan"=>$query->JenisKendaraan,"manufaktur"=>$query->Manufaktur,"thn_rakit"=>$query->TahunRakit,"no_stnk"=>$query->NoSTNK,"no_kendaraan"=>$query->NoKendaraan,"jatuh_tempo_samsat"=>$query->JatuhTempoSamsatID,"max_tangki"=>$query->MaxTangkiBBM,"km_kendaraan"=>$query->KMKendaraan,"jns_bbm"=>$query->JenisBBM,"jenis"=>$query->Jenis,"tanggal"=>$query->TanggalID,"harga"=>$query->HargaBeli,"unit"=>$query->Unit,"stts_asset"=>$query->Status,"file1"=>$query->File1,"file2"=>$query->File2,"file3"=>$query->File3,"file4"=>$query->File4,"file5"=>$query->File5);
}
echo json_encode($return);
