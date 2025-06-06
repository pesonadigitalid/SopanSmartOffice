<?php
session_start();
include_once "../config/connection.php";
$query = $db->get_row("SELECT * FROM tb_karyawan WHERE IDKaryawan='" . $_SESSION["uid"] . "'");
//echo $_SESSION["uid"];

$filtertahun = antiSQLInjection($_GET['tahun']);
$status = antiSQLInjection($_GET['status']);
$nama_proyek = antiSQLInjection($_GET['nama_proyek']);

if ($_GET['id_proyek']) $cond = "AND a.IDProyek='" . $_GET['id_proyek'] . "'";
else if ($_GET['stts']) $cond = "AND a.Status='" . $_GET['stts'] . "'";

if ($filtertahun != "") $cond .= " AND a.Tahun='$filtertahun'";

if ($status != "all") $cond .= " AND a.Status='$status'";

if ($nama_proyek != "") $cond = "AND a.NamaProyek LIKE '%$nama_proyek%'";

//Hanya departement terkait yang bisa melihat detail proyek
if ($query->IDDepartement != "12" && $query->IDDepartement != "8" && $query->IDDepartement != "7" && $query->IDDepartement != "9" && $query->IDDepartement != "5" && $query->IDDepartement != "6" && $query->IDDepartement != "27" && $query->IDDepartement != "13" && $query->IDDepartement != "2" && $query->IDDepartement != "1" && $_SESSION["uid"] != "1") {
    $cond .= " AND (a.IDDepartement='" . $query->IDDepartement . "' OR a.IDDepartementPemilik='" . $query->IDDepartement . "')";
    //cek untuk supervisor, site manager dll.
    $cek = $db->get_row("SELECT * FROM tb_proyek WHERE ProjectManager='" . $_SESSION["uid"] . "' OR SiteManager='" . $_SESSION["uid"] . "' OR Supervisor='" . $_SESSION["uid"] . "'");
    if ($cek) {
        $cond = " AND (a.ProjectManager='" . $_SESSION["uid"] . "' OR a.SiteManager='" . $_SESSION["uid"] . "' OR a.Supervisor='" . $_SESSION["uid"] . "')";
    }
}

$query = $db->get_results("SELECT a.*, b.NamaPelanggan, c.NamaDepartement FROM tb_proyek a, tb_pelanggan b, tb_departement c WHERE a.IDClient=b.IDPelanggan AND a.IDDepartement=c.IDDepartement $cond ORDER BY a.Tahun DESC, a.KodeProyek ASC");
if ($query) {
    $return = array();
    $i = 0;
    foreach ($query as $data) {
        $i++;
        $departementPemilik = $db->get_var("SELECT NamaDepartement FROM tb_departement WHERE IDDepartement='" . $data->IDDepartementPemilik . "'");
        $client = $db->get_var("SELECT NamaPelanggan FROM tb_pelanggan WHERE IDPelanggan='" . $data->IDClient . "'");
        if ($data->Status == "0") $status = "Tender";
        else if ($data->Status == "1") $status = "Fail";
        else if ($data->Status == "2") $status = "Process";
        else $status = "Complete";

        $TotalRetensi = $db->get_var("SELECT COUNT(*) FROM tb_proyek_retensi_jatuh_tempo WHERE IDProyek='" . $data->IDProyek . "' AND Status>'0'");
        if (!$TotalRetensi) $TotalRetensi = 0;

        $TotalGambar = $db->get_var("SELECT COUNT(*) FROM tb_proyek_gambar WHERE IDProyek='" . $data->IDProyek . "' AND Status>'0'");
        if (!$TotalGambar) $TotalGambar = 0;

        $TotalSI = $db->get_var("SELECT COUNT(*) FROM tb_proyek_site_instruction WHERE IDProyek='" . $data->IDProyek . "' AND Status>'0'");
        if (!$TotalSI) $TotalSI = 0;

        $TotalRFI = $db->get_var("SELECT COUNT(*) FROM tb_proyek_rfi WHERE IDProyek='" . $data->IDProyek . "' AND Status>'0'");
        if (!$TotalRFI) $TotalRFI = 0;

        $TotalSP = $db->get_var("SELECT COUNT(*) FROM tb_proyek_sertifikat_pembayaran WHERE IDProyek='" . $data->IDProyek . "' AND Status>'0'");
        if (!$TotalSP) $TotalSP = 0;

        $TotalFH = $db->get_var("SELECT COUNT(*) FROM tb_proyek_file_handover WHERE IDProyek='" . $data->IDProyek . "' AND Status>'0'");
        if (!$TotalFH) $TotalFH = 0;

        $TotalEOT = $db->get_var("SELECT COUNT(*) FROM tb_proyek_eot WHERE IDProyek='" . $data->IDProyek . "' AND Status>'0'");
        if (!$TotalEOT) $TotalEOT = 0;

        $TotalAM = $db->get_var("SELECT COUNT(*) FROM tb_proyek_approval_material WHERE IDProyek='" . $data->IDProyek . "' AND Status>'0'");
        if (!$TotalAM) $TotalAM = 0;

        $TotalDokumenProyek = $db->get_var("SELECT COUNT(*) FROM tb_proyek_file WHERE IDProyek='" . $data->IDProyek . "' AND Status>'0'");
        if (!$TotalDokumenProyek) $TotalDokumenProyek = 0;

        $TotalFileInvoice = $db->get_var("SELECT COUNT(*) FROM tb_proyek_file_invoice WHERE IDProyek='" . $data->IDProyek . "' AND Status>'0'");
        if (!$TotalFileInvoice) $TotalFileInvoice = 0;

        $TotalFilePPH = $db->get_var("SELECT COUNT(*) FROM tb_proyek_file_pph WHERE IDProyek='" . $data->IDProyek . "' AND Status>'0'");
        if (!$TotalFilePPH) $TotalFilePPH = 0;

        $TotalFileFakturPajak = $db->get_var("SELECT COUNT(*) FROM tb_proyek_file_faktur_pajak WHERE IDProyek='" . $data->IDProyek . "' AND Status>'0'");
        if (!$TotalFileFakturPajak) $TotalFileFakturPajak = 0;

        $TotalFileLaporanProyek = $db->get_var("SELECT COUNT(*) FROM tb_proyek_file_laporan_proyek WHERE IDProyek='" . $data->IDProyek . "' AND Status>'0'");
        if (!$TotalFileLaporanProyek) $TotalFileLaporanProyek = 0;

        if ($data->LemburPerJam == "") $data->LemburPerJam = 0;

        array_push($return, array("IDProyek" => $data->IDProyek, "IDDepartement" => $data->IDDepartement, "Tahun" => $data->Tahun, "No" => $i, "NamaClient" => $client, "KodeProyek" => $data->KodeProyek, "NamaProyek" => $data->NamaProyek, "Status" => $status, "Departement" => $data->NamaDepartement, "DepartementPemilik" => $departementPemilik, "GrandTotal" => $data->GrandTotal, "LastSync" => $data->LastSync, "TotalRetensi" => $TotalRetensi, "TotalGambar" => $TotalGambar, "TotalSI" => $TotalSI, "TotalRFI" => $TotalRFI, "TotalSP" => $TotalSP, "TotalFH" => $TotalFH, "TotalEOT" => $TotalEOT, "TotalAM" => $TotalAM, "TotalDokumenProyek" => $TotalDokumenProyek, "TotalFileInvoice" => $TotalFileInvoice, "TotalFilePPH" => $TotalFilePPH, "TotalFileFakturPajak" => $TotalFileFakturPajak, "TotalFileLaporanProyek" => $TotalFileLaporanProyek, "LemburPerJam" => $data->LemburPerJam, "LemburPerJamTipe" => $data->LemburPerJamTipe));
    }
    echo json_encode($return);
} else {
    echo json_encode(array());
}
