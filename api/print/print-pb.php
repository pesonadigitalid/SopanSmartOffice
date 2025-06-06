<?php
session_start();
include_once "../config/connection.php";
$id = $_GET['id'];

$bulan = array("01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", "09" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember");
$dataPB = newQuery("get_row", "SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_pb WHERE NoPB='" . $id . "'");

$tanggalExp = explode("-", $dataPB->Tanggal);
$proyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='" . $dataPB->IDProyek . "'");

$ProjectManager = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $proyek->ProjectManager . "'");
if (!$ProjectManager) $ProjectManager = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

$SiteManager = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='" . $proyek->SiteManager . "'");
if (!$SiteManager) $SiteManager = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

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

<body>
    <table>
        <tr style="padding-bottom: 100px !important;">
            <td width="80%" class="bottom" style="line-height: 1.5em">
                <img src="print-logo.png" align="left" style="padding-right: 10px;margin-right: 20px;border-right: solid 2px #02b1e1;">
                Jl. Tukad Citarum Blok I No. 7B Renon, Perum Surya Graha Asih <br />Denpasar Bali<br />
                Phone : +62 361 - 238055<br />
                Fax : +62 361 - 238055<br />
                Email : mail@lintasdaya.com
            </td>
            <td width="20%" align="right" style="padding-top:20px">
                <img src="print-logo2.png">
            </td>
        </tr>
    </table>
    <table style="margin-top:30px;">
        <tr>
            <td width="50%">
                <h3 class="title-print">PERMINTAAN BARANG</h3>
            </td>
            <td width="50%" align="right">
                <div class="sideLabel">
                    <label>NO. PB</label><br />
                    <label>TANGGAL</label><br />
                    <label>KODE PROYEK</label><br />
                    <label>NAMA PROYEK</label><br />
                </div>
                <div class="sideText">
                    <span><?php echo $dataPB->NoPB; ?></span><br />
                    <span><?php echo $dataPB->TanggalID; ?></span><br />
                    <span><?php if ($proyek) echo $dataPB->KodeProyek . " / " . $proyek->Tahun;
                            else echo "UMUM"; ?></span><br />
                    <span><?php if ($proyek) echo $proyek->NamaProyek;
                            else echo "UMUM"; ?></span><br />
                </div>
            </td>
        </tr>
    </table>
    <table class="tabelList2 border-solid" cellpadding="0" cellspacing="0" style="margin-top: 40px;">
        <thead>
            <tr>
                <th width="30">No.</th>
                <th>Nama Barang</th>
                <th width="40">Qty</th>
                <th width="60">Satuan</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = $db->get_results("SELECT * FROM tb_pb_detail WHERE NoPB='$id' ORDER BY NoUrut ASC");
            if ($query) {
                $i = 1;
                $qty = 0;
                $total = 0;
                foreach ($query as $data) {
                    $satuan = $db->get_var("SELECT a.Nama FROM tb_satuan a, tb_barang b WHERE a.`IDSatuan`=b.`IDSatuan` AND b.`IDBarang`='" . $data->IDBarang . "'");
                    $barang = $db->get_row("SELECT * FROM tb_barang WHERE IDBarang='" . $data->IDBarang . "'");
            ?>
                    <tr class="border-bottom">
                        <td style="text-align: center;"><?php echo $i; ?></td>
                        <td>
                            <?php echo $data->NamaBarang; ?>
                            <?php if ($barang->Deskripsi != "") echo "<br/>" . nl2br($barang->Deskripsi); ?>
                        </td>
                        <td style="text-align: center;"><?php echo $data->Qty; ?></td>
                        <td style="text-align: center;"><?php echo $satuan; ?></td>
                    </tr>
            <?php
                    $i++;
                    $qty += $data->Qty;
                    $total += $data->SubTotal;
                }
            }
            ?>
            <tr>
                <td colspan="3" style="text-align: right;"><strong>TOTAL ITEM</strong></td>
                <td style="text-align: center;"><?php echo number_format($dataPB->TotalItem); ?></td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right;"><strong>TOTAL QTY</strong></td>
                <td style="text-align: center;"><?php echo number_format($dataPB->TotalQty); ?></td>
            </tr>
        </tbody>
    </table>
    <table class="asignment print-friendly" style="margin-top: 10px;">
        <tr>
            <td colspan="3">
                <table width="100%">
                    <tr>
                        <td width="130"><strong>Keterangan</strong></td>
                        <td width="10">:</td>
                        <td><?php echo $dataPB->Keterangan; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td width="30%" class="center" style="padding-top:15px"> </td>
            <td class="center" width="30%" style="padding-top:15px"><strong>Site Manager</strong><br /><br /><br /><br /><strong>( <?php echo $SiteManager; ?> )</</td> <td class="center" width="30%" style="padding-top:15px"><strong>Project Manager</strong><br /><br /><br /><br /><strong>( <?php echo $ProjectManager; ?> )</strong>
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