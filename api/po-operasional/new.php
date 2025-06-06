<?php
include_once "../config/connection.php";

$tanggal = antiSQLInjection($_POST['tanggal']);
$exptgl = explode("/",$tanggal);
$tanggal = $exptgl[2]."-".$exptgl[1]."-".$exptgl[0];

$id_proyek = antiSQLInjection($_POST['id_proyek']);
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

if($pembayarandp<=0){
    $metode_pembayaran = "";
    $metode_pembayaran2 = "";
}

//check grandtotal is still bellow the limit.
$dataProyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='$id_proyek'");
$limitBelanja = $dataProyek->LimitPengeluaranMaterial-$dataProyek->PengeluaranMaterial;
if($grand_total>$limitBelanja){
    echo "2";
} else {
    if($diskon_persen=="") $diskon_persen="0";
    if($ppn_persen=="") $ppn_persen="0";

    $kode_proyek = $dataProyek->KodeProyek;

    $dataLast = $db->get_row("SELECT * FROM tb_po WHERE DATE_FORMAT(Tanggal,'%Y-%m')='".date("Y-m")."' ORDER BY IDPO DESC");
    if($dataLast){
        $last = substr($dataLast->NoPO,-5);
        $last++;
        if($last<10000 and $last>=1000)
            $last = "0".$last;
        else if($last<1000 and $last>=100)
            $last = "00".$last;
        else if($last<100 and $last>=10)
            $last = "000".$last;
        else if($last<10)
            $last = "0000".$last;
        $no_po = "PO".date("Ym").$last;  
    } else {
        $no_po = "PO".date("Ym")."00001";
    }

    $query = $db->query("INSERT INTO tb_po SET NoPO='$no_po', IDProyek='$id_proyek', KodeProyek='$kode_proyek', Tanggal='$tanggal', IDSupplier='$supplier', Total='$total', DiskonPersen='$diskon_persen', Diskon='$diskon', Total2='$total2', PPNPersen='$ppn_persen', PPN='$ppn', GrandTotal='$grand_total', PembayaranDP='$pembayarandp', Sisa='$sisa', Keterangan='$keterangan', MetodePembayaran1='$metode_pembayaran', MetodePembayaran2='$metode_pembayaran2', JatuhTempoBG='$jatuhtempobg', NoBG='$nobg', Kembali='$kembali', CreatedBy='".$_SESSION["uid"]."'");
    if($query){
        echo "1";
        //$id = mysql_insert_id();
        foreach($cartArray as $data){
            if(isset($data)){
                $harga = str_replace(",","",$data->Harga);
                $sub_total = str_replace(",","",$data->SubTotal);
                $query2 = $db->query("INSERT INTO tb_po_detail SET NoPO='$no_po', NoUrut='".$data->NoUrut."', IDBarang='".$data->IDBarang."', NamaBarang='".$data->NamaBarang."', Qty='".$data->QtyBarang."', Harga='".$harga."', SubTotal='".$sub_total."'");

                $db->query("UPDATE tb_barang SET Harga='".$harga."' WHERE IDBarang='".$data->IDBarang."'");
            }
        }

        if($pembayarandp>0){
            $dataLast = $db->get_row("SELECT * FROM tb_pembayaran_hutang WHERE DATE_FORMAT(Tanggal,'%Y-%m')='".date("Ym")."' ORDER BY DateCreated DESC");
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
                $noTransaksi2 = "PH".date("Ym").$last;  
            } else
                $noTransaksi2 = "PH".date("Ym")."00001";
            $db->query("INSERT INTO tb_pembayaran_hutang SET NoPembayaran='$noTransaksi2', NoPembelian='$no_po', Tanggal='".$tanggal."', JumlahPembayaran='$pembayarandp', Sisa='$sisa', CreatedBy='".$_SESSION["uid"]."', Keterangan='PEMBAYARAN AWAL'");
            
            /* INPUT JURNAL */
        }
    } else {
        echo "0";
    }
}