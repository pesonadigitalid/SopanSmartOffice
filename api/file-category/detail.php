<?php
include_once "../config/connection.php";
$id = antiSQLInjection($_GET['id']);
$query = $db->get_row("SELECT * FROM tb_pelanggan_file_category WHERE IDFileCategory='$id' ORDER BY IDFileCategory ASC");
if ($query) {
    echo json_encode($query);
} else {
    echo json_encode(array());
}
