<?php
include_once "../config/connection.php";
function is_decimal($val) {
    return preg_match('/^\d+\.\d+$/',$val);
}

$act = antiSQLInjection($_GET['act']);
switch ($act) {
    case "DataList":
        $return = array();
        $karyawanArray = array();
        
        // $datestart = $_GET['datestart'];
        // $expstart = explode("/",$datestart);
        // $datestartchange = $expstart[2]."-".$expstart[1]."-".$expstart[0];

        // $dateend = $_GET['dateend'];
        // $expend = explode("/",$dateend);
        // $dateendchange = $expend[2]."-".$expend[1]."-".$expend[0];
        
        $status = antiSQLInjection($_GET['status']);
        $id_karyawan = antiSQLInjection($_GET['id_karyawan']);

        // if ($datestart != "" && $dateend != "") {
        //     $cond = "WHERE DateCreated BETWEEN '$datestartchange' AND '$dateendchange'";
        // } else if ($datestart != "") {
        //     $cond = "WHERE DateCreated='$datestartchange'";
        // } else {
        //     $cond = "WHERE DATE_FORMAT(DateCreated, '%Y-%m') = '".date("Y-m")."'";
        // }

        $datestart = antiSQLInjection($_GET['datestart']);
        $cond = "WHERE DATE_FORMAT(DariTanggal, '%Y') = '$datestart'";
        
        if($id_karyawan!="0") $cond .= "AND IDKaryawan='$id_karyawan'";
        
        if($status!="") $cond2 = "AND Status='$status'";
        
        $query = $db->get_results("SELECT *, DATE_FORMAT(DariTanggal, '%d/%m/%Y') AS DariTanggalID, DATE_FORMAT(SampaiTanggal, '%d/%m/%Y') AS SampaiTanggalID FROM tb_cuti $cond $cond2 ORDER BY IDCuti ASC");
        if($query){
            $i=1;
            foreach($query as $data){
                $karyawan = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='".$data->IDKaryawan."'");
                if($data->Status=="1") $status="Baru"; else if($data->Status=="2") $status="Disetujui"; else $status="Ditolak";
                array_push($return,array("IDCuti"=>$data->IDCuti,"DariTanggal"=>$data->DariTanggalID,"No"=>$i,"SampaiTanggal"=>$data->SampaiTanggalID,"JumlahHari"=>$data->JumlahHari,"Keterangan"=>$data->Keterangan,"Jenis"=>$data->Jenis,"Status"=>$status,"Karyawan"=>$karyawan));
                $i++;
            }
        }
        
        $query = $db->get_results("SELECT * FROM tb_karyawan WHERE IDKaryawan>1 AND Status='1' ORDER BY Nama ASC");
        if($query){
            foreach($query as $data){
                array_push($karyawanArray,array("IDKaryawan"=>$data->IDKaryawan,"Nama"=>$data->Nama));
            }
        }
        
        $all = $db->get_var("SELECT COUNT(*) FROM tb_cuti $cond "); if(!$all) $all='';
        $new = $db->get_var("SELECT COUNT(*) FROM tb_cuti $cond AND Status='1'"); if(!$new) $new='';
        $approved = $db->get_var("SELECT COUNT(*) FROM tb_cuti $cond AND Status='2'"); if(!$approved) $approved='';
        $rejected = $db->get_var("SELECT COUNT(*) FROM tb_cuti $cond AND Status='0'"); if(!$rejected) $rejected='';
        
        echo json_encode(array("data" => $return,"all"=>$all,"new"=>$new,"approved"=>$approved,"rejected"=>$rejected,"karyawanArray"=>$karyawanArray));
    break;
    
    case "DeleteRecord":
        $idr = $_POST['idr'];
                
        $query = $db->query("DELETE FROM tb_cuti WHERE IDCuti='$idr'");
        if($query){
            echo "1";
        } else {
            echo "0";
        }
    break;
    
    case "LoadAllRequirement":
        $karyawan = array();
        
        $query = $db->get_results("SELECT * FROM tb_karyawan WHERE IDKaryawan>1 AND Status='1' AND (StatusKaryawan<>'Harian' OR (StatusKaryawan='Harian' AND StatusLainnya='Kantor')) ORDER BY Nama ASC");
        if($query){
            foreach($query as $data){
                array_push($karyawan,array("IDKaryawan"=>$data->IDKaryawan,"Nama"=>$data->Nama));
            }
        }

        $cutiTahun = $db->get_var("SELECT value FROM tb_system_config WHERE label='JUMLAHCUTITAHUNAN'");
        $totalCutiApprove = $db->get_var("SELECT SUM(JumlahHari) FROM tb_cuti WHERE IDKaryawan='$karyawan' AND STATUS='2'");

        $holiday = array();
        $query = $db->get_results("SELECT * FROM tb_public_holiday WHERE DATE_FORMAT(DateCreated,'%Y')>='".date("Y")."' ORDER BY DariTanggal ASC");
        if($query){
            foreach($query as $data){
                $begin = new DateTime($data->DariTanggal);
                $end = new DateTime($data->SampaiTanggal);

                $interval = DateInterval::createFromDateString('1 day');
                $period = new DatePeriod($begin, $interval, $end);
                $i = 0;
                foreach ( $period as $dt ){
                    $i++;
                    //Jangan ngambil data hari libur yang memang hari minggu.
                    if($dt->format("D") != "Sun"){
                        $nDate = $dt->format("Y-m-d");
                        array_push($holiday,$nDate);
                    }
                    
                }

                if($i==0) {
                    $dt = new DateTime($data->DariTanggal);
                    if($dt->format("D") != "Sun"){
                        array_push($holiday,$data->DariTanggal);
                    }
                } else if($data->DariTanggal != $data->SampaiTanggal){
                    $dt = new DateTime($data->SampaiTanggal);
                    if($dt->format("D") != "Sun"){
                        array_push($holiday,$data->SampaiTanggal);
                    }
                }
            }
        }

        $sisa = $cutiTahun-$totalCutiApprove;
        echo json_encode(array("karyawan"=>$karyawan,"holiday"=>$holiday,"sisaCuti"=>$sisa));
    break;
    
    case "NewRecord":
        $dari_tanggal = $_POST['dari_tanggal'];
        $exp = explode("/",$dari_tanggal);
        $dari_tanggal = $exp[2]."-".$exp[1]."-".$exp[0];
        $dari_year = $exp[2];
        
        $sampai_tanggal = $_POST['sampai_tanggal'];
        $exp = explode("/",$sampai_tanggal);
        $sampai_tanggal = $exp[2]."-".$exp[1]."-".$exp[0];
        $sampai_year = $exp[2];
        
        $karyawan = $_POST['karyawan'];
        $jml_hari = $_POST['jml_hari'];
        $keterangan = $_POST['keterangan'];
        $jenis = $_POST['jenis'];
        $stts_cuti = $_POST['stts_cuti'];
        $lokasi = $_POST['lokasi'];

        $begin = new DateTime($dari_tanggal);
        $end   = new DateTime($sampai_tanggal);

        $lanjut = true;

        for($i = $begin; $i <= $end; $i->modify('+1 day')){
            $tanggal = $i->format("Y-m-d");
            $tanggalID = $i->format("d/m/Y");
            $cek = $db->get_row("SELECT * FROM tb_cuti WHERE IDKaryawan='$karyawan' AND (DariTanggal='$tanggal' OR '$tanggal' BETWEEN DariTanggal AND SampaiTanggal)");
            if($cek){
                $lanjut = false;
                $msg = "Data Cuti tidak dapat disimpan karena anda telah mengambil cuti di tanggal ".$tanggalID.". Silahkan hubungi HRD jika ingin mengubah cuti.";
            }
        }

        if($lanjut){
            if(is_decimal($jml_hari) && $jml_hari>1){
                $lanjut = false;
                $msg = "Cuti setengah hari harus dibuat terpisah dari data cuti ini.";
            }
        }

        if($lanjut){
            if($dari_year!=$sampai_year){
                $lanjut = false;
                $msg = "Cuti beda tahun harus dibuat terpisah dari data cuti ini.";
            }
        }
        
        if($lanjut){
            $query = $db->query("INSERT INTO tb_cuti SET IDKaryawan='$karyawan', DariTanggal='$dari_tanggal', SampaiTanggal='$sampai_tanggal', JumlahHari='$jml_hari', Lokasi='$lokasi', Keterangan='$keterangan', Status='$stts_cuti', Jenis='$jenis', CreatedBy='".$_SESSION["uid"]."', DateModified=NOW(), ModifiedBy='".$_SESSION["uid"]."'");
            if($query){
                echo json_encode(array("res"=>1));
            } else {
                echo json_encode(array("res"=>0,"msg"=>"Data Cuti tidak dapat disimpan. Silahkan coba kembali nanti."));
            }
        } else {
            echo json_encode(array("res"=>0,"msg"=>$msg));
        }
    break;
        
    case "Detail":
        $detail = array();
        $id = antiSQLInjection($_GET['id']);

        $query = $db->get_row("SELECT *, DATE_FORMAT(DariTanggal, '%d/%m/%Y') AS DariTanggalID, DATE_FORMAT(SampaiTanggal, '%d/%m/%Y') AS SampaiTanggalID FROM tb_cuti WHERE IDCuti='$id' ORDER BY IDCuti ASC");
        if($query){
            $detail = array("karyawan"=>$query->IDKaryawan,"dari_tanggal"=>$query->DariTanggalID,"sampai_tanggal"=>$query->SampaiTanggalID,"jml_hari"=>$query->JumlahHari,"keterangan"=>$query->Keterangan,"jenis"=>$query->Jenis,"stts_cuti"=>$query->Status,"lokasi"=>$query->Lokasi);
        }
        echo json_encode(array("detail"=>$detail));
    break;
    
    case "EditRecord":
        $dari_tanggal = $_POST['dari_tanggal'];
        $exp = explode("/",$dari_tanggal);
        $dari_tanggal = $exp[2]."-".$exp[1]."-".$exp[0];
        $dari_year = $exp[2];
        
        $sampai_tanggal = $_POST['sampai_tanggal'];
        $exp = explode("/",$sampai_tanggal);
        $sampai_tanggal = $exp[2]."-".$exp[1]."-".$exp[0];
        $sampai_year = $exp[2];
        
        $id = $_POST['id'];
        $karyawan = $_POST['karyawan'];
        $jml_hari = $_POST['jml_hari'];
        $keterangan = $_POST['keterangan'];
        $jenis = $_POST['jenis'];
        $stts_cuti = $_POST['stts_cuti'];
        $lokasi = $_POST['lokasi'];

        $begin = new DateTime($dari_tanggal);
        $end   = new DateTime($sampai_tanggal);

        $lanjut = true;

        for($i = $begin; $i <= $end; $i->modify('+1 day')){
            $tanggal = $i->format("Y-m-d");
            $tanggalID = $i->format("d/m/Y");
            $cek = $db->get_row("SELECT * FROM tb_cuti WHERE IDKaryawan='$karyawan' AND (DariTanggal='$tanggal' OR '$tanggal' BETWEEN DariTanggal AND SampaiTanggal) AND IDCuti!='$id'");
            if($cek){
                $lanjut = false;
                $msg = "Data Cuti tidak dapat disimpan karena anda telah mengambil cuti di tanggal ".$tanggalID.". Silahkan hubungi HRD jika ingin mengubah cuti.";
            }
        }

        if($lanjut){
            if(is_decimal($jml_hari) && $jml_hari>1){
                $lanjut = false;
                $msg = "Cuti setengah hari harus dibuat terpisah dari data cuti ini.";
            }
        }

        if($lanjut){
            if($dari_year!=$sampai_year){
                $lanjut = false;
                $msg = "Cuti beda tahun harus dibuat terpisah dari data cuti ini.";
            }
        }
        
        if($lanjut){
            $query = $db->query("UPDATE tb_cuti SET IDKaryawan='$karyawan', DariTanggal='$dari_tanggal', SampaiTanggal='$sampai_tanggal', JumlahHari='$jml_hari', Lokasi='$lokasi', Keterangan='$keterangan', Status='$stts_cuti', Jenis='$jenis', DateModified=NOW(), ModifiedBy='".$_SESSION["uid"]."' WHERE IDCuti='$id'");
            if($query){
                echo json_encode(array("res"=>1));
            } else {
                echo json_encode(array("res"=>0,"msg"=>"Data Cuti tidak dapat disimpan. Silahkan coba kembali nanti."));
            }
        } else {
            echo json_encode(array("res"=>0,"msg"=>$msg));
        }
    break;
        
    default:
        echo "";
}