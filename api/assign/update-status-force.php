<?php
include_once "../config/connection.php";
require_once('../library/class.phpmailer.php');

$idAssign = antiSQLInjection($_GET['idAssign']);
$cek2 = $db->get_row("SELECT * FROM tb_assign WHERE IDAssign='$idAssign' AND Status='0'");
if ($cek2) {
    $query = $db->query("UPDATE tb_assign SET Status='1', DateApproved=NOW(), DateModified=NOW(), ModifiedBy='" . $_SESSION["uid"] . "', ApproveMethod='2', Keterangan='Diterima oleh Operator', ApprovedBy='" . $_SESSION["uid"] . "' WHERE IDAssign='$idAssign'");
    if ($query) {
        //UPDATE LOKASI
        $dataAssign = $db->get_row("SELECT * FROM tb_assign WHERE IDAssign='$idAssign'");
        $qDetail = $db->get_results("SELECT * FROM tb_assign_detail WHERE IDAssign='" . $idAssign . "'");
        if ($qDetail) {
            foreach ($qDetail as $dDetail) {
                $db->query("UPDATE tb_asset SET IDKaryawan='" . $dataAssign->IDKaryawan . "', ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW() WHERE IDAsset='" . $dDetail->IDAsset . "'");
            }
        }

        $IDKaryawan = $query->IDKaryawan;
        if ($IDKaryawan == '') {
            $IDKaryawan = $_SESSION["uid"];
        }
        $query = $db->get_row("SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') as TanggalID FROM tb_assign WHERE IDAssign='$idAssign'");
        $karyawan = $db->get_row("SELECT * FROM tb_karyawan WHERE IDKaryawan='" . $IDKaryawan . "'");
        if ($query->Status == "0") $status = "Baru";
        else $status = "Diterima";
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

                    <p>Saudara <strong class="underline">' . $karyawan->Nama . '</strong> telah <strong>MENERIMA</strong> asset/invetaris yang anda berikan sebelumnya. Sebagai bukti pengingat berikut detail dari assign asset/invertaris yang anda telah lakukan:</p>

                    <p>
                    <strong>No Assign :</strong> ' . $query->NoAssign . '<br/>
                    <strong>Tanggal :</strong> ' . $query->TanggalID . '<br/>
                    <strong>Karyawan :</strong> ' . $karyawan->Nama . '<br/>
                    <strong>Status : ' . $status . '</strong><br/>
                    </p>

                    <table class="table">
                        <tr>
                            <th width="50">No</th>
                            <th>Asset / Inventaris</th>
                        </tr>';

        $qdetail = $db->get_results("SELECT * FROM tb_assign_detail WHERE IDAssign='" . $query->IDAssign . "'");
        if ($qdetail) {
            $i = 0;
            foreach ($qdetail as $dDetail) {
                $i++;
                $msg .= '<tr>
                            <td>' . $i . '</td>
                            <td>' . $dDetail->KodeAsset . ' - ' . $dDetail->Nama . '</td>
                        </tr>';
            }
        }

        $msg .= '</table>

                    <p><strong>Total Item :</strong> ' . $i . '</p>

                    <p>Terima kasih,
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
        $mail->Subject = "[Antwork] Assign Asset Telah Diterima " . date("d/m/Y") . " - " . $karyawan->Nama;
        $mail->AddAddress("youputra@gmail.com");
        $mail->Body = $msg;

        //SELECT ALL HRD
        $query = $db->get_results("SELECT * FROM tb_karyawan WHERE IDDepartement='7'");
        if ($query) {
            foreach ($query as $data) {
                $mail->AddAddress($data->EmailPribadi);
            }
        }

        //SELECT ALL CC KEPADA
        if ($ccTo != "") {
            $exp = explode(", ", $ccTo);
            foreach ($exp as $dK) {
                $dKaryawan = $db->get_row("SELECT * FROM tb_karyawan WHERE IDKaryawan='$dK'");
                if ($dKaryawan) {
                    $mail->AddAddress($dKaryawan->EmailPribadi);
                }
            }
        }

        /*if(!$mail->Send()) {
                echo "Mailer Error: " . $mail->ErrorInfo;
            } else {
                echo "Message has been sent";
            }*/
        try {
            $mail->Send();
        } catch (Exception $ex) {
        }

        echo "1";
    } else
        echo "2";
} else
    echo "3";
