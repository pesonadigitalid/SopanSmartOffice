<?php
include_once "../config/connection.php";
include_once "../library/class.stok.php";

include_once "../library/class.fungsi.php";

$stok = new Stok($db);
$fungsi = new Fungsi();

$act = antiSQLInjection($_GET['act']);
switch ($act) {
    case "LoadAllRequirement":
        $barang = array();
        $po = array();
        $gudang = array();
        $ppn = antiSQLInjection($_GET['ppn']);
        $idPO = antiSQLInjection($_GET['idPO']);

        $qDetail = $db->get_row("SELECT * FROM tb_po_detail WHERE NoPO='" . $idPO . "'");

        $query = $db->get_results("SELECT 
            a.*, 
            b.NamaPerusahaan, 
            c.Nama AS Jenis,
            e.Diskon
        FROM tb_barang a, 
            tb_supplier b, 
            tb_jenis_material c, 
            tb_po_detail e
        WHERE a.IDSupplier=b.IDSupplier 
            AND a.IDJenis=c.IDMaterial 
            AND ((a.IDBarang=e.IDBarang AND e.NoPO='$idPO')
            OR 
            (a.IDParent=e.IDBarang AND e.NoPO='$idPO'))
        ORDER BY a.Nama ASC");


        if ($query) {
            $i = 0;

            $TotalItemWithPriceAll = 0;
            foreach ($query as $data) {
                $qPaket = $db->get_results("SELECT a.*, b.IsSerialize, b.KodeBarang, b.Nama, b.LibCode FROM tb_barang_child a, tb_barang b WHERE a.IDBarang=b.IDBarang AND a.IDParent='" . $data->IDBarang . "' AND a.HargaPublish>0");
                if ($qPaket) {
                    $TotalItemWithPriceAll += count($qPaket);
                } else {
                    $TotalItemWithPriceAll++;
                }
            }

            foreach ($query as $data) {
                $i++;
                $isPaket = $db->get_results("SELECT a.*, b.IsSerialize, b.KodeBarang, b.Nama, b.LibCode FROM tb_barang_child a, tb_barang b WHERE a.IDBarang=b.IDBarang AND a.IDParent='" . $data->IDBarang . "'");
                if ($isPaket) {

                    $TotalItemWithPrice = 0;
                    foreach ($isPaket as $dataPaket) {
                        if ($dataPaket->HargaPublish > 0) {
                            $TotalItemWithPrice++;
                        }
                    }

                    foreach ($isPaket as $dataPaket) {
                        $dataStokDiterima = $db->get_var("SELECT SUM(Qty) FROM tb_penerimaan_stok_detail WHERE NoPenerimaanBarang IN (SELECT NoPenerimaanBarang FROM tb_penerimaan_stok WHERE NoPO='$idPO' And Status='1') AND IDBarang='" . $dataPaket->IDBarang . "' AND NamaBarang LIKE '%" . $data->Nama . "%' AND IsPaket='1'");
                        if (!$dataStokDiterima) $dataStokDiterima = 0;
                        $dataStokDiterima = intval($dataStokDiterima);

                        $dataStokPO = $db->get_var("SELECT SUM(Qty) FROM tb_po_detail WHERE NoPO='$idPO' AND IDBarang='" . $data->IDBarang . "'");
                        if (!$dataStokPO) $dataStokPO = 0;
                        $dataStokPO = $dataStokPO * $dataPaket->Qty;

                        $diskonPO = $db->get_var("SELECT DiskonPersen FROM tb_po WHERE NoPO='$idPO'");
                        $hpp = $fungsi->getPriceAfterDistributedDiscount($data->Diskon, $dataPaket->HargaPublish, $TotalItemWithPrice, $TotalItemWithPrice);
                        if ($diskonPO && $diskonPO > 0) {
                            $hpp = $fungsi->getPriceAfterDistributedDiscount($diskonPO, $hpp, $TotalItemWithPriceAll);
                        }

                        $sisa = $dataStokPO - $dataStokDiterima;
                        if ($dataPaket->IsSerialize == "0") {
                            $limit = $sisa;
                        } else $limit = 1;
                        if ($limit > 0 && $sisa > 0) {
                            array_push($barang, array("IDBarang" => $dataPaket->IDBarang, "KodeBarang" => $dataPaket->KodeBarang, "Nama" => $dataPaket->Nama . " - Paket " . $data->Nama, "No" => $i, "Supplier" => $dataPaket->NamaPerusahaan, "Harga" => number_format($dataPaket->HargaPublish), "Jenis" => $dataPaket->Jenis, "IsSerialize" => $dataPaket->IsSerialize, "LibCode" => $dataPaket->LibCode, "Limit" => $limit, "HPP" => floatval($hpp), "IsPaket" => 1, "IsChild" => 1, "Sisa" => $sisa, "Diterima" => $dataStokDiterima));
                        }
                    }
                } else {
                    $dataStokDiterima = $db->get_var("SELECT SUM(Qty) FROM tb_penerimaan_stok_detail WHERE NoPenerimaanBarang IN (SELECT NoPenerimaanBarang FROM tb_penerimaan_stok WHERE NoPO='$idPO' And Status='1') AND IDBarang='" . $data->IDBarang . "' AND IsPaket='0'");
                    if (!$dataStokDiterima) $dataStokDiterima = 0;
                    $dataStokPO = $db->get_var("SELECT SUM(Qty) FROM tb_po_detail WHERE NoPO='$idPO' AND IDBarang='" . $data->IDBarang . "'");
                    $sisa = $dataStokPO - $dataStokDiterima;
                    if ($data->IsSerialize == "0") {
                        $sisa = $dataStokPO - $dataStokDiterima;
                        $limit = $sisa;
                    } else $limit = 1;

                    //$hpp = $db->get_var("SELECT Harga FROM tb_po_detail WHERE NoPO='$idPO' AND IDBarang='".$data->IDBarang."'");

                    $diskonPO = $db->get_var("SELECT DiskonPersen FROM tb_po WHERE NoPO='$idPO'");
                    $hpp = $db->get_var("SELECT Harga FROM tb_po_detail WHERE NoPO='$idPO' AND IDBarang='" . $data->IDBarang . "'");
                    if ($diskonPO && $diskonPO > 0) {
                        $hpp = $fungsi->getPriceAfterDistributedDiscount($diskonPO, $hpp, $TotalItemWithPriceAll);
                    }

                    // $cekPPN = $db->get_var("SELECT PPNPersen FROM tb_po WHERE NoPO='$idPO'");
                    // if ($cekPPN) {
                    //     $hpp = $hpp + ($hpp * $cekPPN / 100);
                    // }
                    // $hpp = round($hpp);

                    if ($limit > 0 && $sisa > 0) {
                        array_push($barang, array("IDBarang" => $data->IDBarang, "KodeBarang" => $data->KodeBarang, "Nama" => $data->Nama, "No" => $i, "Supplier" => $data->NamaPerusahaan, "Harga" => number_format($data->Harga), "Jenis" => $data->Jenis, "IsSerialize" => $data->IsSerialize, "LibCode" => $data->LibCode, "Limit" => $limit, "HPP" => $hpp, "IsPaket" => 0, "IsChild" => 0, "Sisa" => $sisa));
                    }
                }
            }
        }

        if ($ppn == "ppn")
            $cond = "AND IsPajak=1";
        else
            $cond = "AND IsPajak=0";

        $query = $db->get_results("SELECT * FROM tb_po WHERE Completed='0' $cond ORDER BY NoPO ASC");
        if ($query) {
            foreach ($query as $data) {
                $supplier = $db->get_row("SELECT * FROM tb_supplier WHERE IDSupplier='" . $data->IDSupplier . "'");
                if ($supplier) $rSupplier = $supplier->NamaPerusahaan;
                else $rSupplier = "-";

                array_push($po, array("IDPO" => $data->IDPO, "NoPO" => $data->NoPO, "Supplier" => $rSupplier));
            }
        }

        $queryGudang = $db->get_results("SELECT * FROM tb_gudang ORDER BY Nama ASC");
        if ($queryGudang) {
            foreach ($queryGudang as $data) {
                array_push($gudang, array("IDGudang" => $data->IDGudang, "Nama" => $data->Nama, "IsDefault" => intval($data->IsDefault)));
            }
        }

        $grandTotal = $db->get_var("SELECT GrandTotal FROM tb_po WHERE NoPO='$idPO'");
        $totalHPP = $db->get_var("SELECT SUM(TotalHPP) FROM tb_penerimaan_stok WHERE NoPO='$idPO'");
        $limitHPP = $grandTotal - $totalHPP;
        $return = array("barang" => $barang, "po" => $po, "limitHPP" => $limitHPP, "gudang" => $gudang);
        echo json_encode($return);
        break;

    case "InsertNew":
        $tanggal = antiSQLInjection($_POST['tanggal']);
        $exp = explode("/", $tanggal);
        $tanggal = $exp[2] . "-" . $exp[1] . "-" . $exp[0];
        $tanggalCond = $exp[2] . "-" . $exp[1];
        $tanggalCond2 = "/SPN/" . $exp[2] . "/" . $exp[1] . "/";

        $po = antiSQLInjection($_POST['po']);
        $gudang = antiSQLInjection($_POST['gudang']);
        $totalitem = antiSQLInjection($_POST['totalitem']);
        $totaljenisitem = antiSQLInjection($_POST['totaljenisitem']);
        $keterangan = antiSQLInjection($_POST['keterangan']);
        $cartArray = antiSQLInjection($_POST['cart']);
        $completePO = antiSQLInjection($_POST['completePO']);
        $totalHPP = antiSQLInjection($_POST['totalHPP']);
        $cartArray = json_decode($cartArray);

        $dataLast = $db->get_row("SELECT * FROM tb_penerimaan_stok WHERE DATE_FORMAT(Tanggal,'%Y-%m')='" . $tanggalCond . "' ORDER BY NoPenerimaanBarang DESC");
        if ($dataLast) $last = intval(substr($dataLast->NoPenerimaanBarang, -3));
        else $last = 0;
        do {
            $last++;
            if ($last < 100 and $last >= 10)
                $last = "0" . $last;
            else if ($last < 10)
                $last = "00" . $last;
            $notransaksi = "PB" . $tanggalCond2 . $last;
            $checkNoTransaksi = $db->get_row("SELECT * FROM tb_penerimaan_stok WHERE NoPenerimaanBarang='$notransaksi'");
        } while ($checkNoTransaksi);

        $lanjut = true;

        //CEK BARANG APAKAH MEMANG ADA DI CART ATAU TIDAK
        foreach ($cartArray as $data) {
            if (isset($data)) {
                if ($data->IsPaket == "0") {
                    $barang = $db->get_row("SELECT * FROM tb_barang WHERE IDBarang='" . $data->IDBarang . "'");
                    $dDetailPO = $db->get_row("SELECT * FROM tb_po_detail WHERE IDBarang='" . $barang->IDBarang . "' OR IDBarang='" . $barang->IDParent . "'");
                    if ($dDetailPO) {
                    } else {
                        $message = "Penerimaan Stok tidak dapat disimpan. " . $barang->Nama . " tidak terdapat didalam No PO tersebut!";
                        $lanjut = false;
                    }

                    if ($data->SNBarang != "") {
                        $cek = $db->get_row("select * from tb_stok_gudang_serial_number WHERE SN='$data->SNBarang'");
                        $cek2 = $db->get_row("select * from tb_stok_purchasing_serial_number WHERE SN='$data->SNBarang'");
                        if ($cek || $cek2) {
                            $message = "Penerimaan Stok tidak dapat disimpan. SN " . $data->SNBarang . " telah tersimpan pada persediaan saat ini. Pastikan anda tidak menginput double untuk SN ini.";
                            $lanjut = false;
                        }
                    }
                }
            }
        }

        if ($lanjut) {
            $dataPO = $db->get_row("SELECT * FROM tb_po WHERE NoPO='$po'");

            $query = $db->query("INSERT INTO tb_penerimaan_stok SET NoPenerimaanBarang='$notransaksi', NoPO='$po', IDSupplier='" . $dataPO->IDSupplier . "', IDPenjualan='" . intval($dataPO->IDPenjualan) . "', IDGudang='$gudang', Tanggal='$tanggal', TotalQty='$totalitem', TotalJenisItem='$totaljenisitem', Keterangan='$keterangan', Status='1', CreatedBy='" . $_SESSION["uid"] . "', TotalHPP='$totalHPP'");
            if ($query) {
                $id = $db->get_var("SELECT LAST_INSERT_ID()");

                if ($completePO == "1")
                    $db->query("UPDATE tb_po SET Completed='1' WHERE NoPO='$po'");

                foreach ($cartArray as $data) {
                    if (isset($data)) {
                        $db->query("INSERT INTO tb_penerimaan_stok_detail SET NoPenerimaanBarang='$notransaksi', NoUrut='" . $data->NoUrut . "', IDBarang='" . $data->IDBarang . "', NamaBarang='" . $data->NamaBarang . "', Qty='" . $data->QtyBarang . "', SN='" . $data->SNBarang . "', HPP='" . $data->HPP . "', IsPaket='" . $data->IsPaket . "', SubTotal='" . $data->SubTotal . "'");
                    }
                }

                $stok->PenerimaanStok($notransaksi, $gudang, $tanggal, $dataPO->IDPenjualan);

                echo json_encode(array("res" => 1, "mes" => "Data penerimaan stok barang berhasil disimpan. Stok berhasil diupdate!"));
            } else {
                echo json_encode(array("res" => 0, "mes" => "Penerimaan Barang tidak dapat disimpan. Silahkan coba kembali."));
            }
        } else {
            echo json_encode(array("res" => 0, "mes" => $message));
        }
        break;

    case "DisplayData":
        $datestart = antiSQLInjection($_GET['datestart']);
        $expstart = explode("/", $datestart);
        $datestartchange = $expstart[2] . "-" . $expstart[1] . "-" . $expstart[0];

        $dateend = antiSQLInjection($_GET['dateend']);
        $expend = explode("/", $dateend);
        $dateendchange = $expend[2] . "-" . $expend[1] . "-" . $expend[0];

        $supplier = antiSQLInjection($_GET['supplier']);
        $ppn = antiSQLInjection($_GET['ppn']);

        if ($datestart != "" && $dateend != "") {
            $cond = "WHERE a.Tanggal BETWEEN '$datestartchange' AND '$dateendchange'";
        } else if ($datestart != "") {
            $cond = "WHERE a.Tanggal='$datestartchange'";
        } else {
            $cond = "WHERE DATE_FORMAT(a.Tanggal,'%Y-%m') = '" . date("Y-m") . "'";
        }

        if ($supplier != "")
            $cond .= " AND a.IDSupplier='$supplier'";

        $penerimaan = array();

        if ($ppn == "true")
            $cond .= " AND a.`NoPO`=b.`NoPO` AND b.`IsPajak`=1";
        else
            $cond .= " AND a.`NoPO`=b.`NoPO` AND b.`IsPajak`=0";

        $query = $db->get_results("SELECT a.*, b.NoPO, DATE_FORMAT(a.Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_penerimaan_stok a, tb_po b $cond And (Status='1' OR Status='2') ORDER BY NoPenerimaanBarang ASC");
        if ($query) {
            $i = 0;
            foreach ($query as $data) {
                $i++;
                $created = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $data->CreatedBy . "'");
                $dataSupplier = $db->get_var("SELECT NamaPerusahaan FROM tb_supplier WHERE IDSupplier = '" . $data->IDSupplier . "'");
                $detailPO = $db->get_row("SELECT * FROM tb_po WHERE NoPO = '" . $data->NoPO . "'");

                array_push($penerimaan, array(
                    "IDPenerimaan" => $data->IDPenerimaan,
                    "NoPenerimaan" => $data->NoPenerimaanBarang,
                    "NoPO" => $data->NoPO,
                    "IsPajak" => $detailPO->IsPajak,
                    "No" => $i,
                    "Supplier" => $dataSupplier,
                    "Tanggal" => $data->TanggalID,
                    "Keterangan" => $data->Keterangan,
                    "CreatedBy" => $created,
                    "Status" => $data->Status,
                ));
            }
        }

        /* LOAD SUPPLIER */
        $supplier = array();
        $query = $db->get_results("SELECT * FROM tb_supplier ORDER BY IDSupplier ASC");
        if ($query) {
            $return = array();
            $i = 0;
            foreach ($query as $data) {
                array_push($supplier, array(
                    "IDSupplier" => $data->IDSupplier,
                    "NamaSupplier" => $data->NamaPerusahaan
                ));
            }
        }

        echo json_encode(array("penerimaan" => $penerimaan, "supplier" => $supplier));
        break;

    case "Delete":
        $idr = antiSQLInjection($_POST['idr']);
        $remark = antiSQLInjection($_POST['remark']);

        $dataPenerimaan = $db->get_row("SELECT * FROM tb_penerimaan_stok WHERE IDPenerimaan='$idr'");
        $nofaktur = $dataPenerimaan->NoPenerimaanBarang;
        if ($stok->CheckAllowDeletePenerimaanStok($nofaktur)) {
            $stok->DeletePenerimaanStok($nofaktur);
            //$db->query("UPDATE tb_penerimaan_stok SET Status='0', ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW() WHERE IDPenerimaan='$idr'");
            $db->query("UPDATE tb_penerimaan_stok SET Status='2', DeletedRemark='$remark', DeletedDate=NOW(), DeletedBy='" . $_SESSION['uid'] . "', ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW() WHERE IDPenerimaan='$idr'");
            $db->query("UPDATE tb_po SET Completed='0' WHERE NoPO='" . $dataPenerimaan->NoPO . "'");
            echo "1";
        } else {
            echo "2";
        }

        break;

    case "Detail":
        $id = antiSQLInjection($_GET['id']);
        $master = array();
        $detail = array();
        $query = $db->get_row("SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID, DATE_FORMAT(DeletedDate, '%d/%m/%Y %H:%i:%s') AS DeletedDateID FROM tb_penerimaan_stok WHERE IDPenerimaan='$id' ORDER BY IDPenerimaan ASC");
        if ($query) {
            $user = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $query->CreatedBy . "'");
            $supplier = $db->get_var("SELECT NamaPerusahaan FROM tb_supplier WHERE IDSupplier='" . $query->IDSupplier . "'");
            if ($query->Keterangan != "") $keterangan = $query->Keterangan;
            else $keterangan = "-";

            $deletedBy = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $query->DeletedBy . "'");

            $master = array("no_penerimaan" => $query->NoPenerimaanBarang, "no_po" => $query->NoPO, "supplier" => $supplier, "tanggal" => $query->TanggalID, "usrlogin" => $user, "total_qty" => $query->TotalQty, "keterangan" => $query->Keterangan, "total_jenis" => $query->TotalJenisItem, "totalHPP" => $query->TotalHPP, "deleted_date" => $query->DeletedDateID, "deleted_by" => $deletedBy, "deleted_remark" => $query->DeletedRemark);

            $query = $db->get_results("SELECT * FROM tb_penerimaan_stok_detail WHERE NoPenerimaanBarang='" . $query->NoPenerimaanBarang . "' ORDER BY NoUrut ASC");
            if ($query) {
                $i = 0;
                foreach ($query as $data) {
                    $i++;
                    array_push($detail, array("NamaBarang" => $data->NamaBarang, "No" => $i, "Qty" => $data->Qty, "SN" => $data->SN, "HPP" => $data->HPP, "SubTotal" => $data->SubTotal));
                }
            }
        }
        echo json_encode(array("master" => $master, "detail" => $detail));
        break;
    default:
        echo "";
}
