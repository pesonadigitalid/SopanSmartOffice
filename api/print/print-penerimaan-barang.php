<?php
session_start();
include_once "../config/connection.php";
$id = $_GET['id'];

$bulan = array("01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", "09" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember");
$dataMaster = newQuery("get_row", "SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_penerimaan_stok WHERE IDPenerimaan='" . $id . "'");
//$dataProyek = newQuery("get_row","SELECT * FROM tb_proyek WHERE IDProyek='".$dataPO->IDProyek."'");
$dataSupplier = newQuery("get_row", "SELECT * FROM tb_supplier WHERE IDSupplier='" . $dataMaster->IDSupplier . "'");
$tanggalExp = explode("-", $dataMaster->Tanggal);
$proyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='" . $dataMaster->IDProyek . "'");


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
    <table style="margin-top:30px;">
        <tr>
            <td colspan="2">
                <h3 class="title-print">PENERIMAAN BARANG</h3>
            </td>
        </tr>
        <tr>
            <td width="50%" class="bottom" style="padding-top: 20px !important;">
                SUPPLIER :<br />
                <strong><?php if ($dataSupplier) echo $dataSupplier->NamaPerusahaan;
                        else echo "UMUM"; ?></strong><br />
                <?php if ($dataSupplier->Alamat != "" && $dataSupplier->Alamat != "-")  echo $dataSupplier->Alamat . "<br />"; ?>
                <?php if ($dataSupplier->Kota != "" && $dataSupplier->Kota != "-") echo $dataSupplier->Kota; ?> <?php if ($dataSupplier->Provinsi != "" && $dataSupplier->Provinsi != "-") echo $dataSupplier->Provinsi; ?> <?php if ($dataSupplier->KodePos != "" && $dataSupplier->KodePos != "-") echo $dataSupplier->KodePos; ?>
            </td>
            <td width="50%" align="right" style="padding-top: 20px !important;">
                <div class="sideLabel">
                    <label>NO. PENERIMAAN</label><br />
                    <label>TANGGAL</label><br />
                    <label>KODE PROYEK</label><br />
                    <label>NAMA PROYEK</label><br />
                </div>
                <div class="sideText">
                    <span><?php echo $dataMaster->NoPenerimaanBarang; ?></span><br />
                    <span><?php echo $dataMaster->TanggalID; ?></span><br />
                    <span><?php if ($proyek) echo $proyek->KodeProyek . " / " . $proyek->Tahun;
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
                <th width="100">Serial Number</th>
                <th width="40">Qty</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = $db->get_results("SELECT * FROM tb_penerimaan_stok_detail WHERE NoPenerimaanBarang='" . $dataMaster->NoPenerimaanBarang . "' ORDER BY NoUrut ASC");
            if ($query) {
                $i = 1;
                $qty = 0;
                foreach ($query as $data) {
            ?>
                    <tr class="border-bottom">
                        <td style="text-align: center;"><?php echo $i; ?></td>
                        <td><?php echo $data->NamaBarang; ?></td>
                        <td style="text-align: center;"><?php if ($data->SN != "") echo $data->SN;
                                                        else echo "-"; ?></td>
                        <td style="text-align: center;"><?php echo number_format($data->Qty); ?></td>
                    </tr>
            <?php
                    $i++;
                    $qty += $data->Qty;
                }
            }
            ?>
            <tr>
                <td colspan="3" style="text-align: right;"><strong>TOTAL BARANG</strong></td>
                <td style="text-align: center;"><?php echo number_format($qty); ?></td>
            </tr>
        </tbody>
    </table>
    <table class="asignment" style="margin-top: 10px;">
        <tr>
            <td width="30%" class="center" style="padding-top:15px"><strong>Cost Control</strong><br /><br /><br /><br /><strong>(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</strong>
            </td>
            <td class="center" width="30%" style="padding-top:15px"><strong>Purchasing</strong><br /><br /><br /><br /><strong>( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</strong>
            </td>
            <td class="center" width="30%" style="padding-top:15px"><strong>Direktur</strong><br /><br /><br /><br /><strong>( Ir. Lukito Pramono )</strong>
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