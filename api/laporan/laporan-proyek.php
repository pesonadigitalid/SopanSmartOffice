<?php
include_once "../config/connection.php";

$act = $_GET['act'];
switch ($act) {

    case "LaporanLabaRugi":
        $id = $_GET['id'];
        $urut = $_GET['urut'];
        $tanggal = $_GET['tanggal'];
        if ($tanggal == "") $tanggal = date("Y-m-d");
        else {
            $exp = explode("/", $tanggal);
            $tanggal = $exp[2] . "-" . $exp[1] . "-" . $exp[0];
        }

        if ($tanggal != "") {
            $cond = " AND a.Tanggal<='$tanggal' ";
            $cond2 = " AND b.Tanggal<='$tanggal' ";
            $cond3 = " AND Tanggal<='$tanggal' ";
        }

        if ($urut == "1") $urut = " ORDER BY b.NamaPerusahaan ASC";
        else $urut = " ORDER BY a.NoPO ASC";

        $pendapatan1 = 0;
        $pendapatan2 = 0;
        $pendapatan3 = 0;

        $ppn10 = 0;
        $pph2 = 0;
        $dpp = 0;
        $totalPajak = 0;

        $aPendapatan = array();

        $ppnArray = array();
        $query = $db->get_results("SELECT DISTINCT(PPNPersen) FROM tb_proyek_invoice WHERE IDProyek='$id' AND PPNPersen>0 $cond3 ORDER BY IDProyek ASC");
        if ($query) {
            foreach ($query as $data) {
                $ppnArray[$data->PPNPersen] = 0;
            }
        }

        $query = $db->get_results("SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_proyek_invoice WHERE IDProyek='$id' $cond3 ORDER BY IDProyek ASC");
        if ($query) {
            foreach ($query as $data) {
                if (($data->Sisa > 0 && $data->Sisa < 1) || $data->Sisa < 0) $sisa = 0;
                else  $sisa = $data->Sisa;

                //Pajak
                $temp_dpp = 0;
                $temp_ppn = 0;
                $temp_pph = 0;
                if ($data->PPNPersen > 0) {
                    $temp_dpp = $data->Jumlah;
                    $temp_ppn = $data->PPN;
                    $temp_pph = round($temp_dpp * 0.02, 2);

                    $ppnArray[$data->PPNPersen] += $data->PPN;
                }

                $ppnLists = array();
                foreach ($ppnArray as $key => $value) {
                    array_push($ppnLists, array("key" => $key, "value" => $value));
                }

                array_push($aPendapatan, array("NoInv" => $data->NoInv, "Tanggal" => $data->TanggalID, "Jumlah" => $data->GrandTotal, "Bayar" => ($data->GrandTotal - $data->Sisa), "Sisa" => $sisa, "Keterangan" => $data->Keterangan, "DPP" => $temp_dpp, "PPN" => $temp_ppn, "PPH" => $temp_pph));
                $pendapatan1 += $data->GrandTotal;
                $pendapatan2 += ($data->GrandTotal - $data->Sisa);
                $pendapatan3 += $sisa;

                $dpp += $temp_dpp;
                $pph2 += $temp_pph;
                $ppn10 += $temp_ppn;
            }
        }

        $material1 = 0;
        $material2 = 0;
        $material3 = 0;

        $aMaterial = array();
        $query = $db->get_results("SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_po a, tb_supplier b WHERE a.IDProyek='$id' AND a.JenisPO='1' AND a.IsAccidental='0' AND a.IDSupplier=b.IDSupplier $cond $urut");
        // $query = $db->get_results("SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_po a, tb_supplier b WHERE a.IDProyek='$id' AND a.JenisPO='1' AND a.IDSupplier=b.IDSupplier ORDER BY b.NamaPerusahaan ASC");
        if ($query) {
            foreach ($query as $data) {
                $supplier = $db->get_row("SELECT * FROM tb_supplier WHERE IDSupplier='" . $data->IDSupplier . "'");
                if ($supplier) $supplier = $supplier->NamaPerusahaan;
                else $supplier = "-";
                if (($data->Sisa > 0 && $data->Sisa < 1) || $data->Sisa < 0) $sisa = 0;
                else  $sisa = $data->Sisa;
                array_push($aMaterial, array("NoInv" => $data->NoPO, "Tanggal" => $data->TanggalID, "Jumlah" => $data->GrandTotal, "Bayar" => $data->TotalPembayaran, "Sisa" => $sisa, "Supplier" => $supplier, "Tipe" => "PO"));
                $material1 += $data->GrandTotal;
                $material2 += $data->TotalPembayaran;
                $material3 += $sisa;
            }
        }

        $query = $db->get_results("SELECT a.*, b.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal a, tb_jurnal_detail b WHERE a.IDJurnal=b.IDJurnal AND b.IDRekening='45' AND b.Debet>0 AND a.IDProyek='$id' $cond AND a.NoRef=''");
        if ($query) {
            foreach ($query as $data) {
                if ($data->NoRef == '') {
                    $cek = $db->get_row("SELECT * FROM tb_jurnal_detail WHERE IDJurnal='" . $data->IDJurnal . "' AND IDRekening!='" . $data->IDRekening . "'");
                    if ($cek->IDRekening != '132') {
                        array_push($aMaterial, array("NoInv" => $data->NoBukti, "Tanggal" => $data->TanggalID, "Jumlah" => $data->Debet, "Bayar" => $data->Debet, "Sisa" => 0, "Keterangan" => $data->Keterangan, "Tipe" => "NON-PO"));
                        $material1 += $data->Debet;
                        $material2 += $data->Debet;
                        $material3 += 0;
                    }
                }
            }
        }

        //Dari Pengiriman
        $pengiriman1 = 0;
        $pengiriman2 = 0;
        $pengiriman3 = 0;

        $aPengiriman = array();
        $query = $db->get_results("SELECT a.*, DATE_FORMAT(b.Tanggal,'%d/%m/%Y') AS TanggalID, b.IDPengiriman FROM tb_pengiriman_detail a, tb_pengiriman b WHERE a.NoPengiriman=b.NoPengiriman AND b.IDProyek='$id' AND StokFrom='0' $cond2 GROUP BY a.NoPengiriman");
        if ($query) {
            foreach ($query as $data) {
                $grandTotal = $db->get_var("SELECT SUM(SubTotal) FROM tb_pengiriman_detail a, tb_pengiriman b WHERE a.NoPengiriman=b.NoPengiriman AND b.IDProyek='$id' AND a.StokFrom='0' AND a.NoPengiriman='" . $data->NoPengiriman . "'");
                if (!$grandTotal) $grandTotal = 0;

                array_push($aPengiriman, array("IDPengiriman" => $data->IDPengiriman, "NoInv" => $data->NoPengiriman, "Tanggal" => $data->TanggalID, "Jumlah" => $grandTotal, "Bayar" => $grandTotal, "Sisa" => 0, "Supplier" => "STOK GUDANG", "Tipe" => "DO"));
                $pengiriman1 += $grandTotal;
                $pengiriman2 += $grandTotal;
                $pengiriman3 += 0;
            }
        }

        $tenaga1 = 0;
        $tenaga2 = 0;
        $tenaga3 = 0;

        $aTenaga = array();
        $query = $db->get_results("SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_po a, tb_supplier b WHERE a.IDProyek='$id' AND a.JenisPO='2' AND a.IsAccidental='0' AND a.IDSupplier=b.IDSupplier $cond $urut");
        // $query = $db->get_results("SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_po a, tb_supplier b WHERE a.IDProyek='$id' AND a.JenisPO='2' AND a.IDSupplier=b.IDSupplier ORDER BY b.NamaPerusahaan ASC");
        if ($query) {
            foreach ($query as $data) {
                $supplier = $db->get_row("SELECT * FROM tb_supplier WHERE IDSupplier='" . $data->IDSupplier . "'");
                if ($supplier) $supplier = $supplier->NamaPerusahaan;
                else $supplier = "-";
                if (($data->Sisa > 0 && $data->Sisa < 1) || $data->Sisa < 0) $sisa = 0;
                else  $sisa = $data->Sisa;
                array_push($aTenaga, array("NoInv" => $data->NoPO, "Tanggal" => $data->TanggalID, "Jumlah" => $data->GrandTotal, "Bayar" => $data->TotalPembayaran, "Sisa" => $sisa, "Supplier" => $supplier, "Tipe" => "PO"));
                $tenaga1 += $data->GrandTotal;
                $tenaga2 += $data->TotalPembayaran;
                $tenaga3 += $sisa;
            }
        }

        $query = $db->get_results("SELECT a.*, b.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal a, tb_jurnal_detail b WHERE a.IDJurnal=b.IDJurnal AND b.IDRekening='45' AND b.Debet>0 AND a.IDProyek='$id' $cond AND a.NoRef=''");
        if ($query) {
            foreach ($query as $data) {
                if ($data->NoRef == '') {
                    $cek = $db->get_row("SELECT * FROM tb_jurnal_detail WHERE IDJurnal='" . $data->IDJurnal . "' AND IDRekening!='" . $data->IDRekening . "'");
                    if ($cek->IDRekening != '138' && $cek->IDRekening != '139') {
                        array_push($aTenaga, array("NoInv" => $data->NoBukti, "Tanggal" => $data->TanggalID, "Jumlah" => $data->Debet, "Bayar" => $data->Debet, "Sisa" => 0, "Keterangan" => $data->Keterangan, "Tipe" => "NON-PO"));
                        $tenaga1 += $data->Debet;
                        $tenaga2 += $data->Debet;
                        $tenaga3 += 0;
                    }
                }
            }
        }

        $overhead1 = 0;
        $overhead2 = 0;
        $overhead3 = 0;

        $aOverhead = array();
        $query = $db->get_results("SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_po a, tb_supplier b WHERE a.IDProyek='$id' AND a.JenisPO='3' AND a.IsAccidental='0' AND a.IDSupplier=b.IDSupplier $cond $urut");
        // $query = $db->get_results("SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_po a, tb_supplier b WHERE a.IDProyek='$id' AND a.JenisPO='3' AND a.IDSupplier=b.IDSupplier ORDER BY b.NamaPerusahaan ASC");
        if ($query) {
            foreach ($query as $data) {
                $supplier = $db->get_row("SELECT * FROM tb_supplier WHERE IDSupplier='" . $data->IDSupplier . "'");
                if ($supplier) $supplier = $supplier->NamaPerusahaan;
                else $supplier = "-";
                if (($data->Sisa > 0 && $data->Sisa < 1) || $data->Sisa < 0) $sisa = 0;
                else  $sisa = $data->Sisa;
                array_push($aOverhead, array("NoInv" => $data->NoPO, "Tanggal" => $data->TanggalID, "Jumlah" => $data->GrandTotal, "Bayar" => $data->TotalPembayaran, "Sisa" => $sisa, "Supplier" => $supplier, "Tipe" => "PO"));
                $overhead1 += $data->GrandTotal;
                $overhead2 += $data->TotalPembayaran;
                $overhead3 += $sisa;
            }
        }

        $query = $db->get_results("SELECT a.*, b.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal a, tb_jurnal_detail b WHERE a.IDJurnal=b.IDJurnal AND (b.IDRekening IN (SELECT IDRekening FROM tb_master_rekening WHERE IDParent='73' OR IDParent='101' OR IDParent IN (SELECT IDRekening FROM tb_master_rekening WHERE IDParent='73' OR IDParent='101')) OR b.IDRekening = '138') AND b.Debet>0 AND a.IDProyek='$id' AND Tipe='0' $cond");
        if ($query) {
            foreach ($query as $data) {
                array_push($aOverhead, array("NoInv" => $data->NoBukti, "Tanggal" => $data->TanggalID, "Jumlah" => $data->Debet, "Bayar" => $data->Debet, "Sisa" => 0, "Keterangan" => $data->Keterangan));
                $overhead1 += $data->Debet;
                $overhead2 += $data->Debet;
                $overhead3 += 0;
            }
        }

        //Return Barang
        $aReturn = array();
        $return1 = 0;
        $return2 = 0;
        $return3 = 0;
        $query = $db->get_results("SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_audit WHERE IDProyek='$id' $cond3 ORDER BY Tanggal ASC, IDAudit ASC");
        if ($query) {
            foreach ($query as $data) {
                array_push($aReturn, array("NoInv" => $data->NoAudit, "Tanggal" => $data->TanggalID, "Jumlah" => $data->GrandTotal, "Bayar" => 0, "Sisa" => 0, "Keterangan" => $data->Keterangan, "Tipe" => ""));
                $return1 += $data->GrandTotal;
            }
        }

        // PO Accidental
        $accidental1 = 0;
        $accidental2 = 0;
        $accidental3 = 0;

        $aAccidental = array();
        $query = $db->get_results("SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_po a, tb_supplier b WHERE a.IDProyek='$id' AND a.IsAccidental='1' AND a.IDSupplier=b.IDSupplier $cond $urut");
        if ($query) {
            foreach ($query as $data) {
                $supplier = $db->get_row("SELECT * FROM tb_supplier WHERE IDSupplier='" . $data->IDSupplier . "'");
                if ($supplier) $supplier = $supplier->NamaPerusahaan;
                else $supplier = "-";
                if (($data->Sisa > 0 && $data->Sisa < 1) || $data->Sisa < 0) $sisa = 0;
                else  $sisa = $data->Sisa;
                array_push($aAccidental, array("NoInv" => $data->NoPO, "Tanggal" => $data->TanggalID, "Jumlah" => $data->GrandTotal, "Bayar" => $data->TotalPembayaran, "Sisa" => $sisa, "Supplier" => $supplier, "Tipe" => "PO"));
                $accidental1 += $data->GrandTotal;
                $accidental2 += $data->TotalPembayaran;
                $accidental3 += $sisa;
            }
        }

        $pengeluaran1 = $material1 + $tenaga1 + $overhead1 + $pengiriman1 + $accidental1;
        $pengeluaran2 = $material2 + $tenaga2 + $overhead2 + $pengiriman2 + $accidental2;
        $pengeluaran3 = $material3 + $tenaga3 + $overhead3 + $pengiriman3 + $accidental3;
        $totalPajak = $pph2 + $ppn10;
        $profit = ($pendapatan2 - $totalPajak) - $pengeluaran1 + $return1;

        $dataProyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='$id'");
        $dataProyekDepartement = $db->get_row("SELECT * FROM tb_departement WHERE IDDepartement='" . $dataProyek->IDDepartement . "'");
        if ($dataProyek->PPNPersen > 0) $ppn = "Project PPN";
        else $ppn = "Project NON-PPN";
        $proyek = array("KodeProyek" => $dataProyek->KodeProyek, "Tahun" => $dataProyek->Tahun, "NamaProyek" => $dataProyek->NamaProyek, "NamaDepartement" => $dataProyekDepartement->NamaDepartement, "Kategori" => $ppn, "Nominal" => $dataProyek->Nominal, "PPNPersen" => $dataProyek->PPNPersen, "PPN" => $dataProyek->PPN, "GrandTotalVO" => $dataProyek->GrandTotalVO, "GrandTotal" => $dataProyek->GrandTotal, "DateStartProject" => $dataProyek->DateStartProject, "DateEndProject" => $dataProyek->DateEndProject);

        echo json_encode(array("proyek" => $proyek, "lMaterial" => $aMaterial, "lTenaga" => $aTenaga, "lOverhead" => $aOverhead, "lPendapatan" => $aPendapatan, "lPengiriman" => $aPengiriman, "lReturn" => $aReturn, "lAccidental" => $aAccidental, "Pendapatan1" => $pendapatan1, "Pendapatan2" => $pendapatan2, "Pendapatan3" => $pendapatan3, "Material1" => $material1, "Material2" => $material2, "Material3" => $material3, "Tenaga1" => $tenaga1, "Tenaga2" => $tenaga2, "Tenaga3" => $tenaga3, "Overhead1" => $overhead1, "Overhead2" => $overhead2, "Overhead3" => $overhead3, "Pengiriman1" => $pengiriman1, "Pengiriman2" => $pengiriman2, "Pengiriman3" => $pengiriman3, "Return1" => $return1, "Return2" => $return2, "Return3" => $return3, "Accidental1" => $accidental1, "Accidental2" => $accidental2, "Accidental3" => $accidental3, "Pengeluaran1" => $pengeluaran1, "Pengeluaran2" => $pengeluaran2, "Pengeluaran3" => $pengeluaran3, "Profit" => $profit, "DPP" => $dpp, "PPN10" => $ppn10, "PPH2" => $pph2, "TotalPajak" => $totalPajak, "ppnLists" => $ppnLists));
        break;

    case "LaporanCashFlow":
        $id = $_GET['id'];
        $dataArray = array();
        /*$query = $db->get_results("SELECT * FROM
                (
                SELECT NoBuktiPenerimaan AS NoBukti, Tanggal, DATE_FORMAT(Tanggal,'%d/%m/%Y') as TanggalID, Keterangan, Jumlah, 'DEBET' AS CType FROM tb_penerimaan_pembayaran WHERE IDProyek='$id'
                UNION
                SELECT NoPembayaran AS NoBukti, Tanggal, DATE_FORMAT(Tanggal,'%d/%m/%Y') as TanggalID, Keterangan, Jumlah, 'CREDIT' AS CType FROM tb_pembayaran WHERE IDProyek='$id'
                UNION
                SELECT NoPengiriman AS NoBukti, Tanggal, DATE_FORMAT(Tanggal,'%d/%m/%Y') as TanggalID, Keterangan, GrandTotal AS Jumlah, 'CREDIT' AS CType FROM
                tb_pengiriman WHERE IDProyek='$id' AND Status='Diterima'
                ) AS a ORDER BY Tanggal ASC");*/

        $query = $db->get_results("SELECT * FROM
                (
                SELECT NoBuktiPenerimaan AS NoBukti, Tanggal, DATE_FORMAT(Tanggal,'%d/%m/%Y') as TanggalID, Keterangan, Jumlah, 'DEBET' AS CType FROM tb_penerimaan_pembayaran WHERE IDProyek='$id'
                UNION
                SELECT NoPembayaran AS NoBukti, Tanggal, DATE_FORMAT(Tanggal,'%d/%m/%Y') as TanggalID, Keterangan, Jumlah, 'CREDIT' AS CType FROM tb_pembayaran WHERE IDProyek='$id'
                ) AS a ORDER BY Tanggal ASC");

        if ($query) {
            $closing = 0;
            foreach ($query as $data) {
                if ($data->CType == "CREDIT") {
                    $closing = $closing - $data->Jumlah;
                } else {
                    $closing = $closing + $data->Jumlah;
                }
                array_push($dataArray, array("NoBukti" => $data->NoBukti, "Tanggal" => $data->TanggalID, "Keterangan" => $data->Keterangan, "Jumlah" => $data->Jumlah, "CType" => $data->CType, "Closing" => $closing));
            }
        }

        $dataProyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='$id'");
        $dataProyekDepartement = $db->get_row("SELECT * FROM tb_departement WHERE IDDepartement='" . $dataProyek->IDDepartement . "'");
        $proyek = array("KodeProyek" => $dataProyek->KodeProyek, "Tahun" => $dataProyek->Tahun, "NamaProyek" => $dataProyek->NamaProyek, "NamaDepartement" => $dataProyekDepartement->NamaDepartement);

        echo json_encode(array("dataCashFlow" => $dataArray, "proyek" => $proyek));
        break;

    case "LaporanPajak":
        $id = $_GET['id'];
        $dataArray = array();
        $query = $db->get_results("SELECT * FROM
                (
                SELECT NoBuktiPenerimaan AS NoBukti, Tanggal, DATE_FORMAT(Tanggal,'%d/%m/%Y') as TanggalID, Keterangan, Jumlah, 'DEBET' AS CType FROM tb_penerimaan_pembayaran WHERE IDProyek='$id'
                UNION
                SELECT NoPembayaran AS NoBukti, Tanggal, DATE_FORMAT(Tanggal,'%d/%m/%Y') as TanggalID, Keterangan, Jumlah, 'CREDIT' AS CType FROM tb_pembayaran WHERE IDProyek='$id'
                UNION
                SELECT NoPengiriman AS NoBukti, Tanggal, DATE_FORMAT(Tanggal,'%d/%m/%Y') as TanggalID, Keterangan, GrandTotal AS Jumlah, 'CREDIT' AS CType FROM
                tb_pengiriman WHERE IDProyek='$id' AND Status='Diterima'
                ) AS a ORDER BY Tanggal ASC");

        if ($query) {
            $closing = 0;
            foreach ($query as $data) {
                if ($data->CType == "CREDIT") {
                    $closing = $closing - $data->Jumlah;
                } else {
                    $closing = $closing + $data->Jumlah;
                }
                array_push($dataArray, array("NoBukti" => $data->NoBukti, "Tanggal" => $data->TanggalID, "Keterangan" => $data->Keterangan, "Jumlah" => $data->Jumlah, "CType" => $data->CType, "Closing" => $closing));
            }
        }

        $dataProyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='$id'");
        $dataProyekDepartement = $db->get_row("SELECT * FROM tb_departement WHERE IDDepartement='" . $dataProyek->IDDepartement . "'");
        $proyek = array("KodeProyek" => $dataProyek->KodeProyek, "Tahun" => $dataProyek->Tahun, "NamaProyek" => $dataProyek->NamaProyek, "NamaDepartement" => $dataProyekDepartement->NamaDepartement);

        echo json_encode(array("dataCashFlow" => $dataArray, "proyek" => $proyek));
        break;

    default:
        echo "";
}
