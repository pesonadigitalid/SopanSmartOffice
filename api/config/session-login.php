<?php
session_start();
include_once "connection.php";
$response["uid"] = $_SESSION["uid"];

/* GET ALL PERMISION */
$query = $db->get_results("SELECT * FROM tb_module");
if ($query) {
	foreach ($query as $data) {
		$cek = $db->get_row("SELECT * FROM tb_hak_akses WHERE IDMember='" . $_SESSION["uid"] . "' AND IDModule='" . $data->IDModule . "'");
		if ($cek) {
			if ($cek->Read == "1") $read = true;
			else $read = false;
			if ($cek->Write == "1") $write = true;
			else $write = false;
		} else {
			$read = false;
			$write = false;
		}
		$permision[$data->ModuleName] = array("read" => $read, "write" => $write);
	}
}

/* UPDATE DATA */
$query = $db->get_row("SELECT * FROM tb_karyawan WHERE IDKaryawan='" . $_SESSION["uid"] . "'");
if ($query) {
	if ($query->Nama == "Administrator")
		$level = 0;
	else
		$level = 1;
	$response["name"] = $query->Nama;
	$response["level"] = $level;
	$response["departement"] = $query->IDDepartement;
	$response["usernm"] = $query->Usernm;
	if ($query->Foto == "")
		$img = "themes/assets/img/profiles/avatar.jpg";
	else
		$img = "files/karyawan_photo/" . $query->Foto;
	$response["profile"] = $img;
}

/* GET DATA KARYAWAN MMS */
$KaryawanMMS = $db->get_results("SELECT * FROM tb_karyawan WHERE IDKaryawan IS NOT NULL ORDER BY Nama");
if (!$KaryawanMMS) $KaryawanMMS = array();
$response["karyawanMMS"] = $KaryawanMMS;

/* TOTAL NOTIFIKASI MAINTENANCE */
$count  = $db->get_var("SELECT COUNT(*) FROM tb_notifikasi_service WHERE Status='1'");
if (!$count) $count = 0;
$response["totalNotifikasiMaintenance"] = $count;

$response["permision"] = $permision;
echo json_encode($response);
