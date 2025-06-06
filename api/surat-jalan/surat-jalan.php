<?php
include_once "../config/connection.php";
include_once "../library/class.stok.php";
include_once "../library/class.fungsi.php";

$stok = new Stok($db);
$fungsi = new Fungsi();

function getDetailBarang($db, $no_penjualan, $idGudang, $dataBarang, $no, $sisa, $harga, $isPaket = 0, $isChild = 0, $idParent = 0, $namaParent = "")
{
    $stokGudang = $db->get_row("SELECT * FROM tb_stok_gudang WHERE IDGudang='$idGudang' AND IDBarang='$dataBarang->IDBarang'");
    if ($stokGudang) {
        $sisaStokGudang = ($dataBarang->HPPMethod == "AVG")
            ? floatval($stokGudang->SisaStok)
            : $db->get_var("SELECT SUM(SisaStok) FROM tb_stok_gudang WHERE IDGudang='$idGudang' AND IDBarang='$dataBarang->IDBarang'");
        if (!$sisaStokGudang) $sisaStokGudang = 0;

        $hppStokGudang = ($dataBarang->HPPMethod == "AVG")
            ? floatval($stokGudang->HPP)
            : $db->get_var("SELECT (SUM((SisaStok*HPP))/SUM(SisaStok)) FROM tb_stok_gudang WHERE IDGudang='$idGudang' AND IDBarang='$dataBarang->IDBarang' AND SisaStok>0");
        if (!$hppStokGudang) $hppStokGudang = 0;
    } else {
        $sisaStokGudang = 0;
        $hppStokGudang = 0;
    }

    $stokPurchasing = $db->get_row("SELECT * FROM tb_stok_purchasing WHERE IDGudang='$idGudang' AND IDBarang='$dataBarang->IDBarang' AND IDPenjualan='$no_penjualan'");
    if ($stokPurchasing) {
        $sisaStokPurchasing = ($dataBarang->HPPMethod == "AVG")
            ? floatval($stokPurchasing->SisaStok)
            : $db->get_var("SELECT SUM(SisaStok) FROM tb_stok_purchasing WHERE IDGudang='$idGudang' AND IDBarang='$dataBarang->IDBarang' AND IDPenjualan='$no_penjualan'");
        if (!$sisaStokPurchasing) $sisaStokPurchasing = 0;

        $hppStokPurchasing = ($dataBarang->HPPMethod == "AVG")
            ? floatval($stokPurchasing->HPP)
            : $db->get_var("SELECT (SUM((SisaStok*HPP))/SUM(SisaStok)) FROM tb_stok_purchasing WHERE IDGudang='$idGudang' AND IDBarang='$dataBarang->IDBarang' AND IDPenjualan='$no_penjualan' AND SisaStok>0");
        if (!$hppStokPurchasing) $hppStokPurchasing = 0;
    } else {
        $sisaStokPurchasing = 0;
        $hppStokPurchasing = 0;
    }

    if ($sisaStokGudang > 0 || $sisaStokPurchasing > 0) {
        if ($sisaStokGudang > 0 &&  $sisaStokPurchasing > 0) $avg = ($hppStokGudang + $hppStokPurchasing) / 2;
        else if ($sisaStokGudang > 0) $avg = $hppStokGudang;
        else $avg = $hppStokPurchasing;

        $sn = array();

        if ($sisaStokPurchasing > 0) {
            $qsn = $db->get_results("SELECT a.*, b.HPP FROM tb_stok_purchasing_serial_number a, tb_stok_purchasing b WHERE a.IDStok=b.IDStok AND IDGudang='$idGudang' AND IDBarang='$dataBarang->IDBarang' AND IDPenjualan='$no_penjualan' AND a.Stok>0");
            if ($qsn) {
                foreach ($qsn as $dsn) {
                    $hpp =  ($dataBarang->HPPMethod == "AVG") ? $avg : $dsn->HPP;
                    array_push($sn, array("SN" => $dsn->SN, "Harga" => floatval($hpp)));
                }
            }
        }

        if ($sisaStokGudang > 0) {
            $qsn = $db->get_results("SELECT a.*, b.HPP FROM tb_stok_gudang_serial_number a, tb_stok_gudang b WHERE a.IDStok=b.IDStok AND b.IDGudang='$idGudang' AND b.IDBarang='$dataBarang->IDBarang' AND a.Stok>0");
            if ($qsn) {
                foreach ($qsn as $dsn) {
                    $hpp =  ($dataBarang->HPPMethod == "AVG") ? $avg : $dsn->HPP;
                    array_push($sn, array("SN" => $dsn->SN . " (GUDANG)", "Harga" => floatval($hpp)));
                }
            }
        }

        $totalAvailableStock = $sisaStokGudang + $sisaStokPurchasing;

        if ($dataBarang->IsSerialize == "0") {
            $limit = $totalAvailableStock;
            if ($limit > $sisa) $limit = $sisa;
        } else $limit = 1;

        return array("IDBarang" => $dataBarang->IDBarang, "KodeBarang" => $dataBarang->KodeBarang, "Nama" =>  $namaParent . $dataBarang->Nama, "No" => $no, "Kategori" => "", "Supplier" => "", "Harga" => $harga, "Jenis" => "", "IsSerialize" => $dataBarang->IsSerialize, "Limit" => $limit, "TotalAvailableStok" => $totalAvailableStock, "HPP" =>  floatval($avg), "HPPReal" => floatval($avg), "HPPRealPurchasing" => floatval($avg), "IsPaket" => $isPaket, "IsChild" => $isChild, "IDParent" => $idParent, "Sisa" => $sisa, "SerialNumberArray" => $sn, "StokPurchasing" => $sisaStokPurchasing, "StokGudang" => $sisaStokGudang, "LibCode" => $dataBarang->LibCode);
    }

    return null;
}

$act = antiSQLInjection($_GET['act']);
switch ($act) {

    case "DataList":
        $type = antiSQLInjection($_GET['status']);
        $pengirimanArray = array();

        $datestart = antiSQLInjection($_GET['datestart']);
        $expstart = explode("/", $datestart);
        $datestartchange = $expstart[2] . "-" . $expstart[1] . "-" . $expstart[0];

        $dateend = antiSQLInjection($_GET['dateend']);
        $expend = explode("/", $dateend);
        $dateendchange = $expend[2] . "-" . $expend[1] . "-" . $expend[0];

        $idPenjualan = antiSQLInjection($_GET['idPenjualan']);
        $nama = antiSQLInjection($_GET['nama']);


        if ($datestart != "" && $dateend != "") {
            $cond = "WHERE Tanggal BETWEEN '$datestartchange' AND '$dateendchange'";
        } else if ($datestart != "") {
            $cond = "WHERE Tanggal='$datestartchange'";
        } else {
            $cond = "WHERE DATE_FORMAT(Tanggal,'%Y-%m') = '" . date("Y-m") . "'";
        }

        if ($type != "")
            $cond .= " AND Status='$type'";

        if ($idPenjualan != "")
            $cond .= " AND IDPenjualan='$idPenjualan'";

        if ($nama != "")
            $cond .= " AND NoSuratJalan IN (SELECT DISTINCT(NoSuratJalan) FROM tb_penjualan_surat_jalan_detail WHERE NamaBarang LIKE '%$nama%') ";

        $query = $db->get_results("SELECT *,DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_penjualan_surat_jalan $cond AND (Status='1' OR Status='2') ORDER BY IDSuratJalan DESC");
        if ($query) {
            $i = 0;
            foreach ($query as $data) {
                $i++;

                $pj = $db->get_row("SELECT a.*, b.KodePelanggan, b.NamaPelanggan FROM tb_penjualan a, tb_pelanggan b WHERE a.IDPelanggan = b.IDPelanggan AND a.IDPenjualan='" . $data->IDPenjualan . "'");

                if ($data->KodeProyek == "") $kodeProyek = "UMUM";
                else $kodeProyek = $data->KodeProyek;

                $jurnal = $db->get_row("SELECT * FROM tb_jurnal WHERE Tipe='8' AND NoRef='$data->IDSuratJalan'");
                if ($jurnal) $statusJurnal = "1";
                else $statusJurnal = "0";

                array_push($pengirimanArray, array("IDSuratJalan" => $data->IDSuratJalan, "NoSuratJalan" => $data->NoSuratJalan, "No" => $i, "IDPenjualan" => $data->IDPenjualan, "NoPenjualan" => $data->NoPenjualan, "Tanggal" => $data->TanggalID, "Total" => $data->Total, "GrandTotal" => $data->GrandTotal, "TotalHPP" => $data->TotalHPP, "NamaPelanggan" => $pj->NamaPelanggan, "Status" => $data->Status, "StatusJurnal" => $statusJurnal));
            }
        }

        echo json_encode(array("pengiriman" => $pengirimanArray));
        break;

    case "LoadAllRequirement":
        $barang = array();
        $penjualan = array();

        $no_penjualan = intval(antiSQLInjection($_GET['no_penjualan']));
        $material_bantu = intval(antiSQLInjection($_GET['material_bantu']));
        $id_gudang = intval(antiSQLInjection($_GET['id_gudang']));

        $spb = $db->get_row("SELECT * FROM tb_penjualan WHERE IDPenjualan='$no_penjualan'");
        if ($id_gudang == "") {
            $id_gudang = $db->get_var("SELECT IDGudang FROM tb_gudang WHERE IsDefault='1' ORDER BY Nama ASC");
        }

        if ($id_gudang != "") {
            if ($material_bantu == "1") {
                $query = $db->get_results("SELECT * FROM tb_barang ORDER BY Nama");
                if ($query) {
                    $i = 0;
                    foreach ($query as $data) {
                        $i++;
                        $dBarang = getDetailBarang($db, $no_penjualan, $id_gudang, $data, $i, 999999, 0);
                        if ($dBarang != null) {
                            array_push($barang, $dBarang);
                        }
                    }
                }
            } else {
                $query = $db->get_results("SELECT * FROM tb_penjualan_detail WHERE NoPenjualan='" . $spb->NoPenjualan . "' AND (IsChild<>'1' OR IsChild IS NULL) AND NamaBarang NOT LIKE '&nbsp%' ORDER BY NoUrut");
                if ($query) {
                    $i = 0;
                    // $idsAdded = array();
                    // $stokPenjualanIds = array();
                    foreach ($query as $data) {
                        // if (in_array($data->IDBarang, $idsAdded)) continue;
                        $i++;
                        $qPaket = $db->get_results("SELECT a.*, b.IsSerialize, b.KodeBarang, b.Nama, b.LibCode FROM tb_barang_child a, tb_barang b WHERE a.IDBarang=b.IDBarang AND a.IDParent='" . $data->IDBarang . "'");
                        if ($qPaket) {
                            $dbarang = $db->get_row("SELECT * FROM tb_barang WHERE IDBarang='" . $data->IDBarang . "'");
                            $stokTerkirim = $db->get_var("SELECT SUM(Qty) FROM tb_penjualan_surat_jalan_detail WHERE NoSuratJalan IN (SELECT NoSuratJalan FROM tb_penjualan_surat_jalan WHERE IDPenjualan='$no_penjualan' AND DeletedDate IS NULL) AND IDBarang='" . $data->IDBarang . "' AND IsPaket='1'");
                            if (!$stokTerkirim) $stokTerkirim = 0;
                            $stokPenjualan = $db->get_var("SELECT SUM(Qty) FROM tb_penjualan_detail WHERE NoPenjualan='" . $data->NoPenjualan . "' AND IDBarang='" . $data->IDBarang . "'");
                            // $stokPenjualanVO = $db->get_var("SELECT SUM(Qty) FROM tb_penjualan_vo_detail WHERE NoVO IN (SELECT NoVO FROM tb_penjualan_vo WHERE IDPenjualan='" . $no_penjualan . "') AND IDBarang='" . $data->IDBarang . "'");
                            $stokPenjualanVO = 0;
                            $stokPenjualan += $stokPenjualanVO;
                            $sisa = $stokPenjualan - $stokTerkirim;

                            if ($dbarang->IsSerialize == "0") {
                                $limit = $sisa;
                            } else $limit = 1;

                            $stokHppPaketGudang = $stok->getStokAndHPPPaket($data->IDBarang, "tb_stok_gudang", "tb_kartu_stok_gudang");
                            $stokGudang = $stokHppPaketGudang['Stok'];

                            $stokHppPaketPurchasing = $stok->getStokAndHPPPaket($data->IDBarang, "tb_stok_purchasing", "tb_kartu_stok_purchasing");
                            $stokPurchasing = $stokHppPaketPurchasing['Stok'];

                            $kodePaket = $dbarang->KodeBarang;

                            array_push($barang, array("IDBarang" => $data->IDBarang, "KodeBarang" => $dbarang->KodeBarang, "Nama" => $data->NamaBarangDisplay, "No" => $i, "Kategori" => $dbarang->NamaDepartement, "Supplier" => $dbarang->NamaPerusahaan, "Harga" => $data->Harga, "Jenis" => $dbarang->Jenis, "IsSerialize" => $dbarang->IsSerialize, "Limit" => $limit, "HPP" => 0, "HPPReal" => 0, "IsPaket" => 1, "Sisa" => $sisa, "SerialNumberArray" => array(), "StokPurchasing" => $stokPurchasing, "StokGudang" => $stokGudang, "LibCode" => $dBarang->LibCode));

                            if ($qPaket) {
                                foreach ($qPaket as $dPaket) {
                                    $dbarang = $db->get_row("SELECT * FROM tb_barang WHERE IDBarang='" . $dPaket->IDBarang . "'");

                                    $stokPaket = $stokPenjualan * $dPaket->Qty;

                                    $stokTerkirim = $db->get_var("SELECT SUM(Qty) FROM tb_penjualan_surat_jalan_detail WHERE NoSuratJalan IN (SELECT NoSuratJalan FROM tb_penjualan_surat_jalan WHERE IDPenjualan='$no_penjualan' AND DeletedDate IS NULL) AND IDBarang='" . $dPaket->IDBarang . "' AND IsChild='1' AND (IDParent='$data->IDBarang' OR IDParent IS NULL OR IDParent='0')");
                                    if (!$stokTerkirim) $stokTerkirim = 0;

                                    // if ($stokPenjualanIds[$dbarang->IDBarang]) {
                                    //     $stokPaket += $stokPenjualanIds[$dbarang->IDBarang];
                                    // }

                                    $sisa = $stokPaket - $stokTerkirim;

                                    $dBarang = getDetailBarang($db, $no_penjualan, $id_gudang, $dbarang, $i, $sisa, 0, 1, 1, $data->IDBarang, "(PAKET $kodePaket) ");

                                    if ($dBarang != null && $dBarang['Sisa'] > 0) {
                                        array_push($barang, $dBarang);
                                    }
                                    // array_push($idsAdded, $dBarang['IDBarang']);
                                    // $stokPenjualanIds[$dBarang['IDBarang']] = $stokPaket;
                                }
                            }
                        } else {
                            $dbarang = $db->get_row("SELECT * FROM tb_barang WHERE IDBarang='" . $data->IDBarang . "'");

                            $stokTerkirim = $db->get_var("SELECT SUM(Qty) FROM tb_penjualan_surat_jalan_detail WHERE NoSuratJalan IN (SELECT NoSuratJalan FROM tb_penjualan_surat_jalan WHERE IDPenjualan='$no_penjualan' AND DeletedDate IS NULL) AND IDBarang='" . $data->IDBarang . "' AND IsChild='0'");
                            if (!$stokTerkirim) $stokTerkirim = 0;
                            $stokPenjualan = $db->get_var("SELECT SUM(Qty) FROM tb_penjualan_detail WHERE NoPenjualan='" . $data->NoPenjualan . "' AND IDBarang='" . $data->IDBarang . "'");
                            // $stokPenjualanVO = $db->get_var("SELECT SUM(Qty) FROM tb_penjualan_vo_detail WHERE NoVO IN (SELECT NoVO FROM tb_penjualan_vo WHERE IDPenjualan='" . $no_penjualan . "') AND IDBarang='" . $data->IDBarang . "'");
                            $stokPenjualanVO = 0;
                            $stokPenjualan += $stokPenjualanVO;

                            // if ($stokPenjualanIds[$dbarang->IDBarang]) {
                            //     $stokPenjualan += $stokPenjualanIds[$dbarang->IDBarang];
                            // }

                            $sisa = $stokPenjualan - $stokTerkirim;

                            $dBarang = getDetailBarang($db, $no_penjualan, $id_gudang, $dbarang, $i, $sisa, ($data->Harga - $data->DiskonValue));
                            if ($dBarang != null) {
                                array_push($barang, $dBarang);
                                // array_push($idsAdded, $dBarang['IDBarang']);
                                // $stokPenjualanIds[$dBarang['IDBarang']] = $stokPenjualan;
                            }
                        }
                    }
                }
            }
        }

        $penjualan = array();
        if ($material_bantu == "1") {
            $cond = "WHERE IsComplete='0' OR IsComplete='1'";
        } else {
            $cond = "WHERE IsComplete='0'";
        }
        $query = $db->get_results("SELECT * FROM tb_penjualan $cond ORDER BY NoPenjualan ASC");
        if ($query) {
            foreach ($query as $data) {
                $pelanggan = $db->get_var("SELECT NamaPelanggan FROM tb_pelanggan WHERE IDPelanggan='" . $data->IDPelanggan . "'");
                array_push($penjualan, array("IDPenjualan" => $data->IDPenjualan, "NoPenjualan" => $data->NoPenjualan, "NoPOKonsumen" => $data->NoPOKonsumen, "Pelanggan" => $pelanggan));
            }
        }

        $gudang = $db->get_results("SELECT * FROM tb_gudang ORDER BY Nama ASC");
        if (!$gudang) $gudang = array();

        $query = $db->get_row("SELECT a.*, b.KodePelanggan, b.NamaPelanggan FROM tb_penjualan a, tb_pelanggan b WHERE a.IDPelanggan = b.IDPelanggan AND a.IDPenjualan='$no_penjualan'");
        $return = array("barang" => $barang, "penjualan" => $query->NoPenjualan, "pelanggan" => $query->NamaPelanggan, "penjualan" => $penjualan, "gudang" => $gudang);
        echo json_encode($return);
        break;

    case "LoadDetailPO":
        $idPO = antiSQLInjection($_GET['idPO']);
        $return = array();
        $query = $db->get_results("SELECT * FROM tb_penerimaan_stok_detail WHERE NoPenerimaanBarang IN (SELECT NoPenerimaanBarang FROM tb_penerimaan_stok WHERE NoPO='$idPO')");
        if ($query) {
            $i = 0;
            foreach ($query as $data) {
                $i++;
                array_push($return, array("NoUrut" => $i, "IDBarang" => $data->IDBarang, "NamaBarang" => $data->NamaBarang, "QtyBarang" => intval($data->Qty), "SNBarang" => $data->SN, "IsSerialize" => $data->IsSerialize, "IsLoaded" => 1, "Harga" => $data->Harga));
            }
        }
        echo json_encode($return);
        break;

    case "InsertNew":
        $tanggal = antiSQLInjection($_POST['tanggal']);
        $exp = explode("/", $tanggal);
        $tanggal = $exp[2] . "-" . $exp[1] . "-" . $exp[0];
        $tanggal2 = $exp[2] . "/" . $exp[1] . "/";
        $tanggalCond = $exp[2] . "-" . $exp[1];

        $idPenjualan = antiSQLInjection($_POST['idPenjualan']);

        $spb = $db->get_row("SELECT * FROM tb_penjualan WHERE IDPenjualan='$idPenjualan'");
        $no_penjualan = $spb->NoPenjualan;

        $totalqty = antiSQLInjection($_POST['totalqty']);
        $totalHPP = antiSQLInjection($_POST['totalHPP']);
        $totalNilai = antiSQLInjection($_POST['totalNilai']);
        $totalMargin = antiSQLInjection($_POST['totalMargin']);
        $keterangan = antiSQLInjection($_POST['keterangan']);
        $cartArray = antiSQLInjection($_POST['cart']);
        $cartArray = json_decode($cartArray);
        $completeSPB = antiSQLInjection($_POST['completeSPB']);
        $material_bantu = antiSQLInjection($_POST['material_bantu']);
        $id_gudang = antiSQLInjection($_POST['id_gudang']);


        $message = $stok->ValidateStokUsage($cartArray, $id_gudang, $idPenjualan, "Surat Jalan ");

        if ($message == "") {
            $dataLast = $db->get_row("SELECT * FROM tb_penjualan_surat_jalan WHERE DATE_FORMAT(Tanggal,'%Y-%m')='" . $tanggalCond . "' ORDER BY NoSuratJalan DESC");
            if ($dataLast) $last = intval(substr($dataLast->NoSuratJalan, -3));
            else $last = 0;
            do {
                $last++;
                if ($last < 100 and $last >= 10)
                    $last = "0" . $last;
                else if ($last < 10)
                    $last = "00" . $last;
                $nopengiriman = "DO/SOPAN/" . $tanggal2 . $last;
                $checkNoTransaksi = $db->get_row("SELECT * FROM tb_penjualan_surat_jalan WHERE NoSuratJalan='$nopengiriman'");
            } while ($checkNoTransaksi);

            $spb = $db->get_row("SELECT * FROM tb_penjualan WHERE IDPenjualan='$idPenjualan'");

            $TotalNilai = $totalNilai;
            $DiskonPersen = $spb->DiskonPersen;
            $Diskon = $TotalNilai * $DiskonPersen / 100;
            $TotalNilai2 = $TotalNilai -  $Diskon;

            $PPNPersen = $spb->PPNPersen;
            $PPN = $TotalNilai2 * $PPNPersen / 100;
            $GrandTotal = $TotalNilai2 + $PPN;
            $totalMargin = $TotalNilai2 - $totalHPP;

            $query = $db->query("INSERT INTO tb_penjualan_surat_jalan SET NoSuratJalan='$nopengiriman', IDPenjualan='" . $idPenjualan . "', NoPenjualan='" . $no_penjualan . "', IDGudang='" . $id_gudang . "', Tanggal='$tanggal', Total='$totalqty', TotalNilai='$TotalNilai', DiskonPersen='$DiskonPersen', Diskon='$Diskon', TotalNilai2='$TotalNilai2', PPNPersen='$PPNPersen', PPN='$PPN', GrandTotal='$GrandTotal', TotalHPP='$totalHPP', TotalMargin='$totalMargin', Keterangan='$keterangan', CreatedBy='" . $_SESSION["uid"] . "', MaterialBantu='$material_bantu'");
            if ($query) {
                if ($completeSPB == "1") {
                    $db->query("UPDATE tb_penjualan SET IsComplete='$completeSPB' WHERE IDPenjualan='$idPenjualan'");
                }

                foreach ($cartArray as $data) {
                    if (isset($data)) {
                        $barangSPB = $db->get_row("SELECT * FROM tb_penjualan_detail WHERE IDBarang='" . $data->IDBarang . "' AND NoPenjualan='$no_penjualan'");

                        if ($barangSPB) {
                            $Diskon = $barangSPB->Diskon;
                            $HargaDiskon = $barangSPB->HargaDiskon;
                        } else {
                            $Diskon = 0;
                            $HargaDiskon = $data->Harga;
                        }

                        $data->SubTotal = $data->QtyBarang * $HargaDiskon;
                        $data->Margin = $HargaDiskon - $data->HPP;
                        $data->SubTotalMargin = $data->QtyBarang * $data->Margin;

                        $garansi = ($data->Garansi != "") ? $fungsi->ENDate($data->Garansi) : "";

                        $db->query("INSERT INTO tb_penjualan_surat_jalan_detail SET NoSuratJalan='$nopengiriman', NoUrut='" . $data->NoUrut . "', IDBarang='" . $data->IDBarang . "', NamaBarang='" . $data->NamaBarang . "', Qty='" . $data->QtyBarang . "', SN='" . $data->SNBarang . "', Harga='" . $data->Harga . "', HPP='" . $data->HPP . "', HPPReal='0', Margin='" . $data->Margin . "', SubTotal='" . $data->SubTotal . "', SubTotalHPP='" . $data->SubTotalHPP . "', SubTotalMargin='" . $data->SubTotalMargin . "', IDStok='" . $queryHPP->IDStokGudang . "', IsPaket='" . $data->IsPaket . "', IsChild='" . $data->IsChild . "', IDParent='" . $data->IDParent . "', Garansi='" . $garansi . "', IsInstallasi='" . $data->IsInstallasi . "', StokFrom='0', Diskon='$Diskon', HargaDiskon='$HargaDiskon'");
                    }
                }

                $TotalHPP = $db->get_var("SELECT SUM(SubTotalHPP) FROM tb_penjualan_surat_jalan_detail WHERE NoSuratJalan='$nopengiriman'");
                if (!$TotalHPP) $TotalHPP = 0;

                $SubTotal =  $db->get_var("SELECT SUM(SubTotal) FROM tb_penjualan_surat_jalan_detail WHERE NoSuratJalan='$nopengiriman'");
                $PPN = $SubTotal * $PPNPersen / 100;
                $GrandTotal = $SubTotal + $PPN;
                $TotalMargin = $SubTotal - $TotalHPP;

                $db->query("UPDATE tb_penjualan_surat_jalan SET TotalHPP='$TotalHPP', TotalHPPReal='$totalHPPReal', TotalMargin='$TotalMargin', TotalNilai='$SubTotal', TotalNilai2='$SubTotal', PPN='$PPN', GrandTotal='$GrandTotal' WHERE IDSuratJalan='$id'");

                $stok->SuratJalan($nopengiriman);

                echo json_encode(array("res" => 1, "mes" => "Surat Jalan berhasil disimpan."));
            } else {
                echo json_encode(array("res" => 0, "mes" => "Surat Jalan tidak dapat disimpan. Silahkan coba kembali."));
            }
        } else {
            echo json_encode(array("res" => 0, "mes" => $message));
        }

        break;

    case "Delete":
        $idr = antiSQLInjection($_POST['idr']);
        $remark = antiSQLInjection($_POST['remark']);

        $dataSuratJalan = $db->get_row("SELECT * FROM tb_penjualan_surat_jalan WHERE IDSuratJalan='$idr'");
        $nofaktur = $dataSuratJalan->NoSuratJalan;
        $stok->DeleteSuratJalan($nofaktur);

        $db->query("UPDATE tb_penjualan_surat_jalan SET Status='2', DeletedRemark='$remark', DeletedDate=NOW(), DeletedBy='" . $_SESSION['uid'] . "', ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW()  WHERE IDSuratJalan='$idr'");

        $db->query("UPDATE tb_penjualan SET IsComplete='0' WHERE IDPenjualan='$dataSuratJalan->IDPenjualan'");
        echo "1";

        break;

    case "PenerimaanBarang":
        $key = antiSQLInjection($_GET['key']);
        $idPengiriman = antiSQLInjection($_GET['idPengiriman']);

        $cek = $db->get_row("SELECT * FROM tb_karyawan WHERE CardNumber='$key'");
        if ($cek) {
            $query = $db->query("UPDATE tb_pengiriman SET Status='Diterima', RecievedBy='" . $cek->IDKaryawan . "', DateTimeRecieved=NOW() WHERE IDPengiriman='$idPengiriman'");
            if ($query)
                echo "1";
            else
                echo "2";
        } else {
            echo "0";
        }
        break;

    case "Detail":
        $id = antiSQLInjection($_GET['id']);
        $master = array();
        $detail = array();
        $query = $db->get_row("SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID, DATE_FORMAT(DeletedDate, '%d/%m/%Y %H:%i:%s') AS DeletedDateID FROM tb_penjualan_surat_jalan WHERE IDSuratJalan='$id' ORDER BY IDSuratJalan ASC");
        if ($query) {
            /*$po = $db->get_row("SELECT * FROM tb_po WHERE NoPO='NoPO'");
            $proyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='".$query->IDProyek."'");
            $supplier = $db->get_var("SELECT NamaPerusahaan FROM tb_supplier WHERE IDSupplier='".$po->IDSupplier."'");
            $recievedBy = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='".$query->RecievedBy."'");
            $proyek = $proyek->KodeProyek." - ".$proyek->NamaProyek;*/
            $user = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $query->CreatedBy . "'");
            $penjualan = $db->get_row("SELECT a.*, b.KodePelanggan, b.NamaPelanggan FROM tb_penjualan a, tb_pelanggan b WHERE a.IDPelanggan = b.IDPelanggan AND a.IDPenjualan='" . $query->IDPenjualan . "'");

            if ($query->Keterangan != "") $keterangan = $query->Keterangan;
            else $keterangan = "-";

            $deletedBy = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $query->DeletedBy . "'");

            $master = array("NoSuratJalan" => $query->NoSuratJalan, "NoPenjualan" => $query->NoPenjualan, "Pelanggan" => $penjualan->NamaPelanggan, "Tanggal" => $query->TanggalID, "usrlogin" => $user, "Total" => $query->Total, "TotalNilai" => $query->TotalNilai, "Diskon" => $query->Diskon, "DiskonPersen" => $query->DiskonPersen, "TotalNilai2" => $query->TotalNilai2, "PPN" => $query->PPN, "PPNPersen" => $query->PPNPersen, "GrandTotal" => $query->GrandTotal, "TotalHPP" => $query->TotalHPP, "TotalMargin" => $query->TotalMargin, "Keterangan" => $keterangan, "idPenjualan" => $query->IDPenjualan, "deleted_date" => $query->DeletedDateID, "deleted_by" => $deletedBy, "deleted_remark" => $query->DeletedRemark);

            // $query = $db->get_results("SELECT *, DATE_FORMAT(Garansi, '%d/%m/%Y') AS GaransiID FROM tb_penjualan_surat_jalan_detail WHERE NoSuratJalan='".$query->NoSuratJalan."' ORDER BY NoUrut ASC");
            // $query = $db->get_results("SELECT *, DATE_FORMAT(Garansi, '%d/%m/%Y') AS GaransiID, SUM(Qty) AS QTY_REAL, (SUM(SubTotalHPP)/SUM(Qty)) AS HPP_AVG, SUM(SubTotal) AS SUB_TOTAL, SUM(SubTotalHPP) AS SUB_TOTAL_HPP, SUM(SubTotalMargin) AS SUB_TOTAL_MARGIN FROM tb_penjualan_surat_jalan_detail WHERE NoSuratJalan='" . $query->NoSuratJalan . "' GROUP BY IDBarang, SN ORDER BY NoUrut ASC");
            $query = $db->get_results("SELECT *, DATE_FORMAT(Garansi, '%d/%m/%Y') AS GaransiID, Qty AS QTY_REAL, HPP AS HPP_AVG, SubTotal AS SUB_TOTAL, SubTotalHPP AS SUB_TOTAL_HPP, SubTotalMargin AS SUB_TOTAL_MARGIN FROM tb_penjualan_surat_jalan_detail WHERE NoSuratJalan='" . $query->NoSuratJalan . "' ORDER BY NoUrut ASC");
            if ($query) {
                $i = 0;
                foreach ($query as $data) {
                    $i++;
                    array_push($detail, array("NamaBarang" => $data->NamaBarang, "No" => $i, "Qty" => $data->QTY_REAL, "SN" => $data->SN, "Harga" => $data->Harga, "HPP" => $data->HPP_AVG, "Margin" => $data->Margin, "SubTotal" => $data->SUB_TOTAL, "SubTotalHPP" => $data->SUB_TOTAL_HPP, "SubTotalMargin" => $data->SUB_TOTAL_MARGIN, "Garansi" => $data->GaransiID, "IDetail" => $data->IDetail, "IsInstallasi" => intval($data->IsInstallasi), "Diskon" => $data->Diskon, "HargaDiskon" => $data->HargaDiskon, "StokFrom" => $data->StokFrom, "IDStok" => $data->IDStok));
                }
            }
        }
        echo json_encode(array("master" => $master, "detail" => $detail));
        break;

    case "UpdateIsInstalasi":
        $IDetail = antiSQLInjection($_POST['IDetail']);
        $IsInstallasi = antiSQLInjection($_POST['IsInstallasi']);
        $query = $db->query("UPDATE tb_penjualan_surat_jalan_detail SET IsInstallasi='$IsInstallasi' WHERE IDetail='$IDetail'");
        echo "1";

    default:
        echo "";
}
