<?php
include_once "../config/connection.php";
include_once "../library/class.excelreader.php";

$cat = antiSQLInjection($_POST['cat']);
$val = antiSQLInjection($_POST['val']);
$tanggal = antiSQLInjection($_POST['tanggal']);
$exp = explode("/", $tanggal);
$tanggal = $exp[2] . "-" . $exp[1] . "-" . $exp[0];
$karyawan = antiSQLInjection($_POST['karyawan']);
$proyek = antiSQLInjection($_POST['proyek']);
$id_proyek = $_POST['id_proyek'];
if ($id_proyek == "") $id_proyek = 0;

$DateTimeAbsent = $tanggal . " " . $val;

if ($cat == 'datang' || $cat == 'pulang') {
  if ($cat == 'datang') {
    if ($val == "" || $val == "-") {
      $db->query("DELETE FROM tb_absent WHERE IDKaryawan='$karyawan' AND Tipe='I' AND DATE_FORMAT(DateTimeAbsent,'%Y-%m-%d')='$tanggal'");
    } else {
      $cek = $db->get_row("SELECT * FROM tb_absent WHERE IDKaryawan='$karyawan' AND Tipe='I' AND DATE_FORMAT(DateTimeAbsent,'%Y-%m-%d')='$tanggal' ORDER BY DateTimeAbsent ASC");
      if ($cek) {
        $db->query("UPDATE tb_absent SET DateTimeAbsent='$DateTimeAbsent' WHERE IDAbsent='" . $cek->IDAbsent . "'");
      } else {
        $db->query("INSERT INTO tb_absent SET DateTimeAbsent='$DateTimeAbsent', IDKaryawan='$karyawan', Tipe='I', DateCreated=NOW(), CreatedBy='" . $_SESSION["uid"] . "'");
      }
    }
    echo "1";
  } else {
    if ($val == "" || $val == "-") {
      $db->query("DELETE FROM tb_absent WHERE IDKaryawan='$karyawan' AND Tipe='O' AND DATE_FORMAT(DateTimeAbsent,'%Y-%m-%d')='$tanggal'");
    } else {
      $cek = $db->get_row("SELECT * FROM tb_absent WHERE IDKaryawan='$karyawan' AND Tipe='O' AND DATE_FORMAT(DateTimeAbsent,'%Y-%m-%d')='$tanggal' ORDER BY DateTimeAbsent ASC");
      if ($cek) {
        $db->query("UPDATE tb_absent SET DateTimeAbsent='$DateTimeAbsent' WHERE IDAbsent='" . $cek->IDAbsent . "'");
      } else {
        $db->query("INSERT INTO tb_absent SET DateTimeAbsent='$DateTimeAbsent', IDKaryawan='$karyawan', Tipe='O', DateCreated=NOW(), CreatedBy='" . $_SESSION["uid"] . "'");
      }
    }
    echo "1";
  }
} else if ($cat == 'datanglembur' || $cat == 'pulanglembur') {
  if ($cat == 'datanglembur') {
    if ($val == "" || $val == "-") {
      $db->query("DELETE FROM tb_absent_lembur WHERE IDKaryawan='$karyawan' AND Tipe='I' AND DATE_FORMAT(DateTimeAbsent,'%Y-%m-%d')='$tanggal'");
    } else {
      $cek = $db->get_row("SELECT * FROM tb_absent_lembur WHERE IDKaryawan='$karyawan' AND Tipe='I' AND DATE_FORMAT(DateTimeAbsent,'%Y-%m-%d')='$tanggal' ORDER BY DateTimeAbsent ASC");
      if ($cek) {
        $db->query("UPDATE tb_absent_lembur SET DateTimeAbsent='$DateTimeAbsent' WHERE IDAbsent='" . $cek->IDAbsent . "'");
      } else {
        $db->query("INSERT INTO tb_absent_lembur SET DateTimeAbsent='$DateTimeAbsent', IDKaryawan='$karyawan', Tipe='I', DateCreated=NOW(), CreatedBy='" . $_SESSION["uid"] . "'");
      }
    }
    echo "1";
  } else {
    if ($val == "" || $val == "-") {
      $db->query("DELETE FROM tb_absent_lembur WHERE IDKaryawan='$karyawan' AND Tipe='O' AND DATE_FORMAT(DateTimeAbsent,'%Y-%m-%d')='$tanggal'");
    } else {
      $cek = $db->get_row("SELECT * FROM tb_absent_lembur WHERE IDKaryawan='$karyawan' AND Tipe='O' AND DATE_FORMAT(DateTimeAbsent,'%Y-%m-%d')='$tanggal' ORDER BY DateTimeAbsent ASC");
      if ($cek) {
        $db->query("UPDATE tb_absent_lembur SET DateTimeAbsent='$DateTimeAbsent' WHERE IDAbsent='" . $cek->IDAbsent . "'");
      } else {
        $db->query("INSERT INTO tb_absent_lembur SET DateTimeAbsent='$DateTimeAbsent', IDKaryawan='$karyawan', Tipe='O', DateCreated=NOW(), CreatedBy='" . $_SESSION["uid"] . "'");
      }
    }
    echo "1";
  }
} else if ($cat == 'proyekdatang' || $cat == 'proyekpulang') {
  if ($cat == 'proyekdatang') {
    if ($val == "" || $val == "-") {
      $db->query("DELETE FROM tb_proyek_absent WHERE DATE_FORMAT(`Datetime`,'%Y-%m-%d')='$tanggal' AND Jenis='1' AND IDKaryawan='$karyawan'");
    } else {
      $cek = $db->get_row("SELECT * FROM tb_proyek_absent WHERE DATE_FORMAT(`Datetime`,'%Y-%m-%d')='$tanggal' AND Jenis='1' AND IDKaryawan='$karyawan' ORDER BY `Datetime` ASC");
      if ($cek) {
        $db->query("UPDATE tb_proyek_absent SET `Datetime`='$DateTimeAbsent' WHERE IDAbsent='" . $cek->IDAbsent . "'");
      } else {
        $db->query("INSERT INTO tb_proyek_absent SET `Datetime`='$DateTimeAbsent', IDKaryawan='$karyawan', Jenis='1', IDProyek='$id_proyek'");
      }
    }
    echo "1";
  } else {
    if ($val == "" || $val == "-") {
      $db->query("DELETE FROM tb_proyek_absent WHERE DATE_FORMAT(`Datetime`,'%Y-%m-%d')='$tanggal' AND Jenis='2' AND IDKaryawan='$karyawan'");
    } else {
      $cek = $db->get_row("SELECT * FROM tb_proyek_absent WHERE DATE_FORMAT(`Datetime`,'%Y-%m-%d')='$tanggal' AND Jenis='2' AND IDKaryawan='$karyawan' ORDER BY `Datetime` ASC");
      if ($cek) {
        $db->query("UPDATE tb_proyek_absent SET `Datetime`='$DateTimeAbsent' WHERE IDAbsent='" . $cek->IDAbsent . "'");
      } else {
        $db->query("INSERT INTO tb_proyek_absent SET `Datetime`='$DateTimeAbsent', IDKaryawan='$karyawan', Jenis='2', IDProyek='$id_proyek'");
      }
    }
    echo "1";
  }
} else if ($cat == 'keterangan' && $proyek != "1") {
  $val = trim(str_replace("[Hitung Gapok]", "", $val));
  $cek = $db->get_row("SELECT * FROM tb_absent WHERE DATE_FORMAT(`DateTimeAbsent`,'%Y-%m-%d')='$tanggal' AND IDKaryawan='$karyawan' ORDER BY `DateTimeAbsent` ASC");
  if ($cek) {
    $db->query("UPDATE tb_absent SET Keterangan='$val' WHERE IDAbsent='" . $cek->IDAbsent . "'");
  } else {
    $db->query("INSERT INTO tb_absent SET DateTimeAbsent='$tanggal', IDKaryawan='$karyawan', Tipe='I', Keterangan='$val', DateCreated=NOW(), CreatedBy='" . $_SESSION["uid"] . "'");
  }
  echo "1";
} else if ($cat == 'keterangan' && $proyek == "1") {
  $val = trim(str_replace("[Hitung Gapok]", "", $val));
  $cek = $db->get_row("SELECT * FROM tb_proyek_absent WHERE DATE_FORMAT(`Datetime`,'%Y-%m-%d')='$tanggal' AND IDKaryawan='$karyawan' ORDER BY `Datetime` ASC");
  if ($cek) {
    $db->query("UPDATE tb_proyek_absent SET Keterangan='$val' WHERE IDAbsent='" . $cek->IDAbsent . "'");
  } else {
    $db->query("INSERT INTO tb_proyek_absent SET Datetime='$tanggal', IDKaryawan='$karyawan', Jenis='1', Keterangan='$val', IDProyek='$id_proyek'");
  }
  echo "1";
} else if ($cat == 'id_proyek') {
  $cek = $db->get_row("SELECT * FROM tb_proyek_absent WHERE DATE_FORMAT(`Datetime`,'%Y-%m-%d')='$tanggal' AND IDKaryawan='$karyawan' ORDER BY `Datetime` ASC");
  if ($cek) {
    $db->query("UPDATE tb_proyek_absent SET IDProyek='$val' WHERE IDAbsent='" . $cek->IDAbsent . "'");
  } else {
    $db->query("INSERT INTO tb_proyek_absent SET Datetime='$tanggal', IDKaryawan='$karyawan', Jenis='1', IDProyek='$val'");
  }
  echo "1";
} else if ($cat == 'hitunggapok') {
  $cek = $db->get_row("SELECT * FROM tb_proyek_absent WHERE DATE_FORMAT(`Datetime`,'%Y-%m-%d')='$tanggal' AND IDKaryawan='$karyawan' ORDER BY `Datetime` ASC");
  if ($cek) {
    $db->query("UPDATE tb_proyek_absent SET HitungGapok='$val' WHERE IDAbsent='" . $cek->IDAbsent . "'");
  } else {
    $db->query("INSERT INTO tb_proyek_absent SET Datetime='$tanggal', IDKaryawan='$karyawan', Jenis='1', HitungGapok='$val'");
  }
  echo "1";
}
