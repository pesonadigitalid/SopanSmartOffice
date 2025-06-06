<?php
include_once "../config/connection.php";


// This map would show Germany:
$south = deg2rad(47.2);
$north = deg2rad(55.2);
$west = deg2rad(5.8);
$east = deg2rad(15.2);

// This also controls the aspect ratio of the projection
$width = 1600;
$height = 1000;

// Formula for mercator projection y coordinate:
function mercY($lat) { return log(tan($lat/2 + M_PI/4)); }

// Some constants to relate chosen area to screen coordinates
$ymin = mercY($south);
$ymax = mercY($north);
$xFactor = $width/($east - $west);
$yFactor = $height/($ymax - $ymin);

function mapProject($lat, $lon) { // both in radians, use deg2rad if neccessary
  global $xFactor, $yFactor, $west, $ymax;
  $x = $lon;
  $y = mercY($lat);
  $x = ($x - $west)*$xFactor;
  $y = ($ymax - $y)*$yFactor; // y points south
  return array($x, $y);
}


function convertX($lon){
    $x = ($lon + 180) * (1600 / 360);
    return $x;
}

function convertY($lat){
    $y = ((-1 * $lat) + 90) * (1000 / 180);
    return $y;
}

$temp = array();
$query = $db->get_results("SELECT * FROM tb_proyek WHERE Status='2' AND Longitute IS NOT NULL AND Latitute IS NOT NULL");
if($query){
    foreach($query as $data){
        $convert = mapProject($data->Latitute,$data->Longitute);
        array_push($temp, array("id"=>$data->KodeProyek,"title"=>$data->KodeProyek."/".$data->Tahun,"description"=>$data->KodeProyek."<br/>".$data->Tahun."<br/>".$data->NamaProyek,"action"=>"tooltip","pin"=>"pulse blue","x"=>"1","y"=>"1"));
    }
}
$return = array("mapwidth"=>"1600","mapheight"=>"1000","categories"=>array(),"levels"=>array(array("id"=>"countries","title"=>"Countries","map"=>"http://revox.io/json/maps/pages-map.svg","minimap"=>"../../themes/assets/img/maps/us-small.jpg","locations"=>$temp)),"minimap"=>"true","sidebar"=>"true","zoomlimit"=>"15");
echo json_encode($return);