<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_GET['id']);

$query = $db->get_row("SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_work_schedule WHERE IDWorkSchedule='$id' ORDER BY IDWorkSchedule ASC");
if ($query) {
    $return = array("tipe" => $query->Tipe, "no_schedule" => $query->NoWorkSchedule, "spb" => $query->RefID, "pelanggan" => $query->IDPelanggan, "karyawan" => $query->IDKaryawan, "karyawan_ids" => $query->IDsKaryawan, "judul" => $query->Judul, "tanggal" => $query->TanggalID, "keterangan" => $query->Keterangan, "status" => $query->Status, "pic_pelanggan" => $query->PICPelanggan, "jenis_unit" => $query->JenisUnit, "no_tangki" => $query->NoTangki, "no_panel_a" => $query->NoPanelA, "no_panel_b" => $query->NoPanelB, "no_panel_c" => $query->NoPanelC, "no_tangki_heatpump" => $query->NoTangkiHeatpump, "no_outdoor_heatpump" => $query->NoOutdoorHeatpump);
}
echo json_encode($return);
