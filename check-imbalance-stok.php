<?php
include_once "api/library/class.sqlcore.php";
include_once "api/library/class.sqlmysql.php";
include_once "api/library/class.sqlmysql.php";

date_default_timezone_set("Asia/Kuala_Lumpur");

$db = new ezSQL_mysql("root", "diadmin", "sopan", "localhost");

$qBarang = $db->get_results("SELECT * FROM tb_barang ORDER BY IDBarang ASC");
$qGudang = $db->get_results("SELECT * FROM tb_gudang ORDER BY IDGudang ASC");
if ($qBarang && $qGudang) {
    foreach ($qBarang as $dBarang) {
        foreach ($qGudang as $dGudang) {
            $stokGudang = $db->get_var("SELECT SUM(SisaStok) FROM tb_stok_gudang WHERE IDGudang='$dGudang->IDGudang' AND IDBarang='$dBarang->IDBarang'");
            $sisaKartuStok = $db->get_var("SELECT StokAkhir FROM tb_kartu_stok_gudang WHERE IDGudang='$dGudang->IDGudang' AND IDBarang='$dBarang->IDBarang' ORDER BY IDKartuStok DESC LIMIT 0,1");
            if ($stokGudang != $sisaKartuStok) {
                echo "STOK GUDANG SALAH: $dBarang->IDBarang / $dGudang->IDGudang / $stokGudang / $sisaKartuStok<br>";
            }
        }
    }
}
