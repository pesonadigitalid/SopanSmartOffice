<?php
include_once "../config/connection.php";

$tanggal = antiSQLInjection($_POST['tanggal']);
$exptgl = explode("/", $tanggal);
$tanggal = $exptgl[2] . "-" . $exptgl[1] . "-" . $exptgl[0];
$tanggalCond = $exptgl[2] . "-" . $exptgl[1];
$tanggalCond2 = $exptgl[2] . $exptgl[1];
$tanggalCond3 = $exptgl[2] . "/" . $exptgl[1] . "/";
$tanggalCond4 = $exptgl[2];

$jatuh_tempo = antiSQLInjection($_POST['jatuh_tempo']);
$expjt = explode("/", $jatuh_tempo);
$jatuh_tempo = $expjt[2] . "-" . $expjt[1] . "-" . $expjt[0];

$jumlah_persen = str_replace(",", "", antiSQLInjection($_POST['jumlah_persen']));
$jumlah = str_replace(",", "", antiSQLInjection($_POST['jumlah']));

$uploaded = antiSQLInjection($_POST['uploaded']);
$NoFakturPajak = antiSQLInjection($_POST['NoFakturPajak']);

$keterangan = antiSQLInjection($_POST['keterangan']);
$terbilang = antiSQLInjection($_POST['terbilang']);
$note1 = antiSQLInjection($_POST['note1']);
$note2 = antiSQLInjection($_POST['note2']);
$sign = antiSQLInjection($_POST['sign']);
$npwp = antiSQLInjection($_POST['npwp']);

$ppn_persen = antiSQLInjection($_POST['ppn_persen']);
$ppn = antiSQLInjection($_POST['ppn']);
$grand_total = antiSQLInjection($_POST['grand_total']);

$noinv = antiSQLInjection($_POST['noinv']);
$IsPajak = antiSQLInjection($_POST['IsPajak']);
$NoFakturPajak = antiSQLInjection($_POST['NoFakturPajak']);
$id_penjualan = antiSQLInjection($_POST['spb']);
$suratJalan = antiSQLInjection($_POST['suratJalan']);


$diskon_persen = antiSQLInjection($_POST['diskon_persen']);
$diskon = antiSQLInjection($_POST['diskon']);
$jumlah2 = antiSQLInjection($_POST['jumlah2']);


$TOP = antiSQLInjection($_POST['TOP']);
$cartArray = antiSQLInjection($_POST['cart']);
$cartArray = json_decode($cartArray);

// $sj = "";
// foreach($suratJalan as $data){
//     $sj .= " ".$data.", ";
// }
// if($sj!="") $sj =  substr($sj, 0, -1);

$dataPenjualan = $db->get_row("SELECT * FROM tb_penjualan WHERE IDPenjualan='$id_penjualan'");
$no_penjualan = $dataPenjualan->NoPenjualan;

//Cek sisa penagihan
$totalPenagihanSampaiSaatIni = $db->get_var("SELECT SUM(GrandTotal) FROM tb_penjualan_invoice WHERE IDPenjualan='$id_penjualan'");
$grandTotalProyek = $db->get_var("SELECT GrandTotal FROM tb_penjualan WHERE IDPenjualan='$id_penjualan'");
$maxPenagihan = $grandTotalProyek - $totalPenagihanSampaiSaatIni;

if ($grand_total <= $maxPenagihan) {
    $cek = $db->get_row("SELECT * FROM tb_penjualan_invoice WHERE NoInvoice='$noinv'");
    if ($cek && $noinv != '') {
        echo json_encode(array("status" => 0, "msg" => "No Invoice yang anda masukan telah digunakan pada invoice lain."));
    } else {
        if ($IsPajak != '1') {
            $dataLast = $db->get_row("SELECT * FROM tb_penjualan_invoice WHERE DATE_FORMAT(Tanggal,'%Y-%m')='" . $tanggalCond . "' AND IsPajak='0' ORDER BY NoInvoice DESC");
            if ($dataLast) $last = intval(substr($dataLast->NoInvoice, -3));
            else $last = 0;
            do {
                $last++;
                if ($last < 100 and $last >= 10)
                    $last = "0" . $last;
                else if ($last < 10)
                    $last = "00" . $last;
                $noinv = "INV/SPN/" . $tanggalCond3 . $last;
                $checkNoTransaksi = $db->get_row("SELECT * FROM tb_penjualan_invoice WHERE NoInvoice='$noinv'");
            } while ($checkNoTransaksi);
            $IsPajak = 0;
        }

        $query = $db->query("INSERT INTO tb_penjualan_invoice SET NoInvoice='$noinv', IDPenjualan='$id_penjualan', NoPenjualan='$no_penjualan', IDSuratJalan='$suratJalan', Tanggal='$tanggal', JatuhTempo='$jatuh_tempo', JumlahPersen='$jumlah_persen', Jumlah='$jumlah', PPNPersen='$ppn_persen', PPN='$ppn', GrandTotal='$grand_total', Sisa='$grand_total', Keterangan='$keterangan', Terbilang='$terbilang', Note1='$note1', Note2='$note2', Sign='$sign', NPWP='$npwp', CreatedBy='" . $_SESSION["uid"] . "', IsPajak='$IsPajak', DiskonPersen='$diskon_persen', Diskon='$diskon', Jumlah2='$jumlah2', TOP='$TOP', NoFakturPajak='$NoFakturPajak'");
        if ($query) {
            $id = $db->get_var("SELECT LAST_INSERT_ID()");
            foreach ($cartArray as $data) {
                if (isset($data)) {
                    $db->query("INSERT INTO tb_penjualan_invoice_detail SET IDInvoice='$id', NoInvoice='$noinv', NoUrut='" . $data->NoUrut . "', IDBarang='" . $data->IDBarang . "', NamaBarang='" . $data->NamaBarang . "', NamaBarangDisplay='" . $data->NamaBarangDisplay . "', Qty='" . $data->QtyBarang . "', SN='" . $data->SNBarang . "', Harga='" . $data->Harga . "', SubTotal='" . $data->SubTotal . "', HargaBeli='" . $data->HPP . "', Margin='" . $data->Margin . "', Diskon='" . $data->Diskon . "', HargaDiskon='" . $data->HargaDiskon . "'");
                }
            }
            echo json_encode(array("status" => 1, "msg" => "Sukses"));
        } else {
            echo json_encode(array("status" => 0, "msg" => "Invoice gagal disimpan. Silahkan coba kembali nanti. Jika Invoice pajak. Silahkan masukan nomor invoice pajak manual anda."));
        }
    }
} else {
    echo json_encode(array("status" => 0, "msg" => "Grand Total Invoice melebihi sisa nilai dari SPB yang harus ditagihan. Batasan pengihan yang dapat dilakukan adalah Rp. " . number_format($maxPenagihan)));
}
