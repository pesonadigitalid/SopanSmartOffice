<?php
include_once "../config/connection.php";

$cond = "";
if ($_GET['kd'] != "") $cond .= "AND IDAsset='" . $_GET['kd'] . "'";
else if ($_GET['param'] == "assign") $cond .= "AND (IDKaryawan IS NULL OR IDKaryawan='0') AND (IDProyek IS NULL OR IDProyek='0') AND Status='1'";
else if ($_GET['param'] == "return") {
    if ($_GET['id'] > 0)
        $cond .= "AND IDKaryawan='" . $_GET['id'] . "'";
    else
        $cond .= "AND IDProyek='" . $_GET['proyek'] . "' AND IDKaryawan='0'";
}

if ($_GET['id_karyawan']) $cond .= "AND IDKaryawan='" . $_GET['id_karyawan'] . "'";

if ($_GET['kategori'] && $_GET['id_karyawan']) $cond .= " AND IDAssetCategory='" . $_GET['kategori'] . "'";
else if ($_GET['kategori']) $cond .= "AND IDAssetCategory='" . $_GET['kategori'] . "'";

if ($_GET['status'] != "") {
    $cond .= "AND Status='" . $_GET['status'] . "'";
}

$assetArray = array();
$karyawanArray = array();
$assetCatArray = array();
$proyekArray = array();

$query = $db->get_results("SELECT *, DATE_FORMAT(TanggalBeli,'%Y') AS Tahun FROM tb_asset WHERE Jenis!='Ijin-Usaha' $cond ORDER BY IDAsset ASC");
if ($query) {
    $i = 1;
    foreach ($query as $data) {
        $allow = true;

        if ($_GET['param'] == "return") {
            $idKaryawan = $_GET['id'];

            $isReceived = $db->get_row("SELECT a.* FROM tb_assign a, tb_assign_detail b WHERE a.IDAssign=b.IDAssign AND b.IDAsset='$data->IDAsset' AND a.IDKaryawan='$idKaryawan' AND a.IDProyek='$idProyek' ORDER BY a.IDAssign DESC LIMIT 0,1");
            if ($isReceived) {
                if (!$isReceived || $isReceived->Status == 0) $allow = false;
            }
        }

        if ($allow) {
            $karyawan = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $data->IDKaryawan . "'");
            $dProyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='" . $data->IDProyek . "'");
            if ($dProyek) {
                $proyek = $dProyek->KodeProyek . "/" . $dProyek->Tahun . "/" . $dProyek->NamaProyek;
            } else {
                $proyek = '';
            }
            $category = $db->get_var("SELECT Nama FROM tb_asset_category WHERE IDAssetCategory='" . $data->IDAssetCategory . "'");
            if ($data->Status == "1") $status = "Aktif";
            else $status = "Tidak Aktif";
            if ($data->Tahun != "0000") $tahun = $data->Tahun;
            else $tahun = "";

            if ($data->IDKaryawan != "") {
                $tanggal_assign = $db->get_var("SELECT DATE_FORMAT(a.Tanggal,'%d/%m/%Y') FROM tb_assign a, tb_assign_detail b WHERE a.IDAssign=b.IDAssign AND b.IDAsset='$data->IDAsset' AND a.IDKaryawan='$data->IDKaryawan'");
            } else {
                $tanggal_assign = "";
            }

            array_push($assetArray, array("IDAsset" => $data->IDAsset, "No" => $i, "KodeAsset" => $data->KodeAsset, "Category" => $category, "Nama" => $data->Nama, "IDKaryawan" => $data->IDKaryawan, "IDProyek" => $data->IDProyek, "Karyawan" => $karyawan, "Unit" => $data->Unit, "Status" => $status, "TahunBeli" => $tahun, "TanggalAssign" => $tanggal_assign, "Proyek" => $proyek));
            $i++;
        }
    }
}

$query = $db->get_results("SELECT * FROM tb_karyawan WHERE IDKaryawan>1 AND Status='1' ORDER BY Nama");
if ($query) {
    $i = 0;
    foreach ($query as $data) {
        $i++;
        if ($data->Status == "1") $status = "Aktif";
        else $status = "Non Aktif";
        $jabatan = $db->get_var("SELECT Jabatan FROM tb_jabatan WHERE IDJabatan='" . $data->IDJabatan . "'");
        array_push($karyawanArray, array("No" => $i, "NIK" => $data->NIK, "Nama" => $data->Nama, "Status" => $data->StatusLainnya, "Jabatan" => $jabatan, "StatusK" => $status, "IDKaryawan" => $data->IDKaryawan, "CardNumber" => $data->CardNumber));
    }
}

$query = $db->get_results("SELECT * FROM tb_proyek ORDER BY KodeProyek ASC");
if ($query) {
    $i = 0;
    foreach ($query as $data) {
        $i++;
        array_push($proyekArray, array("No" => $i, "IDProyek" => $data->IDProyek, "NamaProyek" => $data->NamaProyek, "KodeProyek" => $data->KodeProyek, "Tahun" => $data->Tahun));
    }
}

$query = $db->get_results("SELECT * FROM tb_asset_category WHERE Jenis='Asset' ORDER BY IDAssetCategory ASC");
if ($query) {
    $i = 1;
    foreach ($query as $data) {
        array_push($assetCatArray, array("IDAssetCategory" => $data->IDAssetCategory, "No" => $i, "Nama" => $data->Nama));
    }
}

$return = array("assetArray" => $assetArray, "karyawanArray" => $karyawanArray, "assetCatArray" => $assetCatArray, "proyekArray" => $proyekArray);
echo json_encode($return);
