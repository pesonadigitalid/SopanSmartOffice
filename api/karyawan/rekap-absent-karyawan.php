<?php
include_once "../config/connection.php";
include_once "../library/class.excelreader.php";

if($_FILES['file']){
    $import = 0;
    $f = fopen($_FILES['file']['tmp_name'], "r");
    while(!feof($f)) { 
      $data = preg_split('/\s+/', fgets($f));
      $datetime = $data[1]." ".$data[2];
      $userID = $data[5];
      $tipe = $data[6];

      $cek = $db->get_row("SELECT * FROM tb_karyawan WHERE AbsentID='$userID'");
      if($cek){
        $q = $db->get_row("SELECT * FROM tb_absent WHERE DateTimeAbsent='".$datetime."' AND IDKaryawan='".$cek->IDKaryawan."' AND Tipe='$tipe'");
        if(!$q){
            $db->query("INSERT INTO tb_absent SET DateTimeAbsent='".$datetime."', IDKaryawan='".$cek->IDKaryawan."', Tipe='$tipe', CreatedBy='".$_SESSION["uid"]."'");
            $import++;
        }
      }
      
    }
    fclose($f);


    // $data = new Spreadsheet_Excel_Reader($_FILES['file']['tmp_name']);

    // $baris = $data->rowcount($sheet_index=0);
    // for ($i=1; $i<=$baris; $i++) {
    //     $dateTimeAbsent = $data->val($i, 2);
    //     $user = $data->val($i, 5);
    //     $tipe = $data->val($i, 6);
    //     $db->query("INSERT INTO tb_absent SET DateTimeAbsent='$dateTimeAbsent'");
    // }

    echo json_encode(array("res"=>"1","msg"=>$import));
} else {
    echo json_encode(array("res"=>"0"));
}