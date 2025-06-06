<?php
session_start();
include_once "../config/connection.php";

$type = antiSQLInjection($_GET['type']);
switch ($type) {
    case "LoadSummary":
        $summaryAllSPB = $db->get_var("SELECT SUM(GrandTotal) FROM tb_penjualan");
        $summaryAllSPB = "Rp. " . number_format($summaryAllSPB);
        $totalAllSPB = $db->get_var("SELECT COUNT(*) FROM tb_penjualan");

        $summaryActiveSPB = $db->get_var("SELECT SUM(GrandTotal) FROM tb_penjualan WHERE IsComplete=0");
        $summaryActiveSPB = "Rp. " . number_format($summaryActiveSPB);
        $totalActiveSPB = $db->get_var("SELECT COUNT(*) FROM tb_penjualan WHERE IsComplete=0");

        $summaryCompletedSPB = $db->get_var("SELECT SUM(GrandTotal) FROM tb_penjualan WHERE IsComplete=1");
        $summaryCompletedSPB = "Rp. " . number_format($summaryCompletedSPB);
        $totalCompletedSPB = $db->get_var("SELECT COUNT(*) FROM tb_penjualan WHERE IsComplete=1");

        $summaryNewSPB = $db->get_var("SELECT SUM(GrandTotal) FROM tb_penjualan WHERE (IsComplete=0 OR IsComplete=1)");
        $summaryNewSPB = "Rp. " . number_format($summaryNewSPB);
        $totalNewSPB = $db->get_var("SELECT COUNT(*) FROM tb_penjualan WHERE (IsComplete=0 OR IsComplete=1)");

        $summaryInvoices = $db->get_var("SELECT SUM(GrandTotal) FROM tb_penjualan_invoice WHERE DATE_FORMAT(Tanggal, '%Y') = '".date('Y')."'");
        $summaryInvoices = "Rp. " . number_format($summaryInvoices);
        $totalInvoices = $db->get_var("SELECT COUNT(*) FROM tb_penjualan_invoice WHERE DATE_FORMAT(Tanggal, '%Y') = '".date('Y')."'");

        $summaryPo = $db->get_var("SELECT SUM(GrandTotal) FROM tb_po WHERE DATE_FORMAT(Tanggal, '%Y') = '".date('Y')."'");
        $summaryPo = "Rp. " . number_format($summaryPo);
        $totalPo = $db->get_var("SELECT COUNT(*) FROM tb_po WHERE DATE_FORMAT(Tanggal, '%Y') = '".date('Y')."'");

        $return = array(
            "summaryAllSPB" => $summaryAllSPB,
            "totalAllSPB" => $totalAllSPB,
            "summaryActiveSPB" => $summaryActiveSPB,
            "totalActiveSPB" => $totalActiveSPB,
            "summaryCompletedSPB" => $summaryCompletedSPB,
            "totalCompletedSPB" => $totalCompletedSPB,
            "summaryNewSPB" => $summaryNewSPB,
            "totalNewSPB" => $totalNewSPB,
            "summaryInvoices" => $summaryInvoices,
            "totalInvoices" => $totalInvoices,
            "summaryPo" => $summaryPo,
            "totalPo" => $totalPo,
        );
        echo json_encode($return);
        break;

    case "LoadChart":
        $valueInvoice = [];
        $valuePo = [];
        foreach ($fungsi->months() as $key => $month) {
            $key2 = $key+1;
            $key2 = strval($key2 < 10 ? "0".$key2 : $key2);

            $invoice = $db->get_var("SELECT SUM(GrandTotal) FROM tb_penjualan_invoice WHERE DATE_FORMAT(Tanggal, '%m-%Y') = '".$key2."-".date('Y')."'");
            $valueInvoice[$key] = !empty($invoice) ? $invoice : 0;

            $po = $db->get_var("SELECT SUM(GrandTotal) FROM tb_po WHERE DATE_FORMAT(Tanggal, '%m-%Y') = '".$key2."-".date('Y')."'");
            $valuePo[$key] = !empty($po) ? $po : 0;
        }

        $return = array(
            "months" => $fungsi->months(),
            "valueInvoice" => $valueInvoice,
            "valuePo" => $valuePo,
        );
        echo json_encode($return);
        break;
}
