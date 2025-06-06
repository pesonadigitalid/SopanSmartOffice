<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_GET['id']);

$master = $db->get_row("SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID, DATE_FORMAT(DeletedDate, '%d/%m/%Y %H:%i:%s') AS DeletedDateID FROM tb_penjualan_vo WHERE IDPenjualanVO='$id'");
if ($master) {

    $spb = $db->get_row("SELECT a.*, b.NamaPelanggan FROM tb_penjualan a, tb_pelanggan b WHERE a.IDPelanggan=b.IDPelanggan AND a.IDPenjualan='" . $master->IDPenjualan . "'");
    $dSPB = array("NoPenjualan" => $spb->NoPenjualan, "NamaPelanggan" => $spb->NamaPelanggan);

    $detail = array();
    $qDetail = $db->get_results("SELECT * FROM tb_penjualan_vo_detail WHERE NoVO='$master->NoVO'");
    if ($qDetail) {
        $i = 0;
        foreach ($qDetail as $dDetail) {
            $i++;
            array_push($detail, array("IDBarang" => $dDetail->IDBarang, "NamaBarang" => $dDetail->NamaBarang, "NamaBarangDisplay" => $dDetail->NamaBarangDisplay, "Harga" => $dDetail->Harga, "No" => $i, "NoUrut" => ($i - 1), "Qty" => $dDetail->Qty, "QtyBarang" => floatval($dDetail->Qty), "MaxQtyBarang" => floatval($dDetail->Qty), "SubTotal" => $dDetail->SubTotal, "SNBarang" => $dDetail->SN, "IsSerialize" => $dataBr->IsSerialize, "Limit" => $limit, "HPP" => $dDetail->HargaBeli, "HPPReal" => $dDetail->HargaBeliReal, "Margin" => $dDetail->Margin, "SubTotal" => $dDetail->SubTotal, "isParent" => intval($dDetail->IsParent), "Diskon" => $dDetail->Diskon, "HargaDiskon" => $dDetail->HargaDiskon));
        }
    }

    $deletedBy = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $master->DeletedBy . "'");
    $master->DeletedBy = $deletedBy;

    echo json_encode(array("master" => $master, "detail" => $detail, "spb" => $dSPB));
}
