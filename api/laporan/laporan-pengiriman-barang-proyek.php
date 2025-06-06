<?php
include_once "../config/connection.php";
$proyek = antiSQLInjection($_GET['proyek']);
$result = array();

$query = $db->get_results("SELECT a.*, DATE_FORMAT(b.Tanggal,'%d/%m/%Y') AS TanggalID, b.RecievedBy FROM tb_pengiriman_detail a, tb_pengiriman b WHERE a.NoPengiriman = b.NoPengiriman AND b.IDProyek='$proyek' AND b.Status!='Rejected' ORDER BY a.IDBarang, a.NoPengiriman");
if ($query) {
    $prevID = "";
    $i = 0;
    $totalQty = 0;
    foreach ($query as $data) {
        if ($data->IDBarang != $prevID) {
            if ($i > 0) {
                array_push($result, array("TotalQty" => $totalQty, "IsList" => 0));
            }
            $prevID = $data->IDBarang;
            $totalQty = 0;
        }
        $totalQty += $data->Qty;
        $karyawan = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $data->RecievedBy . "'");
        array_push($result, array("IDBarang" => $data->IDBarang, "NamaBarang" => $data->NamaBarang, "Qty" => $data->Qty, "NoPengiriman" => $data->NoPengiriman, "Tanggal" => $data->TanggalID, "Penerima" => $karyawan, "IsList" => 1));
        $i++;

        //$TotalBarang += $data->Qty;
    }
    if ($totalQty > 0) {
        if ($i > 0) {
            array_push($result, array("TotalQty" => $totalQty, "IsList" => 0));
        }
        $totalQty = 0;
    }
}
$data = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='$proyek'");
$NamaProyek = $data->KodeProyek . " / " . $data->Tahun . " / " . $data->NamaProyek;
echo json_encode(array("data" => $result, "NamaProyek" => $NamaProyek));
