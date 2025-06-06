<?php
session_start();
include_once "../config/connection.php";
$id = $_GET['id'];

$bulan = array("01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", "09" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember");
$dataMaster = newQuery("get_row", "SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_penjualan WHERE IDPenjualan='" . $id . "'");
//$dataProyek = newQuery("get_row","SELECT * FROM tb_proyek WHERE IDProyek='".$dataPO->IDProyek."'");
$dataPelanggan = newQuery("get_row", "SELECT * FROM tb_pelanggan WHERE IDPelanggan='" . $dataMaster->IDPelanggan . "'");
$tanggalExp = explode("-", $dataMaster->Tanggal);
//$proyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='".$dataPO->IDProyek."'");


function terbilang($angka)
{
    $angka = (float)$angka;
    $bilangan = array('', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas');
    if ($angka < 12) {
        return $bilangan[$angka];
    } else if ($angka < 20) {
        return $bilangan[$angka - 10] . ' Belas';
    } else if ($angka < 100) {
        $hasil_bagi = (int)($angka / 10);
        $hasil_mod = $angka % 10;
        return trim(sprintf('%s Puluh %s', $bilangan[$hasil_bagi], $bilangan[$hasil_mod]));
    } else if ($angka < 200) {
        return sprintf('Seratus %s', terbilang($angka - 100));
    } else if ($angka < 1000) {
        $hasil_bagi = (int)($angka / 100);
        $hasil_mod = $angka % 100;
        return trim(sprintf('%s Ratus %s', $bilangan[$hasil_bagi], terbilang($hasil_mod)));
    } else if ($angka < 2000) {
        return trim(sprintf('Seribu %s', terbilang($angka - 1000)));
    } else if ($angka < 1000000) {
        $hasil_bagi = (int)($angka / 1000);
        $hasil_mod = $angka % 1000;
        return sprintf('%s Ribu %s', terbilang($hasil_bagi), terbilang($hasil_mod));
    } else if ($angka < 1000000000) {
        $hasil_bagi = (int)($angka / 1000000);
        $hasil_mod = $angka % 1000000;
        return trim(sprintf('%s Juta %s', terbilang($hasil_bagi), terbilang($hasil_mod)));
    } else if ($angka < 1000000000000) {
        $hasil_bagi = (int)($angka / 1000000000);
        $hasil_mod = fmod($angka, 1000000000);
        return trim(sprintf('%s Milyar %s', terbilang($hasil_bagi), terbilang($hasil_mod)));
    } else if ($angka < 1000000000000000) {
        $hasil_bagi = $angka / 1000000000000;
        $hasil_mod = fmod($angka, 1000000000000);
        return trim(sprintf('%s Triliun %s', terbilang($hasil_bagi), terbilang($hasil_mod)));
    } else {
        return 'Data Salah';
    }
}
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

<body class=" fupsize">
    <table class="header-bg">
        <tr style="padding-bottom: 100px !important;">
            <td width="80%" class="bottom" style="line-height: 1.5em;padding-top:7px;">
                <img src="print-logo-sopan.png" align="left" style="padding-right: 10px;margin-right: 20px;border-right: solid 2px #e11b22;margin-top:-7px;width: 200px;height: 85px;object-fit: contain;">
                Jl. Tukad Batanghari No. 42<br />
                Denpasar 80225, Bali<br />
                Phone. +62 823-2800-1818<br />
                Email. mail.aristonbali@gmail.com
            </td>
            <td width="20%" align="right" style="padding-top:20px">

            </td>
        </tr>
    </table>
    <table style="margin-top:30px;">
        <tr>
            <td width="50%" class="" style="padding-top: 20px !important;vertical-align: middle;">
                <h3 class="title-print" style="font-size: 20px !important">PO RINCIAN</h3>
            </td>
            <td width="50%" align="right" style="padding-top: 20px !important;vertical-align: middle;">
                <div class="sideLabel">
                    <label>NOMOR</label><br />
                    <label>NO. PO KONSUMEN</label><br />
                    <label>TANGGAL</label><br />
                    <label>NO. SPH</label><br />
                </div>
                <div class="sideText">
                    <span><?php echo $dataMaster->NoPenjualan; ?></span><br />
                    <span><?php echo $dataMaster->NoPOKonsumen; ?></span><br />
                    <span><?php echo $dataMaster->TanggalID; ?></span><br />
                    <span><?php echo $dataMaster->NoSPH; ?></span><br />
                </div>
            </td>
        </tr>
    </table>
    <p>Berikut rincian permintaan barang sesuai dengan nomor PO yang tercantum:</p>
    <table class="tabelList2 border-solid" cellpadding="0" cellspacing="0" style="margin-top: 40px;">
        <thead>
            <tr>
                <th width="30">No.</th>
                <th>Nama Barang</th>
                <th width="40">Qty</th>
                <th width="60">Satuan</th>
                <th width="80">Harga Beli</th>
                <th width="80">Harga Jual</th>
                <th width="80">Diskon</th>
                <th width="80">Harga Setelah Diskon</th>
                <th width="80">Sub Total</th>
                <th width="60">Margin</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = $db->get_results("SELECT * FROM tb_penjualan_detail WHERE NoPenjualan='" . $dataMaster->NoPenjualan . "' ORDER BY NoUrut ASC");
            if ($query) {
                $i = 1;
                $qty = 0;
                $total = 0;
                foreach ($query as $data) {
                    $dbarang = $db->get_row("SELECT a.*, b.Nama AS Satuan FROM tb_barang a, tb_satuan b WHERE a.IDBarang='" . $data->IDBarang . "' AND b.IDSatuan=a.IDSatuan");
            ?>
                    <tr class="border-bottom">
                        <td style="text-align: center;"><?php echo $i; ?></td>
                        <td><?php echo $data->NamaBarang; ?></td>
                        <td style="text-align: center;"><?php echo number_format($data->Qty); ?></td>
                        <td style="text-align: center;"><?php echo $dbarang->Satuan; ?></td>
                        <td style="text-align: right;"><?php echo number_format($data->HargaBeli); ?></td>
                        <td style="text-align: right;"><?php echo number_format($data->Harga); ?></td>
                        <td style="text-align: right;"><?php echo strpos($data->Diskon, "%") ? $data->Diskon : number_format($data->Diskon); ?></td>
                        <td style="text-align: right;"><?php echo number_format($data->HargaDiskon); ?></td>
                        <td style="text-align: right;"><?php echo number_format($data->SubTotal); ?></td>
                        <td style="text-align: right;"><?php echo number_format($data->Margin); ?></td>
                    </tr>
            <?php
                    $i++;
                    $qty += $data->Qty;
                    $total += $data->SubTotal;
                }
            }

            $margin = $dataMaster->TotalMargin;
            $margindiskon = ($dataMaster->DiskonPersen / 100 * $margin);
            $margintotal = $margin - $margindiskon;
            $margippn = ($dataMaster->PPNPersen / 100 * $margintotal);
            $margingrandtotal = $margintotal + $margippn;
            ?>
            <tr>
                <td colspan="8" style="text-align: right;"><strong>Sub Total : </strong></td>
                <td style="text-align: right;"><?php echo number_format($dataMaster->Total); ?></td>
                <td style="text-align: right;"><?php echo number_format($dataMaster->TotalMargin); ?></td>
            </tr>
            <tr style="display:none">
                <td colspan="8" style="text-align: right;"><strong>Diskon <?php if ($dataMaster->DiskonPersen > 0) echo number_format($dataMaster->DiskonPersen) . "%"; ?> : </strong></td>
                <td style="text-align: right;"><?php echo number_format($dataMaster->Diskon); ?></td>
                <td style="text-align: right;"><?php echo number_format($margindiskon); ?></td>
            </tr>
            <tr>
                <td colspan="8" style="text-align: right;"><strong>PPN <?php if ($dataMaster->PPNPersen > 0) echo number_format($dataMaster->PPNPersen) . "%"; ?> : </strong></td>
                <td style="text-align: right;"><?php echo number_format($dataMaster->PPN); ?></td>
                <td style="text-align: right;"><?php echo number_format($marginppn); ?></td>
            </tr>
            <tr>
                <td colspan="8" style="text-align: right;"><strong>Grand Total : </strong></td>
                <td style="text-align: right;"><?php echo number_format($dataMaster->GrandTotal); ?></td>
                <td style="text-align: right;"><?php echo number_format($margingrandtotal); ?></td>
            </tr>
        </tbody>
    </table><br />
    <table class="asignment" style="margin-top: 10px;">
        <tr>
            <?php $karyawan = $db->get_row("SELECT * FROM tb_karyawan WHERE IDKaryawan='" . $dataMaster->CreatedBy . "'"); ?>
            </td>
            <td class="center" width="30%" style="padding-top:15px"></td>
            </td>
            <td class="center" width="30%" style="padding-top:15px"></td>
            <td class="center" width="30%" style="padding-top:15px">Mengetahui,<br /><br /><br /><br /><br /><strong>( Auryn )</strong>
            </td>
        </tr>
    </table>
    <script type="text/javascript">
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>