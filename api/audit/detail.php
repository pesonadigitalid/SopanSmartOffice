<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_GET['id']);

$query = $db->get_row("SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID, DATE_FORMAT(DeletedDate, '%d/%m/%Y %H:%i:%s') AS DeletedDateID FROM tb_audit WHERE NoAudit='$id' ORDER BY IDAudit ASC");
if ($query) {
    $idAudit = $query->IDAudit;

    $gudang = $db->get_row("SELECT * FROM tb_gudang WHERE IDGudang='" . $query->IDGudang . "'");
    $pelanggan = $db->get_var("SELECT NamaPelanggan FROM tb_pelanggan WHERE IDPelanggan='" . $spb->IDPelanggan . "'");

    $user = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $query->CreatedBy . "'");
    if ($query->Keterangan != "") $keterangan = $query->Keterangan;
    else $keterangan = "-";

    $deletedBy = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $query->DeletedBy . "'");

    $return = array("IDAudit" => $query->IDAudit, "NoAudit" => $query->NoAudit, "Tanggal" => $query->TanggalID, "CreatedBy" => $user, "TotalItem" => $query->TotalItem, "TotalHPP" => $query->GrandTotal, "Keterangan" => $keterangan, "Gudang" => $gudang->Nama, "IDGudang" => $query->IDGudang, "deleted_date" => $query->DeletedDateID, "deleted_by" => $deletedBy, "deleted_remark" => $query->DeletedRemark);
}

$detailCart = array();

// For Non SN
$query = $db->get_results("SELECT *, SUM(SPGudang) AS SP_GUDANG, SUM(SubTotal) AS SUB_TOTAL, (SUM(SubTotal)/SUM(SPGudang)) AS HPP FROM tb_audit_detail WHERE NoAudit='$id' AND (SN IS NULL OR SN='') GROUP BY IDBarang ORDER BY NoUrut ASC");
$i = 0;
if ($query) {
    foreach ($query as $data) {
        if ($data->NamaBarang) {
            $i++;
            array_push($detailCart, array("NamaBarang" => $data->NamaBarang, "StokGudang" => $data->StokGudang, "Harga" => $data->HPP, "SubTotal" => $data->SUB_TOTAL, "No" => $i, "SN" => $data->SN, "SPGudang" => $data->SP_GUDANG, "StokSI" => ($data->StokGudang - $data->SP_GUDANG)));
        }
    }
}

$query = $db->get_results("SELECT *, SPGudang AS SP_GUDANG, SubTotal AS SUB_TOTAL, Harga AS HPP FROM tb_audit_detail WHERE NoAudit='$id' AND SN IS NOT NULL ORDER BY NoUrut ASC");
if ($query) {
    foreach ($query as $data) {
        if ($data->NamaBarang) {
            $i++;
            array_push($detailCart, array("NamaBarang" => $data->NamaBarang, "StokGudang" => $data->StokGudang, "Harga" => $data->HPP, "SubTotal" => $data->SUB_TOTAL, "No" => $i, "SN" => $data->SN, "SPGudang" => $data->SP_GUDANG, "StokSI" => ($data->StokGudang - $data->SP_GUDANG)));
        }
    }
}

echo json_encode(array("detail" => $return, "detailcart" => $detailCart));
