<?php
include_once "../config/connection.php";
include_once "../library/class.stok.php";

$stok = new Stok($db);

$tanggal = antiSQLInjection($_POST['tanggal']);
$exptgl = explode("/", $tanggal);
$tanggal = $exptgl[2] . "-" . $exptgl[1] . "-" . $exptgl[0];
$tanggalCond = $exptgl[2] . "-" . $exptgl[1];
$tanggalCond2 = $exptgl[2] . $exptgl[1];
$tanggalCond3 = $exptgl[2] . "/" . $exptgl[1] . "/";

$usrlogin = antiSQLInjection($_POST['usrlogin']);
$total = str_replace(",", "", antiSQLInjection($_POST['total']));
$keterangan = antiSQLInjection($_POST['keterangan']);
$uploaded = antiSQLInjection($_POST['uploaded']);
$cartArray = antiSQLInjection($_POST['cart']);
$totalHPP = antiSQLInjection($_POST['totalHPP']);
$id_penjualan = antiSQLInjection($_POST['id_penjualan']);
$id_gudang = antiSQLInjection($_POST['id_gudang']);
$cartArray = json_decode($cartArray);

$lanjut = true;
foreach ($cartArray as $data) {
    if (isset($data)) {
        if ($data->IsSerialize == "1" && $data->QtyBarang == -1) {
            $cek = $db->get_row("SELECT b.SN FROM tb_stok_gudang a, tb_stok_gudang_serial_number b WHERE a.IDStok=b.IDStok AND a.IDBarang='" . $data->IDBarang . "' AND a.IDGudang='" . $id_gudang . "' AND b.SN='" . $data->SN . "' AND b.Stok>0");
            if (!$cek) {
                $message = "Audit Stok tidak dapat disimpan karena SN '" . $data->SN . "' tidak terdaftar pada sistem. Silahkan cek Serial Number Barang yang terdaftar di halaman Stok Persediaan Barang";
                $lanjut = false;
            }
        } else if ($data->IsSerialize == "1" && $data->QtyBarang == 1) {
            $cek = $db->get_row("SELECT b.SN FROM tb_stok_gudang a, tb_stok_gudang_serial_number b WHERE a.IDStok=b.IDStok AND a.IDBarang='" . $data->IDBarang . "' AND b.SN='" . $data->SN . "' AND b.Stok>0");
            if ($cek) {
                $message = "Audit Stok tidak dapat disimpan karena SN '" . $data->SN . "' telah terdaftar dalam Stok Persedian. ";
                $lanjut = false;
            }
        }
    }
}

if ($lanjut) {
    $dataLast = $db->get_row("SELECT * FROM tb_audit WHERE DATE_FORMAT(Tanggal,'%Y-%m')='" . $tanggalCond . "' ORDER BY NoAudit DESC");
    $prefix = "AU/SPN/";

    if ($dataLast) {
        $last = substr($dataLast->NoAudit, -3);
        $last++;
        if ($last < 100 and $last >= 10)
            $last = "0" . $last;
        else if ($last < 10)
            $last = "00" . $last;
        $no = $prefix . $tanggalCond3 . $last;
    } else {
        $no = $prefix . $tanggalCond3 . "001";
    }

    $totalHarga = 0;
    $query = $db->query("INSERT INTO tb_audit SET NoAudit='$no', Tanggal='$tanggal', TotalItem='$total', GrandTotal='$totalHPP', Keterangan='$keterangan', DateCreated=NOW(), CreatedBy='" . $_SESSION["uid"] . "', IDGudang='$id_gudang'");
    if ($query) {
        $id = $db->get_var("SELECT LAST_INSERT_ID()");
        echo json_encode(array("res" => 1, "mes" => "Audit stok berhasil disimpan"));
        foreach ($cartArray as $data) {
            if (isset($data)) {

                $db->query("INSERT INTO tb_audit_detail SET NoAudit='$no', NoUrut='" . $data->NoUrut . "', IDBarang='" . $data->IDBarang . "', NamaBarang='" . $data->NamaBarang . "', StokPurchasingAwal='0', StokGudangAwal='" . $data->StokGudang . "', StokPurchasing='0', StokGudang='" . $data->StokAkhir . "', SPPurchasing='0', SPGudang='" . $data->Selisih . "', Harga='" . $data->Harga . "', SubTotal='" . $data->SubTotal . "', SN='" . $data->SN . "'");

                $totalHarga += $data->SubTotal;
            }
        }

        $query = $db->query("UPDATE tb_audit SET GrandTotal='$totalHarga' WHERE IDAudit='$id'");

        $stok->AuditStokGudang($no);
    } else {
        echo json_encode(array("res" => 0, "mes" => "Audit stok order gagal disimpan. Silahkan coba kembali."));
    }
} else {
    echo json_encode(array("res" => 0, "mes" => $message));
}
