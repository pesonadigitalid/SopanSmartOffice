<?php
include_once "../config/connection.php";

$act = antiSQLInjection($_GET['act']);
switch ($act) {

    case "DataList":
        $type = antiSQLInjection($_GET['status']);
        $pengirimanArray = array();
        $proyekArray = array();

        $datestart = antiSQLInjection($_GET['datestart']);
        $expstart = explode("/", $datestart);
        $datestartchange = $expstart[2] . "-" . $expstart[1] . "-" . $expstart[0];

        $dateend = antiSQLInjection($_GET['dateend']);
        $expend = explode("/", $dateend);
        $dateendchange = $expend[2] . "-" . $expend[1] . "-" . $expend[0];

        $kode_proyek = antiSQLInjection($_GET['kode_proyek']);

        if ($datestart != "" && $dateend != "") {
            $cond = "WHERE Tanggal BETWEEN '$datestartchange' AND '$dateendchange'";
        } else if ($datestart != "") {
            $cond = "WHERE Tanggal='$datestartchange'";
        } else {
            $cond = "WHERE DATE_FORMAT(Tanggal,'%Y-%m') = '" . date("Y-m") . "'";
        }

        if ($kode_proyek != "")
            $cond .= "AND IDProyek='$kode_proyek'";

        if ($type != "")
            $cond .= " AND Status='$type'";

        $query = $db->get_results("SELECT *,DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID, DATE_FORMAT(DateTimeRecieved, '%d/%m/%Y') AS DateTimeRecievedID FROM tb_pengiriman $cond ORDER BY IDPengiriman DESC");
        if ($query) {
            $i = 0;
            foreach ($query as $data) {
                $i++;
                $proyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='" . $data->IDProyek . "'");
                $karyawan = $db->get_row("SELECT * FROM tb_karyawan WHERE IDKaryawan='" . $data->RecievedBy . "'");
                if ($karyawan) $karyawan = $karyawan->Nama;
                else $karyawan = "";
                if ($data->KodeProyek == "") $kodeProyek = "UMUM";
                else $kodeProyek = $data->KodeProyek;
                array_push($pengirimanArray, array("IDPengiriman" => $data->IDPengiriman, "NoPengiriman" => $data->NoPengiriman, "No" => $i, "KodeProyek" => $kodeProyek, "Tahun" => $proyek->Tahun, "Tanggal" => $data->TanggalID, "Total" => $data->Total, "Status" => $data->Status, "CreatedBy" => $created, "RecievedBy" => $karyawan, "DateTimeRecieved" => $data->DateTimeRecievedID));
            }
        }

        $query = $db->get_results("SELECT * FROM tb_proyek WHERE Status='2' ORDER BY KodeProyek ASC");
        if ($query) {
            foreach ($query as $data) {
                array_push($proyekArray, array("IDProyek" => $data->IDProyek, "KodeProyek" => $data->KodeProyek, "NamaProyek" => $data->NamaProyek, "Tahun" => $data->Tahun));
            }
        }

        //GRAB ALL TOTAL DATA
        $all = $db->get_var("SELECT COUNT(*) FROM tb_pengiriman");
        if (!$all) $all = '';
        $new = $db->get_var("SELECT COUNT(*) FROM tb_pengiriman WHERE Status='Baru'");
        if (!$new) $new = '';
        $success = $db->get_var("SELECT COUNT(*) FROM tb_pengiriman WHERE Status='Diterima'");
        if (!$success) $success = '';
        $rejected = $db->get_var("SELECT COUNT(*) FROM tb_pengiriman WHERE Status='Rejected'");
        if (!$rejected) $rejected = '';

        echo json_encode(array("pengiriman" => $pengirimanArray, "proyek" => $proyekArray, "all" => $all, "new" => $new, "success" => $success, "rejected" => $rejected));
        break;
    case "LoadAllRequirement":
        $barang = array();
        $proyek = array();
        $po = array();
        $idPO = antiSQLInjection($_GET['idPO']);
        $id_proyek = intval(antiSQLInjection($_GET['id_proyek']));
        if ($id_proyek != "") $condPO = " AND IDProyek='$id_proyek'";

        $query = $db->get_results("SELECT * FROM tb_po WHERE NoPO NOT IN (SELECT NoPO FROM tb_pengiriman) $condPO");
        if ($query) {
            foreach ($query as $data) {
                $supplier = $db->get_var("SELECT NamaPerusahaan FROM tb_supplier WHERE IDSupplier='" . $data->IDSupplier . "'");
                array_push($po, array("IDPO" => $data->IDPO, "NoPO" => $data->NoPO, "Supplier" => $supplier));
            }
        }

        $query = $db->get_results("SELECT * FROM tb_barang WHERE (StokGudang>0 OR IDBarang IN (SELECT IDBarang FROM tb_stok_purchasing WHERE IDProyek='$id_proyek' AND SisaStok>0)) AND Kategori='2'");
        if ($query) {
            $i = 0;
            foreach ($query as $data) {
                $i++;
                $stokPurchasing = $db->get_var("SELECT SUM(SisaStok) FROM tb_stok_purchasing WHERE IDBarang='" . $data->IDBarang . "' AND IDProyek='$id_proyek'");
                if (!$stokPurchasing) $stokPurchasing = 0;
                array_push($barang, array("IDBarang" => $data->IDBarang, "KodeBarang" => $data->KodeBarang, "Nama" => $data->Nama, "No" => $i, "IsSerialize" => $data->IsSerialize, "StokGudang" => $data->StokGudang, "StokPurchasing" => $stokPurchasing));
            }
        }

        $query = $db->get_results("SELECT * FROM tb_proyek WHERE Status='2' ORDER BY NamaProyek ASC");
        if ($query) {
            foreach ($query as $data) {
                array_push($proyek, array("IDProyek" => $data->IDProyek, "NamaProyek" => $data->NamaProyek, "KodeProyek" => $data->KodeProyek));
            }
        }
        $return = array("barang" => $barang, "proyek" => $proyek, "po" => $po);
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

        $id_proyek = antiSQLInjection($_POST['id_proyek']);
        $totalqty = antiSQLInjection($_POST['totalqty']);
        $keterangan = antiSQLInjection($_POST['keterangan']);
        $status_pengiriman = antiSQLInjection($_POST['status_pengiriman']);
        $diterima_id = antiSQLInjection($_POST['diterima_id']);
        $cartArray = antiSQLInjection($_POST['cart']);
        $cartArray = json_decode($cartArray);

        $get_data = $db->get_row("SELECT IDProyek,KodeProyek FROM tb_proyek WHERE IDProyek='$id_proyek'");

        if ($diskon_persen == "") $diskon_persen = "0";
        if ($ppn_persen == "") $ppn_persen = "0";

        $dataLast = $db->get_row("SELECT * FROM tb_pengiriman WHERE DATE_FORMAT(Tanggal,'%Y-%m')='" . date("Y-m") . "' ORDER BY NoPengiriman DESC");
        if ($dataLast) {
            $last = substr($dataLast->NoPengiriman, -5);
            $last++;
            if ($last < 10000 and $last >= 1000)
                $last = "0" . $last;
            else if ($last < 1000 and $last >= 100)
                $last = "00" . $last;
            else if ($last < 100 and $last >= 10)
                $last = "000" . $last;
            else if ($last < 10)
                $last = "0000" . $last;
            $nopengiriman = "DO" . date("Ym") . $last;
        } else {
            $nopengiriman = "DO" . date("Ym") . "00001";
        }

        if ($diterima_id != "") {
            $status_pengiriman = 'Diterima';
            $condSQL = ", RecievedBy='$diterima_id', DateTimeRecieved=NOW()";
        }

        $query = $db->query("INSERT INTO tb_pengiriman SET NoPengiriman='$nopengiriman', IDProyek='" . $get_data->IDProyek . "', KodeProyek='" . $get_data->KodeProyek . "', Tanggal='$tanggal', Total='$totalqty', Status='$status_pengiriman', Keterangan='$keterangan', CreatedBy='" . $_SESSION["uid"] . "' $condSQL");
        if ($query) {
            echo "1";
            $grandTotal = 0;
            foreach ($cartArray as $data) {
                if (isset($data)) {
                    $qty = $data->QtyBarang;
                    if ($data->StokPurchasing > 0) {
                        $queryHPP = $db->get_results("SELECT * FROM tb_stok_purchasing WHERE IDBarang='" . $data->IDBarang . "' AND IDProyek='" . $get_data->IDProyek . "' AND SisaStok>0");
                        if ($queryHPP) {
                            foreach ($queryHPP as $dataHPP) {
                                if ($qty > $dataHPP->SisaStok) {
                                    $qty = $qty - $dataHPP->SisaStok;
                                    $qtySimpan = $dataHPP->SisaStok;
                                    $stokHPP = 0;
                                    $exit = 0;
                                } else {
                                    $qtySimpan = $qty;
                                    $exit = 1;
                                    $stokHPP = $dataHPP->SisaStok - $qty;
                                }

                                $harga = $dataHPP->Harga;
                                $sub_total = $qtySimpan * $dataHPP->Harga;
                                $grandTotal += $sub_total;

                                $db->query("INSERT INTO tb_pengiriman_detail SET NoPengiriman='$nopengiriman', NoUrut='" . $data->NoUrut . "', IDBarang='" . $data->IDBarang . "', NamaBarang='" . $data->NamaBarang . "', Qty='" . $qtySimpan . "', SN='" . $data->SNBarang . "', Harga='$harga', SubTotal='$sub_total', IDStok='" . $dataHPP->IDStokPurchasing . "', StokFrom='1'");

                                $db->query("UPDATE tb_stok_purchasing SET SisaStok='$stokHPP' WHERE IDStokPurchasing='" . $dataHPP->IDStokPurchasing . "'");
                                $db->query("UPDATE tb_barang SET StokPurchasing=(StokPurchasing-$qtySimpan) WHERE IDBarang='" . $data->IDBarang . "'");

                                if ($exit == 1) {
                                    $qty = 0;
                                    break;
                                }
                            }
                        }
                    }

                    if ($qty > 0) {
                        $queryHPP = $db->get_results("SELECT * FROM tb_stok_gudang WHERE IDBarang='" . $data->IDBarang . "'  AND SisaStok>0");
                        if ($queryHPP) {
                            foreach ($queryHPP as $dataHPP) {
                                if ($qty > $dataHPP->SisaStok) {
                                    $qty = $qty - $dataHPP->SisaStok;
                                    $qtySimpan = $dataHPP->SisaStok;
                                    $stokHPP = 0;
                                    $exit = 0;
                                } else {
                                    $qtySimpan = $qty;
                                    $exit = 1;
                                    $stokHPP = $dataHPP->Stok - $qty;
                                }

                                $harga = $dataHPP->Harga;
                                $sub_total = $qtySimpan * $dataHPP->Harga;
                                $grandTotal += $sub_total;

                                $db->query("INSERT INTO tb_pengiriman_detail SET NoPengiriman='$nopengiriman', NoUrut='" . $data->NoUrut . "', IDBarang='" . $data->IDBarang . "', NamaBarang='" . $data->NamaBarang . "', Qty='" . $qtySimpan . "', SN='" . $data->SNBarang . "', Harga='$harga', SubTotal='$sub_total', IDStok='" . $dataHPP->IDStokGudang . "', StokFrom='0'");
                                $db->query("UPDATE tb_stok_gudang SET SisaStok='$stokHPP' WHERE IDStokGudang='" . $dataHPP->IDStokGudang . "'");
                                $db->query("UPDATE tb_barang SET StokGudang=(StokGudang-$qtySimpan) WHERE IDBarang='" . $data->IDBarang . "'");

                                if ($exit == 1) {
                                    $qty = 0;
                                    break;
                                }
                            }
                        }
                    }
                }
            }
            $db->query("UPDATE tb_pengiriman SET GrandTotal='$grandTotal' WHERE NoPengiriman='$nopengiriman'");
            $db->query("UPDATE tb_proyek SET PengeluaranMaterial=(PengeluaranMaterial+$grandTotal) WHERE IDProyek='" . $get_data->IDProyek . "'");
        } else {
            echo "0";
        }
        break;

    case "Delete":
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
        $query = $db->get_row("SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_pengiriman WHERE IDPengiriman='$id' ORDER BY IDPengiriman ASC");
        if ($query) {
            $po = $db->get_row("SELECT * FROM tb_po WHERE NoPO='NoPO'");
            $proyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='" . $query->IDProyek . "'");
            $user = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $query->CreatedBy . "'");
            $supplier = $db->get_var("SELECT NamaPerusahaan FROM tb_supplier WHERE IDSupplier='" . $po->IDSupplier . "'");
            $recievedBy = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $query->RecievedBy . "'");
            $proyek = $proyek->KodeProyek . " - " . $proyek->NamaProyek;

            if ($query->Keterangan != "") $keterangan = $query->Keterangan;
            else $keterangan = "-";

            $master = array("NoPengiriman" => $query->NoPengiriman, "NoPO" => $query->NoPO, "Proyek" => $proyek, "Supplier" => $supplier, "Tanggal" => $query->TanggalID, "usrlogin" => $user, "Total" => $query->Total, "GrandTotal" => $query->GrandTotal, "Keterangan" => $query->Keterangan, "RecievedBy" => $recievedBy, "DateTimeRecieved" => $query->DateTimeRecieved, "Status" => $query->Status);

            $query = $db->get_results("SELECT * FROM tb_pengiriman_detail WHERE NoPengiriman='" . $query->NoPengiriman . "' ORDER BY NoUrut ASC");
            if ($query) {
                $i = 0;
                foreach ($query as $data) {
                    $i++;
                    array_push($detail, array("NamaBarang" => $data->NamaBarang, "No" => $i, "Qty" => $data->Qty, "SN" => $data->SN, "Harga" => $data->Harga, "SubTotal" => $data->SubTotal));
                }
            }
        }
        echo json_encode(array("master" => $master, "detail" => $detail));
        break;
    default:
        echo "";
}
