<?php
include_once "api/library/class.sqlcore.php";
include_once "api/library/class.sqlmysql.php";
include_once "api/library/class.stok.php";
include_once "api/library/class.fungsi.php";

date_default_timezone_set("Asia/Kuala_Lumpur");

$db = new ezSQL_mysql("root", "diadmin", "sopan", "localhost");
$stok = new Stok($db);
$fungsi = new Fungsi();

$db->query("UPDATE tb_penerimaan_stok SET Tanggal='2024-05-18' WHERE NoPenerimaanBarang='PB/SPN/2024/06/030'");
$db->query("UPDATE tb_penjualan_surat_jalan_detail SET Qty=1, SubTotal=(HargaDiskon*Qty), SubTotalHPP=(HPP*Qty), SubTotalMargin=(Margin*Qty) WHERE SN='302007811211030001649' AND NoSuratJalan IN (SELECT NoSuratJalan FROM tb_penjualan_surat_jalan  WHERE NoPenjualan='SPB/ARIS/2024/05/0059' AND DeletedDate IS NULL)");
$db->query("UPDATE tb_penjualan_surat_jalan_detail SET Qty=1, SubTotalHPP=(HPP*Qty), SubTotalMargin=(Margin*Qty) WHERE Qty>1 AND SN IS NOT NULL");

$db->query("TRUNCATE tb_kartu_stok_gudang");
$db->query("TRUNCATE tb_kartu_stok_purchasing");
$db->query("TRUNCATE tb_stok_gudang");
$db->query("TRUNCATE tb_stok_gudang_serial_number");
$db->query("TRUNCATE tb_stok_purchasing");
$db->query("TRUNCATE tb_stok_purchasing_serial_number");

$db->query("DELETE FROM tb_po WHERE DeletedDate IS NOT NULL");
$db->query("DELETE FROM tb_po_detail WHERE NoPO NOT IN (SELECT NoPO FROM tb_po)");
$db->query("DELETE FROM tb_penerimaan_stok WHERE DeletedDate IS NOT NULL");
$db->query("DELETE FROM tb_penerimaan_stok_detail WHERE NoPenerimaanBarang NOT IN (SELECT NoPenerimaanBarang FROM tb_penerimaan_stok)");
$db->query("DELETE FROM tb_penjualan_surat_jalan WHERE DeletedDate IS NOT NULL");
$db->query("DELETE FROM tb_penjualan_surat_jalan_detail WHERE NoSuratJalan NOT IN (SELECT NoSuratJalan FROM tb_penjualan_surat_jalan)");

UpdateHargaPO($db, $fungsi);

UpdateHPPPenerimaanBarang($db, $fungsi);

$qBarang = $db->get_results("SELECT * FROM tb_barang ORDER BY IDBarang ASC");
if ($qBarang) {
    foreach ($qBarang as $dBarang) {
        $qStok = $db->get_results("SELECT * FROM 
            (
                SELECT IDAudit AS ID, NoAudit AS NoTransaksi, Tanggal, IDGudang, DateCreated, 'Audit' AS Type, '1' AS OrderId FROM tb_audit WHERE DeletedDate IS NULL AND NoAudit IN (SELECT NoAudit FROM tb_audit_detail WHERE IDBarang='$dBarang->IDBarang')
                UNION
                SELECT IDPenerimaan AS ID, NoPenerimaanBarang AS NoTransaksi, Tanggal, IDGudang, DateCreated, 'Penerimaan' AS Type, '0' AS OrderId FROM tb_penerimaan_stok WHERE DeletedDate IS NULL AND NoPenerimaanBarang IN (SELECT NoPenerimaanBarang FROM tb_penerimaan_stok_detail WHERE IDBarang='$dBarang->IDBarang')
                UNION
                SELECT IDSuratJalan AS ID, NoSuratJalan AS NoTransaksi, Tanggal, IDGudang, DateCreated, 'SuratJalan' AS Type, '2' AS OrderId FROM tb_penjualan_surat_jalan WHERE DeletedDate IS NULL AND NoSuratJalan IN (SELECT NoSuratJalan FROM tb_penjualan_surat_jalan_detail WHERE IDBarang='$dBarang->IDBarang')
            ) AS UnionTables ORDER BY Tanggal ASC, OrderId ASC");
        if ($qStok) {
            foreach ($qStok as $dStok) {
                $IDFaktur = $dStok->ID;
                $NoFaktur = $dStok->NoTransaksi;
                $Tanggal = $dStok->Tanggal;
                $IDGudang = $dStok->IDGudang;
                $IDBarang = $dBarang->IDBarang;

                if ($dStok->Type == "Audit") {
                    $qDetail = $db->get_results("SELECT * FROM tb_audit_detail WHERE NoAudit='$NoFaktur' AND IDBarang='$IDBarang'");
                    if ($qDetail) {
                        foreach ($qDetail as $dDetail) {
                            $Tipe = ($dDetail->SPGudang > 0) ? 1 : 2;
                            $Qty = $dDetail->SPGudang;
                            $IDPenjualan = 0;
                            $IDDetailPenerimaan = ($dDetail->SPGudang > 0) ? $dDetail->IDDetail : 0;

                            $stok->InsertKartuStok($IDFaktur, $NoFaktur, $Tanggal, $IDGudang, $IDPenjualan, $IDBarang, $Qty, $dDetail->Harga, $dDetail->SN, $Tipe, 3, false, $IDDetailPenerimaan, 2);
                        }
                    }
                } else if ($dStok->Type == "Penerimaan") {
                    $qDetail = $db->get_results("SELECT * FROM tb_penerimaan_stok_detail WHERE NoPenerimaanBarang='$NoFaktur' AND IDBarang='$IDBarang'");
                    if ($qDetail) {
                        $Tipe = 1;
                        $IDPenjualan = $db->get_var("SELECT a.IDPenjualan FROM tb_po a, tb_penerimaan_stok b WHERE a.NoPO=b.NoPO AND b.IDPenerimaan='$IDFaktur'");
                        if (!$IDPenjualan) $IDPenjualan = 0;

                        foreach ($qDetail as $dDetail) {
                            $Qty = $dDetail->Qty;

                            $stok->InsertKartuStok($IDFaktur, $NoFaktur, $Tanggal, $IDGudang, $IDPenjualan, $IDBarang, $Qty, $dDetail->HPP, $dDetail->SN, $Tipe, 1, false, $dDetail->IDDetail);
                        }
                    }
                } else if ($dStok->Type == "SuratJalan") {
                    $qDetail = GetAndMergeSuratJalan($db, $NoFaktur, $IDBarang);
                    if ($qDetail) {
                        $Tipe = 2;
                        $IDPenjualan = $db->get_var("SELECT IDPenjualan FROM tb_penjualan_surat_jalan WHERE IDSuratJalan='$IDFaktur'");
                        if (!$IDPenjualan) $IDPenjualan = 0;
                        foreach ($qDetail as $dDetail) {
                            $Qty = $dDetail->Qty;
                            $IsStokGudang = $dDetail->StokFrom == 0;
                            $stok->UpdateHPPSuratJalanDetail($dDetail, $IDGudang, $IDPenjualan, $IDFaktur, $NoFaktur, $Tanggal, $IsStokGudang);
                        }
                    }
                }
            }
        }
    }
}

// Repatch Stok
RepatchStok($db, $stok);

UpdateIDDetailSuratJalan($db);

UpdateTotalSuratJalan($db);

echo "OK";

function UpdateHargaPO($db, $fungsi)
{
    $qDetail = $db->get_results("SELECT * FROM tb_po_detail WHERE NoPO='PO/SPN/P/2024/05/011'");
    if ($qDetail) {
        foreach ($qDetail as $dDetail) {
            $Diskon = ($dDetail->IDBarang == '219') ? '50%' : '20%';
            $Harga = $fungsi->getPriceAfterDistributedDiscount($Diskon, $dDetail->HargaPublish);
            $SubTotal = $Harga * $dDetail->Qty;
            $DPP = $fungsi->getDPP($dDetail->PPNPersen, $Harga);
            $db->query("UPDATE tb_po_detail SET Diskon='$Diskon', Harga='$Harga', SubTotal='$SubTotal', DPP='$DPP' WHERE IDDetail='$dDetail->IDDetail'");
        }

        $GrandTotal = $db->get_var("SELECT SUM(SubTotal) FROM tb_po_detail WHERE NoPO='PO/SPN/P/2024/05/011'");
        $DPP = $db->get_var("SELECT SUM(DPP) FROM tb_po_detail WHERE NoPO='PO/SPN/P/2024/05/011'");
        $SubTotal = $GrandTotal - $DPP;

        $db->query("UPDATE tb_po SET Total='$SubTotal', Total2='$SubTotal', DPP='$DPP', GrandTotal='$GrandTotal', Sisa='$GrandTotal' WHERE NoPO='PO/SPN/P/2024/05/011'");
    }
}

function UpdateHPPPenerimaanBarang($db, $fungsi)
{
    $qPO = $db->get_results("SELECT * FROM tb_po WHERE DeletedDate IS NULL");
    if ($qPO) {
        foreach ($qPO as $dPO) {
            $qNoPenerimaanBarang = $db->get_results("SELECT NoPenerimaanBarang FROM tb_penerimaan_stok WHERE NoPO='$dPO->NoPO' AND DeletedDate IS NULL");
            if ($qNoPenerimaanBarang) {
                foreach ($qNoPenerimaanBarang as $dNoPenerimaanBarang) {
                    $NoPenerimaanBarang = $dNoPenerimaanBarang->NoPenerimaanBarang;
                    $qDetail = $db->get_results("SELECT * FROM tb_penerimaan_stok_detail WHERE NoPenerimaanBarang='$NoPenerimaanBarang'");
                    if ($qDetail) {
                        foreach ($qDetail as $dDetail) {
                            if ($dDetail->IsPaket == "1") {
                                $HPP = $db->get_var("SELECT HargaPublish FROM tb_barang_child WHERE IDBarang='$dDetail->IDBarang'");
                                if (!$HPP) $HPP = 0;

                                $diskonPaket = $db->get_var("SELECT Diskon FROM tb_po_detail WHERE NoPO='$dPO->NoPO' AND IDBarang IN (SELECT IDParent FROM tb_barang_child WHERE IDBarang='$dDetail->IDBarang') AND '$dDetail->NamaBarang' LIKE CONCAT('%',NamaBarang,'%')");
                                if (!$diskonPaket) $diskonPaket = 0;

                                $HPP = $fungsi->getPriceAfterDistributedDiscount($diskonPaket, $HPP);
                                if ($dPO->DiskonPersen > 0) {
                                    $HPP = $fungsi->getPriceAfterDistributedDiscount($dPO->DiskonPersen, $HPP);
                                }
                            } else {
                                $HPP = $db->get_var("SELECT Harga FROM tb_po_detail WHERE NoPO='$dPO->NoPO' AND IDBarang='$dDetail->IDBarang'");
                                if (!$HPP) $HPP = 0;
                            }
                            $db->query("UPDATE tb_penerimaan_stok_detail SET HPP='$HPP' WHERE IDDetail='$dDetail->IDDetail'");
                        }
                    }
                }
            }
        }
    }
}

function GetAndMergeSuratJalan($db, $NoFaktur, $IDBarang)
{
    $isSerialize = $db->get_var("SELECT IsSerialize FROM tb_barang WHERE IDBarang='$IDBarang'");
    if ($isSerialize == 0) {
        $qDetail = $db->get_results("SELECT * FROM tb_penjualan_surat_jalan_detail WHERE NoSuratJalan='$NoFaktur' AND IDBarang='$IDBarang'");
        if ($qDetail) {
            $IDetail = 0;
            $Qty = 0;
            foreach ($qDetail as $dDetail) {
                if ($IDetail == 0) $IDetail = $dDetail->IDetail;
                $Qty += $dDetail->Qty;
            }

            if ($IDetail > 0) {
                $db->query("UPDATE tb_penjualan_surat_jalan_detail SET Qty='$Qty', HPP=0, HPPReal=0, Margin=HargaDiskon, SubTotalHPP=0, SubTotalMargin=(Qty*Margin), IDStok=NULL, StokFrom=0 WHERE IDetail='$IDetail'");
                $db->query("DELETE FROM tb_penjualan_surat_jalan_detail WHERE IDetail!='$IDetail' AND NoSuratJalan='$NoFaktur' AND IDBarang='$IDBarang'");
            }
        }
    }
    return $db->get_results("SELECT * FROM tb_penjualan_surat_jalan_detail WHERE NoSuratJalan='$NoFaktur' AND IDBarang='$IDBarang'");
}

function UpdateIDDetailSuratJalan($db)
{
    $qDetail = $db->get_results("SELECT * FROM tb_penjualan_surat_jalan_detail ORDER BY IDetail");
    if ($qDetail) {
        $IDetail = 1;
        foreach ($qDetail as $dDetail) {
            $db->query("UPDATE tb_penjualan_surat_jalan_detail SET IDetail='$IDetail' WHERE IDetail='$dDetail->IDetail'");
            $IDetail++;
        }
        $db->query("ALTER TABLE tb_penjualan_surat_jalan_detail AUTO_INCREMENT = $IDetail");
    }
}

function RepatchStok($db, $stok)
{
    $qSuratJalanDetailWithoutHPP = $db->get_results("SELECT a.*, b.IDPenjualan, b.IDSuratJalan, b.NoSuratJalan, b.Tanggal, b.IDGudang FROM tb_penjualan_surat_jalan_detail a, tb_penjualan_surat_jalan b WHERE a.NoSuratJalan=b.NoSuratJalan AND a.HPP='0'");
    if ($qSuratJalanDetailWithoutHPP) {
        foreach ($qSuratJalanDetailWithoutHPP as $dDetail) {
            $isPaket = $db->get_row("SELECT * FROM tb_barang_child WHERE IDParent='$dDetail->IDBarang'");
            if (!$isPaket) {
                $IDFaktur = $dDetail->IDSuratJalan;
                $NoFaktur = $dDetail->NoSuratJalan;
                $Tanggal = $dDetail->Tanggal;
                $IDGudang = $dDetail->IDGudang;
                $IDPenjualan = $dDetail->IDPenjualan;

                $IsStokGudang = $dDetail->StokFrom == 0;
                $stok->UpdateHPPSuratJalanDetail($dDetail, $IDGudang, $IDPenjualan, $IDFaktur, $NoFaktur, $Tanggal, $IsStokGudang);

                $HPPNull = $db->get_row("SELECT * FROM tb_penjualan_surat_jalan_detail WHERE IDBarang='$dDetail->IDBarang' AND NoSuratJalan='$dDetail->NoSuratJalan' AND HPP='0'");
                if ($HPPNull) {
                    $db->query("DELETE FROM tb_penjualan_surat_jalan_detail WHERE IDetail='$HPPNull->IDetail'");
                    echo "IDBarang: $HPPNull->IDBarang ;NoSuratJalan: $HPPNull->NoSuratJalan ;HPP: $HPPNull->HPP<br/>";
                }
            }
        }
    }
}

function UpdateTotalSuratJalan($db)
{
    $query = $db->get_results("SELECT * FROM `tb_penjualan_surat_jalan`");
    if ($query) {
        foreach ($query  as $data) {
            $IDSuratJalan = $data->IDSuratJalan;
            $NoSuratJalan = $data->NoSuratJalan;
            $DiskonPersen = $data->DiskonPersen;
            $PPNPersen = $data->PPNPersen;

            $HPP = $db->get_var("SELECT SUM(HPP*Qty) FROM tb_penjualan_surat_jalan_detail WHERE NoSuratJalan='$NoSuratJalan'");
            if (!$HPP) $HPP = 0;

            $Margin = $db->get_var("SELECT SUM(Margin*Qty) FROM tb_penjualan_surat_jalan_detail WHERE NoSuratJalan='$NoSuratJalan'");
            if (!$Margin) $Margin = 0;

            $SubTotal = $db->get_var("SELECT SUM(SubTotal) FROM tb_penjualan_surat_jalan_detail WHERE NoSuratJalan='$NoSuratJalan'");

            $Diskon = round($SubTotal * $DiskonPersen / 100);
            $SubTotal2 = $SubTotal - $Diskon;

            $PPN = round($SubTotal2 * $PPNPersen / 100);
            $GrandTotal = $SubTotal2 + $PPN;

            $db->query("UPDATE tb_penjualan_surat_jalan SET TotalNilai='$SubTotal', Diskon='$Diskon', TotalNilai2='$SubTotal2', PPN='$PPN', GrandTotal='$GrandTotal', TotalHPP='$HPP', TotalHPPReal='$HPP', TotalMargin='$Margin' WHERE NoSuratJalan='$NoSuratJalan'");

            $qJurnal = $db->get_results("SELECT * FROM `tb_jurnal` WHERE `NoRef`='$IDSuratJalan' AND Tipe='8' AND NoBukti='$NoSuratJalan' AND Debet!='$HPP' AND Kredit!='$HPP'");
            if ($qJurnal) {
                foreach ($qJurnal as $dJurnal) {
                    $db->query("UPDATE tb_jurnal SET Debet='$HPP' AND Kredit='$HPP' WHERE IDJurnal='$dJurnal->IDJurnal'");

                    $db->query("UPDATE tb_jurnal_detail SET Debet='$HPP' WHERE IDJurnal='$dJurnal->IDJurnal' AND Debet>0");

                    $db->query("UPDATE tb_jurnal_detail SET Kredit='$HPP' WHERE IDJurnal='$dJurnal->IDJurnal' AND Kredit>0");

                    echo "IDJurnal: $dJurnal->IDJurnal ;Tanggal: $dJurnal->Tanggal ;NoRef: $dJurnal->NoRef ;NoBukti: $dJurnal->NoBukti ;Debet: $dJurnal->Debet ;Kredit: $dJurnal->Kredit<br/>";
                }
            }
        }
    }
}
