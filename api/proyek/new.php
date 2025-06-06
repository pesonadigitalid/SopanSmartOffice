<?php
include_once "../config/connection.php";
include_once "../library/class.proyek.php";
$proyek = new Proyek;

$no_kontrak = antiSQLInjection($_POST['no_kontrak']);
$nama_proyek = antiSQLInjection($_POST['nama_proyek']);
$tahun = antiSQLInjection($_POST['tahun']);
$client = antiSQLInjection($_POST['client']);
$kategori = antiSQLInjection($_POST['kategori']);
$kategori2 = antiSQLInjection($_POST['kategori2']);
$status = antiSQLInjection($_POST['status']);
$nominal = str_replace(",","",antiSQLInjection($_POST['nominal']));
$ppn_persen = antiSQLInjection($_POST['ppn_persen']);
$ppn = str_replace(",","",antiSQLInjection($_POST['ppn']));
$grand_total = str_replace(",","",antiSQLInjection($_POST['grand_total']));
$limit_peng_persen = antiSQLInjection($_POST['limit_peng_persen']);
$limit_pengeluaran = str_replace(",","",antiSQLInjection($_POST['limit_pengeluaran']));
$limit_material = str_replace(",","",antiSQLInjection($_POST['limit_material']));
$limit_tenaga = str_replace(",","",antiSQLInjection($_POST['limit_tenaga']));
$limit_overhead = str_replace(",","",antiSQLInjection($_POST['limit_overhead']));
$project_manager = antiSQLInjection($_POST['project_manager']);
$site_manager = antiSQLInjection($_POST['site_manager']);
$supervisor = antiSQLInjection($_POST['supervisor']);
$site_admin = antiSQLInjection($_POST['site_admin']);
$site_admin2 = antiSQLInjection($_POST['site_admin2']);
$locked = antiSQLInjection($_POST['locked']);

$tanggalmulai = antiSQLInjection($_POST['tanggalmulai']);
if($tanggalmulai!=''){
    $exptgl = explode("/",$tanggalmulai);
    $tanggalmulai = $exptgl[2]."-".$exptgl[1]."-".$exptgl[0];
}
$tanggalselesai = antiSQLInjection($_POST['tanggalselesai']);
if($tanggalselesai!=''){
    $exptgl = explode("/",$tanggalselesai);
    $tanggalselesai = $exptgl[2]."-".$exptgl[1]."-".$exptgl[0];
}

$prefix = $db->get_var("SELECT PrefixProject FROM tb_departement WHERE IDDepartement='$kategori'");

$dataLast = $db->get_row("SELECT * FROM tb_proyek WHERE Tahun='$tahun' AND IDDepartement='$kategori' ORDER BY KodeProyek DESC");
if($dataLast){
    $last = substr($dataLast->KodeProyek,-4);
    $last++;
    if($last<1000 and $last>=100)
        $last = "0".$last;
    else if($last<100 and $last>=10)
        $last = "00".$last;
    else if($last<10)
        $last = "000".$last;
    $kode_proyek = $prefix."-".$last;
} else {
    $kode_proyek = $prefix."-"."0001";
}

if($status=="") $status="0";
$total = $limit_material+$limit_tenaga+$limit_overhead;
if($total>$limit_pengeluaran){
    echo "2";
} else {
    $query = $db->query("INSERT INTO tb_proyek SET NoKontrak='$no_kontrak', KodeProyek='$kode_proyek', IDDepartement='$kategori', IDDepartementPemilik='$kategori2', Tahun='$tahun', NamaProyek='$nama_proyek', IDClient='$client', Nominal='$nominal', PPNPersen='$ppn_persen', PPN='$ppn', Total2='$grand_total', GrandTotal='$grand_total', SisaPembayaran='$grand_total', LimitPengeluaran='$limit_pengeluaran', LimitPengeluaranPersen='$limit_peng_persen', LimitPengeluaranGaji='$limit_tenaga', LimitPengeluaranMaterial='$limit_material', LimitPengeluaranOverHead='$limit_overhead', Status='$status', ProjectManager='$project_manager', SiteManager='$site_manager', Supervisor='$supervisor', SiteAdmin='$site_admin', SiteAdmin2='$site_admin2', LockCoordinate='$locked', DateStartProject='$tanggalmulai', DateEndProject='$tanggalselesai', CreatedBy='".$_SESSION["uid"]."'");
    if($query){
        $id = $db->get_var("SELECT LAST_INSERT_ID()");
        $proyek->calcGrandTotalProyek($id);
        echo "1";
    } else {
        echo "0";
        // echo "INSERT INTO tb_proyek SET NoKontrak='$no_kontrak', KodeProyek='$kode_proyek', IDDepartement='$kategori', IDDepartementPemilik='$kategori2', Tahun='$tahun', NamaProyek='$nama_proyek', IDClient='$client', Nominal='$nominal', PPNPersen='$ppn_persen', PPN='$ppn', Total2='$grand_total', GrandTotal='$grand_total', SisaPembayaran='$grand_total', LimitPengeluaran='$limit_pengeluaran', LimitPengeluaranPersen='$limit_peng_persen', LimitPengeluaranGaji='$limit_tenaga', LimitPengeluaranMaterial='$limit_material', LimitPengeluaranOverHead='$limit_overhead', Status='$status', ProjectManager='$project_manager', SiteManager='$site_manager', Supervisor='$supervisor', LockCoordinate='$locked', DateStartProject='$tanggalmulai', DateEndProject='$tanggalselesai', CreatedBy='".$_SESSION["uid"]."'";
    }
}
