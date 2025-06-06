<?php
include_once "../config/connection.php";
$idj = antiSQLInjection($_GET['idj']);
$idk = antiSQLInjection($_GET['idk']);
$jabatan = antiSQLInjection($_GET['jabatan']);
$status_karyawan = antiSQLInjection($_GET['status_karyawan']);
$mark = antiSQLInjection($_GET['mark']);
$status_akun = antiSQLInjection($_GET['status_akun']);
$status_harian = antiSQLInjection($_GET['status_harian']);
$id_proyek = antiSQLInjection($_GET['id_proyek']);

$cond = "WHERE IDKaryawan>0";

if ($_GET['status'] == "1") $cond .= " AND Status='1'";

if ($idj != "") {
    $cond .= " AND (IDJabatan='$idj' OR IDJabatan2='$idj')";
}

if ($idk != "") {
    $cond .= " AND IDKaryawan='$idk'";
}

if ($jabatan != "") $cond .= " AND IDJabatan='$jabatan'";

if ($status_karyawan != "") $cond .= " AND StatusKaryawan='$status_karyawan'";

if ($mark != "") $cond .= " AND StatusLainnya='$mark'";

if ($status_akun != "") $cond .= " AND Status='$status_akun'";

if ($status_harian == '0') $cond .= " AND StatusKaryawan<>'Harian'";
else if ($status_harian == '1') $cond .= " AND StatusKaryawan='Harian'";

if ($id_proyek != "") $cond .= " AND IDProyek='$id_proyek'";

$query = $db->get_results("SELECT * FROM tb_karyawan $cond AND IDKaryawan>1 ORDER BY IDKaryawan ASC");
if ($query) {
    $return = array();
    $i = 0;
    foreach ($query as $data) {
        $i++;
        if ($data->Status == "1") $status = "Aktif";
        else $status = "Non Aktif";
        $jabatan = $db->get_var("SELECT Jabatan FROM tb_jabatan WHERE IDJabatan='" . $data->IDJabatan . "'");
        array_push($return, array("No" => $i, "NIK" => $data->NIK, "Nama" => $data->Nama, "Status" => $data->StatusKaryawan, "StatusLainnya" => $data->StatusLainnya, "Jabatan" => $jabatan, "StatusK" => $status, "IDKaryawan" => $data->IDKaryawan, "CardNumber" => $data->CardNumber));
    }
    echo json_encode($return);
} else {
    echo json_encode(array());
}
