<?php
include_once "../config/connection.php";
$idr = antiSQLInjection($_POST['idr']);

$dataReturn = $db->get_row("SELECT * FROM tb_return_penjualan WHERE NoReturn='$idr'");
$query = $db->query("DELETE FROM tb_return_penjualan WHERE NoReturn='$idr'");
if($query){
    //RECALCULATE STOK
    $query = $db->get_results("SELECT * FROM tb_return_penjualan_detail WHERE NoReturn='".$idr."'");
    if($query){
        foreach($query as $data){
            $db->query("DELETE FROM tb_stok_gudang WHERE IDReturn='".$idr."' AND IDBarang='".$data->IDBarang."'");
            $stokGudang = $db->get_var("SELECT SUM(SisaStok) FROM tb_stok_gudang WHERE IDBarang='".$data->IDBarang."'");
            $db->query("UPDATE tb_barang SET StokGudang='$stokGudang' WHERE IDBarang='".$data->IDBarang."'");

            //PENYESUAIAN STOK AKHIR KARTU STOK
            $deleted = $db->get_row("SELECT * FROM tb_kartu_stok_gudang WHERE ID='".$dataReturn->IDReturn."' AND IDBarang='".$data->IDBarang."' AND Tipe='4'");
            $query = $db->get_results("SELECT * FROM tb_kartu_stok_gudang WHERE IDStokGudang>'".$deleted->IDStokGudang."' AND IDBarang='".$data->IDBarang."' ORDER BY IDStokGudang ASC");
            if($query){
                foreach($query as $data){
                    $db->get_row("UPDATE tb_kartu_stok_gudang SET StokAkhir=(StokAkhir-".$deleted->StokPenyesuaian.") WHERE IDStokGudang='".$data->IDStokGudang."'");
                }
            }
            $db->query("DELETE FROM tb_kartu_stok_gudang WHERE ID='".$dataReturn->IDReturn."' AND IDBarang='".$data->IDBarang."' AND Tipe='4'");
        }
    }

    $db->query("DELETE FROM tb_return_penjualan_detail WHERE NoReturn='$idr'");
    echo "1";
} else {
    echo "0";
}