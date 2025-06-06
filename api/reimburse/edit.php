<?php
include_once "../config/connection.php";
require_once('../library/class.phpmailer.php');

$id = antiSQLInjection($_POST['id']);
$stts = antiSQLInjection($_POST['stts']);
$metode_pem = antiSQLInjection($_POST['metode_pem']);
$metode_pem1 = antiSQLInjection($_POST['metode_pem1']);
$metode_pem2 = antiSQLInjection($_POST['metode_pem2']);
$no_bg = antiSQLInjection($_POST['no_bg']);
$jatuh_tempo = antiSQLInjection($_POST['jatuh_tempo']);
$lokasi_service = antiSQLInjection($_POST['lokasi_service']);

$keterangan = antiSQLInjection($_POST['keterangan']);

$dataReimburse = $db->get_row("SELECT * FROM tb_reimburse WHERE IDReimburse='$id'");
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
$query = $db->query("UPDATE tb_reimburse SET Status='$stts', TanggalCompleted='$tanggalComplete', MetodePembayaran1='$metode_pem1', MetodePembayaran2='$metode_pem2', Keterangan='$keterangan', MetodePembayaran='$metode_pem', NoBG='$no_bg', BGJatuhTempo='$jatuh_tempo' $append1 $append2 WHERE IDReimburse='$id'");
if ($query) {
    if ($stts == "1") {
        // Send Email.
        $dataReimburse = $db->get_row("SELECT * FROM tb_reimburse WHERE IDReimburse='$id'");
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

        // $db->query("INSERT INTO tb_jurnal_detail SET IDRekening='$metode_pem2', JurnalRef='".$dataReimburse->NoReimburse."', Tanggal='$tanggalComplete', Pos='Debet', Keterangan='$keterangan', Debet='0', Kredit='".$dataReimburse->TotalNilai."', Closing='0', MataUang='1', Kurs='0'");

        // Ga valid karena overhead harus diinput dari jurnal
        // if($dataReimburse->Kategori=="Reimburse Proyek"){
        //     $db->query("UPDATE tb_proyek SET PengeluaranOverHead=(PengeluaranOverHead+".$dataReimburse->TotalNilai.") WHERE IDProyek='".$dataReimburse->IDProyek."'");
        //     $db->query("INSERT INTO tb_jurnal_detail SET IDRekening='118', JurnalRef='".$dataReimburse->NoReimburse."', Tanggal='$tanggalComplete', Pos='Kredit', Keterangan='$keterangan', Debet='".$dataReimburse->TotalNilai."', Kredit='0', Closing='0', MataUang='1', Kurs='0'");
        // } else if($category=="Reimburse BBM"){
        //     $db->query("INSERT INTO tb_jurnal_detail SET IDRekening='119', JurnalRef='".$dataReimburse->NoReimburse."', Tanggal='$tanggalComplete', Pos='Kredit', Keterangan='$keterangan', Debet='".$dataReimburse->TotalNilai."', Kredit='0', Closing='0', MataUang='1', Kurs='0'");
        // } else {
        //     $db->query("INSERT INTO tb_jurnal_detail SET IDRekening='120', JurnalRef='".$dataReimburse->NoReimburse."', Tanggal='$tanggalComplete', Pos='Kredit', Keterangan='$keterangan', Debet='".$dataReimburse->TotalNilai."', Kredit='0', Closing='0', MataUang='1', Kurs='0'");
        // }
    }

    echo "1";
} else {
    echo "0";
}
