<?php
session_start();
include_once "../config/connection.php";

include_once "../library/class.awss3.php";
$AwsS3 = new AwsS3();

$act = antiSQLInjection($_GET['act']);
switch ($act) {
    case "DataList":
        $datestart = antiSQLInjection($_GET['datestart']);
        $expstart = explode("/", $datestart);
        $datestartchange = $expstart[2] . "-" . $expstart[1] . "-" . $expstart[0];

        $dateend = antiSQLInjection($_GET['dateend']);
        $expend = explode("/", $dateend);
        $dateendchange = $expend[2] . "-" . $expend[1] . "-" . $expend[0];

        if ($datestart != "" && $dateend != "") {
            $cond = "WHERE TanggalMulai BETWEEN '$datestartchange' AND '$dateendchange'";
        } else if ($datestart != "") {
            $cond = "WHERE TanggalMulai='$datestartchange'";
        } else {
            $cond = "WHERE DATE_FORMAT(TanggalMulai,'%Y-%m') = '" . date("Y-m") . "'";
        }

        $query = $db->get_results("SELECT *, DATE_FORMAT(TanggalMulai, '%d/%m/%Y') AS TanggalMulaiID, DATE_FORMAT(TanggalSelesai, '%d/%m/%Y') AS TanggalSelesaiID FROM tb_training_record $cond ORDER BY IDTraining ASC");
        if ($query) {
            $i = 0;
            $return = array();
            foreach ($query as $data) {

                $CheckInX = explode("-", $data->TanggalMulai);
                $CheckOutX =  explode("-", $data->TanggalSelesai);
                $date1 =  mktime(0, 0, 0, $CheckInX[1], $CheckInX[2], $CheckInX[0]);
                $date2 =  mktime(0, 0, 0, $CheckOutX[1], $CheckOutX[2], $CheckOutX[0]);
                $durasi = ($date2 - $date1) / (3600 * 24) + 1;

                $i++;
                $karyawan = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $data->IDKaryawan . "' ORDER BY IDKaryawan");
                array_push($return, array("IDTraining" => $data->IDTraining, "Karyawan" => $karyawan, "NamaTraining" => $data->NamaTraining, "TanggalMulai" => $data->TanggalMulaiID, "TanggalSelesai" => $data->TanggalSelesaiID, "LokasiTraining" => $data->LokasiTraining, "Durasi" => $durasi, "FileSertifikat" => $data->FileSertifikat, "No" => $i));
            }
            echo json_encode($return);
        } else {
            echo json_encode(array());
        }
        break;

    case "LoadAllRequirement":
        $karyawanArray = array();

        $query = $db->get_results("SELECT * FROM tb_karyawan WHERE IDKaryawan>1 ORDER BY Nama ASC");
        if ($query) {
            $i = 0;
            foreach ($query as $data) {
                $i++;
                array_push($karyawanArray, array("IDKaryawan" => $data->IDKaryawan, "Nama" => $data->Nama, "No" => $i));
            }
        }
        $return = array("karyawanArray" => $karyawanArray);
        echo json_encode($return);
        break;

    case "AddRecord":
        $karyawan = antiSQLInjection($_POST['karyawan']);
        $nama_training = antiSQLInjection($_POST['nama_training']);
        $keterangan = antiSQLInjection($_POST['keterangan']);

        $tgl_mulai = antiSQLInjection($_POST['tgl_mulai']);
        $exp_mulai = explode("/", $tgl_mulai);
        $tgl_mulai = $exp_mulai[2] . "-" . $exp_mulai[1] . "-" . $exp_mulai[0];

        $tgl_selesai = antiSQLInjection($_POST['tgl_selesai']);
        $exp_selesai = explode("/", $tgl_selesai);
        $tgl_selesai = $exp_selesai[2] . "-" . $exp_selesai[1] . "-" . $exp_selesai[0];

        $lokasi_training = antiSQLInjection($_POST['lokasi_training']);

        if ($_FILES['file_sertifikat']) {
            $trainingName = $AwsS3->uploadFileDirect("training_record_file",  $_FILES['file_sertifikat']);
            $sqlCond .= ", FileSertifikat='$trainingName'";
        }

        $query = $db->query("INSERT INTO tb_training_record SET IDKaryawan='$karyawan', NamaTraining='$nama_training', Keterangan='$keterangan', TanggalMulai='$tgl_mulai', TanggalSelesai='$tgl_selesai', LokasiTraining='$lokasi_training' $sqlCond");
        if ($query) {
            echo "1";
        } else {
            echo "0";
        }
        break;

    case "Detail":
        $id = antiSQLInjection($_GET['id']);
        $query = $db->get_row("SELECT *, DATE_FORMAT(TanggalMulai, '%d/%m/%Y') AS TanggalMulaiID, DATE_FORMAT(TanggalSelesai, '%d/%m/%Y') AS TanggalSelesaiID FROM tb_training_record WHERE IDTraining='$id' ORDER BY IDTraining ASC");
        if ($query) {
            $return = array("karyawan" => $query->IDKaryawan, "nama_training" => $query->NamaTraining, "keterangan" => $query->Keterangan, "tgl_mulai" => $query->TanggalMulaiID, "tgl_selesai" => $query->TanggalSelesaiID, "lokasi_training" => $query->LokasiTraining, "file_sertifikat" => $query->FileSertifikat);
        }
        echo json_encode($return);
        break;

    case "EditRecord":
        $id = antiSQLInjection($_POST['id']);
        $karyawan = antiSQLInjection($_POST['karyawan']);
        $nama_training = antiSQLInjection($_POST['nama_training']);
        $keterangan = antiSQLInjection($_POST['keterangan']);

        $tgl_mulai = antiSQLInjection($_POST['tgl_mulai']);
        $exp_mulai = explode("/", $tgl_mulai);
        $tgl_mulai = $exp_mulai[2] . "-" . $exp_mulai[1] . "-" . $exp_mulai[0];

        $tgl_selesai = antiSQLInjection($_POST['tgl_selesai']);
        $exp_selesai = explode("/", $tgl_selesai);
        $tgl_selesai = $exp_selesai[2] . "-" . $exp_selesai[1] . "-" . $exp_selesai[0];

        $lokasi_training = antiSQLInjection($_POST['lokasi_training']);

        if ($_FILES['file_sertifikat']) {
            // unlink the old one
            $data = $db->get_row("SELECT * FROM tb_training_record WHERE IDTraining='$id'");
            if ($data) {
                if ($data->FileSertifikat != "")
                    $AwsS3->deleteFile("training_record_file/" . $data->FileSertifikat);
            }
            
            $trainingName = $AwsS3->uploadFileDirect("training_record_file",  $_FILES['file_sertifikat']);
            $sqlCond .= ", FileSertifikat='$trainingName'";
        }

        $query = $db->query("UPDATE tb_training_record SET IDKaryawan='$karyawan', NamaTraining='$nama_training', Keterangan='$keterangan', TanggalMulai='$tgl_mulai', TanggalSelesai='$tgl_selesai', LokasiTraining='$lokasi_training' $sqlCond WHERE IDTraining='$id'");
        if ($query) {
            echo "1";
        } else {
            echo "0";
        }
        break;

    case "DeleteRecord":
        $idr = antiSQLInjection($_POST['idr']);

        // unlink the old one
        $data = $db->get_row("SELECT * FROM tb_training_record WHERE IDTraining='$idr'");
        if ($data) {
            if ($data->FileSertifikat != "")
                $AwsS3->deleteFile("training_record_file/" . $data->FileSertifikat);
        }

        $query = $db->query("DELETE FROM tb_training_record WHERE IDTraining='$idr'");
        if ($query) {
            echo "1";
        } else {
            echo "0";
        }
        break;
    default:
        echo "";
}
