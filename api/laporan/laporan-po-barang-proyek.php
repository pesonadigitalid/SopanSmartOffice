<?php
include_once "../config/connection.php";
$proyek = antiSQLInjection($_GET['proyek']);
$result = array();

$query = $db->get_results("SELECT c.KodeBarang, a.`IDBarang`, c.Nama AS NamaBarang, SUM(Qty) AS Qty, d.Nama AS JenisMaterial
FROM tb_po_detail a, tb_po b, tb_barang c, tb_jenis_material d
WHERE a.NoPO = b.NoPO AND b.IDProyek='$proyek' AND b.`JenisPO`='1' AND a.IDBarang=c.IDBarang AND c.IDJenis=d.IDMaterial
GROUP BY d.Nama, c.Nama");
if ($query) {
    foreach ($query as $data) {
        $penerimaan = $db->get_var("SELECT SUM(a.Qty) FROM tb_penerimaan_stok_detail a, tb_penerimaan_stok b WHERE a.`NoPenerimaanBarang`=b.`NoPenerimaanBarang` AND a.`IDBarang`='" . $data->IDBarang . "' AND b.`IDProyek`='" . $proyek . "'");
        if (!$penerimaan) $penerimaan = 0;
        $pengiriman = $db->get_var("SELECT SUM(a.Qty) FROM tb_pengiriman_detail a, tb_pengiriman b WHERE a.`NoPengiriman`=b.`NoPengiriman` AND a.`IDBarang`='" . $data->IDBarang . "' AND b.`IDProyek`='" . $proyek . "' AND b.Status!='Rejected'");
        if (!$pengiriman) $pengiriman = 0;
        array_push($result, array("IDBarang" => $data->IDBarang, "KodeBarang" => $data->KodeBarang, "NamaBarang" => $data->NamaBarang, "Qty" => $data->Qty, "QtyDiterima" => $penerimaan, "QtyDikirim" => $pengiriman, "JenisMaterial" => $data->JenisMaterial));
    }
}
$data = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='$proyek'");
$NamaProyek = $data->KodeProyek . " / " . $data->Tahun . " / " . $data->NamaProyek;
echo json_encode(array("data" => $result, "NamaProyek" => $NamaProyek));
