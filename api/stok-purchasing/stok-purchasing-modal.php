<?php
include_once "../config/connection.php";
$id = antiSQLInjection($_GET['id']);
$gudang = antiSQLInjection($_GET['gudang']);
$idPenjualan = antiSQLInjection($_GET['idPenjualan']);
$query = $db->get_results("SELECT *, DATE_FORMAT(DateCreated,'%d/%m/%Y') AS TanggalID FROM tb_kartu_stok_purchasing WHERE IDBarang='$id' AND IDGudang='$gudang' AND IDPenjualan='$idPenjualan' ORDER BY IDKartuStok ASC");
$StokAkhir = 0;
$HPPAkhir = 0;
if ($query) {
    $return = array();
    $i = 0;
    foreach ($query as $data) {
        $i++;
        $StokAkhir = $data->StokAkhir;
        $HPPAkhir += $data->SubTotalHPP;

        $prefixCode = substr($data->NoFaktur, 0, 2);
        $url = "";
        if ($prefixCode == "AU") $url = "#/audit-stok/detail/" . $data->NoFaktur;
        else if ($prefixCode == "PB") {
            $idPenerimaanBarang = $db->get_var("SELECT IDPenerimaan FROM tb_penerimaan_stok WHERE NoPenerimaanBarang='" . $data->NoFaktur . "'");
            if ($idPenerimaanBarang) $url = "#/penerimaan-barang/detail/" . $idPenerimaanBarang;
        } else if ($prefixCode == "TF") {
            $idTransferStok = $db->get_var("SELECT IDTransferStok FROM tb_transfer_stok WHERE NoTransferStok='" . $data->NoFaktur . "'");
            if ($idTransferStok) $url = "#/transfer-stok/detail/" . $idTransferStok;
        } else if ($prefixCode == "DO") {
            $idSuratJalan = $db->get_var("SELECT IDSuratJalan FROM 	tb_penjualan_surat_jalan WHERE NoSuratJalan='" . $data->NoFaktur . "'");
            if ($idSuratJalan) $url = "#/surat-jalan/detail/" . $idSuratJalan;
        }

        array_push($return, array("IDKartuStok" => $data->IDKartuStok, "No" => $i, "Keterangan" => $data->Keterangan, "StokPenyesuaian" => $data->StokPenyesuaian, "StokAkhir" => $data->StokAkhir, "SN" => $data->SN, "HPP" => $data->HPP, "SubTotalHPP" => $data->SubTotalHPP, "Tanggal" => $data->TanggalID, "URL" => $url, "HPPAkhir" => $HPPAkhir));
    }
}
$data = $db->get_row("SELECT * FROM tb_barang WHERE IDBarang='$id'");
$NamaBarang = $data->KodeBarang . " / " . $data->Nama;
echo json_encode(array("results" => $return, "StokAkhir" => $StokAkhir, "HPPAkhir" => $HPPAkhir, "NamaBarang" => $NamaBarang));
