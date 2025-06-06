<?php
include_once "../config/connection.php";

$tanggal = antiSQLInjection($_POST['tanggal']);
$exptgl = explode("/", $tanggal);
$tanggal = $exptgl[2] . "-" . $exptgl[1] . "-" . $exptgl[0];
$tanggalCond = $exptgl[2] . "-" . $exptgl[1];
$tanggalCond2 = $exptgl[2] . "/" . $exptgl[1] . "/";

$kategori = antiSQLInjection($_POST['kategori']);
$spb = antiSQLInjection($_POST['spb']);
$usrlogin = antiSQLInjection($_POST['usrlogin']);
$supplier = antiSQLInjection($_POST['supplier']);
$total = str_replace(",", "", antiSQLInjection($_POST['total']));
$diskon_persen = antiSQLInjection($_POST['diskon_persen']);
$diskon = str_replace(",", "", antiSQLInjection($_POST['diskon']));
$total2 = str_replace(",", "", antiSQLInjection($_POST['total2']));
$ppn_persen = antiSQLInjection($_POST['ppn_persen']);
$ppn = str_replace(",", "", antiSQLInjection($_POST['ppn']));
$total_dpp = str_replace(",", "", antiSQLInjection($_POST['total_dpp']));
$grand_total = str_replace(",", "", antiSQLInjection($_POST['grand_total']));
$pembayarandp = str_replace(",", "", antiSQLInjection($_POST['pembayarandp']));
$sisa = str_replace(",", "", antiSQLInjection($_POST['sisa']));
$keterangan = antiSQLInjection($_POST['keterangan']);
$uploaded = antiSQLInjection($_POST['uploaded']);
$cartArray = antiSQLInjection($_POST['cart']);
$cartArray = json_decode($cartArray);

$metode_pembayaran = antiSQLInjection($_POST['metode_pembayaran']);
$metode_pembayaran2 = antiSQLInjection($_POST['metode_pembayaran2']);
$nobg = antiSQLInjection($_POST['nobg']);
$jatuhtempobg = antiSQLInjection($_POST['jatuhtempobg']);
$kembali = antiSQLInjection($_POST['kembali']);

$inv_pembayaran = antiSQLInjection($_POST['inv_pembayaran']);
$inv_bank = antiSQLInjection($_POST['inv_bank']);
$inv_delivery = antiSQLInjection($_POST['inv_delivery']);
$inv_expedisi = antiSQLInjection($_POST['inv_expedisi']);
$inv_alamat_pengiriman = antiSQLInjection($_POST['inv_alamat_pengiriman']);

$jenis_po = antiSQLInjection($_POST['jenis_po']);
$isPajak = antiSQLInjection($_POST['isPajak']);
$isMMSMaterialBantu = antiSQLInjection($_POST['isMMSMaterialBantu']);

if ($isPajak == "1") {
    $sqlID = "SELECT * FROM tb_po WHERE DATE_FORMAT(Tanggal,'%Y-%m')='" . $tanggalCond . "' AND IsPajak='1' ORDER BY NoPO DESC";
    $prefix = "PO/SPN/P/";
} else {
    $sqlID = "SELECT * FROM tb_po WHERE DATE_FORMAT(Tanggal,'%Y-%m')='" . $tanggalCond . "' AND IsPajak='0' ORDER BY NoPO DESC";
    $prefix = "PO/SPN/";
}

if ($pembayarandp <= 0) {
    $metode_pembayaran = "";
    $metode_pembayaran2 = "";
}

//check grandtotal is still bellow the limit.
$grandTotalSPB = $db->get_var("SELECT GrandTotal FROM tb_penjualan WHERE IDPenjualan='$spb'");
$grandTotalPOSPB = $db->get_var("SELECT SUM(GrandTotal) FROM tb_po WHERE IDPenjualan='$spb' AND DeletedDate IS NULL");
if (!$grandTotalPOSPB) $grandTotalPOSPB = 0;
$limitBelanja = $grandTotalSPB - $grandTotalPOSPB;

if ($grand_total > $limitBelanja && $spb != '0') {
    echo "2";
} else {
    if ($diskon_persen == "") $diskon_persen = "0";
    if ($ppn_persen == "") $ppn_persen = "0";

    $dataLast = $db->get_row($sqlID);
    if ($dataLast) $last = intval(substr($dataLast->NoPO, -3));
    else $last = 0;
    do {
        $last++;
        if ($last < 100 and $last >= 10)
            $last = "0" . $last;
        else if ($last < 10)
            $last = "00" . $last;
        $no_po = $prefix . $tanggalCond2 . $last;
        $checkNoTransaksi = $db->get_row("SELECT * FROM tb_po WHERE NoPO='$no_po'");
    } while ($checkNoTransaksi);

    $kategori = "Stok Gudang";
    if ($spb > 0) $kategori = "Stok Purchasing";

    $query = $db->query("INSERT INTO tb_po SET NoPO='$no_po', IDPenjualan='$spb', Tanggal='$tanggal', Kategori='$kategori', IDSupplier='$supplier', Total='$total', DiskonPersen='$diskon_persen', Diskon='$diskon', Total2='$total2', PPNPersen='$ppn_persen', PPN='$ppn', DPP='$total_dpp', GrandTotal='$grand_total', PembayaranDP='$pembayarandp', Sisa='$sisa', Keterangan='$keterangan', MetodePembayaran1='$metode_pembayaran', MetodePembayaran2='$metode_pembayaran2', JatuhTempoBG='$jatuhtempobg', NoBG='$nobg', Kembali='$kembali', InvPembayaran='$inv_pembayaran', InvBank='$inv_bank', InvDelivery='$inv_delivery', InvExpedisi='$inv_expedisi', InvAlamatPengiriman='$inv_alamat_pengiriman', JenisPO='$jenis_po', IsPajak='$isPajak', IsMMSMaterialBantu='$isMMSMaterialBantu', CreatedBy='" . $_SESSION["uid"] . "'");
    if ($query) {
        echo "1";
        //$id = mysql_insert_id();
        foreach ($cartArray as $data) {
            if (isset($data)) {
                $harga = str_replace(",", "", $data->Harga);
                $harga_publish = str_replace(",", "", $data->HargaPublish);
                $sub_total = str_replace(",", "", $data->SubTotal);

                $db->query("INSERT INTO tb_po_detail SET NoPO='$no_po', NoUrut='" . $data->NoUrut . "', IDBarang='" . $data->IDBarang . "', NamaBarang='" . $data->NamaBarang . "', Qty='" . $data->QtyBarang . "', HargaPublish='" . $harga_publish . "', Diskon='" . $data->Diskon . "', Harga='" . $harga . "', Satuan='" . $data->Satuan . "', SubTotal='" . $sub_total . "', PPNPersen='" . $data->PPNPersen . "', DPP='" . $data->DPP . "'");

                $barang = $db->get_row("SELECT * FROM tb_barang WHERE IDBarang='" . $data->IDBarang . "'");

                // $db->query("UPDATE tb_barang SET HargaPublish='" .  $harga_publish . "', DiskonPersen='" . $data->Diskon . "', Harga='" . $harga . "', Margin='" . ($barang->HargaJual - $harga) . "', DPP='" . $data->DPP . "' WHERE IDBarang='" . $data->IDBarang . "'");
            }
        }
    } else {
        echo "0";
    }
}
