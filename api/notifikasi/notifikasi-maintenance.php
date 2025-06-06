<?php
include_once "../config/connection.php";

$date = Date("Y-m-d");
$types = array("IsNotifiedService6", "IsNotifiedService12", "IsNotifiedService18");
$dateIntervals = array(5, 11, 17);
$prefixes = array("Notifikasi Service 6 Bulan", "Notifikasi Service 12 Bulan", "Notifikasi Service 18 Bulan");

$i = 0;
foreach ($types as $key => $type) {
    $prefix = $prefixes[$key];
    $dateInterval = $dateIntervals[$key];
    $qBarang = $db->get_results("SELECT * FROM tb_barang WHERE $type='1' AND (IDBarang='2' OR IDBarang='65')");
    if ($qBarang) {
        foreach ($qBarang as $dBarang) {
            $sql = $dBarang->IsBarang == "2"
                ? "SELECT a.NoPenjualan AS NoFaktur, a.IDPenjualan, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS Tanggal, a.IDPelanggan, c.NamaPelanggan FROM tb_penjualan a, tb_penjualan_detail b, tb_pelanggan c WHERE a.NoPenjualan=b.NoPenjualan AND a.IDPelanggan=c.IDPelanggan AND b.IDBarang='$dBarang->IDBarang' AND DATE_ADD(a.Tanggal, INTERVAL $dateInterval MONTH)>=$date AND a.NoPenjualan NOT IN (SELECT NoFaktur FROM tb_notifikasi_service WHERE JenisNotifikasi='$type' AND IDBarang='$dBarang->IDBarang')"
                : "SELECT a.NoSuratJalan AS NoFaktur, a.IDPenjualan, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS Tanggal, c.IDPelanggan, d.NamaPelanggan FROM tb_penjualan_surat_jalan a, tb_penjualan_surat_jalan_detail b, tb_penjualan c, tb_pelanggan d WHERE a.NoSuratJalan=b.NoSuratJalan AND a.IDPenjualan=c.IDPenjualan AND c.IDPelanggan=d.IDPelanggan AND b.IDBarang='$dBarang->IDBarang' AND DATE_ADD(a.Tanggal, INTERVAL $dateInterval MONTH)>=$date AND a.NoSuratJalan NOT IN (SELECT NoFaktur FROM tb_notifikasi_service WHERE JenisNotifikasi='$type' AND IDBarang='$dBarang->IDBarang')";

            $qFaktur = $db->get_results($sql);
            if ($qFaktur) {
                foreach ($qFaktur as $dFaktur) {
                    $i++;
                    $tanggalAkhirMaintenance = date("Y-m-d", strtotime($dFaktur->Tanggal . " +$dateInterval month"));
                    $keterangan = "REMINDER NOTIFIKASI MAINTENANCE SERVICE " . ($dateInterval + 1) . " BULAN UNTUK PELANGGAN " . $dFaktur->NamaPelanggan . "; NO FAKTUR : " . $dFaktur->NoFaktur . "; TANGGAL : " . $dFaktur->Tanggal;
                    $db->query("INSERT INTO tb_notifikasi_service SET IDPenjualan='$dFaktur->IDPenjualan', NoFaktur='$dFaktur->NoFaktur', TanggalFaktur='$dFaktur->Tanggal', IDBarang='$dBarang->IDBarang', IDPelanggan='$dFaktur->IDPelanggan', TanggalAkhirMaintenance='$tanggalAkhirMaintenance', JenisNotifikasi='$type', Keterangan='$keterangan', Status='1'");
                }
            }
        }
    }
}

echo $i . " REMINDERS GENERATED";
