<?php
include_once "../config/connection.php";

$tanggal = antiSQLInjection($_POST['tanggal']);
$exptgl = explode("/",$tanggal);
$tanggal = $exptgl[2]."-".$exptgl[1]."-".$exptgl[0];
$tanggalCond = $exptgl[2]."-".$exptgl[1];
$tanggalCond2 = $exptgl[2].$exptgl[1];
$tanggalCond3 = $exptgl[2]."/".$exptgl[1]."/";

$no_return_konsumen = antiSQLInjection($_POST['no_return_konsumen']);
$usrlogin = antiSQLInjection($_POST['usrlogin']);
$supplier = antiSQLInjection($_POST['supplier']);
$total = str_replace(",","",antiSQLInjection($_POST['total']));
$diskon_persen = antiSQLInjection($_POST['diskon_persen']);
$diskon = str_replace(",","",antiSQLInjection($_POST['diskon']));
$total2 = str_replace(",","",antiSQLInjection($_POST['total2']));
$ppn_persen = antiSQLInjection($_POST['ppn_persen']);
$ppn = str_replace(",","",antiSQLInjection($_POST['ppn']));
$grand_total = str_replace(",","",antiSQLInjection($_POST['grand_total']));
$pembayarandp = str_replace(",","",antiSQLInjection($_POST['pembayarandp']));
$sisa = str_replace(",","",antiSQLInjection($_POST['sisa']));
$keterangan = antiSQLInjection($_POST['keterangan']);
$uploaded = antiSQLInjection($_POST['uploaded']);
$cartArray = antiSQLInjection($_POST['cart']);
$cartArray = json_decode($cartArray);

$metode_pembayaran = antiSQLInjection($_POST['metode_pembayaran']);
$metode_pembayaran2 = antiSQLInjection($_POST['metode_pembayaran2']);
$nobg = antiSQLInjection($_POST['nobg']);
$jatuhtempobg = antiSQLInjection($_POST['jatuhtempobg']);
$kembali = antiSQLInjection($_POST['kembali']);
$ongkos_kirim = antiSQLInjection($_POST['ongkos_kirim']);

$totalitem = antiSQLInjection($_POST['totalitem']);

if($diskon_persen=="") $diskon_persen="0";
if($ppn_persen=="") $ppn_persen="0";

$lanjut = true;
foreach($cartArray as $data){
    if(isset($data)){
        if($data->IsSerialize=="1"){
            $cek = $db->get_row("SELECT * FROM tb_stok_gudang WHERE IDBarang='".$data->IDBarang."' AND SN='".$data->SNBarang."' AND SisaStok>0");
            if(!$cek){
                $message = "Return tidak dapat disimpan karena SN '".$data->SNBarang."' tidak terdaftar pada data Stok. Silahkan cek Serial Number Barang yang terdaftar pada Persediaan Stok Gudang.";
                $lanjut=false;
            }
        }
    }
}

if($lanjut){
    $dataLast = $db->get_row("SELECT * FROM tb_return_po WHERE DATE_FORMAT(Tanggal,'%Y-%m')='".$tanggalCond."' ORDER BY NoReturn DESC");
    if($dataLast){
        $last = substr($dataLast->NoReturn,-3);
            $last++;
            if($last<100 and $last>=10)
                $last = "0".$last;
            else if($last<10)
                $last = "00".$last;
        $no_return = "RT/SPN/".$tanggalCond3.$last;
    } else {
        $no_return = "RT/SPN/".$tanggalCond3."00001";
    }

    $query = $db->query("INSERT INTO tb_return_po SET NoReturn='$no_return', IDReturnKonsumen='', NoReturnKonsumen='$no_return_konsumen', IDSupplier='$supplier', Tanggal='$tanggal', Total='$total', OngkosKirim='$ongkos_kirim', GrandTotal='$grand_total', PembayaranDP='$pembayarandp', Sisa='$sisa', Keterangan='$keterangan', MetodePembayaran1='$metode_pembayaran', MetodePembayaran2='$metode_pembayaran2', JatuhTempoBG='$jatuhtempobg', NoBG='$nobg', TotalPembayaran='$pembayarandp', Kembali='$kembali', TotalItem='$totalitem', CreatedBy='".$_SESSION["uid"]."'");
    if($query){
        $id = $db->get_var("SELECT LAST_INSERT_ID()");
        echo json_encode(array("res"=>1,"mes"=>"Return Barang berhasil disimpan."));

        foreach($cartArray as $data){
            if(isset($data)){
                if($data->IsSerialize=="1"){
                    $qty = $data->QtyBarang;
                    $savedStok = $qty;

                    $queryHPP = $db->get_row("SELECT * FROM tb_stok_gudang WHERE IDBarang='".$data->IDBarang."' AND SN='".$data->SNBarang."' AND SisaStok>0");
                    $HPP = $queryHPP->Harga;

                    $db->query("INSERT INTO tb_return_po_detail SET NoReturn='$no_return', NoUrut='".$data->NoUrut."', IDBarang='".$data->IDBarang."', NamaBarang='".$data->NamaBarang."', Qty='".$qty."', Harga='".$HPP."', SubTotal='".$HPP."', SN='".$data->SNBarang."', IDStok='".$queryHPP->IDStokGudang."', StokFrom='0'");

                    $db->query("UPDATE tb_stok_gudang SET SisaStok=(SisaStok-$qty) WHERE IDStokGudang='".$queryHPP->IDStokGudang."'");
                    $db->query("UPDATE tb_barang SET StokGudang=(StokGudang-$qty) WHERE IDBarang='".$data->IDBarang."'");
                } else {
                    $qty = $data->QtyBarang;
                    $savedStok = 0;
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

                            $subTotal = $qtySimpan*$data->HPP;
                            $savedStok += $qtySimpan;

                            $db->query("INSERT INTO tb_return_po_detail SET NoReturn='$no_return', NoUrut='".$data->NoUrut."', IDBarang='".$data->IDBarang."', NamaBarang='".$data->NamaBarang."', Qty='".$qtySimpan."', Harga='".$data->HPP."', SubTotal='".$subTotal."', IDStok='".$dataHPP->IDStokGudang."', StokFrom='0'");
                            $db->query("UPDATE tb_stok_gudang SET SisaStok='$stokHPP' WHERE IDStokGudang='".$dataHPP->IDStokGudang."'");
                            $db->query("UPDATE tb_barang SET StokGudang=(StokGudang-$qtySimpan) WHERE IDBarang='".$data->IDBarang."'");

                            if($exit==1){
                                $qty = 0;
                                break;
                            }
                        }
                    }
                }
                $stokGudang = $db->get_var("SELECT SUM(SisaStok) FROM tb_stok_gudang WHERE IDBarang='".$data->IDBarang."'");
                $db->query("INSERT INTO tb_kartu_stok_gudang SET ID='$id', IDBarang='".$data->IDBarang."', StokPenyesuaian='-".$savedStok."', StokAkhir='".$stokGudang."', Tipe='5', Keterangan='Pengiriman Barang. No Surat Jalan $nopengiriman', Tanggal='$tanggal'");
            }
        }
    } else {
        echo json_encode(array("res"=>0,"mes"=>"Return Barang tidak dapat disimpan. Silahkan coba kembali."));
    }
} else {
    echo json_encode(array("res"=>0,"mes"=>$message));
}