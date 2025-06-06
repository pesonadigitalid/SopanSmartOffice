<?php
include_once "../config/connection.php";

include_once "../library/class.awss3.php";
$AwsS3 = new AwsS3();

$id = antiSQLInjection($_POST['id']);
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
$isSerial = antiSQLInjection($_POST['isSerial']);
$isBarang = antiSQLInjection($_POST['isBarang']);
$parent = antiSQLInjection($_POST['parent']);
$libCode = antiSQLInjection($_POST['libCode']);

$hargaPublish = str_replace(",", "", antiSQLInjection($_POST['hargaPublish']));
$diskonPersen = str_replace(",", "", antiSQLInjection($_POST['diskonPersen']));
$isSellingProduct = antiSQLInjection($_POST['isSellingProduct']);

$IsBarangPPN = antiSQLInjection($_POST['IsBarangPPN']);
$PPNPersen = antiSQLInjection($_POST['PPNPersen']);
$DPP = str_replace(",", "", antiSQLInjection($_POST['DPP']));

$IsNotifiedService6 = antiSQLInjection($_POST['IsNotifiedService6']);
$IsNotifiedService12 = antiSQLInjection($_POST['IsNotifiedService12']);
$IsNotifiedService18 = antiSQLInjection($_POST['IsNotifiedService18']);

$barang_child = antiSQLInjection($_POST['barang_child']);

if ($isSerial == "" || $isSerial == "0") {
    $isSerial = 0;
    $libCode = "";
}

$cek2 = $db->get_row("SELECT * FROM tb_barang WHERE LibCode='$libCode' AND IDBarang<>'$id'");
if ($cek2) {
    echo "4";
} else {
    $sql = "UPDATE tb_barang SET KodeBarang='$kode_barang', Nama='$nama', Kategori='$kategori', IDJenis='$jenis', IDSupplier='$supplier', Harga='$harga', HargaJual='$hargajual', Margin='$margin', HargaJualGrosir='$hargajualgrosir', MarginGrosir='$margingrosir', IDParent='$parent', IDSatuan='$satuan', IsSerialize='$isSerial', IsBarang='$isBarang', LibCode='$libCode', HargaPublish='$hargaPublish', DiskonPersen='$diskonPersen', IsSellingProduct='$isSellingProduct', IsBarangPPN='$IsBarangPPN', PPNPersen='$PPNPersen', DPP='$DPP', IsNotifiedService6='$IsNotifiedService6', IsNotifiedService12='$IsNotifiedService12', IsNotifiedService18='$IsNotifiedService18'";

    if ($_FILES['foto1'] != "") {
        $cekImg = newQuery("get_var", "SELECT Foto1 FROM tb_barang WHERE IDBarang='$id'");
        if ($cekImg) {
            $AwsS3->deleteFile("barang/" . $cekImg);
        }

        $foto1 = $AwsS3->uploadFileDirect("barang",  $_FILES['foto1']);
        $sql .= ", Foto1='$foto1'";
    }

    if ($_FILES['foto2'] != "") {
        $cekImg = newQuery("get_var", "SELECT Foto2 FROM tb_barang WHERE IDBarang='$id'");
        if ($cekImg) {
            $AwsS3->deleteFile("barang/" . $cekImg);
        }

        $foto2 = $AwsS3->uploadFileDirect("barang",  $_FILES['foto2']);
        $sql .= ", Foto2='$foto2'";
    }

    if ($_FILES['foto3'] != "") {
        $cekImg = newQuery("get_var", "SELECT Foto3 FROM tb_barang WHERE IDBarang='$id'");
        if ($cekImg) {
            $AwsS3->deleteFile("barang/" . $cekImg);
        }

        $foto3 = $AwsS3->uploadFileDirect("barang",  $_FILES['foto3']);
        $sql .= ", Foto3='$foto3'";
    }

    $sql .= " WHERE IDBarang='$id'";

    $query = $db->query($sql);

    if ($barang_child != "") {
        $barang_child = json_decode($barang_child);
        if (count($barang_child) > 0) {
            foreach ($barang_child as $data) {
                $db->query("UPDATE tb_barang_child SET Qty='" . floatval($data->Qty) . "', HargaPublish='" . floatval($data->Harga) . "' WHERE IDBarangChildren='$data->IDBarangChildren'");
            }
            $query = true;
        }
    }

    if ($query) {
        echo "1";
    } else {
        echo "0";
    }
}
