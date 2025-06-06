<?php
include_once "../config/connection.php";
include_once "../library/class.stok.php";

$stok = new Stok($db);

$act = antiSQLInjection($_GET['act']);
switch ($act) {
    case "LoadAllRequirement":
        $id_gudang = antiSQLInjection($_GET['id_gudang']);
        $spb = antiSQLInjection($_GET['spb']);

        $stokTableName = ($spb > 0) ? "tb_stok_purchasing" : "tb_stok_gudang";
        $stokTableNameCondition = ($spb > 0) ? " AND IDPenjualan='$spb' " : "";

        $barang = array();
        if ($id_gudang != "") {
            $query = $db->get_results("SELECT a.*, b.NamaPerusahaan, c.Nama AS Jenis FROM tb_barang a, tb_supplier b, tb_jenis_material c WHERE a.IDSupplier=b.IDSupplier AND a.IDJenis=c.IDMaterial AND a.IsBarang='1' ORDER BY a.Nama ASC");
            if ($query) {
                $i = 0;
                foreach ($query as $data) {
                    $i++;

                    $stokGudang = $db->get_var("SELECT SUM(SisaStok) FROM $stokTableName WHERE IDGudang='$id_gudang' AND IDBarang='$data->IDBarang' $stokTableNameCondition");
                    if (!$stokGudang) $stokGudang = 0;

                    $hppStokGudang = $db->get_var("SELECT SUM(HPP*SisaStok)/SUM(SisaStok) FROM $stokTableName WHERE IDGudang='$id_gudang' AND IDBarang='$data->IDBarang' $stokTableNameCondition");
                    if (!$hppStokGudang) $hppStokGudang = 0;

                    $stokPurchasing = 0;
                    $isPaket = $db->get_results("SELECT a.*, b.* FROM tb_barang_child a, tb_barang b WHERE a.IDBarang=b.IDBarang AND a.IDParent='" . $data->IDBarang . "'");
                    if (!$isPaket) {
                        array_push($barang, array("IDBarang" => $data->IDBarang, "KodeBarang" => $data->KodeBarang, "Nama" => $data->Nama, "No" => $i, "Kategori" => "", "Supplier" => $data->NamaPerusahaan, "Harga" => round($data->Harga), "Jenis" => $data->Jenis, "StokGudang" => intval($stokGudang), "StokPurchasing" => $stokPurchasing, "IsSerialize" => $data->IsSerialize, "HPP" => $hppStokGudang, "HPP" => $hppStokGudang, "LibCode" => $data->LibCode));
                    }
                }
            }
        }

        $gudang = $db->get_results("SELECT * FROM tb_gudang ORDER BY Nama ASC");
        if (!$gudang) $gudang = array();

        $spb = array();
        $query = $db->get_results("SELECT a.*, b.NamaPelanggan FROM tb_penjualan a, tb_pelanggan b WHERE a.IDPelanggan=b.IDPelanggan ORDER BY NoPenjualan ASC");
        if ($query) {
            $return = array();
            foreach ($query as $data) {
                array_push($spb, array(
                    "IDPenjualan" => $data->IDPenjualan,
                    "NoPenjualan" => $data->NoPenjualan . " / " . $data->NamaPelanggan
                ));
            }
        }

        $return = array("barang" => $barang, "gudang" => $gudang, "spb" => $spb);
        echo json_encode($return);
        break;

    case "InsertNew":
        $tanggal = antiSQLInjection($_POST['tanggal']);
        $exp = explode("/", $tanggal);
        $tanggal = $exp[2] . "-" . $exp[1] . "-" . $exp[0];
        $tanggalCond = $exp[2] . "-" . $exp[1];
        $tanggalCond2 = "/SPN/" . $exp[2] . "/" . $exp[1] . "/";

        $spb = antiSQLInjection($_POST['spb']);
        $id_gudang_from = antiSQLInjection($_POST['id_gudang_from']);
        $id_gudang_to = antiSQLInjection($_POST['id_gudang_to']);
        $totalitem = antiSQLInjection($_POST['totalitem']);
        $totaljenisitem = antiSQLInjection($_POST['totaljenisitem']);
        $keterangan = antiSQLInjection($_POST['keterangan']);
        $cartArray = antiSQLInjection($_POST['cart']);
        $totalHPP = antiSQLInjection($_POST['totalHPP']);
        $cartArray = json_decode($cartArray);

        $dataLast = $db->get_row("SELECT * FROM tb_transfer_stok WHERE DATE_FORMAT(Tanggal,'%Y-%m')='" . $tanggalCond . "' ORDER BY NoTransferStok DESC");
        if ($dataLast) $last = intval(substr($dataLast->NoTransferStok, -3));
        else $last = 0;
        do {
            $last++;
            if ($last < 100 and $last >= 10)
                $last = "0" . $last;
            else if ($last < 10)
                $last = "00" . $last;
            $notransaksi = "TS" . $tanggalCond2 . $last;
            $checkNoTransaksi = $db->get_row("SELECT * FROM tb_transfer_stok WHERE NoTransferStok='$notransaksi'");
        } while ($checkNoTransaksi);

        $message = $stok->ValidateStokUsage($cartArray, $id_gudang_from, $spb, "Transfer Stok ");

        if ($message == "") {
            $query = $db->query("INSERT INTO tb_transfer_stok SET NoTransferStok='$notransaksi', IDPenjualan='$spb',IDGudangFrom='$id_gudang_from', IDGudangTo='$id_gudang_to', Tanggal='$tanggal', TotalQty='$totalitem', TotalJenisItem='$totaljenisitem', Keterangan='$keterangan', Status='1', CreatedBy='" . $_SESSION["uid"] . "', TotalHPP='$totalHPP'");
            if ($query) {
                $id = $db->get_var("SELECT LAST_INSERT_ID()");

                foreach ($cartArray as $data) {
                    if (isset($data)) {
                        $db->query("INSERT INTO tb_transfer_stok_detail SET NoTransferStok='$notransaksi', NoUrut='" . $data->NoUrut . "', IDBarang='" . $data->IDBarang . "', NamaBarang='" . $data->NamaBarang . "', Qty='" . $data->QtyBarang . "', SN='" . $data->SNBarang . "', HPP='" . $data->HPP . "', SubTotal='" . $data->SubTotal . "'");
                    }
                }

                $stok->TransferStokGudang($notransaksi);

                echo json_encode(array("res" => 1, "mes" => "Data transfer stok barang berhasil disimpan. Stok berhasil diupdate!"));
            } else {
                echo json_encode(array("res" => 0, "mes" => "Transfer stok tidak dapat disimpan. Silahkan coba kembali."));
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

        if ($datestart != "" && $dateend != "") {
            $cond = "WHERE Tanggal BETWEEN '$datestartchange' AND '$dateendchange'";
        } else if ($datestart != "") {
            $cond = "WHERE Tanggal='$datestartchange'";
        } else {
            $cond = "WHERE DATE_FORMAT(Tanggal,'%Y-%m') = '" . date("Y-m") . "'";
        }

        $dataTransfer = $db->get_results("SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM `tb_transfer_stok` $cond And (Status='1' OR Status='2') ORDER BY NoTransferStok ASC");
        if ($dataTransfer) {
            $i = 0;
            foreach ($dataTransfer as $data) {
                $i++;
                $created = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $data->CreatedBy . "'");
                $stokFrom = $db->get_var("SELECT Nama FROM tb_gudang WHERE IDGudang='" . $data->IDGudangFrom . "'");
                $stokTo = $db->get_var("SELECT Nama FROM tb_gudang WHERE IDGudang='" . $data->IDGudangTo . "'");

                $data->CreatedBy = $created;
                $data->GudangFrom = $stokFrom;
                $data->GudangTo = $stokTo;
                $data->No = $i;

                array_push($penerimaan, $data);
            }
        } else $dataTransfer = array();

        echo json_encode(array("data" => $dataTransfer));
        break;

    case "Delete":
        $idr = antiSQLInjection($_POST['idr']);
        $remark = antiSQLInjection($_POST['remark']);

        $dataTransferStok = $db->get_row("SELECT * FROM tb_transfer_stok WHERE IDTransferStok='$idr'");
        $nofaktur = $dataTransferStok->NoTransferStok;
        if ($stok->CheckAllowDeleteTransferStokGudang($nofaktur)) {
            $stok->DeleteTransferStokGudang($nofaktur);
            $db->query("UPDATE tb_transfer_stok SET Status='2', DeletedRemark='$remark', DeletedDate=NOW(), DeletedBy='" . $_SESSION['uid'] . "', ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW() WHERE IDTransferStok='$idr'");
            echo "1";
        } else {
            echo "2";
        }

        break;

    case "Detail":
        $id = antiSQLInjection($_GET['id']);
        $detail = array();
        $master = $db->get_row("SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID, DATE_FORMAT(DeletedDate, '%d/%m/%Y %H:%i:%s') AS DeletedDateID FROM tb_transfer_stok WHERE IDTransferStok='$id' ORDER BY IDTransferStok ASC");
        if ($master) {
            $created = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $master->CreatedBy . "'");
            $stokFrom = $db->get_var("SELECT Nama FROM tb_gudang WHERE IDGudang='" . $master->IDGudangFrom . "'");
            $stokTo = $db->get_var("SELECT Nama FROM tb_gudang WHERE IDGudang='" . $master->IDGudangTo . "'");
            $master->CreatedBy = $created;
            $master->GudangFrom = $stokFrom;
            $master->GudangTo = $stokTo;

            $deletedBy = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $master->DeletedBy . "'");
            $master->DeletedBy = $deletedBy;
            $master->DeletedRemark = $master->DeletedRemark;

            $detail = $db->get_results("SELECT *, SUM(Qty) AS TOTAL_QTY, SUM(SubTotal) AS SUB_TOTAL, (SUM(SubTotal)/SUM(Qty)) AS HPP FROM tb_transfer_stok_detail WHERE NoTransferStok='" . $master->NoTransferStok . "' GROUP BY NoUrut ORDER BY NoUrut ASC");
            if (!$detail) $detail = array();
        } else {
            $master = array();
        }
        echo json_encode(array("master" => $master, "detail" => $detail));
        break;
    default:
        echo "";
}
