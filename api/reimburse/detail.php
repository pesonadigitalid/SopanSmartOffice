<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_GET['id']);

$query = $db->get_row("SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_reimburse WHERE IDReimburse='$id' ORDER BY IDReimburse ASC");
if($query){
    $karyawan = $db->get_var("SELECT NamaKaryawan FROM tb_karyawan WHERE IDKaryawan='".$query->Tanggal."'");
    $return = array("no_reimburse"=>$query->NoReimburse,"tanggal"=>$query->TanggalID,"karyawan"=>$karyawan,"category"=>$query->Kategori,"no_kendaraan"=>$query->NoKendaraan,"total_nilai"=>$query->TotalNilai,"jumlah_liter"=>$query->JumlahLiterBBM,"km_kendaraan"=>$query->KMKendaraan,"stts"=>$query->Status,"karyawan"=>$query->IDKaryawan,"proyek"=>$query->IDProyek,"metode_pem1"=>$query->MetodePembayaran,"metode_pem2"=>$query->SubPembayaran,"keterangan"=>$query->Keterangan);
}
echo json_encode($return);