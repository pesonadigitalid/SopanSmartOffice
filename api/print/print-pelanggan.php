<?php
session_start();
include_once "../config/connection.php";
$id = $_GET['id'];
$bulan = array(01=>"Januari",02=>"Februari",03=>"Maret",04=>"April",05=>"Mei",06=>"Juni",07=>"Juli",08=>"Agustus",09=>"September",10=>"Oktober",11=>"November",12=>"Desember");
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
        <div class="laporanTitle">
            <h1 class="underline">** DATA PELANGGAN **</h1>
        </div>
        <div class="data-container">
        <?php
            $query = $db->get_row("SELECT a.*, b.NamaDepartement FROM tb_pelanggan a, tb_departement b WHERE a.Kategori=b.IDDepartement AND a.IDPelanggan='$id' ORDER BY IDPelanggan ASC");
            
            if($query->NamaKP1=="") $namakp1 = "-"; else $namakp1 = $query->NamaKP1;
            if($query->JabatanKP1=="") $jabatankp1 = "-"; else $jabatankp1 = $query->JabatanKP1;
            if($query->EmailKP1=="") $emailkp1 = "-"; else $emailkp1 = $query->EmailKP1;
            if($query->HPKP1=="") $hpkp1 = "-"; else $hpkp1 = $query->HPKP1;
            if($query->NamaKP2=="") $namakp2 = "-"; else $namakp2 = $query->NamaKP2;
            if($query->JabatanKP2=="") $jabatankp2 = "-"; else $jabatankp2 = $query->JabatanKP2;
            if($query->EmailKP2=="") $emailkp2 = "-"; else $emailkp2 = $query->EmailKP2;
            if($query->HPKP2=="") $hpkp2 = "-"; else $hpkp2 = $query->HPKP2;
            
            if($query->Jenis=="Perusahaan" || $query->Jenis=="Perorangan"){
                ?>
                <fieldset>
                    <label><strong>* Kode Pelanggan</strong></label>
                    <span style="margin-right: 5px;">:</span><span><?php echo $query->KodePelanggan; ?></span>
                </fieldset>
                <fieldset>
                    <label><strong>* Kategori</strong></label>
                    <span style="margin-right: 5px;">:</span><span><?php echo $query->NamaDepartement; ?></span>
                </fieldset>
                <fieldset>
                    <label><strong>* Jenis</strong></label>
                    <span style="margin-right: 5px;">:</span><span><?php echo $query->Jenis; ?></span>
                </fieldset>
                <fieldset>
                    <label><strong>1. Nama Perusahaan</strong></label>
                    <span style="margin-right: 5px;">:</span><span><?php echo $query->NamaPelanggan; ?></span>
                </fieldset>
                <fieldset>
                    <label><strong>2. Alamat</strong></label>
                    <span style="margin-right: 5px;">:</span><span><?php echo $query->Alamat; ?></span>
                </fieldset>
                <fieldset>
                    <label class="sub-label"><strong>* Kota</strong></label>
                    <span style="margin-right: 5px;">:</span><span><?php echo $query->Kota; ?></span>
                </fieldset>
                <fieldset>
                    <label class="sub-label"><strong>* Provinsi</strong></label>
                    <span style="margin-right: 5px;">:</span><span><?php echo $query->Provinsi; ?></span>
                </fieldset>
                <fieldset>
                    <label><strong>3. Nomor Telepon</strong></label>
                    <span style="margin-right: 5px;">:</span><span><?php echo $query->NoTelp; ?></span>
                </fieldset>
                <fieldset>
                    <label><strong>4. Nomor Fax</strong></label>
                    <span style="margin-right: 5px;">:</span><span><?php echo $query->NoFax; ?></span>
                </fieldset>
                <fieldset>
                    <label><strong>5. Kode Pos</strong></label>
                    <span style="margin-right: 5px;">:</span><span><?php echo $query->KodePos; ?></span>
                </fieldset>
                <fieldset>
                    <label><strong>6. Email</strong></label>
                    <span style="margin-right: 5px;">:</span><span><?php echo $query->Email; ?></span>
                </fieldset>
                <fieldset>
                    <label><strong>7. Web</strong></label>
                    <span style="margin-right: 5px;">:</span><span><?php echo $query->Website; ?></span>
                </fieldset>
                <h4>Kontak Person 1</h4>
                <fieldset>
                    <label><strong>1. Nama</strong></label>
                    <span style="margin-right: 5px;">:</span><span><?php echo $namakp1; ?></span>
                </fieldset>
                <fieldset>
                    <label><strong>2. Jabatan</strong></label>
                    <span style="margin-right: 5px;">:</span><span><?php echo $jabatankp1; ?></span>
                </fieldset>
                <fieldset>
                    <label><strong>3. Email</strong></label>
                    <span style="margin-right: 5px;">:</span><span><?php echo $emailkp1; ?></span>
                </fieldset>
                <fieldset>
                    <label><strong>4. HP</strong></label>
                    <span style="margin-right: 5px;">:</span><span><?php echo $hpkp1; ?></span>
                </fieldset>
                <h4>Kontak Person 2</h4>
                <fieldset>
                    <label><strong>1. Nama</strong></label>
                    <span style="margin-right: 5px;">:</span><span><?php echo $namakp2; ?></span>
                </fieldset>
                <fieldset>
                    <label><strong>2. Jabatan</strong></label>
                    <span style="margin-right: 5px;">:</span><span><?php echo $jabatankp2; ?></span>
                </fieldset>
                <fieldset>
                    <label><strong>3. Email</strong></label>
                    <span style="margin-right: 5px;">:</span><span><?php echo $emailkp2; ?></span>
                </fieldset>
                <fieldset>
                    <label><strong>4. HP</strong></label>
                    <span style="margin-right: 5px;">:</span><span><?php echo $hpkp2; ?></span>
                </fieldset>
                <?php
            } else {
                ?>
                <fieldset>
                    <label><strong>* Kode Pelanggan</strong></label>
                    <span style="margin-right: 5px;">:</span><span><?php echo $query->KodePelanggan; ?></span>
                </fieldset>
                <fieldset>
                    <label><strong>* Kategori</strong></label>
                    <span style="margin-right: 5px;">:</span><span><?php echo $query->Kategori; ?></span>
                </fieldset>
                <fieldset>
                    <label><strong>1. Nama</strong></label>
                    <span style="margin-right: 5px;">:</span><span><?php echo $query->NamaPelanggan; ?></span>
                </fieldset>
                <fieldset>
                    <label><strong>2. Alamat</strong></label>
                    <span style="margin-right: 5px;">:</span><span><?php echo $query->Alamat; ?></span>
                </fieldset>
                <fieldset>
                    <label class="sub-label"><strong>* Kota</strong></label>
                    <span style="margin-right: 5px;">:</span><span><?php echo $query->Kota; ?></span>
                </fieldset>
                <fieldset>
                    <label class="sub-label"><strong>* Provinsi</strong></label>
                    <span style="margin-right: 5px;">:</span><span><?php echo $query->Provinsi; ?></span>
                </fieldset>
                <fieldset>
                    <label><strong>3. Nomor Telepon</strong></label>
                    <span style="margin-right: 5px;">:</span><span><?php echo $query->NoTelp; ?></span>
                </fieldset>
                <fieldset>
                    <label><strong>4. Nomor Fax</strong></label>
                    <span style="margin-right: 5px;">:</span><span><?php echo $query->NoFax; ?></span>
                </fieldset>
                <fieldset>
                    <label><strong>5. Kode Pos</strong></label>
                    <span style="margin-right: 5px;">:</span><span><?php echo $query->KodePos; ?></span>
                </fieldset>
                <fieldset>
                    <label><strong>6. Alamat Email</strong></label>
                    <span style="margin-right: 5px;">:</span><span><?php echo $query->Email; ?></span>
                </fieldset>
                <?php
            }
        ?>
        </div>
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