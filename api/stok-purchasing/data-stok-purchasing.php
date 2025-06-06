<?php
include_once "../config/connection.php";
$act = antiSQLInjection($_GET['act']);
switch ($act) {
    case "ListData":
        $jenis = antiSQLInjection($_GET['jenis']);
        $gudang = antiSQLInjection($_GET['gudang']);
        $spb = antiSQLInjection($_GET['spb']);
        $tipe = antiSQLInjection($_GET['tipe']);

        if ($jenis != "") $cond = " AND IDJenis='$jenis'";
        if ($gudang != "") $cond2 .= " AND IDGudang='$gudang'";
        if ($spb != "") $cond2 .= " AND IDPenjualan='$spb'";
        if ($spb != "") $cond3 = " AND a.IDPenjualan='$spb'";

        $DataStok = array();
        $DataMaterial = array();

        $query = $db->get_results("SELECT * FROM tb_barang WHERE IDBarang>0 $cond AND IsBarang='1' ORDER BY IDBarang ASC");
        if ($query) {
            $i = 0;
            foreach ($query as $data) {

                $cek = $db->get_results("SELECT * FROM tb_barang_child WHERE IDParent='" . $data->IDBarang . "' AND IDBarang>0");
                if ($cek) {
                    $isPaket = 1;

                    $hpp = 0;
                    $stok = 0;
                    $break = false;
                    foreach ($cek as $child) {
                        if ($break) continue;

                        $stokChild = $db->get_var("SELECT SUM(SisaStok) FROM tb_stok_purchasing WHERE IDBarang='" . $child->IDBarang . "' $cond2");
                        if (!$stokChild || $stokChild == 0) {
                            $break = true;
                            $stok = 0;
                            $hpp = 0;
                            continue;
                        } else if (($stok == 0 && $stokChild > 0) || $stokChild < $stok) {
                            $stok = $stokChild;
                        }

                        $hppChild = $db->get_var("SELECT SUM(SubTotalHPP)/SUM(StokPenyesuaian) FROM tb_kartu_stok_purchasing WHERE IDBarang='" . $child->IDBarang . "' $cond2");
                        if (!$hppChild) $hppChild = 0;
                        $hpp += $hppChild;
                    }
                } else {
                    $isPaket = 0;
                    $stok = $db->get_var("SELECT SUM(SisaStok) FROM tb_stok_purchasing WHERE IDBarang='" . $data->IDBarang . "' $cond2");
                    $hpp = $db->get_var("SELECT SUM(SubTotalHPP)/SUM(StokPenyesuaian) FROM tb_kartu_stok_purchasing WHERE IDBarang='" . $data->IDBarang . "' $cond2");
                }

                if (!$stok) $stok = 0;
                if (!$hpp) $hpp = 0;

                $jenis = $db->get_var("SELECT Nama FROM tb_jenis_material WHERE IDMaterial='" . $data->IDJenis . "'");
                $total = $hpp * $stok;

                $stokAvailable = $tipe == "1"
                    ? false
                    : $db->get_row("SELECT * FROM tb_stok_purchasing WHERE IDBarang='" . $data->IDBarang . "' $cond2");

                if ($stok > 0 || $stokAvailable) {
                    $i++;

                    if ($tipe == "1") $cond2 .= " AND SisaStok>0";
                    $spb = $db->get_results("SELECT IDPenjualan, NoPenjualan FROM tb_penjualan WHERE IDPenjualan IN (SELECT IDPenjualan FROM tb_stok_purchasing WHERE IDBarang='" . $data->IDBarang . "' $cond2)");
                    if (!$spb) $spb = array();

                    array_push($DataStok, array("IDBarang" => $data->IDBarang, "No" => $i, "KodeBarang" => $data->KodeBarang, "Nama" => $data->Nama, "StokGudang" => $stok, "isPaket" => $isPaket, "StokPurchasing" => $data->StokPurchasing, "Jenis" => $jenis, "IsSerialize" => $data->IsSerialize, "HPP" => $hpp, "TotalNilai" => $total, "SPB" => $spb));
                }
            }
        }

        $query = $db->get_results("SELECT * FROM tb_jenis_material ORDER BY Nama ASC");
        if ($query) {
            $i = 0;
            foreach ($query as $data) {
                $i++;
                if ($data->Parent == "0") {
                    $parent = "ROOT";
                } else {
                    $parent = $db->get_var("SELECT Nama FROM tb_jenis_material WHERE IDMaterial='" . $data->Parent . "'");
                }
                array_push($DataMaterial, array("IDMaterial" => $data->IDMaterial, "No" => $i, "Parent" => $parent, "Nama" => $data->Nama));
            }
        }

        $querySPB = $db->get_results("SELECT * FROM tb_penjualan WHERE DeletedDate IS NULL ORDER BY NoPenjualan");
        if (!$querySPB) $querySPB = array();

        $queryGudang = $db->get_results("SELECT * FROM tb_gudang ORDER BY Nama");
        if (!$queryGudang) $queryGudang = array();

        $return = array("DataStok" => $DataStok, "DataMaterial" => $DataMaterial, "DataGudang" => $queryGudang, "DataSPB" => $querySPB);
        echo json_encode($return);
        break;

    case "DetailStokPurchasing":
        $id = antiSQLInjection($_GET['id']);
        $query = $db->get_results("SELECT a.*, b.Nama AS NamaBarang, b.KodeBarang, c.SN, c.DateCreated AS TanggalPenerimaan, c.IDStokSN, d.Nama AS NamaGudang FROM tb_stok_purchasing a, tb_barang b, tb_stok_purchasing_serial_number c, tb_gudang d WHERE a.`IDBarang`=b.`IDBarang` AND  a.`IDStok`=c.`IDStok` AND a.IDGudang=d.IDGudang AND a.`IDBarang`='$id' AND c.`Stok`>0 ORDER BY IDBarang ASC");
        if ($query) {
            $return = array();
            $i = 0;
            foreach ($query as $data) {
                $i++;

                $noFaktur = $db->get_var("SELECT NoFaktur FROM tb_kartu_stok_purchasing WHERE SN='$data->SN' AND IDGudang='$data->IDGudang'");
                $tanggal = $data->TanggalPenerimaan;
                if (substr($noFaktur, 0, 2) == "PB") {
                    $tanggal = $db->get_var("SELECT Tanggal FROM tb_penerimaan_stok WHERE NoPenerimaanBarang='$noFaktur'");
                }

                $spb = $db->get_var("SELECT NoPenjualan FROM tb_penjualan WHERE IDPenjualan='$data->IDPenjualan'");
                if (!$spb) $spb = "-";

                array_push($return, array("IDStokPurchasing" => $data->IDStokSN, "No" => $i, "IDBarang" => $data->IDBarang, "NamaBarang" => $data->NamaBarang, "KodeBarang" => $data->KodeBarang, "SPB" => $spb, "SN" => $data->SN, "Gudang" => $data->NamaGudang, "TanggalPenerimaan" => $fungsi->IDDate($tanggal)));
            }
            echo json_encode($return);
        } else {
            echo json_encode(array());
        }
        break;

    case "DetailSN":
        $id = antiSQLInjection($_GET['id']);
        $query = $db->get_row("SELECT a.*, b.IDBarang FROM tb_stok_purchasing_serial_number a, tb_stok_purchasing b WHERE a.IDStok=b.IDStok AND a.IDStokSN='$id'");
        if ($query) {
            echo json_encode(array("SerialNumber" => $query->SN, "IDBarang" => $query->IDBarang));
        } else {
            echo json_encode(array());
        }
        break;

    case "EditSNStokPurchasing":
        $id = antiSQLInjection($_POST['id']);
        $serial_number = antiSQLInjection($_POST['serial_number']);

        $data = $db->get_row("SELECT a.*, b.IDBarang , b.IDGudang FROM tb_stok_purchasing_serial_number a, tb_stok_purchasing b WHERE a.IDStok=b.IDStok AND a.IDStokSN='$id'");
        if ($data) {

            $noFaktur = $db->get_var("SELECT NoFaktur FROM tb_kartu_stok_purchasing WHERE SN='$data->SN' AND IDGudang='$data->IDGudang' AND IDBarang='$data->IDBarang'");
            if (substr($noFaktur, 0, 2) == "PB") {
                $db->query("UPDATE tb_penerimaan_stok_detail SET SN='$serial_number' WHERE NoPenerimaanBarang='$noFaktur' AND SN='$data->SN' AND IDBarang='$data->IDBarang'");
            }

            $db->query("UPDATE tb_stok_purchasing_serial_number SET SN='$serial_number' WHERE IDStokSN='$id'");
            $db->query("UPDATE tb_kartu_stok_purchasing SET SN='$serial_number' WHERE SN='$data->SN' AND IDGudang='$data->IDGudang' AND IDBarang='$data->IDBarang'");

            echo "1";
        } else {
            echo "0";
        }

        break;
}
