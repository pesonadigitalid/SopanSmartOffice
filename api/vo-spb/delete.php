<?php
include_once "../config/connection.php";

$idr = antiSQLInjection($_POST['idr']);
$remark = antiSQLInjection($_POST['remark']);

$data = $db->get_row("SELECT * FROM tb_penjualan_vo WHERE IDPenjualanVO='$idr'");
$IDPenjualan = $data->IDPenjualan;
$NoVO = $data->NoVO;
$NoPenjualan = $db->get_var("SELECT NoPenjualan FROM tb_penjualan WHERE IDPenjualan='$IDPenjualan'");

/* RECALC */
$q = $db->get_results("SELECT * FROM tb_penjualan_vo WHERE IDPenjualanVO>'$idr' AND IDPenjualan='$IDPenjualan'");
if ($q) {
    foreach ($q as $d) {
        $db->query("UPDATE tb_penjualan_vo SET GrandTotalSPB=(GrandTotalSPB-" . $data->GrandTotal . "), GrandTotalSPBAkhir=(GrandTotalSPBAkhir-" . $data->GrandTotal . ") WHERE IDPenjualanVO='" . $d->IDPenjualanVO . "'");
    }
}

$qDetail = $db->get_results("SELECT * FROM tb_penjualan_vo_detail WHERE NoVO='$NoVO'");
if ($qDetail) {
    foreach ($qDetail as $dDetail) {
        $detailOrigin = $db->get_row("SELECT * FROM tb_penjualan_detail WHERE NoPenjualan='$NoPenjualan' AND IDBarang='$dDetail->IDBarang'");
        if ($detailOrigin) {
            $Qty = $detailOrigin->Qty - $dDetail->Qty;
            $SubTotal = $detailOrigin->SubTotal - $dDetail->SubTotal;
            $Margin = $detailOrigin->Margin - $dDetail->Margin;
            if ($Qty > 0) {
                $db->query("UPDATE tb_penjualan_detail SET 
                            Qty='$Qty', 
                            SubTotal='$SubTotal', 
                            Margin='$Margin'
                        WHERE IDDetail='$detailOrigin->IDDetail'");
            } else {
                $db->query("DELETE FROM tb_penjualan_detail WHERE IDDetail='$detailOrigin->IDDetail'");
            }
        }
    }

    // UPDATE TOTAL in SPB
    $dSPB = $db->get_row("SELECT * FROM tb_penjualan WHERE NoPenjualan='$NoPenjualan'");
    if ($dSPB) {
        $DiskonPersen = $dSPB->DiskonPersen;
        $PPNPersen = $dSPB->PPNPersen;

        $TotalItem = $db->get_var("SELECT SUM(Qty) FROM tb_penjualan_detail WHERE NoPenjualan='$NoPenjualan'");
        $Total = $db->get_var("SELECT SUM(SubTotal) FROM tb_penjualan_detail WHERE NoPenjualan='$NoPenjualan'");
        $TotalHPP = $db->get_var("SELECT SUM(HargaBeli*Qty) FROM tb_penjualan_detail WHERE NoPenjualan='$NoPenjualan'");
        $Diskon = $Total * $DiskonPersen / 100;

        $Total2 = $Total - $Diskon;
        $PPN = $Total2 * $PPNPersen / 100;

        $GrandTotal =  $Total2 + $PPN;
        $TotalMargin = $Total - $TotalHPP;

        $db->query("UPDATE tb_penjualan SET TotalItem='$TotalItem', Total='$Total', Diskon='$Diskon', Total2='$Total2', PPN='$PPN', GrandTotal='$GrandTotal', TotalHPP='$TotalHPP', TotalMargin='$TotalMargin' WHERE NoPenjualan='$NoPenjualan'");
    }
}

$query = $db->query("UPDATE tb_penjualan_vo SET Status='2', DeletedRemark='$remark', DeletedDate=NOW(), DeletedBy='".$_SESSION['uid']."' WHERE IDPenjualanVO='$idr'");
if ($query) {
    // $query = $db->query("DELETE FROM tb_penjualan_vo_detail WHERE NoVO='$NoVO'");
    echo "1";
} else {
    echo "0";
}
