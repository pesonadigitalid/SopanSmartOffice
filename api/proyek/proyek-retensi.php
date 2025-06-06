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
        $JatuhTempo = antiSQLInjection($_POST['JatuhTempo']);
        $exptgl = explode("/", $JatuhTempo);
        $JatuhTempoEN = $exptgl[2] . "-" . $exptgl[1] . "-" . $exptgl[0];
        do {
            $HashCode = HASH_PREFIX . md5(date("YmdHis") . rand(1, 1000000000));
            $cek = $db->get_row("SELECT * FROM tb_proyek_retensi_jatuh_tempo WHERE HashCode='$HashCode'");
        } while ($cek);

        $Keterangan = antiSQLInjection($_POST['Keterangan']);

        if ($_FILES['file']) {
            $fileName = $AwsS3->uploadFileDirect("proyek_retensi",  $_FILES['file']);
        } else {
            $fileName = "";
        }

        $db->query("INSERT INTO tb_proyek_retensi_jatuh_tempo SET IDProyek='$IDProyek', JatuhTempo='$JatuhTempoEN', Keterangan='$Keterangan', FileRetensi='$fileName', CreatedBy='" . $_SESSION["uid"] . "', ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW(), HashCode='$HashCode'");

        $payload = array("response" => 1);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "Update":
        $IDProyek = antiSQLInjection($_POST['IDProyek']);
        $IDProyekRetensi = antiSQLInjection($_POST['IDProyekRetensi']);
        $JatuhTempo = antiSQLInjection($_POST['JatuhTempo']);
        $exptgl = explode("/", $JatuhTempo);
        $JatuhTempoEN = $exptgl[2] . "-" . $exptgl[1] . "-" . $exptgl[0];

        $Keterangan = antiSQLInjection($_POST['Keterangan']);

        if ($_FILES['file']) {
            // unlink the old one
            $data = $db->get_row("SELECT * FROM tb_proyek_retensi_jatuh_tempo WHERE IDProyekRetensi='$IDProyekRetensi'");
            if ($data) {
                if ($data->FileRetensi != "")
                    $AwsS3->deleteFile("proyek_retensi/" . $data->FileRetensi);
            }

            $fileName = $AwsS3->uploadFileDirect("proyek_retensi",  $_FILES['file']);
            $sql = ", FileRetensi='$fileName'";
        } else {
            $fileName = "";
            $sql = "";
        }

        $db->query("UPDATE tb_proyek_retensi_jatuh_tempo SET JatuhTempo='$JatuhTempoEN', Keterangan='$Keterangan', ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW() $sql WHERE IDProyek='$IDProyek' AND IDProyekRetensi='$IDProyekRetensi'");

        $payload = array("response" => 1);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "Detail":
        $IDProyek = antiSQLInjection($_GET['IDProyek']);
        $IDProyekRetensi = antiSQLInjection($_GET['IDProyekRetensi']);

        $dDetail = $db->get_row("SELECT *, DATE_FORMAT(JatuhTempo,'%d/%m/%Y') AS JatuhTempoID FROM tb_proyek_retensi_jatuh_tempo WHERE IDProyek='$IDProyek' AND IDProyekRetensi='$IDProyekRetensi'");
        $proyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='$IDProyek'");

        $payload = array("data" => $dDetail, "proyek" => $proyek);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "DisplayData":
        $IDProyek = antiSQLInjection($_GET['IDProyek']);

        $dDetail = $db->get_results("SELECT *, DATE_FORMAT(JatuhTempo,'%d/%m/%Y') AS JatuhTempoID FROM tb_proyek_retensi_jatuh_tempo WHERE IDProyek='$IDProyek' AND Status>'0'");
        $proyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='$IDProyek'");

        $payload = array("data" => $dDetail, "proyek" => $proyek);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "Delete":
        $IDProyek = antiSQLInjection($_POST['IDProyek']);
        $IDProyekRetensi = antiSQLInjection($_POST['IDProyekRetensi']);

        // unlink the old one
        $data = $db->get_row("SELECT * FROM tb_proyek_retensi_jatuh_tempo WHERE IDProyekRetensi='$IDProyekRetensi'");
        if ($data) {
            if ($data->FileRetensi != "")
                $AwsS3->deleteFile("proyek_retensi/" . $data->FileRetensi);
        }

        // $db->query("DELETE FROM tb_proyek_retensi_jatuh_tempo WHERE IDProyek='$IDProyek' AND IDProyekRetensi='$IDProyekRetensi'");
        $db->query("UPDATE tb_proyek_retensi_jatuh_tempo SET Status='0', DateModified=NOW(), ModifiedBy='" . $_SESSION["uid"] . "' WHERE IDProyek='$IDProyek' AND IDProyekRetensi='$IDProyekRetensi'");
        $payload = array("response" => 1);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    default:
        echo "";
}
