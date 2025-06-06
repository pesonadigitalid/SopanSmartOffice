<?php
include_once "../config/connection.php";
$idr = antiSQLInjection($_POST['idr']);

$query = $db->get_results("SELECT * FROM tb_pengiriman_detail WHERE NoPengiriman=(SELECT NoPengiriman FROM tb_pengiriman WHERE IDPengiriman='$idr')");
if($query){
	foreach($query as $data){
		//$db->query("INSERT INTO tb_stok_gudang SET IDBarang='".$data->IDBarang."', Stok='".$data->Qty."', SisaStok='".$data->Qty."', Harga='".$data->Harga."', IDPenerimaan='0', SN='', IDReturn='".$idr."'");
		//$stokGudang = $db->get_var("SELECT SUM(SisaStok) FROM tb_stok_gudang WHERE IDBarang='".$data->IDBarang."'");
		//$db->query("UPDATE tb_barang SET StokGudang='$stokGudang' WHERE IDBarang='".$data->IDBarang."'");
        if($data->StokFrom=="0"){
            $db->query("UPDATE tb_stok_gudang SET SisaStok=(SisaStok+".$data->Qty.") WHERE IDStokGudang='".$data->IDStok."'");
            $db->query("UPDATE tb_barang SET StokGudang=(StokGudang+".$data->Qty.") WHERE IDBarang='".$data->IDBarang."'");
        } else {
            $db->query("UPDATE tb_stok_purchasing SET SisaStok=(SisaStok+".$data->Qty.") WHERE IDStokPurchasing='".$data->IDStok."'");
            $db->query("UPDATE tb_barang SET StokPurchasing=(StokPurchasing+".$data->Qty.") WHERE IDBarang='".$data->IDBarang."'");
        }
	}
}
$dataPengiriman = $db->get_row("SELECT * FROM tb_pengiriman WHERE IDPengiriman='$idr'");
$query = $db->query("UPDATE tb_pengiriman SET Status='Rejected' WHERE IDPengiriman='$idr'");
if($query){
	$db->query("UPDATE tb_proyek SET PengeluaranMaterial=(PengeluaranMaterial-".$dataPengiriman->GrandTotal.") WHERE IDProyek='".$dataPengiriman->IDProyek."'");
    echo "1";
} else {
    echo "0";
}