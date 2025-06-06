<?php
include_once "../config/connection.php";

$act = antiSQLInjection($_GET['act']);
switch ($act) {        
    case "Detail":
        $detail = array();
        $id = "7";

        $query = $db->get_row("SELECT * FROM tb_system_config WHERE id_system_config='$id' ORDER BY id_system_config ASC");
        if($query){
            $detail = array("label"=>$query->label,"jml_cuti_tahunan"=>$query->value);
        }
        echo json_encode(array("detail"=>$detail));
    break;
    
    case "EditRecord":        
        $id = antiSQLInjection($_POST['id']);
        $value = antiSQLInjection($_POST['jml_cuti_tahunan']);
        
        $query = $db->query("UPDATE tb_system_config SET value='$value' WHERE id_system_config='$id'");
        if($query){
            echo "1";
        } else {
            echo "0";
        }
    break;
        
    default:
        echo "";
}