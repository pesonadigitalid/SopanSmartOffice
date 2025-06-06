<?php
include_once "../config/connection.php";

$act = antiSQLInjection($_GET['act']);
switch ($act) {
    case "DataList":
        $pelangganArray = array();
        $penjualanArray = array();
        $pelanggan = antiSQLInjection($_GET['pelanggan']);
        $filterstatus = antiSQLInjection($_GET['filterstatus']);

        $datestart = antiSQLInjection($_GET['datestart']);
        $expstart = explode("/",$datestart);
        $datestartchange = $expstart[2]."-".$expstart[1]."-".$expstart[0];

        $dateend = antiSQLInjection($_GET['dateend']);
        $expend = explode("/",$dateend);
        $dateendchange = $expend[2]."-".$expend[1]."-".$expend[0];

        if ($datestart != "" && $dateend != "") {
            $cond = "AND a.Tanggal BETWEEN '$datestartchange' AND '$dateendchange'";
        } else if ($datestart != "") {
            $cond = "AND a.Tanggal='$datestartchange'";
        } else {
            $cond = "AND DATE_FORMAT(a.Tanggal,'%Y-%m') = '" . date("Y-m") . "'";
        }

        if($pelanggan != "")
            $cond .= " AND a.IDPelanggan='$pelanggan'";

        if($filterstatus == "Lunas")
            $cond .= " AND a.Sisa<='0'";
        else if($filterstatus == "Hutang")
            $cond .= " AND a.Sisa>'0'";

        $query = $db->get_results("SELECT a.*, b.*, DATE_FORMAT(a.Tanggal,'%d/%m/%Y') AS TanggalID FROM tb_penjualan a, tb_pelanggan b WHERE a.IDPelanggan=b.IDPelanggan AND a.Tipe='2' $cond ORDER BY IDPenjualan ASC");
        if($query){
            $i=0;
            foreach($query as $data){
                $i++;
                array_push($penjualanArray,array("IDPenjualan"=>$data->IDPenjualan,"NoPenjualan"=>$data->NoPenjualan,"IDPelanggan"=>$data->IDPelanggan,"Pelanggan"=>$data->NamaPelanggan,"Tanggal"=>$data->TanggalID,"TotalItem"=>$data->TotalItem,"Total"=>$data->Total,"Diskon"=>$data->Diskon,"DiskonPersen"=>$data->DiskonPersen,"Total2"=>$data->Total2,"PPN"=>$data->PPN,"PPNPersen"=>$data->PPNPersen,"GrandTotal"=>$data->GrandTotal,"Status"=>$data->Status,"Keterangan"=>$data->Keterangan,"TotalPembayaran"=>$data->TotalPembayaran,"Sisa"=>$data->Sisa,"No"=>$i));
            }
        }

        $query = $db->get_results("SELECT * FROM tb_pelanggan WHERE Status='1' ORDER BY NamaPelanggan ASC");
        if($query){
            foreach($query as $data){
                array_push($pelangganArray,array("IDPelanggan"=>$data->IDPelanggan,"KodePelanggan"=>$data->KodePelanggan,"NamaPelanggan"=>$data->NamaPelanggan));
            }
        }
        $return = array("penjualan"=>$penjualanArray,"pelanggan"=>$pelangganArray);
        echo json_encode($return);
        break;

    case "LoadAllRequirement":
        $pelangganArray = array();
        $barangArray = array();

        $query = $db->get_results("SELECT * FROM tb_pelanggan WHERE Status='1' ORDER BY NamaPelanggan ASC");
        if($query){
            foreach($query as $data){
                array_push($pelangganArray,array("IDPelanggan"=>$data->IDPelanggan,"KodePelanggan"=>$data->KodePelanggan,"NamaPelanggan"=>$data->NamaPelanggan));
            }
        }

        $query = $db->get_results("SELECT * FROM tb_barang ORDER BY Nama");
        if($query){
            $i=0;
            foreach($query as $data){
                $i++;
                array_push($barangArray,array("IDBarang"=>$data->IDBarang,"KodeBarang"=>$data->KodeBarang,"Nama"=>$data->Nama,"No"=>$i,"Harga"=>$data->Harga,"HargaJual"=>$data->HargaJual,"IsSerialize"=>$data->IsSerialize,"Limit"=>$data->StokGudang,"HargaJualGrosir"=>$data->HargaJualGrosir));
            }
        }

        $return = array("barang"=>$barangArray,"pelanggan"=>$pelangganArray);
        echo json_encode($return);
        break;

    case "InsertNew":
        $tanggal = antiSQLInjection($_POST['tanggal']);
        $exp = explode("/",$tanggal);
        $tanggal = $exp[2]."-".$exp[1]."-".$exp[0];
        $tanggalCond = $exp[2]."-".$exp[1];
        $tanggalCond2 = $exp[2].$exp[1];

        $pelanggan = antiSQLInjection($_POST['pelanggan']);
        $total_item = antiSQLInjection($_POST['total_item']);
        $total = antiSQLInjection($_POST['total']);
        $diskon_persen = antiSQLInjection($_POST['diskon_persen']);
        $diskon = antiSQLInjection($_POST['diskon']);
        $total2 = antiSQLInjection($_POST['total2']);
        $ppn_persen = antiSQLInjection($_POST['ppn_persen']);
        $ppn = antiSQLInjection($_POST['ppn']);
        $grand_total = antiSQLInjection($_POST['grand_total']);
        $pembayarandp = antiSQLInjection($_POST['pembayarandp']);
        $sisa = antiSQLInjection($_POST['sisa']);
        $keterangan = antiSQLInjection($_POST['keterangan']);
        $metode_pembayaran = antiSQLInjection($_POST['metode_pembayaran']);
        $metode_pembayaran2 = antiSQLInjection($_POST['metode_pembayaran2']);
        $kembali = antiSQLInjection($_POST['kembali']);

        $cartArray = antiSQLInjection($_POST['cart']);
        $cartArray = json_decode($cartArray);

        $dataLast = $db->get_row("SELECT * FROM tb_penjualan WHERE DATE_FORMAT(Tanggal,'%Y-%m')='".$tanggalCond."' ORDER BY NoPenjualan DESC");
        if($dataLast){
            $last = intval(substr($dataLast->NoPenjualan,-5));
            $last++;
            if($last<10000 and $last>=1000)
                $last = "0".$last;
            else if($last<1000 and $last>=100)
                $last = "00".$last;
            else if($last<100 and $last>=10)
                $last = "000".$last;
            else if($last<10)
                $last = "0000".$last;
            $notransaksi = "JL".$tanggalCond2.$last;
        } else {
            $notransaksi = "JL".$tanggalCond2."00001";
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
            $query = $db->query("INSERT INTO tb_penjualan SET NoPenjualan='$notransaksi', IDPelanggan='$pelanggan', Tanggal='$tanggal', TotalItem='$total_item', Total='$total', Diskon='$diskon', DiskonPersen='$diskon_persen', Total2='$total2', PPN='$ppn', PPNPersen='$ppn_persen', GrandTotal='$grand_total', TotalPembayaran='$pembayarandp', Kembali='$kembali', Sisa='$sisa', MetodePembayaran1='$metode_pembayaran', MetodePembayaran2='$metode_pembayaran2', Keterangan='$keterangan', Tipe='2', CreatedBy='".$_SESSION["uid"]."'");

            if($query){
                echo json_encode(array("res"=>1,"mes"=>"Data penjualan barang berhasil disimpan!"));
                foreach($cartArray as $data){
                    if(isset($data)){
                        $qty = $data->QtyBarang;
                        if($data->SNBarang!=""){
                            $queryHPP = $db->get_row("SELECT * FROM tb_stok_gudang WHERE IDBarang='".$data->IDBarang."'  AND SisaStok>0 AND SN='".$data->SNBarang."'");
                            if($queryHPP){
                                $qtySimpan = 1;
                                $stokHPP = 0;
                                $hargaBeli = $queryHPP->Harga;
                                $hargaJual = $data->Harga;
                                $margin = ($hargaJual-$hargaBeli)*$qtySimpan;
                                $subTotal = $hargaJual*$qtySimpan;
                                    
                                $marginBarang = $hargaJual-$hargaBeli;
                                
                                $db->query("INSERT INTO tb_penjualan_detail SET NoPenjualan='$notransaksi', NoUrut='".$data->NoUrut."', IDBarang='".$data->IDBarang."', NamaBarang='".$data->NamaBarang."', Qty='".$qtySimpan."', SN='".$data->SNBarang."', Harga='".$hargaJual."', SubTotal='".$subTotal."', HargaBeli='".$hargaBeli."', Margin='".$margin."'");
                                $db->query("UPDATE tb_stok_gudang SET SisaStok='$stokHPP' WHERE IDStokGudang='".$queryHPP->IDStokGudang."'");
                                $db->query("UPDATE tb_barang SET StokGudang=(StokGudang-$qtySimpan), HargaJualGrosir='$hargaJual', MarginGrosir='$marginBarang' WHERE IDBarang='".$data->IDBarang."'");
                            }
                        } else {
                            $queryHPP = $db->get_results("SELECT * FROM tb_stok_gudang WHERE IDBarang='".$data->IDBarang."'  AND SisaStok>0");
                            if($queryHPP){
                                foreach($queryHPP as $dataHPP){
                                    if($qty>$dataHPP->SisaStok){
                                        $qty = $qty-$dataHPP->SisaStok;
                                        $qtySimpan = $dataHPP->SisaStok;
                                        $stokHPP = 0;
                                        $exit = 0;
                                    } else {
                                        $qtySimpan = $qty;
                                        $exit = 1;
                                        $stokHPP = $dataHPP->SisaStok-$qty;
                                    }

                                    $hargaBeli = $dataHPP->Harga;
                                    $hargaJual = $data->Harga;
                                    $margin = ($hargaJual-$hargaBeli)*$qtySimpan;
                                    $subTotal = $hargaJual*$qtySimpan;
                                    
                                    $marginBarang = $hargaJual-$hargaBeli;

                                    $db->query("INSERT INTO tb_penjualan_detail SET NoPenjualan='$notransaksi', NoUrut='".$data->NoUrut."', IDBarang='".$data->IDBarang."', NamaBarang='".$data->NamaBarang."', Qty='".$qtySimpan."', SN='', Harga='".$hargaJual."', SubTotal='".$subTotal."', HargaBeli='".$hargaBeli."', Margin='".$margin."'");
                                    $db->query("UPDATE tb_stok_gudang SET SisaStok='$stokHPP' WHERE IDStokGudang='".$dataHPP->IDStokGudang."'");

                                    $db->query("UPDATE tb_barang SET StokGudang=(StokGudang-$qtySimpan), HargaJualGrosir='$hargaJual', MarginGrosir='$marginBarang' WHERE IDBarang='".$data->IDBarang."'");

                                    if($exit==1){
                                        $qty = 0;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                echo json_encode(array("res"=>0,"mes"=>"Data penjualan gagal disimpan. Silahkan coba kembali nanti."));
            }
        } else {
            echo json_encode(array("res"=>0,"mes"=>$message));
        }
        break;
        
    case "Delete":
        $idr = antiSQLInjection($_POST['idr']);

        $allow = 1;
        $dataPenjualan = $db->get_row("SELECT * FROM tb_penjualan WHERE IDPenjualan='$idr'");

        if($allow==0){
            echo "2";
        } else {
            $query = $db->query("DELETE FROM tb_penjualan WHERE IDPenjualan='$idr'");
            if($query){
                //RECALCULATE STOK
                $query = $db->get_results("SELECT * FROM tb_penjualan_detail WHERE NoPenjualan='".$dataPenjualan->NoPenjualan."'");
                if($query){
                    foreach($query as $data){
                        $db->query("INSERT INTO tb_stok_gudang SET IDReturn='".$idr."' AND IDBarang='".$data->IDBarang."', Stok='".$data->Qty."', SisaStok='".$data->Qty."', SN='".$data->SN."', Harga='".$data->HargaBeli."'");

                        $stokGudang = $db->get_var("SELECT SUM(SisaStok) FROM tb_stok_gudang WHERE IDBarang='".$data->IDBarang."'");
    
                        $db->query("UPDATE tb_barang SET StokGudang='$stokGudang' WHERE IDBarang='".$data->IDBarang."'");
                    }
                }
                $db->query("DELETE FROM tb_penjualan_detail WHERE NoPenjualan='".$dataPenjualan->NoPenjualan."'");
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
        $data = $db->get_row("SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_penjualan WHERE IDPenjualan='$id' ORDER BY IDPenjualan ASC");
        if($data){
            $pelanggan = $db->get_row("SELECT * FROM tb_pelanggan WHERE IDPelanggan='".$data->IDPelanggan."'");
            $pelanggan = $pelanggan->KodePelanggan." - ".$pelanggan->NamaPelanggan;
            $master = array("IDPenjualan"=>$data->IDPenjualan,"NoPenjualan"=>$data->NoPenjualan,"IDPelanggan"=>$data->IDPelanggan,"Pelanggan"=>$pelanggan,"Tanggal"=>$data->TanggalID,"TotalItem"=>$data->TotalItem,"Total"=>$data->Total,"Diskon"=>$data->Diskon,"DiskonPersen"=>$data->DiskonPersen,"Total2"=>$data->Total2,"PPN"=>$data->PPN,"PPNPersen"=>$data->PPNPersen,"GrandTotal"=>$data->GrandTotal,"Status"=>$data->Status,"Keterangan"=>$data->Keterangan,"TotalPembayaran"=>$data->TotalPembayaran,"Sisa"=>$data->Sisa,"Kembali"=>$data->Kembali,"MetodePembayaran1"=>$data->MetodePembayaran1,"MetodePembayaran2"=>$data->MetodePembayaran2,"No"=>$i);

            $query = $db->get_results("SELECT * FROM tb_penjualan_detail WHERE NoPenjualan='".$data->NoPenjualan."' ORDER BY NoUrut ASC");
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