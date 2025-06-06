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
        $TipeRFI = antiSQLInjection($_POST['TipeRFI']);
        $Keterangan = antiSQLInjection($_POST['Keterangan']);
        do {
            $HashCode = HASH_PREFIX . md5(date("YmdHis") . rand(1, 1000000000));
            $cek = $db->get_row("SELECT * FROM tb_proyek_rfi WHERE HashCode='$HashCode'");
        } while ($cek);

        if ($_FILES['file']) {
            $fileName = $AwsS3->uploadFileDirect("proyek_rfi",  $_FILES['file']);
        } else {
            $fileName = "";
        }

        $db->query("INSERT INTO tb_proyek_rfi SET IDProyek='$IDProyek', Nama='$Nama', TipeRFI='$TipeRFI', Keterangan='$Keterangan', FileRFI='$fileName', CreatedBy='" . $_SESSION["uid"] . "', ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW(), HashCode='$HashCode'");

        $payload = array("response" => 1);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "Update":
        $IDProyek = antiSQLInjection($_POST['IDProyek']);
        $IDProyekRFI = antiSQLInjection($_POST['IDProyekRFI']);
        $Nama = antiSQLInjection($_POST['Nama']);
        $TipeRFI = antiSQLInjection($_POST['TipeRFI']);
        $Keterangan = antiSQLInjection($_POST['Keterangan']);

        if ($_FILES['file']) {
            // unlink the old one
            $data = $db->get_row("SELECT * FROM tb_proyek_rfi WHERE IDProyekRFI='$IDProyekRFI'");
            if ($data) {
                if ($data->FileRFI != "")
                    $AwsS3->deleteFile("proyek_rfi/" . $data->FileRFI);
            }

            $fileName = $AwsS3->uploadFileDirect("proyek_rfi",  $_FILES['file']);
            $sql = ", FileRFI='$fileName'";
        } else {
            $fileName = "";
            $sql = "";
        }

        $db->query("UPDATE tb_proyek_rfi SET Nama='$Nama', TipeRFI='$TipeRFI', Keterangan='$Keterangan', ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW() $sql WHERE IDProyek='$IDProyek' AND IDProyekRFI='$IDProyekRFI'");

        $payload = array("response" => 1);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "Detail":
        $IDProyek = antiSQLInjection($_GET['IDProyek']);
        $IDProyekRFI = antiSQLInjection($_GET['IDProyekRFI']);

        $dDetail = $db->get_row("SELECT * FROM tb_proyek_rfi WHERE IDProyek='$IDProyek' AND IDProyekRFI='$IDProyekRFI'");
        $proyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='$IDProyek'");

        $payload = array("data" => $dDetail, "proyek" => $proyek);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "DisplayData":
        $IDProyek = antiSQLInjection($_GET['IDProyek']);
        $Tipe = antiSQLInjection($_GET['Tipe']);

        if ($Tipe != "") {
            $cond = " AND TipeRFI='$Tipe'";
        }

        $dDetail = $db->get_results("SELECT * FROM tb_proyek_rfi WHERE IDProyek='$IDProyek' AND Status>'0' $cond");
        $proyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='$IDProyek'");


        //GRAB ALL TOTAL DATA
        $all = $db->get_var("SELECT COUNT(*) FROM tb_proyek_rfi WHERE IDProyek='$IDProyek'");
        if (!$all) $all = '';
        $new = $db->get_var("SELECT COUNT(*) FROM tb_proyek_rfi WHERE IDProyek='$IDProyek' AND TipeRFI='1'");
        if (!$new) $new = '';
        $approved = $db->get_var("SELECT COUNT(*) FROM tb_proyek_rfi WHERE IDProyek='$IDProyek' AND TipeRFI='2'");
        if (!$approved) $approved = '';

        $payload = array("data" => $dDetail, "proyek" => $proyek, "all" => $all, "new" => $new, "approved" => $approved);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "Delete":
        $IDProyek = antiSQLInjection($_POST['IDProyek']);
        $IDProyekRFI = antiSQLInjection($_POST['IDProyekRFI']);

        // unlink the old one
        $data = $db->get_row("SELECT * FROM tb_proyek_rfi WHERE IDProyekRFI='$IDProyekRFI'");
        if ($data) {
            if ($data->FileRFI != "")
                $AwsS3->deleteFile("proyek_rfi/" . $data->FileRFI);
        }

        // $db->query("DELETE FROM tb_proyek_rfi WHERE IDProyek='$IDProyek' AND IDProyekRFI='$IDProyekRFI'");
        $db->query("UPDATE tb_proyek_rfi SET Status='0', DateModified=NOW(), ModifiedBy='" . $_SESSION["uid"] . "' WHERE IDProyek='$IDProyek' AND IDProyekRFI='$IDProyekRFI'");
        $payload = array("response" => 1);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    default:
        echo "";
}
