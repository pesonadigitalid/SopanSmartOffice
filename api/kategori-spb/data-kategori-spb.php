<?php
include_once "../config/connection.php";

$query = $db->get_results("SELECT * FROM tb_penjualan_file_category ORDER BY IDPenjualanFileCategory ASC");
if ($query) {
    $return = array();
    $i = 0;
    foreach ($query as $data) {
        $i++;
        $data->No = $i;
        $data->Status = ($data->Status == "1") ? true : false;

        array_push($return, $data);
    }
    echo json_encode($return);
} else {
    echo json_encode(array());
}
