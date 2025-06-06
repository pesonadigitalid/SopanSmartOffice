<?php
session_start();
include_once "../config/connection.php";

$id_penjualan = antiSQLInjection($_POST['id_penjualan']);
$tanggal = antiSQLInjection($_POST['tanggal']);
$exp = explode("/", $tanggal);
$tanggal = $exp[2] . "-" . $exp[1] . "-" . $exp[0];
$tanggalCond = $exp[2] . "-" . $exp[1];
$tanggalCond3 = $exp[2];
$tanggalCond2 = $exp[2] . "/" . $exp[1] . "/";

$total_item = antiSQLInjection($_POST['total_item']);
$total = antiSQLInjection($_POST['total']);
$diskon_persen = antiSQLInjection($_POST['diskon_persen']);
$diskon = antiSQLInjection($_POST['diskon']);
$total2 = antiSQLInjection($_POST['total2']);
$ppn_persen = antiSQLInjection($_POST['ppn_persen']);
$ppn = antiSQLInjection($_POST['ppn']);
$grand_total = antiSQLInjection($_POST['grand_total']);
$keterangan = antiSQLInjection($_POST['keterangan']);

$totalHPP = antiSQLInjection($_POST['totalHPP']);
$totalHPPReal = antiSQLInjection($_POST['totalHPPReal']);
$totalMargin = antiSQLInjection($_POST['totalMargin']);

$cartArray = antiSQLInjection($_POST['cart']);
$cartArray = json_decode($cartArray);

$spb = $db->get_row("SELECT * FROM tb_penjualan WHERE IDPenjualan='$id_penjualan'");
$noPenjualan = $spb->NoPenjualan;

foreach ($cartArray as $data) {
    if (isset($data) && $data->QtyBarang < 0) {
        $penjualanDetail = $db->get_row("SELECT * FROM tb_penjualan_detail WHERE NoPenjualan='$noPenjualan' AND IDBarang='$data->IDBarang'");
        if ($penjualanDetail) {
            $penjualanDetailQty = $penjualanDetail->Qty;

            $suratJalanDetail = $db->get_row("SELECT a.* FROM tb_penjualan_surat_jalan_detail a, tb_penjualan_surat_jalan b WHERE a.NoSuratJalan=b.NoSuratJalan AND b.NoPenjualan='$noPenjualan' AND a.IDBarang='$data->IDBarang' AND b.DeletedDate IS NULL");
            if ($suratJalanDetail) $penjualanDetailQty -= $suratJalanDetail->Qty;

            if ($penjualanDetailQty < abs($data->QtyBarang)) {
                die(json_encode(array("res" => 0, "mes" => "Data tidak dapat disimpan. Sisa Qty $penjualanDetail->NamaBarang hanya " . number_format($penjualanDetailQty))));
            }
        }
    }
}

$dataLast = $db->get_row("SELECT * FROM tb_penjualan_vo WHERE DATE_FORMAT(Tanggal,'%Y')='" . $tanggalCond3 . "' AND CreatedBy='" . $_SESSION["uid"] . "' ORDER BY IDPenjualanVO DESC");
if ($dataLast) {
    $last = intval(substr($dataLast->NoVO, -4));
} else {
    $last = 0;
}

do {
    $last++;
    if ($last < 1000 and $last >= 100) {
        $last = "0" . $last;
    } else if ($last < 100 and $last >= 10) {
        $last = "00" . $last;
    } else if ($last < 10) {
        $last = "000" . $last;
    }

    $notransaksi = "VO/" . strtoupper($_SESSION["Usernm"]) . "/" . $tanggalCond2 . $last;
    $checkNoTransaksi = $db->get_row("SELECT * FROM tb_penjualan_vo WHERE NoVO='$notransaksi'");
} while ($checkNoTransaksi);

$lanjut = true;

if ($lanjut) {
    $spb = $db->get_row("SELECT * FROM tb_penjualan WHERE IDPenjualan='$id_penjualan'");
    $noPenjualan = $spb->NoPenjualan;
    $grandTotalSPB = $spb->GrandTotal;
    $grandTotalSPBAkhir = $grandTotalSPB + $grand_total;

    $query = $db->query("INSERT INTO tb_penjualan_vo SET NoVO='$notransaksi', IDPenjualan='$id_penjualan', Tanggal='$tanggal', TotalItem='$total_item', Total='$total', Diskon='$diskon', DiskonPersen='$diskon_persen', Total2='$total2', PPN='$ppn', PPNPersen='$ppn_persen', GrandTotal='$grand_total', GrandTotalSPB='$grandTotalSPB', GrandTotalSPBAkhir='$grandTotalSPBAkhir', Keterangan='$keterangan', CreatedBy='" . $_SESSION["uid"] . "', TotalHPP='$totalHPP', TotalHPPReal='$totalHPPReal', TotalMargin='$totalMargin'");

    if ($query) {
        $db->query("UPDATE tb_penjualan SET IsComplete='0' WHERE IDPenjualan='" . $id_penjualan . "'");
        echo json_encode(array("res" => 1, "mes" => "Data VO SPB berhasil disimpan!"));
        foreach ($cartArray as $data) {
            if (isset($data)) {
                $db->query("INSERT INTO tb_penjualan_vo_detail SET NoVO='$notransaksi', NoUrut='" . $data->NoUrut . "', IDBarang='" . $data->IDBarang . "', NamaBarang='" . $data->NamaBarang . "', NamaBarangDisplay='" . $data->NamaBarangDisplay . "', Qty='" . $data->QtyBarang . "', SN='" . $data->SNBarang . "', Harga='" . $data->Harga . "', SubTotal='" . $data->SubTotal . "', HargaBeli='" . $data->HPP . "', HargaBeliReal='" . $data->HPPReal . "', Margin='" . $data->Margin . "', IsParent='" . $data->isParent . "', IsChild='" . $data->isChild . "', Diskon='" . $data->Diskon . "', HargaDiskon='" . $data->HargaDiskon . "'");
                $db->query("UPDATE tb_barang SET HargaJual='" . $data->Harga . "' WHERE IDBarang='" . $data->IDBarang . "'");

                // Update QTY in Penjualan / SPB Detail
                $penjualanDetail = $db->get_row("SELECT * FROM tb_penjualan_detail WHERE NoPenjualan='$noPenjualan' AND IDBarang='$data->IDBarang'");
                if ($penjualanDetail) {
                    $db->query("UPDATE tb_penjualan_detail SET 
                        Qty='" . ($penjualanDetail->Qty + $data->QtyBarang) . "', 
                        SubTotal='" . ($penjualanDetail->SubTotal + $data->SubTotal) . "', 
                        Margin='" . ($penjualanDetail->Margin + $data->Margin) . "', 
                        QtyOriginal='$penjualanDetail->Qty', 
                        SubTotalOriginal='$penjualanDetail->SubTotal', 
                        MarginOriginal='$penjualanDetail->Margin' 
                    WHERE IDDetail='$penjualanDetail->IDDetail'");
                } else {
                    $db->query("INSERT INTO tb_penjualan_detail SET 
                        NoPenjualan='$noPenjualan', 
                        IDBarang='" . $data->IDBarang . "', 
                        NamaBarang='" . $data->NamaBarang . "', 
                        NamaBarangDisplay='" . $data->NamaBarangDisplay . "', 
                        Qty='" . $data->QtyBarang . "', 
                        SN='" . $data->SNBarang . "', 
                        Harga='" . $data->Harga . "', 
                        SubTotal='" . $data->SubTotal . "', 
                        HargaBeli='" . $data->HPP . "', 
                        HargaBeliReal='" . $data->HPPReal . "', 
                        Margin='" . $data->Margin . "', 
                        IsParent='" . $data->isParent . "', 
                        IsChild='" . $data->isChild . "',
                        QtyOriginal='0', 
                        SubTotalOriginal='0', 
                        MarginOriginal='0', 
                        Diskon='" . $data->Diskon . "', 
                        HargaDiskon='" . $data->HargaDiskon . "'");
                }
            }
        }

        $qPenjualanDetail = $db->get_results("SELECT * FROM tb_penjualan_detail WHERE NoPenjualan='$noPenjualan' ORDER BY IDDetail");
        if ($qPenjualanDetail) {
            $noUrut = 0;
            foreach ($qPenjualanDetail as $dPenjualanDetail) {
                $db->query("UPDATE tb_penjualan_detail SET NoUrut='$noUrut' WHERE IDDetail='$dPenjualanDetail->IDDetail'");
            }
        }

        // UPDATE TOTAL in SPB
        $dSPB = $db->get_row("SELECT * FROM tb_penjualan WHERE NoPenjualan='$noPenjualan'");
        if ($dSPB) {
            $DiskonPersen = $dSPB->DiskonPersen;
            $PPNPersen = $dSPB->PPNPersen;

            $TotalItem = $db->get_var("SELECT SUM(Qty) FROM tb_penjualan_detail WHERE NoPenjualan='$noPenjualan'");
            $Total = $db->get_var("SELECT SUM(SubTotal) FROM tb_penjualan_detail WHERE NoPenjualan='$noPenjualan'");
            $TotalHPP = $db->get_var("SELECT SUM(HargaBeli*Qty) FROM tb_penjualan_detail WHERE NoPenjualan='$noPenjualan'");
            $Diskon = $Total * $DiskonPersen / 100;

            $Total2 = $Total - $Diskon;
            $PPN = $Total2 * $PPNPersen / 100;

            $GrandTotal =  $Total2 + $PPN;
            $TotalMargin = $Total - $TotalHPP;

            $db->query("UPDATE tb_penjualan SET TotalItem='$TotalItem', Total='$Total', Diskon='$Diskon', Total2='$Total2', PPN='$PPN', GrandTotal='$GrandTotal', TotalHPP='$TotalHPP', TotalMargin='$TotalMargin' WHERE NoPenjualan='$noPenjualan'");
        }
    } else {
        echo json_encode(array("res" => 0, "mes" => "Data VO SPB gagal disimpan. Silahkan coba kembali nanti."));
    }
} else {
    echo json_encode(array("res" => 0, "mes" => $message));
}
