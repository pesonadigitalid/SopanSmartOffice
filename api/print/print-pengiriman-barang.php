<?php
session_start();
include_once "../config/connection.php";
$id = $_GET['id'];

$bulan = array("01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", "09" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember");
$dataMaster = newQuery("get_row", "SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_penjualan_surat_jalan WHERE IDSuratJalan='" . $id . "'");
//$dataProyek = newQuery("get_row","SELECT * FROM tb_proyek WHERE IDProyek='".$dataPO->IDProyek."'");
//$dataSupplier = newQuery("get_row","SELECT * FROM tb_supplier WHERE IDSupplier='".$dataMaster->IDSupplier."'");
$tanggalExp = explode("-", $dataMaster->Tanggal);
$penjualan = $db->get_row("SELECT a.*, b.KodePelanggan, b.NamaPelanggan FROM tb_penjualan a, tb_pelanggan b WHERE a.IDPelanggan = b.IDPelanggan AND a.IDPenjualan='" . $dataMaster->IDPenjualan . "'");
$dataPelanggan = $db->get_row("SELECT * FROM tb_pelanggan WHERE IDPelanggan='" . $penjualan->IDPelanggan . "'");


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
    <style type="text/css">
        .listItem td {
            border-bottom: none !important;
            border-top: none !important;
        }
    </style>
</head>

<body class=" fupsize">
    <?php
    if ($penjualan->PPNPersen == '0') {
        $perusahaan = "Solusi Pemanas Air Nusantara";
    ?>
        <table class="header-bg">
            <tr>
                <td width="70%" class="bottom" style="line-height: 1.5em;padding-top:7px;">
                    <img src="print-logo-sopan.png" align="left" style="padding-right: 10px;margin-right: 20px;border-right: solid 2px #e11b22;margin-top:-7px;width: 200px;height: 85px;object-fit: contain;">
                    Jl. Tukad Batanghari No. 42<br />
                    Denpasar 80225, Bali<br />
                    Phone. +62 823-2800-1818<br />
                    Email. mail.aristonbali@gmail.com
                </td>
                <td width="30%" align="right" style="padding-top:20px;padding-right:50px;">
                    <img src="https://chart.googleapis.com/chart?chs=100x100&cht=qr&chl=<?php echo $dataMaster->NoSuratJalan; ?>&choe=UTF-8">
                </td>
            </tr>
        </table>
    <?php
    } else {
        $perusahaan = "CV. LINTAS DAYA";
    ?>
        <table>
            <tr style="padding-bottom: 100px !important;">
                <td width="80%" class="bottom" style="line-height: 1.5em">
                    <img src="print-logo.png" align="left" style="padding-right: 10px;margin-right: 20px;border-right: solid 2px #02b1e1;">
                    JL. Tukad Citarum I No. 7B Renon, Perum Surya Graha Asih <br />Denpasar Bali<br />
                    Phone : +62 361 - 238055<br />
                    Fax : +62 361 - 238055<br />
                    Email : info@lintasdaya.com
                </td>
                <td width="20%" align="right" style="padding-top:20px">
                    <img src="https://chart.googleapis.com/chart?chs=100x100&cht=qr&chl=<?php echo $dataMaster->NoSuratJalan; ?>&choe=UTF-8">
                </td>
            </tr>
        </table>
    <?php } ?>
    <table style="margin-top:0px;">
        <tr>
            <td width="50%" class="" style="padding-top: 0px !important;vertical-align: middle;">
                <h3 class="title-print" style="font-size: 20px !important">SURAT JALAN</h3>
            </td>
            <td width="50%" align="right" style="padding-top: 0px !important;vertical-align: middle;">
                <div class="sideLabel">
                    <label>NOMOR</label><br />
                    <label>TANGGAL</label><br />
                    <label>NO. SPB</label><br />
                    <label>NO. PO</label><br />
                </div>
                <div class="sideText">
                    <span><?php echo $dataMaster->NoSuratJalan; ?></span><br />
                    <span><?php echo $dataMaster->TanggalID; ?></span><br />
                    <span><?php if ($penjualan) echo $penjualan->NoPenjualan; ?></span><br />
                    <span><?php if ($penjualan) echo $penjualan->NoPOKonsumen; ?></span><br />
                </div>
            </td>
        </tr>
        <tr>
            <td width="50%" class="bottom" style="padding-top: 0px !important;" colspan="2">
                Kepada :<br />
                <strong><?php if ($dataPelanggan) echo $dataPelanggan->NamaPelanggan;
                        else echo "UMUM"; ?></strong><br />
                <?php if ($dataPelanggan->Alamat != "" && $dataPelanggan->Alamat != "-")  echo $dataPelanggan->Alamat . "<br />"; ?>
                <?php if ($dataPelanggan->Kota != "" && $dataPelanggan->Kota != "-") echo $dataPelanggan->Kota; ?> <?php if ($dataPelanggan->Provinsi != "" && $dataPelanggan->Provinsi != "-") echo $dataPelanggan->Provinsi; ?> <?php if ($dataPelanggan->KodePos != "" && $dataPelanggan->KodePos != "-") echo $dataPelanggan->KodePos; ?><br />
            </td>
        </tr>
    </table>

    <table class="tabelList2 border-solid" cellpadding="0" cellspacing="0" style="margin-top: 0px;">
        <thead>
            <tr>
                <th width="10">No.</th>
                <th>Nama Barang</th>
                <th width="50">Qty</th>
                <th width="50">Satuan</th>
                <th width="100">Serial Number</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = $db->get_results("SELECT *, SUM(Qty) AS QtyReal, (SUM(HPP)/COUNT(*)) AS HPPReal
FROM tb_penjualan_surat_jalan_detail 
WHERE NoSuratJalan='" . $dataMaster->NoSuratJalan . "' 
GROUP BY NoUrut, IDBarang");
            if ($query) {
                $i = 1;
                $tqty = 0;
                $total = 0;
                foreach ($query as $data) {
                    // $cekSPB = $db->get_results("SELECT * FROM tb_penjualan_detail WHERE IDBarang='".$data->IDBarang."' AND NoPenjualan='".$dataMaster->NoPenjualan."' AND IsParent='1'");
                    // if($cekSPB){
                    $qty = $db->get_var("SELECT SUM(Qty) FROM tb_penjualan_surat_jalan_detail WHERE NoSuratJalan='" . $data->NoSuratJalan . "' AND IDBarang='" . $data->IDBarang . "'");
                    $harga = $db->get_var("SELECT (SUM(SubTotal)/SUM(Qty)) FROM tb_penjualan_surat_jalan_detail WHERE NoSuratJalan='" . $data->NoSuratJalan . "' AND IDBarang='" . $data->IDBarang . "'");
                    $subTotal = $harga * $qty;
                    $dbarang = $db->get_row("SELECT a.*, b.Nama AS Satuan FROM tb_barang a, tb_satuan b WHERE a.IDBarang='" . $data->IDBarang . "' AND b.IDSatuan=a.IDSatuan");
            ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $data->NamaBarang; ?></td>
                        <td style="text-align: center;"><?php echo $data->QtyReal; ?></td>
                        <td style="text-align: center;"><?php echo $dbarang->Satuan; ?></td>
                        <td style="text-align: center;"><?php if ($data->SN != "") echo $data->SN;
                                                        else echo "-"; ?></td>
                    </tr>
            <?php
                    $i++;
                    if ($data->Harga > 0) $tqty += $data->QtyReal;
                    $total += $subTotal;
                    // }
                }
            }
            ?>
            <tr>
                <td colspan="2">
                    <strong style="text-decoration: underline;">Keterangan :</strong><br />
                    <?php if ($dataMaster->Keterangan == "") echo "Tidak ada.";
                    else  echo $dataMaster->Keterangan; ?>
                </td>
                <td colspan="3">
                    <strong style="text-decoration: underline;">Total Qty :</strong><br />
                    <?php echo $tqty; ?>
                </td>
            </tr>
        </tbody>
    </table><br />
    <table class="tabelList2 border-solid" style="margin-top: 10px;">
        <?php $karyawan = $db->get_row("SELECT * FROM tb_karyawan WHERE IDKaryawan='" . $dataMaster->CreatedBy . "'"); ?>
        <tr>
            <td class="center">Yang Mengeluarkan : </td>
            <td class="center">Menyetujui : </td>
            <td class="center">Mengetahui : </td>
            <td class="center">Pembawa : </td>
            <td class="center">Pelanggan : </td>
        </tr>
        <tr>
            <td width="20%" class="center"><strong>Administrasi</strong><br /><br /><br /><br /><strong>( Aryantini )</strong>
            </td>
            <td class="center" width="20%"><strong>Kepala Cabang</strong><br /><br /><br /><br /><strong>(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</strong>
            </td>
            <td class="center" width="20%"><strong>Accounting</strong><br /><br /><br /><br /><strong>( Baidah )</strong>
            </td>
            <td class="center" width="20%"><strong>Sopir</strong><br /><br /><br /><br /><strong>(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</strong>
            </td>
            <td width="20%" class="center"><strong>Penerima</strong><br /><br /><br /><strong>
                    <div style="text-align: left;"><strong>Nama:<br />Tgl. Diterima:</strong> </div>
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