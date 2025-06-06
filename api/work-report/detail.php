<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_GET['id']);

$query = $db->get_row("SELECT a.*, b.Status as StatusWS, DATE_FORMAT(a.Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_work_report a, tb_work_schedule b WHERE a.IDWorkSchedule=b.IDWorkSchedule AND a.IDWorkReport='$id' ORDER BY a.IDWorkReport ASC");
if($query){
    $files = $db->get_results("SELECT * FROM tb_work_report_file WHERE IDWorkReport='".$query->IDWorkReport."'");

    $return = array("work_schedule"=>$query->IDWorkSchedule,"no_report"=>$query->NoWorkReport,"tanggal"=>$query->TanggalID,"keterangan"=>$query->Keterangan,"foto1"=>$query->Image1,"foto2"=>$query->Image2,"foto3"=>$query->Image3,"foto4"=>$query->Image4,"foto5"=>$query->Image5,"is_completed"=>$query->StatusWS,"files" =>$files);
}
echo json_encode($return);
