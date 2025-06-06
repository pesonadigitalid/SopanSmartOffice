<?php
include_once "../config/connection.php";
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
        $Tanggal = antiSQLInjection($_POST['Tanggal']);
        $exptgl = explode("/", $Tanggal);
        $TanggalEN = $exptgl[2] . "-" . $exptgl[1] . "-" . $exptgl[0];

        $NoNota = antiSQLInjection($_POST['NoNota']);
        $Keterangan = antiSQLInjection($_POST['Keterangan']);
        $Jumlah = str_replace(",", "", antiSQLInjection($_POST['Jumlah']));
        do {
            $HashCode = HASH_PREFIX . md5(date("YmdHis") . rand(1, 1000000000));
            $cek = $db->get_row("SELECT * FROM tb_proyek_petty_cash_cashflow WHERE HashCode='$HashCode'");
        } while ($cek);

        $db->query("INSERT INTO tb_proyek_petty_cash_cashflow SET IDProyek='$IDProyek', Tanggal='$TanggalEN', Keterangan='$Keterangan', NoNota='$NoNota', Jumlah='$Jumlah', CreatedBy='" . $_SESSION["uid"] . "', ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW(), HashCode='$HashCode'");

        $payload = array("response" => 1);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "Update":
        $IDProyek = antiSQLInjection($_POST['IDProyek']);
        $IDProyekPCCashFlow = antiSQLInjection($_POST['IDProyekPCCashFlow']);
        $Tanggal = antiSQLInjection($_POST['Tanggal']);
        $exptgl = explode("/", $Tanggal);
        $TanggalEN = $exptgl[2] . "-" . $exptgl[1] . "-" . $exptgl[0];

        $NoNota = antiSQLInjection($_POST['NoNota']);
        $Keterangan = antiSQLInjection($_POST['Keterangan']);
        $Jumlah = str_replace(",", "", antiSQLInjection($_POST['Jumlah']));

        $db->query("UPDATE tb_proyek_petty_cash_cashflow SET IDProyek='$IDProyek', Tanggal='$TanggalEN', Keterangan='$Keterangan', NoNota='$NoNota', Jumlah='$Jumlah', ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW() $sql WHERE IDProyek='$IDProyek' AND IDProyekPCCashFlow='$IDProyekPCCashFlow'");

        $payload = array("response" => 1);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "Detail":
        $IDProyek = antiSQLInjection($_GET['IDProyek']);
        $IDProyekPCCashFlow = antiSQLInjection($_GET['IDProyekPCCashFlow']);

        $dDetail = $db->get_row("SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_proyek_petty_cash_cashflow WHERE IDProyek='$IDProyek' AND IDProyekPCCashFlow='$IDProyekPCCashFlow'");
        $proyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='$IDProyek'");

        $payload = array("data" => $dDetail, "proyek" => $proyek);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "DisplayData":
        $IDProyek = antiSQLInjection($_GET['IDProyek']);

        $dDetail = $db->get_results("SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_proyek_petty_cash_cashflow WHERE IDProyek='$IDProyek'");
        $proyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='$IDProyek'");
        $periode = $db->get_results("SELECT * FROM tb_petty_cash_periode WHERE IDProyek='$IDProyek'");

        $payload = array("data" => $dDetail, "proyek" => $proyek, "periode" => $periode);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "Delete":
        $IDProyek = antiSQLInjection($_POST['IDProyek']);
        $IDProyekPCCashFlow = antiSQLInjection($_POST['IDProyekPCCashFlow']);

        $db->query("DELETE FROM tb_proyek_petty_cash_cashflow WHERE IDProyek='$IDProyek' AND IDProyekPCCashFlow='$IDProyekPCCashFlow'");
        $payload = array("response" => 1);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    default:
        echo "";
}
