<?php
include_once "../config/connection.php";

$auth = $db->get_var("SELECT value FROM tb_system_config WHERE label='AUTHENTICATE_ACCESS'");
if ($auth == "true") {
    $db->query("UPDATE tb_system_config SET value='false' WHERE label='AUTHENTICATE_ACCESS'");
    echo "1";
} else {
    echo "0";
}
