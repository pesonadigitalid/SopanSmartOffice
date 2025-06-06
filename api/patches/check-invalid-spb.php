<?php
include_once "../config/connection.php";

$PPNPersen = 11;
$query = newQuery("get_results", "SELECT * FROM tb_penjualan");
if ($query) {
    $return = array();
    foreach ($query as $data) {
        $grandTotal = newQuery("get_var", "SELECT SUM(SubTotal) FROM tb_penjualan_detail WHERE NoPenjualan='$data->NoPenjualan'");
        if ($grandTotal != $data->Total) {
            $total = $grandTotal;
            $diskon = round($total * $data->DiskonPersen / 100);
            $total2 = $total - $diskon;
            $ppn = round($total2 * $data->PPNPersen / 100);
            $grandTotalFinal = $total2 + $ppn;
            
            newQuery("query", "UPDATE tb_penjualan SET Total='$total', Diskon='$diskon', Total2='$total2', PPN='$ppn', GrandTotal='$grandTotalFinal' WHERE IDPenjualan='$data->IDPenjualan'");

            array_push($return, array("IDPenjualan" => $data->IDPenjualan, "NoPenjualan" => $data->NoPenjualan, "GrandTotal" => $data->Total, "GrandTotal2" => $grandTotal));
        }
    }
    echo json_encode($return);
} else {
    echo json_encode(array());
}
