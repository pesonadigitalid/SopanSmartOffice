<?php
include_once "../config/connection.php";
$id = antiSQLInjection($_GET['id']);
$query = $db->get_row("SELECT * FROM tb_penjualan_file_category WHERE IDPenjualanFileCategory='$id' ORDER BY IDPenjualanFileCategory ASC");
if ($query) {
    echo json_encode($query);
} else {
    echo json_encode(array());
}
