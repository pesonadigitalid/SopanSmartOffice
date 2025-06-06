<?php
include_once "../config/connection.php";

$departement = array();
$departement_pemilik = array();
$kProyekManager = array();
$kSiteManager = array();
$kSupervisor = array();
$kSiteAdmin = array();
$pelanggan = array();
$upload = array();
$detail = array();
$vOrder = array();

//DEPARTEMENT
$query = $db->get_results("SELECT * FROM tb_departement WHERE AvailableOnProject='1' ORDER BY NamaDepartement ASC");
if ($query) {
    $return = array();
    $i = 0;
    foreach ($query as $data) {
        $i++;
        array_push($departement, array("IDDepartement" => $data->IDDepartement, "No" => $i, "NamaDepartement" => $data->NamaDepartement));
    }
}

//DEPARTEMENT
$query = $db->get_results("SELECT * FROM tb_departement WHERE IDDepartement='9' OR  IDDepartement='13' ORDER BY NamaDepartement ASC");
if ($query) {
    $return = array();
    $i = 0;
    foreach ($query as $data) {
        $i++;
        array_push($departement_pemilik, array("IDDepartement" => $data->IDDepartement, "No" => $i, "NamaDepartement" => $data->NamaDepartement));
    }
}

//PROJECT MANAGER
$query = $db->get_results("SELECT * FROM tb_karyawan WHERE IDJabatan='5' OR IDJabatan2='5' ORDER BY IDKaryawan ASC");
if ($query) {
    $return = array();
    $i = 0;
    foreach ($query as $data) {
        $i++;
        if ($data->Status != "1") $status = "(Resign)";
        else $status = "";
        $jabatan = $db->get_var("SELECT Jabatan FROM tb_jabatan WHERE IDJabatan='" . $data->IDJabatan . "'");
        array_push($kProyekManager, array("No" => $i, "NIK" => $data->NIK, "Nama" => $data->Nama . " " . $status, "Status" => $data->StatusLainnya, "Jabatan" => $jabatan, "StatusK" => $status, "IDKaryawan" => $data->IDKaryawan));
    }
}

//SITEMANAGER
$query = $db->get_results("SELECT * FROM tb_karyawan WHERE IDJabatan='6' OR IDJabatan2='6' ORDER BY IDKaryawan ASC");
if ($query) {
    $return = array();
    $i = 0;
    foreach ($query as $data) {
        $i++;
        if ($data->Status != "1") $status = "(Resign)";
        else $status = "";
        $jabatan = $db->get_var("SELECT Jabatan FROM tb_jabatan WHERE IDJabatan='" . $data->IDJabatan . "'");
        array_push($kSiteManager, array("No" => $i, "NIK" => $data->NIK, "Nama" => $data->Nama . " " . $status, "Status" => $data->StatusLainnya, "Jabatan" => $jabatan, "StatusK" => $status, "IDKaryawan" => $data->IDKaryawan));
    }
}

//SUPERVISOR
$query = $db->get_results("SELECT * FROM tb_karyawan WHERE IDJabatan='7' OR IDJabatan2='7' ORDER BY IDKaryawan ASC");
if ($query) {
    $return = array();
    $i = 0;
    foreach ($query as $data) {
        $i++;
        if ($data->Status != "1") $status = "(Resign)";
        else $status = "";
        $jabatan = $db->get_var("SELECT Jabatan FROM tb_jabatan WHERE IDJabatan='" . $data->IDJabatan . "'");
        array_push($kSupervisor, array("No" => $i, "NIK" => $data->NIK, "Nama" => $data->Nama . " " . $status, "Status" => $data->StatusLainnya, "Jabatan" => $jabatan, "StatusK" => $status, "IDKaryawan" => $data->IDKaryawan));
    }
}

//SITEADMIN
$query = $db->get_results("SELECT * FROM tb_karyawan WHERE IDKaryawan>0 AND IDKaryawan>1 ORDER BY IDKaryawan ASC");
if ($query) {
    $return = array();
    $i = 0;
    foreach ($query as $data) {
        $i++;
        if ($data->Status == "1") $status = "Aktif";
        else $status = "Non Aktif";
        $jabatan = $db->get_var("SELECT Jabatan FROM tb_jabatan WHERE IDJabatan='" . $data->IDJabatan . "'");
        array_push($kSiteAdmin, array("No" => $i, "NIK" => $data->NIK, "Nama" => $data->Nama, "Status" => $data->StatusLainnya, "Jabatan" => $jabatan, "StatusK" => $status, "IDKaryawan" => $data->IDKaryawan));
    }
}

//PELANGGAN
$query = $db->get_results("SELECT a.*, b.NamaDepartement FROM tb_pelanggan a, tb_departement b WHERE a.Kategori=b.IDDepartement ORDER BY NamaPelanggan ASC");
if ($query) {
    $return = array();
    $i = 0;
    foreach ($query as $data) {
        $i++;
        array_push($pelanggan, array("IDPelanggan" => $data->IDPelanggan, "No" => $i, "KodePelanggan" => $data->KodePelanggan, "Nama" => $data->NamaPelanggan, "Provinsi" => $data->Provinsi, "NoTelp" => $data->NoTelp, "Jenis" => $data->Jenis, "Departement" => $data->NamaDepartement));
    }
}

//UPLOAD
$id = $_GET['id'];
$query = $db->get_results("SELECT a.*, b.Nama, DATE_FORMAT(a.DateCreated,'%d/%m/%Y') AS DateCreatedID FROM tb_proyek_file a, tb_karyawan b WHERE a.`CreatedBy`=b.`IDKaryawan` AND a.IDProyek='$id'");
if ($query) {
    $return = array();
    foreach ($query as $data) {
        array_push($upload, array("IDProyekFile" => $data->IDProyekFile, "FileType" => $data->FileType, "Name" => $data->Name, "FileName" => $data->FileName, "Nama" => $data->Nama, "DateCreated" => $data->DateCreatedID));
    }
}

//DETAIL
$query = $db->get_row("SELECT *, DATE_FORMAT(DateStartProject,'%d/%m/%Y') AS DateStartProjectID, DATE_FORMAT(DateEndProject,'%d/%m/%Y') AS DateEndProjectID FROM tb_proyek WHERE IDProyek='$id' ORDER BY IDProyek ASC");
if ($query) {
    if ($query->DateEndProjectID != '0000/00/00') $tanggalend = $query->DateEndProjectID;
    else $tanggalend = '';
    if ($query->DateStartProjectID != '0000/00/00') $tanggalstart = $query->DateStartProjectID;
    else $tanggalstart = '';
    $detail = array("kode_proyek" => $query->KodeProyek, "no_kontrak" => $query->NoKontrak, "IDProyek" => $query->IDProyek, "nama_proyek" => $query->NamaProyek, "tahun" => $query->Tahun, "client" => $query->IDClient, "statusProyek" => $query->Status, "nominal" => $query->Nominal, "ppn_persen" => $query->PPNPersen, "ppn" => $query->PPN, "grand_total" => $query->GrandTotal, "grand_total_vo" => $query->GrandTotalVO, "limit_peng_persen" => $query->LimitPengeluaranPersen, "limit_pengeluaran" => $query->LimitPengeluaran, "limit_material" => $query->LimitPengeluaranMaterial, "limit_tenaga" => $query->LimitPengeluaranGaji, "limit_overhead" => $query->LimitPengeluaranOverHead, "project_manager" => $query->ProjectManager, "site_manager" => $query->SiteManager, "supervisor" => $query->Supervisor, "site_admin" => $query->SiteAdmin, "site_admin2" => $query->SiteAdmin2, "kategori" => $query->IDDepartement, "kategori2" => $query->IDDepartementPemilik, "locked" => $query->LockCoordinate, "tanggalmulai" => $tanggalstart, "tanggalselesai" => $tanggalend, "last_sync" => $query->LastSync);
}

$totalVO = 0;
$query = $db->get_results("SELECT *,DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_proyek_vo WHERE IDProyek='$id' ORDER BY IDVO");
if ($query) {
    $return = array();
    foreach ($query as $data) {
        $totalVO += $data->NilaiVO;
        array_push($vOrder, array("IDVO" => $data->IDVO, "IDProyek" => $data->IDProyek, "NoVO" => $data->NoVO, "No" => $i, "Tanggal" => $data->TanggalID, "Keterangan" => $data->Keterangan, "NilaiVO" => $data->NilaiVO, "NilaiAkhirProyek" => $data->NilaiAkhirProyek));
    }
}

$return = array("departement" => $departement, "departement_pemilik" => $departement_pemilik, "kProyekManager" => $kProyekManager, "kSiteManager" => $kSiteManager, "kSupervisor" => $kSupervisor, "kSiteAdmin" => $kSiteAdmin, "pelanggan" => $pelanggan, "upload" => $upload, "detail" => $detail, "variantOrder" => $vOrder, "totalVO" => $totalVO);
echo json_encode($return);
