<?php
session_start();
include_once "../config/connection.php";
$bulan = array("01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", "09" => "September", 10 => "Oktober", "11" => "November", 12 => "Desember");

function durationWork($m, $y, $d)
{
    $datetime1 = new DateTime($y . "-" . $m . "-" . $d);
    $datetime2 = new DateTime(date("Y-m-d"));
    $interval = $datetime1->diff($datetime2);
    $year = $interval->format('%y');
    $month = $interval->format('%m');

    $return = "";
    if ($year > 0) $return .= "$year Tahun ";
    $return .= "$month Bulan";

    return $return;
}

$status_karyawan = antiSQLInjection($_GET['status_karyawan']);
$status_akun = antiSQLInjection($_GET['status_akun']);
$proyek = antiSQLInjection($_GET['proyek']);
$agama = antiSQLInjection($_GET['agama']);

$cond = "";
$subtitle = "";
if ($status_akun != "") {
    $cond = " AND Status='$status_akun'";
    $subtitle = ($status_akun == "1") ? "Status: Karyawan Aktif;" : "Status: Karyawan Resign;";
}

if ($proyek != "") {
    $cond .= " AND IDProyek='$proyek'";
    $DProyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='" . $proyek . "'");
    if ($DProyek) $subtitle .= " Proyek: " . $DProyek->KodeProyek . "/" . $DProyek->Tahun . " " . $DProyek->NamaProyek . ";";
}
if ($agama != "") {
    $cond .= " AND Agama='$agama'";
    $subtitle .= " Agama: " . $agama . ";";
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="author" content="Yogi Pratama - Pesona Creative - 085737654543" />

    <title>SOPAN Smart Office - Smart office for smart people</title>

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
    <div class="laporanTitle">
        <h1 class="underline">** LAPORAN DATA KARYAWAN **</h1><?php echo $subtitle; ?>
    </div>
    <table class="tabelList2" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th width="70">NIK</th>
                <th>Nama Karyawan</th>
                <th width="90">Departement</th>
                <th width="150">Tanggal Masuk</th>
                <th width="100">Lama Bekerja</th>
                <th width="100">Status Lainnya</th>
                <th width="100">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($status_karyawan == "" || $status_karyawan == "Tetap") { ?>
                <tr>
                    <td colspan="7">
                        <?php
                        $total = newQuery("get_var", "SELECT COUNT(*) FROM tb_karyawan WHERE IDKaryawan>1 AND Nama!='Lukito Pramono MMS' AND StatusKaryawan='Tetap' $cond ORDER BY TahunMasuk ASC, BulanMasuk ASC, TanggalMasuk ASC");
                        ?>
                        <strong>KARYAWAN TETAP <?php echo "(" . $total . ")"; ?></strong>
                    </td>
                </tr>
                <?php
                $query = newQuery("get_results", "SELECT * FROM tb_karyawan WHERE IDKaryawan>1 AND Nama!='Lukito Pramono MMS' AND StatusKaryawan='Tetap' $cond ORDER BY TahunMasuk ASC, BulanMasuk ASC, TanggalMasuk ASC");
                if ($query) {
                    foreach ($query as $data) {
                        $departement = newQuery("get_row", "SELECT * FROM tb_departement WHERE IDDepartement='" . $data->IDDepartement . "'");
                ?>
                        <tr>
                            <td><?php echo $data->NIK; ?></td>
                            <td><?php echo $data->Nama; ?></td>
                            <td style="text-align: center;"><?php echo $departement->NamaDepartement; ?></td>
                            <td style="text-align: center;"><?php echo $data->TanggalMasuk . "/" . $data->BulanMasuk . "/" . $data->TahunMasuk; ?></td>
                            <td style="text-align: center;"><?php echo durationWork($data->BulanMasuk, $data->TahunMasuk, $data->TanggalMasuk); ?></td>
                            <td style="text-align: center;"><?php echo $data->StatusLainnya; ?></td>
                            <td style="text-align: center;"><?php echo ($data->Status == "1") ? "Aktif" : "Resign"; ?></td>
                        </tr>
                <?php
                    }
                } else {
                    echo "<tr><td colspan='7'>Tidak ada karyawan dalam kategori ini...</td></tr>";
                }
                ?>
                <tr>
                    <td colspan="7">&nbsp;</td>
                </tr>
            <?php } ?>
            <?php if ($status_karyawan == "" || $status_karyawan == "Kontrak") { ?>
                <tr>
                    <td colspan="7">
                        <?php
                        $total = newQuery("get_var", "SELECT COUNT(*) FROM tb_karyawan WHERE IDKaryawan>1 AND Nama!='Lukito Pramono MMS' AND StatusKaryawan='Kontrak' $cond ORDER BY TahunMasuk ASC, BulanMasuk ASC, TanggalMasuk ASC");
                        ?>
                        <strong>KARYAWAN KONTRAK <?php echo "(" . $total . ")"; ?></strong>
                    </td>
                </tr>
                <?php
                $query = newQuery("get_results", "SELECT * FROM tb_karyawan WHERE IDKaryawan>1 AND Nama!='Lukito Pramono MMS' AND StatusKaryawan='Kontrak' $cond ORDER BY TahunMasuk ASC, BulanMasuk ASC, TanggalMasuk ASC");
                if ($query) {
                    foreach ($query as $data) {
                        $departement = newQuery("get_row", "SELECT * FROM tb_departement WHERE IDDepartement='" . $data->IDDepartement . "'");
                ?>
                        <tr>
                            <td><?php echo $data->NIK; ?></td>
                            <td><?php echo $data->Nama; ?></td>
                            <td style="text-align: center;"><?php echo $departement->NamaDepartement; ?></td>
                            <td style="text-align: center;"><?php echo $data->TanggalMasuk . "/" . $data->BulanMasuk . "/" . $data->TahunMasuk; ?></td>
                            <td style="text-align: center;"><?php echo durationWork($data->BulanMasuk, $data->TahunMasuk, $data->TanggalMasuk); ?></td>
                            <td style="text-align: center;"><?php echo $data->StatusLainnya; ?></td>
                            <td style="text-align: center;"><?php echo ($data->Status == "1") ? "Aktif" : "Resign"; ?></td>
                        </tr>
                <?php
                    }
                } else {
                    echo "<tr><td colspan='7'>Tidak ada karyawan dalam kategori ini...</td></tr>";
                }
                ?>
                <tr>
                    <td colspan="7">&nbsp;</td>
                </tr>
            <?php } ?>
            <?php if ($status_karyawan == "" || $status_karyawan == "Harian") { ?>
                <tr>
                    <td colspan="7">
                        <?php
                        $total = newQuery("get_var", "SELECT COUNT(*) FROM tb_karyawan WHERE IDKaryawan>1 AND Nama!='Lukito Pramono MMS' AND StatusKaryawan='Harian' $cond ORDER BY TahunMasuk ASC, BulanMasuk ASC, TanggalMasuk ASC");
                        ?>
                        <strong>KARYAWAN HARIAN <?php echo "(" . $total . ")"; ?></strong>
                    </td>
                </tr>
                <?php
                $query = newQuery("get_results", "SELECT * FROM tb_karyawan WHERE IDKaryawan>1 AND Nama!='Lukito Pramono MMS' AND StatusKaryawan='Harian' $cond ORDER BY TahunMasuk ASC, BulanMasuk ASC, TanggalMasuk ASC");
                if ($query) {
                    foreach ($query as $data) {
                        $departement = newQuery("get_row", "SELECT * FROM tb_departement WHERE IDDepartement='" . $data->IDDepartement . "'");
                ?>
                        <tr>
                            <td><?php echo $data->NIK; ?></td>
                            <td><?php echo $data->Nama; ?></td>
                            <td style="text-align: center;"><?php echo $departement->NamaDepartement; ?></td>
                            <td style="text-align: center;"><?php echo $data->TanggalMasuk . "/" . $data->BulanMasuk . "/" . $data->TahunMasuk; ?></td>
                            <td style="text-align: center;"><?php echo durationWork($data->BulanMasuk, $data->TahunMasuk, $data->TanggalMasuk); ?></td>
                            <td style="text-align: center;"><?php echo $data->StatusLainnya; ?></td>
                            <td style="text-align: center;"><?php echo ($data->Status == "1") ? "Aktif" : "Resign"; ?></td>
                        </tr>
                <?php
                    }
                } else {
                    echo "<tr><td colspan='7'>Tidak ada karyawan dalam kategori ini...</td></tr>";
                }
                ?>
                <tr>
                    <td colspan="7">&nbsp;</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <table class="asignment" style="margin-top: 20px;">
        <tr>
            <td class="center" width="60%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td class="center" width="40%">Mengetahui,<br /><br /><br /><br />( HRD )</td>
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