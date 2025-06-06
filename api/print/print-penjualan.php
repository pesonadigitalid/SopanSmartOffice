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
    <?php
    if ($dataMaster->PPNPersen == '0') {
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
                    <img src="print-logo2.png">
                </td>
            </tr>
        </table>
    <?php } ?>
    <table style="margin-top:30px;">
        <tr>
            <td width="50%" class="" style="padding-top: 20px !important;vertical-align: middle;">
                <h3 class="title-print" style="font-size: 20px !important">SURAT PEMESANAN BARANG</h3>
            </td>
            <td width="50%" align="right" style="padding-top: 20px !important;vertical-align: middle;">
                <div class="sideLabel">
                    <label>NOMOR</label><br />
                    <label>TANGGAL</label><br />
                </div>
                <div class="sideText">
                    <span><?php echo $dataMaster->NoPenjualan; ?></span><br />
                    <span><?php echo $dataMaster->TanggalID; ?></span><br />
                </div>
            </td>
        </tr>
    </table>
    <p>Dengan ini saya selaku Marketing di <?php echo $perusahaan; ?> melakukan pemesanan barang sebagai berikut :</p>
    <table class="tabelList2 border-solid" cellpadding="0" cellspacing="0" style="margin-top: 10px;">
        <thead>
            <tr>
                <th width="10">No.</th>
                <th>Nama Barang</th>
                <th width="50">Qty</th>
                <th width="50">Satuan</th>
                <th width="100">Harga</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = $db->get_results("SELECT * FROM tb_penjualan_detail WHERE NoPenjualan='" . $dataMaster->NoPenjualan . "' AND Harga>0 ORDER BY NoUrut ASC");
            if ($query) {
                $i = 1;
                foreach ($query as $data) {
                    $dbarang = $db->get_row("SELECT a.*, b.Nama AS Satuan FROM tb_barang a, tb_satuan b WHERE a.IDBarang='" . $data->IDBarang . "' AND b.IDSatuan=a.IDSatuan");
            ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $data->NamaBarang; ?></td>
                        <td style="text-align: center;"><?php echo $data->Qty; ?></td>
                        <td style="text-align: center;"><?php echo $dbarang->Satuan; ?></td>
                        <td style="text-align: right;"><?php echo number_format(round($data->SubTotal / $data->Qty, 2)); ?></td>
                    </tr>
            <?php
                    $i++;
                }
            }
            ?>
            <tr>
                <td rowspan="5" colspan="2">
                    <?php
                    if ($dataMaster->Included != '') {
                    ?>
                        <strong style="text-decoration: underline;">Termasuk :</strong><br />
                        <?php
                        $exp = preg_split("/\\r\\n|\\r|\\n/", $dataMaster->Included);
                        ?>
                        <ul class="included">
                            <?php
                            foreach ($exp as $ds) {
                            ?>
                                <li><?php echo $ds; ?></li>
                            <?php
                            }
                            ?>
                        </ul>
                    <?php
                    }
                    ?>
                    <strong style="text-decoration: underline;">Tanggal Pemasangan :</strong><br />
                    <?php echo nl2br($dataMaster->TanggalPemasangan); ?><br />
                    <strong style="text-decoration: underline;">Kondisi Pembayaran :</strong><br />
                    <?php echo nl2br($dataMaster->KondisiPembayaran); ?><br />
                </td>
                <td colspan="2" style="text-align: right;"><strong>SUB TOTAL</strong></td>
                <td style="text-align: right;"><?php echo number_format($dataMaster->Total); ?></td>
            </tr>
            <tr style="display: none;">
                <td colspan="2" style="text-align: right;"><strong>DISKON <?php if ($dataMaster->DiskonPersen > 0) echo number_format($dataMaster->DiskonPersen) . "%"; ?></strong></td>
                <td style="text-align: right;"><?php echo number_format($dataMaster->Diskon); ?></td>
            </tr>
            <tr style="display: none;">
                <td colspan="2" style="text-align: right;"><strong>TOTAL</strong></td>
                <td style="text-align: right;"><?php echo number_format($dataMaster->GrandTotal); ?></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: right;"><strong>PPN <?php if ($dataMaster->PPNPersen > 0) echo number_format($dataMaster->PPNPersen) . "%"; ?></strong></td>
                <td style="text-align: right;"><?php echo number_format($dataMaster->PPN); ?></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: right;"><strong>GRAND TOTAL</strong></td>
                <td style="text-align: right;"><?php echo number_format($dataMaster->GrandTotal); ?></td>
            </tr>
        </tbody>
    </table><br />
    <p>Dengan data pelanggan sebagai berikut :</p>
    <table class="tabelList2 border-solid" cellpadding="0" cellspacing="0" style="margin-top: 10px;">
        <tr>
            <td width="200">
                Nama Pelanggan<br />
                Alamat<br />
                No. Telp / Handphone<br />
                No. NPWP<br />
            </td>
            <td>
                <?php if ($dataPelanggan->NamaNPWP != '') echo $dataPelanggan->NamaNPWP;
                else echo $dataPelanggan->NamaPelanggan; ?><br />
                <?php if ($dataPelanggan->AlamatNPWP != '') echo $dataPelanggan->AlamatNPWP;
                else echo $dataPelanggan->Alamat; ?><br />
                <?php echo $dataPelanggan->NoTelp; ?><br />
                <?php echo $dataPelanggan->NoNPWP; ?><br />
            </td>
        </tr>
    </table>
    <table class="tabelList2 border-solid" style="margin-top: 50px;">
        <?php $karyawan = $db->get_row("SELECT * FROM tb_karyawan WHERE IDKaryawan='" . $dataMaster->IDSales . "'"); ?>
        <tr>
            <td class="center">Yang Memesan : </td>
            <td class="center">Menyetujui : </td>
            <td class="center">Mengetahui : </td>
            <td class="center">Mengetahui : </td>
        </tr>
        <tr>
            <td width="25%" class="center"><strong>Marketing</strong><br /><br /><br /><br /><strong>( <?php echo $karyawan->Nama; ?> )</strong>
            </td>
            <td class="center" width="25%"><strong>Direktur</strong><br /><br /><br /><br /><strong>( Ir. Lukito Pramono )</strong>
            </td>
            <td class="center" width="25%"><strong>Administrasi</strong><br /><br /><br /><br /><strong>( Aryantini )</strong>
            </td>
            <td class="center" width="25%"><strong>Engineering</strong><br /><br /><br /><br /><strong>(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</strong>
            </td>
        </tr>
    </table>
    <div <div class="newPage"></div>
    <?php
    if ($dataMaster->PPNPersen == '0') {
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
                    <img src="print-logo2.png">
                </td>
            </tr>
        </table>
    <?php } ?>
    <table style="margin-top:30px;">
        <tr>
            <td width="50%" class="" style="padding-top: 20px !important;vertical-align: middle;padding-left:0">
                Nomor : <?php echo $dataMaster->NoPenjualan; ?>
            </td>
            <td width="50%" align="right" style="padding-top: 20px !important;vertical-align: middle;">
                Denpasar, <?php echo $tanggalExp[2] . " " . $bulan[$tanggalExp[1]] . " " . $tanggalExp[0]; ?>
            </td>
        </tr>
    </table>
    <p>
        Kepada : <br />
        <strong><?php if ($dataPelanggan->NamaNPWP != '') echo $dataPelanggan->NamaNPWP;
                else echo $dataPelanggan->NamaPelanggan; ?></strong><br />
        <?php if ($dataPelanggan->AlamatNPWP != '') echo $dataPelanggan->AlamatNPWP;
        else echo $dataPelanggan->Alamat; ?>
    </p>
    <p>
        <span class="total-inline">Up</span> : <?php echo $dataPelanggan->NamaKP1; ?> / <?php echo $dataPelanggan->NamaKP2; ?>
    </p>
    <p>
        <span class="total-inline">Perihal</span> : <?php echo $dataMaster->Prihal; ?>
    </p>
    <p>Dengan Hormat,</p>
    <p>Bersama ini kami mengajukan <?php echo $dataMaster->Prihal; ?> dengan rincian sebagai berikut : </p>
    <p style="text-align: center;line-height: 30px;margin: 30px 0;">
        <strong style="font-size:15px;">TOTAL HARGA : <br />
            Rp. <?php echo number_format($dataMaster->GrandTotal, 2); ?></strong><br />
        <?php echo terbilang($dataMaster->GrandTotal); ?>
    </p>
    <div class="term"><?php echo $dataMaster->TermAndCondition; ?></div>
    <script type="text/javascript">
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>