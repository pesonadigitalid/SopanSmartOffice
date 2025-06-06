<?php
include_once "../config/connection.php";

$spb = antiSQLInjection($_GET['spb']);
$isMMSMaterialBantu = antiSQLInjection($_GET['isMMSMaterialBantu']);
$isPajak = antiSQLInjection($_GET['isPajak']);

$barang = array();
$supplier = array();

$cond = "";
if ($isPajak) $cond = " AND a.IsBarangPPN='1'";


//GRAB ALL DATA BARANG
if ($spb != "" && $isMMSMaterialBantu != "1") {
    $idbarang = array();
    $query = $db->get_results("SELECT a.* FROM tb_barang a, tb_penjualan_detail b, tb_penjualan c WHERE a.IDBarang=b.IDBarang AND b.NoPenjualan=c.NoPenjualan AND c.IDPenjualan='$spb' $cond ");
    if ($query) {
        foreach ($query as $data) {
            array_push($idbarang, $data->IDBarang);
            $qPaket = $db->get_results("SELECT IDBarang FROM tb_barang_child WHERE IDParent='" . $data->IDBarang . "'");
            if ($qPaket) {
                foreach ($qPaket as $dPaket) {
                    if ($dPaket->IDBarang) {
                        array_push($idbarang, $dPaket->IDBarang);
                    }
                }
            }
        }
    }
    
    $query = $db->get_results("SELECT a.*, b.NamaPerusahaan, c.Nama AS Jenis FROM tb_barang a, tb_supplier b, tb_jenis_material c WHERE a.IDSupplier=b.IDSupplier AND a.IDJenis=c.IDMaterial AND a.IDBarang IN (" . implode(",", $idbarang) . ") $cond ORDER BY a.Nama ASC");
    if ($query) {
        $i = 0;
        foreach ($query as $data) {
            $i++;
            $satuan = $db->get_var("SELECT Nama FROM tb_satuan WHERE IDSatuan='" . $data->IDSatuan . "'");
            if ($data->IsSerialize == "0") {
                $limit = 1000000;
            } else $limit = 1;

            if ($limit > 0) {
                array_push($barang, array("IDBarang" => $data->IDBarang, "KodeBarang" => $data->KodeBarang, "Nama" => $data->Nama, "No" => $i, "Kategori" => "", "Supplier" => $data->NamaPerusahaan, "Harga" => number_format($data->Harga), "Jenis" => $data->Jenis, "IsSerialize" => $data->IsSerialize, "Limit" => $limit, "Satuan" => $satuan, "DiskonPersen" => $data->DiskonPersen, "HargaPublish" => $data->HargaPublish, "IsBarangPPN" => $data->IsBarangPPN, "PPNPersen" => $data->PPNPersen, "DPP" => $data->DPP));
            }
        }
    }
} else {
    if ($isMMSMaterialBantu == "1") {
        $cond .= " AND a.IDJenis='264'";
    } else {
        $cond .= "";
    }
    
    $query = $db->get_results("SELECT a.*, b.NamaPerusahaan, c.Nama AS Jenis FROM tb_barang a, tb_supplier b, tb_jenis_material c WHERE a.IDSupplier=b.IDSupplier AND a.IDJenis=c.IDMaterial $cond ORDER BY a.Nama ASC");
    if ($query) {
        $i = 0;
        foreach ($query as $data) {
            $i++;
            $satuan = $db->get_var("SELECT Nama FROM tb_satuan WHERE IDSatuan='" . $data->IDSatuan . "'");
            if ($data->IsSerialize == "0") {
                $limit = 1000;
            } else $limit = 1;
            if ($limit > 0) {
                array_push($barang, array("IDBarang" => $data->IDBarang, "KodeBarang" => $data->KodeBarang, "Nama" => $data->Nama, "No" => $i, "Kategori" => "", "Supplier" => $data->NamaPerusahaan, "Harga" => number_format($data->Harga), "Jenis" => $data->Jenis, "IsSerialize" => $data->IsSerialize, "Limit" => $limit, "Satuan" => $satuan, "DiskonPersen" => $data->DiskonPersen, "HargaPublish" => $data->HargaPublish, "IsBarangPPN" => $data->IsBarangPPN, "PPNPersen" => $data->PPNPersen, "DPP" => $data->DPP));
            }
        }
    }
}

//GRAB ALL DATA SUPPLIER
$query = $db->get_results("SELECT a.*, b.NamaDepartement FROM tb_supplier a LEFT JOIN tb_departement b ON a.Kategori=b.IDDepartement  ORDER BY a.NamaPerusahaan ASC");
if ($query) {
    $return = array();
    $i = 0;
    foreach ($query as $data) {
        $i++;
        array_push($supplier, array("IDSupplier" => $data->IDSupplier, "No" => $i, "KodeSupplier" => $data->KodeSupplier, "Nama" => $data->NamaPerusahaan, "Provinsi" => $data->Provinsi, "NoTelp" => $data->NoTelp, "NoFax" => $data->NoFax, "Departement" => $data->NamaDepartement, "JenisProduk" => $data->Jenis, "Bank" => $data->Bank, "NoRek" => $data->NoRek));
    }
}

/* LOAD SPB */
if ($isMMSMaterialBantu == "1") {
    $cond = "WHERE IsComplete='0' OR IsComplete='1'";
} else {
    $cond = "WHERE IsComplete='0'";
}
$spb = array();
$query = $db->get_results("SELECT * FROM tb_penjualan $cond ORDER BY NoPenjualan ASC");
if ($query) {
    $return = array();
    $i = 0;
    foreach ($query as $data) {
        $pelanggan = $db->get_var("SELECT NamaPelanggan FROM tb_pelanggan WHERE IDPelanggan='" . $data->IDPelanggan . "'");
        array_push($spb, array(
            "IDPenjualan" => $data->IDPenjualan,
            "NoPenjualan" => $data->NoPenjualan . " / " . $pelanggan
        ));
    }
}

$return = array("barang" => $barang, "supplier" => $supplier, "spb" => $spb);
echo json_encode($return);
