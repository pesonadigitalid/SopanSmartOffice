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
        $FileType = antiSQLInjection($_POST['FileType']);
        $IDProyek = antiSQLInjection($_POST['IDProyek']);
        $Name = antiSQLInjection($_POST['Name']);
        do {
            $HashCode = HASH_PREFIX . md5(date("YmdHis") . rand(1, 1000000000));
            $cek = $db->get_row("SELECT * FROM tb_proyek_file WHERE HashCode='$HashCode'");
        } while ($cek);

        if ($_FILES['file']) {
            $fileName = $AwsS3->uploadFileDirect("proyek_file",  $_FILES['file']);
        } else {
            $fileName = "";
        }

        $db->query("INSERT INTO tb_proyek_file SET IDProyek='$IDProyek', Name='$Name', HashCode='$HashCode', FileType='$FileType', `FileName`='$fileName', CreatedBy='" . $_SESSION["uid"] . "', DateCreated=NOW()");

        $payload = array("response" => 1);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "Update":
        $IDProyek = antiSQLInjection($_POST['IDProyek']);
        $IDProyekFile = antiSQLInjection($_POST['IDProyekFile']);
        $FileType = antiSQLInjection($_POST['FileType']);
        $Name = antiSQLInjection($_POST['Name']);

        if ($_FILES['file']) {
            // unlink the old one
            $data = $db->get_row("SELECT * FROM tb_proyek_file WHERE IDProyekFile='$IDProyekFile'");
            if ($data) {
                if ($data->FileName != "")
                    $AwsS3->deleteFile("proyek_file/" . $data->FileName);
            }

            $fileName = $AwsS3->uploadFileDirect("proyek_file",  $_FILES['file']);
            $sql = ", `FileName`='$fileName'";
        } else {
            $fileName = "";
            $sql = "";
        }

        $db->query("UPDATE tb_proyek_file SET Name='$Name', FileType='$FileType', ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW() $sql WHERE IDProyek='$IDProyek' AND IDProyekFile='$IDProyekFile'");

        $payload = array("response" => 1);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "Detail":
        $IDProyek = antiSQLInjection($_GET['IDProyek']);
        $IDProyekFile = antiSQLInjection($_GET['IDProyekFile']);

        $dProyekFile = $db->get_row("SELECT * FROM tb_proyek_file WHERE IDProyek='$IDProyek' AND IDProyekFile='$IDProyekFile'");
        $proyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='$IDProyek'");

        $payload = array("data" => $dProyekFile, "proyek" => $proyek);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "DisplayData":
        $IDProyek = antiSQLInjection($_GET['IDProyek']);

        $dDetail = $db->get_results("SELECT * FROM tb_proyek_file WHERE IDProyek='$IDProyek' AND Status>'0'");
        $proyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='$IDProyek'");

        $payload = array("data" => $dDetail, "proyek" => $proyek);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "Delete":
        $IDProyek = antiSQLInjection($_POST['IDProyek']);
        $IDProyekFile = antiSQLInjection($_POST['IDProyekFile']);

        // unlink the old one
        $data = $db->get_row("SELECT * FROM tb_proyek_file WHERE IDProyekFile='$IDProyekFile'");
        if ($data) {
            if ($data->FileName != "")
                $AwsS3->deleteFile("proyek_file/" . $data->FileName);
        }

        // $db->query("DELETE FROM tb_proyek_file WHERE IDProyek='$IDProyek' AND IDProyekFile='$IDProyekFile'");
        $db->query("UPDATE tb_proyek_file SET Status='0', DateModified=NOW(), ModifiedBy='" . $_SESSION["uid"] . "' WHERE IDProyek='$IDProyek' AND IDProyekFile='$IDProyekFile'");
        $payload = array("response" => 1);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    default:
        echo "";
}
