<?php
include_once "../config/connection.php";
$bulan = array("01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", "09" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember");
$act = antiSQLInjection($_GET['act']);
switch ($act) {
    case "DataKaryawan":
        $jabatanArray = array();
        $departementArray = array();
        $karyawan = array();

        $idj = antiSQLInjection($_GET['idj']);
        $idk = antiSQLInjection($_GET['idk']);
        $jabatan = antiSQLInjection($_GET['jabatan']);
        $departement = antiSQLInjection($_GET['departement']);
        $status_karyawan = antiSQLInjection($_GET['status_karyawan']);
        $mark = antiSQLInjection($_GET['mark']);
        $status_akun = antiSQLInjection($_GET['status_akun']);
        $proyek = antiSQLInjection($_GET['proyek']);
        $agama = antiSQLInjection($_GET['agama']);

        $cond = "WHERE IDKaryawan>1";
        $cond2 = "";

        if ($_GET['status'] == "1") $cond .= " AND Status='1'";

        if ($idj != "") $cond .= " AND IDJabatan='$idj'";
        if ($idk != "") $cond .= " AND IDKaryawan='$idk'";

        if ($jabatan != "") $cond .= " AND IDJabatan='$jabatan'";
        if ($departement != "") $cond .= " AND IDDepartement='$departement'";
        if ($status_karyawan != "") $cond .= " AND StatusKaryawan='$status_karyawan'";
        if ($mark != "") $cond .= " AND StatusLainnya='$mark'";

        if ($status_akun != "") $cond2 .= " AND Status='$status_akun'";
        if ($proyek != "") $cond .= " AND IDProyek='$proyek'";
        if ($agama != "") $cond .= " AND Agama='$agama'";

        $query = $db->get_results("SELECT * FROM tb_karyawan $cond $cond2 ORDER BY IDKaryawan ASC");
        if ($query) {
            $i = 0;
            foreach ($query as $data) {
                $i++;
                if ($data->Status == "1") $status = "Aktif";
                else $status = "Non Aktif";
                $jabatan = $db->get_var("SELECT Jabatan FROM tb_jabatan WHERE IDJabatan='" . $data->IDJabatan . "'");

                $CountFinger = $db->get_var("SELECT COUNT(*) FROM tb_karyawan_finger WHERE IDKaryawan='" . $data->IDKaryawan . "'");
                if (!$CountFinger) $CountFinger = 0;

                $proyek = "-";
                if ($data->IDProyek > 0) {
                    $DProyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='" . $data->IDProyek . "'");
                    if ($DProyek) $proyek = $DProyek->KodeProyek . "/" . $DProyek->Tahun . " " . $DProyek->NamaProyek;
                }

                array_push($karyawan, array("No" => $i, "NIK" => $data->NIK, "Nama" => $data->Nama, "Status" => $data->StatusKaryawan, "StatusLainnya" => $data->StatusLainnya, "Jabatan" => $jabatan, "StatusK" => $status, "IDKaryawan" => $data->IDKaryawan, "CardNumber" => $data->CardNumber, "CountFinger" => $CountFinger, "AbsentID" => $data->AbsentID, "Agama" => $data->Agama, "Proyek" => $proyek));
            }
        }

        $query = $db->get_results("SELECT * FROM tb_jabatan ORDER BY IDJabatan ASC");
        if ($query) {
            foreach ($query as $data) {
                array_push($jabatanArray, array("No" => $i, "Jabatan" => $data->Jabatan, "IDJabatan" => $data->IDJabatan));
            }
        }

        $query = $db->get_results("SELECT * FROM tb_departement ORDER BY IDDepartement ASC");
        if ($query) {
            foreach ($query as $data) {
                array_push($departementArray, array("No" => $i, "Nama" => $data->NamaDepartement, "IDDepartement" => $data->IDDepartement));
            }
        }

        $proyek = array();
        $query = $db->get_results("SELECT a.*, b.NamaPelanggan, c.NamaDepartement FROM tb_proyek a, tb_pelanggan b, tb_departement c WHERE a.IDClient=b.IDPelanggan AND a.IDDepartement=c.IDDepartement ORDER BY a.Tahun ASC, a.KodeProyek ASC");
        if ($query) {
            $return = array();
            $i = 0;
            foreach ($query as $data) {
                $i++;
                $client = $db->get_var("SELECT NamaPelanggan FROM tb_pelanggan WHERE IDPelanggan='" . $data->IDClient . "'");
                if ($data->Status == "0") $status = "Tender";
                else if ($data->Status == "1") $status = "Fail";
                else if ($data->Status == "2") $status = "Process";
                else $status = "Complete";
                array_push($proyek, array("IDProyek" => $data->IDProyek, "Tahun" => $data->Tahun, "No" => $i, "NamaClient" => $client, "KodeProyek" => $data->KodeProyek, "NamaProyek" => $data->NamaProyek, "Status" => $status, "Departement" => $data->NamaDepartement));
            }
        }


        //GRAB ALL TOTAL DATA
        $all = $db->get_var("SELECT COUNT(*) FROM tb_karyawan $cond");
        if (!$all) $all = '';
        $active = $db->get_var("SELECT COUNT(*) FROM tb_karyawan $cond AND Status='1'");
        if (!$active) $active = '';
        $resign = $db->get_var("SELECT COUNT(*) FROM tb_karyawan $cond AND Status='0'");
        if (!$resign) $resign = '';

        $return = array("jabatan" => $jabatanArray, "departement" => $departementArray, "karyawan" => $karyawan, "all" => $all, "active" => $active, "resign" => $resign, "proyek" => $proyek);
        echo json_encode($return);
        break;

    case "DetailKaryawan":
        $departement = array();
        $jabatan = array();
        $historyJabatan = array();
        $detailKaryawan = array();
        $historyTraining = array();

        $id = antiSQLInjection($_GET['id']);

        $query = $db->get_results("SELECT * FROM tb_departement ORDER BY NamaDepartement ASC");
        if ($query) {
            foreach ($query as $data) {
                array_push($departement, array("IDDepartement" => $data->IDDepartement, "NamaDepartement" => $data->NamaDepartement));
            }
        }

        $query = $db->get_results("SELECT * FROM tb_jabatan ORDER BY Jabatan ASC");
        if ($query) {
            foreach ($query as $data) {
                array_push($jabatan, array("Jabatan" => $data->Jabatan, "IDJabatan" => $data->IDJabatan));
            }
        }

        $query = $db->get_results("SELECT a.*, b.Jabatan FROM tb_history_jabatan a, tb_jabatan b WHERE a.IDJabatan=b.IDJabatan AND a.IDKaryawan='$id' ORDER BY a.IDHistory ASC");
        if ($query) {
            foreach ($query as $data) {
                array_push($historyJabatan, array("jabatan" => $data->Jabatan, "periode_awal" => $data->PeriodeAwal, "periode_akhir" => $data->PeriodeAkhir));
            }
        }

        $query = $db->get_results("SELECT *, DATE_FORMAT(TanggalMulai,'%d/%m/%Y') AS TanggalMulaiID, DATE_FORMAT(TanggalSelesai,'%d/%m/%Y') AS TanggalSelesaiID FROM tb_training_record WHERE IDKaryawan='$id' ORDER BY TanggalMulai ASC");
        if ($query) {
            foreach ($query as $data) {

                $CheckInX = explode("-", $data->TanggalMulai);
                $CheckOutX =  explode("-", $data->TanggalSelesai);
                $date1 =  mktime(0, 0, 0, $CheckInX[1], $CheckInX[2], $CheckInX[0]);
                $date2 =  mktime(0, 0, 0, $CheckOutX[1], $CheckOutX[2], $CheckOutX[0]);
                $durasi = ($date2 - $date1) / (3600 * 24) + 1;

                array_push($historyTraining, array("NamaTraining" => $data->NamaTraining, "TanggalMulai" => $data->TanggalMulaiID, "TanggalSelesai" => $data->TanggalSelesaiID, "Durasi" => $durasi, "FileSertifikat" => $data->FileSertifikat));
            }
        }

        $query = $db->get_row("SELECT a.*, DATE_FORMAT(a.TglLahir,'%d/%m/%Y') AS TglLahirID, DATE_FORMAT(a.TanggalResign,'%d/%m/%Y') AS TanggalResignID FROM tb_karyawan a WHERE a.IDKaryawan='$id' ORDER BY a.IDKaryawan ASC");
        if ($query) {

            $jabatan1 = $db->get_var("SELECT Jabatan FROM tb_jabatan WHERE IDJabatan='" . $query->IDJabatan . "'");
            if ($jabatan1 != "") $nama_jabatan = $query->IDJabatan;
            else $nama_jabatan = "-";

            $jabatan2 = $db->get_var("SELECT Jabatan FROM tb_jabatan WHERE IDJabatan='" . $query->IDJabatan2 . "'");
            if ($jabatan2 != "") $nama_jabatan2 = $query->IDJabatan2;
            else $nama_jabatan2 = "-";

            $departement2 = $db->get_var("SELECT NamaDepartement FROM tb_departement WHERE IDDepartement='" . $query->IDDepartement . "'");
            if (!$departement2 != "") $departement2 = "-";

            if ($query->Status == "1") $statusUser = "Aktif";
            else $statusUser = "Tidak Aktif";

            if ($query->JenisKelamin == "L") $jns_kelamin = "Laki-laki";
            else $jns_kelamin = "Perempuan";

            $nama_dep = $db->get_var("SELECT NamaDepartement FROM tb_departement WHERE IDDepartement='" . $query->IDDepartement . "'");

            $detailKaryawan = array("nik" => $query->NIK, "nik2" => $query->NIK_Manual, "nama_jabatan" => $nama_jabatan, "thn_masuk" => $query->TahunMasuk, "IDKaryawan" => $query->IDKaryawan, "nama" => $query->Nama, "jenis_kelamin" => $query->JenisKelamin, "jenis_kelamin2" => $jns_kelamin, "jenis_kelamin2" => $jns_kelamin, "alamat_sementara" => $query->AlamatSementara, "alamat_ktp" => $query->AlamatKTP, "no_telp" => $query->NoTelp, "email" => $query->EmailPribadi, "stts_karyawan" => $query->StatusKaryawan, "agama" => $query->Agama, "stts_lainnya" => $query->StatusLainnya, "tahunmasuk" => $query->TahunMasuk, "jabatan" => $query->IDJabatan, "nama_jabatan2" => $nama_jabatan2, "nama_ayah" => $query->NamaAyah, "alamat_ayah" => $query->AlamatAyah, "no_telp_ayah" => $query->NoTelpAyah, "nama_ibu" => $query->NamaIbu, "alamat_ibu" => $query->AlamatIbu, "no_telp_ibu" => $query->NoTelpIbu, "nama_suami" => $query->NamaSuami, "alamat_suami" => $query->AlamatSuami, "no_telp_suami" => $query->NoTelpSuami, "nama_wali" => $query->NamaWali, "alamat_wali" => $query->AlamatWali, "no_telp_wali" => $query->NoTelpWali, "usrname" => $query->Usernm, "martial_stts" => $query->MartialStatus, "statusUser" => $query->Status, "namaStatusUser" => $statusUser, "bln_masuk" => $bulan[$query->BulanMasuk], "foto" => $query->Foto, "namabank" => $query->NamaBank1, "norekening" => $query->NoRekening1, "departement" => $query->IDDepartement, "departement2" => $departement2, "jabatan2" => $query->IDJabatan2, "no_ktp" => $query->NoKTP, "tempat_lahir" => $query->TempatLahir, "tanggal_lahir" => $query->TglLahirID, "pendidikan_terakhir" => $query->PendidikanTerakhir, "jumlah_anak" => $query->JumlahAnak, "bln_masuk" => $query->BulanMasuk, "tgl_masuk" => $query->TanggalMasuk, "absen_id" => $query->AbsentID, "id_proyek" => $query->IDProyek, "file_surat_lamaran" => $query->FileSuratLamaran, "file_ktp" => $query->FileKTP, "file_kartu_keluarga" => $query->FileKartuKeluarga, "file_ijasah" => $query->FileIjasah, "file_sertifikat_asuransi" => $query->FileSertifikatAsuransi, "file_bpjs_kesehatan" => $query->FileBPJSKesehatan, "file_bpjs_ketenagakerjaan" => $query->FileBPJSKetenagakerjaan, "file_sertifikat_keterangan_kerja" => $query->FileSertifikatKeteranganKerja, "tanggal_resign" => $query->TanggalResignID);
        }

        $proyek = array();
        $query = $db->get_results("SELECT a.*, b.NamaPelanggan, c.NamaDepartement FROM tb_proyek a, tb_pelanggan b, tb_departement c WHERE a.IDClient=b.IDPelanggan AND a.IDDepartement=c.IDDepartement AND a.Status='2' ORDER BY a.Tahun ASC, a.KodeProyek ASC");
        if ($query) {
            $return = array();
            $i = 0;
            foreach ($query as $data) {
                $i++;
                $client = $db->get_var("SELECT NamaPelanggan FROM tb_pelanggan WHERE IDPelanggan='" . $data->IDClient . "'");
                if ($data->Status == "0") $status = "Tender";
                else if ($data->Status == "1") $status = "Fail";
                else if ($data->Status == "2") $status = "Process";
                else $status = "Complete";
                array_push($proyek, array("IDProyek" => $data->IDProyek, "Tahun" => $data->Tahun, "No" => $i, "NamaClient" => $client, "KodeProyek" => $data->KodeProyek, "NamaProyek" => $data->NamaProyek, "Status" => $status, "Departement" => $data->NamaDepartement));
            }
        }

        $return = array("departement" => $departement, "jabatan" => $jabatan, "historyJabatan" => $historyJabatan, "detailKaryawan" => $detailKaryawan, "historyTraining" => $historyTraining, "proyek" => $proyek);
        echo json_encode($return);
        break;

    case "GetHakAkses":
        $idk = antiSQLInjection($_GET['idk']);
        $permission = array();

        $dataKaryawan = $db->get_row("SELECT * FROM tb_karyawan WHERE IDKaryawan='$idk'");
        $departement = $db->get_var("SELECT NamaDepartement FROM tb_departement WHERE IDDepartement='" . $dataKaryawan->IDDepartement . "'");
        $jabatan = $db->get_var("SELECT Jabatan FROM tb_jabatan WHERE IDJabatan='" . $dataKaryawan->IDJabatan . "'");
        $karyawan = array("Nama" => $dataKaryawan->Nama, "NIK" => $dataKaryawan->NIK, "Jabatan" => $jabatan, "Departement" => $departement);

        $query = $db->get_results("SELECT * FROM tb_module");
        if ($query) {
            foreach ($query as $data) {
                $cek = $db->get_row("SELECT * FROM tb_hak_akses WHERE IDMember='$idk' AND IDModule='" . $data->IDModule . "'");
                if ($cek) {
                    if ($cek->Read == "1") $read = true;
                    else $read = false;
                    if ($cek->Write == "1") $write = true;
                    else $write = false;
                } else {
                    $read = false;
                    $write = false;
                }
                array_push($permission, array("idmodule" => $data->IDModule, "module" => $data->ModuleName, "moduleName" => $data->VisibilityName, "read" => $read, "write" => $write));
            }
        }
        echo json_encode(array("permission" => $permission, "karyawan" => $karyawan));
        break;

    case "GetHakAksesAll":
        $idk = antiSQLInjection($_GET['idk']);
        $permission = array();
        $module = array();

        $queryKaryawan = $db->get_results("SELECT * FROM tb_karyawan WHERE Status='1' ORDER BY IDKaryawan ASC");
        if ($queryKaryawan) {
            foreach ($queryKaryawan as $dataKaryawan) {
                $temp = array("IDKaryawan" => $dataKaryawan->IDKaryawan, "Nama" => $dataKaryawan->Nama, "Permission" => array());
                $queryModule = $db->get_results("SELECT * FROM tb_module ORDER BY IDModule ASC");
                if ($queryModule) {
                    foreach ($queryModule as $dataModule) {
                        $cek = $db->get_row("SELECT * FROM tb_hak_akses WHERE IDMember='" . $dataKaryawan->IDKaryawan . "' AND IDModule='" . $dataModule->IDModule . "'");
                        if ($cek) {
                            if ($cek->Read == "1") $read = true;
                            else $read = false;
                            if ($cek->Write == "1") $write = true;
                            else $write = false;
                            if ($cek->Delete == "1") $delete = true;
                            else $delete = false;
                        } else {
                            $read = false;
                            $write = false;
                            $delete = false;
                        }
                        array_push($temp['Permission'], array("idmodule" => $dataModule->IDModule, "module" => $dataModule->ModuleName, "moduleName" => $dataModule->VisibilityName, "read" => $read, "write" => $write, "delete" => $delete));
                    }
                }
                array_push($permission, $temp);
            }
        }

        $query = $db->get_results("SELECT * FROM tb_module ORDER BY IDModule ASC");
        if ($query) {
            foreach ($query as $data) {
                array_push($module, array("idmodule" => $data->IDModule, "module" => $data->ModuleName, "moduleName" => $data->VisibilityName));
            }
        }
        echo json_encode(array("permission" => $permission, "module" => $module));
        break;

    case "SaveHakAkses":
        $idk = antiSQLInjection($_POST['idk']);
        $hak_akses = antiSQLInjection($_POST['hak_akses']);
        $hak_akses = json_decode($hak_akses);

        foreach ($hak_akses as $data) {
            if (isset($data)) {
                if ($data->read == "true")
                    $read = "1";
                else
                    $read = "0";

                if ($data->write == "true")
                    $write = "1";
                else
                    $write = "0";

                $cek = $db->get_row("SELECT * FROM tb_hak_akses WHERE IDMember='$idk' AND IDModule='" . $data->idmodule . "'");
                if ($cek)
                    $sql = "UPDATE tb_hak_akses SET `Read`='" . $read . "', `Write`='" . $write . "' WHERE IDAccess='" . $cek->IDAccess . "'";
                else
                    $sql = "INSERT INTO tb_hak_akses SET IDMember='$idk', IDModule='" . $data->idmodule . "', `Read`='" . $read . "', `Write`='" . $write . "'";
                $db->query($sql);
            }
        }
        echo "1";
        break;

    case "SaveHakAksesModule":
        $idk = antiSQLInjection($_POST['idk']);
        $idmodule = antiSQLInjection($_POST['idmodule']);
        $tipe = antiSQLInjection($_POST['tipe']);
        $value = antiSQLInjection($_POST['value']);
        if ($value == 'true') $value = 1;
        else $value = 0;

        if ($tipe === 'read') {
            $field = "`Read`='$value'";
        } else if ($tipe === 'write') {
            $field = "`Write`='$value'";
        } else {
            $field = "`Delete`='$value'";
        }

        $cek = $db->get_row("SELECT * FROM tb_hak_akses WHERE IDMember='$idk' AND IDModule='" . $idmodule . "'");
        if ($cek)
            $sql = "UPDATE tb_hak_akses SET $field WHERE IDAccess='" . $cek->IDAccess . "'";
        else
            $sql = "INSERT INTO tb_hak_akses SET IDMember='$idk', IDModule='" . $idmodule . "', $field";
        $db->query($sql);
        echo "1";
        break;

    case "GrantAccessGaji":
        $pass = antiSQLInjection($_GET['pass']);
        $cek = $db->get_row("SELECT * FROM tb_karyawan WHERE (IDKaryawan='6' || IDKaryawan='24') AND Passwd='" . md5($pass) . "'");
        if ($cek) echo "1";
        else echo "2";
        break;

    default:
        echo "";
}
