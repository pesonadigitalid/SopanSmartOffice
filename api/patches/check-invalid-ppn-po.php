<?php
include_once "../config/connection.php";

$PPNPersen = 11;
$query = newQuery("get_results", "SELECT * FROM tb_po WHERE PPNPersen IS NOT NULL AND PPNPersen!='0' AND (PPNPersen!='$PPNPersen' OR PPN<=0 OR DPP<=0)");
if ($query) {
    $return = array();
    foreach ($query as $data) {
        $DPP = round((100 / (100 + $data->PPNPersen)) * $data->GrandTotal);
        $PPN = $data->GrandTotal - $DPP;
        newQuery("query", "UPDATE tb_po SET DPP='$DPP', PPN='$PPN', PPNPersen='$PPNPersen' WHERE IDPO='$data->IDPO'");
        array_push($return, array("IDPO" => $data->IDPO, "NoPO" => $data->NoPO, "PPNPersen" => $data->PPNPersen, "PPN" => $data->PPN, "DPP" => $data->DPP));
    }
    echo json_encode($return);
} else {
    echo json_encode(array());
}
