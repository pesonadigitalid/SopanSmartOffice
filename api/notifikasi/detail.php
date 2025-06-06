<?php
include_once "../config/connection.php";

$id = antiSQLInjection($_GET['id']);

$query = $db->get_row("SELECT a.*, b.NamaPelanggan, DATE_FORMAT(a.DateCreated,'%d/%m/%Y') AS TanggalID, DATE_FORMAT(a.TanggalFaktur,'%d/%m/%Y') AS TanggalFakturID, DATE_FORMAT(a.TanggalAkhirMaintenance,'%d/%m/%Y') AS TanggalAkhirMaintenanceID FROM tb_notifikasi_service a, tb_pelanggan b WHERE a.IDPelanggan=b.IDPelanggan AND a.IDNotifikasi='$id'");

echo json_encode($query);
