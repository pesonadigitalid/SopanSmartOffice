<?php
include_once "../config/connection.php";
$idr = antiSQLInjection($_POST['idr']);


$dataReturn = $db->query("SELECT * FROM tb_return_po WHERE NoReturn='$idr'");
$query = $db->query("DELETE FROM tb_return_po WHERE NoReturn='$idr'");
if($query){
    /* PENYESUAIAN */
    $query = $db->get_results("SELECT * FROM tb_return_po_detail WHERE NoReturn = '$idr'");
    if($query){
        foreach($query as $data){
            $db->query("UPDATE tb_stok_gudang SET SisaStok=(SisaStok+".$data->Qty.") WHERE IDStokGudang='".$data->IDStok."'");
            $db->query("UPDATE tb_barang SET StokGudang=(StokGudang+".$data->Qty.") WHERE IDBarang='".$data->IDBarang."'");

            //PENYESUAIAN STOK AKHIR KARTU STOK
            $deleted = $db->get_row("SELECT * FROM tb_kartu_stok_gudang WHERE ID='".$dataReturn->IDReturn."' AND IDBarang='".$data->IDBarang."' AND Tipe='5'");
            $query = $db->get_results("SELECT * FROM tb_kartu_stok_gudang WHERE IDStokGudang>'".$deleted->IDStokGudang."' AND IDBarang='".$data->IDBarang."' ORDER BY IDStokGudang ASC");
            if($query){
                foreach($query as $data){
                    $db->get_row("UPDATE tb_kartu_stok_gudang SET StokAkhir=(StokAkhir-".$deleted->StokPenyesuaian.") WHERE IDStokGudang='".$data->IDStokGudang."'");
                }
            }
            $db->query("DELETE FROM tb_kartu_stok_gudang WHERE ID='".$$dataReturn->IDReturn."' AND IDBarang='".$data->IDBarang."' AND Tipe='5'");
        }
    }

    $db->query("DELETE FROM tb_return_po_detail WHERE NoReturn='$idr'");
    echo "1";
} else {
    echo "0";
}