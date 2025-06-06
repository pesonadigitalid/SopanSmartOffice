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
            $cek = $db->get_row("SELECT * FROM tb_proyek_gambar WHERE HashCode='$HashCode'");
        } while ($cek);

        if ($_FILES['file']) {
            $fileName = $AwsS3->uploadFileDirect("gambar_proyek",  $_FILES['file']);
        } else {
            $fileName = "";
        }

        $db->query("INSERT INTO tb_proyek_gambar SET IDProyek='$IDProyek', Kategori='$Kategori', Keterangan='$Keterangan', FileGambar='$fileName', CreatedBy='" . $_SESSION["uid"] . "', ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW(), HashCode='$HashCode'");

        $payload = array("response" => 1);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "Update":
        $IDProyek = antiSQLInjection($_POST['IDProyek']);
        $IDProyekGambar = antiSQLInjection($_POST['IDProyekGambar']);
        $Kategori = antiSQLInjection($_POST['Kategori']);
        $Keterangan = antiSQLInjection($_POST['Keterangan']);

        if ($_FILES['file']) {
            // unlink the old one
            $data = $db->get_row("SELECT * FROM tb_proyek_gambar WHERE IDProyekGambar='$IDProyekGambar'");
            if ($data) {
                if ($data->FileGambar != "")
                    $AwsS3->deleteFile("gambar_proyek/" . $data->FileGambar);
            }

            $fileName = $AwsS3->uploadFileDirect("gambar_proyek",  $_FILES['file']);
            $sql = ", FileGambar='$fileName'";
        } else {
            $fileName = "";
            $sql = "";
        }

        $db->query("UPDATE tb_proyek_gambar SET Kategori='$Kategori', Keterangan='$Keterangan', ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW() $sql WHERE IDProyek='$IDProyek' AND IDProyekGambar='$IDProyekGambar'");

        $payload = array("response" => 1);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "Detail":
        $IDProyek = antiSQLInjection($_GET['IDProyek']);
        $IDProyekGambar = antiSQLInjection($_GET['IDProyekGambar']);

        $dProyekGambar = $db->get_row("SELECT * FROM tb_proyek_gambar WHERE IDProyek='$IDProyek' AND IDProyekGambar='$IDProyekGambar'");
        $proyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='$IDProyek'");

        $payload = array("data" => $dProyekGambar, "proyek" => $proyek);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "DisplayData":
        $IDProyek = antiSQLInjection($_GET['IDProyek']);

        $dProyekGambar = $db->get_results("SELECT * FROM tb_proyek_gambar WHERE IDProyek='$IDProyek' AND Status>'0'");
        $proyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='$IDProyek'");

        $payload = array("data" => $dProyekGambar, "proyek" => $proyek);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "Delete":
        $IDProyek = antiSQLInjection($_POST['IDProyek']);
        $IDProyekGambar = antiSQLInjection($_POST['IDProyekGambar']);

        // unlink the old one
        $data = $db->get_row("SELECT * FROM tb_proyek_gambar WHERE IDProyekGambar='$IDProyekGambar'");
        if ($data) {
            if ($data->FileGambar != "")
                $AwsS3->deleteFile("gambar_proyek/" . $data->FileGambar);
        }

        // $db->query("DELETE FROM tb_proyek_gambar WHERE IDProyek='$IDProyek' AND IDProyekGambar='$IDProyekGambar'");
        $db->query("UPDATE tb_proyek_gambar SET Status='0', DateModified=NOW(), ModifiedBy='" . $_SESSION["uid"] . "' WHERE IDProyek='$IDProyek' AND IDProyekGambar='$IDProyekGambar'");
        $payload = array("response" => 1);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    default:
        echo "";
}
