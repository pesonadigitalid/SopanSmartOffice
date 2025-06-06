<?php
include_once "../config/connection.php";
include_once "../library/class.excelreader.php";

$dept = $_POST['dept'];
$ret = array();
if ($_FILES['file']) {

  $import = 0;
  if ($dept == "LD") {
    $f = fopen($_FILES['file']['tmp_name'], "r");
    while (!feof($f)) {
      $data = preg_split('/\s+/', fgets($f));
      $datetime = $data[1] . " " . $data[2];
      $userID = $data[0];
      if ($data[6] == "I" || $data[6] == "O") {
        $tipe = $data[6];
      } else if ($data[7] == "I" || $data[7] == "O") {
        $tipe = $data[7];
      } else if ($data[8] == "I" || $data[8] == "O") {
        $tipe = $data[8];
      }

      $cek = $db->get_row("SELECT * FROM tb_karyawan WHERE AbsentID='$userID' AND IDDepartement!='4' AND StatusKaryawan<>'Harian'");
      if ($cek) {
        $q = $db->get_row("SELECT * FROM tb_absent WHERE DateTimeAbsent='" . $datetime . "' AND IDKaryawan='" . $cek->IDKaryawan . "' AND Tipe='$tipe'");
        if (!$q) {
          $db->query("INSERT INTO tb_absent SET DateTimeAbsent='" . $datetime . "', IDKaryawan='" . $cek->IDKaryawan . "', Tipe='$tipe', CreatedBy='" . $_SESSION["uid"] . "'");
          $import++;
        }
      }
    }
  } else if ($dept == "MMS") {
    // $fn = fopen($_FILES['file']['tmp_name'],"r");
    // while($row = fgets($fn)) {
    //   $col = explode("   ",$row);

    //   if(trim($col[9]) == "Daytime" || trim($col[9]) == "Sabtu"){
    //     $userID = trim($col[3]);
    //     $date = trim($col[8]);
    //     $jam_masuk = trim($col[10]);
    //     $jam_keluar = trim($col[12]);
    //   } else {
    //     $userID = trim($col[3]);
    //     $date = trim($col[9]);
    //     $jam_masuk = trim($col[11]);
    //     $jam_keluar = trim($col[13]);
    //   }

    //   $exp = explode("/", $date);
    //   $enDate = $exp[2]."-".$exp[1]."-".$exp[0];
    //   $dateTimeMasuk = $enDate." ".$jam_masuk;
    //   $dateTimeKeluar = $enDate." ".$jam_keluar;

    //   $cek = $db->get_row("SELECT * FROM tb_karyawan WHERE AbsentID='$userID'");
    //   if($cek){
    //     $q = $db->get_row("SELECT * FROM tb_absent WHERE DateTimeAbsent='".$dateTimeMasuk."' AND IDKaryawan='".$cek->IDKaryawan."' AND Tipe='I'");
    //     if(!$q){
    //         $db->query("INSERT INTO tb_absent SET DateTimeAbsent='".$dateTimeMasuk."', IDKaryawan='".$cek->IDKaryawan."', Tipe='I', CreatedBy='".$_SESSION["uid"]."'");
    //         $import++;
    //     }

    //     $q = $db->get_row("SELECT * FROM tb_absent WHERE DateTimeAbsent='".$dateTimeKeluar."' AND IDKaryawan='".$cek->IDKaryawan."' AND Tipe='O'");
    //     if(!$q){
    //         $db->query("INSERT INTO tb_absent SET DateTimeAbsent='".$dateTimeKeluar."', IDKaryawan='".$cek->IDKaryawan."', Tipe='O', CreatedBy='".$_SESSION["uid"]."'");
    //         $import++;
    //     }
    //   }
    // }
    $f = fopen($_FILES['file']['tmp_name'], "r");
    while (!feof($f)) {
      $data = preg_split('/\s+/', fgets($f));
      $datetime = $data[1] . " " . $data[2];
      $userID = $data[0];
      if ($data[6] == "I" || $data[6] == "O") {
        $tipe = $data[6];
      } else if ($data[7] == "I" || $data[7] == "O") {
        $tipe = $data[7];
      } else if ($data[8] == "I" || $data[8] == "O") {
        $tipe = $data[8];
      }

      $cek = $db->get_row("SELECT * FROM tb_karyawan WHERE AbsentID='$userID'");
      if ($cek) {
        $q = $db->get_row("SELECT * FROM tb_absent WHERE DateTimeAbsent='" . $datetime . "' AND IDKaryawan='" . $cek->IDKaryawan . "' AND Tipe='$tipe'");
        if (!$q) {
          $db->query("INSERT INTO tb_absent SET DateTimeAbsent='" . $datetime . "', IDKaryawan='" . $cek->IDKaryawan . "', Tipe='$tipe', CreatedBy='" . $_SESSION["uid"] . "'");
          $import++;
        }
      }
    }
  } else if ($dept == "LD2") {
    $fn = fopen($_FILES['file']['tmp_name'], "r");
    while ($row = fgets($fn)) {
      $col = explode("   ", $row);
      $userID = "";
      $dat = "";
      if (intval($col[1]) != "") {
        $userID = intval($col[1]);
        if (trim($col[2]) != "") {
          $dat = trim($col[2]);
        } else if (trim($col[3]) != "") {
          $dat = trim($col[3]);
        } else if (trim($col[4]) != "") {
          $dat = trim($col[4]);
        } else if (trim($col[5]) != "") {
          $dat = trim($col[5]);
        } else if (trim($col[6]) != "") {
          $dat = trim($col[6]);
        } else if (trim($col[7]) != "") {
          $dat = trim($col[7]);
        } else if (trim($col[8]) != "") {
          $dat = trim($col[8]);
        }
      } else if (intval($col[2]) != "") {
        $userID = intval($col[2]);
        if (trim($col[3]) != "") {
          $dat = trim($col[3]);
        } else if (trim($col[4]) != "") {
          $dat = trim($col[4]);
        } else if (trim($col[5]) != "") {
          $dat = trim($col[5]);
        } else if (trim($col[6]) != "") {
          $dat = trim($col[6]);
        } else if (trim($col[7]) != "") {
          $dat = trim($col[7]);
        } else if (trim($col[8]) != "") {
          $dat = trim($col[8]);
        }
      }

      //echo $userID."/".$dat."<br/>";

      if ($userID != "") {
        $exp = explode(" ", $dat);
        if (trim($exp[1]) == "") {
          $tanggal = trim($exp[2]);
          $jam = trim($exp[3]);
          if (trim($exp[4]) != "")
            $tipe = trim($exp[4]);
          else
            $tipe = trim($exp[5]);
        } else {
          $tanggal = trim($exp[1]);
          $jam = trim($exp[2]);
          if (trim($exp[3]) != "")
            $tipe = trim($exp[3]);
          else
            $tipe = trim($exp[4]);
        }

        if ($userID != "" && $tanggal != "" && $jam != "" && $tipe != "") {
          $expT = explode("/", $tanggal);
          $datetime = $expT[2] . "-" . $expT[1] . "-" . $expT[0] . " " . $jam;

          if ($tipe == "C/Masuk") $tipe = "I";
          else $tipe = "O";

          $cek = $db->get_row("SELECT * FROM tb_karyawan WHERE AbsentID='$userID'");
          if ($cek) {
            $q = $db->get_row("SELECT * FROM tb_absent WHERE DateTimeAbsent='" . $datetime . "' AND IDKaryawan='" . $cek->IDKaryawan . "' AND Tipe='$tipe'");
            if (!$q) {
              $db->query("INSERT INTO tb_absent SET DateTimeAbsent='" . $datetime . "', IDKaryawan='" . $cek->IDKaryawan . "', Tipe='$tipe', CreatedBy='" . $_SESSION["uid"] . "'");
              $import++;
            }
          }
        }
      }
    }
  }

  fclose($f);
  echo json_encode(array("res" => "1", "msg" => $import));
} else {
  echo json_encode(array("res" => "0"));
}
