
<?php
include_once "../config/connection.php";

$act = antiSQLInjection($_GET['act']);
switch ($act) {
    case "TabIN":
        $rfcode = antiSQLInjection($_POST['rfcode']);
        $tapType = antiSQLInjection($_POST['tapType']);
        $tanggal = date("Y-m-d");
        $time = date("H:i:s");
        $dataKaryawan = $db->get_row("SELECT * FROM tb_karyawan WHERE CardNumber='$rfcode'");
        if($dataKaryawan){
            $allow = 1;

            if($tapType=="IN")
                $field = "JIn = '$time'";
            else 
                $field = "JOut = '$time'";

            $cek = $db->get_row("SELECT * FROM tb_in_out WHERE IDKaryawan='".$dataKaryawan->IDKaryawan."' AND Tanggal='$tanggal' AND (JIn='00:00:00' OR JOut='00:00:00') ORDER BY IDInOut DESC");
            if($cek){
                if($tapType=="IN" && $cek->JOut=="00:00:00"){
                    $allow = 0;
                    $type = "3";
                } else if($tapType=="OUT" && $cek->JOut!="00:00:00" && $cek->JIn=="00:00:00"){
                    $allow = 0;
                    $type = "2";
                }

                if($allow==1){
                    $db->query("UPDATE tb_in_out SET $field WHERE IDInOut='".$cek->IDInOut."'");
                    echo "1";
                } else {
                    echo $type;
                }
            } else if($tapType=="IN") {
                echo "3";
            } else {
                $db->query("INSERT INTO tb_in_out SET IDKaryawan='".$dataKaryawan->IDKaryawan."', Tanggal='$tanggal', $field");
                echo "1"."INSERT INTO tb_in_out SET IDKaryawan='".$dataKaryawan->IDKaryawan."', Tanggal='$tanggal', $field";
            }
        } else {
            echo "0";
        }
        break;

    default:
        echo "";
}