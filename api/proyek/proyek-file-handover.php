<?php
include_once "../config/connection.php";

include_once "../library/class.awss3.php";
$AwsS3 = new AwsS3();

$act = antiSQLInjection($_GET['act']);
switch ($act) {
    case "LoadAllRequirement":
        $IDProyek = antiSQLInjection($_GET['IDProyek']);

        $dProyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='$IDProyek'");

        $payload = array("proyek" => $dProyek);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "InsertNew":
        $IDProyek = antiSQLInjection($_POST['IDProyek']);
        $Kategori = antiSQLInjection($_POST['Kategori']);
        $Keterangan = antiSQLInjection($_POST['Keterangan']);
        do {
            $HashCode = HASH_PREFIX . md5(date("YmdHis") . rand(1, 1000000000));
            $cek = $db->get_row("SELECT * FROM tb_proyek_file_handover WHERE HashCode='$HashCode'");
        } while ($cek);

        if ($_FILES['file']) {
            $fileName = $AwsS3->uploadFileDirect("proyek_handover",  $_FILES['file']);
        } else {
            $fileName = "";
        }

        $db->query("INSERT INTO tb_proyek_file_handover SET IDProyek='$IDProyek', Kategori='$Kategori', Keterangan='$Keterangan', FileHandover='$fileName', CreatedBy='" . $_SESSION["uid"] . "', ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW(), HashCode='$HashCode'");

        $payload = array("response" => 1);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "Update":
        $IDProyek = antiSQLInjection($_POST['IDProyek']);
        $IDProyekFileHandover = antiSQLInjection($_POST['IDProyekFileHandover']);
        $Kategori = antiSQLInjection($_POST['Kategori']);
        $Keterangan = antiSQLInjection($_POST['Keterangan']);

        if ($_FILES['file']) {
            // unlink the old one
            $data = $db->get_row("SELECT * FROM tb_proyek_file_handover WHERE IDProyekFileHandover='$IDProyekFileHandover'");
            if ($data) {
                if ($data->FileHandover != "")
                    $AwsS3->deleteFile("proyek_handover/" . $data->FileHandover);
            }

            $fileName = $AwsS3->uploadFileDirect("proyek_handover",  $_FILES['file']);
            $sql = ", FileHandover='$fileName'";
        } else {
            $fileName = "";
            $sql = "";
        }

        $db->query("UPDATE tb_proyek_file_handover SET Kategori='$Kategori', Keterangan='$Keterangan', ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW() $sql WHERE IDProyek='$IDProyek' AND IDProyekFileHandover='$IDProyekFileHandover'");

        $payload = array("response" => 1);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "Detail":
        $IDProyek = antiSQLInjection($_GET['IDProyek']);
        $IDProyekFileHandover = antiSQLInjection($_GET['IDProyekFileHandover']);

        $dProyekGambar = $db->get_row("SELECT * FROM tb_proyek_file_handover WHERE IDProyek='$IDProyek' AND IDProyekFileHandover='$IDProyekFileHandover'");
        $proyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='$IDProyek'");

        $payload = array("data" => $dProyekGambar, "proyek" => $proyek);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "DisplayData":
        $IDProyek = antiSQLInjection($_GET['IDProyek']);

        $dProyekGambar = $db->get_results("SELECT * FROM tb_proyek_file_handover WHERE IDProyek='$IDProyek' AND Status>'0'");
        $proyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='$IDProyek'");

        $payload = array("data" => $dProyekGambar, "proyek" => $proyek);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "Delete":
        $IDProyek = antiSQLInjection($_POST['IDProyek']);
        $IDProyekFileHandover = antiSQLInjection($_POST['IDProyekFileHandover']);

        // unlink the old one
        $data = $db->get_row("SELECT * FROM tb_proyek_file_handover WHERE IDProyekFileHandover='$IDProyekFileHandover'");
        if ($data) {
            if ($data->FileHandover != "")
                $AwsS3->deleteFile("proyek_handover/" . $data->FileHandover);
        }

        // $db->query("DELETE FROM tb_proyek_file_handover WHERE IDProyek='$IDProyek' AND IDProyekFileHandover='$IDProyekFileHandover'");
        $db->query("UPDATE tb_proyek_file_handover SET Status='0', DateModified=NOW(), ModifiedBy='" . $_SESSION["uid"] . "' WHERE IDProyek='$IDProyek' AND IDProyekFileHandover='$IDProyekFileHandover'");
        $payload = array("response" => 1);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    default:
        echo "";
}
