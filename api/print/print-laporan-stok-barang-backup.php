<?php
session_start();

include_once "../config/connection.php";
$bulan = array("01"=>"Januari","02"=>"Februari","03"=>"Maret","04"=>"April","05"=>"Mei","06"=>"Juni","07"=>"Juli","08"=>"Agustus","09"=>"September",10=>"Oktober","11"=>"November",12=>"Desember");

$tipe = $_GET['tipe'];
if($tipe=="1") $sub = "GUDANG";  else $sub = "PURCHASING";

$periode = "Sampai dengan ".date("d/m/Y");
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
            <h1 class="underline">** LAPORAN STOK BARANG <?php echo $sub; ?> **</h1><?php echo $periode; ?>
        </div>
        <?php if($tipe=="1"){ ?>
            <table class="tabelList2" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th width="30">No</th>
                        <th width="100">Kode Barang</th>
                        <th>Nama Barang</th>
                        <th width="150">Jenis</th>
                        <th width="100">Stok Gudang</th>
                        <th width="150">Total Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totalHPP = 0;
                    $query = newQuery("get_results","SELECT * FROM tb_barang WHERE IDBarang>0 AND Kategori<>'4' ORDER BY IDBarang ASC");
                    if($query){
                        $i=1;
                        foreach($query as $data){
                            if($data->StokGudang>0){
                                $jenis = $db->get_var("SELECT Nama FROM tb_jenis_material WHERE IDMaterial='".$data->IDJenis."'");
                                $jumlahStok = $db->get_var("SELECT SUM(SisaStok) FROM tb_stok_gudang WHERE IDBarang='".$data->IDBarang."'");
                                //$hpp = $db->get_var("SELECT SUM((Harga*SisaStok)) FROM tb_stok_gudang WHERE IDBarang='".$data->IDBarang."' AND SisaStok>0");
                                $hpp = getHPPAvg($data->IDBarang);
                                $hpp = $hpp*$data->StokGudang;

                                if($data->StokGudang!=$jumlahStok) $style="color:red;"; else $style="";
                                ?>
                                <tr>
                                    <td style="text-align: center;"><?php echo $i;?></td>
                                    <td style="text-align: center;<?php echo $style; ?>"><?php echo $data->KodeBarang;?></td>
                                    <td><?php echo $data->Nama;?></td>
                                    <td><?php echo $jenis;?></td>
                                    <td style="text-align: center;"><?php echo $data->StokGudang;?></td>
                                    <td style="text-align: right;"><?php echo number_format($hpp,2); ?></td>
                                </tr>
                                <?php
                                $totalHPP += $hpp;
                                $i++;
                            }
                        }
                        ?>
                        <tr>
                            <td style="text-align: right;" colspan="5"><strong>Total Nilai Persediaan : </strong></td>
                            <td style="text-align: right;"><?php echo number_format($totalHPP,2); ?></td>
                        </tr>
                        <?php
                    } else {
                        echo "<td colspan='6'>Tidak ada data yang dapat ditampilkan...</td>";
                    }
                    ?>
                </tbody>
            </table>
        <?php } else { ?>
            <table class="tabelList2" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th width="30">No</th>
                        <th width="70">Kode Barang</th>
                        <th width="120">Nama Barang</th>
                        <th width="100">Jenis</th>
                        <th width="100">Stok Purchasing</th>
                        <th width="100">Total Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = newQuery("get_results","SELECT * FROM tb_barang WHERE IDBarang>0 AND Kategori<>'4' ORDER BY IDBarang ASC");
                    if($query){
                        $i=1;
                        foreach($query as $data){
                            if($data->StokPurchasing>0){
                                $jenis = $db->get_var("SELECT Nama FROM tb_jenis_material WHERE IDMaterial='".$data->IDJenis."'");
                                $jumlahStok = $db->get_var("SELECT SUM(SisaStok) FROM tb_stok_purchasing WHERE IDBarang='".$data->IDBarang."'");
                                $hpp = $db->get_var("SELECT SUM((Harga*SisaStok)) FROM tb_stok_purchasing WHERE IDBarang='".$data->IDBarang."' AND SisaStok>0");
                                if($data->StokGudang!=$jumlahStok) $style="color:red;"; else $style="";
                                ?>
                                <tr>
                                    <td style="text-align: center;"><?php echo $i;?></td>
                                    <td style="text-align: center;"><?php echo $data->KodeBarang;?></td>
                                    <td><?php echo $data->Nama;?></td>
                                    <td><?php echo $jenis;?></td>
                                    <td style="text-align: center;"><?php echo $data->StokPurchasing;?></td>
                                    <td style="text-align: right;"><?php echo number_format($hpp,2); ?></td>
                                </tr>
                                <?php
                                $totalHPP += $hpp;
                                $i++;
                            }
                        }
                        ?>
                        <tr>
                            <td style="text-align: right;" colspan="5"><strong>Total Nilai Persediaan : </strong></td>
                            <td style="text-align: right;"><?php echo number_format($totalHPP,2); ?></td>
                        </tr>
                        <?php
                    } else {
                        echo "<td colspan='6'>Tidak ada data yang dapat ditampilkan...</td>";
                    }
                    ?>
                </tbody>
            </table>
        <?php } ?>
        
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