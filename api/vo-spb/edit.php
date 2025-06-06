<?php
session_start();
include_once "../config/connection.php";

$id_vo = antiSQLInjection($_POST['id_vo']);
$tanggal = antiSQLInjection($_POST['tanggal']);
$exp = explode("/", $tanggal);
$tanggal = $exp[2] . "-" . $exp[1] . "-" . $exp[0];
$tanggalCond = $exp[2] . "-" . $exp[1];
$tanggalCond3 = $exp[2];
$tanggalCond2 = $exp[2] . "/" . $exp[1] . "/";

$total_item = antiSQLInjection($_POST['total_item']);
$total = antiSQLInjection($_POST['total']);
$diskon_persen = antiSQLInjection($_POST['diskon_persen']);
$diskon = antiSQLInjection($_POST['diskon']);
$total2 = antiSQLInjection($_POST['total2']);
$ppn_persen = antiSQLInjection($_POST['ppn_persen']);
$ppn = antiSQLInjection($_POST['ppn']);
$grand_total = antiSQLInjection($_POST['grand_total']);
$keterangan = antiSQLInjection($_POST['keterangan']);

$totalHPP = antiSQLInjection($_POST['totalHPP']);
$totalHPPReal = antiSQLInjection($_POST['totalHPPReal']);
$totalMargin = antiSQLInjection($_POST['totalMargin']);

$cartArray = antiSQLInjection($_POST['cart']);
$cartArray = json_decode($cartArray);

$vo = $db->get_row("SELECT * FROM tb_penjualan_vo WHERE IDPenjualanVO='$id_vo'");
if ($vo) {
    $GrandTotalOld = $vo->GrandTotal;
    $Selisih = $GrandTotalOld - $grand_total;
    $grandTotalSPB = $vo->GrandTotalSPB - $Selisih;
    $grandTotalSPBAkhir = $vo->GrandTotalSPBAkhir - $Selisih;

    $query = $db->query("UPDATE tb_penjualan_vo SET Tanggal='$tanggal', TotalItem='$total_item', Total='$total', Diskon='$diskon', DiskonPersen='$diskon_persen', Total2='$total2', PPN='$ppn', PPNPersen='$ppn_persen', GrandTotal='$grand_total', GrandTotalSPB='$grandTotalSPB', GrandTotalSPBAkhir='$grandTotalSPBAkhir', Keterangan='$keterangan', TotalHPP='$totalHPP', TotalHPPReal='$totalHPPReal', TotalMargin='$totalMargin'");

    if ($query) {
        $db->query("DELETE FROM tb_penjualan_vo_detail WHERE NoVO='$vo->NoVO'");
        foreach ($cartArray as $data) {
            if (isset($data)) {
                $db->query("INSERT INTO tb_penjualan_vo_detail SET NoVO='$vo->NoVO', NoUrut='" . $data->NoUrut . "', IDBarang='" . $data->IDBarang . "', NamaBarang='" . $data->NamaBarang . "', NamaBarangDisplay='" . $data->NamaBarangDisplay . "', Qty='" . $data->QtyBarang . "', SN='" . $data->SNBarang . "', Harga='" . $data->Harga . "', SubTotal='" . $data->SubTotal . "', HargaBeli='" . $data->HPP . "', HargaBeliReal='" . $data->HPPReal . "', Margin='" . $data->Margin . "', IsParent='" . $data->isParent . "', IsChild='" . $data->isChild . "', Diskon='" . $data->Diskon . "', HargaDiskon='" . $data->HargaDiskon . "'");
            }
        }
        echo json_encode(array("res" => 1, "mes" => "Data VO SPB berhasil disimpan!"));
    } else {
        echo json_encode(array("res" => 0, "mes" => "Data VO SPB gagal disimpan. Silahkan coba kembali nanti."));
    }
} else {
    echo json_encode(array("res" => 0, "mes" => "VO tidak ditemukan."));
}
