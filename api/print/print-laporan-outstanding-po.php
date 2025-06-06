<?php
session_start();
include_once "../config/connection.php";
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
            <h1 class="underline">** LAPORAN OUTSTANDING PO **</h1>
        </div>
        <table class="tabelList2" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th width="70">No. PO</th>
                    <th width="120">Tanggal</th>
                    <th width="150">Proyek</th>
                    <th width="150">Supplier</th>
                    <th>Nama Barang</th>
                    <th width="100">Qty</th>
                    <th width="100">Diterima</th>
                    <th width="100">Sisa</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = newQuery("get_results","SELECT a.NoPO, a.IDProyek, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID, a.IDSupplier, b.IDBarang, b.NamaBarang, b.Qty FROM tb_po a, tb_po_detail b WHERE a.NoPO=b.NoPO AND a.JenisPO='1'");
                if($query){
                    $total = 0;
                    foreach($query as $data){
                        if($data->IDProyek>0){
                            $proyek = $db->get_var("SELECT NamaProyek FROM tb_proyek WHERE IDProyek='".$data->IDProyek."'");
                        } else {
                            $proyek = "UMUM";
                        }
                        $supplier = $db->get_var("SELECT NamaPerusahaan FROM tb_supplier WHERE IDSupplier='".$data->IDSupplier."'");
                        $diterima = $db->get_var("SELECT SUM(a.Qty) FROM tb_penerimaan_stok_detail a, tb_penerimaan_stok b WHERE a.NoPenerimaanBarang=b.NoPenerimaanBarang AND b.NoPO='".$data->NoPO."' AND a.IDBarang='".$data->IDBarang."'");
                        if(!$diterima) $diterima=0;
                        $sisa = $data->Qty - $diterima;
                        if($sisa>0){
                            ?>
                            <tr>
                                <td style="text-align: center;"><?php echo $data->NoPO; ?></td>
                                <td style="text-align: center;"><?php echo $data->TanggalID; ?></td>
                                <td><?php echo $proyek; ?></td>
                                <td><?php echo $supplier; ?></td>
                                <td><?php echo $data->NamaBarang; ?></td>
                                <td style="text-align: center;"><?php echo $data->Qty; ?></td>
                                <td style="text-align: center;"><?php echo $diterima; ?></td>
                                <td style="text-align: center;"><?php echo $sisa; ?></td>
                            </tr>
                            <?php
                        }
                    }
                } else {
                    echo "<tr><td colspan='7'>Tidak ada data yang dapat ditampilkan...</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <table class="asignment" style="margin-top: 20px;">
            <tr>
                <td class="center" width="60%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td class="center" width="40%">Mengetahui,<br /><br /><br /><br />( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</td>
            </tr>
        </table>
        <script type="text/javascript">
            window.onload = function () { window.print(); }
        </script>
    </body>
</html>
</body>
</html>