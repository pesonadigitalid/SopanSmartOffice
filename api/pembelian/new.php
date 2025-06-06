<?php
include_once "../config/connection.php";

$tanggal = antiSQLInjection($_POST['tanggal']);
$exp = explode("/",$tanggal);
$tanggal = $exp[2]."-".$exp[1]."-".$exp[0];
$dateID = $exp[2].$exp[1];

$no_po = antiSQLInjection($_POST['no_po']);
$usrlogin = antiSQLInjection($_POST['usrlogin']);
$supplier = antiSQLInjection($_POST['supplier']);
$proyek = antiSQLInjection($_POST['proyek']);
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

$metode_pembayaran = antiSQLInjection($_POST['metode_pembayaran']);
$metode_pembayaran2 = antiSQLInjection($_POST['metode_pembayaran2']);
$nobg = antiSQLInjection($_POST['nobg']);
$jatuhtempobg = antiSQLInjection($_POST['jatuhtempobg']);
$kembali = antiSQLInjection($_POST['kembali']);

if($jatuhtempobg!=""){
    $exp = explode("/",$jatuhtempobg);
    $jatuhtempobg = $exp[2]."-".$exp[1]."-".$exp[0];
}

$cartArray = json_decode($cartArray);

$data_po = $db->get_row("SELECT * FROM tb_po WHERE NoPO='$no_po'");
$data_proyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='$proyek'");

if($diskon_persen=="") $diskon_persen="0";
if($ppn_persen=="") $ppn_persen="0";
if($data_po->IDProyek=="") $data_po->IDProyek="0";
if($data_po->KodeProyek=="") $data_po->KodeProyek="0";

$dataLast = $db->get_row("SELECT * FROM tb_pembelian ORDER BY NoPembelian DESC");
if($dataLast){
    $last = substr($dataLast->NoPembelian,-5);
    $last++;
    if($last<10000 and $last>=1000)
        $last = "0".$last;
    else if($last<1000 and $last>=100)
        $last = "00".$last;
    else if($last<100 and $last>=10)
        $last = "000".$last;
    else if($last<10)
        $last = "0000".$last;
    $nopembelian = "BL".$dateID.$last;  
} else {
    $nopembelian = "BL".$dateID."00001"; 
}
//if($keterangan=="") $keterangan="(NULL)";

$metode_pembayaran = $_POST['metode_pembayaran'];
$metode_pembayaran2 = $_POST['metode_pembayaran2'];
$nobg = $_POST['nobg'];
$jatuhtempobg = $_POST['jatuhtempobg'];

if($pembayarandp<=0){
    $metode_pembayaran = "";
    $metode_pembayaran2 = "";
}

$query = $db->query("INSERT INTO tb_pembelian SET NoPembelian='$nopembelian', NoPO='$no_po', IDProyek='".$data_proyek->IDProyek."', KodeProyek='".$data_proyek->KodeProyek."', IDSupplier='$supplier', Tanggal='$tanggal', Total='$total', DiskonPersen='$diskon_persen', Diskon='$diskon', Total2='$total2', PPNPersen='$ppn_persen', PPN='$ppn', GrandTotal='$grand_total', PembayaranDP='$pembayarandp', Sisa='$sisa', Keterangan='$keterangan', MetodePembayaran1='$metode_pembayaran', MetodePembayaran2='$metode_pembayaran2', JatuhTempoBG='$jatuhtempobg', NoBG='$nobg', Kembali='$kembali', CreatedBy='".$_SESSION["uid"]."'");
if($query){
    echo "1";
    $id = mysql_insert_id();
    foreach($cartArray as $data){
        if(isset($data)){
            $harga = str_replace(",","",$data->Harga);
            $sub_total = str_replace(",","",$data->SubTotal);
            $query2 = $db->query("INSERT INTO tb_pembelian_detail SET NoPembelian='$nopembelian', NoUrut='".$data->NoUrut."', IDBarang='".$data->IDBarang."', NamaBarang='".$data->NamaBarang."', Qty='".$data->QtyBarang."', Harga='".$harga."', SubTotal='".$sub_total."'");
            
            if($no_po==""){
                $querystok = $db->query("INSERT INTO tb_stok_gudang SET IDBarang='".$data->IDBarang."', Stok='".$data->QtyBarang."', SisaStok='".$data->QtyBarang."', Harga='".$harga."', IDPembelian='".$id."'");
            } else {
                $querystok = $db->query("INSERT INTO tb_stok_purchasing SET IDBarang='".$data->IDBarang."', Stok='".$data->QtyBarang."', SisaStok='".$data->QtyBarang."', Harga='".$harga."', IDPembelian='".$id."', IDProyek='".$data_po->IDProyek."'");
            }

            //lastStok
            $stokGudang = $db->get_var("SELECT SUM(SisaStok) FROM tb_stok_gudang WHERE IDBarang='".$data->IDBarang."'");
            $stokPurchasing = $db->get_var("SELECT SUM(SisaStok) FROM tb_stok_purchasing WHERE IDBarang='".$data->IDBarang."'");

            $db->query("UPDATE tb_barang SET StokGudang='$stokGudang', StokPurchasing='$stokPurchasing' WHERE IDBarang='".$data->IDBarang."'");
        }
    }
    
    if($pembayarandp!=0){
        if($no_po!=""){
            $tanggalPembayaranHutang = $data_po->Tanggal;
        } else {
            $tanggalPembayaranHutang = $tanggal;
        }
        $dataLast = $db->get_row("SELECT * FROM tb_pembayaran_hutang WHERE Tanggal='".$tanggalPembayaranHutang."' ORDER BY DateCreated DESC");
        if($dataLast){
            $last = substr($dataLast->NoPembayaran,-5);
            $last++;
            if($last<10000 and $last>=1000)
                $last = "0".$last;
            else if($last<1000 and $last>=100)
                $last = "00".$last;
            else if($last<100 and $last>=10)
                $last = "000".$last;
            else if($last<10)
                $last = "0000".$last;
            $noTransaksi2 = "PH".$dateID.$last;  
        } else
            $noTransaksi2 = "PH".$dateID."00001";
        $db->query("INSERT INTO tb_pembayaran_hutang SET NoPembayaran='$noTransaksi2', NoPembelian='$nopembelian', Tanggal='".$tanggalPembayaranHutang."', JumlahPembayaran='$pembayarandp', Sisa='$sisa', CreatedBy='".$_SESSION["uid"]."', Keterangan='PEMBAYARAN AWAL'");
        
        /* INPUT JURNAL */

    }
} else {
    echo 0;
}