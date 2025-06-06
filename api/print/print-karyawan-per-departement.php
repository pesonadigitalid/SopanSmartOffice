<?php
session_start();
include_once "../config/connection.php";
$bulan = array("01"=>"Januari","02"=>"Februari","03"=>"Maret","04"=>"April","05"=>"Mei","06"=>"Juni","07"=>"Juli","08"=>"Agustus","09"=>"September",10=>"Oktober","11"=>"November",12=>"Desember");

function durationWork($m,$y,$d){
    $datetime1 = new DateTime($y."-".$m."-".$d);
    $datetime2 = new DateTime(date("Y-m-d"));
    $interval = $datetime1->diff($datetime2);
    $year = $interval->format('%y');
    $month = $interval->format('%m');

    $return = "";
    if($year>0) $return .= "$year Tahun ";
    $return .= "$month Bulan";

    return $return;
}

$status_karyawan = $_GET['status_karyawan'];
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
        <style>
        table, tr, td, th, tbody, thead, tfoot {
            page-break-inside: avoid !important;
        }
        body {
        counter-reset: section;
        }

        @page {
            @bottom-left {
                content: counter(page);
             }
         }
        </style>
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
            <h1 class="underline">** LAPORAN DATA KARYAWAN **</h1>
        </div>
        <table class="tabelList6" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th colspan="10" style="text-align: left">Departement : Non-Departement</th>
                </tr>
                <tr>
                    <th width="5">No.</th>
                    <th width="70">NIK</th>
                    <th width="150">Nama Karyawan</th>
                    <th width="30">JK</th>
                    <th width="100">Jabatan</th>
                    <th width="50">Status</th>
                    <th width="80">Tgl. Mulai Kerja</th>
                    <th>Alamat</th>
                    <th width="80">Telp</th>
                    <th width="110">TTL</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = newQuery("get_results","SELECT *, DATE_FORMAT(TglLahir,'%d/%m/%Y') AS TglLahirID FROM tb_karyawan WHERE Status='1' AND IDKaryawan>1 AND Nama!='Lukito Pramono MMS' AND IDDepartement='0' AND StatusKaryawan!='Harian' ORDER BY TahunMasuk ASC, BulanMasuk ASC, TanggalMasuk ASC");
                if($query){
                    foreach($query as $data){
                        $i++;
                        $jabatan = newQuery("get_var","SELECT Jabatan FROM tb_jabatan WHERE IDJabatan='".$data->IDJabatan."'");
                        ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo $data->NIK_Manual; ?></td>
                            <td><?php echo $data->Nama; ?></td>
                            <td><?php echo $data->JenisKelamin; ?></td>
                            <td><?php echo $jabatan; ?></td>
                            <td><?php echo $data->StatusKaryawan; ?></td>
                            <td><?php echo $data->TanggalMasuk.'/'.$data->BulanMasuk.'/'.$data->TahunMasuk; ?></td>
                            <td><?php echo $data->AlamatKTP; ?></td>
                            <td><?php echo $data->NoTelp; ?></td>
                            <td><?php echo $data->TempatLahir.", ".$data->TglLahirID; ?></td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='10'>Tidak ada karyawan dalam departement ini...</td></tr>";
                }
                ?>
            </tbody>
            <tfooter>
                <tr>
                    <th colspan="10" style="text-align: right;padding-right:20px;">Jumlah Karyawan : <?php echo $i; ?></th>
                </tr>
            </tfooter>
        </table><br/>

        <?php
        $qDepartement = newQuery("get_results","SELECT * FROM tb_departement ORDER BY NamaDepartement ASC");
        if($qDepartement){
            foreach($qDepartement as $dDepartement){
                $i = 0;
                $numKar = newQuery("get_var","SELECT COUNT(*) FROM tb_karyawan WHERE Status='1' AND IDKaryawan>1 AND Nama!='Lukito Pramono MMS' AND IDDepartement='".$dDepartement->IDDepartement."' AND StatusKaryawan!='Harian' ORDER BY TahunMasuk ASC, BulanMasuk ASC, TanggalMasuk ASC");
                if($numKar>0){
                    ?>
                    <table class="tabelList6" cellpadding="0" cellspacing="0">
                        <thead>
                            <tr>
                                <th colspan="10" style="text-align: left">Departement : <?php echo $dDepartement->NamaDepartement; ?></th>
                            </tr>
                            <tr>
                                <th width="5">No.</th>
                                <th width="70">NIK</th>
                                <th width="150">Nama Karyawan</th>
                                <th width="30">JK</th>
                                <th width="100">Jabatan</th>
                                <th width="50">Status</th>
                                <th width="80">Tgl. Mulai Kerja</th>
                                <th>Alamat</th>
                                <th width="80">Telp</th>
                                <th width="110">TTL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = newQuery("get_results","SELECT *, DATE_FORMAT(TglLahir,'%d/%m/%Y') AS TglLahirID FROM tb_karyawan WHERE Status='1' AND IDKaryawan>1 AND Nama!='Lukito Pramono MMS' AND IDDepartement='".$dDepartement->IDDepartement."' AND StatusKaryawan!='Harian' ORDER BY TahunMasuk ASC, BulanMasuk ASC, TanggalMasuk ASC");
                            if($query){
                                foreach($query as $data){
                                    $i++;
                                    $jabatan = newQuery("get_var","SELECT Jabatan FROM tb_jabatan WHERE IDJabatan='".$data->IDJabatan."'");
                                    ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $data->NIK_Manual; ?></td>
                                        <td><?php echo $data->Nama; ?></td>
                                        <td><?php echo $data->JenisKelamin; ?></td>
                                        <td><?php echo $jabatan; ?></td>
                                        <td><?php echo $data->StatusKaryawan; ?></td>
                                        <td><?php echo $data->TanggalMasuk.'/'.$data->BulanMasuk.'/'.$data->TahunMasuk; ?></td>
                                        <td><?php echo $data->AlamatKTP; ?></td>
                                        <td><?php echo $data->NoTelp; ?></td>
                                        <td><?php echo $data->TempatLahir.", ".$data->TglLahirID; ?></td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo "<tr><td colspan='10'>Tidak ada karyawan dalam departement ini...</td></tr>";
                            }
                            ?>
                        </tbody>
                        <tfooter>
                            <tr>
                                <th colspan="10" style="text-align: right;padding-right:20px;">Jumlah Karyawan : <?php echo $i; ?></th>
                            </tr>
                        </tfooter>
                    </table><br/>
                    <?php
                }
            }
        }
        ?>

        <?php
        $qDepartement = newQuery("get_results","SELECT * FROM tb_departement ORDER BY NamaDepartement ASC");
        if($qDepartement){
            foreach($qDepartement as $dDepartement){
                $i = 0;
                $numKar = newQuery("get_var","SELECT COUNT(*) FROM tb_karyawan WHERE Status='1' AND IDKaryawan>1 AND Nama!='Lukito Pramono MMS' AND IDDepartement='".$dDepartement->IDDepartement."' AND StatusKaryawan='Harian' ORDER BY TahunMasuk ASC, BulanMasuk ASC, TanggalMasuk ASC");
                if($numKar>0){
                    ?>
                    <table class="tabelList6" cellpadding="0" cellspacing="0">
                        <thead>
                            <tr>
                                <th colspan="10" style="text-align: left">Departement : <?php echo $dDepartement->NamaDepartement; ?></th>
                            </tr>
                            <tr>
                                <th width="5">No.</th>
                                <th width="70">NIK</th>
                                <th width="150">Nama Karyawan</th>
                                <th width="30">JK</th>
                                <th width="100">Jabatan</th>
                                <th width="50">Status</th>
                                <th width="80">Tgl. Mulai Kerja</th>
                                <th>Alamat</th>
                                <th width="80">Telp</th>
                                <th width="110">TTL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = newQuery("get_results","SELECT *, DATE_FORMAT(TglLahir,'%d/%m/%Y') AS TglLahirID FROM tb_karyawan WHERE Status='1' AND IDKaryawan>1 AND Nama!='Lukito Pramono MMS' AND IDDepartement='".$dDepartement->IDDepartement."' AND StatusKaryawan='Harian' ORDER BY TahunMasuk ASC, BulanMasuk ASC, TanggalMasuk ASC");
                            if($query){
                                foreach($query as $data){
                                    $i++;
                                    $jabatan = newQuery("get_var","SELECT Jabatan FROM tb_jabatan WHERE IDJabatan='".$data->IDJabatan."'");
                                    ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $data->NIK_Manual; ?></td>
                                        <td><?php echo $data->Nama; ?></td>
                                        <td><?php echo $data->JenisKelamin; ?></td>
                                        <td><?php echo $jabatan; ?></td>
                                        <td><?php echo $data->StatusKaryawan; ?></td>
                                        <td><?php echo $data->TanggalMasuk.'/'.$data->BulanMasuk.'/'.$data->TahunMasuk; ?></td>
                                        <td><?php echo $data->AlamatKTP; ?></td>
                                        <td><?php echo $data->NoTelp; ?></td>
                                        <td><?php echo $data->TempatLahir.", ".$data->TglLahirID; ?></td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo "<tr><td colspan='10'>Tidak ada karyawan dalam departement ini...</td></tr>";
                            }
                            ?>
                        </tbody>
                        <tfooter>
                            <tr>
                                <th colspan="10" style="text-align: right;padding-right:20px;">Jumlah Karyawan : <?php echo $i; ?></th>
                            </tr>
                        </tfooter>
                    </table><br/>
                    <?php
                }
            }
        }
        ?>
        
        <table class="asignment" style="margin-top: 20px;">
            <tr>
                <td class="center" width="60%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td class="center" width="40%">Mengetahui,<br /><br /><br /><br />( HRD )</td>
            </tr>
        </table>
        <script type="text/javascript">
            // window.onload = function () { window.print(); }
        </script>
    </body>
</html>
</body>
</html>