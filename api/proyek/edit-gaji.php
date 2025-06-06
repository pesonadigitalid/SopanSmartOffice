<?php
include_once "../config/connection.php";

$IDProyek = antiSQLInjection($_POST['IDProyek']);
$LemburPerJam = antiSQLInjection($_POST['LemburPerJam']);
$LemburPerJamTipe = str_replace(",", "", antiSQLInjection($_POST['LemburPerJamTipe']));

$query = $db->query("UPDATE tb_proyek SET LemburPerJam='$LemburPerJam', LemburPerJamTipe='$LemburPerJamTipe' WHERE IDProyek='$IDProyek'");
if ($query) {
    echo "1";
} else {
    echo "0";
}
