<?php
include_once "../config/connection.php";

include_once "../library/class.awss3.php";
$AwsS3 = new AwsS3();

$id = $_POST['id'];
$kode_asset = antiSQLInjection($_POST['kode_asset']);
$category = antiSQLInjection($_POST['category']);
$nama = antiSQLInjection($_POST['nama']);
$deskripsi = antiSQLInjection($_POST['deskripsi']);
$jns_kendaraan = antiSQLInjection($_POST['jns_kendaraan']);
$manufaktur = antiSQLInjection($_POST['manufaktur']);
$thn_rakit = antiSQLInjection($_POST['thn_rakit']);
$no_stnk = antiSQLInjection($_POST['no_stnk']);
$no_kendaraan = antiSQLInjection($_POST['no_kendaraan']);
$unit = antiSQLInjection($_POST['unit']);

$jatuh_tempo_samsat = antiSQLInjection($_POST['jatuh_tempo_samsat']);
$exp = explode("/", $jatuh_tempo_samsat);
$jatuh_tempo_samsat = $exp[2] . "-" . $exp[1] . "-" . $exp[0];

$max_tangki = antiSQLInjection($_POST['max_tangki']);
$km_kendaraan = antiSQLInjection($_POST['km_kendaraan']);
$jns_bbm = antiSQLInjection($_POST['jns_bbm']);
$jenis = antiSQLInjection($_POST['jenis']);

$tanggal = antiSQLInjection($_POST['tanggal']);
$exp = explode("/", $tanggal);
$tanggal = $exp[2] . "-" . $exp[1] . "-" . $exp[0];

$harga = antiSQLInjection($_POST['harga']);
$stts_asset = antiSQLInjection($_POST['stts_asset']);

$cek = $db->get_row("SELECT * FROM tb_asset WHERE KodeAsset='$kode_asset' AND Jenis!='Ijin-Usaha' AND IDAsset!='$id'");
if ($cek) {
    echo "2";
} else {
    $sql = "UPDATE tb_asset SET KodeAsset='$kode_asset', IDAssetCategory='$category', Nama='$nama', Deskripsi='$deskripsi', JenisKendaraan='$jns_kendaraan', Manufaktur='$manufaktur', TahunRakit='$thn_rakit', NoKendaraan='$no_kendaraan', NoSTNK='$no_stnk', JatuhTempoSamsat='$jatuh_tempo_samsat', MaxTangkiBBM='$max_tangki', KMKendaraan='$km_kendaraan', JenisBBM='$jns_bbm', Jenis='$jenis', TanggalBeli='$tanggal', HargaBeli='$harga', Unit='$unit', Status='$stts_asset', ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW()";

    if ($_FILES['foto1'] != "") {
        $cekImg = newQuery("get_var", "SELECT Foto1 FROM tb_asset WHERE IDAsset='$id'");
        if ($cekImg) {
            $AwsS3->deleteFile("asset_photo/" . $cekImg);
        }

        $foto1 = $AwsS3->uploadFileDirect("asset_photo",  $_FILES['foto1']);
        $sql .= ", Foto1='$foto1'";
    }

    if ($_FILES['foto2'] != "") {
        $cekImg = newQuery("get_var", "SELECT Foto2 FROM tb_asset WHERE IDAsset='$id'");
        if ($cekImg) {
            $AwsS3->deleteFile("asset_photo/" . $cekImg);
        }

        $foto2 = $AwsS3->uploadFileDirect("asset_photo",  $_FILES['foto2']);
        $sql .= ", Foto2='$foto2'";
    }

    if ($_FILES['foto3'] != "") {
        $cekImg = newQuery("get_var", "SELECT Foto3 FROM tb_asset WHERE IDAsset='$id'");
        if ($cekImg) {
            $AwsS3->deleteFile("asset_photo/" . $cekImg);
        }

        $foto3 = $AwsS3->uploadFileDirect("asset_photo",  $_FILES['foto3']);
        $sql .= ", Foto3='$foto3'";
    }

    if ($_FILES['file1'] != "") {
        $cekImg = newQuery("get_var", "SELECT File1 FROM tb_asset WHERE IDAsset='$id'");
        if ($cekImg) {
            $AwsS3->deleteFile("asset_file/" . $cekImg);
        }

        $file1 = $AwsS3->uploadFileDirect("asset_file",  $_FILES['file1']);
        $sql .= ", File1='$file1'";
    }

    if ($_FILES['file2'] != "") {
        $cekImg = newQuery("get_var", "SELECT File2 FROM tb_asset WHERE IDAsset='$id'");
        if ($cekImg) {
            $AwsS3->deleteFile("asset_file/" . $cekImg);
        }

        $file2 = $AwsS3->uploadFileDirect("asset_file",  $_FILES['file2']);
        $sql .= ", File2='$file2'";
    }

    if ($_FILES['file3'] != "") {
        $cekImg = newQuery("get_var", "SELECT File3 FROM tb_asset WHERE IDAsset='$id'");
        if ($cekImg) {
            $AwsS3->deleteFile("asset_file/" . $cekImg);
        }

        $file3 = $AwsS3->uploadFileDirect("asset_file",  $_FILES['file3']);
        $sql .= ", File3='$file3'";
    }

    if ($_FILES['file4'] != "") {
        $cekImg = newQuery("get_var", "SELECT File4 FROM tb_asset WHERE IDAsset='$id'");
        if ($cekImg) {
            $AwsS3->deleteFile("asset_file/" . $cekImg);
        }

        $file4 = $AwsS3->uploadFileDirect("asset_file",  $_FILES['file4']);
        $sql .= ", File4='$file4'";
    }

    if ($_FILES['file5'] != "") {
        $cekImg = newQuery("get_var", "SELECT File5 FROM tb_asset WHERE IDAsset='$id'");
        if ($cekImg) {
            $AwsS3->deleteFile("asset_file/" . $cekImg);
        }

        $file5 = $AwsS3->uploadFileDirect("asset_file",  $_FILES['file5']);
        $sql .= ", File5='$file5'";
    }

    $sql .= " WHERE IDAsset='$id'";

    $query = $db->query($sql);
    if ($query) {
        echo "1";
    } else {
        echo "0";
    }
}
