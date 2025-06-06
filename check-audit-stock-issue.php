<?php
include_once "api/library/class.sqlcore.php";
include_once "api/library/class.sqlmysql.php";
include_once "api/library/class.sqlmysql.php";

date_default_timezone_set("Asia/Kuala_Lumpur");

$db = new ezSQL_mysql("root", "diadmin", "mms", "localhost");

// $noAudit = ["AU/MMS/2024/01/074", "AU/MMS/2024/01/072", "AU/MMS/2024/01/066", "AU/MMS/2024/01/065"];
// $query = $db->get_results("SELECT * FROM tb_audit WHERE NoAudit IN ('" . implode("','", $noAudit) . "')");
// if ($query) {
//     foreach ($query as $data) {
//         $qDetail = $db->get_results("SELECT * FROM tb_audit_detail WHERE NoAudit='$data->NoAudit'");
//         if ($qDetail) {
//             foreach ($qDetail as $dDetail) {
//                 $noStok = $db->get_row("SELECT * FROM tb_stok_gudang WHERE IDGudang='$data->IDGudang' AND IDBarang='$dDetail->IDBarang' AND RefID='$data->IDAudit' AND RefIDDetail='$dDetail->IDDetail' AND Tipe='2'");

//                 $stokDeleted = $db->get_row("SELECT * FROM tb_stok_gudang WHERE IDGudang='$data->IDGudang' AND IDBarang='$dDetail->IDBarang' AND RefID='$data->IDAudit' AND Tipe='1'");

//                 if (!$noStok && $stokDeleted) {
//                     if ($stokDeleted->SisaStok != abs($dDetail->SPGudang)) {
//                         echo "INVALID STOK DELETED $dDetail->IDBarang<br/>";
//                     } else {
//                         $db->query("DELETE FROM tb_stok_gudang WHERE IDStok='$stokDeleted->IDStok'");
//                         $db->query("DELETE FROM tb_kartu_stok_gudang WHERE NoFaktur='$data->NoAudit'");
//                     }
//                 }
//             }
//         }

//         $db->query("DELETE FROM tb_audit_detail WHERE NoAudit='$data->NoAudit'");
//         $db->query("DELETE FROM tb_audit WHERE NoAudit='$data->NoAudit'");
//     }
// }

$query = $db->get_results("SELECT * FROM tb_audit WHERE Status='1'");
if ($query) {
    foreach ($query as $data) {
        $qDetail = $db->get_results("SELECT * FROM tb_audit_detail WHERE NoAudit='$data->NoAudit'");
        if ($qDetail) {
            foreach ($qDetail as $dDetail) {
                $noStok = $db->get_row("SELECT * FROM tb_stok_gudang WHERE IDGudang='$data->IDGudang' AND IDBarang='$dDetail->IDBarang' AND RefID='$data->IDAudit' AND RefIDDetail='$dDetail->IDDetail' AND Tipe='2'");

                $stokDeleted = $db->get_row("SELECT * FROM tb_stok_gudang WHERE IDGudang='$data->IDGudang' AND IDBarang='$dDetail->IDBarang' AND RefID='$data->IDAudit' AND Tipe='1'");

                if (!$noStok && $stokDeleted) {
                    echo "NO STOK $dDetail->IDBarang<br/>";
                }
            }
        }
    }
}
