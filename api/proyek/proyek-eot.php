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
            $cek = $db->get_row("SELECT * FROM tb_proyek_eot WHERE HashCode='$HashCode'");
        } while ($cek);

        if ($_FILES['file']) {
            $fileName = $AwsS3->uploadFileDirect("proyek_eot",  $_FILES['file']);
        } else {
            $fileName = "";
        }

        $db->query("INSERT INTO tb_proyek_eot SET IDProyek='$IDProyek', Nama='$Nama', Keterangan='$Keterangan', FileEOT='$fileName', CreatedBy='" . $_SESSION["uid"] . "', ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW(), HashCode='$HashCode'");

        $payload = array("response" => 1);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "Update":
        $IDProyek = antiSQLInjection($_POST['IDProyek']);
        $IDProyekEOT = antiSQLInjection($_POST['IDProyekEOT']);
        $Nama = antiSQLInjection($_POST['Nama']);
        $Keterangan = antiSQLInjection($_POST['Keterangan']);

        if ($_FILES['file']) {
            // unlink the old one
            $data = $db->get_row("SELECT * FROM tb_proyek_eot WHERE IDProyekEOT='$IDProyekEOT'");
            if ($data) {
                if ($data->FileEOT != "")
                    $AwsS3->deleteFile("proyek_eot/" . $data->FileEOT);
            }

            $fileName = $AwsS3->uploadFileDirect("proyek_eot",  $_FILES['file']);
            $sql = ", FileEOT='$fileName'";
        } else {
            $fileName = "";
            $sql = "";
        }

        $db->query("UPDATE tb_proyek_eot SET Nama='$Nama', Keterangan='$Keterangan', ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW() $sql WHERE IDProyek='$IDProyek' AND IDProyekEOT='$IDProyekEOT'");

        $payload = array("response" => 1);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "Detail":
        $IDProyek = antiSQLInjection($_GET['IDProyek']);
        $IDProyekEOT = antiSQLInjection($_GET['IDProyekEOT']);

        $dDetail = $db->get_row("SELECT * FROM tb_proyek_eot WHERE IDProyek='$IDProyek' AND IDProyekEOT='$IDProyekEOT'");
        $proyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='$IDProyek'");

        $payload = array("data" => $dDetail, "proyek" => $proyek);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "DisplayData":
        $IDProyek = antiSQLInjection($_GET['IDProyek']);

        $dDetail = $db->get_results("SELECT * FROM tb_proyek_eot WHERE IDProyek='$IDProyek' AND Status>'0'");
        $proyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='$IDProyek'");

        $payload = array("data" => $dDetail, "proyek" => $proyek);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "Delete":
        $IDProyek = antiSQLInjection($_POST['IDProyek']);
        $IDProyekEOT = antiSQLInjection($_POST['IDProyekEOT']);

        // unlink the old one
        $data = $db->get_row("SELECT * FROM tb_proyek_eot WHERE IDProyekEOT='$IDProyekEOT'");
        if ($data) {
            if ($data->FileEOT != "")
                $AwsS3->deleteFile("proyek_eot/" . $data->FileEOT);
        }

        // $db->query("DELETE FROM tb_proyek_eot WHERE IDProyek='$IDProyek' AND IDProyekEOT='$IDProyekEOT'");
        $db->query("UPDATE tb_proyek_eot SET Status='0', DateModified=NOW(), ModifiedBy='" . $_SESSION["uid"] . "' WHERE IDProyek='$IDProyek' AND IDProyekEOT='$IDProyekEOT'");
        $payload = array("response" => 1);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    default:
        echo "";
}
