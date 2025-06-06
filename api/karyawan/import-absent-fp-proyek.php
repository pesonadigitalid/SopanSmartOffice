<?php
include_once "../config/connection.php";
include_once "../library/class.excelreader.php";

$proyek = $_POST['proyek'];
$ret = array();
if ($_FILES['file']) {

  $import = 0;
  $f = fopen($_FILES['file']['tmp_name'], "r");
  while (!feof($f)) {
    $data = preg_split('/\s+/', fgets($f));
    $datetime = $data[1] . " " . $data[2];
    $userID = $data[0];
    if ($data[6] == "I" || $data[6] == "O") {
      $Jenis = $data[6];
    } else if ($data[7] == "I" || $data[7] == "O") {
      $Jenis = $data[7];
    } else if ($data[8] == "I" || $data[8] == "O") {
      $Jenis = $data[8];
    }

    if ($Jenis == "I") $Jenis = "1";
    else $Jenis = "2";

    $cek = $db->get_row("SELECT * FROM tb_karyawan WHERE AbsentID='$userID' AND IDDepartement!='4' AND IDProyek='$proyek'");
    // $cek = $db->get_row("SELECT * FROM tb_karyawan WHERE AbsentID='$userID' AND IDDepartement!='4' AND StatusKaryawan='Harian'");
    if ($cek) {
      $q = $db->get_row("SELECT * FROM tb_proyek_absent WHERE Datetime='" . $datetime . "' AND IDKaryawan='" . $cek->IDKaryawan . "' AND Jenis='$Jenis'");
      if (!$q) {
        $db->query("INSERT INTO tb_proyek_absent SET Datetime='" . $datetime . "', IDProyek='$proyek', IDKaryawan='" . $cek->IDKaryawan . "', Jenis='$Jenis', CreatedBy='" . $_SESSION["uid"] . "'");
        $import++;
      }
    }
  }

  fclose($f);
  echo json_encode(array("res" => "1", "msg" => $import));
} else {
  echo json_encode(array("res" => "0"));
}
