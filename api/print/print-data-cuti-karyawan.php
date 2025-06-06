<?php
session_start();
include_once "../config/connection.php";
include_once "../library/class.cuticalculation.php";
$bulan2 = array("01"=>"Januari","02"=>"Februari","03"=>"Maret","04"=>"April","05"=>"Mei","06"=>"Juni","07"=>"Juli","08"=>"Agustus","09"=>"September",10=>"Oktober","11"=>"November",12=>"Desember");

$bulan = $_GET['datestart'];
$tahun = $_GET['dateend'];
$subtitle = "Periode : ".$bulan2[$bulan]." ".$tahun;

$bulan2 = intval($bulan)-1;
if($bulan2<10) $bulan2 = "0".$bulan2;
if($bulan=="01"){
    $tahun2 = $tahun-1;
    $bulan2 = "12";
} else {
    $tahun2 = $tahun;
}

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
                    <th>Karyawan</th>
                    <th width="70">Departement</th>
                    <th>Jabatan</th>
                    <th>Sisa Cuti Bulan Lalu</th>
                    <th>Cuti Bulan Sekarang</th>
                    <th>Sisa Cuti</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = newQuery("get_results","SELECT * FROM tb_karyawan WHERE Status='1' AND IDKaryawan>1 ORDER BY Nama ASC");
                if($query){
                    foreach($query as $data){
                        $departement = newQuery("get_var","SELECT NamaDepartement FROM tb_departement WHERE IDDepartement='".$data->IDDepartement."'");
                        $jabatan = newQuery("get_var","SELECT Jabatan FROM tb_jabatan WHERE IDJabatan='".$data->IDJabatan."'");
                        $karyawan = $data->IDKaryawan;

                        $cutiBulanLalu = $cuti->getTotalCutiKaryawanSetahun($tahun2,$bulan2,$karyawan,"CUTI TAHUNAN");
                        $sisaCutiBulanLalu = $totalCutiTahunan-$cutiBulanLalu;
                        $cutiBulanSekarang = $cuti->getTotalCutiBulananKaryawan($tahun,$bulan,$karyawan,"CUTI TAHUNAN");
                        $sisaCuti = $sisaCutiBulanLalu-$cutiBulanSekarang;
                        ?>
                        <tr>
                            <td><?php echo $data->Nama; ?></td>
                            <td style="text-align: center;"><?php echo $departement; ?></td>
                            <td style="text-align: center;"><?php echo $jabatan; ?></td>
                            <td style="text-align: center;"><?php echo $sisaCutiBulanLalu; ?></td>
                            <td style="text-align: center;"><?php echo $cutiBulanSekarang; ?></td>
                            <td style="text-align: center;"><?php echo $sisaCuti; ?></td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='6'>Tidak ada data yang dapat ditampilkan...</td></tr>";
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