<?php
include_once "../config/connection.php";
require_once('../library/class.phpmailer.php');

$tanggal = antiSQLInjection($_POST['tanggal']);
$exp = explode("/", $tanggal);
$tanggal = $exp[2] . "-" . $exp[1] . "-" . $exp[0];
$tanggalCond = $exp[2] . "-" . $exp[1];
$tanggalCond2 = $exp[2] . $exp[1];

$karyawan = antiSQLInjection($_POST['karyawan']);
$category = antiSQLInjection($_POST['category']);
$no_kendaraan = antiSQLInjection($_POST['no_kendaraan']);
$total_nilai = antiSQLInjection($_POST['total_nilai']);
$jumlah_liter = antiSQLInjection($_POST['jumlah_liter']);
$km_kendaraan = antiSQLInjection($_POST['km_kendaraan']);
$lokasi_service = antiSQLInjection($_POST['lokasi_service']);
$stts = antiSQLInjection($_POST['stts']);

$metode_pem = antiSQLInjection($_POST['metode_pem']);
$metode_pem1 = antiSQLInjection($_POST['metode_pem1']);
$metode_pem2 = antiSQLInjection($_POST['metode_pem2']);
$no_bg = antiSQLInjection($_POST['no_bg']);
$jatuh_tempo = antiSQLInjection($_POST['jatuh_tempo']);

$proyek = antiSQLInjection($_POST['proyek']);
$uploaded = antiSQLInjection($_POST['uploaded']);
$keterangan = antiSQLInjection($_POST['keterangan']);

if ($stts == 0) {
    $metode_pem1 = '';
    $metode_pem2 = '';
}

if ($category == "Reimburse BBM") {
    $lokasi_service = "";
} else if ($category == "Reimburse Service") {
    $jumlah_liter = "";
    $km_kendaraan = "";
} else {
    $no_kendaraan = "";
    $lokasi_service = "";
    $jumlah_liter = "";
    $km_kendaraan = "";
}

$dataLast = $db->get_row("SELECT * FROM tb_reimburse WHERE DATE_FORMAT(Tanggal,'%Y-%m')='" . $tanggalCond . "' ORDER BY NoReimburse DESC");
if ($dataLast) {
    $last = substr($dataLast->NoReimburse, -5);
    $last++;
    if ($last < 10000 and $last >= 1000)
        $last = "0" . $last;
    else if ($last < 1000 and $last >= 100)
        $last = "00" . $last;
    else if ($last < 100 and $last >= 10)
        $last = "000" . $last;
    else if ($last < 10)
        $last = "0000" . $last;
    $noreimburse = "REIM" . $tanggalCond2 . $last;
} else {
    $noreimburse = "REIM" . $tanggalCond2 . "00001";
}

$allow = 1;

if ($category == "Reimburse BBM" && $allow == 1) {
    $cekDK = $db->get_row("SELECT * FROM tb_asset WHERE NoKendaraan='$no_kendaraan'");
    if ($cekDK) {
        if ($jumlah_liter > $cekDK->MaxTangkiBBM) {
            echo "3";
            $allow = 0;
        } else {
            if ($km_kendaraan > $cekDK->KMKendaraan) {
                $allow = 1;
            } else {
                echo "2";
                $allow = 0;
            }
        }
    } else {
        echo "4";
        $allow = 0;
    }
}

if ($category == "Reimburse Service" && $allow == 1) {
    $cekDK = $db->get_row("SELECT * FROM tb_asset WHERE NoKendaraan='$no_kendaraan'");
    if ($cekDK) {
        $allow = 1;
    } else {
        echo "4";
        $allow = 0;
    }
}

if ($proyek != "" && $proyek != "0" && $allow == 1) {
    $dataProyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='$proyek'");
    if ($dataProyek) {
        $allow = 1;
    } else {
        $allow = 0;
    }
    // $limitOverhead = $dataProyek->LimitPengeluaranOverHead-$dataProyek->PengeluaranOverHead;
    // if($total_nilai>$limitOverhead){
    //     echo "5";
    //     $allow = 0;
    // } else {
    //     $allow = 1;
    // }
}

if ($allow == 1) {
    if ($stts == "1") {
        $tanggalApproved = date("Y-m-d");
        $approvedBy = $uploaded;
        $append1 = ", TanggalApproved='$tanggalComplete', ApprovedBy='$completedBy'";
    }
    if ($stts == "2") {
        $tanggalComplete = date("Y-m-d");
        $completedBy = $uploaded;
        $append2 = ", TanggalCompleted='$tanggalComplete', CompletedBy='$completedBy'";
    }
    $query = $db->query("INSERT INTO tb_reimburse SET NoReimburse='$noreimburse', IDKaryawan='$karyawan', Tanggal='$tanggal', Kategori='$category', NoKendaraan='$no_kendaraan', TotalNilai='$total_nilai', JumlahLiterBBM='$jumlah_liter', KMKendaraan='$km_kendaraan', LokasiService='$lokasi_service', Status='$stts', CreatedBy='$uploaded', IDProyek='$proyek', MetodePembayaran1='$metode_pem1', MetodePembayaran2='$metode_pem2', Keterangan='$keterangan', MetodePembayaran='$metode_pem', NoBG='$no_bg', BGJatuhTempo='$jatuh_tempo' $append1 $append2");
    if ($query) {
        echo "1";
        if ($category == "Reimburse BBM")
            $update = $db->query("UPDATE tb_asset SET KMKendaraan='$km_kendaraan' WHERE NoKendaraan='$no_kendaraan'");

        if ($stts == "1") {
            // Send Email.
            $dataReimburse = $db->get_row("SELECT * FROM tb_reimburse WHERE NoReimburse='$noreimburse'");
            $karyawan = $db->get_row("SELECT * FROM tb_karyawan WHERE IDKaryawan='" . $dataReimburse->IDKaryawan . "'");
            $msg = '<!DOCTYPE html>
                <html>
                <head>
                    <title></title>
                    <style type="text/css">
                        .body{
                            font-size: 12px;
                            font-family: verdana;
                            color: #333;
                            line-height: 1.6em;
                        }
                        .table{
                            width: 100%;
                            border-collapse: collapse;
                        }
                        .table td, table th{
                            padding: 3px 20px;
                            border: solid 1px #ccc;
                            text-align: left;
                        }
                        .table th{
                            background: #efefef;
                        }

                    </style>
                </head>
                <body class="body">
                <p>Hi,</p>

                <p>Reimburse Saudara <strong class="underline">' . $karyawan->Nama . '</strong> telah <strong>DISETUJUI HRD</strong> silahkan cek data reimburse tersebut dan mohon segara lakukan pencairan dana Reimburse</p>

                <p>Terima kasih,<br/>
                AntWork Smart System.</p>

                <p><strong>PS: </strong>Email ini adalah email otomatis dari sistem. Mohon tidak membalasnya. Terima Kasih.</p>
                </body>
                </html>';
            //SEND MAIL
            //SET MAIL HEADER
            $mail = new PHPMailer(); // create a new object
            $mail->IsSMTP(); // enable SMTP
            $mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
            $mail->SMTPAuth = true; // authentication enabled
            $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
            $mail->Host = "smtp.gmail.com";
            $mail->Port = 465; // or 587
            $mail->IsHTML(true);
            $mail->Username = "mailblastinformation@gmail.com";
            $mail->Password = "tjzvhfserbdzsvgb";
            $mail->SetFrom("mailblastinformation@gmail.com", "AntWork - No Reply");
            $mail->Subject = "[Antwork] Reimburse Telah Disetujui HRD " . date("d/m/Y") . " - " . $dataReimburse->NoReimburse;
            $mail->AddAddress("youputra@gmail.com");
            $mail->Body = $msg;

            //SELECT ALL ACCOUNTING
            $query = $db->get_results("SELECT * FROM tb_karyawan WHERE IDJabatan='3'");
            if ($query) {
                foreach ($query as $data) {
                    $mail->AddAddress($data->EmailPribadi);
                    //echo $data->EmailPribadi;
                }
            }
            try {
                $mail->Send();
            } catch (Exception $ex) {
            }
            // $db->query("INSERT INTO tb_jurnal_detail SET IDRekening='$metode_pem2', JurnalRef='$noreimburse', Tanggal='$tanggalComplete', Pos='Debet', Keterangan='$keterangan', Debet='0', Kredit='$total_nilai', Closing='0', MataUang='1', Kurs='0'");

            // if($category=="Reimburse Proyek"){
            //     $db->query("UPDATE tb_proyek SET PengeluaranOverHead=(PengeluaranOverHead+$total_nilai) WHERE IDProyek='$proyek'");
            //     $db->query("INSERT INTO tb_jurnal_detail SET IDRekening='118', JurnalRef='$noreimburse', Tanggal='$tanggalComplete', Pos='Kredit', Keterangan='$keterangan', Debet='$total_nilai', Kredit='0', Closing='0', MataUang='1', Kurs='0'");
            // } else if($category=="Reimburse BBM"){
            //     $db->query("INSERT INTO tb_jurnal_detail SET IDRekening='119', JurnalRef='$noreimburse', Tanggal='$tanggalComplete', Pos='Kredit', Keterangan='$keterangan', Debet='$total_nilai', Kredit='0', Closing='0', MataUang='1', Kurs='0'");
            // } else {
            //     $db->query("INSERT INTO tb_jurnal_detail SET IDRekening='120', JurnalRef='$noreimburse', Tanggal='$tanggalComplete', Pos='Kredit', Keterangan='$keterangan', Debet='$total_nilai', Kredit='0', Closing='0', MataUang='1', Kurs='0'");
            // }
        }
    } else {
        echo "0";
    }
}
