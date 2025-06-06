<?php
session_start();
include_once "../config/connection.php";
$id = $_GET['id'];

$bulan = array("01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", "09" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember");
$dataInvoice = newQuery("get_row", "SELECT a.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID, b.NoPOKonsumen, b.NoPenjualan, b.IDPelanggan FROM tb_penjualan_invoice a, tb_penjualan b WHERE a.IDInvoice='" . $id . "' AND a.IDPenjualan=b.IDPenjualan");
$dataPelanggan = newQuery("get_row", "SELECT * FROM tb_pelanggan WHERE IDPelanggan='" . $dataInvoice->IDPelanggan . "'");
$tanggalExp = explode("-", $dataInvoice->Tanggal);

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

if ($dataPelanggan->Alamat != "" && $dataPelanggan->Alamat != "-")  $alamatSupplier = $dataPelanggan->Alamat . "<br />";
if ($dataPelanggan->Kota != "" && $dataPelanggan->Kota != "-") $kotaSupplier = $dataPelanggan->Kota;
if ($dataPelanggan->Provinsi != "" && $dataPelanggan->Provinsi != "-") $provSupplier = $dataPelanggan->Provinsi;
if ($dataPelanggan->KodePos != "" && $dataPelanggan->KodePos != "-") $kodepostSupplier = $dataPelanggan->KodePos;

require_once "../library/mpdf/mpdf.php";
$mpdf = new mPDF('win-1252', array(210, 297), 9, 'DejaVuSansCondensed', 10, 10, 10, 10, 0, 8);
$mpdf->showImageErrors = true;
$content = '<!DOCTYPE html>
  <html>
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="author" content="Yogi Pratama - Pesona Creative - 085737654543" />
    <title>SOPAN Smart Office - Integrated System</title>
    <style type="text/css">
    @font-face {
      font-family: "CFont";
      src: url("fonts/Ubuntu-R.ttf");
    }
    
    @font-face {
      font-family: "CFont";
      src: url("fonts/Ubuntu-B.ttf");
      font-weight: bold;
    }
    
    @font-face {
      font-family: "CFont";
      src: url("fonts/Ubuntu-RI.ttf");
      font-style: italic;
    }
    
    @font-face {
      font-family: "CFont";
      src: url("fonts/Ubuntu-BI.ttf");
      font-weight: bold;
      font-style: italic;
    }
    
    body {
      font-family: "CFont";
      font-size: 12px;
      line-height: 1.8em;
      color: #333;
      padding: 0;
      margin: 0;
    }
    
    h1 {
      font-size: 13px;
      color: #000;
      margin: 0 !important;
    }
    
    .title-print {
      font-size: 24px !important;
      margin: 0;
    }
    
    .divider {
      border-bottom: 1px solid #eee !important;
      margin: 10px 0 40px 0 !important;
    }
    
    .sideLabel {
      border-right: 3px solid #000;
      float: left;
      text-align: right;
      padding-right: 5px;
      margin-right: 5px;
      width: 35% !important;
    }
    
    .sideText {
      text-align: left !important;
      font-weight: bold;
    }
    
    table {
      width: 100%;
      border: none;
    }
    
    table td {
      vertical-align: text-top;
    }
    
    .center {
      text-align: center;
    }
    
    .tabelList {
      margin-top: 10px;
    }
    
    .tabelList th {
      background: #eee;
      border-top: solid 1px #ccc;
      border-bottom: solid 1px #ccc;
      padding: 5px;
    }
    
    .tabelList td {
      padding: 5px;
    }
    

    .tabelList2{
        border-collapse: collapse;
    }
    
    .tabelList2 th {
      background: #eee;
      border: 1px solid #ccc;
      padding: 5px;
      border-collapse: collapse;
    }
    
    .tabelList2 td {
      padding: 5px;
      border: 1px solid #ccc;
      border-collapse: collapse;
    }
    
    .asignment {
      margin-top: 0px;
    }
    
    .underline {
      text-decoration: underline;
    }
    
    .underline {
      text-decoration: underline;
    }
    
    .laporanTitle {
      text-align: center;
      margin: 10px 0 20px;
      padding-top: 20px;
      border-top: solid 1px #ccc;
    }
    
    .bottom {
      vertical-align: bottom !important;
    }
    
    .smallfont th {
      font-size: 9px;
    }
    
    .biggerfont td {
      font-size: 11px;
    }
    
    .data-container {
      margin-bottom: 50px;
    }
    
    .data-container h4 {
      margin: 15px 5px 10px 5px;
      font-size: 14px;
    }
    
    .data-container fieldset {
      border: none;
      padding: 2px 5px;
    }
    
    .data-container fieldset label {
      width: 150px;
      display: inline-block;
    }
    
    .sub-label {
      padding-left: 20px;
      width: 130px !important;
    }
    
    .container {
      display: table;
      width: 100%;
      padding: 0 5px;
    }
    
    .container.margin-bottom {
      margin-bottom: 20px;
    }
    
    .container img,
    .img-kosong {
      width: 100px;
      height: 100px;
      border: 1px solid #000;
      padding: 4px;
    }
    
    .img-kosong {
      text-align: center;
      font-size: 14px;
      display: table-cell;
      vertical-align: middle;
    }
    
    .side-title h4 {
      font-size: 14px;
      font-weight: bold;
      margin: 20px 0 5px 0;
    }
    
    .img-karyawan,
    .side-top {
      display: table-cell;
      width: 18%;
      vertical-align: top;
    }
    
    .side-top {
      width: 82%;
    }
    
    .side-left,
    .side-right {
      display: table-cell;
      width: 50%;
      vertical-align: top;
    }
    
    .list-data {
      display: table;
      margin: 4px 0;
    }
    
    .nama-label,
    .titik-dua,
    .isi-label {
      display: table-cell;
    }
    
    .nama-label {
      width: 170px;
      font-weight: bold;
    }
    
    .small-width {
      width: 100px;
    }
    
    .sub-label2 {
      padding-left: 15px !important;
      width: 155px !important;
    }
    
    .titik-dua {
      width: 10px;
    }

    .note2 td{
      max-width: 300px;
      border: solid 2px #333;
      text-transform: uppercase;
      text-align: center;
      font-weight: bold;
      color: #333;
      padding:10px;
    }
    </style>
  </head>

  <body>
    <table>
      <tr style="padding-bottom: 100px !important;">
        <td width="100%" class="bottom" style="line-height: 1.5em;vertical-align:text-top;">';
if ($dataInvoice->IsPajak == '0') {
  $perusahaan = "Solusi Pemanas Air Nusantara";
  $content .= '<table width="100%">
                <tr>
                    <td>
                        <table width="100%">
                            <tr>
                                <td>
                                    <img src="print-logo-sopan.png" align="left" style="padding-right: 10px;margin-right: 20px;border-right: solid 2px #e11b22;vertical-align:text-top;width: 200px;">
                                </td>
                                <td style="padding-top:15px">
                                    <p>
                              Jl. Tukad Batanghari No. 42<br />
                              Denpasar 80225, Bali<br />
                              Phone. +62 823-2800-1818<br />
                              Email. mail.aristonbali@gmail.com
                          </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>';
} else {
  $perusahaan = "CV. LINTAS DAYA";
  $content .= '<table>
              <tr>
                <td width="80%" class="bottom" style="line-height: 1.5em;vertical-align:text-top;">
                    <table width="100%">
                        <tr>
                            <td>
                                <img src="print-logo.png" align="left" style="padding-right: 10px;margin-right: 20px;border-right: solid 2px #02b1e1;vertical-align:text-top">
                            </td>
                            <td style="padding-top:10px">
                                <p >
                                    JL. Tukad Citarum I No. 7B Renon, Perum Surya Graha Asih
                                  <br />Denpasar Bali
                                  <br /> Phone : +62 361 - 238055
                                  <br /> Fax : +62 361 - 238055
                                  <br /> Email : info@lintasdaya.com
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="20%" align="right" style="padding-top:20px">
                  <img src="print-logo2.png">
                </td>
              </tr>
            </table>';
}
$content .= '</td>
      </tr>
    </table>
    <table style="margin-top:30px;">
      <tr>
        <td colspan="2">
          <h3 class="title-print">INVOICE PENJUALAN</h3></td>
      </tr>
      <tr>
        <td width="50%" class="bottom" style="padding-top: 20px !important;">
          Kepada :
          <br />
          <strong>' . $dataPelanggan->NamaPelanggan . '</strong>
          <br />' . $alamatSupplier . '
          ' . $kotaSupplier . '
          ' . $provSupplier . '
          ' . $kodepostSupplier . '
        </td>
        <td width="50%" align="right" style="padding-top: 20px !important;">
            <table width="100%">
                <tr>
                    <td align="right"><label>NOMOR</label></td>
                    <td rowspan="4" style="width: 5px; background: #000; margin-left:20px; margin-right:10px;"></td>
                    <td><strong>' . $dataInvoice->NoInvoice . '</strong></td>
                </tr>
                <tr>
                    <td align="right"><label>TANGGAL CETAK</label></td>
                    <td><strong>' . $tanggalExp[2] . ' ' . $bulan[$tanggalExp[1]] . ' ' . $tanggalExp[0] . '</strong></td>
                </tr>
                <tr>
                    <td align="right"><label>NO. SPB</label></td>
                    <td><strong>' . $dataInvoice->NoPenjualan . '</strong></td>
                </tr>
                <tr>
                    <td align="right"><label>NO. PO</label></td>
                    <td><strong>' . $dataInvoice->NoPOKonsumen . '</strong></td>
                </tr>
            </table>
        </td>
      </tr>
    </table>
    <table class="tabelList2 border-solid" cellpadding="0" cellspacing="0" style="margin-top: 30px;margin-bottom: 30px;">
      <thead>
        <tr>
          <th width="20">No.</th>
          <th>Nama Barang</th>
          <th width="40">Qty</th>
          <th width="90">Satuan</th>
          <th width="100">Harga Satuan</th>
          <th width="100">Sub Total</th>
        </tr>
      </thead>
      <tbody>';

$query = $db->get_results("SELECT * FROM tb_penjualan_invoice_detail WHERE NoInvoice='" . $dataInvoice->NoInvoice . "' ORDER BY NoUrut ASC");
if ($query) {
  $i = 1;
  $qty = 0;
  $total = 0;
  foreach ($query as $data) {
    if ($data->DiskonType == '0')
      $diskon = number_format($data->DiskonValue);
    else
      $diskon = ($data->DiskonValue);

    $dbarang = $db->get_row("SELECT a.*, b.Nama AS Satuan FROM tb_barang a, tb_satuan b WHERE a.IDBarang='" . $data->IDBarang . "' AND b.IDSatuan=a.IDSatuan");
    $content .= '<tr class="border-bottom">
              <td style="text-align: center;">' . $i . '</td>
              <td>' . $data->NamaBarang . '</td>
              <td style="text-align: center;">' . number_format($data->Qty) . '</td>
              <td style="text-align: center;">' . $dbarang->Satuan . '</td>
              <td style="text-align: right;">' . number_format(round($data->SubTotal / $data->Qty, 2)) . '</td>
              <td style="text-align: right;">' . number_format($data->SubTotal) . '</td>
          </tr>';
    $i++;
    $qty += $data->Qty;
    $total += $data->SubTotal;
  }
}

if ($dataInvoice->Keterangan != "") $ket = $dataInvoice->Keterangan;
else $ket = "-";

$content .= '<tr>
              <td rowspan="5" colspan="2">
              <strong>Keterangan : </strong><br/>
              ' . $ket . '
              </td>
              <td colspan="3" style="text-align: right;"><strong>Total : </strong></td>
              <td style="text-align: right;">' . number_format($total) . '</td>
          </tr>
          <tr>
              <td colspan="3" style="text-align: right;"><strong>Penagihan (' . $dataInvoice->JumlahPersen . '%) : </strong></td>
              <td style="text-align: right;">' . number_format($dataInvoice->Jumlah) . '</td>
          </tr>
          <tr>
              <td colspan="3" style="text-align: right;"><strong>PPN ' . number_format($dataInvoice->PPNPersen) . '% : </strong></td>
              <td style="text-align: right;">' . number_format($dataInvoice->PPN) . '</td>
          </tr>
          <tr>
              <td colspan="3" style="text-align: right;"><strong>Grand Total Penagihan : </strong></td>
              <td style="text-align: right;">' . number_format($dataInvoice->GrandTotal) . '</td>
          </tr>
      </tbody>
    </table>
    <table class="asignment" style="margin-top: 10px;">
        <tr>
            <td colspan="2" style="padding-bottom: 20px !important;"><strong>Terbilang</strong> : ' . $terbilang . '</td>
        </tr>
        <tr>
            <td width="50%">
                Untuk Pembayaran Silahkan Transfer ke :<br />
                <strong>' . nl2br($dataInvoice->Note1) . '</strong><br/><br/>';
if ($dataInvoice->Note2 != "" && $dataInvoice->Note2 != "-") {
  $content .= '<table class="note2"><tr><td>' . nl2br($dataInvoice->Note2) . '</td></tr></table>';
}
$content .= '</td>
            <td class="center">Denpasar, ' . $tanggalExp[2] . ' ' . $bulan[$tanggalExp[1]] . ' ' . $tanggalExp[0] . '<br /><strong>' . $perusahaan . '</strong><br /><br /><br /><br /><br /><br /><br />' . $nama . '
            </td>
        </tr>
    </table>
  </body>
  </html>';

$filename = str_replace("/", "-", $dataInvoice->NoInvoice);
$mpdf->WriteHTML($content);
$mpdf->Output('INVOICE-' . $filename . '.pdf', 'F');
$file =  "INVOICE-" . $filename . ".pdf";
header("location: $file");
