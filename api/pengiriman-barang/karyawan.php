<?php
include_once "../config/connection.php";

$act = antiSQLInjection($_GET['act']);
switch ($act) {
    case "DataKaryawan":
        $jabatanArray = array();
        $karyawan = array();

        $idj = antiSQLInjection($_GET['idj']);
        $idk = antiSQLInjection($_GET['idk']);
        $jabatan = antiSQLInjection($_GET['jabatan']);
        $status_karyawan = antiSQLInjection($_GET['status_karyawan']);
        $mark = antiSQLInjection($_GET['mark']);
        $status_akun = antiSQLInjection($_GET['status_akun']);

        $cond = "WHERE IDKaryawan>0";

        if($_GET['status']=="1") $cond .= " AND Status='1'";
        if($idj!="") $cond .= " AND IDJabatan='$idj'";
        if($idk!="") $cond .= " AND IDKaryawan='$idk'";

        if($jabatan!="") $cond .= " AND IDJabatan='$jabatan'";
        if($status_karyawan!="") $cond .= " AND StatusKaryawan='$status_karyawan'";
        if($mark!="") $cond .= " AND StatusLainnya='$mark'";

        if($status_akun!="") $cond .= " AND Status='$status_akun'";

        $query = $db->get_results("SELECT * FROM tb_karyawan $cond ORDER BY IDKaryawan ASC");
        if($query){
            $i=0;
            foreach($query as $data){
                $i++;
                if($data->Status=="1") $status="Aktif"; else $status="Non Aktif";
                $jabatan = $db->get_var("SELECT Jabatan FROM tb_jabatan WHERE IDJabatan='".$data->IDJabatan."'");
                array_push($karyawan,array("No"=>$i,"NIK"=>$data->NIK,"Nama"=>$data->Nama,"Status"=>$data->StatusLainnya,"Jabatan"=>$jabatan,"StatusK"=>$status,"IDKaryawan"=>$data->IDKaryawan));
            }
        }

        $query = $db->get_results("SELECT * FROM tb_jabatan ORDER BY IDJabatan ASC");
        if($query){
            foreach($query as $data){
                array_push($jabatanArray,array("No"=>$i,"Jabatan"=>$data->Jabatan,"IDJabatan"=>$data->IDJabatan));
            }
        }

        $return = array("jabatan"=>$jabatanArray,"karyawan"=>$karyawan);
        echo json_encode($return);
        break;
        
    case "DetailKaryawan":
        $departement = array();
        $jabatan = array();
        $historyJabatan = array();
        $detailKaryawan = array();

        $id = antiSQLInjection($_GET['id']);

        $query = $db->get_results("SELECT * FROM tb_departement ORDER BY NamaDepartement ASC");
        if($query){
            foreach($query as $data){
                array_push($departement,array("IDDepartement"=>$data->IDDepartement,"NamaDepartement"=>$data->NamaDepartement));
            }
        }

        $query = $db->get_results("SELECT * FROM tb_jabatan ORDER BY Jabatan ASC");
        if($query){
            foreach($query as $data){
                array_push($jabatan,array("Jabatan"=>$data->Jabatan,"IDJabatan"=>$data->IDJabatan));
            }
        }

        $query = $db->get_results("SELECT a.*, b.Jabatan FROM tb_history_jabatan a, tb_jabatan b WHERE a.IDJabatan=b.IDJabatan AND a.IDKaryawan='$id' ORDER BY a.IDHistory ASC");
        if($query){
            foreach($query as $data){
                array_push($historyJabatan, array("jabatan"=>$data->Jabatan,"periode_awal"=>$data->PeriodeAwal,"periode_akhir"=>$data->PeriodeAkhir));
            }
        }

        $query = $db->get_row("SELECT a.*,b.Jabatan FROM tb_karyawan a, tb_jabatan b WHERE a.IDJabatan=b.IDJabatan AND a.IDKaryawan='$id' ORDER BY a.IDKaryawan ASC");
        if($query){
            $detailKaryawan = array("nik"=>$query->NIK,"nama_jabatan"=>$query->Jabatan,"thn_masuk"=>$query->TahunMasuk,"IDKaryawan"=>$query->IDKaryawan,"nama"=>$query->Nama,"jenis_kelamin"=>$query->JenisKelamin,"alamat_sementara"=>$query->AlamatSementara,"alamat_ktp"=>$query->AlamatKTP,"no_telp"=>$query->NoTelp,"email"=>$query->EmailPribadi,"stts_karyawan"=>$query->StatusKaryawan,"agama"=>$query->Agama,"stts_lainnya"=>$query->StatusLainnya,"tahunmasuk"=>$query->TahunMasuk,"jabatan"=>$query->IDJabatan,"nama_ayah"=>$query->NamaAyah,"alamat_ayah"=>$query->AlamatAyah,"no_telp_ayah"=>$query->NoTelpAyah,"nama_ibu"=>$query->NamaIbu,"alamat_ibu"=>$query->AlamatIbu,"no_telp_ibu"=>$query->NoTelpIbu,"nama_suami"=>$query->NamaSuami,"alamat_suami"=>$query->AlamatSuami,"no_telp_suami"=>$query->NoTelpSuami,"nama_wali"=>$query->NamaWali,"alamat_wali"=>$query->AlamatWali,"no_telp_wali"=>$query->NoTelpWali,"usrname"=>$query->Usernm,"martial_stts"=>$query->MartialStatus,"statusUser"=>$query->Status,"bln_masuk"=>$query->BulanMasuk,"foto"=>$query->Foto,"namabank"=>$query->NamaBank1,"norekening"=>$query->NoRekening1,"departement"=>$query->IDDepartement);
        }

        $return = array("departement"=>$departement,"jabatan"=>$jabatan,"historyJabatan"=>$historyJabatan,"detailKaryawan"=>$detailKaryawan);
        echo json_encode($return);
        break;

    default:
        echo "";
}