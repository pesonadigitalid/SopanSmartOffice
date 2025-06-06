<?php
include_once "../config/connection.php";

$tanggal = antiSQLInjection($_POST['tanggal']);
$exp = explode("/",$tanggal);
$tanggal = $exp[2]."-".$exp[1]."-".$exp[0];

$id_proyek = antiSQLInjection($_POST['id_proyek']);
$usrlogin = antiSQLInjection($_POST['usrlogin']);
$totalqty = antiSQLInjection($_POST['totalqty']);
$keterangan = antiSQLInjection($_POST['keterangan']);
$status_pengiriman = antiSQLInjection($_POST['status_pengiriman']);
$uploaded = antiSQLInjection($_POST['uploaded']);
$cartArray = antiSQLInjection($_POST['cart']);
$cartArray = json_decode($cartArray);

$get_data = $db->get_row("SELECT IDProyek,KodeProyek FROM tb_proyek WHERE IDProyek='$id_proyek'");

if($diskon_persen=="") $diskon_persen="0";
if($ppn_persen=="") $ppn_persen="0";

$dataLast = $db->get_row("SELECT * FROM tb_pengiriman ORDER BY NoPengiriman DESC");
if($dataLast){
    $last = substr($dataLast->NoPengiriman,-5);
    $last++;
    if($last<10000 and $last>=1000)
        $last = "0".$last;
    else if($last<1000 and $last>=100)
        $last = "00".$last;
    else if($last<100 and $last>=10)
        $last = "000".$last;
    else if($last<10)
        $last = "0000".$last;
    $nopengiriman = "DO".date("Ym").$last;  
} else {
    $nopengiriman = "DO".date("Ym")."00001"; 
}

if($keterangan=="") $keterangan="(NULL)";
$query = $db->query("INSERT INTO tb_pengiriman SET NoPengiriman='$nopengiriman', IDProyek='".$get_data->IDProyek."', KodeProyek='".$get_data->KodeProyek."', Tanggal='$tanggal', Total='$totalqty', Status='$status_pengiriman', Keterangan='$keterangan', CreatedBy='$uploaded'");
if($query){
    echo "1";
    $id = mysql_insert_id();
    foreach($cartArray as $data){
        if(isset($data)){
            $harga = str_replace(",","",$data->Harga);
            $sub_total = str_replace(",","",$data->SubTotal);
            $query2 = $db->query("INSERT INTO tb_pengiriman_detail SET NoPengiriman='$nopengiriman', NoUrut='".$data->NoUrut."', IDBarang='".$data->IDBarang."', NamaBarang='".$data->NamaBarang."', Qty='".$data->QtyBarang."', Harga='".$harga."', SubTotal='".$sub_total."'");
        }
    }
} else {
    echo "0";
}