<?php
include_once "../config/connection.php";

include_once "../library/class.awss3.php";
$AwsS3 = new AwsS3();

// getHPPAvg(8);
$act = antiSQLInjection($_GET['act']);
switch ($act) {
    case "LoadAllRequirement":
        $po = array();

        $id = antiSQLInjection($_GET['id']);
        if ($id != "") $cond = " OR a.IDPO='$id'";

        $query = $db->get_results("SELECT a.*, b.TotalPajak FROM tb_po AS a LEFT JOIN (SELECT IDPO, SUM(Nilai)  AS  TotalPajak FROM tb_po_faktur_pajak GROUP BY IDPO) AS b ON a.IDPO=b.IDPO WHERE a.IsPajak='1' AND a.CompletedFakturPajak='0' AND a.PPN>0 AND (a.PPN>b.TotalPajak OR b.TotalPajak IS NULL $cond)");
        if ($query) {
            foreach ($query as $data) {
                $proyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='" . $data->IDProyek . "'");
                if ($proyek) $rProyek = $proyek->KodeProyek . " - " . $proyek->NamaProyek;
                else $rProyek = "UMUM";

                $supplier = $db->get_row("SELECT * FROM tb_supplier WHERE IDSupplier='" . $data->IDSupplier . "'");
                if ($supplier) $rSupplier = $supplier->NamaPerusahaan;
                else $rSupplier = "-";

                $ppnPajak = $db->get_var("SELECT SUM(Nilai) AS Total FROM tb_po_faktur_pajak WHERE NoPO='" . $data->NoPO . "' AND Status<>'2'");

                array_push($po, array("IDPO" => $data->IDPO, "NoPO" => $data->NoPO, "Proyek" => $rProyek, "Supplier" => $rSupplier, "Outstanding" => intval($data->PPN - $ppnPajak)));
            }
        }
        $return = array("po" => $po);
        echo json_encode($return);
        break;

    case "InsertNew":
        $tanggal = antiSQLInjection($_POST['tanggal']);
        $exp = explode("/", $tanggal);
        $tanggal = $exp[2] . "-" . $exp[1] . "-" . $exp[0];
        $tanggalCond = $exp[2] . "-" . $exp[1];
        $tanggalCond2 = "/LD/" . $exp[2] . "/" . $exp[1] . "/";

        $no_faktur = antiSQLInjection($_POST['no_faktur']);

        $id_po = antiSQLInjection($_POST['id_po']);
        $po = $db->get_row("SELECT * FROM tb_po WHERE IDPO='$id_po'");

        $keterangan = antiSQLInjection($_POST['keterangan']);
        $nilai = antiSQLInjection($_POST['nilai']);
        $uploaded = antiSQLInjection($_POST['uploaded']);

        if ($_FILES['file']) {
            $fileName = $AwsS3->uploadFileDirect("pajak_file",  $_FILES['file']);
        } else {
            $fileName = "";
        }

        $query = "INSERT INTO tb_po_faktur_pajak SET NoFaktur='$no_faktur', NoPO='" . $po->NoPO . "', IDPO='$id_po', Tanggal='$tanggal', Keterangan='$keterangan', Nilai='$nilai', DateCreated=NOW(), CreatedBy='$uploaded'";

        if ($fileName != "")
            $query .= ", File='$fileName'";

        if ($db->query($query)) {
            echo json_encode(array("res" => 1, "mes" => "Data berhasil disimpan"));
        } else {
            echo json_encode(array("res" => 0, "mes" => "Data gagal disimpan"));
        }
        break;

    case "EditRecord":
        $idfaktur = antiSQLInjection($_POST['idfaktur']);
        $tanggal = antiSQLInjection($_POST['tanggal']);
        $exp = explode("/", $tanggal);
        $tanggal = $exp[2] . "-" . $exp[1] . "-" . $exp[0];
        $tanggalCond = $exp[2] . "-" . $exp[1];
        $tanggalCond2 = "/LD/" . $exp[2] . "/" . $exp[1] . "/";

        $no_faktur = antiSQLInjection($_POST['no_faktur']);

        $id_po = antiSQLInjection($_POST['id_po']);
        $po = $db->get_row("SELECT * FROM tb_po WHERE IDPO='$id_po'");

        $keterangan = antiSQLInjection($_POST['keterangan']);
        $nilai = antiSQLInjection($_POST['nilai']);
        $uploaded = antiSQLInjection($_POST['uploaded']);

        if ($_FILES['file']) {
            $data = $db->get_row("SELECT * FROM tb_po_faktur_pajak WHERE IDPOFakturPajak='$idfaktur'");
            if ($data) {
                if ($data->File != "")
                    $AwsS3->deleteFile("pajak_file/" . $data->File);
            }
            $fileName = $AwsS3->uploadFileDirect("pajak_file",  $_FILES['file']);
        } else {
            $fileName = "";
        }

        $query = "UPDATE tb_po_faktur_pajak SET NoFaktur='$no_faktur', NoPO='" . $po->NoPO . "', IDPO='$id_po', Tanggal='$tanggal', Keterangan='$keterangan', Nilai='$nilai', DateModified=NOW(), ModifiedBy='$uploaded'";

        if ($fileName != "")
            $query .= ", File='$fileName'";

        $query .= " WHERE IDPOFakturPajak='$idfaktur'";

        if ($db->query($query)) {
            echo json_encode(array("res" => 1, "mes" => "Data berhasil diperbaharui"));
        } else {
            echo json_encode(array("res" => 0, "mes" => "Data gagal diperbaharui"));
        }
        break;

    case "DisplayData":
        $datestart = antiSQLInjection($_GET['datestart']);
        $expstart = explode("/", $datestart);
        $datestartchange = $expstart[2] . "-" . $expstart[1] . "-" . $expstart[0];

        $dateend = antiSQLInjection($_GET['dateend']);
        $expend = explode("/", $dateend);
        $dateendchange = $expend[2] . "-" . $expend[1] . "-" . $expend[0];

        $supplier = antiSQLInjection($_GET['supplier']);
        $nopo = antiSQLInjection($_GET['nopo']);
        $status = antiSQLInjection($_GET['status']);

        if ($datestart != "" && $dateend != "") {
            $cond = "WHERE a.Tanggal BETWEEN '$datestartchange' AND '$dateendchange'";
        } else if ($datestart != "") {
            $cond = "WHERE a.Tanggal='$datestartchange'";
        } else {
            $cond = "WHERE DATE_FORMAT(a.Tanggal,'%Y-%m') = '" . date("Y-m") . "'";
        }

        if ($supplier != "")
            $cond .= " AND b.IDSupplier='$supplier'";

        if ($nopo != "")
            $cond .= " AND a.NoPO LIKE '%$nopo%'";

        if ($status != "")
            $cond .= " AND b.Status='$status'";

        $pajakpo = array();
        $query = $db->get_results("SELECT a.*, DATE_FORMAT(a.`Tanggal`, '%d/%m/%Y') AS TanggalID, b.IDPO, b.NoPO, c.NamaPerusahaan FROM tb_po_faktur_pajak a, tb_po b, tb_supplier c $cond AND a.`IDPO`=b.`IDPO` AND b.`IDSupplier`=c.`IDSupplier` ORDER BY NoFaktur DESC");
        if ($query) {
            $i = 0;
            foreach ($query as $data) {
                $i++;
                $dataSupplier = $db->get_var("SELECT NamaPerusahaan FROM tb_supplier WHERE IDSupplier = '" . $data->IDSupplier . "'");

                if ($data->Status == '0') {
                    $status = "New";
                } else if ($data->Status == '1') {
                    $status = "Approved";
                } else {
                    $status = "Rejected";
                }

                array_push($pajakpo, array(
                    "IDPOFakturPajak" => $data->IDPOFakturPajak,
                    "IDPO" => $data->IDPO,
                    "NoPO" => $data->NoPO,
                    "NoFaktur" => $data->NoFaktur,
                    "Tanggal" => $data->TanggalID,
                    "No" => $i,
                    "Supplier" => $data->NamaPerusahaan,
                    "Keterangan" => $data->Keterangan,
                    "Nilai" => $data->Nilai,
                    "File" => $data->File,
                    "Status" => $data->Status,
                    "StatusString" =>  $status,
                    "FromSupplier" => $data->FromSupplier
                ));
            }
        }

        /* LOAD SUPPLIER */
        $supplier = array();
        $query = $db->get_results("SELECT * FROM tb_supplier ORDER BY NamaPerusahaan ASC");
        if ($query) {
            $return = array();
            $i = 0;
            foreach ($query as $data) {
                array_push($supplier, array(
                    "IDSupplier" => $data->IDSupplier,
                    "NamaSupplier" => $data->NamaPerusahaan
                ));
            }
        }

        echo json_encode(array("pajakpo" => $pajakpo, "supplier" => $supplier));
        break;

    case "Delete":
        $idr = antiSQLInjection($_POST['idr']);

        $data = $db->get_row("SELECT * FROM tb_po_faktur_pajak WHERE IDPOFakturPajak='$idr'");
        if ($data) {
            if ($data->File != "")
                $AwsS3->deleteFile("pajak_file/" . $data->File);
        }

        $query = "DELETE FROM tb_po_faktur_pajak WHERE IDPOFakturPajak='$idr'";

        if ($db->query($query)) {
            echo json_encode(array("res" => 1, "mes" => "Data berhasil dihapus"));
        } else {
            echo json_encode(array("res" => 0, "mes" => "Data gagal dihapus"));
        }
        break;

    case "Detail":
        $id = antiSQLInjection($_GET['id']);
        $detail = array();

        $query = $db->get_row("SELECT a.*, DATE_FORMAT(a.Tanggal, '%d/%m/%Y')AS TanggalID, b.PPN FROM tb_po_faktur_pajak a, tb_po b WHERE a.IDPOFakturPajak='$id' AND a.IDPO=b.IDPO ORDER BY a.IDPOFakturPajak ASC");
        if ($query) {
            $ppnPajak = $db->get_var("SELECT SUM(Nilai) AS Total FROM tb_po_faktur_pajak WHERE NoPO='" . $query->NoPO . "' AND Status<>'2'");
            $detail = array("IDPOFakturPajak" => $query->IDPOFakturPajak, "IDPO" => $query->IDPO, "NoPO" => $query->NoPO, "NoFaktur" => $query->NoFaktur, "Tanggal" => $query->TanggalID, "Keterangan" => $query->Keterangan, "Nilai" => $query->Nilai, "File" => $query->File, "Outstanding" => intval($query->PPN - $ppnPajak));
        }
        echo json_encode(array("detail" => $detail));
        break;

    default:
        echo "";
}
