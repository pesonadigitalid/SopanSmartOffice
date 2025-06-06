<?php
include_once "../config/connection.php";

$cat = antiSQLInjection($_GET['kategori']);
$cat2 = antiSQLInjection($_GET['kategori2']);
$id = antiSQLInjection($_GET['id']);

if($cat2=="1"){
    $cond = " AND IDSupplier IN (SELECT id FROM tb_contact_category_user WHERE id_category='$id') ";
    $cond2 = " AND IDPelanggan IN (SELECT id FROM tb_contact_category_user WHERE id_category='$id') ";
} else if($cat2=="2"){
    $cond = " AND IDSupplier NOT IN (SELECT id FROM tb_contact_category_user WHERE id_category='$id') ";
    $cond2 = " AND IDPelanggan NOT IN (SELECT id FROM tb_contact_category_user WHERE id_category='$id') ";
}

if($cat!="0" && $cat!=""){
    $sql = "SELECT * FROM tb_supplier WHERE Kategori='$cat' $cond ORDER BY IDSupplier ASC";
    $sql2 = "SELECT * FROM tb_pelanggan WHERE Kategori='$cat' $cond2 ORDER BY IDPelanggan ASC";
} else {
    $sql = "SELECT * FROM tb_supplier WHERE IDSupplier>0 $cond ORDER BY IDSupplier ASC";
    $sql2 = "SELECT * FROM tb_pelanggan WHERE IDPelanggan>0 $cond2 ORDER BY IDPelanggan ASC";
}

$i=0;
$return = array();

$query = $db->get_results($sql);
if($query){
    foreach($query as $data){
        $i++;
        $cek = $db->get_row("SELECT * FROM tb_contact_category_user WHERE type='Supplier' AND id='".$data->IDSupplier."' AND id_category='$id'");
        if($cek) $checked = true; else $checked = false;
        array_push($return,array("IDContact"=>$data->IDSupplier,"No"=>$i,"Kode"=>$data->KodeSupplier,"Nama"=>$data->NamaPerusahaan,"Provinsi"=>$data->Provinsi,"NoTelp"=>$data->NoTelp,"Kategori"=>$data->Kategori,"Jenis"=>"Supplier","checked"=>$checked));
    }
}

$query = $db->get_results($sql2);
if($query){
    foreach($query as $data){
        $i++;
        $cek = $db->get_row("SELECT * FROM tb_contact_category_user WHERE type='Pelanggan' AND id='".$data->IDPelanggan."' AND id_category='$id'");
        if($cek) $checked = true; else $checked = false;
        array_push($return,array("IDContact"=>$data->IDPelanggan,"No"=>$i,"Kode"=>$data->KodePelanggan,"Nama"=>$data->NamaPelanggan,"Provinsi"=>$data->Provinsi,"NoTelp"=>$data->NoTelp,"Kategori"=>$data->Kategori,"Jenis"=>"Pelanggan","checked"=>$checked));
    }
}

echo json_encode($return);