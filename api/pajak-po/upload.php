<?php
include_once "../config/connection.php";

$nopo = $_POST['nopo'];
$daftarFakturPajak = $_POST['daftarFakturPajak'];
$nopoReplace = str_replace("/", "", $nopo);
if($_FILES['file']){
    $file_name = $_FILES['file']['name'];
    $file_size =$_FILES['file']['size'];
    $file_tmp =$_FILES['file']['tmp_name'];
    $file_type=$_FILES['file']['type'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $photoName = $nopoReplace."_".date("ms").".".$file_ext;
    move_uploaded_file($file_tmp,"../../files/pajak_po/".$photoName);

    /* unlink previous file */
    $query = $db->get_row("SELECT * FROM tb_po WHERE NoPO='$nopo'");
    if($query){
        if($query->FakturPajak!="" && file_exists("../../files/pajak_po/".$query->FakturPajak)){
            unlink("../../files/pajak_po/".$query->FakturPajak);
        }
    }
} else {
    $photoName = "";
}

$query = $db->query("UPDATE tb_po SET FakturPajak='$photoName', DaftarFakturPajak='$daftarFakturPajak' WHERE NoPO='$nopo'");
if($query){
    echo "1";
} else {
    echo "0";
}