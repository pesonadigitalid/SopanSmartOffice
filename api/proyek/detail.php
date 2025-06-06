<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_GET['id']);

$query = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='$id' ORDER BY IDProyek ASC");
if($query){
    $return = array("kode_proyek"=>$query->KodeProyek,"IDProyek"=>$query->IDProyek,"nama_proyek"=>$query->NamaProyek,"tahun"=>$query->Tahun,"client"=>$query->IDClient,"statusProyek"=>$query->Status,"nominal"=>$query->Nominal,"ppn_persen"=>$query->PPNPersen,"ppn"=>$query->PPN,"grand_total"=>$query->GrandTotal,"limit_peng_persen"=>$query->LimitPengeluaranPersen,"limit_pengeluaran"=>$query->LimitPengeluaran,"limit_material"=>$query->LimitPengeluaranMaterial,"limit_tenaga"=>$query->LimitPengeluaranGaji,"limit_overhead"=>$query->LimitPengeluaranOverHead,"project_manager"=>$query->ProjectManager,"site_manager"=>$query->SiteManager,"supervisor"=>$query->Supervisor,"kategori"=>$query->IDDepartement);
}
echo json_encode($return);