<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_GET['id']);

$query = $db->get_row("SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID, DATE_FORMAT(DeletedDate, '%d/%m/%Y %H:%i:%s') AS DeletedDateID FROM tb_po WHERE NoPO='$id' ORDER BY IDPO ASC");
if ($query) {
    $dataPO = $query;
    $idPO = $query->IDPO;
    $penjualan = $db->get_row("SELECT a.NoPenjualan, b.NamaPelanggan FROM tb_penjualan a, tb_pelanggan b WHERE a.IDPelanggan=b.IDPelanggan AND a.IDPenjualan='" . $query->IDPenjualan . "'");
    $user = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $query->CreatedBy . "'");
    $supplier = $db->get_var("SELECT NamaPerusahaan FROM tb_supplier WHERE IDSupplier='" . $query->IDSupplier . "'");

    if ($query->JenisPO == "1") {
        $jenis_po = "PO MATERIAL";
    } else if ($query->JenisPO == "2") {
        $jenis_po = "PO TENAGA/SUBKON";
    } else if ($query->JenisPO == "3") {
        $jenis_po = "PO OVERHEAD";
    }

    if ($query->Keterangan != "") {
        $keterangan = $query->Keterangan;
    } else {
        $keterangan = "-";
    }

    $deletedBy = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $query->DeletedBy . "'");

    $return = array("no_po" => $query->NoPO,  "tanggal" => $query->TanggalID, "kategori" => $query->Kategori, "usrlogin" => $user, "total" => $query->Total, "diskon_persen" => $query->DiskonPersen, "total2" => $query->Total2, "ppn_persen" => $query->PPNPersen, "ppn" => $query->PPN,  "total_dpp" => $query->DPP, "grand_total" => $query->GrandTotal, "pembayarandp" => $query->PembayaranDP, "sisa" => $query->Sisa, "keterangan" => $keterangan, "id_supplier" => $query->IDSupplier, "supplier" => $supplier, "metode_pembayaran" => $query->MetodePembayaran1, "metode_pembayaran2" => $query->MetodePembayaran2, "jatuhtempobg" => $query->JatuhTempoBG, "nobg" => $query->NoBG, "kembali" => $query->Kembali, "total_pembayaran" => $query->TotalPembayaran, "completed" => $query->Completed, "inv_pembayaran" => $query->InvPembayaran, "inv_bank" => $query->InvBank, "inv_delivery" => $query->InvDelivery, "inv_expedisi" => $query->InvExpedisi, "inv_alamat_pengiriman" => $query->InvAlamatPengiriman, "jenis_po" => $jenis_po, "jenis_po2" => $query->JenisPO, "spb" => $query->IDPenjualan, "no_spb" => $penjualan->NoPenjualan . " / " . $penjualan->NamaPelanggan, "isMMSMaterialBantu" => $query->IsMMSMaterialBantu, "deleted_date" => $query->DeletedDateID, "deleted_by" => $deletedBy, "deleted_remark" => $query->DeletedRemark, "completedFakturPajak" => $query->CompletedFakturPajak);
}

$masterpenerimaan = array();
$query = $db->get_row("SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_penerimaan_stok WHERE NoPO='$id' AND DeletedDate IS NULL ORDER BY IDPenerimaan ASC");
$totalperimaan = $db->get_var("SELECT COUNT(*) FROM tb_penerimaan_stok WHERE NoPO='$id' AND DeletedDate IS NULL");
if (!$totalperimaan) {
    $totalperimaan = 0;
}

if ($query) {
    $by = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $query->CreatedBy . "'");
    $masterpenerimaan = array("TotalPenerimaan" => $totalperimaan, "NoPenerimaan" => $query->NoPenerimaanBarang, "Tanggal" => $query->TanggalID, "By" => $by);
}

$detailpenerimaan = array();
$query = $db->get_results("SELECT a.NamaBarang, SUM(a.Qty) AS TotalQty FROM tb_penerimaan_stok_detail a, tb_penerimaan_stok b WHERE a.NoPenerimaanBarang=b.NoPenerimaanBarang AND b.NoPO='$id' AND b.DeletedDate IS NULL GROUP BY a.IDBarang");
if ($query) {
    foreach ($query as $data) {
        array_push($detailpenerimaan, array("NamaBarang" => $data->NamaBarang, "Qty" => $data->TotalQty));
    }
}

$historypenerimaan = array();
$dataPenerimaan = array();
$query = $db->get_results("SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_penerimaan_stok WHERE NoPO='$id' AND DeletedDate IS NULL ORDER BY IDPenerimaan ASC");
if ($query) {
    foreach ($query as $data) {
        $by = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $data->CreatedBy . "'");
        array_push($historypenerimaan, array("IDPenerimaan" => $data->IDPenerimaan, "NoPenerimaan" => $data->NoPenerimaanBarang, "Tanggal" => $data->TanggalID, "By" => $by));
        array_push($dataPenerimaan, array("IDPenerimaan" => $data->IDPenerimaan, "NoPenerimaanBarang" => $data->NoPenerimaanBarang));
    }
}

$i = 0;
$detailCart = array();
$query = $db->get_results("SELECT * FROM tb_po_detail WHERE NoPO='$id' ORDER BY NoUrut ASC");
if ($query) {
    foreach ($query as $data) {
        $i++;
        if ($dataPO->JenisPO == "1") {
            $satuan = $db->get_var("SELECT a.Nama FROM tb_satuan a, tb_barang b WHERE a.`IDSatuan`=b.`IDSatuan` AND b.`IDBarang`='" . $data->IDBarang . "'");
        } else {
            $satuan = $data->Satuan;
        }

        array_push($detailCart, array("IDBarang" => $data->IDBarang, "NamaBarang" => $data->NamaBarang, "HargaPublish" => $data->HargaPublish, "Diskon" => $data->Diskon, "Harga" => $data->Harga, "No" => $i, "NoUrut" => ($i - 1), "Qty" => $data->Qty, "QtyBarang" => $data->Qty, "Satuan" => $satuan, "SubTotal" => $data->SubTotal, "PPNPersen" => $data->PPNPersen, "DPP" => $data->DPP));
    }
}

$dataPembayaran = array();
$query = $db->get_results("SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_jurnal WHERE NoRef='$idPO' AND Tipe='5' ORDER BY Tanggal ASC, NoBukti ASC");
if ($query) {
    $i = 0;
    foreach ($query as $data) {
        $i++;
        $by = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $data->CreatedBy . "'");
        array_push($dataPembayaran, array("NoPembayaran" => $data->NoBukti, "Tanggal" => $data->TanggalID, "No" => $i, "Jumlah" => $data->Debet, "UserName" => $by));
    }
}

$dataSPB = array();
$qPenjualan = $db->get_results("SELECT * FROM tb_penjualan ORDER BY NoPenjualan ASC");
if ($qPenjualan) {
    foreach ($qPenjualan as $dPenjualan) {
        $NamaPelanggan = $db->get_var("SELECT NamaPelanggan FROM tb_pelanggan WHERE IDPelanggan='" . $dPenjualan->IDPelanggan . "'");
        array_push($dataSPB, array(
            "IDPenjualan" => $dPenjualan->IDPenjualan,
            "NoPenjualan" => $dPenjualan->NoPenjualan . " / " . $NamaPelanggan
        ));
    }
}

$dataSuratJalan = array();
$query = $db->get_results("SELECT DISTINCT(a.NoSuratJalan)
                            FROM tb_penjualan_surat_jalan_detail a,
                            tb_stok_gudang b, tb_penerimaan_stok c
                            WHERE a.StokFrom='0' AND a.IDStok=b.IDStokGudang AND
                            b.IDPenerimaan=c.IDPenerimaan AND c.NoPO='$id'
                            ORDER BY a.NoSuratJalan ASC ");
if ($query) {
    foreach ($query as $data) {
        array_push($dataSuratJalan, array("NoSuratJalan" => $data->NoSuratJalan));
    }
}

$dataFakturPajak = array();
$totalNilaiFaktur = 0;
$query = $db->get_results("SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_po_faktur_pajak WHERE IDPO='$idPO' ORDER BY Tanggal ASC");
if ($query) {
    $i = 0;
    foreach ($query as $data) {
        $i++;
        $totalNilaiFaktur += $data->Nilai;
        $by = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $data->CreatedBy . "'");
        array_push($dataFakturPajak, array("NoFaktur" => $data->NoFaktur, "Tanggal" => $data->TanggalID, "No" => $i, "Nilai" => $data->Nilai, "Nilai" => $data->Nilai, "UserName" => $by));
    }
}

$ppn = $return['ppn'];
$sisaPPN = $ppn - $totalNilaiFaktur;

echo json_encode(array("detail" => $return, "masterpenerimaan" => $masterpenerimaan, "detailpenerimaan" => $detailpenerimaan, "historypenerimaan" => $historypenerimaan, "detailcart" => $detailCart, "dataPembayaran" => $dataPembayaran, "nourut" => ($i), "spb" => $dataSPB, "dataPenerimaan" => $dataPenerimaan, "dataSuratJalan" => $dataSuratJalan, "dataFakturPajak" => $dataFakturPajak, "totalNilaiFaktur" => $totalNilaiFaktur, "sisaPPN" => $sisaPPN));
