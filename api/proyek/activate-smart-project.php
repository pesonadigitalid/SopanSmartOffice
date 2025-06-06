<?php
include_once "../config/connection.php";
$id = antiSQLInjection($_GET['id']);
$date = date("Y-m-d H:i:s");
$db->query("UPDATE tb_proyek SET LastSync='$date' WHERE IDProyek='$id'");
return $date;
