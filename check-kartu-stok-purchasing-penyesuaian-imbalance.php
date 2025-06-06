<?php
include_once "api/library/class.sqlcore.php";
include_once "api/library/class.sqlmysql.php";
include_once "api/library/class.sqlmysql.php";

date_default_timezone_set("Asia/Kuala_Lumpur");

$db = new ezSQL_mysql("root", "diadmin", "sopan", "localhost");

$qBarang = $db->get_results("SELECT * FROM tb_barang ORDER BY IDBarang ASC");
if ($qBarang) {
    foreach ($qBarang as $dBarang) {
        echo "IDBarang: $dBarang->IDBarang<br>";
        $qIDPenjualan = $db->get_results("SELECT DISTINCT(IDPenjualan) FROM tb_kartu_stok_purchasing WHERE IDBarang='$dBarang->IDBarang' ORDER BY IDKartuStok ASC");
        if ($qIDPenjualan) {
            foreach ($qIDPenjualan as $dIDPenjualan) {
                $qKartuStok = $db->get_results("SELECT * FROM tb_kartu_stok_purchasing WHERE IDBarang='$dBarang->IDBarang' AND IDPenjualan='$dIDPenjualan->IDPenjualan' ORDER BY IDKartuStok ASC");
                if ($qKartuStok) {
                    $stokAkhir = 0;
                    foreach ($qKartuStok as $dKartuStok) {
                        echo "IDKartuStok: $dKartuStok->IDKartuStok<br>";
                        if (($stokAkhir + $dKartuStok->StokPenyesuaian) != $dKartuStok->StokAkhir) {
                            echo "IDBarang SALAH: $dBarang->IDBarang / $stokAkhir / $dKartuStok->StokPenyesuaian / $dKartuStok->StokAkhir / $dIDPenjualan->IDPenjualan<br>";
                            break;
                        }
                        $stokAkhir = $dKartuStok->StokAkhir;
                    }
                }
            }
        }
    }
}
