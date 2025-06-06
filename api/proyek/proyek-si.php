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
        $Nama = antiSQLInjection($_POST['Nama']);
        $Keterangan = antiSQLInjection($_POST['Keterangan']);
        do {
            $HashCode = HASH_PREFIX . md5(date("YmdHis") . rand(1, 1000000000));
            $cek = $db->get_row("SELECT * FROM tb_proyek_site_instruction WHERE HashCode='$HashCode'");
        } while ($cek);

        if ($_FILES['file']) {
            $fileName = $AwsS3->uploadFileDirect("proyek_si",  $_FILES['file']);
        } else {
            $fileName = "";
        }

        $db->query("INSERT INTO tb_proyek_site_instruction SET IDProyek='$IDProyek', Nama='$Nama', Keterangan='$Keterangan', FileSiteInstruction='$fileName', CreatedBy='" . $_SESSION["uid"] . "', ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW(), HashCode='$HashCode'");

        $payload = array("response" => 1);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "Update":
        $IDProyek = antiSQLInjection($_POST['IDProyek']);
        $IDProyekSiteInstruction = antiSQLInjection($_POST['IDProyekSiteInstruction']);
        $Nama = antiSQLInjection($_POST['Nama']);
        $Keterangan = antiSQLInjection($_POST['Keterangan']);

        if ($_FILES['file']) {
            // unlink the old one
            $data = $db->get_row("SELECT * FROM tb_proyek_site_instruction WHERE IDProyekSiteInstruction='$IDProyekSiteInstruction'");
            if ($data) {
                if ($data->FileSiteInstruction != "")
                    $AwsS3->deleteFile("proyek_si/" . $data->FileSiteInstruction);
            }

            $fileName = $AwsS3->uploadFileDirect("proyek_si",  $_FILES['file']);
            $sql = ", FileSiteInstruction='$fileName'";
        } else {
            $fileName = "";
            $sql = "";
        }

        $db->query("UPDATE tb_proyek_site_instruction SET Nama='$Nama', Keterangan='$Keterangan', ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW() $sql WHERE IDProyek='$IDProyek' AND IDProyekSiteInstruction='$IDProyekSiteInstruction'");

        $payload = array("response" => 1);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "Detail":
        $IDProyek = antiSQLInjection($_GET['IDProyek']);
        $IDProyekSiteInstruction = antiSQLInjection($_GET['IDProyekSiteInstruction']);

        $dDetail = $db->get_row("SELECT * FROM tb_proyek_site_instruction WHERE IDProyek='$IDProyek' AND IDProyekSiteInstruction='$IDProyekSiteInstruction'");
        $proyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='$IDProyek'");

        $payload = array("data" => $dDetail, "proyek" => $proyek);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "DisplayData":
        $IDProyek = antiSQLInjection($_GET['IDProyek']);

        $dDetail = $db->get_results("SELECT * FROM tb_proyek_site_instruction WHERE IDProyek='$IDProyek' AND Status>'0'");
        $proyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='$IDProyek'");

        $payload = array("data" => $dDetail, "proyek" => $proyek);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "Delete":
        $IDProyek = antiSQLInjection($_POST['IDProyek']);
        $IDProyekSiteInstruction = antiSQLInjection($_POST['IDProyekSiteInstruction']);

        // unlink the old one
        $data = $db->get_row("SELECT * FROM tb_proyek_site_instruction WHERE IDProyekSiteInstruction='$IDProyekSiteInstruction'");
        if ($data) {
            if ($data->FileSiteInstruction != "")
                $AwsS3->deleteFile("proyek_si/" . $data->FileSiteInstruction);
        }

        // $db->query("DELETE FROM tb_proyek_site_instruction WHERE IDProyek='$IDProyek' AND IDProyekSiteInstruction='$IDProyekSiteInstruction'");
        $db->query("UPDATE tb_proyek_site_instruction SET Status='0', DateModified=NOW(), ModifiedBy='" . $_SESSION["uid"] . "' WHERE IDProyek='$IDProyek' AND IDProyekSiteInstruction='$IDProyekSiteInstruction'");
        $payload = array("response" => 1);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    default:
        echo "";
}
