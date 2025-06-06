<?php
include_once "../config/connection.php";

$tanggal = antiSQLInjection($_POST['tanggal']);
$exp = explode("/", $tanggal);
$tahun = $exp[2];
$tanggal = $exp[2] . "-" . $exp[1] . "-" . $exp[0];

$karyawan = antiSQLInjection($_POST['karyawan']);
$total_item = antiSQLInjection($_POST['total_item']);
$uploaded = antiSQLInjection($_POST['uploaded']);
$cartArray = antiSQLInjection($_POST['cart']);
$cartArray = json_decode($cartArray);

do {
    $HashCode = HASH_PREFIX . md5(date("YmdHis") . rand(1, 1000000000));
    $cek = $db->get_row("SELECT * FROM tb_assign WHERE HashCode='$HashCode'");
} while ($cek);

$dataLast = $db->get_row("SELECT * FROM tb_return_asset WHERE DATE_FORMAT(Tanggal,'Y')='$tahun' ORDER BY IDReturn DESC");
if ($dataLast) {
    $last = substr($dataLast->NoReturn, -5);
    $last++;
    if ($last < 10000 and $last >= 1000)
        $last = "0" . $last;
    else if ($last < 1000 and $last >= 100)
        $last = "00" . $last;
    else if ($last < 100 and $last >= 10)
        $last = "000" . $last;
    else if ($last < 10)
        $last = "0000" . $last;
    $no_return = "RST" . $tahun . $last;
} else {
    $no_return = "RST" . $tahun . "00001";
}

$query = $db->query("INSERT INTO tb_return_asset SET NoReturn='$no_return', Tanggal='$tanggal', IDKaryawan='$karyawan', TotalItem='$total_item', CreatedBy='$uploaded', HashCode='$HashCode'");
if ($query) {
    echo "1";
    $id = mysql_insert_id();
    foreach ($cartArray as $data) {
        if (isset($data)) {
            $query2 = $db->query("INSERT INTO tb_return_asset_detail SET IDReturn='$id', IDAsset='" . $data->IDAsset . "', KodeAsset='" . $data->KodeAsset . "', Nama='" . $data->NamaAsset . "'");
            $updateReturn = $db->query("UPDATE tb_asset SET IDKaryawan=NULL, ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW() WHERE IDAsset='" . $data->IDAsset . "'");
        }
    }
} else {
    echo "0";
}
