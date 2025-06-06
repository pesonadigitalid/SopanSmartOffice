<?php
include_once "../config/connection.php";

include_once "../library/class.awss3.php";
$AwsS3 = new AwsS3();

$idr = antiSQLInjection($_POST['idr']);

$cek = $db->get_row("SELECT * FROM tb_barang_child WHERE IDBarang='$idr'");
if($cek){
    echo "2";
} else {
    $cekImg = newQuery("get_row", "SELECT * FROM tb_barang WHERE IDBarang='$idr'");
    if ($cekImg->Foto1 != "")
        $AwsS3->deleteFile("barang/" . $cekImg->Foto1);
    if ($cekImg->Foto2 != "")
        $AwsS3->deleteFile("barang/" . $cekImg->Foto2);
    if ($cekImg->Foto3 != "")
        $AwsS3->deleteFile("barang/" . $cekImg->Foto3);

	$query = $db->query("DELETE FROM tb_barang WHERE IDBarang='$idr'");
	if($query){
        $db->query("DELETE FROM tb_barang_child WHERE IDParent='$idr'");
	    echo "1";
	} else {
	    echo "0";
	}
}
