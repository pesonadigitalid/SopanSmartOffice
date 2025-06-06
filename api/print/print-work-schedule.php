<?php
session_start();
include_once "../config/connection.php";
$id = $_GET['id'];

$bulan = array("01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", "09" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember");
$dataMaster = newQuery("get_row", "SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_work_schedule WHERE IDWorkSchedule='" . $id . "'");
$dataSPB = newQuery("get_row", "SELECT * FROM tb_penjualan WHERE IDPenjualan='" . $dataMaster->RefID . "'");
$dataPelanggan = newQuery("get_row", "SELECT * FROM tb_pelanggan WHERE IDPelanggan='" . $dataMaster->IDPelanggan . "'");
$tanggalExp = explode("-", $dataMaster->Tanggal);
$qTeknisi = newQuery("get_results", "SELECT * FROM tb_karyawan WHERE IDKaryawan IN (" . $dataMaster->IDsKaryawan . ")");
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

<body class="fupsize" style="padding: 0 10px;">
    <?php
    $perusahaan = "Solusi Pemanas Air Nusantara";
    ?>
    <table class="header-bg">
        <tr>
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
    <table style="margin-top:10px;">
        <tr>
            <td width="50%" class="" style="padding-top: 20px !important;vertical-align: middle;">
                <h3 class="title-print" style="font-size: 20px !important">WORK ORDER</h3>
            </td>
            <td width="50%" align="right" style="padding-top: 20px !important;vertical-align: middle;">
                <div class="sideLabel">
                    <label>NOMOR</label>
                </div>
                <div class="sideText">
                    <span><?php echo $dataMaster->NoWorkSchedule; ?></span>
                </div>
            </td>
        </tr>
    </table>
    <p>Informasi Order dan Pelanggan :</p>
    <table class="tabelList2 border-solid" cellpadding="0" cellspacing="0" style="margin-top: 10px;">
        <tr>
            <td width="150">Tanggal Order:</td>
            <td><?php echo $dataMaster->TanggalID; ?></td>
            <td width="150">Tanggal Kunjungan:</td>
            <td></td>
        </tr>
        <tr>
            <td width="150">Teknisi:</td>
            <td>
                <?php
                if ($qTeknisi) {
                    foreach ($qTeknisi as $dataTeknisi) {
                        echo $dataTeknisi->Nama . "<br/> ";
                    }
                }
                ?>
            </td>
            <td width="150">No. SPB:</td>
            <td><?php echo $dataSPB->NoPenjualan; ?></td>
        </tr>
        <tr>
            <td width="150">Nama Konsumen:</td>
            <td><?php echo $dataPelanggan->NamaPelanggan; ?></td>
            <td width="150">No. Telp:</td>
            <td><?php echo $dataPelanggan->NoTelp; ?></td>
        </tr>
        <tr>
            <td width="150">Alamat:</td>
            <td colspan="3"><?php echo $dataPelanggan->Alamat; ?></td>
        </tr>
        <tr>
            <td width="150">PIC Lapangan:</td>
            <td colspan="3"><?php echo $dataMaster->PICPelanggan; ?></td>
        </tr>
    </table>
    <table class="tabelList2 border-solid" cellpadding="0" cellspacing="0" style="margin-top: 30px;">
        <tr>
            <td width="150">Deskripsi Pekerjaan:</td>
            <td><?php echo $dataMaster->Tipe == "1" ? "Pemasangan Unit Water Heater" : ($dataMaster->Tipe == "2" ? "Service / Maintenance Unit Water Heater" : "Survey Unit Water Heater"); ?></td>
        </tr>
        <tr>
            <td width="150">Jenis Unit:</td>
            <td>
                <table class="noborder" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="33%"><input type="checkbox" <?php if ($dataMaster->JenisUnit == "EWH") echo "checked"; ?> /> EWH</td>
                        <td width="33%"><input type="checkbox" <?php if ($dataMaster->JenisUnit == "SWH") echo "checked"; ?> /> SWH</td>
                        <td><input type="checkbox" <?php if ($dataMaster->JenisUnit == "Heatpump") echo "checked"; ?> /> Heatpump</td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <strong style="display:inline-block; width:60px;">Tipe: </strong>________________________________________________________
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td width="150">Nomor Tangki:</td>
            <td>
                <table class="noborder" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="45%">
                            <strong style="display:inline-block; width:60px;">Tangki: </strong> <?php echo ($dataMaster->NoTangki != "") ? $dataMaster->NoTangki : "____________________________"; ?><br />
                            <strong style="display:inline-block; width:60px;">Panel A: </strong><?php echo ($dataMaster->NoPanelA != "") ? $dataMaster->NoPanelA : "____________________________"; ?><br />
                            <strong style="display:inline-block; width:60px;">Panel B: </strong><?php echo ($dataMaster->NoPanelB != "") ? $dataMaster->NoPanelB : "____________________________"; ?><br />
                            <strong style="display:inline-block; width:60px;">Panel C: </strong><?php echo ($dataMaster->NoPanelC != "") ? $dataMaster->NoPanelC : "____________________________"; ?>
                        </td>
                        <td>
                            <strong style="display:inline-block; width:110px;">Tangki Heatpump: </strong><?php echo ($dataMaster->NoTangkiHeatpump != "") ? $dataMaster->NoTangkiHeatpump : "____________________________"; ?><br />
                            <strong style="display:inline-block; width:110px;">Outdoor Heatpump: </strong><?php echo ($dataMaster->NoOutdoorHeatpump != "") ? $dataMaster->NoOutdoorHeatpump : "____________________________"; ?><br />
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table class="tabelList2 border-solid" cellpadding="0" cellspacing="0" style="margin-top: 30px;">
        <tr>
            <td width="150" height="100">Kebutuhan Material:</td>
            <td></td>
        </tr>
        <tr>
            <td width="150" height="100">Keterangan:</td>
            <td></td>
        </tr>
    </table>
    <table class="tabelList2 border-solid" style="margin-top: 30px;">
        <?php $karyawan = $db->get_row("SELECT * FROM tb_karyawan WHERE IDKaryawan='" . $dataMaster->IDSales . "'"); ?>
        <tr>
            <td class="center">Pemberi Kerja : </td>
            <td class="center">Penerima Kerja : </td>
            <td class="center">Diperiksa : </td>
            <td class="center">Disetujui : </td>
            <td class="center">Konsumen : </td>
        </tr>
        <tr>
            <td class="center" width="20%"><br /><br /><br /><br /><strong>(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</strong>
            </td>
            <td class="center" width="20%"><br /><br /><br /><br /><strong>(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</strong>
            </td>
            <td class="center" width="20%"><br /><br /><br /><br /><strong>(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</strong>
            </td>
            <td class="center" width="20%"><br /><br /><br /><br /><strong>(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</strong>
            </td>
            <td class="center" width="20%"><br /><br /><br /><br /><strong>(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</strong>
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