<?php
include_once "../config/connection.php";
$tahun = antiSQLInjection($_GET['tahun']);
$id_karyawan = antiSQLInjection($_GET['karyawan']);
if ($id_karyawan != "") $cond = "AND IDKaryawan='$id_karyawan'";
else $cond = "";

$idReturn = antiSQLInjection($_GET['id']);

$act = antiSQLInjection($_GET['act']);
switch ($act) {
    case "DataReturn":
        $DataReturn = array();
        $DataKaryawan = array();

        $query = $db->get_results("SELECT *,DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_return_asset WHERE DATE_FORMAT(Tanggal, '%Y') = '$tahun' $cond ORDER BY IDReturn ASC");
        if ($query) {
            $return = array();
            $i = 1;
            foreach ($query as $data) {
                $karyawan = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $data->IDKaryawan . "'");
                array_push($DataReturn, array("IDReturn" => $data->IDReturn, "NoReturn" => $data->NoReturn, "No" => $i, "Tanggal" => $data->TanggalID, "Karyawan" => $karyawan, "TotalItem" => $data->TotalItem));
                $i++;
            }
        }

        $query = $db->get_results("SELECT * FROM tb_karyawan WHERE IDKaryawan>1 ORDER BY Nama ASC");
        if ($query) {
            $i = 0;
            foreach ($query as $data) {
                $i++;
                if ($data->Status == "1") $status = "Aktif";
                else $status = "Non Aktif";
                $jabatan = $db->get_var("SELECT Jabatan FROM tb_jabatan WHERE IDJabatan='" . $data->IDJabatan . "'");
                array_push($DataKaryawan, array("No" => $i, "NIK" => $data->NIK, "Nama" => $data->Nama, "Status" => $data->StatusLainnya, "Jabatan" => $jabatan, "StatusK" => $status, "IDKaryawan" => $data->IDKaryawan, "CardNumber" => $data->CardNumber));
            }
        }

        $return = array("DataReturn" => $DataReturn, "DataKaryawan" => $DataKaryawan);
        echo json_encode($return);
        break;
    case "DetailReturn":
        $DataMaster = array();
        $DataDetail = array();

        $query = $db->get_row("SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_return_asset WHERE IDReturn='$idReturn' ORDER BY IDReturn ASC");
        if ($query) {
            $karyawan = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $query->IDKaryawan . "'");
            $dProyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='" . $query->IDProyek . "'");
            if ($dProyek) {
                $proyek = $dProyek->KodeProyek . "/" . $dProyek->Tahun . "/" . $dProyek->NamaProyek;
            } else {
                $proyek = '';
            }
            array_push($DataMaster, array("IDReturn" => $query->IDReturn, "NoReturn" => $query->NoReturn, "No" => $i, "Tanggal" => $query->TanggalID, "Karyawan" => $karyawan, "TotalItem" => $query->TotalItem, "Proyek" => $proyek));
        }

        $query = $db->get_results("SELECT * FROM tb_return_asset_detail WHERE IDReturn='$idReturn' ORDER BY IDReturnDetail ASC");
        if ($query) {
            $i = 1;
            foreach ($query as $data) {
                array_push($DataDetail, array("IDReturnDetail" => $data->IDReturnDetail, "KodeAsset" => $data->KodeAsset, "No" => $i, "Nama" => $data->Nama));
                $i++;
            }
        }

        $return = array("DataMaster" => $DataMaster, "DataDetail" => $DataDetail);
        echo json_encode($return);
        break;
}
