<?php
include_once "../config/connection.php";

$penjualan = array();
$surat_jalan = array();

$query = $db->get_results("SELECT * FROM tb_penjualan WHERE DeletedDate IS NULL ORDER BY NoPenjualan ASC");
$IDPenjualan = "";
if ($query) {
    foreach ($query as $data) {
        $penagihan = $db->get_var("SELECT SUM(GrandTotal) FROM tb_penjualan_invoice WHERE IDPenjualan='" . $data->IDPenjualan . "'");
        if (!$penagihan) $penagihan = 0;

        // $totalVO = $db->get_var("SELECT SUM(GrandTotal) FROM tb_penjualan_vo WHERE IDPenjualan='$data->IDPenjualan'");
        // if (!$totalVO) $totalVO = 0;
        $totalVO = 0;
        $grandTotal = $data->GrandTotal + $totalVO;
        // var_dump($data->GrandTotal);

        $sisa = $grandTotal - $penagihan;
        if ($sisa > 0) {

            $top = array();
            if ($data->DP > 0) array_push($top, array("Label" => "DP " . $data->DP . "%", "Value" => $data->DP));
            if ($data->TerminI > 0) array_push($top, array("Label" => "Termin I " . $data->TerminI . "%", "Value" => $data->TerminI));
            if ($data->TerminII > 0) array_push($top, array("Label" => "Termin II " . $data->TerminII . "%", "Value" => $data->TerminII));
            if ($data->TerminIII > 0) array_push($top, array("Label" => "Termin III " . $data->TerminIII . "%", "Value" => $data->TerminIII));
            if ($data->TerminIV > 0) array_push($top, array("Label" => "Pelunasan " . $data->TerminIV . "%", "Value" => $data->TerminIV));

            //Ambil data barang
            $baranglist = array();
            $q = $db->get_results("SELECT * FROM tb_penjualan_detail WHERE NoPenjualan='" . $data->NoPenjualan . "' AND Harga>0 ORDER BY NoUrut");
            if ($q) {
                $i = 0;
                foreach ($q as $d) {
                    $barang = $db->get_row("SELECT * FROM tb_barang WHERE IDBarang='" . $d->IDBarang . "'");

                    $qtyReal = $d->Qty;
                    $subTotal = $qtyReal * ($d->Harga - $d->Diskon);

                    array_push($baranglist, array("IDBarang" => $d->IDBarang, "KodeBarang" => $barang->KodeBarang, "Nama" => htmlentities($d->NamaBarang), "No" => $i, "Harga" => $d->HargaBeli, "HargaJual" => $d->Harga, "IsSerialize" => "0", "Limit" => $qtyReal, "HPP" => $d->HargaBeli, "Diskon" => $d->Diskon, "HargaDiskon" => $d->HargaDiskon));
                    $i++;
                }
            }

            array_push($penjualan, array("IDPenjualan" => $data->IDPenjualan, "NoPenjualan" => $data->NoPenjualan, "NoPOKonsumen" => $data->NoPOKonsumen, "nilaiJual" => ($grandTotal), "totalPenagihan" => ($penagihan), "SisaPenagihan" => $sisa, "Diskon" => $data->DiskonPersen, "PPN" => $data->PPNPersen, "TOP" => $top, "BarangList" => $baranglist));

            $IDPenjualan .= $data->IDPenjualan . ",";
        }
    }
}

$IDPenjualan = substr($IDPenjualan, 0, -1);
$query = $db->get_results("SELECT * FROM tb_penjualan_surat_jalan WHERE IDPenjualan IN ($IDPenjualan) AND MaterialBantu='0' AND DeletedDate IS NULL ORDER BY NoSuratJalan ASC");
if ($query) {
    foreach ($query as $data) {
        $cek = $db->get_row("SELECT * FROM tb_penjualan_invoice WHERE IDSuratJalan LIKE '% " . $data->IDSuratJalan . ",%' ");
        if ($cek) $isInvoiced = 1;
        else $isInvoiced = 0;

        $dataCart = array();
        $i = 0;
        // $qSJ = $db->get_results("SELECT * FROM tb_penjualan_surat_jalan_detail WHERE NoSuratJalan='".$data->NoSuratJalan."' AND Harga>0");
        $qSJ = $db->get_results("SELECT *, DATE_FORMAT(Garansi, '%d/%m/%Y') AS GaransiID, SUM(Qty) AS QTY_REAL, AVG(HPP) AS HPP_AVG, AVG(HPPReal) AS HPP_REAL_AVG FROM tb_penjualan_surat_jalan_detail WHERE NoSuratJalan='" . $data->NoSuratJalan . "' GROUP BY IDBarang, SN ORDER BY NoUrut ASC");
        if ($qSJ) {
            foreach ($qSJ as $dSJ) {
                // $hpp = $db->get_var("SELECT AVG(a.HPP) FROM tb_penjualan_surat_jalan_detail a, tb_barang_child b WHERE a.`IDBarang`=b.`IDBarang` AND b.`IDParent`='" . $dSJ->IDBarang . "'");
                // if (!$hpp) $hpp = 0;
                $hpp = $dSJ->HPP_AVG;
                $harga = $dSJ->Harga;

                $margin = ($harga - $dSJ->Diskon) - $hpp;

                $qtyReal = $dSJ->QTY_REAL;
                $subTotal = $qtyReal * ($dSJ->Harga - $dSJ->Diskon);
                $totalMargin = $margin * $qtyReal;

                array_push($dataCart, array("HPP" => $hpp, "Harga" => $harga, "IDBarang" => $dSJ->IDBarang, "IsSerialize" => 0, "Limit" => $qtyReal, "Margin" => $totalMargin, "NamaBarang" => $dSJ->NamaBarang, "NamaBarangDisplay" => $dSJ->NamaBarang, "NoUrut" => $i, "QtyBarang" => $qtyReal, "SNBarang" => "", "SubTotal" => $subTotal, "Diskon" => $dSJ->Diskon, "DiskonPersen" => $dSJ->DiskonPersen, "DiskonValue" => ($dSJ->DiskonValue != "") ? $dSJ->DiskonValue : 0, "DiskonType" => ($dSJ->DiskonType != "") ? $dSJ->DiskonType : 0));
                $i++;
            }
        }

        array_push($surat_jalan, array("IDSuratJalan" => $data->IDSuratJalan, "NoSuratJalan" => $data->NoSuratJalan, "IDPenjualan" => $data->IDPenjualan, "NoPenjualan" => $data->NoPenjualan, "TotalNilai" => $data->TotalNilai, "Diskon" => $data->Diskon, "DiskonPersen" => $data->DiskonPersen, "TotalNilai2" => $data->TotalNilai2, "PPN" => $data->PPN, "PPNPersen" => $data->PPNPersen, "GrandTotal" => $data->GrandTotal, "IsInvoiced" => $isInvoiced, "Cart" => $dataCart, "CartNo" => $i));
    }
}

$return = array("surat_jalan" => $surat_jalan, "penjualan" => $penjualan);
echo json_encode($return);
