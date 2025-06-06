<?php
include_once "../config/connection.php";
require_once('../library/class.phpmailer.php');

$act = antiSQLInjection($_GET['act']);
switch ($act) {
    case "DataList":
        $d = array();
        $query = $db->get_results("SELECT * FROM tb_blasting ORDER BY IDBlasting DESC");
        if($query){
            $no = 0;
            foreach($query as $data){
                $no++;

                $recipient = "";
                if($data->ToID!=""){
                    $exp = explode(", ",$data->ToID);
                    $i = 0;
                    $sisa = 0;
                    foreach($exp as $to){
                        $prefix = substr(trim($to), 0, 3);
                        $id = substr(trim($to), 3);
                        $i++;

                        if($i<=3){
                            if($prefix == "GRP"){
                                $qR = $db->get_row("SELECT * FROM tb_contact_category WHERE IDContactCategory='$id'");
                                if($qR)
                                    $recipient .= " Group ".$qR->Nama.", ";
                            } else if($prefix == "SUP"){
                                $qR = $db->get_row("SELECT * FROM tb_supplier WHERE IDSupplier='$id'");
                                if($qR)
                                    $recipient .= " ".$qR->NamaPerusahaan.", ";
                            } else if($prefix == "PEL"){
                                $qR = $db->get_row("SELECT * FROM tb_pelanggan WHERE IDPelanggan='$id'");
                                if($qR)
                                    $recipient .= " ".$qR->NamaPelanggan.", ";
                            }
                        } else {
                            $sisa++;
                        }
                    }
                    if($sisa>0){
                        $recipient .= " dan $sisa lainnya. ";
                    }
                    if($recipient!="") $recipient = substr($recipient, 0, -1);
                }

                array_push($d, array("No"=>$no,"IDBlasting"=>$data->IDBlasting,"Subject"=>$data->Subject,"Recipient"=>$recipient,"DateCreated"=>$data->DateCreated));
            }
        }
        $return = array("data"=>$d);
        echo json_encode($return);
        break;

    case "LoadAllRequirement":
        //$to = array(array("IDTo"=>"ALL","Nama"=>"All Contact"));
        $to = array();

        $query = $db->get_results("SELECT * FROM tb_contact_category ORDER BY Nama ASC");
        if($query){
            foreach($query as $data){
                array_push($to, array("IDTo"=>"GRP".$data->IDContactCategory,"Nama"=>"Group ".$data->Nama));
            }
        }

        $query = $db->get_results("SELECT * FROM tb_supplier ORDER BY NamaPerusahaan ASC");
        if($query){
            foreach($query as $data){
                array_push($to, array("IDTo"=>"SUP".$data->IDSupplier,"Nama"=>$data->NamaPerusahaan));
            }
        }

        $query = $db->get_results("SELECT * FROM tb_pelanggan ORDER BY NamaPelanggan ASC");
        if($query){
            foreach($query as $data){
                array_push($to, array("IDTo"=>"PEL".$data->IDPelanggan,"Nama"=>$data->NamaPelanggan));
            }
        }

        $return = array("to"=>$to);
        echo json_encode($return);
        break;

    case "InsertNew":
        $mail = new PHPMailer(); // create a new object

        $kepada = antiSQLInjection($_POST['kepada']);
        $subject = antiSQLInjection($_POST['subject']);
        $message = antiSQLInjection($_POST['message']);
        $from = antiSQLInjection($_POST['from']);

        $file1_e = antiSQLInjection($_POST['file1_e']);
        $file2_e = antiSQLInjection($_POST['file2_e']);
        $file3_e = antiSQLInjection($_POST['file3_e']);

        $kepadaTo = "";
        $recipient = "";
        foreach($kepada as $data){
            $kepadaTo .= " ".$data.", ";

            $prefix = substr($data, 0, 3);
            $id = substr($data, 3);
            if($prefix == "GRP"){
                $qChild = $db->get_results("SELECT * FROM tb_contact_category_user WHERE id_category='$id'");
                if($qChild){
                    foreach($qChild as $dChild){
                        if($dChild->type=="Pelanggan"){
                            $qR = $db->get_row("SELECT * FROM tb_pelanggan WHERE IDPelanggan='".$dChild->id."'");
                            if($qR){
                                if($qR->Email!="" || $qR->Email!="-")
                                    $recipient .= " ".$qR->Email.", ";
                                if($qR->Email2!="" || $qR->Email!="-")
                                    $recipient .= " ".$qR->Email2.", ";
                                if($qR->EmailKP1!="" || $qR->Email!="-")
                                    $recipient .= " ".$qR->EmailKP1.", ";
                                if($qR->EmailKP2!="" || $qR->Email!="-")
                                    $recipient .= " ".$qR->EmailKP2.", ";
                            }
                        } else if($dChild->type=="Supplier"){
                            $qR = $db->get_row("SELECT * FROM tb_supplier WHERE IDSupplier='".$dChild->id."'");
                            if($qR){
                                if($qR->Email!="" || $qR->Email!="-")
                                    $recipient .= " ".$qR->Email.", ";
                                if($qR->Email2!="" || $qR->Email!="-")
                                    $recipient .= " ".$qR->Email2.", ";
                                if($qR->EmailKP1!="" || $qR->Email!="-")
                                    $recipient .= " ".$qR->EmailKP1.", ";
                                if($qR->EmailKP2!="" || $qR->Email!="-")
                                    $recipient .= " ".$qR->EmailKP2.", ";
                            }
                        }
                    }
                }
            } else if($prefix == "SUP"){
                $qR = $db->get_row("SELECT * FROM tb_supplier WHERE IDSupplier='$id'");
                if($qR){
                    if($qR->Email!="" || $qR->Email!="-")
                        $recipient .= " ".$qR->Email.", ";
                    if($qR->Email2!="" || $qR->Email!="-")
                        $recipient .= " ".$qR->Email2.", ";
                    if($qR->EmailKP1!="" || $qR->Email!="-")
                        $recipient .= " ".$qR->EmailKP1.", ";
                    if($qR->EmailKP2!="" || $qR->Email!="-")
                        $recipient .= " ".$qR->EmailKP2.", ";
                }
            } else if($prefix == "PEL"){
                $qR = $db->get_row("SELECT * FROM tb_pelanggan WHERE IDPelanggan='$id'");
                if($qR){
                    if($qR->Email!="" || $qR->Email!="-")
                        $recipient .= " ".$qR->Email.", ";
                    if($qR->Email2!="" || $qR->Email!="-")
                        $recipient .= " ".$qR->Email2.", ";
                    if($qR->EmailKP1!="" || $qR->Email!="-")
                        $recipient .= " ".$qR->EmailKP1.", ";
                    if($qR->EmailKP2!="" || $qR->Email!="-")
                        $recipient .= " ".$qR->EmailKP2.", ";
                }
            }
        }
        if($kepadaTo!="") $kepadaTo =  substr($kepadaTo, 0, -1);
        if($recipient!="") $recipient =  substr($recipient, 0, -2);

        if($_FILES['file']){
            $file_name = $_FILES['file']['name'];
            $file_size =$_FILES['file']['size'];
            $file_tmp =$_FILES['file']['tmp_name'];
            $file_type=$_FILES['file']['type'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $photoName = str_replace(" ", "", $subject)."_1.".$file_ext;
            move_uploaded_file($file_tmp,"../../../smartoffice/files/blasting/".$photoName);
            $imgEmail = '<img src="http://124.81.242.210:8007/smartoffice/files/blasting/'.$photoName.'"/><br/>';
        } else if($file1_e!="null" && $file1_e!=null && $file1_e!=""){
            $photoName = $file1_e;
            $imgEmail = '<img src="http://124.81.242.210:8007/smartoffice/files/blasting/'.$file1_e.'"/><br/>';
        }

        if($_FILES['file2']){
            $file_name = $_FILES['file2']['name'];
            $file_size =$_FILES['file2']['size'];
            $file_tmp =$_FILES['file2']['tmp_name'];
            $file_type=$_FILES['file2']['type'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $photoName2 = str_replace(" ", "", $subject)."_2.".$file_ext;
            move_uploaded_file($file_tmp,"../../../smartoffice/files/blasting/".$photoName2);
            $imgEmail2 = '<img src="http://124.81.242.210:8007/smartoffice/files/blasting/'.$photoName2.'"/><br/>';
        } else if($file2_e!="null" && $file2_e!=null && $file2_e!=""){
            $photoName2 = $file2_e;
            $imgEmail2 = '<img src="http://124.81.242.210:8007/smartoffice/files/blasting/'.$file2_e.'"/><br/>';
        }

        if($_FILES['file3']){
            $file_name = $_FILES['file3']['name'];
            $file_size =$_FILES['file3']['size'];
            $file_tmp =$_FILES['file3']['tmp_name'];
            $file_type=$_FILES['file3']['type'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $photoName3 = str_replace(" ", "", $subject)."_3.".$file_ext;
            move_uploaded_file($file_tmp,"../../../smartoffice/files/blasting/".$photoName3);
            $imgEmail3 = '<img src="http://124.81.242.210:8007/smartoffice/files/blasting/'.$photoName3.'"/><br/>';
        } else if($file3_e!="null" && $file3_e!=null && $file3_e!=""){
            $photoName3 = $file3_e;
            $imgEmail3 = '<img src="http://124.81.242.210:8007/smartoffice/files/blasting/'.$file3_e.'"/><br/>';
        }
        
        $query = $db->query("INSERT INTO tb_blasting SET ToID='$kepadaTo', RecipientMail='$recipient', Subject='$subject', Message='$message', Image1='$photoName', Image2='$photoName2', Image3='$photoName3', CreatedBy='".$_SESSION["uid"]."', FromText='$from'");
        if($query){
            // SEND MAIL
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
                    '.nl2br($message).'
                    <br/>
                    '.$imgEmail.'<br/>
                    '.$imgEmail2.'<br/>
                    '.$imgEmail3.'
                    </body></html>';
            $mail->IsSMTP(); // enable SMTP
            $mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
            $mail->SMTPAuth = true; // authentication enabled
            $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
            $mail->Host = "smtp.gmail.com";
            $mail->Port = 465; // or 587
            $mail->IsHTML(true);
            $mail->Username = "mailblastinformation@gmail.com";
            $mail->Password = "Heartof123456";
            $mail->SetFrom("mailblastinformation@gmail.com",$from);
            $mail->Subject = $subject;

            $mail->AddAddress("mailblastinformation@gmail.com");
            // ADD BCC
            //$mail->AddBCC("officialpesonacreative@gmail.com");

            if($recipient!=""){
                $exp = explode(", ",$recipient);
                foreach($exp as $d){
                    $email = trim($d);
                    //echo $email;
                    $mail->AddBCC($email);
                }
            }

            $mail->Body = $msg;
            if($mail->Send())
                echo "1";
            else
                echo "2";
        } else {
            echo "0";
        }
        break;
        
    case "Delete":
        $idr = antiSQLInjection($_POST['idr']);

        $query = $db->query("DELETE FROM tb_blasting WHERE IDBlasting='$idr'");
        echo "1";

        break;
    
    case "Detail":
        $did = antiSQLInjection($_GET['id']);
        $to = array();

        $query = $db->get_results("SELECT * FROM tb_contact_category ORDER BY Nama ASC");
        if($query){
            foreach($query as $data){
                array_push($to, array("IDTo"=>"GRP".$data->IDContactCategory,"Nama"=>"Group ".$data->Nama));
            }
        }

        $query = $db->get_results("SELECT * FROM tb_supplier ORDER BY NamaPerusahaan ASC");
        if($query){
            foreach($query as $data){
                array_push($to, array("IDTo"=>"SUP".$data->IDSupplier,"Nama"=>$data->NamaPerusahaan));
            }
        }

        $query = $db->get_results("SELECT * FROM tb_pelanggan ORDER BY NamaPelanggan ASC");
        if($query){
            foreach($query as $data){
                array_push($to, array("IDTo"=>"PEL".$data->IDPelanggan,"Nama"=>$data->NamaPelanggan));
            }
        }

        $detail = array();
        $data = $db->get_row("SELECT * FROM tb_blasting WHERE IDBlasting='$did'");
        if($data){
            $kepada = array();
            if($data->ToID!=""){
                $exp = explode(", ",$data->ToID);
                foreach($exp as $d){
                    array_push($kepada, trim($d));
                }
            }
            $detail = array("IDBlasting"=>$data->IDBlasting,"Subject"=>$data->Subject,"From"=>$data->FromText,"Kepada"=>$kepada,"Message"=>$data->Message,"Image1"=>$data->Image1,"Image2"=>$data->Image2,"Image3"=>$data->Image3);
        }

        $return = array("to"=>$to, "detail"=>$detail);
        echo json_encode($return);
        break;
    default:
        echo "";
}