<?php
include_once "../config/connection.php";
if ($_GET['param'] == "getmaterial") $cond = "AND IsParent='1'";
$query = $db->get_results("SELECT * FROM tb_jenis_material WHERE IDMaterial IS NOT NULL $cond ORDER BY Nama ASC");
if ($query) {
    $return = array();
    $i = 0;
    foreach ($query as $data) {
        $i++;
        if ($data->Parent == "0") {
            $parent = "ROOT";
        } else {
            $parent = $db->get_var("SELECT Nama FROM tb_jenis_material WHERE IDMaterial='" . $data->Parent . "'");
        }
        array_push($return, array("IDMaterial" => $data->IDMaterial, "No" => $i, "Parent" => $parent, "Nama" => $data->Nama));
    }
    echo json_encode($return);
} else {
    echo json_encode(array());
}
