<?php
include_once "../config/connection.php";

$act = antiSQLInjection($_GET['act']);
switch ($act) {
    case "DataList":
        $pelangganArray = array();
        $pengirimanArray = array();
        $pelanggan = antiSQLInjection($_GET['pelanggan']);
        $filterstatus = antiSQLInjection($_GET['filterstatus']);

        $datestart = antiSQLInjection($_GET['datestart']);
        $expstart = explode("/",$datestart);
        $datestartchange = $expstart[2]."-".$expstart[1]."-".$expstart[0];

        $dateend = antiSQLInjection($_GET['dateend']);
        $expend = explode("/",$dateend);
        $dateendchange = $expend[2]."-".$expend[1]."-".$expend[0];

        if ($datestart != "" && $dateend != "") {
            $cond = "AND Tanggal BETWEEN '$datestartchange' AND '$dateendchange'";
        } else if ($datestart != "") {
            $cond = "AND Tanggal='$datestartchange'";
        } else {
            $cond = "AND DATE_FORMAT(Tanggal,'%Y-%m') = '" . date("Y-m") . "'";
        }

        if($pelanggan != "")
            $cond .= " AND IDPelanggan='$pelanggan'";

        if($filterstatus == "Lunas")
            $cond .= " AND Sisa<='0'";
        else if($filterstatus == "Hutang")
            $cond .= " AND Sisa>'0'";

        $query = $db->get_results("SELECT *, DATE_FORMAT(Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_pengiriman_mms ORDER BY NoPengiriman ASC");
        if($query){
            $i=0;
            foreach($query as $data){
                $i++;
                array_push($pengirimanArray,array("IDPengiriman"=>$data->IDPengiriman,"NoPengiriman"=>$data->NoPengiriman,"NoPenjualan"=>$data->NoPenjualan,"Tanggal"=>$data->TanggalID,"Total"=>$data->Total,"GrandTotal"=>$data->GrandTotal,"Keterangan"=>$data->Keterangan,"DeliveredBy"=>$data->DeliveredBy,"No"=>$i));
            }
        }

        $query = $db->get_results("SELECT * FROM tb_pelanggan WHERE Status='1' ORDER BY NamaPelanggan ASC");
        if($query){
            foreach($query as $data){
                array_push($pelangganArray,array("IDPelanggan"=>$data->IDPelanggan,"KodePelanggan"=>$data->KodePelanggan,"NamaPelanggan"=>$data->NamaPelanggan));
            }
        }
        $return = array("pengiriman"=>$pengirimanArray,"pelanggan"=>$pelangganArray);
        echo json_encode($return);
        break;

    case "LoadAllRequirement":
        $penjualanArray = array();
        $barangArray = array();

        $query = $db->get_results("SELECT * FROM tb_penjualan ORDER BY NoPenjualan ASC");
        if($query){
            foreach($query as $data){
                array_push($penjualanArray,array("IDPenjualan"=>$data->IDPenjualan,"NoPenjualan"=>$data->NoPenjualan));
            }
        }

        $query = $db->get_results("SELECT * FROM tb_barang ORDER BY Nama");
        if($query){
            $i=0;
            foreach($query as $data){
                $i++;
                array_push($barangArray,array("IDBarang"=>$data->IDBarang,"KodeBarang"=>$data->KodeBarang,"Nama"=>$data->Nama,"No"=>$i,"Harga"=>$data->Harga,"HargaJual"=>$data->HargaJual,"IsSerialize"=>$data->IsSerialize,"Limit"=>$data->StokGudang));
            }
        }

        $return = array("barang"=>$barangArray,"penjualan"=>$penjualanArray);
        echo json_encode($return);
        break;

    case "InsertNew":
        $tanggal = antiSQLInjection($_POST['tanggal']);
        $exp = explode("/",$tanggal);
        $tanggal = $exp[2]."-".$exp[1]."-".$exp[0];
        $tanggalCond = $exp[2]."-".$exp[1];
        $tanggalCond2 = $exp[2].$exp[1];

        $nopenjualan = antiSQLInjection($_POST['nopenjualan']);
        $total_item = antiSQLInjection($_POST['total_item']);
        $keterangan = antiSQLInjection($_POST['keterangan']);

        $cartArray = antiSQLInjection($_POST['cart']);
        $cartArray = json_decode($cartArray);

        $dataLast = $db->get_row("SELECT * FROM tb_pengiriman_mms WHERE DATE_FORMAT(Tanggal,'%Y-%m')='".$tanggalCond."' ORDER BY NoPengiriman DESC");
        if($dataLast){
            $last = intval(substr($dataLast->NoPengiriman,-5));
            $last++;
            if($last<10000 and $last>=1000)
                $last = "0".$last;
            else if($last<1000 and $last>=100)
                $last = "00".$last;
            else if($last<100 and $last>=10)
                $last = "000".$last;
            else if($last<10)
                $last = "0000".$last;
            $notransaksi = "SPK".$tanggalCond2.$last;
        } else {
            $notransaksi = "SPK".$tanggalCond2."00001";
        }
        
        $lanjut=true;
        
        //CEK BARANG APAKAH MASIH ADA STOK ATAU TIDAK
        foreach($cartArray as $data){
            if(isset($data)){
                if($data->SNBarang!=""){
                    $barang = $db->get_row("SELECT * FROM tb_stok_gudang WHERE IDBarang='".$data->IDBarang."' AND SN='".$data->SNBarang."'");
                    if(!$barang || $barang->SisaStok<=0){
                        $barang = $db->get_row("SELECT * FROM tb_barang WHERE IDBarang='".$data->IDBarang."'");
                        $message = "Penjualan tidak dapat dilakukan karena Stok. ".$barang->Nama." tidak mencukupi atau Serial Number Barang tersebut tidak terdaftar.";
                        $lanjut=false;
                    }
                } else {
                    $barang = $db->get_row("SELECT * FROM tb_barang WHERE IDBarang='".$data->IDBarang."'");
                    if($barang->StokGudang<$data->QtyBarang){
                        $message = "Penjualan tidak dapat dilakukan karena Stok. ".$barang->Nama." tidak mencukupi. Stok Gudang = ".$barang->StokGudang."; Stok Penjualan = ".$data->QtyBarang;
                        $lanjut=false;
                    }
                }
            }
        }
        
        if($lanjut){
            $query = $db->query("INSERT INTO tb_pengiriman_mms SET NoPengiriman='$notransaksi', NoPenjualan='$nopenjualan', Tanggal='$tanggal', Total='$total_item', Keterangan='$keterangan', CreatedBy='".$_SESSION["uid"]."'");

            if($query){
                echo json_encode(array("res"=>1,"mes"=>"Data pengiriman barang berhasil disimpan!"));
                foreach($cartArray as $data){
                    if(isset($data)){
                        $db->query("INSERT INTO tb_pengiriman_mms_detail SET NoPengiriman='$notransaksi', NoUrut='".$data->NoUrut."', IDBarang='".$data->IDBarang."', NamaBarang='".$data->NamaBarang."', Qty='".$data->QtyBarang."', SN='".$data->SNBarang."', Harga='".$data->Harga."', SubTotal='".$data->SubTotal."'");
                    }
                }
            } else {
                echo json_encode(array("res"=>0,"mes"=>"Data pengiriman gagal disimpan. Silahkan coba kembali nanti."));
            }
        } else {
            echo json_encode(array("res"=>0,"mes"=>$message));
        }
        break;
        
    case "Delete":
        $idr = antiSQLInjection($_POST['idr']);

        $allow = 1;
        $dataPenjualan = $db->get_row("SELECT * FROM tb_pengiriman_mms WHERE IDPengiriman='$idr'");

        if($allow==0){
            echo "2";
        } else {
            $query = $db->query("DELETE FROM tb_pengiriman_mms WHERE IDPengiriman='$idr'");
            if($query){
                $db->query("DELETE FROM tb_pengiriman_mms_detail WHERE NoPengiriman='".$dataPenjualan->NoPengiriman."'");
                echo "1";
            } else {
                echo "0";
            }
        }
        break;
    
    case "Detail":
        $id = antiSQLInjection($_GET['id']);
        $master = array();
        $detail = array();
        $data = $db->get_row("SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_pengiriman_mms WHERE IDPengiriman='$id' ORDER BY IDPengiriman ASC");
        if($data){
            $master = array("IDPengiriman"=>$data->IDPengiriman,"NoPengiriman"=>$data->NoPengiriman,"NoPenjualan"=>$data->NoPenjualan,"Tanggal"=>$data->TanggalID,"TotalItem"=>$data->Total,"Keterangan"=>$data->Keterangan,"No"=>$i);

            $query = $db->get_results("SELECT * FROM tb_pengiriman_mms_detail WHERE NoPengiriman='".$data->NoPengiriman."' ORDER BY NoUrut ASC");
            if($query){
                $i=0;
                foreach($query as $data){
                    $i++;
                    array_push($detail,array("NamaBarang"=>$data->NamaBarang,"No"=>$i,"Qty"=>$data->Qty,"SN"=>$data->SN,"Harga"=>$data->Harga,"SubTotal"=>$data->SubTotal));
                }
            }

        }
        echo json_encode(array("master"=>$master,"detail"=>$detail));
        break;
    default:
        echo "";
}