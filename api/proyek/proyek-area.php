<?php
include_once "../config/connection.php";
$act = antiSQLInjection($_GET['act']);
switch ($act) {
    case "LoadAllRequirement":
        $IDProyek = antiSQLInjection($_GET['IDProyek']);

        $dProyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='$IDProyek'");
        $cond = "";
        if ($dProyek->ProjectManager != "") $cond .= " OR IDKaryawan='$dProyek->ProjectManager'";
        if ($dProyek->SiteManager != "") $cond .= " OR IDKaryawan='$dProyek->SiteManager'";


        //SUPERVISOR
        $kSupervisor = array();
        $query = $db->get_results("SELECT * FROM tb_karyawan WHERE IDJabatan='7' $cond ORDER BY IDJabatan ASC, IDKaryawan ASC");
        if ($query) {
            $return = array();
            $i = 0;
            foreach ($query as $data) {
                $i++;
                if ($data->Status != "1") $status = "(Resign)";
                else $status = "";
                $jabatan = $db->get_var("SELECT Jabatan FROM tb_jabatan WHERE IDJabatan='" . $data->IDJabatan . "'");
                array_push($kSupervisor, array("No" => $i, "NIK" => $data->NIK, "Nama" => $data->Nama . " " . $status, "Status" => $data->StatusLainnya, "Jabatan" => $jabatan, "StatusK" => $status, "IDKaryawan" => $data->IDKaryawan));
            }
        }

        $payload = array("proyek" => $dProyek, "supervisor" => $kSupervisor);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "InsertNew":
        $IDProyek = antiSQLInjection($_POST['IDProyek']);
        $Nama = antiSQLInjection($_POST['Nama']);
        $Keterangan = antiSQLInjection($_POST['Keterangan']);
        $Supervisor = antiSQLInjection($_POST['Supervisor']);

        $db->query("INSERT INTO tb_proyek_area SET IDProyek='$IDProyek', Nama='$Nama', Keterangan='$Keterangan', Supervisor='$Supervisor', CreatedBy='" . $_SESSION["uid"] . "', ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW()");

        $payload = array("response" => 1);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "Update":
        $IDProyek = antiSQLInjection($_POST['IDProyek']);
        $IDArea = antiSQLInjection($_POST['IDArea']);
        $Nama = antiSQLInjection($_POST['Nama']);
        $Keterangan = antiSQLInjection($_POST['Keterangan']);
        $Supervisor = antiSQLInjection($_POST['Supervisor']);

        $db->query("UPDATE tb_proyek_area SET Nama='$Nama', Keterangan='$Keterangan', Supervisor='$Supervisor', CreatedBy='" . $_SESSION["uid"] . "', ModifiedBy='" . $_SESSION["uid"] . "', DateModified=NOW() WHERE IDProyek='$IDProyek' AND IDArea='$IDArea'");

        $payload = array("response" => 1);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "Detail":
        $IDProyek = antiSQLInjection($_GET['IDProyek']);
        $IDArea = antiSQLInjection($_GET['IDArea']);

        $dProyekArea = $db->get_row("SELECT * FROM tb_proyek_area WHERE IDProyek='$IDProyek' AND IDArea='$IDArea'");
        $proyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='$IDProyek'");
        $cond = "";
        if ($proyek->ProjectManager != "") $cond .= " OR IDKaryawan='$proyek->ProjectManager'";
        if ($proyek->SiteManager != "") $cond .= " OR IDKaryawan='$proyek->SiteManager'";


        //SUPERVISOR
        $kSupervisor = array();
        $query = $db->get_results("SELECT * FROM tb_karyawan WHERE IDJabatan='7' $cond ORDER BY IDJabatan ASC, IDKaryawan ASC");
        if ($query) {
            $return = array();
            $i = 0;
            foreach ($query as $data) {
                $i++;
                if ($data->Status != "1") $status = "(Resign)";
                else $status = "";
                $jabatan = $db->get_var("SELECT Jabatan FROM tb_jabatan WHERE IDJabatan='" . $data->IDJabatan . "'");
                array_push($kSupervisor, array("No" => $i, "NIK" => $data->NIK, "Nama" => $data->Nama . " " . $status, "Status" => $data->StatusLainnya, "Jabatan" => $jabatan, "StatusK" => $status, "IDKaryawan" => $data->IDKaryawan));
            }
        }

        $payload = array("data" => $dProyekArea, "proyek" => $proyek, "supervisor" => $kSupervisor);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "DisplayData":
        $IDProyek = antiSQLInjection($_GET['IDProyek']);

        $dProyekArea = $db->get_results("SELECT * FROM tb_proyek_area WHERE IDProyek='$IDProyek'");
        if ($dProyekArea) {
            foreach ($dProyekArea as $proyekArea) {
                $sup = $db->get_var("SELECT Nama FROM tb_karyawan WHERE IDKaryawan='$proyekArea->Supervisor'");
                if (!$sup) $sup = "";
                $proyekArea->SupervisorName = $sup;
            }
        }
        $proyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='$IDProyek'");
        $cond = "";
        if ($proyek->ProjectManager != "") $cond .= " OR IDKaryawan='$proyek->ProjectManager'";
        if ($proyek->SiteManager != "") $cond .= " OR IDKaryawan='$proyek->SiteManager'";


        //SUPERVISOR
        $kSupervisor = array();
        $query = $db->get_results("SELECT * FROM tb_karyawan WHERE IDJabatan='7' $cond ORDER BY IDJabatan ASC, IDKaryawan ASC");
        if ($query) {
            $return = array();
            $i = 0;
            foreach ($query as $data) {
                $i++;
                if ($data->Status != "1") $status = "(Resign)";
                else $status = "";
                $jabatan = $db->get_var("SELECT Jabatan FROM tb_jabatan WHERE IDJabatan='" . $data->IDJabatan . "'");
                array_push($kSupervisor, array("No" => $i, "NIK" => $data->NIK, "Nama" => $data->Nama . " " . $status, "Status" => $data->StatusLainnya, "Jabatan" => $jabatan, "StatusK" => $status, "IDKaryawan" => $data->IDKaryawan));
            }
        }

        $payload = array("data" => $dProyekArea, "proyek" => $proyek, "supervisor" => $kSupervisor);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;

    case "Delete":
        $IDProyek = antiSQLInjection($_POST['IDProyek']);
        $IDArea = antiSQLInjection($_POST['IDArea']);

        $db->query("DELETE FROM tb_proyek_area WHERE IDProyek='$IDProyek' AND IDArea='$IDArea'");
        $payload = array("response" => 1);
        $return = array("payload" => $payload);
        echo json_encode($return);
        break;
    default:
        echo "";
}
