<?php
include_once "../config/connection.php";

$act = antiSQLInjection($_GET['act']);
switch ($act) {
    case "DataList":
        $return = array();

        $datestart = antiSQLInjection($_GET['datestart']);
        $cond = "WHERE DATE_FORMAT(DariTanggal, '%Y') = '$datestart'";

        $query = $db->get_results("SELECT *, DATE_FORMAT(DariTanggal, '%d/%m/%Y') AS DariTanggalID, DATE_FORMAT(SampaiTanggal, '%d/%m/%Y') AS SampaiTanggalID FROM tb_public_holiday $cond ORDER BY DariTanggal ASC");
        if($query){
            $i=1;
            foreach($query as $data){
                $now = strtotime($data->SampaiTanggal);
                $your_date = strtotime($data->DariTanggal);
                $datediff = $now - $your_date;
                $jumlahHari = floor($datediff / (60 * 60 * 24)) + 1;
                array_push($return,array("IDPublicHoliday"=>$data->IDPublicHoliday,"NamaHariLibur"=>$data->NamaHariLibur,"DariTanggal"=>$data->DariTanggalID,"No"=>$i,"SampaiTanggal"=>$data->SampaiTanggalID,"JumlahHari"=>$jumlahHari,"Keterangan"=>$data->Keterangan));
                $i++;
            }
        }
        
        echo json_encode(array("data" => $return));
    break;
    
    case "Delete":
        $idr = antiSQLInjection($_POST['idr']);
                
        $query = $db->query("DELETE FROM tb_public_holiday WHERE IDPublicHoliday='$idr'");
        if($query){
            echo "1";
        } else {
            echo "0";
        }
    break;
    
    case "LoadAllRequirement":

    break;
    
    case "NewRecord":
        $dari_tanggal = antiSQLInjection($_POST['dari_tanggal']);
        $exp = explode("/",$dari_tanggal);
        $dari_tanggal = $exp[2]."-".$exp[1]."-".$exp[0];
        
        $sampai_tanggal = antiSQLInjection($_POST['sampai_tanggal']);
        $exp = explode("/",$sampai_tanggal);
        $sampai_tanggal = $exp[2]."-".$exp[1]."-".$exp[0];

        $keterangan = antiSQLInjection($_POST['keterangan']);
        $nama_hari_libur = antiSQLInjection($_POST['nama_hari_libur']);

        $query = $db->query("INSERT INTO tb_public_holiday SET NamaHariLibur='$nama_hari_libur', DariTanggal='$dari_tanggal', SampaiTanggal='$sampai_tanggal', Keterangan='$keterangan', CreatedBy='".$_SESSION["uid"]."'");
        if($query){
            echo json_encode(array("res"=>1));
        } else {
            echo json_encode(array("res"=>0,"msg"=>"Data Public Holiday tidak dapat disimpan. Silahkan coba kembali nanti."));
        }
    break;
        
    case "Detail":
        $detail = array();
        $id = antiSQLInjection($_GET['id']);

        $query = $db->get_row("SELECT *, DATE_FORMAT(DariTanggal, '%d/%m/%Y') AS DariTanggalID, DATE_FORMAT(SampaiTanggal, '%d/%m/%Y') AS SampaiTanggalID FROM tb_public_holiday WHERE IDPublicHoliday='$id' ORDER BY IDPublicHoliday ASC");
        if($query){
            $detail = array("nama_hari_libur"=>$query->NamaHariLibur,"dari_tanggal"=>$query->DariTanggalID,"sampai_tanggal"=>$query->SampaiTanggalID,"keterangan"=>$query->Keterangan);
        }
        echo json_encode(array("detail"=>$detail));
    break;
    
    case "EditRecord":
        $dari_tanggal = antiSQLInjection($_POST['dari_tanggal']);
        $exp = explode("/",$dari_tanggal);
        $dari_tanggal = $exp[2]."-".$exp[1]."-".$exp[0];
        
        $sampai_tanggal = antiSQLInjection($_POST['sampai_tanggal']);
        $exp = explode("/",$sampai_tanggal);
        $sampai_tanggal = $exp[2]."-".$exp[1]."-".$exp[0];

        $keterangan = antiSQLInjection($_POST['keterangan']);
        $nama_hari_libur = antiSQLInjection($_POST['nama_hari_libur']);
        
        $id = antiSQLInjection($_POST['id']);
        
        $query = $db->query("UPDATE tb_public_holiday SET NamaHariLibur='$nama_hari_libur', DariTanggal='$dari_tanggal', SampaiTanggal='$sampai_tanggal', Keterangan='$keterangan', CreatedBy='".$_SESSION["uid"]."' WHERE IDPublicHoliday='$id'");
        if($query){
            echo json_encode(array("res"=>1));
        } else {
            echo json_encode(array("res"=>0,"msg"=>"Data Public Holiday tidak dapat disimpan. Silahkan coba kembali nanti."));
        }
    break;
        
    default:
        echo "";
}