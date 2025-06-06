<?php
session_start();
include_once "../config/connection.php";
$bulan = array("01"=>"Januari","02"=>"Februari","03"=>"Maret","04"=>"April","05"=>"Mei","06"=>"Juni","07"=>"Juli","08"=>"Agustus","09"=>"September",10=>"Oktober","11"=>"November",12=>"Desember");

$filtertahun = $_GET['tahun'];
$status = $_GET['status'];
$nama_proyek = $_GET['nama_proyek'];

if($_GET['id_proyek']) $cond = "AND a.IDProyek='".$_GET['id_proyek']."'";
else if($_GET['stts']) $cond = "AND a.Status='".$_GET['stts']."'";

if($filtertahun!=""){
    $cond .= " AND a.Tahun='$filtertahun'";
    $subtitle .= "Tahun <strong>".$filtertahun."</strong>, ";
}

if($status!="all"){
    if($status=="0") $stts="Tender"; else if($status=="1") $stts="Fail"; else if($status=="2") $stts="Process"; else $stts="Complete";
    $cond .= " AND a.Status='$status'";
    $subtitle .= "Status <strong>".$stts."</strong>, ";
}

if($nama_proyek!=""){
    $cond = "AND a.NamaProyek LIKE '%$nama_proyek%'";
    $subtitle .= "Nama Proyek <strong style='text-transform:capitalize;'>".$nama_proyek."</strong>, ";
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
        <div class="laporanTitle">
            <h1 class="underline">** LAPORAN PROYEK **</h1><?php echo $subtitle; ?>
        </div>
        <table class="tabelList2" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th width="30">No</th>
                    <th width="70">Kode</th>
                    <th width="120">Proyek</th>
                    <th width="100">Departement</th>
                    <th width="100">Pelaksana</th>
                    <th width="100">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = newQuery("get_results","SELECT a.*, b.NamaPelanggan, c.NamaDepartement FROM tb_proyek a, tb_pelanggan b, tb_departement c WHERE a.IDClient=b.IDPelanggan AND a.IDDepartement=c.IDDepartement $cond ORDER BY a.IDProyek DESC");
                if($query){
                    $i=1;
                    foreach($query as $data){
                        $departementPemilik = $db->get_var("SELECT NamaDepartement FROM tb_departement WHERE IDDepartement='".$data->IDDepartementPemilik."'");
                        if($data->Status=="0") $status="Tender"; else if($data->Status=="1") $status="Fail"; else if($data->Status=="2") $status="Process"; else $status="Complete";
                        ?>
                        <tr>
                            <td style="text-align: center;"><?php echo $i;?></td>
                            <td style="text-align: center;"><?php echo $data->KodeProyek;?></td>
                            <td><?php echo $data->NamaProyek;?></td>
                            <td><?php echo $departementPemilik;?></td>
                            <td><?php echo $data->NamaDepartement;?></td>
                            <td><?php echo $status;?></td>
                        </tr>
                        <?php
                        $i++;
                    }
                } else {
                    echo "<td colspan='6'>Tidak ada data yang dapat ditampilkan...</td>";
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
            window.onload = function () { window.print(); }
        </script>
    </body>
</html>
</body>
</html>