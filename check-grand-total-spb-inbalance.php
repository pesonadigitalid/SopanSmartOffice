<?php
include_once "api/library/class.sqlcore.php";
include_once "api/library/class.sqlmysql.php";
include_once "api/library/class.sqlmysql.php";

date_default_timezone_set("Asia/Kuala_Lumpur");

$db = new ezSQL_mysql("root", "diadmin", "sopan", "localhost");

$qBarang = $db->get_results("SELECT * FROM tb_penjualan ORDER BY IDPenjualan ASC");
if ($qBarang) {
    foreach ($qBarang as $dBarang) {

        if (($dBarang->Total2 + $dBarang->PPN) != $dBarang->GrandTotal) {
            echo "IDPenjualan SALAH: $dBarang->IDPenjualan / $dBarang->Total2 / $dBarang->PPN / $dBarang->GrandTotal<br>";
        }

        // $qDetailBarang = $db->get_results("SELECT * FROM tb_penjualan_detail WHERE NoPenjualan='$dBarang->NoPenjualan'");
        // if ($qDetailBarang) {
        //     foreach ($qDetailBarang as $dDetailBarang) {
        //         if ($dDetailBarang->HargaDiskon * $dDetailBarang->Qty != $dDetailBarang->SubTotal) {
        //             echo "IDBarang SALAH: $dBarang->IDBarang / $dDetailBarang->HargaDiskon / $dDetailBarang->Qty / $dDetailBarang->SubTotal<br>";
        //         }
        //     }
        // }
    }
}

