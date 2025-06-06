<?php
session_start();
include_once "../library/class.sqlcore.php";
include_once "../library/class.sqlmysql.php";
include_once "../library/class.sqlmysql.php";
include_once "../library/class.fungsi.php";

date_default_timezone_set("Asia/Kuala_Lumpur");

$db = new ezSQL_mysql("root", "diadmin", "sopan", "localhost");
$fungsi = new Fungsi();

define("HASH_PREFIX", "000");

function newQuery($type, $sql)
{
    global $db;
    return $db->$type($sql);
}

function antiSQLInjection($input)
{
    $reg = "/(delete|update|insert|'|;|javascript|script|exec|DELETE|UPDATE|INSERT|JAVASCRIPT|SCRIPT|EXEC)/";
    return (preg_replace($reg, "", $input));
}

function getHPPAvg($idBarang)
{
    global $db;

    $totalQty = 0;
    $totalSubHPP = 0;
    $hpp = 0;
    $query = $db->get_results("SELECT * FROM tb_stok_gudang WHERE IDBarang='$idBarang' AND SisaStok>0");
    if ($query) {
        foreach ($query as $data) {
            $totalSubHPP += ($data->SisaStok * $data->Harga);
            $totalQty += $data->SisaStok;
        }
    }

    if ($totalQty > 0) {
        $hpp = round($totalSubHPP / $totalQty, 2);
    } else {
        $barang = $db->get_row("SELECT * FROM tb_barang WHERE IDBarang='$idBarang'");
        $hpp = $barang->Harga;
    }
    return $hpp;

    $barang = $db->get_row("SELECT * FROM tb_barang WHERE IDBarang='$idBarang'");
    // $qtyAkhir = $barang->StokGudang;
    // //echo $qtyAkhir."/";
    // if ($qtyAkhir > 0) {

    //     $stokAwal = $db->get_var("SELECT SUM(SubTotal) FROM tb_audit_detail WHERE IDBarang='$idBarang'");
    //     if (!$stokAwal) $stokAwal = 0;
    //     //echo $stokAwal."/";

    //     $penerimaan = $db->get_var("SELECT SUM(HPP*Qty) FROM tb_penerimaan_stok_detail WHERE IDBarang='$idBarang'");
    //     if (!$penerimaan) $penerimaan = 0;
    //     //echo $penerimaan."/";

    //     $pengiriman = $db->get_var("SELECT SUM(HPPReal*Qty) FROM tb_penjualan_surat_jalan_detail WHERE IDBarang='$idBarang'");
    //     if (!$pengiriman) $pengiriman = 0;
    //     //echo $pengiriman."/";

    //     $hpp = round(($stokAwal + $penerimaan - $pengiriman) / $qtyAkhir);
    // } else {
    //     $hpp = $barang->Harga;
    // }
    // return $hpp;
}


function getHPPStokPurchasingAvg($idBarang, $idProyek = "")
{
    global $db;

    $cond = "";
    if ($idProyek != "") $cond = " AND IDProyek='$idProyek' ";

    $totalQty = 0;
    $totalSubHPP = 0;
    $hpp = 0;
    $query = $db->get_results("SELECT * FROM tb_stok_purchasing WHERE IDBarang='$idBarang' AND SisaStok>0 $cond");
    if ($query) {
        foreach ($query as $data) {
            $totalSubHPP += ($data->SisaStok * $data->Harga);
            $totalQty += $data->SisaStok;
        }
    }

    if ($totalQty > 0) {
        $hpp = round($totalSubHPP / $totalQty, 2);
    } else {
        $barang = $db->get_row("SELECT * FROM tb_barang WHERE IDBarang='$idBarang'");
        $hpp = $barang->Harga;
    }
    return $hpp;

    $barang = $db->get_row("SELECT * FROM tb_barang WHERE IDBarang='$idBarang'");
    // $qtyAkhir = $barang->StokGudang;
    // //echo $qtyAkhir."/";
    // if ($qtyAkhir > 0) {

    //     $stokAwal = $db->get_var("SELECT SUM(SubTotal) FROM tb_audit_detail WHERE IDBarang='$idBarang'");
    //     if (!$stokAwal) $stokAwal = 0;
    //     //echo $stokAwal."/";

    //     $penerimaan = $db->get_var("SELECT SUM(HPP*Qty) FROM tb_penerimaan_stok_detail WHERE IDBarang='$idBarang'");
    //     if (!$penerimaan) $penerimaan = 0;
    //     //echo $penerimaan."/";

    //     $pengiriman = $db->get_var("SELECT SUM(HPPReal*Qty) FROM tb_penjualan_surat_jalan_detail WHERE IDBarang='$idBarang'");
    //     if (!$pengiriman) $pengiriman = 0;
    //     //echo $pengiriman."/";

    //     $hpp = round(($stokAwal + $penerimaan - $pengiriman) / $qtyAkhir);
    // } else {
    //     $hpp = $barang->Harga;
    // }
    // return $hpp;
}
