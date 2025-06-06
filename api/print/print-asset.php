<?php
session_start();
include_once "../config/connection.php";
$bulan = array("01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", "09" => "September", 10 => "Oktober", "11" => "November", 12 => "Desember");
$cond = "";
$karyawan = $_GET['id_karyawan'];
$kategori = $_GET['kategori'];
$status = $_GET['status'];

$getKaryawan = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='$karyawan'");
$getAssetCat = $db->get_var("SELECT Nama FROM tb_asset_category WHERE IDAssetCategory='$kategori'");

if ($karyawan != "") {
    $cond .= " AND IDKaryawan='$karyawan' ";
    $subtitle .= "Karyawan <strong>" . $getKaryawan . "</strong>, ";
}

if ($kategori != "") {
    $cond .= " AND IDAssetCategory='$kategori' ";
    $subtitle .= "Kategori <strong>" . $getAssetCat . "</strong>, ";
}

if ($status != "") {
    $cond .= " AND Status='$status' ";
    if ($status == "1")
        $subtitle .= "Status Aktif, ";
    else
        $subtitle .= "Status Tidak Aktif, ";
}

$subtitle = substr($subtitle, 0, -2);
// $cond = substr($cond, 0, -5);
$periode = "Periode : " . $bulan[date("m")] . " " . date("Y");
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="author" content="Yogi Pratama - Pesona Creative - 085737654543" />

    <title>SOPAN Smart Office - Integrated System</title>

    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />
    <link rel="stylesheet" href="print-style.css" media="all" type="text/css" />
</head>

<body>
    <table>
        <tr>
            <td width="50%" class="bottom">
                <h1>CV. Solusi Pemanas Air Nusantara</h1>
                Jl. Tukad Yeh Aya No.70b, Panjer, Denpasar Selatan, Kota Denpasar, Bali 80234<br />
                Telp. (0361) 8497915, Fax. -<br />
                User : <?php echo $_SESSION["name"]; ?>
            </td>
            <td width="50%" align="right" class="bottom">
                Tanggal Cetak : <?php echo date("d/m/Y"); ?>
            </td>
        </tr>
    </table>
    <div class="laporanTitle">
        <h1 class="underline">** LAPORAN ASSET **</h1><?php echo $subtitle; ?>
    </div>
    <table class="tabelList2" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th width="30">No</th>
                <th width="70">Kode Asset</th>
                <th width="120">Kategori</th>
                <th>Nama</th>
                <th width="100">Tahun Beli</th>
                <th width="150">Lokasi Sekarang</th>
                <th width="80">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = newQuery("get_results", "SELECT *, DATE_FORMAT(TanggalBeli,'%Y') AS Tahun FROM tb_asset WHERE Jenis!='Ijin-Usaha' $cond ORDER BY IDAsset ASC");
            if ($query) {
                $i = 1;
                foreach ($query as $data) {
                    $namaKaryawan = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $data->IDKaryawan . "'");
                    $namaKategori = $db->get_var("SELECT Nama FROM tb_asset_category WHERE IDAssetCategory='" . $data->IDAssetCategory . "'");
                    if ($data->Tahun != "0000") $tahun = $data->Tahun;
                    else $tahun = "";

                    if ($data->IDKaryawan != "") {
                        $tanggal_assign = $db->get_var("SELECT DATE_FORMAT(a.Tanggal,'%d/%m/%Y') FROM tb_assign a, tb_assign_detail b WHERE a.IDAssign=b.IDAssign AND b.IDAsset='$data->IDAsset' AND a.IDKaryawan='$data->IDKaryawan'");
                    } else {
                        $tanggal_assign = "";
                    }
            ?>
                    <tr>
                        <td style="text-align: center;"><?php echo $i; ?></td>
                        <td><?php echo $data->KodeAsset; ?></td>
                        <td><?php echo $namaKategori; ?></td>
                        <td>
                            <?php echo $data->Nama; ?>
                        </td>
                        <td><?php echo $tahun; ?></td>
                        <td>
                            <?php echo $namaKaryawan; ?>
                            <?php if ($tanggal_assign != "") echo "<br/>Tgl Assign: " . $tanggal_assign; ?>
                        </td>
                        <td><?php echo ($data->Status == "1") ? "Aktif" : "Tidak Aktif"; ?></td>
                    </tr>
            <?php
                    $i++;
                }
            } else {
                echo "<td colspan='5'>Tidak ada data yang dapat ditampilkan...</td>";
            }
            ?>
        </tbody>
    </table>
    <table class="asignment" style="margin-top: 20px;">
        <tr>
            <td class="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td class="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td class="center">Mengetahui,<br /><br /><br /><br /><br /><br />(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
        </tr>
    </table>
    <script type="text/javascript">
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>
</body>

</html>