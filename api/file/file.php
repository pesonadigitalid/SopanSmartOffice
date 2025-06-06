<?php
include_once "../config/connection.php";

include_once "../library/class.awss3.php";
$AwsS3 = new AwsS3();

$act = antiSQLInjection($_GET['act']);
switch ($act) {
    case "LoadAllRequirement":
        $IDFileCategory = antiSQLInjection($_GET['IDFileCategory']);
        $IDPelanggan = antiSQLInjection($_GET['IDPelanggan']);

        $fileCategory = $db->get_row("SELECT * FROM tb_pelanggan_file_category WHERE IDFileCategory='$IDFileCategory'");

        $pelangganName = "";
        if ($IDPelanggan > 0) {
            $pelanggan = $db->get_row("SELECT * FROM tb_pelanggan WHERE IDPelanggan='$IDPelanggan'");
            if ($pelanggan) {
                $pelangganName = $pelanggan->NamaPelanggan;
            }
        }

        $payload = array("fileCategory" => $fileCategory->Nama, "pelangganName" => $pelangganName);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "InsertNew":
        $IDPelanggan = antiSQLInjection($_POST['IDPelanggan']);
        $IDFileCategory = antiSQLInjection($_POST['IDFileCategory']);
        $Nama = antiSQLInjection($_POST['Nama']);
        $Keterangan = antiSQLInjection($_POST['Keterangan']);
        do {
            $HashCode = HASH_PREFIX . md5(date("YmdHis") . rand(1, 1000000000));
            $cek = $db->get_row("SELECT * FROM tb_pelanggan_file WHERE HashCode='$HashCode'");
        } while ($cek);

        if ($_FILES['file']) {
            $file_name = $_FILES['file']['name'];
            $file_size = $_FILES['file']['size'];
            $file_tmp = $_FILES['file']['tmp_name'];
            $file_type = $_FILES['file']['type'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $fileName = $AwsS3->uploadFileDirect("mms_pelanggan",  $_FILES['file']);
        } else {
            $fileName = "";
            $file_ext = "";
        }

        $db->query("INSERT INTO tb_pelanggan_file SET IDFileCategory='$IDFileCategory', IDPelanggan='$IDPelanggan', Nama='$Nama', Keterangan='$Keterangan', `FileName`='$fileName', FileType='" . strtoupper($file_ext) . "', CreatedBy='" . $_SESSION["uid"] . "', HashCode='$HashCode'");

        $payload = array("response" => 1);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "Update":
        $IDFileCategory = antiSQLInjection($_POST['IDFileCategory']);
        $IDFile = antiSQLInjection($_POST['IDFile']);
        $Nama = antiSQLInjection($_POST['Nama']);
        $Keterangan = antiSQLInjection($_POST['Keterangan']);

        if ($_FILES['file']) {
            // unlink the old one
            $data = $db->get_row("SELECT * FROM tb_pelanggan_file WHERE IDFile='$IDFile'");
            if ($data) {
                if ($data->FileName != "")
                    $AwsS3->deleteFile("mms_pelanggan/" . $data->FileName);
            }

            $file_name = $_FILES['file']['name'];
            $file_size = $_FILES['file']['size'];
            $file_tmp = $_FILES['file']['tmp_name'];
            $file_type = $_FILES['file']['type'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $fileName = $AwsS3->uploadFileDirect("mms_pelanggan",  $_FILES['file']);

            $sql = ", `FileName`='$fileName', FileType='" . strtoupper($file_ext) . "'";
        } else {
            $fileName = "";
            $sql = "";
        }

        $db->query("UPDATE tb_pelanggan_file SET IDFileCategory='$IDFileCategory', Nama='$Nama', Keterangan='$Keterangan', ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW() $sql WHERE IDFile='$IDFile'");

        $payload = array("response" => 1);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "Detail":
        $IDFileCategory = antiSQLInjection($_GET['IDFileCategory']);
        $IDFile = antiSQLInjection($_GET['IDFile']);
        $IDPelanggan = antiSQLInjection($_GET['IDPelanggan']);

        $dataFile = $db->get_row("SELECT * FROM tb_pelanggan_file WHERE IDFileCategory='$IDFileCategory' AND IDFile='$IDFile' AND IDPelanggan='$IDPelanggan'");

        $fileCategory = $db->get_row("SELECT * FROM tb_pelanggan_file_category WHERE IDFileCategory='$IDFileCategory'");

        $pelangganName = "";
        if ($IDPelanggan > 0) {
            $pelanggan = $db->get_row("SELECT * FROM tb_pelanggan WHERE IDPelanggan='$IDPelanggan'");
            if ($pelanggan) {
                $pelangganName = $pelanggan->NamaPelanggan;
            }
        }

        $payload = array("data" => $dataFile, "fileCategory" => $fileCategory->Nama, "pelangganName" => $pelangganName);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "DisplayData":
        $IDFileCategory = antiSQLInjection($_GET['IDFileCategory']);
        $IDPelanggan = antiSQLInjection($_GET['IDPelanggan']);

        $result = array();
        $query = $db->get_results("SELECT * FROM tb_pelanggan_file WHERE IDFileCategory='$IDFileCategory' AND IDPelanggan='$IDPelanggan' ORDER BY Nama");
        if ($query) {
            foreach ($query as $data) {

                array_push($result, array(
                    "ID" => $data->IDFile,
                    "IDFileCategory" => $data->IDFileCategory,
                    "Nama" => $data->Nama,
                    "Keterangan" => $data->Keterangan,
                    "Type" => $data->FileType,
                    "FileName" => $data->FileName,
                    "DateCreated" => $data->DateCreated,
                    "CreatedBy" => $data->CreatedBy,
                    "DateModified" => $data->DateModified,
                    "ModifiedBy" => $data->ModifiedBy
                ));
            }
        }

        $fileCategory = $db->get_row("SELECT * FROM tb_pelanggan_file_category WHERE IDFileCategory='$IDFileCategory'");

        $pelangganName = "";
        if ($IDPelanggan > 0) {
            $pelanggan = $db->get_row("SELECT * FROM tb_pelanggan WHERE IDPelanggan='$IDPelanggan'");
            if ($pelanggan) {
                $pelangganName = $pelanggan->NamaPelanggan;
            }
        }

        $payload = array("data" => $result, "fileCategory" => $fileCategory->Nama, "pelangganName" => $pelangganName);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "Delete":
        $IDFile = antiSQLInjection($_POST['IDFile']);

        // unlink the old one
        $data = $db->get_row("SELECT * FROM tb_pelanggan_file WHERE IDFile='$IDFile'");
        if ($data) {
            if ($data->FileName != "")
                $AwsS3->deleteFile("mms_pelanggan/" . $data->FileName);
        }

        $db->query("DELETE FROM tb_pelanggan_file WHERE IDFile='$IDFile'");
        $payload = array("response" => 1);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    default:
        echo "";
}
