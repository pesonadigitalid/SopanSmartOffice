<?php
session_start();
include_once "../config/connection.php";
$id = $_GET['id'];

$bulan = array("01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", "09" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember");
$dataInvoice = newQuery("get_row", "SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID, b.NoPOKonsumen, b.NoPenjualan, b.Jenis, b.IDPelanggan FROM tb_penjualan_invoice a, tb_penjualan b WHERE a.IDInvoice='" . $id . "' AND a.IDPenjualan=b.IDPenjualan");
$dataPelanggan = newQuery("get_row", "SELECT * FROM tb_pelanggan WHERE IDPelanggan='" . $dataInvoice->IDPelanggan . "'");
$tanggalExp = explode("-", $dataInvoice->Tanggal);
$tglJatuhTempoExp = explode("-", $dataInvoice->JatuhTempo);
if ($dataInvoice->JatuhTempo != "") $jatuhTempo = $tglJatuhTempoExp[2] . " " . $bulan[$tglJatuhTempoExp[1]] . " " . $tglJatuhTempoExp[0];
else $jatuhTempo = "-";
if ($dataInvoice->NoPenjualan != "") $noPenjualan = $dataInvoice->NoPenjualan;
else $noPenjualan = "-";
if ($dataInvoice->NoPOKonsumen != "") $noPOKonsumen = $dataInvoice->NoPOKonsumen;
else $noPOKonsumen = "-";

$nama = "Ir. Lukito Pramono, M.T.";
$nama = ($dataInvoice->Sign != "") ? $dataInvoice->Sign : $nama;

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

$terbilang = terbilang($dataInvoice->GrandTotal) . " Rupiah";
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

<body class="fupsize">
    <?php
    if ($dataInvoice->IsPajak == '0') {
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
    <table style="margin-top:0px;">
        <tr>
            <td style="padding-top: 0px !important;vertical-align: middle;">
                <h3 class="title-print" style="font-size: 20px !important;">INVOICE PENJUALAN</h3>
            </td>
            <td width="50%" align="right" style="padding-top: 0px !important;">
                <div class="sideLabel">
                    <label>NOMOR</label><br />
                    <label>TANGGAL CETAK</label><br />
                    <label>JATUH TEMPO</label><br />
                    <label>NO. SPB</label><br />
                    <label>NO. PO</label><br />
                    <label>Jenis</label><br />
                </div>
                <div class="sideText">
                    <span><?php echo $dataInvoice->NoInvoice; ?></span><br />
                    <span><?php echo $tanggalExp[2] . " " . $bulan[$tanggalExp[1]] . " " . $tanggalExp[0]; ?></span><br />
                    <span><?php echo $jatuhTempo; ?></span><br />
                    <span><?php echo $noPenjualan; ?></span><br />
                    <span><?php echo $noPOKonsumen; ?></span><br />
                    <span><?php echo $dataInvoice->Jenis; ?></span><br />
                </div>
            </td>
        </tr>
        <tr>
            <td width="50%" class="bottom" style="padding-top: 0px !important;" colspan="2">
                Kepada :<br />
                <strong><?php echo $dataPelanggan->NamaPelanggan; ?><br />
                    <?php if ($dataPelanggan->Alamat != "" && $dataPelanggan->Alamat != "-")  echo $dataPelanggan->Alamat . "<br />"; ?></strong>
                <?php if ($dataPelanggan->Kota != "" && $dataPelanggan->Kota != "-") echo $dataPelanggan->Kota; ?> <?php if ($dataPelanggan->Provinsi != "" && $dataPelanggan->Provinsi != "-") echo $dataPelanggan->Provinsi; ?> <?php if ($dataPelanggan->KodePos != "" && $dataPelanggan->KodePos != "-") echo $dataPelanggan->KodePos; ?>
            </td>
        </tr>
    </table>
    <table class="tabelList2 border-solid" cellpadding="0" cellspacing="0" style="margin-top: 10px;">
        <thead>
            <tr>
                <th width="20">No.</th>
                <th>Nama Barang</th>
                <th width="40">Qty</th>
                <th width="100">Satuan</th>
                <th width="120">Harga Satuan</th>
                <th width="120">Sub Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = $db->get_results("SELECT * FROM tb_penjualan_invoice_detail WHERE NoInvoice='" . $dataInvoice->NoInvoice . "' ORDER BY NoUrut ASC");
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
                        <td style="text-align: right;">
                            <?php echo number_format($data->HargaDiskon); ?>
                        </td>
                        <td style="text-align: right;"><?php echo number_format($data->SubTotal); ?></td>
                    </tr>
            <?php
                    $i++;
                    $qty += $data->Qty;
                    $total += $data->SubTotal;
                }
            }
            ?>
            <tr>
                <td rowspan="6" colspan="2">
                    <strong>Keterangan : </strong><br />
                    <?php if ($dataInvoice->Keterangan != "") echo $dataInvoice->Keterangan;
                    else echo "-"; ?>
                </td>
                <td colspan="3" style="text-align: right;"><strong>Total : </strong></td>
                <td style="text-align: right;"><?php echo number_format($total); ?></td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right;"><strong>Penagihan (<?php echo number_format($dataInvoice->JumlahPersen); ?>%) : </strong></td>
                <td style="text-align: right;"><?php echo number_format($dataInvoice->Jumlah); ?></td>
            </tr>
            <tr style="display: none;">
                <td colspan="3" style="text-align: right;"><strong>Diskon <?php echo number_format($dataInvoice->DiskonPersen); ?>% : </strong></td>
                <td style="text-align: right;"><?php echo number_format($dataInvoice->Diskon); ?></td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right;"><strong>PPN <?php echo number_format($dataInvoice->PPNPersen); ?>% : </strong></td>
                <td style="text-align: right;"><?php echo number_format($dataInvoice->PPN); ?></td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right;"><strong>Grand Total Penagihan : </strong></td>
                <td style="text-align: right;"><?php echo number_format($dataInvoice->GrandTotal); ?></td>
            </tr>
        </tbody>
    </table>
    <table class="asignment" style="margin-top: 5px;">
        <tr>
            <td colspan="2" style="padding-bottom: 5px !important;"><strong>Terbilang</strong> : <?php echo $terbilang; ?></td>
        </tr>
        <tr>
            <td width="50%">
                Untuk Pembayaran Silahkan Transfer ke :<br />
                <strong><?php echo nl2br($dataInvoice->Note1); ?></strong>
                <?php if ($dataInvoice->Note2 != "" && $dataInvoice->Note2 != "-") { ?>
                    <div style="max-width: 300px;border: solid 2px #333; text-transform: uppercase;text-align: center;font-weight: bold;color: #333;margin-top:10px">
                        <?php echo nl2br($dataInvoice->Note2); ?>
                    </div>
                <?php } ?>
            </td>
            <td class="center">Denpasar, <?php echo $tanggalExp[2] . " " . $bulan[$tanggalExp[1]] . " " . $tanggalExp[0]; ?><br />
                <strong><?php echo $perusahaan; ?></strong>
                <br /><br /><br /><br /><br /><?php echo $nama; ?>
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
</body>

</html>