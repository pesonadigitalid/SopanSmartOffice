<?php
include_once "../config/connection.php";
$id = antiSQLInjection($_GET['id']);
$act = antiSQLInjection($_GET['act']);

if($act=="LockLocation"){
    $lat = antiSQLInjection($_POST['lat']);
    $long = antiSQLInjection($_POST['long']);
    $from = antiSQLInjection($_POST['from']);
    
    $cek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='$id' AND (ProjectManager='$from' OR SiteManager='$from' OR Supervisor='$from') AND LockCoordinate='0'");
    if($cek){
        $db->query("UPDATE tb_proyek SET Longitute='$long', Latitute='$lat', AllowCoordinate='0', LockCoordinate='1' WHERE IDProyek='$id'");
        echo "1";
    } else {
        echo "0";
    }
    
} else if($act=="Absent"){
    $lat = antiSQLInjection($_POST['lat']);
    $long = antiSQLInjection($_POST['long']);
    $from = antiSQLInjection($_POST['from']);
    $jenis = antiSQLInjection($_POST['jenis']);

    if($jenis=="masuk"){
        $cond = ", Jenis='1'";
    } else {
        $cond = ", Jenis='2'";
    }
    
    $cek = $db->get_row("SELECT *, IFNULL(( 3959 * ACOS( COS( RADIANS('$lat') ) * COS( RADIANS( Latitute ) ) * COS( RADIANS( Longitute ) - RADIANS('$long') ) + SIN( RADIANS('$lat') ) * SIN( RADIANS( Latitute ) ) ) ), 0) AS distance FROM tb_proyek WHERE IDProyek='$id' GROUP BY IDProyek HAVING distance < '0.1' ORDER BY distance ASC");
    if($cek){
        $db->query("INSERT INTO tb_proyek_absent SET IDProyek='$id', IDKaryawan='$from' $cond");
        echo "1";
    } else {
        echo "0";
    }
} else {
    echo "0";
}