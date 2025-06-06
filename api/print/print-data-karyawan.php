<?php
session_start();
include_once "../config/connection.php";
$id = $_GET['id'];
$bulan = array("01"=>"Januari","02"=>"Februari","03"=>"Maret","04"=>"April","05"=>"Mei","06"=>"Juni","07"=>"Juli","08"=>"Agustus","09"=>"September","10"=>"Oktober","11"=>"November","12"=>"Desember");
$cond = " WHERE ";
$jabatan = $_GET['jabatan'];
$status_karyawan = $_GET['status_karyawan'];
$departement = $_GET['departement'];

$getJab = $db->get_var("SELECT Jabatan FROM tb_jabatan WHERE IDJabatan='".$jabatan."'");
$getDepart = $db->get_var("SELECT NamaDepartement FROM tb_departement WHERE IDDepartement='".$departement."'");

$subtitle="";
if($jabatan!=""){
    $cond .= "IDJabatan='$jabatan' AND ";
    $subtitle .= "Jabatan <strong>".$getJab."</strong>, ";
}

if($status_karyawan!=""){
    $cond .= "StatusKaryawan='$status_karyawan' AND ";
    $subtitle .= "Status <strong>".$status_karyawan."</strong>, ";
}

if($departement!=""){
    $cond .= "IDDepartement='$departement' AND ";
    $subtitle .= "Departement <strong>".$getDepart."</strong>, ";
}

$subtitle = substr($subtitle, 0, -2);
$periode = "Periode : ".$bulan[date("m")]." ".date("Y");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <meta name="description" content=""/>
        <meta name="author" content="Yogi Pratama - Pesona Creative - 085737654543"/>
        
        <title>SOPAN Smart Office - Integrated System</title>
        
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
        <div class="laporanTitle" style="margin-bottom: 40px;">
            <h1 class="underline">** DATA KARYAWAN **</h1><?php echo $subtitle; ?>
        </div>
        <?php 
        $query = newQuery("get_results","SELECT * FROM tb_karyawan $cond IDKaryawan>1 ORDER BY IDKaryawan ASC");
        if($query){
            $i=1;
            foreach($query as $data){
                ?>
                <div class="data-container">
                    <?php
                        $total = $db->get_var("SELECT COUNT(*) FROM tb_karyawan $cond IDKaryawan>1 ORDER BY IDKaryawan ASC");
                        $query = $db->get_row("SELECT * FROM tb_karyawan WHERE IDKaryawan='".$data->IDKaryawan."' ORDER BY IDKaryawan ASC"); 
                        $jabatan = $db->get_var("SELECT Jabatan FROM tb_jabatan WHERE IDJabatan='".$query->IDJabatan."'");
                        $departement = $db->get_var("SELECT NamaDepartement FROM tb_departement WHERE IDDepartement='".$query->IDDepartement."'");
                        if($query->IDJabatan2=="" || $query->IDJabatan2=="0")
                            $jabatan2 = "Tidak Ada";
                        else
                            $jabatan2 = $db->get_var("SELECT Jabatan FROM tb_jabatan WHERE IDJabatan='".$query->IDJabatan2."'");
                        $bulan_masuk = $query->BulanMasuk;
                        if($query->JenisKelamin=="L") $jenis_kelamin="Laki - laki"; else $jenis_kelamin="Perempuan";
                    ?>
                    <div class="container">
                        <div class="img-karyawan">
                            <?php 
                                if($query->Foto!=""){
                                    ?><img src="https://lintasdaya.s3-ap-southeast-1.amazonaws.com/karyawan_photo_sopan/<?php echo $query->Foto; ?>" class="img-responsive" style="border: 1px solid #d8d8d8"/><?php
                                } else {
                                    ?><img src="./files/karyawan_photo/default.jpg" class="img-responsive" style="border: 1px solid #d8d8d8"/><?php
                                }
                            ?>
                        </div>
                        <div class="side-top">
                            <div class="list-data">
                                <div class="nama-label small-width">NIK</div>
                                <div class="titik-dua">:</div>
                                <div class="isi-label"><?php echo $query->NIK; ?></div>
                            </div>
                            <div class="list-data">
                                <div class="nama-label small-width">Jabatan I</div>
                                <div class="titik-dua">:</div>
                                <div class="isi-label"><?php echo $jabatan; ?></div>
                            </div>
                            <div class="list-data">
                                <div class="nama-label small-width">Jabatan II</div>
                                <div class="titik-dua">:</div>
                                <div class="isi-label"><?php echo $jabatan2; ?></div>
                            </div>
                            <div class="list-data">
                                <div class="nama-label small-width">Departement</div>
                                <div class="titik-dua">:</div>
                                <div class="isi-label"><?php echo $departement; ?></div>
                            </div>
                            <div class="list-data">
                                <div class="nama-label small-width">Tanggal Masuk</div>
                                <div class="titik-dua">:</div>
                                <div class="isi-label"><?php echo $bulan[$query->BulanMasuk]." ".$query->TahunMasuk; ?></div>
                            </div>
                            <div class="list-data">
                                <div class="nama-label small-width">Mark</div>
                                <div class="titik-dua">:</div>
                                <div class="isi-label"><?php echo $query->StatusLainnya; ?></div>
                            </div>
                            <div class="list-data">
                                <div class="nama-label small-width">Status</div>
                                <div class="titik-dua">:</div>
                                <div class="isi-label"><?php if($query->Status=="1") echo "Aktif"; else echo "Tidak Aktif"; ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="container">
                        <div class="side-left">
                            <div class="list-data">
                                <div class="nama-label side-title"><h4>Data Pribadi</h4></div>
                                <div class="titik-dua"></div>
                                <div class="isi-label"></div>
                            </div>
                            <div class="list-data">
                                <div class="nama-label">1. Nama</div>
                                <div class="titik-dua">:</div>
                                <div class="isi-label"><?php echo $query->Nama; ?></div>
                            </div>
                            <div class="list-data">
                                <div class="nama-label">2. Jenis Kelamin</div>
                                <div class="titik-dua">:</div>
                                <div class="isi-label"><?php echo $jenis_kelamin; ?></div>
                            </div>
                            <div class="list-data">
                                <div class="nama-label">3. Alamat Sementara</div>
                                <div class="titik-dua">:</div>
                                <div class="isi-label"><?php echo $query->AlamatSementara; ?></div>
                            </div>
                            <div class="list-data">
                                <div class="nama-label">4. Alamat Berdasarkan KTP</div>
                                <div class="titik-dua">:</div>
                                <div class="isi-label"><?php echo $query->AlamatKTP; ?></div>
                            </div>
                            <div class="list-data">
                                <div class="nama-label">5. Nomor Telepon</div>
                                <div class="titik-dua">:</div>
                                <div class="isi-label"><?php echo $query->NoTelp; ?></div>
                            </div>
                            <div class="list-data">
                                <div class="nama-label">6. Email Pribadi</div>
                                <div class="titik-dua">:</div>
                                <div class="isi-label"><?php echo $query->EmailPribadi; ?></div>
                            </div>
                            <div class="list-data">
                                <div class="nama-label side-title"><h4>Status</h4></div>
                                <div class="titik-dua"></div>
                                <div class="isi-label"></div>
                            </div>
                            <div class="list-data">
                                <div class="nama-label">1. Status Karyawan</div>
                                <div class="titik-dua">:</div>
                                <div class="isi-label"><?php echo $query->StatusKaryawan; ?></div>
                            </div>
                            <div class="list-data">
                                <div class="nama-label">2. Status Hubungan</div>
                                <div class="titik-dua">:</div>
                                <div class="isi-label"><?php echo $query->MartialStatus; ?></div>
                            </div>
                            <div class="list-data">
                                <div class="nama-label">3. Status Agama</div>
                                <div class="titik-dua">:</div>
                                <div class="isi-label"><?php echo $query->Agama; ?></div>
                            </div>
                        </div>
                        <div class="side-right">
                            <div class="list-data">
                                <div class="nama-label side-title"><h4>Data Keluarga</h4></div>
                                <div class="titik-dua"></div>
                                <div class="isi-label"></div>
                            </div>
                            <div class="list-data">
                                <div class="nama-label">1. Nama Ayah</div>
                                <div class="titik-dua">:</div>
                                <div class="isi-label"><?php echo $query->NamaAyah; ?></div>
                            </div>
                            <div class="list-data">
                                <div class="nama-label sub-label2">Alamat</div>
                                <div class="titik-dua">:</div>
                                <div class="isi-label"><?php echo $query->AlamatAyah; ?></div>
                            </div>
                            <div class="list-data">
                                <div class="nama-label sub-label2">Nomor Telepon</div>
                                <div class="titik-dua">:</div>
                                <div class="isi-label"><?php echo $query->NoTelpAyah; ?></div>
                            </div>
                            <div class="list-data">
                                <div class="nama-label">2. Nama Ibu</div>
                                <div class="titik-dua">:</div>
                                <div class="isi-label"><?php echo $query->NamaIbu; ?></div>
                            </div>
                            <div class="list-data">
                                <div class="nama-label sub-label2">Alamat</div>
                                <div class="titik-dua">:</div>
                                <div class="isi-label"><?php echo $query->AlamatIbu; ?></div>
                            </div>
                            <div class="list-data">
                                <div class="nama-label sub-label2">Nomor Telepon</div>
                                <div class="titik-dua">:</div>
                                <div class="isi-label"><?php echo $query->NoTelpIbu; ?></div>
                            </div>
                            <div class="list-data">
                                <div class="nama-label">3. Nama Suami / Istri</div>
                                <div class="titik-dua">:</div>
                                <div class="isi-label"><?php echo $query->NamaSuami; ?></div>
                            </div>
                            <div class="list-data">
                                <div class="nama-label sub-label2">Alamat</div>
                                <div class="titik-dua">:</div>
                                <div class="isi-label"><?php echo $query->AlamatSuami; ?></div>
                            </div>
                            <div class="list-data">
                                <div class="nama-label sub-label2">Nomor Telepon</div>
                                <div class="titik-dua">:</div>
                                <div class="isi-label"><?php echo $query->NoTelpSuami; ?></div>
                            </div>
                            <div class="list-data">
                                <div class="nama-label">4. Sodara / Wali</div>
                                <div class="titik-dua">:</div>
                                <div class="isi-label"><?php echo $query->NamaWali; ?></div>
                            </div>
                            <div class="list-data">
                                <div class="nama-label sub-label2">Alamat</div>
                                <div class="titik-dua">:</div>
                                <div class="isi-label"><?php echo $query->AlamatWali; ?></div>
                            </div>
                            <div class="list-data">
                                <div class="nama-label sub-label2">Nomor Telepon</div>
                                <div class="titik-dua">:</div>
                                <div class="isi-label"><?php echo $query->NoTelpWali; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="divider" <?php if($total==$i) echo "style='display: none;'";?>></div>
                <?php
                $i++;
            }
        } else {
            echo "<p>Tidak ada data yang dapat ditampilkan...</p>";
        }
        ?>
        <table class="asignment" style="margin-top: 20px;">
            <tr>
                <td class="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td class="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td class="center">Mengetahui,<br /><br /><br /><br /><br /><br />(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
            </tr>
        </table>
        <script type="text/javascript">
            window.onload = function () { window.print(); }
        </script>
    </body>
</html>
</body>
</html>