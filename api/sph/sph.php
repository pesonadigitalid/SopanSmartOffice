<?php
include_once "../config/connection.php";

$act = antiSQLInjection($_GET['act']);
switch ($act) {
    case "DataList":
        $pelangganArray = array();
        $SPHArray = array();
        $pelanggan = antiSQLInjection($_GET['pelanggan']);
        $filterstatus = antiSQLInjection($_GET['filterstatus']);

        $datestart = antiSQLInjection($_GET['datestart']);
        $expstart = explode("/", $datestart);
        $datestartchange = $expstart[2] . "-" . $expstart[1] . "-" . $expstart[0];

        $dateend = antiSQLInjection($_GET['dateend']);
        $expend = explode("/", $dateend);
        $dateendchange = $expend[2] . "-" . $expend[1] . "-" . $expend[0];

        $sales = $_GET['sales'];

        if ($datestart != "" && $dateend != "") {
            $cond = "AND a.Tanggal BETWEEN '$datestartchange' AND '$dateendchange'";
            $cond2 = "AND Tanggal BETWEEN '$datestartchange' AND '$dateendchange'";
        } else if ($datestart != "") {
            $cond = "AND a.Tanggal='$datestartchange'";
            $cond2 = "AND Tanggal='$datestartchange'";
        } else {
            $cond = "AND DATE_FORMAT(a.Tanggal,'%Y-%m') = '" . date("Y-m") . "'";
            $cond2 = "AND DATE_FORMAT(Tanggal,'%Y-%m') = '" . date("Y-m") . "'";
        }

        if ($pelanggan != "")
            $cond .= " AND a.IDPelanggan='$pelanggan'";


        if ($sales != "") {
            $cond .= " AND (a.CreatedBy='$sales' OR a.IDSales='$sales')";
        }

        if ($filterstatus != "")
            $cond .= " AND a.Status='$filterstatus'";

        // if($_SESSION["IDJabatan"]=='25')
        //     $cond .= " AND a.CreatedBy='25'";
        if ($_SESSION["IDJabatan"] == '9' || $_SESSION["IDJabatan2"] == '9') {
            $cond .= " AND (a.CreatedBy='" . $_SESSION["uid"] . "' OR a.IDSales='" . $_SESSION["uid"] . "')";
            $cond2 .= " AND (CreatedBy='" . $_SESSION["uid"] . "' OR IDSales='" . $_SESSION["uid"] . "')";
        }

        $query = $db->get_results("SELECT a.*, b.*, a.Status AS StatusSPH, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_sph a, tb_pelanggan b WHERE a.IDPelanggan=b.IDPelanggan $cond ORDER BY IDSPH ASC");
        if ($query) {
            $i = 0;
            foreach ($query as $data) {
                $i++;

                $sales = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='$data->IDSales'");
                if (!$sales) $sales = "-";

                array_push($SPHArray, array("IDSPH" => $data->IDSPH, "NoSPH" => $data->NoSPH, "IDPelanggan" => $data->IDPelanggan, "Pelanggan" => $data->NamaPelanggan, "Tanggal" => $data->TanggalID, "TotalItem" => $data->TotalItem, "Total" => $data->Total, "Diskon" => $data->Diskon, "DiskonPersen" => $data->DiskonPersen, "Total2" => $data->Total2, "PPN" => $data->PPN, "PPNPersen" => $data->PPNPersen, "GrandTotal" => $data->GrandTotal, "Status" => $data->Status, "Keterangan" => $data->Keterangan, "TotalPembayaran" => $data->TotalPembayaran, "Sisa" => $data->Sisa, "No" => $i, "Status" => $data->StatusSPH, "Sales" => $sales));
            }
        }

        $query = $db->get_results("SELECT * FROM tb_pelanggan WHERE Status='1' ORDER BY NamaPelanggan ASC");
        if ($query) {
            foreach ($query as $data) {
                array_push($pelangganArray, array("IDPelanggan" => $data->IDPelanggan, "KodePelanggan" => $data->KodePelanggan, "NamaPelanggan" => $data->NamaPelanggan));
            }
        }

        $all = $db->get_var("SELECT COUNT(*) FROM tb_sph WHERE Status IS NOT NULL $cond2");
        if (!$all) $all = '';
        $approved = $db->get_var("SELECT COUNT(*) FROM tb_sph WHERE Status='1' $cond2");
        if (!$approved) $approved = '';
        $declined = $db->get_var("SELECT COUNT(*) FROM tb_sph WHERE Status='2' $cond2");
        if (!$declined) $declined = '';

        $sales = $db->get_results("SELECT * FROM tb_karyawan WHERE (IDJabatan='9' OR IDJabatan2='9') AND Status='1' ORDER BY Nama");
        if (!$sales) $sales = array();

        $return = array("sph" => $SPHArray, "sales" => $sales, "pelanggan" => $pelangganArray, "all" => $all, "approved" => $approved, "declined" => $declined);
        echo json_encode($return);
        break;

    case "LoadAllRequirement":
        $pelangganArray = array();
        $barangArray = array();

        $query = $db->get_results("SELECT * FROM tb_pelanggan WHERE Status='1' ORDER BY NamaPelanggan ASC");
        if ($query) {
            foreach ($query as $data) {
                array_push($pelangganArray, array("IDPelanggan" => $data->IDPelanggan, "KodePelanggan" => $data->KodePelanggan, "NamaPelanggan" => $data->NamaPelanggan));
            }
        }

        $query = $db->get_results("SELECT * FROM tb_barang ORDER BY Nama");
        if ($query) {
            $i = 0;
            foreach ($query as $data) {
                $i++;
                array_push($barangArray, array("IDBarang" => $data->IDBarang, "KodeBarang" => $data->KodeBarang, "Nama" => $data->Nama, "No" => $i, "Harga" => $data->Harga, "HargaJual" => $data->HargaJual, "IsSerialize" => "0", "Limit" => "1000000", "HPP" => 0));
            }
        }

        $sales = $db->get_results("SELECT * FROM tb_karyawan WHERE (IDJabatan='9' OR IDJabatan2='9') AND Status='1' ORDER BY Nama");
        if (!$sales) $sales = array();

        $return = array("barang" => $barangArray, "pelanggan" => $pelangganArray, "sales" => $sales);
        echo json_encode($return);
        break;

    case "InsertNew":
        $tanggal = antiSQLInjection($_POST['tanggal']);
        $exp = explode("/", $tanggal);
        $tanggal = $exp[2] . "-" . $exp[1] . "-" . $exp[0];
        $tanggalCond = $exp[2] . "-" . $exp[1];
        $tanggalCond3 = $exp[2];
        $tanggalCond2 = $exp[2] . "/" . $exp[1] . "/";

        $pelanggan = antiSQLInjection($_POST['pelanggan']);
        $total_item = antiSQLInjection($_POST['total_item']);
        $total = antiSQLInjection($_POST['total']);
        $diskon_persen = antiSQLInjection($_POST['diskon_persen']);
        $diskon = antiSQLInjection($_POST['diskon']);
        $total2 = antiSQLInjection($_POST['total2']);
        $ppn_persen = antiSQLInjection($_POST['ppn_persen']);
        $ppn = antiSQLInjection($_POST['ppn']);
        $grand_total = antiSQLInjection($_POST['grand_total']);
        $pembayarandp = antiSQLInjection($_POST['pembayarandp']);
        $sisa = antiSQLInjection($_POST['sisa']);
        $keterangan = antiSQLInjection($_POST['keterangan']);
        $kembali = antiSQLInjection($_POST['kembali']);

        $totalHPP = antiSQLInjection($_POST['totalHPP']);
        $totalHPPReal = antiSQLInjection($_POST['totalHPPReal']);
        $totalMargin = antiSQLInjection($_POST['totalMargin']);

        $sales = antiSQLInjection($_POST['sales']);

        $cartArray = antiSQLInjection($_POST['cart']);
        $cartArray = json_decode($cartArray);

        if ($_SESSION["IDJabatan"] == '9' || $_SESSION["IDJabatan2"] == '9') {
            $sales =  $_SESSION["uid"];
        }

        $usrnm = $db->get_var("SELECT Usernm FROM tb_karyawan WHERE IDKaryawan='$sales'");
        $createdBy = $sales;
        if (!$usrnm) {
            $usrnm = $_SESSION["Usernm"];
            $createdBy = $_SESSION["uid"];
        }

        $dataLast = $db->get_row("SELECT * FROM tb_sph WHERE DATE_FORMAT(Tanggal,'%Y')='" . $tanggalCond3 . "' AND CreatedBy='" . $createdBy . "' ORDER BY IDSPH DESC");
        if ($dataLast) $last = intval(substr($dataLast->NoSPH, -4));
        else $last = 0;
        do {
            $last++;
            if ($last < 1000 and $last >= 100)
                $last = "0" . $last;
            else if ($last < 100 and $last >= 10)
                $last = "00" . $last;
            else if ($last < 10)
                $last = "000" . $last;
            $notransaksi = "SPH/" . strtoupper($usrnm) . "/" . $tanggalCond2 . $last;
            $checkNoTransaksi = $db->get_row("SELECT * FROM tb_sph WHERE NoSPH='$notransaksi'");
        } while ($checkNoTransaksi);

        $lanjut = true;
        if ($lanjut) {
            $query = $db->query("INSERT INTO tb_sph SET NoSPH='$notransaksi', IDPelanggan='$pelanggan', IDSales='$sales', Tanggal='$tanggal', TotalItem='$total_item', Total='$total', Diskon='$diskon', DiskonPersen='$diskon_persen', Total2='$total2', PPN='$ppn', PPNPersen='$ppn_persen', GrandTotal='$grand_total', TotalPembayaran='$pembayarandp', Kembali='$kembali', Sisa='$sisa', Keterangan='$keterangan', TotalHPP='$totalHPP', TotalHPPReal='$totalHPPReal', TotalMargin='$totalMargin', CreatedBy='" . $_SESSION["uid"] . "'");

            if ($query) {
                echo json_encode(array("res" => 1, "mes" => "Data SPH barang berhasil disimpan!"));
                foreach ($cartArray as $data) {
                    if (isset($data)) {
                        $QtyBarang = str_replace(",", "", $data->QtyBarang);
                        $Harga = str_replace(",", "", $data->Harga);
                        $HargaDiskon = str_replace(",", "", $data->HargaDiskon);
                        $SubTotal = str_replace(",", "", $data->SubTotal);
                        $HPP = str_replace(",", "", $data->HPP);
                        $Margin = str_replace(",", "", $data->Margin);

                        $db->query("INSERT INTO tb_sph_detail SET NoSPH='$notransaksi', NoUrut='" . $data->NoUrut . "', IDBarang='" . $data->IDBarang . "', NamaBarang='" . $data->NamaBarang . "', Qty='" . $QtyBarang . "', SN='" . $data->SNBarang . "', Harga='" . $Harga . "', HargaDiskon='" . $HargaDiskon . "', SubTotal='" . $SubTotal . "', HargaBeli='" . $HPP . "', HargaBeliReal='" . $data->HPPReal . "', Margin='" . $Margin . "', Diskon='" . $data->Diskon . "'");

                        $db->query("UPDATE tb_barang SET HargaJual='" . $Harga . "' WHERE IDBarang='" . $data->IDBarang . "'");
                    }
                }
            } else {
                echo json_encode(array("res" => 0, "mes" => "Data SPH gagal disimpan. Silahkan coba kembali nanti."));
            }
        } else {
            echo json_encode(array("res" => 0, "mes" => "Data SPH gagal disimpan. Silahkan coba kembali nanti."));
        }
        break;

    case "Delete":
        $idr = antiSQLInjection($_POST['idr']);
        $remark = antiSQLInjection($_POST['remark']);

        $allow = 1;
        $dataPenjualan = $db->get_row("SELECT * FROM tb_sph WHERE IDSPH='$idr'");
        $cek = $db->get_row("SELECT * FROM tb_penjualan WHERE NoSPH='" . $dataPenjualan->NoSPH . "' AND DeletedDate IS NULL");
        if ($cek) $allow = 0;
        if ($allow == 0) {
            echo "2";
        } else {
            $query = $db->query("UPDATE tb_sph SET Status='2', DeletedRemark='$remark', DeletedDate=NOW(), DeletedBy='" . $_SESSION['uid'] . "' WHERE IDSPH='$idr'");
            if ($query) {
                // $db->query("DELETE FROM tb_sph_detail WHERE NoSPH='".$dataPenjualan->NoSPH."'");
                echo "1";
            } else {
                echo "0";
            }
        }
        break;

    case "UpdateStatus":
        $id = antiSQLInjection($_POST['id']);
        $status = antiSQLInjection($_POST['status']);

        $query = $db->get_row("UPDATE tb_sph SET Status='$status' WHERE IDSPH='$id'");
        if ($query) {
            echo "1";
        } else {
            echo "0";
        }
        break;

    case "Detail":
        $id = antiSQLInjection($_GET['id']);
        $NoUrut = 0;
        $master = array();
        $detail = array();
        $data = $db->get_row("SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID, DATE_FORMAT(DeletedDate, '%d/%m/%Y %H:%i:%s') AS DeletedDateID FROM tb_sph WHERE IDSPH='$id' ORDER BY IDSPH ASC");
        if ($data) {
            $pelanggan = $db->get_row("SELECT * FROM tb_pelanggan WHERE IDPelanggan='" . $data->IDPelanggan . "'");
            $pelanggan = $pelanggan->KodePelanggan . " - " . $pelanggan->NamaPelanggan;
            if ($data->Status == '0') $status = 'Baru';
            else if ($data->Status == '1') $status = 'Approved';
            else if ($data->Status == '2') $status = 'Removed';

            $sales = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='$data->IDSales'");
            if (!$sales) $sales = "-";

            $deletedBy = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $data->DeletedBy . "'");

            $master = array("IDSPH" => $data->IDSPH, "NoSPH" => $data->NoSPH, "IDPelanggan" => $data->IDPelanggan, "Pelanggan" => $pelanggan, "Tanggal" => $data->TanggalID, "TotalItem" => $data->TotalItem, "Total" => $data->Total, "Diskon" => $data->Diskon, "DiskonPersen" => $data->DiskonPersen, "Total2" => $data->Total2, "PPN" => $data->PPN, "PPNPersen" => $data->PPNPersen, "GrandTotal" => $data->GrandTotal, "Status" => $status, "Keterangan" => $data->Keterangan, "TotalPembayaran" => $data->TotalPembayaran, "Sisa" => $data->Sisa, "Kembali" => $data->Kembali, "TotalHPP" => $data->TotalHPP, "TotalHPPReal" => $data->TotalHPPReal, "TotalMargin" => $data->TotalMargin, "No" => $i, "deleted_date" => $data->DeletedDateID, "deleted_by" => $deletedBy, "deleted_remark" => $data->DeletedRemark, "sales" => $sales);

            $query = $db->get_results("SELECT * FROM tb_sph_detail WHERE NoSPH='" . $data->NoSPH . "' ORDER BY NoUrut ASC");
            if ($query) {
                $i = 0;
                foreach ($query as $data) {
                    $i++;
                    $NoUrut++;
                    array_push($detail, array("NamaBarang" => $data->NamaBarang, "No" => $i, "Qty" => $data->Qty, "SN" => $data->SN, "HPP" => $data->HargaBeli, "HPPReal" => $data->HargaBeliReal, "Harga" => $data->Harga, "HargaDiskon" => $data->HargaDiskon, "Margin" => $data->Margin, "SubTotal" => $data->SubTotal, "Diskon" => $data->Diskon));
                }
            }
        }

        $i = 0;
        $detailCart = array();
        $query = $db->get_results("SELECT * FROM tb_sph_detail WHERE NoSPH='" . $data->NoSPH . "' ORDER BY NoUrut ASC");
        if ($query) {
            foreach ($query as $data) {
                $i++;
                $dataBr = $db->get_row("SELECT * FROM tb_barang WHERE IDBarang='" . $data->IDBarang . "'");
                if ($dataBr->IsSerialize == "1") $limit = 1;
                else $limit = 100000;
                array_push($detailCart, array("IDBarang" => $data->IDBarang, "NamaBarang" => $data->NamaBarang, "Harga" => $data->Harga, "HargaDiskon" => $data->HargaDiskon, "No" => $i, "NoUrut" => ($i - 1), "Qty" => $data->Qty, "QtyBarang" => $data->Qty, "SubTotal" => $data->SubTotal, "SNBarang" => $data->SN, "IsSerialize" => $dataBr->IsSerialize, "Limit" => $limit, "HPP" => $data->HargaBeli, "HPPReal" => $data->HargaBeliReal, "Margin" => $data->Margin, "SubTotal" => $data->SubTotal, "Diskon" => $data->Diskon));
            }
        }

        echo json_encode(array("master" => $master, "detail" => $detail, "detailCart" => $detailCart, "nourut" => ($NoUrut)));
        break;

    case "Edit":
        $id = antiSQLInjection($_POST['id']);
        $no_sph = antiSQLInjection($_POST['no_sph']);
        $tanggal = antiSQLInjection($_POST['tanggal']);
        $exp = explode("/", $tanggal);
        $tanggal = $exp[2] . "-" . $exp[1] . "-" . $exp[0];
        $tanggalCond = $exp[2] . "-" . $exp[1];
        $tanggalCond2 = $exp[2] . "/" . $exp[1] . "/";

        $pelanggan = antiSQLInjection($_POST['pelanggan']);
        $total_item = antiSQLInjection($_POST['total_item']);
        $total = antiSQLInjection($_POST['total']);
        $diskon_persen = antiSQLInjection($_POST['diskon_persen']);
        $diskon = antiSQLInjection($_POST['diskon']);
        $total2 = antiSQLInjection($_POST['total2']);
        $ppn_persen = antiSQLInjection($_POST['ppn_persen']);
        $ppn = antiSQLInjection($_POST['ppn']);
        $grand_total = antiSQLInjection($_POST['grand_total']);
        $pembayarandp = antiSQLInjection($_POST['pembayarandp']);
        $sisa = antiSQLInjection($_POST['sisa']);
        $keterangan = antiSQLInjection($_POST['keterangan']);
        $kembali = antiSQLInjection($_POST['kembali']);

        $totalHPP = antiSQLInjection($_POST['totalHPP']);
        $totalHPPReal = antiSQLInjection($_POST['totalHPPReal']);
        $totalMargin = antiSQLInjection($_POST['totalMargin']);

        $cartArray = antiSQLInjection($_POST['cart']);
        $cartArray = json_decode($cartArray);

        $lanjut = true;
        if ($lanjut) {
            $query = $db->query("UPDATE tb_sph SET IDPelanggan='$pelanggan', Tanggal='$tanggal', TotalItem='$total_item', Total='$total', Diskon='$diskon', DiskonPersen='$diskon_persen', Total2='$total2', PPN='$ppn', PPNPersen='$ppn_persen', GrandTotal='$grand_total', TotalPembayaran='$pembayarandp', Kembali='$kembali', Sisa='$sisa', Keterangan='$keterangan', TotalHPP='$totalHPP', TotalHPPReal='$totalHPPReal', TotalMargin='$totalMargin' WHERE IDSPH='$id'");
            if ($query) {
                echo json_encode(array("res" => 1, "mes" => "Data SPH barang berhasil disimpan!"));
                $db->query("DELETE FROM tb_sph_detail WHERE NoSPH = '$no_sph'");
                foreach ($cartArray as $data) {
                    if (isset($data)) {
                        $QtyBarang = str_replace(",", "", $data->QtyBarang);
                        $Harga = str_replace(",", "", $data->Harga);
                        $HargaDiskon = str_replace(",", "", $data->HargaDiskon);
                        $SubTotal = str_replace(",", "", $data->SubTotal);
                        $HPP = str_replace(",", "", $data->HPP);
                        $Margin = str_replace(",", "", $data->Margin);

                        $db->query("INSERT INTO tb_sph_detail SET NoSPH='$no_sph', NoUrut='" . $data->NoUrut . "', IDBarang='" . $data->IDBarang . "', NamaBarang='" . $data->NamaBarang . "', Qty='" . $QtyBarang . "', SN='" . $data->SNBarang . "', Harga='" . $Harga . "', HargaDiskon='" . $HargaDiskon . "', SubTotal='" . $SubTotal . "', HargaBeli='" . $HPP . "', HargaBeliReal='" . $data->HPPReal . "', Margin='" . $Margin . "', Diskon='" . $data->Diskon . "'");

                        $db->query("UPDATE tb_barang SET HargaJual='" . $Harga . "' WHERE IDBarang='" . $data->IDBarang . "'");
                    }
                }
            } else {
                echo json_encode(array("res" => 0, "mes" => "Data SPH gagal disimpan. Silahkan coba kembali nanti."));
            }
        } else {
            echo json_encode(array("res" => 0, "mes" => "Data SPH gagal disimpan. Silahkan coba kembali nanti."));
        }
        break;

    default:
        echo "";
}
