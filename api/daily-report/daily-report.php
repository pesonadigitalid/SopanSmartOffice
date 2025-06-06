<?php
include_once "../config/connection.php";

$act = antiSQLInjection($_GET['act']);
switch ($act) {
    case "DailyReport":
        $report = array();
        $SuratJalanArray = array();  
        

        $datestart = antiSQLInjection($_GET['datestart']);
        $no_surat_jalan = antiSQLInjection($_GET['no_surat_jalan']);

        if ($datestart != "") {
            $expstart = explode("/", $datestart);
            $tanggal = $expstart[2] . "-" . $expstart[1] . "-" . $expstart[0];
            $tanggalID = $datestart;
        } else {
            $tanggal = date("Y-m-d");
            $tanggalID = date("d/m/Y");
        }
        
        $cond = "AND DATE_FORMAT(DateCreated,'%Y-%m-%d')='".$tanggal."'";
        if($no_surat_jalan!="0") $cond = " AND NoSuratJalan='$no_surat_jalan'";

        $queryP = $db->get_results("SELECT DISTINCT(Createdby) FROM tb_daily_report WHERE IDReport>0 $cond ORDER BY IDReport ASC");
        if($queryP){
            foreach($queryP as $dataP){
                $reportTime = array();
                $count = 0;
                $queryR = $db->get_results("SELECT *, TIMEDIFF(JIn,JOut) AS Diff FROM tb_in_out WHERE IDInOut>0 $cond AND JIn!='00:00:00' AND JOut!='00:00:00' AND IDKaryawan='".$dataP->Createdby."' ORDER BY IDInOut ASC");
                if($queryR){
                    $condElse = "";
                    foreach($queryR as $dataR){
                        $condElse .= " AND (DATE_FORMAT(DateCreated,'%H:%m:%s')>='".$dataR->JIn."' AND DATE_FORMAT(DateCreated,'%H:%m:%s')<'".$dataR->JOut."')  ";
                        $reportDetail = array();
                        $queryT = $db->get_results("SELECT *,DATE_FORMAT(DateCreated,'%H:%m') AS TimeReport FROM tb_daily_report WHERE IDReport>0 $cond AND Createdby='".$dataP->Createdby."' AND (DATE_FORMAT(DateCreated,'%H:%m:%s')<'".$dataR->JIn."' AND DATE_FORMAT(DateCreated,'%H:%m:%s')>='".$dataR->JOut."') ORDER BY IDReport ASC");
                        if($queryT){
                            foreach($queryT as $dataT){
                                array_push($reportDetail, array("IDReport"=>$dataT->IDReport,"Subject"=>$dataT->Subject,"Report"=>$dataT->Report,"Image1"=>$dataT->Image1,"Image2"=>$dataT->Image2,"Image3"=>$dataT->Image3,"TimeReport"=>$dataT->TimeReport,"Lat"=>$dataR->Lat,"Lng"=>$dataR->Lng));
                            }
                        }

                        $count++;
                        array_push($reportTime, array("Out"=>substr($dataR->JOut,0,-3),"In"=>substr($dataR->JIn,0,-3),"Duration"=>substr($dataR->Diff,0,-3),"ReportDetail"=>
                                $reportDetail));
                    }

                    $reportDetail = array();
                    $queryR = $db->get_results("SELECT *,DATE_FORMAT(DateCreated,'%H:%m') AS TimeReport FROM tb_daily_report WHERE IDReport>0 $cond AND Createdby='".$dataP->Createdby."' $condElse ORDER BY IDReport ASC");
                    if($queryR){
                        foreach($queryR as $dataR){
                            array_push($reportDetail, array("IDReport"=>$dataR->IDReport,"Subject"=>$dataR->Subject,"Report"=>$dataR->Report,"Image1"=>$dataR->Image1,"Image2"=>$dataR->Image2,"Image3"=>$dataR->Image3,"TimeReport"=>$dataR->TimeReport,"Lat"=>$dataR->Lat,"Lng"=>$dataR->Lng));
                        }
                        array_push($reportTime, array("Out"=>"-","In"=>"-","Duration"=>"-","ReportDetail"=>
                        $reportDetail));
                    }
                } else {
                    $reportDetail = array();
                    $queryR = $db->get_results("SELECT *,DATE_FORMAT(DateCreated,'%H:%m') AS TimeReport FROM tb_daily_report WHERE IDReport>0 $cond AND Createdby='".$dataP->Createdby."' ORDER BY IDReport ASC");
                    if($queryR){
                        foreach($queryR as $dataR){
                            $count++;
                            array_push($reportDetail, array("IDReport"=>$dataR->IDReport,"Subject"=>$dataR->Subject,"Report"=>$dataR->Report,"Image1"=>$dataR->Image1,"Image2"=>$dataR->Image2,"Image3"=>$dataR->Image3,"TimeReport"=>$dataR->TimeReport,"Lat"=>$dataR->Lat,"Lng"=>$dataR->Lng));
                        }
                        array_push($reportTime, array("Out"=>"-","In"=>"-","Duration"=>"-","ReportDetail"=>
                        $reportDetail));
                    }
                }




/*
                //REPORT
                $reportDetail = array();
                $queryR = $db->get_results("SELECT *,DATE_FORMAT(DateCreated,'%H:%m') AS TimeReport FROM tb_daily_report WHERE IDReport>0 $cond AND Createdby='".$dataP->Createdby."' ORDER BY IDReport ASC");
                if($queryR){
                    foreach($queryR as $dataR){
                        array_push($reportDetail, array("IDReport"=>$dataR->IDReport,"Subject"=>$dataR->Subject,"Report"=>$dataR->Report,"Image1"=>$dataR->Image1,"Image2"=>$dataR->Image2,"Image3"=>$dataR->Image3,"TimeReport"=>$dataR->TimeReport));
                    }
                }

                //IN-OUT
                $reportTabStation = array();
                $count = 0;
                $queryR = $db->get_results("SELECT *, TIMEDIFF(JIn,JOut) AS Diff FROM tb_in_out WHERE IDInOut>0 $cond AND IDKaryawan='".$dataP->Createdby."' ORDER BY IDInOut ASC");
                if($queryR){
                    foreach($queryR as $dataR){
                        $count++;
                        array_push($reportTabStation, array("Out"=>substr($dataR->JOut,0,-3),"In"=>substr($dataR->JIn,0,-3),"Duration"=>substr($dataR->Diff,0,-3)));
                    }
                }*/

                $karyawan = $db->get_row("SELECT * FROM tb_karyawan WHERE IDKaryawan='".$dataP->Createdby."'");
                array_push($report, array("Nama"=>$karyawan->Nama,"Tanggal"=>$tanggalID,"ReportTabStation"=>$reportTime,"Count"=>$count));
            }
        }
              
        $query = $db->get_results("SELECT * FROM tb_penjualan_surat_jalan ORDER BY IDSuratJalan ASC");
        if($query){
            $i=0;
            foreach($query as $data){
                $i++;
                array_push($SuratJalanArray,array("IDSuratJalan"=>$data->IDSuratJalan,"NoSuratJalan"=>$data->NoSuratJalan,"No"=>$i));
            }
        }

        
        $return = array("report"=>$report,"SuratJalanArray"=>$SuratJalanArray);
        echo json_encode($return);
        break;

    case "ReportPerKaryawan":
        $report = array();
        $totalCount = 0;

        $bulan = antiSQLInjection($_GET['bulan']);
        $tahun = antiSQLInjection($_GET['tahun']);
        $karyawan = antiSQLInjection($_GET['karyawan']);

        for($i=1;$i<=cal_days_in_month(CAL_GREGORIAN, intval($bulan), intval($tahun));$i++){

            $reportTime = array();
            if($i<10)
                $tanggalD = "0".$i;
            else
                $tanggalD = $i;
            $tanggal = $tahun."-".$bulan."-".$tanggalD;
            $tanggalID = $tanggalD."/".$bulan."/".$tahun;

            $cond = "AND DATE_FORMAT(DateCreated,'%Y-%m-%d')='".$tanggal."'";
            $queryP = $db->get_results("SELECT DISTINCT(CreatedBy) FROM tb_daily_report WHERE IDReport>0 $cond AND CreatedBy='$karyawan' ORDER BY IDReport ASC");
            if($queryP){
                foreach($queryP as $dataP){
                    $count = 0;
                    $queryR = $db->get_results("SELECT *, TIMEDIFF(JIn,JOut) AS Diff FROM tb_in_out WHERE IDInOut>0 $cond AND JIn!='00:00:00' AND JOut!='00:00:00' AND IDKaryawan='$karyawan' ORDER BY IDInOut ASC");
                    if($queryR){
                        $condElse = "";
                        foreach($queryR as $dataR){
                            $condElse .= " AND (DATE_FORMAT(DateCreated,'%H:%m:%s')>='".$dataR->JIn."' AND DATE_FORMAT(DateCreated,'%H:%m:%s')<'".$dataR->JOut."')  ";
                            $reportDetail = array();
                            $queryT = $db->get_results("SELECT *,DATE_FORMAT(DateCreated,'%H:%m') AS TimeReport FROM tb_daily_report WHERE IDReport>0 $cond AND Createdby='".$dataP->CreatedBy."' AND (DATE_FORMAT(DateCreated,'%H:%m:%s')<'".$dataR->JIn."' AND DATE_FORMAT(DateCreated,'%H:%m:%s')>='".$dataR->JOut."') ORDER BY IDReport ASC");
                            if($queryT){
                                foreach($queryT as $dataT){
                                    array_push($reportDetail, array("IDReport"=>$dataT->IDReport,"Subject"=>$dataT->Subject,"Report"=>$dataT->Report,"Image1"=>$dataT->Image1,"Image2"=>$dataT->Image2,"Image3"=>$dataT->Image3,"TimeReport"=>$dataT->TimeReport,"Lat"=>$dataR->Lat,"Lng"=>$dataR->Lng));
                                }
                            }

                            $count++;
                            array_push($reportTime, array("Out"=>substr($dataR->JOut,0,-3),"In"=>substr($dataR->JIn,0,-3),"Duration"=>substr($dataR->Diff,0,-3),"ReportDetail"=>
                                    $reportDetail));
                        }

                        $reportDetail = array();
                        $queryR = $db->get_results("SELECT *,DATE_FORMAT(DateCreated,'%H:%m') AS TimeReport FROM tb_daily_report WHERE IDReport>0 $cond AND Createdby='".$dataP->CreatedBy."' $condElse ORDER BY IDReport ASC");
                        if($queryR){
                            foreach($queryR as $dataR){
                                array_push($reportDetail, array("IDReport"=>$dataR->IDReport,"Subject"=>$dataR->Subject,"Report"=>$dataR->Report,"Image1"=>$dataR->Image1,"Image2"=>$dataR->Image2,"Image3"=>$dataR->Image3,"TimeReport"=>$dataR->TimeReport,"Lat"=>$dataR->Lat,"Lng"=>$dataR->Lng));
                            }
                            array_push($reportTime, array("Out"=>"-","In"=>"-","Duration"=>"-","ReportDetail"=>$reportDetail));
                        }
                    } else {
                        $reportDetail = array();
                        $queryR = $db->get_results("SELECT *,DATE_FORMAT(DateCreated,'%H:%m') AS TimeReport FROM tb_daily_report WHERE IDReport>0 $cond AND Createdby='".$dataP->CreatedBy."' ORDER BY IDReport ASC");
                        if($queryR){
                            foreach($queryR as $dataR){
                                $count++;
                                array_push($reportDetail, array("IDReport"=>$dataR->IDReport,"Subject"=>$dataR->Subject,"Report"=>$dataR->Report,"Image1"=>$dataR->Image1,"Image2"=>$dataR->Image2,"Image3"=>$dataR->Image3,"TimeReport"=>$dataR->TimeReport,"Lat"=>$dataR->Lat,"Lng"=>$dataR->Lng));
                            }
                            array_push($reportTime, array("Out"=>"-","In"=>"-","Duration"=>"-","ReportDetail"=>$reportDetail));
                        }
                    }

/*
                    //REPORT
                    $reportDetail = array();
                    $queryR = $db->get_results("SELECT *,DATE_FORMAT(DateCreated,'%H:%m') AS TimeReport FROM tb_daily_report WHERE IDReport>0 $cond AND Createdby='".$dataP->Createdby."' ORDER BY IDReport ASC");
                    if($queryR){
                        foreach($queryR as $dataR){
                            array_push($reportDetail, array("IDReport"=>$dataR->IDReport,"Subject"=>$dataR->Subject,"Report"=>$dataR->Report,"Image1"=>$dataR->Image1,"Image2"=>$dataR->Image2,"Image3"=>$dataR->Image3,"TimeReport"=>$dataR->TimeReport));
                        }
                    }

                    //IN-OUT
                    $reportTabStation = array();
                    $count = 0;
                    $queryR = $db->get_results("SELECT *, TIMEDIFF(JIn,JOut) AS Diff FROM tb_in_out WHERE IDInOut>0 $cond AND IDKaryawan='".$dataP->Createdby."' ORDER BY IDInOut ASC");
                    if($queryR){
                        foreach($queryR as $dataR){
                            $count++;
                            $totalCount++;
                            array_push($reportTabStation, array("Out"=>substr($dataR->JOut,0,-3),"In"=>substr($dataR->JIn,0,-3),"Duration"=>substr($dataR->Diff,0,-3)));
                        }
                    }
*/
                    $dkaryawan = $db->get_row("SELECT * FROM tb_karyawan WHERE IDKaryawan='".$dataP->CreatedBy."'");
                    array_push($report, array("Nama"=>$dkaryawan->Nama,"Tanggal"=>$tanggalID,"ReportTabStation"=>$reportTime,"Count"=>$count));
                }
            }
        }
        $return = array("report"=>$report, "totalCount"=>$totalCount);
        echo json_encode($return);
        break;
    
    case "LoadAllRequirement":
        $SuratJalanArray = array();
        
        $query = $db->get_results("SELECT * FROM tb_penjualan_surat_jalan ORDER BY IDSuratJalan ASC");
        if($query){
            $i=0;
            foreach($query as $data){
                $i++;
                array_push($SuratJalanArray,array("IDSuratJalan"=>$data->IDSuratJalan,"NoSuratJalan"=>$data->NoSuratJalan,"No"=>$i));
            }
        }
        $return = array("SuratJalanArray"=>$SuratJalanArray);
        echo json_encode($return);
    break;

    default:
        echo "";
}