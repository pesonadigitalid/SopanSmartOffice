<?php
include_once "../config/connection.php";
require_once('../library/class.phpmailer.php');

$tanggal = antiSQLInjection($_POST['tanggal']);
$exp = explode("/", $tanggal);
$tahun = $exp[2];
$tanggal = $exp[2] . "-" . $exp[1] . "-" . $exp[0];

$karyawan = antiSQLInjection($_POST['karyawan']);
$total_item = antiSQLInjection($_POST['total_item']);
$uploaded = antiSQLInjection($_POST['uploaded']);
$cartArray = antiSQLInjection($_POST['cart']);
$cc = antiSQLInjection($_POST['cc']);
$cartArray = json_decode($cartArray);

$status = antiSQLInjection($_POST['status']);
$approve_method = antiSQLInjection($_POST['approve_method']);
$rfidcode = antiSQLInjection($_POST['rfidcode']);

do {
    $HashCode = HASH_PREFIX . md5(date("YmdHis") . rand(1, 1000000000));
    $cek = $db->get_row("SELECT * FROM tb_assign WHERE HashCode='$HashCode'");
} while ($cek);

$ccTo = "";
foreach ($cc as $data) {
    $ccTo .= " " . $data . ", ";
}
if ($ccTo != "") $ccTo =  substr($ccTo, 0, -1);

if ($approve_method == "1") $rfidcode = "";

$dataKaryawan = $db->get_row("SELECT * FROM tb_karyawan WHERE IDKaryawan='$karyawan'");

if ($approve_method == "2" && $dataKaryawan->CardNumber == "" && $karyawan != "") {
    echo "2";
} else if ($approve_method == "2" && $dataKaryawan->CardNumber != $rfidcode && $karyawan != "") {
    echo "3";
} else {
    $dataLast = $db->get_row("SELECT * FROM tb_assign WHERE DATE_FORMAT(Tanggal,'%Y')='$tahun' ORDER BY IDAssign DESC");
    if ($dataLast) {
        $last = substr($dataLast->NoAssign, -5);
        $last++;
        if ($last < 10000 and $last >= 1000)
            $last = "0" . $last;
        else if ($last < 1000 and $last >= 100)
            $last = "00" . $last;
        else if ($last < 100 and $last >= 10)
            $last = "000" . $last;
        else if ($last < 10)
            $last = "0000" . $last;
        $no_assign = "AST" . $tahun . $last;
    } else {
        $no_assign = "AST" . $tahun . "00001";
    }

    if ($approve_method == "2" && $dataKaryawan->CardNumber == $rfidcode && $karyawan != "") {
        $status = "1";
        $cond = ", Status='1', ApproveMethod='2', DateApproved=NOW() ";
    } else {
        $status = "0";
        $cond = "";
    }

    $query = $db->query("INSERT INTO tb_assign SET NoAssign='$no_assign', Tanggal='$tanggal', IDKaryawan='$karyawan', TotalItem='$total_item', CCTo='$ccTo', CreatedBy='$uploaded', HashCode='$HashCode' $cond");
    if ($query) {
        echo "1";
        $id = mysql_insert_id();
        foreach ($cartArray as $data) {
            if (isset($data)) {
                $query2 = $db->query("INSERT INTO tb_assign_detail SET IDAssign='$id', IDAsset='" . $data->IDAsset . "', KodeAsset='" . $data->KodeAsset . "', Nama='" . $data->NamaAsset . "'");
                // if ($status == "1")
                $updateAsset = $db->query("UPDATE tb_asset SET IDKaryawan='$karyawan', ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW() WHERE IDAsset='" . $data->IDAsset . "'");
            }
        }
    } else {
        echo "0";
    }
}
