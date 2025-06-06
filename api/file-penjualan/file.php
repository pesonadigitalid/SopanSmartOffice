<?php
include_once "../config/connection.php";

include_once "../library/class.awss3.php";
$AwsS3 = new AwsS3();

$act = antiSQLInjection($_GET['act']);
switch ($act) {
    case "LoadAllRequirement":
        $IDPenjualanFileCategory = antiSQLInjection($_GET['IDPenjualanFileCategory']);
        $IDPenjualan = antiSQLInjection($_GET['IDPenjualan']);

        $fileCategory = $db->get_row("SELECT * FROM tb_penjualan_file_category WHERE IDPenjualanFileCategory='$IDPenjualanFileCategory'");

        $penjualanName = "";
        if ($IDPenjualan > 0) {
            $pelanggan = $db->get_row("SELECT * FROM tb_penjualan WHERE IDPenjualan='$IDPenjualan'");
            if ($pelanggan) {
                $penjualanName = $pelanggan->NoPenjualan;
            }
        }

        $payload = array("fileCategory" => $fileCategory->Nama, "penjualanName" => $penjualanName);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "InsertNew":
        $IDPenjualan = antiSQLInjection($_POST['IDPenjualan']);
        $IDPenjualanFileCategory = antiSQLInjection($_POST['IDPenjualanFileCategory']);
        $Nama = antiSQLInjection($_POST['Nama']);
        $Keterangan = antiSQLInjection($_POST['Keterangan']);
        do {
            $HashCode = HASH_PREFIX . md5(date("YmdHis") . rand(1, 1000000000));
            $cek = $db->get_row("SELECT * FROM tb_penjualan_file WHERE HashCode='$HashCode'");
        } while ($cek);

        if ($_FILES['file']) {
            $file_name = $_FILES['file']['name'];
            $file_size = $_FILES['file']['size'];
            $file_tmp = $_FILES['file']['tmp_name'];
            $file_type = $_FILES['file']['type'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $fileName = $AwsS3->uploadFileDirect("mms_penjualan",  $_FILES['file']);
        } else {
            $fileName = "";
            $file_ext = "";
        }

        $db->query("INSERT INTO tb_penjualan_file SET IDPenjualanFileCategory='$IDPenjualanFileCategory', IDPenjualan='$IDPenjualan', Nama='$Nama', Keterangan='$Keterangan', `FileName`='$fileName', FileType='" . strtoupper($file_ext) . "', CreatedBy='" . $_SESSION["uid"] . "', HashCode='$HashCode'");

        $payload = array("response" => 1);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "Update":
        $IDPenjualanFileCategory = antiSQLInjection($_POST['IDPenjualanFileCategory']);
        $IDFile = antiSQLInjection($_POST['IDFile']);
        $Nama = antiSQLInjection($_POST['Nama']);
        $Keterangan = antiSQLInjection($_POST['Keterangan']);

        if ($_FILES['file']) {
            // unlink the old one
            $data = $db->get_row("SELECT * FROM tb_penjualan_file WHERE IDFile='$IDFile'");
            if ($data) {
                if ($data->FileName != "")
                    $AwsS3->deleteFile("mms_penjualan/" . $data->FileName);
            }

            $file_name = $_FILES['file']['name'];
            $file_size = $_FILES['file']['size'];
            $file_tmp = $_FILES['file']['tmp_name'];
            $file_type = $_FILES['file']['type'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $fileName = $AwsS3->uploadFileDirect("mms_penjualan",  $_FILES['file']);

            $sql = ", `FileName`='$fileName', FileType='" . strtoupper($file_ext) . "'";
        } else {
            $fileName = "";
            $sql = "";
        }

        $db->query("UPDATE tb_penjualan_file SET IDPenjualanFileCategory='$IDPenjualanFileCategory', Nama='$Nama', Keterangan='$Keterangan', ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW() $sql WHERE IDFile='$IDFile'");

        $payload = array("response" => 1);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "Detail":
        $IDPenjualanFileCategory = antiSQLInjection($_GET['IDPenjualanFileCategory']);
        $IDFile = antiSQLInjection($_GET['IDFile']);
        $IDPenjualan = antiSQLInjection($_GET['IDPenjualan']);

        $dataFile = $db->get_row("SELECT * FROM tb_penjualan_file WHERE IDPenjualanFileCategory='$IDPenjualanFileCategory' AND IDFile='$IDFile' AND IDPenjualan='$IDPenjualan'");

        $fileCategory = $db->get_row("SELECT * FROM tb_penjualan_file_category WHERE IDPenjualanFileCategory='$IDPenjualanFileCategory'");

        $penjualanName = "";
        if ($IDPenjualan > 0) {
            $pelanggan = $db->get_row("SELECT * FROM tb_penjualan WHERE IDPenjualan='$IDPenjualan'");
            if ($pelanggan) {
                $penjualanName = $pelanggan->NoPenjualan;
            }
        }

        $payload = array("data" => $dataFile, "fileCategory" => $fileCategory->Nama, "penjualanName" => $penjualanName);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "DisplayData":
        $IDPenjualanFileCategory = antiSQLInjection($_GET['IDPenjualanFileCategory']);
        $IDPenjualan = antiSQLInjection($_GET['IDPenjualan']);

        $result = array();
        $query = $db->get_results("SELECT * FROM tb_penjualan_file WHERE IDPenjualanFileCategory='$IDPenjualanFileCategory' AND IDPenjualan='$IDPenjualan' ORDER BY Nama");
        if ($query) {
            foreach ($query as $data) {

                array_push($result, array(
                    "ID" => $data->IDFile,
                    "IDPenjualanFileCategory" => $data->IDPenjualanFileCategory,
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

        $fileCategory = $db->get_row("SELECT * FROM tb_penjualan_file_category WHERE IDPenjualanFileCategory='$IDPenjualanFileCategory'");

        $penjualanName = "";
        if ($IDPenjualan > 0) {
            $pelanggan = $db->get_row("SELECT * FROM tb_penjualan WHERE IDPenjualan='$IDPenjualan'");
            if ($pelanggan) {
                $penjualanName = $pelanggan->NoPenjualan;
            }
        }

        $payload = array("data" => $result, "fileCategory" => $fileCategory->Nama, "penjualanName" => $penjualanName);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "Delete":
        $IDFile = antiSQLInjection($_POST['IDFile']);

        // unlink the old one
        $data = $db->get_row("SELECT * FROM tb_penjualan_file WHERE IDFile='$IDFile'");
        if ($data) {
            if ($data->FileName != "")
                $AwsS3->deleteFile("mms_penjualan/" . $data->FileName);
        }

        $db->query("DELETE FROM tb_penjualan_file WHERE IDFile='$IDFile'");
        $payload = array("response" => 1);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    default:
        echo "";
}
