<?php
include_once "../config/connection.php";
$tahun = antiSQLInjection($_GET['tahun']);
$id_karyawan = antiSQLInjection($_GET['karyawan']);
if ($id_karyawan != "") $cond = "AND IDKaryawan='$id_karyawan' AND Status<2 ";
else $cond = " AND Status<2 ";

$idAssign = antiSQLInjection($_GET['id']);

$act = antiSQLInjection($_GET['act']);
switch ($act) {
    case "DataAssign":
        $DataAssign = array();
        $DataKaryawan = array();

        $query = $db->get_results("SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_assign WHERE DATE_FORMAT(DateCreated, '%Y') = '$tahun' $cond ORDER BY IDAssign ASC");
        if ($query) {
            $i = 1;
            foreach ($query as $data) {
                if ($data->IDKaryawan == "0" && $data->IDProyek > 0) {
                    $data->IDKaryawan = $db->get_var("SELECT SiteAdmin2 FROM tb_proyek WHERE IDProyek='" . $data->IDProyek . "'");
                    if (!$data->IDKaryawan) $data->IDKaryawan = '';
                }

                $karyawan = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $data->IDKaryawan . "'");
                if (!$karyawan) $karyawan = "UMUM";
                $proyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='" . $data->IDProyek . "'");

                array_push($DataAssign, array("IDAssign" => $data->IDAssign, "NoAssign" => $data->NoAssign, "No" => $i, "Tanggal" => $data->TanggalID, "Status" => $data->Status, "Karyawan" => $karyawan, "TotalItem" => $data->TotalItem, "Proyek" => $proyek->NamaProyek, "IDKaryawan" => $data->IDKaryawan));
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
        $return = array("DataAssign" => $DataAssign, "DataKaryawan" => $DataKaryawan);
        echo json_encode($return);
        break;
    case "DetailAssign":
        $DataMaster = array();
        $DataDetail = array();
        $cc = "";

        $query = $db->get_row("SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID, DATE_FORMAT(DateApproved, '%d/%m/%Y') AS DateApprovedID FROM tb_assign WHERE IDAssign='$idAssign' ORDER BY IDAssign ASC");
        if ($query) {
            $karyawan = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $query->IDKaryawan . "'");
            if (!$karyawan) $karyawan = "UMUM";
            $proyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='" . $query->IDProyek . "'");
            if ($proyek != "") $proyekName = $proyek->KodeProyek . " / " . $proyek->Tahun . " / " . $proyek->NamaProyek;
            else $proyekName = "-";

            $ccTo = $query->CCTo;
            if ($ccTo != "") {
                $exp = explode(", ", $ccTo);
                foreach ($exp as $dK) {
                    $dKaryawan = $db->get_row("SELECT * FROM tb_karyawan WHERE IDKaryawan='$dK'");
                    if ($dKaryawan) {
                        $cc .= $dKaryawan->Nama . ", ";
                    }
                }
                if ($cc != "") $cc = substr($cc, 0, -2);
            }


            array_push($DataMaster, array("IDAssign" => $query->IDAssign, "NoAssign" => $query->NoAssign, "No" => $i, "Tanggal" => $query->TanggalID, "DateApproved" => $query->DateApprovedID, "Status" => $query->Status, "Karyawan" => $karyawan, "TotalItem" => $query->TotalItem, "Proyek" => $proyekName, "CCTo" => $cc));
        }

        $query = $db->get_results("SELECT * FROM tb_assign_detail WHERE IDAssign='$idAssign' ORDER BY IDAssignDetail ASC");
        if ($query) {
            $i = 1;
            foreach ($query as $data) {
                array_push($DataDetail, array("IDAssignDetail" => $data->IDAssignDetail, "KodeAsset" => $data->KodeAsset, "No" => $i, "Nama" => $data->Nama));
                $i++;
            }
        }

        $return = array("DataMaster" => $DataMaster, "DataDetail" => $DataDetail);
        echo json_encode($return);
        break;
}
