<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_GET['id']);

$query = $db->get_row("SELECT *,DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_surat_keluar WHERE IDSuratKeluar='$id' ORDER BY IDSuratKeluar ASC");
if($query){
    $return = array("nosurat"=>$query->NoSurat,"id_proyek"=>$query->IDProyek,"id_department"=>$query->IDDepartement,"jenis"=>$query->Jenis,"prihal"=>$query->Prihal,"tanggal"=>$query->TanggalID,"deskripsi"=>$query->Deskripsi,"file_surat_keluar"=>$query->FileSurat);
}
echo json_encode($return);