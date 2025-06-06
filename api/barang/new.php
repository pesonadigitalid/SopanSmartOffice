<?php
include_once "../config/connection.php";

include_once "../library/class.awss3.php";
$AwsS3 = new AwsS3();

$kode_barang = antiSQLInjection($_POST['kode_barang']);
$nama = antiSQLInjection(htmlentities($_POST['nama']));
$kategori = antiSQLInjection($_POST['kategori']);
$jenis = antiSQLInjection($_POST['jenis']);
$supplier = antiSQLInjection($_POST['supplier']);
$satuan = antiSQLInjection($_POST['satuan']);
$harga = str_replace(",", "", antiSQLInjection($_POST['harga']));
$hargajual = str_replace(",", "", antiSQLInjection($_POST['hargajual']));
$margin = str_replace(",", "", antiSQLInjection($_POST['margin']));
$hargajualgrosir = str_replace(",", "", antiSQLInjection($_POST['hargajualgrosir']));
$margingrosir = str_replace(",", "", antiSQLInjection($_POST['margingrosir']));
$parent = antiSQLInjection($_POST['parent']);
$isSerial = antiSQLInjection($_POST['isSerial']);
$isBarang = antiSQLInjection($_POST['isBarang']);
$libCode = antiSQLInjection($_POST["libCode"]);
$iduser = antiSQLInjection($_SESSION["uid"]);

$hargaPublish = str_replace(",", "", antiSQLInjection($_POST['hargaPublish']));
$diskonPersen = str_replace(",", "", antiSQLInjection($_POST['diskonPersen']));
$isSellingProduct = antiSQLInjection($_POST['isSellingProduct']);

$IsBarangPPN = antiSQLInjection($_POST['IsBarangPPN']);
$PPNPersen = antiSQLInjection($_POST['PPNPersen']);
$DPP = str_replace(",", "", antiSQLInjection($_POST['DPP']));

$IsNotifiedService6 = antiSQLInjection($_POST['IsNotifiedService6']);
$IsNotifiedService12 = antiSQLInjection($_POST['IsNotifiedService12']);
$IsNotifiedService18 = antiSQLInjection($_POST['IsNotifiedService18']);

if ($isSerial == "" || $isSerial == "0") {
    $isSerial = 0;
    $libCode = "";
}

$dataLast = $db->get_row("SELECT * FROM tb_barang ORDER BY KodeBarang DESC");
if ($dataLast) {
    $last = intval(substr($dataLast->KodeBarang, -5));
    $last++;
    if ($last < 10000 and $last >= 1000)
        $kode_barang = "0" . $last;
    else if ($last < 1000 and $last >= 100)
        $kode_barang = "00" . $last;
    else if ($last < 100 and $last >= 10)
        $kode_barang = "000" . $last;
    else if ($last < 10)
        $kode_barang = "0000" . $last;
} else {
    $kode_barang = "00001";
}

$kode_barang = "M" . $kode_barang;

if ($_FILES['foto1']) {
    $foto1Name = $AwsS3->uploadFileDirect("barang",  $_FILES['foto1']);
} else {
    $foto1Name = "";
}

if ($_FILES['foto2']) {
    $foto2Name = $AwsS3->uploadFileDirect("barang",  $_FILES['foto2']);
} else {
    $foto2Name = "";
}

if ($_FILES['foto3']) {
    $foto3Name = $AwsS3->uploadFileDirect("barang",  $_FILES['foto3']);
} else {
    $foto3Name = "";
}

if (strlen($kode_barang) > 10) {
    echo "3";
} else {
    $cek = $db->get_row("SELECT * FROM tb_barang WHERE KodeBarang='$kode_barang'");
    $cek2 = $db->get_row("SELECT * FROM tb_barang WHERE LibCode='$libCode'");
    if ($cek) {
        echo "2";
    } else if ($cek2) {
        echo "4";
    } else {
        $query = $db->query("INSERT INTO tb_barang SET KodeBarang='$kode_barang', Nama='$nama', Kategori='$kategori',IDJenis='$jenis', IDSupplier='$supplier', Harga='$harga', HargaJual='$hargajual', Margin='$margin', HargaJualGrosir='$hargajualgrosir', MarginGrosir='$margingrosir', IDParent='$parent', IDSatuan='$satuan', CreatedBy='$iduser', IsSerialize='$isSerial', IsBarang='$isBarang', Foto1='$foto1Name', Foto2='$foto2Name', Foto3='$foto3Name', LibCode='$libCode', HargaPublish='$hargaPublish', DiskonPersen='$diskonPersen', IsSellingProduct='$isSellingProduct', IsBarangPPN='$IsBarangPPN', PPNPersen='$PPNPersen', DPP='$DPP', IsNotifiedService6='$IsNotifiedService6', IsNotifiedService12='$IsNotifiedService12', IsNotifiedService18='$IsNotifiedService18'");
        if ($query) {
            echo "1";
        } else {
            echo "INSERT INTO tb_barang SET KodeBarang='$kode_barang', Nama='$nama', Kategori='$kategori',IDJenis='$jenis', IDSupplier='$supplier', Harga='$harga', HargaJual='$hargajual', Margin='$margin', HargaJualGrosir='$hargajualgrosir', MarginGrosir='$margingrosir', IDParent='$parent', IDSatuan='$satuan', CreatedBy='$iduser', IsSerialize='$isSerial', IsBarang='$isBarang', Foto1='$foto1Name', Foto2='$foto2Name', Foto3='$foto3Name', LibCode='$libCode', HargaPublish='$hargaPublish', DiskonPersen='$diskonPersen', IsSellingProduct='$isSellingProduct', IsBarangPPN='$IsBarangPPN', PPNPersen='$PPNPersen', DPP='$DPP', IsNotifiedService6='$IsNotifiedService6', IsNotifiedService12='$IsNotifiedService12', IsNotifiedService18='$IsNotifiedService18'";
        }
    }
}
