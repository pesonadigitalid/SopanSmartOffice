<?php
include_once "../config/connection.php";

$act = antiSQLInjection($_GET['act']);
switch ($act) {
    case "DataList":
        $pelangganArray = array();
        $penjualanArray = array();
        $kategoriSpbArray = array();
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

        if ($pelanggan != "") {
            $cond .= " AND a.IDPelanggan='$pelanggan'";
        }

        if ($sales != "") {
            $cond .= " AND (a.CreatedBy='$sales' OR a.IDSales='$sales')";
        }

        if ($filterstatus == "Lunas") {
            $cond .= " AND a.Sisa<='0'";
        } else if ($filterstatus == "Hutang") {
            $cond .= " AND a.Sisa>'0'";
        }

        // if($_SESSION["IDJabatan"]=='25')
        //     $cond .= " AND a.CreatedBy='25'";
        if ($_SESSION["IDJabatan"] == '9' || $_SESSION["IDJabatan2"] == '9') {
            $cond .= " AND (a.CreatedBy='" . $_SESSION["uid"] . "' OR a.IDSales='" . $_SESSION["uid"] . "')";
            $cond2 .= " AND (CreatedBy='" . $_SESSION["uid"] . "' OR IDSales='" . $_SESSION["uid"] . "')";
        }

        $query = $db->get_results("SELECT a.*, b.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_penjualan a, tb_pelanggan b WHERE a.IDPelanggan=b.IDPelanggan AND a.Tipe='1' $cond ORDER BY IDPenjualan ASC");
        if ($query) {
            $i = 0;
            foreach ($query as $data) {
                $i++;

                // $totalVO = $db->get_var("SELECT SUM(GrandTotal) FROM tb_penjualan_vo WHERE IDPenjualan='$data->IDPenjualan'");
                // if (!$totalVO) $totalVO = 0;
                $totalVO = 0;
                $grandTotal = $data->GrandTotal + $totalVO;

                $totalTerbayar = $db->get_var("SELECT SUM(GrandTotal-Sisa) FROM tb_penjualan_invoice WHERE IDPenjualan='$data->IDPenjualan'");
                if (!$totalTerbayar) $totalTerbayar = 0;
                $sisa = $grandTotal - $totalTerbayar;

                $penjualanFile = array();
                $qFileCategory = $db->get_results("SELECT * FROM tb_penjualan_file_category ORDER BY IDPenjualanFileCategory ASC");
                if ($qFileCategory) {
                    foreach ($qFileCategory as $dFileCategory) {
                        $totalFile = $db->get_var("SELECT COUNT(*) FROM tb_penjualan_file WHERE IDPenjualanFileCategory='$dFileCategory->IDPenjualanFileCategory' AND IDPenjualan='$data->IDPenjualan'");
                        if (!$totalFile) $totalFile = 0;

                        array_push($penjualanFile, intval($totalFile));
                    }
                }

                $sales = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='$data->IDSales'");
                if (!$sales) $sales = "-";

                $isComplete = $data->IsComplete;
                if ($isComplete == "0") {
                    $do = $db->get_row("SELECT * FROM tb_penjualan_surat_jalan WHERE NoPenjualan='$data->NoPenjualan' AND DeletedDate IS NULL");
                    $isComplete = ($do) ? "1" : "0";
                }

                array_push($penjualanArray, array("IDPenjualan" => $data->IDPenjualan, "NoPenjualan" => $data->NoPenjualan, "NoPOKonsumen" => $data->NoPOKonsumen, "IDPelanggan" => $data->IDPelanggan, "Pelanggan" => $data->NamaPelanggan, "Tanggal" => $data->TanggalID, "TotalItem" => $data->TotalItem, "Total" => $data->Total, "Diskon" => $data->Diskon, "DiskonPersen" => $data->DiskonPersen, "Total2" => $data->Total2, "PPN" => $data->PPN, "PPNPersen" => $data->PPNPersen, "GrandTotal" => $grandTotal, "Status" => $data->Status, "Keterangan" => $data->Keterangan, "TotalPembayaran" => $totalTerbayar, "Sisa" => $sisa, "IsComplete" => $isComplete, "No" => $i, "penjualanFile" => $penjualanFile, "Sales" => $sales));
            }
        }

        $query = $db->get_results("SELECT * FROM tb_pelanggan WHERE Status='1' ORDER BY NamaPelanggan ASC");
        if ($query) {
            foreach ($query as $data) {
                array_push($pelangganArray, array("IDPelanggan" => $data->IDPelanggan, "KodePelanggan" => $data->KodePelanggan, "NamaPelanggan" => $data->NamaPelanggan));
            }
        }

        //GRAB ALL TOTAL DATA
        $all = $db->get_var("SELECT COUNT(*) FROM tb_penjualan WHERE NoPenjualan IS NOT NULL $cond2");
        if (!$all) {
            $all = '';
        }

        $lunas = $db->get_var("SELECT COUNT(*) FROM tb_penjualan WHERE Sisa='0' $cond2");
        if (!$lunas) {
            $lunas = '';
        }

        $hutang = $db->get_var("SELECT COUNT(*) FROM tb_penjualan WHERE Sisa>0 $cond2");
        if (!$hutang) {
            $hutang = '';
        }

        $query = $db->get_results("SELECT * FROM tb_penjualan_file_category ORDER BY IDPenjualanFileCategory ASC");
        if ($query) {
            $return = array();
            $i = 0;
            foreach ($query as $data) {
                $i++;
                $data->No = $i;
                $data->Status = ($data->Status == "1") ? true : false;

                array_push($kategoriSpbArray, $data);
            }
        }

        $sales = $db->get_results("SELECT * FROM tb_karyawan WHERE (IDJabatan='9' OR IDJabatan2='9') AND Status='1' ORDER BY Nama");
        if (!$sales) $sales = array();

        $return = array("penjualan" => $penjualanArray, "pelanggan" => $pelangganArray, "kategori_spb" => $kategoriSpbArray, "all" => $all, "lunas" => $lunas, "hutang" => $hutang, "sales" => $sales);
        echo json_encode($return);
        break;

    case "LoadAllRequirement":
        $pelangganArray = array();
        $barangArray = array();
        $spharray = array();

        $query = $db->get_results("SELECT a.*, b.NamaDepartement FROM tb_pelanggan a, tb_departement b WHERE a.Kategori=b.IDDepartement AND a.Status='1' ORDER BY NamaPelanggan ASC");
        if ($query) {
            foreach ($query as $data) {
                array_push($pelangganArray, array("IDPelanggan" => $data->IDPelanggan, "KodePelanggan" => $data->KodePelanggan, "NamaPelanggan" => $data->NamaPelanggan, "NamaDepartement" => $data->NamaDepartement));
            }
        }

        $query = $db->get_results("SELECT * FROM tb_barang ORDER BY Nama");
        if ($query) {
            $i = 0;
            foreach ($query as $data) {
                $i++;
                array_push($barangArray, array("IDBarang" => $data->IDBarang, "KodeBarang" => $data->KodeBarang, "Nama" => $data->Nama, "No" => $i, "Harga" => $data->Harga, "HargaJual" => $data->HargaJual, "IsSerialize" => "0", "Limit" => "1000000", "HPP" => $data->Harga, "HPPReal" => 0));
            }
        }

        $condSPH = "";
        if ($_SESSION["IDJabatan"] == '9' || $_SESSION["IDJabatan2"] == '9') {
            $condSPH .= " AND (CreatedBy='" . $_SESSION["uid"] . "' OR IDSales='" . $_SESSION["uid"] . "')";
        }

        $query = $db->get_results("SELECT * FROM tb_sph WHERE NoSPH NOT IN (SELECT NoSPH FROM tb_penjualan WHERE NoSPH IS NOT NULL AND NoSPH<>'0' AND DeletedDate IS NULL) AND DeletedDate IS NULL $condSPH ORDER BY NoSPH ASC");
        if ($query) {
            foreach ($query as $data) {
                array_push($spharray, array("IDSPH" => $data->IDSPH, "NoSPH" => $data->NoSPH));
            }
        }

        $sales = $db->get_results("SELECT * FROM tb_karyawan WHERE (IDJabatan='9' OR IDJabatan2='9') AND Status='1' ORDER BY Nama");
        if (!$sales) $sales = array();

        $return = array("barang" => $barangArray, "pelanggan" => $pelangganArray, "sph" => $spharray, "sales" => $sales);
        echo json_encode($return);
        break;

    case "InsertNew":
        $no_po_konsumen = antiSQLInjection($_POST['no_po_konsumen']);
        $tanggal = antiSQLInjection($_POST['tanggal']);
        $exp = explode("/", $tanggal);
        $tanggal = $exp[2] . "-" . $exp[1] . "-" . $exp[0];
        $tanggalCond = $exp[2] . "-" . $exp[1];
        $tanggalCond3 = $exp[2];
        $tanggalCond2 = $exp[2] . "/" . $exp[1] . "/";

        $kategori = antiSQLInjection($_POST['kategori']);
        $pelanggan = antiSQLInjection($_POST['pelanggan']);
        $sales = antiSQLInjection($_POST['sales']);
        $sales = antiSQLInjection($_POST['sales']);
        $jenis = antiSQLInjection($_POST['jenis']);
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
        $metode_pembayaran = antiSQLInjection($_POST['metode_pembayaran']);
        $metode_pembayaran2 = antiSQLInjection($_POST['metode_pembayaran2']);
        $kembali = antiSQLInjection($_POST['kembali']);

        $totalHPP = antiSQLInjection($_POST['totalHPP']);
        $totalHPPReal = antiSQLInjection($_POST['totalHPPReal']);
        $totalMargin = antiSQLInjection($_POST['totalMargin']);
        $sph = antiSQLInjection($_POST['sph']);

        $prihal = antiSQLInjection($_POST['prihal']);
        $term_condition = antiSQLInjection($_POST['term_condition']);
        $included = antiSQLInjection($_POST['included']);
        $tanggal_pemasangan = antiSQLInjection($_POST['tanggal_pemasangan']);
        $kondisi_pembayaran = antiSQLInjection($_POST['kondisi_pembayaran']);
        $ongkos_kirim = antiSQLInjection($_POST['ongkos_kirim']);

        $pem_dp = antiSQLInjection($_POST['pem_dp']);
        $pem_termin1 = antiSQLInjection($_POST['pem_termin1']);
        $pem_termin2 = antiSQLInjection($_POST['pem_termin2']);
        $pem_termin3 = antiSQLInjection($_POST['pem_termin3']);
        $pem_pelunasan = antiSQLInjection($_POST['pem_pelunasan']);

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

        $dataLast = $db->get_row("SELECT * FROM tb_penjualan WHERE DATE_FORMAT(Tanggal,'%Y')='" . $tanggalCond3 . "' AND CreatedBy='" . $createdBy . "' ORDER BY IDPenjualan DESC");
        if ($dataLast) {
            $last = intval(substr($dataLast->NoPenjualan, -4));
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

            $notransaksi = "SPB/" . strtoupper($usrnm) . "/" . $tanggalCond2 . $last;
            $checkNoTransaksi = $db->get_row("SELECT * FROM tb_penjualan WHERE NoPenjualan='$notransaksi'");
        } while ($checkNoTransaksi);

        $lanjut = true;

        //CEK BARANG APAKAH MASIH ADA STOK ATAU TIDAK
        /*foreach($cartArray as $data){
        if(isset($data)){
        if($data->SNBarang!=""){
        $barang = $db->get_row("SELECT * FROM tb_stok_gudang WHERE IDBarang='".$data->IDBarang."' AND SN='".$data->SNBarang."'");
        if(!$barang || $barang->SisaStok<=0){
        $barang = $db->get_row("SELECT * FROM tb_barang WHERE IDBarang='".$data->IDBarang."'");
        $message = "Penjualan tidak dapat dilakukan karena Stok. ".$barang->Nama." tidak mencukupi atau Serial Number Barang tersebut tidak terdaftar.";
        $lanjut=false;
        }
        } else {
        $barang = $db->get_row("SELECT * FROM tb_barang WHERE IDBarang='".$data->IDBarang."'");
        if($barang->StokGudang<$data->QtyBarang){
        $message = "Penjualan tidak dapat dilakukan karena Stok. ".$barang->Nama." tidak mencukupi. Stok Gudang = ".$barang->StokGudang."; Stok Penjualan = ".$data->QtyBarang;
        $lanjut=false;
        }
        }
        }
        }*/

        if ($lanjut) {
            $query = $db->query("INSERT INTO tb_penjualan SET NoPenjualan='$notransaksi', NoPOKonsumen='$no_po_konsumen', NoSPH='$sph', IDPelanggan='$pelanggan', IDSales='$sales', Tanggal='$tanggal', Kategori='$kategori', TotalItem='$total_item', Total='$total', Diskon='$diskon', DiskonPersen='$diskon_persen', Total2='$total2', PPN='$ppn', PPNPersen='$ppn_persen', GrandTotal='$grand_total', TotalPembayaran='$pembayarandp', Kembali='$kembali', Sisa='$sisa', Keterangan='$keterangan', TotalHPP='$totalHPP', TotalHPPReal='$totalHPPReal', TotalMargin='$totalMargin', CreatedBy='" . $_SESSION["uid"] . "', Prihal='$prihal', TermAndCondition='$term_condition', Included='$included', TanggalPemasangan='$tanggal_pemasangan', KondisiPembayaran='$kondisi_pembayaran', OngkosKirim='$ongkos_kirim', DP='$pem_dp', TerminI='$pem_termin1', TerminII='$pem_termin2', TerminIII='$pem_termin3', TerminIV='$pem_pelunasan', Jenis='$jenis'");

            if ($query) {
                echo json_encode(array("res" => 1, "mes" => "Data SPB berhasil disimpan!"));
                foreach ($cartArray as $data) {
                    if (isset($data)) {
                        $QtyBarang = str_replace(",", "", $data->QtyBarang);
                        $Harga = str_replace(",", "", $data->Harga);
                        $HargaDiskon = str_replace(",", "", $data->HargaDiskon);
                        $SubTotal = str_replace(",", "", $data->SubTotal);
                        $HPP = str_replace(",", "", $data->HPP);
                        $Margin = str_replace(",", "", $data->Margin);

                        $db->query("INSERT INTO tb_penjualan_detail SET NoPenjualan='$notransaksi', NoUrut='" . $data->NoUrut . "', IDBarang='" . $data->IDBarang . "', NamaBarang='" . $data->NamaBarang . "', NamaBarangDisplay='" . $data->NamaBarangDisplay . "', Qty='" . $QtyBarang . "', SN='" . $data->SNBarang . "', Harga='" . $Harga . "', HargaDiskon='" . $HargaDiskon . "', SubTotal='" . $SubTotal . "', HargaBeli='" . $HPP . "', HargaBeliReal='" . $data->HPPReal . "', Margin='" . $Margin . "', IsParent='" . $data->isParent . "', IsChild='" . $data->isChild . "', Diskon='" . $data->Diskon . "'");
                        $db->query("UPDATE tb_barang SET HargaJual='" . $Harga . "' WHERE IDBarang='" . $data->IDBarang . "'");
                        /*$qty = $data->QtyBarang;
                    if($data->SNBarang!=""){
                    $queryHPP = $db->get_row("SELECT * FROM tb_stok_gudang WHERE IDBarang='".$data->IDBarang."'  AND SisaStok>0 AND SN='".$data->SNBarang."'");
                    if($queryHPP){
                    $qtySimpan = 1;
                    $stokHPP = 0;
                    $hargaBeli = $queryHPP->Harga;
                    $hargaJual = $data->Harga;
                    $margin = ($hargaJual-$hargaBeli)*$qtySimpan;
                    $subTotal = $hargaJual*$qtySimpan;

                    $marginBarang = $hargaJual-$hargaBeli;

                    $db->query("INSERT INTO tb_penjualan_detail SET NoPenjualan='$notransaksi', NoUrut='".$data->NoUrut."', IDBarang='".$data->IDBarang."', NamaBarang='".$data->NamaBarang."', Qty='".$qtySimpan."', SN='".$data->SNBarang."', Harga='".$hargaJual."', SubTotal='".$subTotal."', HargaBeli='".$hargaBeli."', Margin='".$margin."'");
                    $db->query("UPDATE tb_stok_gudang SET SisaStok='$stokHPP' WHERE IDStokGudang='".$queryHPP->IDStokGudang."'");
                    $db->query("UPDATE tb_barang SET StokGudang=(StokGudang-$qtySimpan), HargaJual='$hargaJual', Margin='$marginBarang' WHERE IDBarang='".$data->IDBarang."'");
                    }
                    } else {
                    $queryHPP = $db->get_results("SELECT * FROM tb_stok_gudang WHERE IDBarang='".$data->IDBarang."'  AND SisaStok>0");
                    if($queryHPP){
                    foreach($queryHPP as $dataHPP){
                    if($qty>$dataHPP->SisaStok){
                    $qty = $qty-$dataHPP->SisaStok;
                    $qtySimpan = $dataHPP->SisaStok;
                    $stokHPP = 0;
                    $exit = 0;
                    } else {
                    $qtySimpan = $qty;
                    $exit = 1;
                    $stokHPP = $dataHPP->SisaStok-$qty;
                    }

                    $hargaBeli = $dataHPP->Harga;
                    $hargaJual = $data->Harga;
                    $margin = ($hargaJual-$hargaBeli)*$qtySimpan;
                    $subTotal = $hargaJual*$qtySimpan;

                    $marginBarang = $hargaJual-$hargaBeli;

                    $db->query("INSERT INTO tb_penjualan_detail SET NoPenjualan='$notransaksi', NoUrut='".$data->NoUrut."', IDBarang='".$data->IDBarang."', NamaBarang='".$data->NamaBarang."', Qty='".$qtySimpan."', SN='', Harga='".$hargaJual."', SubTotal='".$subTotal."', HargaBeli='".$hargaBeli."', Margin='".$margin."'");
                    $db->query("UPDATE tb_stok_gudang SET SisaStok='$stokHPP' WHERE IDStokGudang='".$dataHPP->IDStokGudang."'");

                    $db->query("UPDATE tb_barang SET StokGudang=(StokGudang-$qtySimpan), HargaJual='$hargaJual', Margin='$marginBarang' WHERE IDBarang='".$data->IDBarang."'");

                    if($exit==1){
                    $qty = 0;
                    break;
                    }
                    }
                    }
                    }*/
                    }
                }
            } else {
                echo json_encode(array("res" => 0, "mes" => "Data SPB gagal disimpan. Silahkan coba kembali nanti."));
            }
        } else {
            echo json_encode(array("res" => 0, "mes" => $message));
        }
        break;

    case "Delete":
        $idr = antiSQLInjection($_POST['idr']);
        $remark = antiSQLInjection($_POST['remark']);

        $allow = 1;
        $dataPenjualan = $db->get_row("SELECT * FROM tb_penjualan WHERE IDPenjualan='$idr'");
        $cek = $db->get_row("SELECT * FROM tb_penjualan_surat_jalan WHERE IDPenjualan='$idr' AND DeletedDate IS NULL");
        $cek2 = $db->get_row("SELECT * FROM tb_penjualan_invoice WHERE IDPenjualan='$idr'");
        $cek3 = $db->get_row("SELECT * FROM tb_penjualan_vo WHERE IDPenjualan='$idr' AND DeletedDate IS NULL");

        if ($cek || $cek2 || $cek3) {
            $allow = 0;
        }

        if ($allow == 0) {
            echo "2";
        } else {
            $query = $db->query("UPDATE tb_penjualan SET IsComplete='2', DeletedRemark='$remark', DeletedDate=NOW(), DeletedBy='" . $_SESSION['uid'] . "' WHERE IDPenjualan='$idr'");
            if ($query) {
                //RECALCULATE STOK
                // $query = $db->get_results("SELECT * FROM tb_penjualan_detail WHERE NoPenjualan='".$dataPenjualan->NoPenjualan."'");
                // if($query){
                //     foreach($query as $data){
                //         $db->query("INSERT INTO tb_stok_gudang SET IDReturn='".$idr."' AND IDBarang='".$data->IDBarang."', Stok='".$data->Qty."', SisaStok='".$data->Qty."', SN='".$data->SN."', Harga='".$data->HargaBeli."'");

                //         $stokGudang = $db->get_var("SELECT SUM(SisaStok) FROM tb_stok_gudang WHERE IDBarang='".$data->IDBarang."'");

                //         $db->query("UPDATE tb_barang SET StokGudang='$stokGudang' WHERE IDBarang='".$data->IDBarang."'");
                //     }
                // }
                //                $db->query("DELETE FROM tb_penjualan_detail WHERE NoPenjualan='" . $dataPenjualan->NoPenjualan . "'");
                echo "1";
            } else {
                echo "0";
            }
        }
        break;

    case "Detail":
        $id = antiSQLInjection($_GET['id']);
        $skipVO = antiSQLInjection($_GET['skipVO']);
        $NoUrut = 0;
        $NoSPH = "";
        $master = array();
        $detail = array();
        $detailHistory = array();
        $data = $db->get_row("SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID, DATE_FORMAT(DeletedDate, '%d/%m/%Y %H:%i:%s') AS DeletedDateID FROM tb_penjualan WHERE IDPenjualan='$id' ORDER BY IDPenjualan ASC");
        if ($data) {
            $NoSPH = $data->NoSPH;
            $pelanggan = $db->get_row("SELECT * FROM tb_pelanggan WHERE IDPelanggan='" . $data->IDPelanggan . "'");
            $pelanggan = $pelanggan->KodePelanggan . " - " . $pelanggan->NamaPelanggan;
            $invoiced = $db->get_row("SELECT * FROM tb_penjualan_invoice WHERE IDPenjualan='$id'");
            if ($invoiced) {
                $locked = true;
            } else {
                $locked = false;
            }

            $locked = false;
            $IDPenjualan = $data->IDPenjualan;

            if ($skipVO != "1") {
                // $TotalItemVO = $db->get_var("SELECT SUM(TotalItem) FROM tb_penjualan_vo WHERE IDPenjualan='$IDPenjualan'");
                // if (!$TotalItemVO)
                $TotalItemVO = 0;

                // $TotalVO = $db->get_var("SELECT SUM(Total) FROM tb_penjualan_vo WHERE IDPenjualan='$IDPenjualan'");
                // if (!$TotalVO)
                $TotalVO = 0;

                // $Total2VO = $db->get_var("SELECT SUM(Total2) FROM tb_penjualan_vo WHERE IDPenjualan='$IDPenjualan'");
                // if (!$Total2VO)
                $Total2VO = 0;

                // $GrandTotalVO = $db->get_var("SELECT SUM(GrandTotal) FROM tb_penjualan_vo WHERE IDPenjualan='$IDPenjualan'");
                // if (!$GrandTotalVO)
                $GrandTotalVO = 0;

                // $PPNVO = $db->get_var("SELECT SUM(PPN) FROM tb_penjualan_vo WHERE IDPenjualan='$IDPenjualan'");
                // if (!$PPNVO)
                $PPNVO = 0;

                // $DiskonVO = $db->get_var("SELECT SUM(Diskon) FROM tb_penjualan_vo WHERE IDPenjualan='$IDPenjualan'");
                // if (!$DiskonVO)
                $DiskonVO = 0;

                // $TotalHPPVO = $db->get_var("SELECT SUM(TotalHPP) FROM tb_penjualan_vo WHERE IDPenjualan='$IDPenjualan'");
                // if (!$TotalHPPVO)
                $TotalHPPVO = 0;

                // $TotalMarginVO = $db->get_var("SELECT SUM(TotalMargin) FROM tb_penjualan_vo WHERE IDPenjualan='$IDPenjualan'");
                // if (!$TotalMarginVO)
                $TotalMarginVO = 0;
            }
            $deletedBy = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $data->DeletedBy . "'");

            $sales = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $data->IDSales . "'");

            $canEdit = true;

            if ($canEdit) {
                $canEdit = !($db->get_row("SELECT * FROM tb_penjualan_surat_jalan WHERE IDPenjualan='$IDPenjualan' AND DeletedDate IS NULL"));
            }

            $master = array("IDPenjualan" => $data->IDPenjualan, "NoPenjualan" => $data->NoPenjualan, "NoPOKonsumen" => $data->NoPOKonsumen, "IDSales" => $data->IDSales, "Sales" => $sales, "IDPelanggan" => $data->IDPelanggan, "Pelanggan" => $pelanggan, "Tanggal" => $data->TanggalID, "Kategori" => $data->Kategori, "TotalItem" => ($data->TotalItem + $TotalItemVO), "Total" => ($data->Total + $TotalVO), "Diskon" => ($data->Diskon + $DiskonVO), "DiskonPersen" => $data->DiskonPersen, "Total2" => ($data->Total2 + $Total2VO), "PPN" => ($data->PPN + $PPNVO), "PPNPersen" => $data->PPNPersen, "GrandTotal" => ($data->GrandTotal + $GrandTotalVO), "Status" => $data->Status, "Keterangan" => $data->Keterangan, "TotalPembayaran" => $data->TotalPembayaran, "Sisa" => $data->Sisa, "Kembali" => $data->Kembali, "MetodePembayaran1" => $data->MetodePembayaran1, "MetodePembayaran2" => $data->MetodePembayaran2, "TotalHPP" => ($data->TotalHPP + $GrandTotalVO), "TotalMargin" => ($data->TotalMargin + $TotalMarginVO), "NoSPH" => $data->NoSPH, "No" => $i, "Prihal" => $data->Prihal, "TermAndCondition" => $data->TermAndCondition, "Included" => $data->Included, "TanggalPemasangan" => $data->TanggalPemasangan, "KondisiPembayaran" => $data->KondisiPembayaran, "OngkosKirim" => $data->OngkosKirim, "pem_dp" => $data->DP, "pem_termin1" => $data->TerminI, "pem_termin2" => $data->TerminII, "pem_termin3" => $data->TerminIII, "pem_pelunasan" => $data->TerminIV, "Jenis" => $data->Jenis, "locked_top" => $locked, "deleted_date" => $data->DeletedDateID, "deleted_by" => $deletedBy, "deleted_remark" => $data->DeletedRemark, "is_complete" => $data->IsComplete, "can_edit" => $canEdit);

            $space = "&nbsp;&nbsp;&nbsp;&nbsp;";
            $query = $db->get_results("SELECT * FROM tb_penjualan_detail WHERE NoPenjualan='" . $data->NoPenjualan . "' ORDER BY NoUrut ASC");
            if ($query) {
                $i = 0;
                foreach ($query as $data) {
                    $i++;
                    $NoUrut++;
                    $cek = $db->get_row("SELECT * FROM tb_barang_child WHERE IDParent='" . $data->IDBarang . "'");
                    if ($cek) {
                        $isParent = 1;
                    } else {
                        $isParent = 0;
                    }

                    $qty = ($data->QtyOriginal != null) ? $data->QtyOriginal : $data->Qty;
                    $margin = ($qty == 0) ? 0 : ($data->HargaDiskon - $data->HargaBeli) * $qty;

                    if ($data->Qty > 0) {
                        array_push($detail, array("IDBarang" => $data->IDBarang, "NamaBarang" => $data->NamaBarang, "No" => $i, "Qty" => $data->Qty, "SN" => $data->SN, "HPP" => $data->HargaBeli, "HPPReal" => $data->HargaBeliReal, "Harga" => $data->Harga, "Margin" => $data->Margin, "HargaDiskon" => $data->HargaDiskon, "SubTotal" => $data->SubTotal, "isParent" => $isParent, "Diskon" => $data->Diskon, "HargaDiskon" => floatval($data->HargaDiskon)));

                        $qPaket = $db->get_results("SELECT a.*, b.IsSerialize, b.KodeBarang, b.Nama, b.LibCode FROM tb_barang_child a, tb_barang b WHERE a.IDBarang=b.IDBarang AND a.IDParent='" . $data->IDBarang . "'");
                        if ($qPaket) {
                            foreach ($qPaket as $dPaket) {
                                $i++;
                                $namaBarang = $space . "* " . $dPaket->Nama;
                                array_push($detail, array("IDBarang" => $dPaket->IDBarang, "NamaBarang" => $namaBarang, "No" => $i, "Qty" => $data->Qty, "SN" => $data->SN, "HPP" => 0, "HPPReal" => 0, "Harga" => 0, "Margin" => 0, "HargaDiskon" => 0, "SubTotal" => 0, "isParent" => 0, "Diskon" => "0%", "HargaDiskon" => 0));
                            }
                        }
                    }

                    array_push($detailHistory, array("IDBarang" => $data->IDBarang, "NamaBarang" => $data->NamaBarang, "No" => $i, "Qty" =>  $qty, "SN" => $data->SN, "HPP" => $data->HargaBeli, "HPPReal" => $data->HargaBeliReal, "Harga" => $data->Harga, "Margin" => $margin, "SubTotal" =>  $qty * $data->Harga, "isParent" => $isParent));
                }
            }

            if ($skipVO != "1") {
                $qVO = $db->get_results("SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_penjualan_vo WHERE IDPenjualan='$IDPenjualan' AND Status='1' AND DeletedDate IS NULL ORDER BY NoVO");
                if ($qVO) {
                    foreach ($qVO as $dVO) {
                        array_push($detailHistory, array("header" => "VO: $dVO->NoVO; Tanggal: $dVO->TanggalID; Keterangan: $dVO->Keterangan"));

                        $query = $db->get_results("SELECT * FROM tb_penjualan_vo_detail WHERE NoVO='" . $dVO->NoVO . "' ORDER BY NoUrut ASC");
                        $i = 0;
                        foreach ($query as $data) {
                            $i++;
                            $NoUrut++;
                            $cek = $db->get_row("SELECT * FROM tb_barang_child WHERE IDParent='" . $data->IDBarang . "'");
                            if ($cek) {
                                $isParent = 1;
                            } else {
                                $isParent = 0;
                            }

                            array_push($detailHistory, array("IDBarang" => $data->IDBarang, "NamaBarang" => $data->NamaBarang, "No" => $i, "Qty" => $data->Qty, "SN" => $data->SN, "HPP" => $data->HargaBeli, "HPPReal" => $data->HargaBeliReal, "Harga" => $data->Harga, "Margin" => $data->Margin, "SubTotal" => $data->SubTotal, "isParent" => $isParent));
                        }
                    }
                }
            }
        }

        $i = 0;
        $detailCart = array();
        $query = $db->get_results("SELECT * FROM tb_penjualan_detail WHERE NoPenjualan='" . $data->NoPenjualan . "' ORDER BY NoUrut ASC");
        if ($query) {
            foreach ($query as $data) {
                $i++;
                $dataBr = $db->get_row("SELECT * FROM tb_barang WHERE IDBarang='" . $data->IDBarang . "'");
                if ($dataBr->IsSerialize == "1") {
                    $limit = 1;
                } else {
                    $limit = 100000;
                }

                // $cek = $db->get_row("SELECT * FROM tb_barang_child WHERE IDParent='".$data->IDBarang."'");
                // if($cek) $isParent=1; else $isParent=0;
                array_push($detailCart, array("IDBarang" => $data->IDBarang, "NamaBarang" => $data->NamaBarang, "NamaBarangDisplay" => $data->NamaBarangDisplay, "Harga" => $data->Harga, "HargaDiskon" => $data->HargaDiskon, "No" => $i, "NoUrut" => ($i - 1), "Qty" => $data->Qty, "QtyBarang" => floatval($data->Qty), "MaxQtyBarang" => floatval($data->Qty), "SubTotal" => $data->SubTotal, "SNBarang" => $data->SN, "IsSerialize" => $dataBr->IsSerialize, "Limit" => $limit, "HPP" => $data->HargaBeli, "HPPReal" => $data->HargaBeliReal, "Margin" => $data->Margin, "SubTotal" => $data->SubTotal, "Diskon" => $data->Diskon, "HargaDiskon" => floatval($data->HargaDiskon), "isParent" => intval($data->IsParent)));
            }
        }

        $dataPengiriman = array();
        $query = $db->get_results("SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_penjualan_surat_jalan WHERE IDPenjualan='$id' AND DeletedDate IS NULL ORDER BY Tanggal ASC");
        if ($query) {
            $i = 0;
            foreach ($query as $data) {
                $i++;
                $by = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $data->CreatedBy . "'");
                array_push($dataPengiriman, array("IDSuratJalan" => $data->IDSuratJalan, "NoSuratJalan" => $data->NoSuratJalan, "Tanggal" => $data->TanggalID, "No" => $i, "GrandTotal" => $data->GrandTotal, "UserName" => $by));
            }
        }

        $dataSupplier = "";
        $query = $db->get_results("SELECT a.*, b.* FROM tb_po a, tb_supplier b WHERE a.IDSupplier=b.IDSupplier AND a.IDPenjualan='$id'");
        if ($query) {
            foreach ($query as $data) {
                $dataSupplier .= "- " . $data->NamaPerusahaan . "\r\n";
            }
        }

        $spharray = array();
        $query = $db->get_results("SELECT * FROM tb_sph WHERE NoSPH='$NoSPH' ORDER BY NoSPH ASC");
        if ($query) {
            foreach ($query as $data) {
                array_push($spharray, array("IDSPH" => $data->IDSPH, "NoSPH" => $data->NoSPH));
            }
        }

        $notifikasiMaintenance = $db->get_results("SELECT *, DATE_FORMAT(DateCreated,'%d/%m/%Y') AS Tanggal , DATE_FORMAT(TanggalAkhirMaintenance,'%d/%m/%Y') AS TanggalAkhirMaintenance FROM tb_notifikasi_service WHERE IDPenjualan='$id' ORDER BY DateCreated ASC");
        if (!$notifikasiMaintenance) $notifikasiMaintenance = array();

        echo json_encode(array("master" => $master, "detail" => $detail, "detailHistory" => $detailHistory, "detailCart" => $detailCart, "pengiriman" => $dataPengiriman, "nourut" => ($NoUrut), "dataSupplier" => $dataSupplier, "sph" => $spharray, "notifikasiMaintenance" => $notifikasiMaintenance));
        break;

    case "LoadSPHDetail":
        $nosph = antiSQLInjection($_GET['nosph']);
        $sph = $db->get_row("SELECT * FROM tb_sph WHERE NoSPH='$nosph'");

        $DetailSPHArray = array();
        $query = $db->get_results("SELECT a.*, b.StokGudang, b.IsSerialize, b.Harga AS HargaBeliBarang FROM tb_sph_detail a, tb_barang b WHERE a.IDBarang=b.IDBarang AND NoSPH='" . $nosph . "' ORDER BY NoUrut ASC");
        if ($query) {
            $i = 0;
            foreach ($query as $data) {
                $cek = $db->get_results("SELECT * FROM tb_barang_child WHERE IDParent='" . $data->IDBarang . "'");
                if (!$cek) {
                    $hpp = $data->HargaBeliBarang;
                    $hppReal = 0;
                    $isParent = 0;
                } else {
                    $hpp = 0;
                    $hppReal = 0;
                    $isParent = 1;
                }

                $margin = ($data->Harga - $hpp) * $data->Qty;

                array_push($DetailSPHArray, array("IDDetail" => $data->IDDetail, "No" => $i, "noUrut" => $i, "IDBarang" => $data->IDBarang, "NamaBarang" => $data->NamaBarang, "NamaBarangDisplay" => $data->NamaBarang, "Qty" => $data->Qty, "Harga" => floatval($data->Harga), "HPP" => floatval($hpp), "HPPReal" => $hppReal, "Margin" => intval($margin), "SubTotal" => $data->SubTotal, "Limit" => $data->StokGudang, "IsSerialize" => $data->IsSerialize, "isParent" => $isParent, "isChild" => 0, "Diskon" => $data->Diskon, "HargaDiskon" => floatval($data->HargaDiskon)));
                $i++;

                // if ($cek) {
                //     foreach ($cek as $dChild) {
                //         $space = "&nbsp;&nbsp;&nbsp;";

                //         $cekSubChild = $db->get_results("SELECT * FROM tb_barang_child WHERE IDParent='" . $dChild->IDBarang . "'");

                //         $dbarang = $db->get_row("SELECT * FROM tb_barang WHERE IDBarang='" . $dChild->IDBarang . "'");
                //         if ($dbarang) {
                //             if (!$cekSubChild) {
                //                 $hpp = $dbarang->Harga;
                //                 $hppReal = 0;
                //                 $isParent = 0;
                //             } else {
                //                 $hpp = 0;
                //                 $hppReal = 0;
                //                 $isParent = 1;
                //             }

                //             $margin = (0 - $hpp) * ($dChild->Qty * $data->Qty);

                //             array_push($DetailSPHArray, array("IDDetail" => $dbarang->IDDetail, "No" => $i, "noUrut" => $i, "IDBarang" => $dbarang->IDBarang, "NamaBarang" => $space . " * " . $dbarang->Nama, "NamaBarangDisplay" => $dbarang->Nama, "Qty" => ($dChild->Qty * $data->Qty), "Harga" => 0, "HPP" => intval($hpp), "HPPReal" => $hppReal, "Margin" => $margin, "SubTotal" => 0, "Limit" => 0, "IsSerialize" => $dbarang->IsSerialize, "IsSerialize" => $dbarang->IsSerialize, "isParent" => $isParent, "isChild" => 0, "Diskon" => 0, "HargaDiskon" => 0));
                //             $i++;
                //         }
                //         $masterBarang = $dbarang;
                //         if ($cekSubChild) {
                //             foreach ($cekSubChild as $dChild) {
                //                 $dbarang = $db->get_row("SELECT * FROM tb_barang WHERE IDBarang='" . $dChild->IDBarang . "'");
                //                 if ($dbarang) {
                //                     $hpp = $dbarang->Harga;
                //                     $margin = (0 - $hpp) * ($dChild->Qty * $data->Qty);
                //                     array_push($DetailSPHArray, array("IDDetail" => $dbarang->IDDetail, "No" => $i, "noUrut" => $i, "IDBarang" => $dbarang->IDBarang, "NamaBarang" => $space . $space . " * " . $dbarang->Nama, "NamaBarangDisplay" => $masterBarang->Nama . " - " . $dbarang->Nama, "Qty" => ($dChild->Qty * $data->Qty), "Harga" => 0, "HPP" => floatval($hpp), "HPPReal" => 0, "Margin" => $margin, "SubTotal" => 0, "Limit" => 0, "IsSerialize" => $dbarang->IsSerialize, "isParent" => 0, "isChild" => 1, "Diskon" => 0, "HargaDiskon" => 0));
                //                     $i++;
                //                 }
                //             }
                //         }
                //     }
                // }
            }
        }

        echo json_encode(array("detail" => $DetailSPHArray, "pelanggan" => $sph->IDPelanggan, "sales" => $sph->IDSales, "DiskonPersen" => $sph->DiskonPersen, "PPNPersen" => $sph->PPNPersen));
        break;

    case "RekapPO":
        $pelangganArray = array();
        $penjualanArray = array();
        $pelanggan = antiSQLInjection($_GET['pelanggan']);
        $filterstatus = antiSQLInjection($_GET['filterstatus']);

        $datestart = antiSQLInjection($_GET['datestart']);
        $expstart = explode("/", $datestart);
        $datestartchange = $expstart[2] . "-" . $expstart[1] . "-" . $expstart[0];

        $dateend = antiSQLInjection($_GET['dateend']);
        $expend = explode("/", $dateend);
        $dateendchange = $expend[2] . "-" . $expend[1] . "-" . $expend[0];

        if ($datestart != "" && $dateend != "") {
            $cond = "AND a.Tanggal BETWEEN '$datestartchange' AND '$dateendchange'";
        } else if ($datestart != "") {
            $cond = "AND a.Tanggal='$datestartchange'";
        } else {
            $cond = "AND DATE_FORMAT(a.Tanggal,'%Y-%m') = '" . date("Y-m") . "'";
        }

        if ($pelanggan != "") {
            $cond .= " AND a.IDPelanggan='$pelanggan'";
        }

        if ($filterstatus == "Lunas") {
            $cond .= " AND a.Sisa<='0'";
        } else if ($filterstatus == "Hutang") {
            $cond .= " AND a.Sisa>'0'";
        }

        if ($_SESSION["IDJabatan"] == '25') {
            $cond .= " AND a.CreatedBy='25'";
        }

        $query = $db->get_results("SELECT a.*, b.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_penjualan a, tb_pelanggan b WHERE a.IDPelanggan=b.IDPelanggan AND a.Tipe='1' $cond ORDER BY IDPenjualan ASC");
        if ($query) {
            $i = 0;
            foreach ($query as $data) {
                $i++;
                array_push($penjualanArray, array("IDPenjualan" => $data->IDPenjualan, "NoPenjualan" => $data->NoPenjualan, "IDPelanggan" => $data->IDPelanggan, "Pelanggan" => $data->NamaPelanggan, "Tanggal" => $data->TanggalID, "TotalItem" => $data->TotalItem, "Total" => $data->Total, "Diskon" => $data->Diskon, "DiskonPersen" => $data->DiskonPersen, "Total2" => $data->Total2, "PPN" => $data->PPN, "PPNPersen" => $data->PPNPersen, "GrandTotal" => $data->GrandTotal, "Status" => $data->Status, "Keterangan" => $data->Keterangan, "TotalPembayaran" => $data->TotalPembayaran, "Sisa" => $data->Sisa, "IsComplete" => $data->IsComplete, "No" => $i));
            }
        }

        $query = $db->get_results("SELECT * FROM tb_pelanggan WHERE Status='1' ORDER BY NamaPelanggan ASC");
        if ($query) {
            foreach ($query as $data) {
                array_push($pelangganArray, array("IDPelanggan" => $data->IDPelanggan, "KodePelanggan" => $data->KodePelanggan, "NamaPelanggan" => $data->NamaPelanggan));
            }
        }

        //GRAB ALL TOTAL DATA
        $all = $db->get_var("SELECT COUNT(*) FROM tb_penjualan");
        if (!$all) {
            $all = '';
        }

        $lunas = $db->get_var("SELECT COUNT(*) FROM tb_penjualan WHERE Sisa='0'");
        if (!$lunas) {
            $lunas = '';
        }

        $hutang = $db->get_var("SELECT COUNT(*) FROM tb_penjualan WHERE Sisa>0");
        if (!$hutang) {
            $hutang = '';
        }

        $return = array("penjualan" => $penjualanArray, "pelanggan" => $pelangganArray, "all" => $all, "lunas" => $lunas, "hutang" => $hutang);
        echo json_encode($return);
        break;

    case "Edit":
        $id = antiSQLInjection($_POST['id']);
        $no_penjualan = antiSQLInjection($_POST['no_penjualan']);
        $no_po_konsumen = antiSQLInjection($_POST['no_po_konsumen']);
        $tanggal = antiSQLInjection($_POST['tanggal']);
        $exp = explode("/", $tanggal);
        $tanggal = $exp[2] . "-" . $exp[1] . "-" . $exp[0];
        $tanggalCond = $exp[2] . "-" . $exp[1];
        $tanggalCond2 = $exp[2] . "/" . $exp[1] . "/";

        $kategori = antiSQLInjection($_POST['kategori']);
        $pelanggan = antiSQLInjection($_POST['pelanggan']);
        $jenis = antiSQLInjection($_POST['jenis']);
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
        $metode_pembayaran = antiSQLInjection($_POST['metode_pembayaran']);
        $metode_pembayaran2 = antiSQLInjection($_POST['metode_pembayaran2']);
        $kembali = antiSQLInjection($_POST['kembali']);

        $totalHPP = antiSQLInjection($_POST['totalHPP']);
        $totalHPPReal = antiSQLInjection($_POST['totalHPPReal']);
        $totalMargin = antiSQLInjection($_POST['totalMargin']);
        $sph = antiSQLInjection($_POST['sph']);

        $prihal = antiSQLInjection($_POST['prihal']);
        $term_condition = antiSQLInjection($_POST['term_condition']);
        $included = antiSQLInjection($_POST['included']);
        $tanggal_pemasangan = antiSQLInjection($_POST['tanggal_pemasangan']);
        $kondisi_pembayaran = antiSQLInjection($_POST['kondisi_pembayaran']);
        $ongkos_kirim = antiSQLInjection($_POST['ongkos_kirim']);

        $pem_dp = antiSQLInjection($_POST['pem_dp']);
        $pem_termin1 = antiSQLInjection($_POST['pem_termin1']);
        $pem_termin2 = antiSQLInjection($_POST['pem_termin2']);
        $pem_termin3 = antiSQLInjection($_POST['pem_termin3']);
        $pem_pelunasan = antiSQLInjection($_POST['pem_pelunasan']);

        $is_complete = antiSQLInjection($_POST['is_complete']);
        if ($is_complete == "") $is_complete = '0';

        $cartArray = antiSQLInjection($_POST['cart']);
        $cartArray = json_decode($cartArray);

        $lanjut = true;

        //CEK GRAND TOTAL VS TOTAL INVOICE
        $totalInv = $db->get_var("SELECT SUM(GrandTotal) FROM tb_penjualan_invoice WHERE IDPenjualan='$id'");
        if (!$totalInv) {
            $totalInv = 0;
        }

        if ($grand_total < $totalInv) {
            $lanjut = false;
            $message = "Data SPB tidak dapat disimpan karena Invoice telah dikeluarkan dengan total nilai Rp." . number_format($totalInv, 2);
        }

        $idBarangHasChangedHarga = array();
        if ($lanjut) {
            // CEK QTY SUDAH DIKIRIM ATAU BELUM + CEK KALAU SUDAH DIBUATKAN INVOICE, JANGAN KASI UPDATE HARGA
            $qCek = $db->get_results("SELECT * FROM tb_penjualan_detail WHERE NoPenjualan='$no_penjualan'");
            if ($qCek) {
                foreach ($qCek as $dCek) {
                    $ada = false;
                    $qtyBarang = 0;
                    $newHarga = 0;
                    foreach ($cartArray as $cart) {
                        if (isset($cart)) {
                            if ($cart->IDBarang == $dCek->IDBarang) {
                                $qtyBarang = $cart->QtyBarang;
                                $newHarga = $cart->Harga;
                                $ada = true;
                            }
                        }
                    }

                    if (!$ada) {
                        $suratJalan = $db->get_row("SELECT a.*, b.IDPenjualan FROM tb_penjualan_surat_jalan_detail a, tb_penjualan_surat_jalan b WHERE a.`NoSuratJalan`=b.`NoSuratJalan` AND b.`IDPenjualan`='$id' AND a.`IDBarang`='" . $dCek->IDBarang . "'");
                        if ($suratJalan) {
                            $lanjut = false;
                            $message = "Data SPB tidak dapat disimpan karena " . $dCek->NamaBarang . " telah dikirimkan pada " . $suratJalan->NoSuratJalan;
                        }
                    } else {
                        $stokTerkirim = $db->get_var("SELECT SUM(Qty) FROM tb_penjualan_surat_jalan_detail WHERE NoSuratJalan IN (SELECT NoSuratJalan FROM tb_penjualan_surat_jalan WHERE IDPenjualan='$id') AND IDBarang='" . $dCek->IDBarang . "'");

                        // $qtyVO = $db->get_var("SELECT SUM(Qty) FROM tb_penjualan_vo_detail WHERE NoVO IN (SELECT NoVO FROM tb_penjualan_vo WHERE IDPenjualan='$id') AND IDBarang='" . $dCek->IDBarang . "'");
                        $qtyVO = 0;

                        if ($stokTerkirim > ($qtyBarang + $qtyVO)) {
                            $lanjut = false;
                            $message = "Data SPB tidak dapat disimpan karena " . $dCek->NamaBarang . " telah dikirimkan melebihi jumlah QTY SPB dan VO.";
                        }

                        if ($newHarga > 0 && $newHarga != $dCek->Harga) {
                            $cek = $db->get_row("SELECT * FROM tb_penjualan_invoice_detail WHERE IDBarang='$dCek->IDBarang' AND NoInvoice IN (SELECT NoInvoice FROM tb_penjualan_invoice WHERE IDPenjualan='$id')");
                            if ($cek) {
                                $lanjut = false;
                                $message = "Data SPB tidak dapat disimpan karena " . $dCek->NamaBarang . " telah dibuatkan invoice. Anda tidak dapat mengubah Harga barang untuk barang yang telah diterbitkan invoicenya.";
                            }
                        }
                    }
                }
            }
        }

        if ($lanjut) {
            $query = $db->query("UPDATE tb_penjualan SET NoPOKonsumen='$no_po_konsumen', NoSPH='$sph', IDPelanggan='$pelanggan', Tanggal='$tanggal', Kategori='$kategori', TotalItem='$total_item', Total='$total', Diskon='$diskon', DiskonPersen='$diskon_persen', Total2='$total2', PPN='$ppn', PPNPersen='$ppn_persen', GrandTotal='$grand_total', TotalPembayaran='$pembayarandp', Kembali='$kembali', Sisa='$sisa', Keterangan='$keterangan', TotalHPP='$totalHPP', TotalHPPReal='$totalHPPReal', TotalMargin='$totalMargin', Prihal='$prihal', TermAndCondition='$term_condition', Included='$included', TanggalPemasangan='$tanggal_pemasangan', KondisiPembayaran='$kondisi_pembayaran', OngkosKirim='$ongkos_kirim', IsComplete='0', DP='$pem_dp', TerminI='$pem_termin1', TerminII='$pem_termin2', TerminIII='$pem_termin3', TerminIV='$pem_pelunasan', Jenis='$jenis', IsComplete='$is_complete', ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW() WHERE IDPenjualan='$id'");

            echo json_encode(array("res" => 1, "mes" => "Data SPB berhasil disimpan!"));
            // $db->query("DELETE FROM tb_penjualan_detail WHERE NoPenjualan='$no_penjualan'");
            foreach ($cartArray as $data) {
                if (isset($data)) {
                    // We dont expect delete on this.
                    // $db->query("INSERT INTO tb_penjualan_detail SET NoPenjualan='$no_penjualan', NoUrut='" . $data->NoUrut . "', IDBarang='" . $data->IDBarang . "', NamaBarang='" . $data->NamaBarang . "', NamaBarangDisplay='" . $data->NamaBarangDisplay . "', Qty='" . $data->QtyBarang . "', SN='" . $data->SNBarang . "', Harga='" . $data->Harga . "', SubTotal='" . $data->SubTotal . "', HargaBeli='" . $data->HPP . "', HargaBeliReal='" . $data->HPPReal . "', Margin='" . $data->Margin . "', IsParent='" . $data->isParent . "', IsChild='" . $data->isChild . "'");

                    $QtyBarang = str_replace(",", "", $data->QtyBarang);
                    $Harga = str_replace(",", "", $data->Harga);
                    $HargaDiskon = str_replace(",", "", $data->HargaDiskon);
                    $SubTotal = str_replace(",", "", $data->SubTotal);
                    $HPP = str_replace(",", "", $data->HPP);
                    $Margin = str_replace(",", "", $data->Margin);

                    $db->query("UPDATE tb_penjualan_detail SET NoUrut='" . $data->NoUrut . "', IDBarang='" . $data->IDBarang . "', NamaBarang='" . $data->NamaBarang . "', NamaBarangDisplay='" . $data->NamaBarangDisplay . "', Qty='" . $QtyBarang . "', SN='" . $data->SNBarang . "', Harga='" . $Harga . "', HargaDiskon='" . $HargaDiskon . "', SubTotal='" . $SubTotal . "', HargaBeli='" . $HPP . "', HargaBeliReal='" . $data->HPPReal . "', Margin='" . $Margin . "', IsParent='" . $data->isParent . "', IsChild='" . $data->isChild . "', Diskon='" . $data->Diskon . "' WHERE NoPenjualan='$no_penjualan' AND IDBarang='" . $data->IDBarang . "'");

                    $db->query("UPDATE tb_barang SET HargaJual='" . $data->Harga . "' WHERE IDBarang='" . $data->IDBarang . "'");

                    // Update harga di Surat Jalan
                    $qsjDetail = $db->get_results("SELECT a.* FROM tb_penjualan_surat_jalan_detail a, tb_penjualan_surat_jalan b WHERE a.NoSuratJalan=b.NoSuratJalan AND b.IDPenjualan='$id' AND a.IDBarang='" . $data->IDBarang . "'");
                    if ($qsjDetail) {
                        foreach ($qsjDetail as $sjDetail) {
                            $Harga =  str_replace(",", "", $data->Harga);
                            $Diskon =  str_replace(",", "", $data->Diskon);
                            $HargaDiskon = str_replace(",", "", $data->HargaDiskon);
                            $Margin = $HargaDiskon - $sjDetail->HPP;
                            $SubTotal = $sjDetail->Qty * $HargaDiskon;
                            $SubTotalMargin = $sjDetail->Qty * $Margin;

                            $db->query("UPDATE tb_penjualan_surat_jalan_detail SET Harga='$Harga', Diskon='$Diskon', HargaDiskon='$HargaDiskon', Margin='$Margin', SubTotalMargin='$SubTotalMargin', SubTotal='$SubTotal' WHERE IDetail='" . $sjDetail->IDetail . "'");

                            $Total = $db->get_var("SELECT SUM(SubTotal) FROM tb_penjualan_surat_jalan_detail WHERE NoSuratJalan='" . $sjDetail->NoSuratJalan . "'");

                            $PPN = round($Total * $ppn_persen / 100);

                            $GrandTotal = $Total + $PPN;

                            $TotalMargin = $db->get_var("SELECT SUM(SubTotalMargin) FROM tb_penjualan_surat_jalan_detail WHERE NoSuratJalan='" . $sjDetail->NoSuratJalan . "'");

                            $db->query("UPDATE tb_penjualan_surat_jalan SET TotalNilai='$Total', TotalNilai2='$Total', PPN='$PPN', PPNPersen='$ppn_persen', GrandTotal='$GrandTotal', TotalMargin='$TotalMargin' WHERE NoSuratJalan='" . $sjDetail->NoSuratJalan . "'");
                        }
                    }

                    // Update harga di VO
                    $qvoDetail = $db->get_results("SELECT a.* FROM tb_penjualan_vo_detail a, tb_penjualan_vo b WHERE a.NoVO=b.NoVO AND b.IDPenjualan='$id' AND a.IDBarang='" . $data->IDBarang . "'");
                    if ($qvoDetail) {
                        foreach ($qvoDetail as $voDetail) {
                            $Harga = $data->Harga;
                            $Diskon = $data->Diskon;
                            $HargaBeli = $voDetail->HargaBeli;
                            $Margin =  ($Harga - $Diskon) - $HargaBeli;
                            $SubTotal = $voDetail->Qty * ($Harga - $Diskon);
                            $SubTotalMargin = $voDetail->Qty * $Margin;

                            $db->query("UPDATE tb_penjualan_vo_detail SET Harga='$Harga', Diskon='$Diskon', HargaBeli='$HargaBeli', Margin='$SubTotalMargin', SubTotal='$SubTotal' WHERE IDDetail='" . $voDetail->IDDetail . "'");

                            $Total = $db->get_var("SELECT SUM(SubTotal) FROM tb_penjualan_vo_detail WHERE NoVO='" . $voDetail->NoVO . "'");
                            $PPN = $Total * $ppn_persen / 100;
                            $GrandTotal = $Total + $PPN;

                            $TotalHPP = $db->get_var("SELECT SUM(HargaBeli*Qty)) FROM tb_penjualan_vo_detail WHERE NoVO='" . $voDetail->NoVO . "'");

                            $TotalMargin = $db->get_var("SELECT SUM(Margin) FROM tb_penjualan_vo_detail WHERE NoVO='" . $voDetail->NoVO . "'");
                            $db->query("UPDATE tb_penjualan_vo SET TotalHPP='$TotalHPP', TotalMargin='$TotalMargin', Total='$Total', Total2='$Total', PPN='$PPN', PPNPersen='$ppn_persen', GrandTotal='$GrandTotal' WHERE NoVO='" . $voDetail->NoVO . "'");
                        }
                    }
                }
            }

            // Reset Grand Total Akhir VO
            $GrandTotalSPBAkhir = $grand_total;
            $qVO = $db->get_results("SELECT * FROM tb_penjualan_vo WHERE IDPenjualan='$id' ORDER BY IDPenjualanVO DESC");
            if ($qVO) {
                foreach ($qVO as $dVO) {
                    $db->query("UPDATE tb_penjualan_vo SET GrandTotalSPBAkhir='$GrandTotalSPBAkhir' WHERE IDPenjualanVO='$dVO->IDPenjualanVO'");
                    $GrandTotalSPBAkhir -= $dVO->GrandTotal;
                }
            }

            /*if($query){

        } else {
        echo json_encode(array("res"=>0,"mes"=>"Data SPB gagal disimpan. Silahkan coba kembali nanti."));
        }*/
        } else {
            echo json_encode(array("res" => 0, "mes" => $message));
        }
        break;

    case "SetComplete":
        $id = antiSQLInjection($_POST['id']);

        $no_po_konsumen = antiSQLInjection($_POST['noPOKonsumen']);
        $keterangan = antiSQLInjection($_POST['keterangan']);

        $prihal = antiSQLInjection($_POST['prihal']);
        $term_condition = antiSQLInjection($_POST['term_condition']);
        $included = antiSQLInjection($_POST['included']);
        $tanggal_pemasangan = antiSQLInjection($_POST['tanggal_pemasangan']);
        $kondisi_pembayaran = antiSQLInjection($_POST['kondisi_pembayaran']);

        $pem_dp = antiSQLInjection($_POST['pem_dp']);
        $pem_termin1 = antiSQLInjection($_POST['pem_termin1']);
        $pem_termin2 = antiSQLInjection($_POST['pem_termin2']);
        $pem_termin3 = antiSQLInjection($_POST['pem_termin3']);
        $pem_pelunasan = antiSQLInjection($_POST['pem_pelunasan']);

        $is_complete = antiSQLInjection($_POST['is_complete']);
        if ($is_complete == "") $is_complete = '0';

        $query = $db->query("UPDATE tb_penjualan SET NoPOKonsumen='$no_po_konsumen', Keterangan='$keterangan', Prihal='$prihal', TermAndCondition='$term_condition', Included='$included', TanggalPemasangan='$tanggal_pemasangan', KondisiPembayaran='$kondisi_pembayaran', DP='$pem_dp', TerminI='$pem_termin1', TerminII='$pem_termin2', TerminIII='$pem_termin3', TerminIV='$pem_pelunasan', IsComplete='$is_complete', ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW() WHERE IDPenjualan='$id'");

        echo json_encode(array(
            "res" => 1,
            "mes" => ($is_complete == '0')
                ? "SPB berhasil disimpan dan diset sebagai SPB Incomplete"
                : "SPB berhasil disimpan dan diset sebagai SPB Complete"
        ));

        break;

    default:
        echo "";
}
