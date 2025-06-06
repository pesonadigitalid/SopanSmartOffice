<?php
include_once "../config/connection.php";

$act = $_GET['act'];
switch ($act) {
    case "LaporanInvoiceJatuhTempo":
        $datestart = $_GET['datestart'];
        $expstart = explode("/", $datestart);
        $datestartchange = $expstart[2] . "-" . $expstart[1] . "-" . $expstart[0];

        $dateend = $_GET['dateend'];
        $expend = explode("/", $dateend);
        $dateendchange = $expend[2] . "-" . $expend[1] . "-" . $expend[0];

        if ($datestart != "" && $dateend != "") {
            $cond = "AND Tanggal BETWEEN '$datestartchange' AND '$dateendchange'";
        } else if ($datestart != "") {
            $cond = "AND Tanggal='$datestartchange'";
        } else {
            $cond = "AND DATE_FORMAT(Tanggal,'%Y-%m') = '" . date("Y-m") . "'";
        }

        $return = array();
        $query = $db->get_results("SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID, DATE_FORMAT(JatuhTempo,'%d/%m/%Y') AS JatuhTempoID FROM tb_penjualan_invoice WHERE Sisa>0 $cond ORDER BY JatuhTempo ASC");
        if ($query) {
            $i = 0;
            foreach ($query as $data) {
                $i++;
                $now = time();
                $your_date = strtotime($data->JatuhTempo);
                $datediff = $now - $your_date;
                $DueDate = floor($datediff / (60 * 60 * 24));
                $DueDate = abs($DueDate);
                array_push($return, array("IDInvoice" => $data->IDInvoice, "NoInvoice" => $data->NoInvoice, "NoPenjualan" => $data->NoPenjualan, "Tanggal" => $data->TanggalID, "JatuhTempo" => $data->JatuhTempoID, "Jumlah" => $data->Sisa, "DueDate" => $DueDate, "No" => $i));
            }
        }
        echo json_encode($return);
        break;

    case "LaporanLabaRugi":
        $id = $_GET['id'];
        $urut = $_GET['urut'];

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
        $query = $db->get_results("SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_penjualan_invoice WHERE IDPenjualan='$id' AND Status='1' ORDER BY IDPenjualan ASC");
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
                }

                array_push($aPendapatan, array("IDInvoice" => $data->IDInvoice, "NoInv" => $data->NoInvoice, "Tanggal" => $data->TanggalID, "Jumlah" => $data->GrandTotal, "Bayar" => ($data->GrandTotal - $data->Sisa), "Sisa" => $sisa, "Keterangan" => $data->Keterangan, "DPP" => $temp_dpp, "PPN" => $temp_ppn, "PPH" => $temp_pph));
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
        $query = $db->get_results("SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_po a, tb_supplier b WHERE IDPenjualan='$id' AND a.JenisPO='1' AND a.IsAccidental='0' AND a.IDSupplier=b.IDSupplier AND a.DeletedDate IS NULL $cond $urut");
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

        // $query = $db->get_results("SELECT a.*, b.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal a, tb_jurnal_detail b WHERE a.IDJurnal=b.IDJurnal AND b.IDRekening='45' AND b.Debet>0 AND a.IDPenjualan='$id' $cond AND a.NoRef=''");
        // if ($query) {
        //     foreach ($query as $data) {
        //         if ($data->NoRef == '') {
        //             $cek = $db->get_row("SELECT * FROM tb_jurnal_detail WHERE IDJurnal='" . $data->IDJurnal . "' AND IDRekening!='" . $data->IDRekening . "'");
        //             if ($cek->IDRekening != '132') {
        //                 array_push($aMaterial, array("NoInv" => $data->NoBukti, "Tanggal" => $data->TanggalID, "Jumlah" => $data->Debet, "Bayar" => $data->Debet, "Sisa" => 0, "Keterangan" => $data->Keterangan, "Tipe" => "NON-PO"));
        //                 $material1 += $data->Debet;
        //                 $material2 += $data->Debet;
        //                 $material3 += 0;
        //             }
        //         }
        //     }
        // }

        //Dari Pengiriman
        $pengiriman1 = 0;
        $pengiriman2 = 0;
        $pengiriman3 = 0;

        $aPengiriman = array();
        $query = $db->get_results("SELECT a.*, DATE_FORMAT(b.Tanggal,'%d/%m/%Y') AS TanggalID, b.IDSuratJalan FROM tb_penjualan_surat_jalan_detail a, tb_penjualan_surat_jalan b WHERE a.NoSuratJalan=b.NoSuratJalan AND b.IDPenjualan='$id' AND a.StokFrom='0' AND b.Status='1' AND b.DeletedDate IS NULL $cond2 GROUP BY a.NoSuratJalan");
        if ($query) {
            foreach ($query as $data) {
                $grandTotal = $db->get_var("SELECT SUM(SubTotalHPP) FROM tb_penjualan_surat_jalan_detail a, tb_penjualan_surat_jalan b WHERE a.NoSuratJalan=b.NoSuratJalan AND b.IDPenjualan='$id' AND a.StokFrom='0' AND b.NoSuratJalan='" . $data->NoSuratJalan . "'");
                if (!$grandTotal) $grandTotal = 0;

                if ($grandTotal > 0) {

                    $jurnal = $db->get_row("SELECT * FROM tb_jurnal WHERE Tipe='8' AND NoRef='$data->IDSuratJalan'");
                    if ($jurnal) $statusJurnal = "1";
                    else $statusJurnal = "0";

                    array_push($aPengiriman, array("IDPengiriman" => $data->IDSuratJalan, "NoInv" => $data->NoSuratJalan, "Tanggal" => $data->TanggalID, "Jumlah" => $grandTotal, "Bayar" => $grandTotal, "Sisa" => 0, "Supplier" => "STOK GUDANG", "Tipe" => "DO", "StatusJurnal" => $statusJurnal));
                    $pengiriman1 += $grandTotal;
                    $pengiriman2 += $grandTotal;
                    $pengiriman3 += 0;
                }
            }
        }

        $tenaga1 = 0;
        $tenaga2 = 0;
        $tenaga3 = 0;

        $aTenaga = array();
        $query = $db->get_results("SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_po a, tb_supplier b WHERE a.IDPenjualan='$id' AND a.JenisPO='2' AND a.IsAccidental='0' AND a.IDSupplier=b.IDSupplier AND a.DeletedDate IS NULL $cond $urut");
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

        // $query = $db->get_results("SELECT a.*, b.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal a, tb_jurnal_detail b WHERE a.IDJurnal=b.IDJurnal AND b.IDRekening='45' AND b.Debet>0 AND a.IDPenjualan='$id' $cond AND a.NoRef=''");
        // if ($query) {
        //     foreach ($query as $data) {
        //         if ($data->NoRef == '') {
        //             $cek = $db->get_row("SELECT * FROM tb_jurnal_detail WHERE IDJurnal='" . $data->IDJurnal . "' AND IDRekening!='" . $data->IDRekening . "'");
        //             if ($cek->IDRekening != '138' && $cek->IDRekening != '139') {
        //                 array_push($aTenaga, array("NoInv" => $data->NoBukti, "Tanggal" => $data->TanggalID, "Jumlah" => $data->Debet, "Bayar" => $data->Debet, "Sisa" => 0, "Keterangan" => $data->Keterangan, "Tipe" => "NON-PO"));
        //                 $tenaga1 += $data->Debet;
        //                 $tenaga2 += $data->Debet;
        //                 $tenaga3 += 0;
        //             }
        //         }
        //     }
        // }

        $overhead1 = 0;
        $overhead2 = 0;
        $overhead3 = 0;

        $aOverhead = array();
        $query = $db->get_results("SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_po a, tb_supplier b WHERE a.IDPenjualan='$id' AND a.JenisPO='3' AND a.IsAccidental='0' AND a.IDSupplier=b.IDSupplier AND a.DeletedDate IS NULL $cond $urut");
        // $query = $db->get_results("SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_po a, tb_supplier b WHERE a.IDPenjualan='$id' AND a.JenisPO='3' AND a.IDSupplier=b.IDSupplier ORDER BY b.NamaPerusahaan ASC");
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

        $query = $db->get_results("SELECT a.*, b.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_jurnal a, tb_jurnal_detail b WHERE a.IDJurnal=b.IDJurnal AND (b.IDRekening IN (SELECT IDRekening FROM tb_master_rekening WHERE IDParent='73' OR IDParent='101' OR IDParent IN (SELECT IDRekening FROM tb_master_rekening WHERE IDParent='73' OR IDParent='101')) OR b.IDRekening = '138') AND b.Debet>0 AND a.IDPenjualan='$id' AND a.Tipe='0' $cond");
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
        $query = $db->get_results("SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_audit WHERE IDPenjualan='$id' AND Status='1' $cond3 ORDER BY Tanggal ASC, IDAudit ASC");
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
        // $query = $db->get_results("SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_po a, tb_supplier b WHERE a.IDPenjualan='$id' AND a.IsAccidental='1' AND a.IDSupplier=b.IDSupplier $cond $urut");
        // if ($query) {
        //     foreach ($query as $data) {
        //         $supplier = $db->get_row("SELECT * FROM tb_supplier WHERE IDSupplier='" . $data->IDSupplier . "'");
        //         if ($supplier) $supplier = $supplier->NamaPerusahaan;
        //         else $supplier = "-";
        //         if (($data->Sisa > 0 && $data->Sisa < 1) || $data->Sisa < 0) $sisa = 0;
        //         else  $sisa = $data->Sisa;
        //         array_push($aAccidental, array("NoInv" => $data->NoPO, "Tanggal" => $data->TanggalID, "Jumlah" => $data->GrandTotal, "Bayar" => $data->TotalPembayaran, "Sisa" => $sisa, "Supplier" => $supplier, "Tipe" => "PO"));
        //         $accidental1 += $data->GrandTotal;
        //         $accidental2 += $data->TotalPembayaran;
        //         $accidental3 += $sisa;
        //     }
        // }

        $pengeluaran1 = $material1 + $tenaga1 + $overhead1 + $pengiriman1 + $accidental1;
        $pengeluaran2 = $material2 + $tenaga2 + $overhead2 + $pengiriman2 + $accidental2;
        $pengeluaran3 = $material3 + $tenaga3 + $overhead3 + $pengiriman3 + $accidental3;
        $totalPajak = $pph2 + $ppn10;
        $profit = ($pendapatan2 - $totalPajak) - $pengeluaran1 + $return1;

        $dataSPB = $db->get_row("SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID, b.NamaPelanggan FROM tb_penjualan a, tb_pelanggan b WHERE a.IDPelanggan=b.IDPelanggan AND a.IDPenjualan='$id'");
        if ($dataSPB->PPNPersen > 0) $ppn = "Penjualan PPN";
        else $ppn = "Penjualan NON-PPN";
        $SPB = array("NoPenjualan" => $dataSPB->NoPenjualan, "Tanggal" => $dataSPB->TanggalID, "Pelanggan" => $dataSPB->NamaPelanggan, "Keterangan" => $dataSPB->Keterangan, "Kategori" => $ppn, "Total" => $dataSPB->Total, "Diskon" => $dataSPB->Diskon, "DiskonPersen" => $dataSPB->DiskonPersen, "PPN" => $dataSPB->PPN, "PPNPersen" => $dataSPB->PPNPersen, "OngkosKirim" => $dataSPB->OngkosKirim, "GrandTotal" => $dataSPB->GrandTotal);

        echo json_encode(array("SPB" => $SPB, "lMaterial" => $aMaterial, "lTenaga" => $aTenaga, "lOverhead" => $aOverhead, "lPendapatan" => $aPendapatan, "lPengiriman" => $aPengiriman, "lReturn" => $aReturn, "lAccidental" => $aAccidental, "Pendapatan1" => $pendapatan1, "Pendapatan2" => $pendapatan2, "Pendapatan3" => $pendapatan3, "Material1" => $material1, "Material2" => $material2, "Material3" => $material3, "Tenaga1" => $tenaga1, "Tenaga2" => $tenaga2, "Tenaga3" => $tenaga3, "Overhead1" => $overhead1, "Overhead2" => $overhead2, "Overhead3" => $overhead3, "Pengiriman1" => $pengiriman1, "Pengiriman2" => $pengiriman2, "Pengiriman3" => $pengiriman3, "Return1" => $return1, "Return2" => $return2, "Return3" => $return3, "Accidental1" => $accidental1, "Accidental2" => $accidental2, "Accidental3" => $accidental3, "Pengeluaran1" => $pengeluaran1, "Pengeluaran2" => $pengeluaran2, "Pengeluaran3" => $pengeluaran3, "Profit" => $profit, "DPP" => $dpp, "PPN10" => $ppn10, "PPH2" => $pph2, "TotalPajak" => $totalPajak));
        break;

    default:
        echo "";
}
