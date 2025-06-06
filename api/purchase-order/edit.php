<?php
include_once "../config/connection.php";

$no_po = antiSQLInjection($_POST['no_po']);
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

$completed = antiSQLInjection($_POST['completed']);
$completedFakturPajak = antiSQLInjection($_POST['completedFakturPajak']);

if ($pembayarandp <= 0) {
    $metode_pembayaran = "";
    $metode_pembayaran2 = "";
}

$dPO = $db->get_row("SELECT * FROM tb_po WHERE NoPO='$no_po'");
$gtotal = $dPO->GrandTotal;

//check grandtotal is still bellow the limit.
$grandTotalSPB = $db->get_var("SELECT GrandTotal FROM tb_penjualan WHERE IDPenjualan='$spb'");
$grandTotalPOSPB = $db->get_var("SELECT SUM(GrandTotal) FROM tb_po WHERE IDPenjualan='$spb' AND NoPO<>'$no_po'");
if (!$grandTotalPOSPB) $grandTotalPOSPB = 0;
$limitBelanja = $grandTotalSPB - $grandTotalPOSPB;

$allow = true;
// Cek kalau ada barang yang dihapus dan telah diterima
$query = $db->get_results("SELECT * FROM tb_po_detail WHERE NoPO='$no_po'");
if ($query) {
    foreach ($query as $data) {
        $available = false;
        foreach ($cartArray as $dataCart) {
            if (isset($dataCart)) {
                if ($dataCart->IDBarang == $data->IDBarang) {
                    $available = true;
                    break;
                }
            }
        }

        if (!$available) {
            // cek kalau barang sudah diterima atau belum
            $cek = $db->get_row("SELECT a.* FROM tb_penerimaan_stok_detail a, tb_penerimaan_stok b WHERE a.NoPenerimaanBarang=b.NoPenerimaanBarang AND b.NoPO='$no_po' AND b.Status='1'");
            if ($cek) {
                $allow = false;
                $message = $data->NamaBarang . " tidak dapat dihapus karena telah diterima!";
                echo $message;
                break;
            }
        }
    }
}
// Cek kalau qty dibawah barang yg telah diterima
foreach ($cartArray as $dataCart) {
    if (isset($dataCart)) {
        $qty = $dataCart->QtyBarang;
        $totalQtyDiterima = $db->get_var("SELECT SUM(a.Qty) FROM tb_penerimaan_stok_detail a, tb_penerimaan_stok b WHERE a.NoPenerimaanBarang=b.NoPenerimaanBarang AND b.NoPO='$no_po' AND a.IDBarang='" . $dataCart->IDBarang . "' AND b.Status='1'");

        if ($totalQtyDiterima > $qty) {
            $allow = false;
            $message = $dataCart->NamaBarang . " tidak boleh lebih kecil dari " . $totalQtyDiterima;
            echo $message;
            break;
        }
    }
}

if ($grand_total > $limitBelanja && $spb != '0') {
    echo "2";
} else if ($allow) {
    if ($diskon_persen == "") {
        $diskon_persen = "0";
    }

    if ($ppn_persen == "") {
        $ppn_persen = "0";
    }

    if ($spb > 0) $kategori = "Stok Purchasing";
    if (($spb == "0" || $spb == "") && $kategori == "Stok Purchasing") $kategori == "Stok Gudang";

    $query = $db->query("UPDATE tb_po SET Tanggal='$tanggal', Kategori='$kategori', IDPenjualan='$spb', IDSupplier='$supplier', Total='$total', DiskonPersen='$diskon_persen', Diskon='$diskon', Total2='$total2', PPNPersen='$ppn_persen', PPN='$ppn', DPP='$total_dpp', GrandTotal='$grand_total', PembayaranDP='$pembayarandp', Sisa='$sisa', Keterangan='$keterangan', MetodePembayaran1='$metode_pembayaran', MetodePembayaran2='$metode_pembayaran2', JatuhTempoBG='$jatuhtempobg', NoBG='$nobg', Kembali='$kembali', InvPembayaran='$inv_pembayaran', InvBank='$inv_bank', InvDelivery='$inv_delivery', InvExpedisi='$inv_expedisi', InvAlamatPengiriman='$inv_alamat_pengiriman', JenisPO='$jenis_po', IsPajak='$isPajak', IsMMSMaterialBantu='$isMMSMaterialBantu', Completed='$completed', CompletedFakturPajak='$completedFakturPajak', ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW() WHERE NoPO='$no_po'");
    if ($query) {
        $db->query("DELETE FROM tb_po_detail WHERE NoPO='$no_po'");
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

                // Sesuaikan harga HPP Stok Barang & DO dan kalkulasi ulang profit DO
                $barangPaket = array();
                $TotalItemWithPrice = 0;
                $qPaket = $db->get_results("SELECT * FROM tb_barang_child WHERE IDParent='" . $data->IDBarang . "'");
                if ($qPaket) {
                    foreach ($qPaket as $dPaket) {
                        array_push($barangPaket, $dPaket->IDBarang);
                        if ($dPaket->HargaPublish > 0) {
                            $TotalItemWithPrice++;
                        }
                    }
                }
                $isPaket = count($barangPaket) > 0;
                $condPaket = ($isPaket) ? " AND a.IDBarang IN (" . implode(",", $barangPaket) . ") AND NamaBarang LIKE '%$data->NamaBarang%'" : " AND a.IDBarang='" . $data->IDBarang . "'";
                $qPenerimaan = $db->get_results("SELECT b.*, a.IDDetail, a.NamaBarang, a.IDBarang FROM tb_penerimaan_stok_detail a, tb_penerimaan_stok b WHERE a.NoPenerimaanBarang=b.NoPenerimaanBarang AND b.NoPO='$no_po' $condPaket");
                if ($qPenerimaan) {
                    foreach ($qPenerimaan as $dPenerimaan) {
                        if ($isPaket) {
                            $hpp = $db->get_var("SELECT HargaPublish FROM tb_barang_child WHERE IDParent='" . $data->IDBarang . "' AND IDBarang='" . $dPenerimaan->IDBarang . "'");
                            if (!$hpp) $hpp = 0;
                        } else {
                            $hpp = $harga_publish;
                        }

                        if ($hpp > 0) {
                            $hpp = $fungsi->getPriceAfterDistributedDiscount($data->Diskon, $hpp, $TotalItemWithPrice, $TotalItemWithPrice);
                            if ($diskon_persen > 0) {
                                $hpp = $fungsi->getPriceAfterDistributedDiscount($diskon_persen, $hpp, 0, 0);
                            }
                        }

                        $db->query("UPDATE tb_penerimaan_stok_detail SET HPP='" . $hpp . "', SubTotal=(Qty*HPP) WHERE IDDetail='" . $dPenerimaan->IDDetail . "'");

                        $totalHPP = $db->get_var("SELECT SUM(SubTotal) FROM tb_penerimaan_stok_detail WHERE NoPenerimaanBarang='" . $dPenerimaan->NoPenerimaanBarang . "'");
                        if (!$totalHPP) {
                            $totalHPP = 0;
                        }

                        $db->query("UPDATE tb_penerimaan_stok SET TotalHPP='$totalHPP' WHERE NoPenerimaanBarang='" . $dPenerimaan->NoPenerimaanBarang . "'");

                        // UPDATE HPP STOK
                        $stokFrom = ($spb == "" || $spb == "0") ? 0 : 1;
                        $tbName = ($stokFrom  == 0) ? "tb_stok_gudang" : "tb_stok_purchasing";
                        $tbName2 = ($stokFrom  == 0) ? "tb_stok_gudang_serial_number" : "tb_stok_purchasing_serial_number";
                        $addCondition = ($stokFrom == 0) ? "" : " AND IDPenjualan='$spb' ";

                        $qStok = $db->get_results("SELECT * FROM $tbName WHERE RefID='" . $dPenerimaan->IDPenerimaan . "' AND RefIDDetail='" . $dPenerimaan->IDDetail . "' AND Tipe='1' $addCondition");
                        if ($qStok) {
                            foreach ($qStok as $dStok) {
                                $db->query("UPDATE $tbName SET HPP='" . $hpp . "' WHERE IDStok='" . $dStok->IDStok . "'");

                                $db->query("UPDATE tb_penjualan_surat_jalan_detail SET HPP='" . $hpp . "', HPPReal='" . $hpp . "', Margin=(HargaDiskon-HPP), SubTotalHPP=(Qty*HPP), SubTotalMargin=(Qty*Margin) WHERE IDStok='" . $dStok->IDStok . "' AND StokFrom='" . $stokFrom . "'");
                            }
                        }
                    }
                }
            }
        }
    } else {
        echo "0";
    }
}
