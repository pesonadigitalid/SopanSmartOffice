<?php
session_start();
include_once "../config/connection.php";
include_once "../library/class.cuticalculation.php";
$bulan2 = array("01"=>"Januari","02"=>"Februari","03"=>"Maret","04"=>"April","05"=>"Mei","06"=>"Juni","07"=>"Juli","08"=>"Agustus","09"=>"September",10=>"Oktober","11"=>"November",12=>"Desember");

$bulan = $_GET['datestart'];
$tahun = $_GET['dateend'];
$subtitle = "Periode : ".$bulan2[$bulan]." ".$tahun;

$cuti = new CutiCalculation;

$totalCutiTahunan = newQuery("get_var","SELECT VALUE FROM tb_system_config WHERE label='JUMLAHCUTITAHUNAN'");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <meta name="description" content=""/>
        <meta name="author" content="Yogi Pratama - Pesona Creative - 085737654543"/>
        
        <title>SOPAN Smart Office - Smart office for smart people</title>
        
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport"/>
        <link rel="stylesheet" href="print-style.css" media="all" type="text/css"/>
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
            <h1 class="underline">** LAPORAN DATA CUTI KARYAWAN **</h1><?php echo $subtitle; ?>
        </div>
        <table class="tabelList2" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th width="20">No</th>
                    <th width="60">NIK</th>
                    <th width="200">Karyawan</th>
                    <th width="60">Tgl. Mulai</th>
                    <th width="70">Tgl. Selesai</th>
                    <th width="60">Lama Cuti</th>
                    <th width="150">Alamat Cuti</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = newQuery("get_results","SELECT a.*, b.Nama, b.NIK, DATE_FORMAT(a.DariTanggal,'%d/%m/%Y') AS DariTanggalID, DATE_FORMAT(a.SampaiTanggal,'%d/%m/%Y') AS SampaiTanggalID FROM tb_cuti a, tb_karyawan b WHERE a.`IDKaryawan`=b.`IDKaryawan` AND DATE_FORMAT(a.DariTanggal,'%Y-%m')='$tahun-$bulan' AND a.Status='2' AND b.IDKaryawan>1 AND b.Status='1' ORDER BY b.Nama ASC");
                if($query){
                    $i=0;
                    foreach($query as $data){
                        $i++;
                        ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo $data->NIK; ?></td>
                            <td><?php echo $data->Nama; ?></td>
                            <td><?php echo $data->DariTanggalID; ?></td>
                            <td><?php echo $data->SampaiTanggalID; ?></td>
                            <td><?php echo $data->JumlahHari; ?></td>
                            <td><?php echo $data->Lokasi; ?></td>
                            <td><?php echo $data->Keterangan; ?></td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='8'>Tidak ada data yang dapat ditampilkan...</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <table class="asignment" style="margin-top: 20px;">
            <tr>
                <td class="center" width="60%"></td>
                <td class="center" width="40%">Mengetahui,<br /><br /><br /><br />( HRD )</td>
            </tr>
        </table>
        <script type="text/javascript">
            window.onload = function () { window.print(); }
        </script>
    </body>
</html>
</body>
</html>