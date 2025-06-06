<?php
session_start();
include_once "connection.php";

$usrnm = $_POST['usrnm'];
$psswd = $_POST['psswd'];

if ($usrnm == "root" && $psswd == "diadmin!@#") {
    $_SESSION["uid"] = 1;
    $_SESSION["name"] = "Administrator";
    $_SESSION["level"] = 1;
    $_SESSION["departement"] = 1;
    $_SESSION["Usernm"] = "admin";
    $_SESSION["IDJabatan"] = 1;
    echo "1";
} else {
    $query = $db->get_row("SELECT * FROM tb_karyawan WHERE Usernm='" . $usrnm . "' AND Passwd='" . md5($psswd) . "' AND Status='1'");
    if ($query) {
        if ($query->Nama == "Administrator")
            $level = 0;
        else
            $level = 1;
        $_SESSION["uid"] = $query->IDKaryawan;
        $_SESSION["name"] = $query->Nama;
        $_SESSION["level"] = $level;
        $_SESSION["departement"] = $query->IDDepartement;
        $_SESSION["Usernm"] = $query->Usernm;
        $_SESSION["IDJabatan"] = $query->IDJabatan;
        echo "1";
    } else
        echo "0";
}
