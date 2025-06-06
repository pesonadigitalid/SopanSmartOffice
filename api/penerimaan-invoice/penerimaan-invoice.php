<?php
include_once "../config/connection.php";

$act = antiSQLInjection($_GET['act']);
switch ($act) {
        
    case "DataList":
        $spb = antiSQLInjection($_GET['spb']);

        $datestart = antiSQLInjection($_GET['datestart']);
        $expstart = explode("/",$datestart);
        $datestartchange = $expstart[2]."-".$expstart[1]."-".$expstart[0];

        $dateend = antiSQLInjection($_GET['dateend']);
        $expend = explode("/",$dateend);
        $dateendchange = $expend[2]."-".$expend[1]."-".$expend[0];

        $filterstatus = $_GET['filterstatus'];

        if ($datestart != "" && $dateend != "") {
            $cond = "WHERE Tanggal BETWEEN '$datestartchange' AND '$dateendchange'";
        } else if ($datestart != "") {
            $cond = "WHERE Tanggal='$datestartchange'";
        } else {
            $cond = "WHERE DATE_FORMAT(Tanggal,'%Y-%m') = '" . date("Y-m") . "'";
        }

        if($spb!="")
            $cond2 = " AND IDInvoice IN (SELECT IDInvoice FROM tb_penjualan_invoice WHERE IDPenjualan='$spb'";

        if($filterstatus=="approved"){
            $cond2 = " AND IDPenerimaan IN (SELECT NoRef FROM tb_jurnal WHERE Tipe='4') ";
        } else if($filterstatus=="unapproved"){
            $cond2 = " AND IDPenerimaan NOT IN (SELECT NoRef FROM tb_jurnal WHERE Tipe='4') ";
        }

        $dataPenerimaan = array();
        $query = $db->get_results("SELECT *,DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_penjualan_invoice_penerimaan $cond $cond2 ORDER BY IDPenerimaan DESC");
        if($query){
            $i=0;
            foreach($query as $data){
                $i++;
                $dInv = $db->get_row("SELECT * FROM tb_penjualan_invoice WHERE IDInvoice='".$data->IDInvoice."'");
                $dPnj = $db->get_row("SELECT * FROM tb_penjualan WHERE IDPenjualan='".$dInv->IDPenjualan."'");
                $cekApp = $db->get_row("SELECT * FROM tb_jurnal WHERE NoRef='".$data->IDPenerimaan."' AND Tipe='4'");
                if($cekApp) $app = 1; else $app = 0;
                array_push($dataPenerimaan,array("IDPenerimaan"=>$data->IDPenerimaan,"NoPenerimaan"=>$data->NoPenerimaan,"No"=>$i,"NoInvoice"=>$dInv->NoInvoice,"NoPenjualan"=>$dPnj->NoPenjualan,"Tanggal"=>$data->TanggalID,"Jumlah"=>$data->Jumlah,"Keterangan"=>$data->Keterangan,"Approved"=>1));
            }
        }

        $spb = array();
        $query = $db->get_results("SELECT * FROM tb_penjualan ORDER BY NoPenjualan ASC");
        if($query){
            foreach($query as $data){
                array_push($spb,array("IDPenjualan"=>$data->IDPenjualan,"NoPenjualan"=>$data->NoPenjualan,"NoPOKonsumen"=>$data->NoPOKonsumen));
            }
        }

        $TotalInvoice = $db->get_var("SELECT SUM(GrandTotal) FROM tb_penjualan_invoice $cond");
        if(!$TotalInvoice) $TotalInvoice=0;
        $TotalPenerimaan = $db->get_var("SELECT SUM(Jumlah) FROM tb_penjualan_invoice_penerimaan $cond");
        if(!$TotalPenerimaan) $TotalPenerimaan=0;
        $SisaPiutang = $TotalInvoice-$TotalPenerimaan;

        $All = $db->get_var("SELECT COUNT(*) FROM tb_penjualan_invoice_penerimaan");
        if(!$All) $All=0;

        $Approved = $db->get_var("SELECT COUNT(*) FROM tb_penjualan_invoice_penerimaan WHERE IDPenerimaan IN (SELECT NoRef FROM tb_jurnal WHERE Tipe='4')");
        if(!$Approved) $Approved=0;

        $UnApproved = $db->get_var("SELECT COUNT(*) FROM tb_penjualan_invoice_penerimaan WHERE IDPenerimaan NOT IN (SELECT NoRef FROM tb_jurnal WHERE Tipe='4')");
        if(!$UnApproved) $UnApproved=0;

        echo json_encode(array("data" => $dataPenerimaan,"spb" => $spb,"TotalInvoice" => $TotalInvoice,"TotalPenerimaan" => $TotalPenerimaan,"SisaPiutang" => $SisaPiutang,"All" => $All,"Approved" => $Approved,"UnApproved" => $UnApproved));
        break;

    case "LoadAllRequirement":
        $invoice = array();

        $query = $db->get_results("SELECT * FROM tb_penjualan_invoice WHERE Sisa>0 ORDER BY NoInvoice ASC");
        if($query){
            foreach($query as $data){
                $terbayar = $db->get_var("SELECT SUM(Jumlah) FROM tb_penjualan_invoice_penerimaan WHERE IDInvoice='".$data->IDInvoice."'");
                if(!$terbayar) $terbayar=0;
                $sisa = $data->GrandTotal-$terbayar;
                array_push($invoice,array("IDInvoice"=>$data->IDInvoice,"NoInvoice"=>$data->NoInvoice,"Jumlah"=>$data->GrandTotal,"Terbayar"=>$terbayar,"Sisa"=>$sisa));
            }
        }

        echo json_encode($invoice);
        break;

    case "InsertNew":
        $tanggal = antiSQLInjection($_POST['tanggal']);
        $exp = explode("/",$tanggal);
        $tanggal = $exp[2]."-".$exp[1]."-".$exp[0];
        $tanggal2 = $exp[2]."/".$exp[1]."/";

        $idInvoice = antiSQLInjection($_POST['idInvoice']);
        
        $jumlah = antiSQLInjection($_POST['jumlah']);
        $keterangan = antiSQLInjection($_POST['keterangan']);

        $dataLast = $db->get_row("SELECT * FROM tb_penjualan_invoice_penerimaan WHERE DATE_FORMAT(Tanggal,'%Y-%m')='".date("Y-m")."' ORDER BY NoPenerimaan DESC");
        if($dataLast){
            $last = substr($dataLast->NoPenerimaan,-3);
            $last++;
            if($last<100 and $last>=10)
                $last = "0".$last;
            else if($last<10)
                $last = "00".$last;
            $no = "TR/".$tanggal2.$last;
        } else {
            $no = "TR/".$tanggal2."001";
        }

        $query = $db->query("INSERT INTO tb_penjualan_invoice_penerimaan SET NoPenerimaan='$no', Tanggal='$tanggal', IDInvoice='".$idInvoice."', Jumlah='".$jumlah."', Keterangan='$keterangan', CreatedBy='".$_SESSION["uid"]."'");
        if($query){
            //COUNT SISA
            $terbayar = $db->get_var("SELECT SUM(Jumlah) FROM tb_penjualan_invoice_penerimaan WHERE IDInvoice='$idInvoice'");
            if(!$terbayar) $terbayar=0;
            $db->query("UPDATE tb_penjualan_invoice SET Sisa=(GrandTotal-$terbayar) WHERE IDInvoice='$idInvoice'");
            $dInv = $db->get_row("SELECT * FROM tb_penjualan_invoice WHERE IDInvoice='$idInvoice'");
            $db->query("UPDATE tb_penjualan SET Sisa=(GrandTotal-$terbayar) WHERE IDPenjualan='".$dInv->IDPenjualan."'");
            echo "1";
        } else 
            echo "0";
        break;
        
    case "Delete":
        $idr = antiSQLInjection($_POST['idr']);
        $data = $db->get_row("SELECT * FROM tb_jurnal WHERE NoRef='$idr' AND Tipe='4'");
        if($data->Status==="1"){
            echo "2";
        } else {
            $data = $db->get_row("SELECT * FROM tb_penjualan_invoice_penerimaan WHERE IDPenerimaan='$idr'");
            $query = $db->query("DELETE FROM tb_penjualan_invoice_penerimaan WHERE IDPenerimaan='$idr'");
            if($query){
                $idInvoice = $data->IDInvoice;
                //COUNT SISA
                $terbayar = $db->get_var("SELECT SUM(Jumlah) FROM tb_penjualan_invoice_penerimaan WHERE IDInvoice='$idInvoice'");
                if(!$terbayar) $terbayar=0;
                $db->query("UPDATE tb_penjualan_invoice SET Sisa=(GrandTotal-$terbayar) WHERE IDInvoice='$idInvoice'");
                $dInv = $db->get_row("SELECT * FROM tb_penjualan_invoice WHERE IDInvoice='$idInvoice'");
                $db->query("UPDATE tb_penjualan SET Sisa=(GrandTotal-$terbayar) WHERE IDPenjualan='".$dInv->IDPenjualan."'");
                echo "1";
            } else {
                echo "0";
            }
        }
        break;
        
        
    
    case "Detail":
        $id = antiSQLInjection($_GET['id']);

        $detail = array();

        $query = $db->get_row("SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_penjualan_invoice_penerimaan WHERE IDPenerimaan='$id'");
        if($query){
            $detail = array("IDPenerimaan"=>$query->IDPenerimaan,"Tanggal"=>$query->TanggalID,"NoPenerimaan"=>$query->NoPenerimaan,"IDInvoice"=>$query->IDInvoice,"Jumlah"=>$query->Jumlah,"Keterangan"=>$query->Keterangan);
        }

        $invoice = array();

        $query = $db->get_results("SELECT * FROM tb_penjualan_invoice ORDER BY NoInvoice ASC");
        if($query){
            foreach($query as $data){
                $terbayar = $db->get_var("SELECT SUM(Jumlah) FROM tb_penjualan_invoice_penerimaan WHERE IDInvoice='".$data->IDInvoice."'");
                if(!$terbayar) $terbayar=0;
                $sisa = $data->GrandTotal-$terbayar;
                array_push($invoice,array("IDInvoice"=>$data->IDInvoice,"NoInvoice"=>$data->NoInvoice,"Jumlah"=>$data->GrandTotal,"Terbayar"=>$terbayar,"Sisa"=>$sisa));
            }
        }

        echo json_encode(array("data"=>$detail,"invoice"=>$invoice));
        break;

    case "Edit":
        $noinv = antiSQLInjection($_POST['noinv']);
        $keterangan = antiSQLInjection($_POST['keterangan']);
        $db->query("UPDATE tb_penjualan_invoice_penerimaan SET Keterangan='$keterangan' WHERE NoPenerimaan='$noinv'");
        echo "1";
        break;

    default:
        echo "";
}