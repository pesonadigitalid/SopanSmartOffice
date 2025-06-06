<?php
include_once "../config/connection.php";

include_once "../library/class.awss3.php";
$AwsS3 = new AwsS3();

$id = antiSQLInjection($_POST['id']);
$nik = antiSQLInjection($_POST['nik']);
$nik2 = antiSQLInjection($_POST['nik2']);
$jabatan = antiSQLInjection($_POST['jabatan']);
$departement = antiSQLInjection($_POST['departement']);
$thn_masuk = antiSQLInjection($_POST['thn_masuk']);
$bln_masuk = antiSQLInjection($_POST['bln_masuk']);
$tgl_masuk = antiSQLInjection($_POST['tgl_masuk']);
$nama = antiSQLInjection($_POST['nama']);
$jenis_kelamin = antiSQLInjection($_POST['jenis_kelamin']);
$alamat_sementara = antiSQLInjection($_POST['alamat_sementara']);
$alamat_ktp = antiSQLInjection($_POST['alamat_ktp']);
$no_telp = antiSQLInjection($_POST['no_telp']);
$email = antiSQLInjection($_POST['email']);
$stts_karyawan = antiSQLInjection($_POST['stts_karyawan']);
$agama = antiSQLInjection($_POST['agama']);
$stts_lainnya = antiSQLInjection($_POST['stts_lainnya']);
$nama_ayah = antiSQLInjection($_POST['nama_ayah']);
$no_telp_ayah = antiSQLInjection($_POST['no_telp_ayah']);
$alamat_ayah = antiSQLInjection($_POST['alamat_ayah']);
$nama_ibu = antiSQLInjection($_POST['nama_ibu']);
$no_telp_ibu = antiSQLInjection($_POST['no_telp_ibu']);
$alamat_ibu = antiSQLInjection($_POST['alamat_ibu']);
$nama_suami = antiSQLInjection($_POST['nama_suami']);
$no_telp_suami = antiSQLInjection($_POST['no_telp_suami']);
$alamat_suami = antiSQLInjection($_POST['alamat_suami']);
$nama_wali = antiSQLInjection($_POST['nama_wali']);
$no_telp_wali = antiSQLInjection($_POST['no_telp_wali']);
$alamat_wali = antiSQLInjection($_POST['alamat_wali']);
$usrname = antiSQLInjection($_POST['usrname']);
$pass = antiSQLInjection($_POST['pass']);
$martial_stts = antiSQLInjection($_POST['martial_stts']);
$status = antiSQLInjection($_POST['status']);

$namabank = antiSQLInjection($_POST['namabank']);
$norekening = antiSQLInjection($_POST['norekening']);

$jabatan2 = antiSQLInjection($_POST['jabatan2']);
$no_ktp = antiSQLInjection($_POST['no_ktp']);
$absen_id = antiSQLInjection($_POST['absen_id']);

$tempat_lahir = antiSQLInjection($_POST['tempat_lahir']);
$tanggal_lahir = antiSQLInjection($_POST['tanggal_lahir']);

if ($tanggal_lahir != "") {
    $exptgl = explode("/", $tanggal_lahir);
    $tanggal_lahir = $exptgl[2] . "-" . $exptgl[1] . "-" . $exptgl[0];
} else $tanggal_lahir = "0000-00-00";

$tanggal_resign = antiSQLInjection($_POST['tanggal_resign']);
if ($tanggal_resign != "") {
    $exptgl = explode("/", $tanggal_resign);
    $tanggal_resign = $exptgl[2] . "-" . $exptgl[1] . "-" . $exptgl[0];
} else $tanggal_resign = "0000-00-00";

$pendidikan_terakhir = antiSQLInjection($_POST['pendidikan_terakhir']);
$jumlah_anak = antiSQLInjection($_POST['jumlah_anak']);
$id_proyek = antiSQLInjection($_POST['id_proyek']);

if ($status == "") $status = "0";
$cek = $db->get_row("SELECT * FROM tb_karyawan WHERE Usernm='$usrname' AND IDKaryawan!='$id'");
if ($cek) {
    echo "2";
} else {
    $sqlCond = "";
    if ($_FILES['file']) {
        // unlink the old one
        $data = $db->get_row("SELECT * FROM tb_karyawan WHERE IDKaryawan='$id'");
        if ($data) {
            if ($data->Foto != "")
                $AwsS3->deleteFile("karyawan_photo/" . $data->Foto);
        }

        $photoName = $AwsS3->uploadFileDirect("karyawan_photo",  $_FILES['file']);
        $sqlCond .= ", Foto='$photoName'";
    }

    if ($_FILES['file_surat_lamaran']) {
        // unlink the old one
        $data = $db->get_row("SELECT * FROM tb_karyawan WHERE IDKaryawan='$id'");
        if ($data) {
            if ($data->FileSuratLamaran != "")
                $AwsS3->deleteFile("karyawan_file/" . $data->FileSuratLamaran);
        }

        $surat_lamaranName = $AwsS3->uploadFileDirect("karyawan_file",  $_FILES['file_surat_lamaran']);
        $sqlCond .= ", FileSuratLamaran='$surat_lamaranName'";
    }

    if ($_FILES['file_ktp']) {
        // unlink the old one
        $data = $db->get_row("SELECT * FROM tb_karyawan WHERE IDKaryawan='$id'");
        if ($data) {
            if ($data->FileKTP != "")
                $AwsS3->deleteFile("karyawan_file/" . $data->FileKTP);
        }

        $ktpName = $AwsS3->uploadFileDirect("karyawan_file",  $_FILES['file_ktp']);
        $sqlCond .= ", FileKTP='$ktpName'";
    }

    if ($_FILES['file_kartu_keluarga']) {
        // unlink the old one
        $data = $db->get_row("SELECT * FROM tb_karyawan WHERE IDKaryawan='$id'");
        if ($data) {
            if ($data->FileKartuKeluarga != "")
                $AwsS3->deleteFile("karyawan_file/" . $data->FileKartuKeluarga);
        }

        $kartu_keluargaName = $AwsS3->uploadFileDirect("karyawan_file",  $_FILES['file_kartu_keluarga']);
        $sqlCond .= ", FileKartuKeluarga='$kartu_keluargaName'";
    }

    if ($_FILES['file_ijasah']) {
        // unlink the old one
        $data = $db->get_row("SELECT * FROM tb_karyawan WHERE IDKaryawan='$id'");
        if ($data) {
            if ($data->FileIjasah != "")
                $AwsS3->deleteFile("karyawan_file/" . $data->FileIjasah);
        }

        $ijasahName = $AwsS3->uploadFileDirect("karyawan_file",  $_FILES['file_ijasah']);
        $sqlCond .= ", FileIjasah='$ijasahName'";
    }

    if ($_FILES['file_sertifikat_asuransi']) {
        // unlink the old one
        $data = $db->get_row("SELECT * FROM tb_karyawan WHERE IDKaryawan='$id'");
        if ($data) {
            if ($data->FileSertifikatAsuransi != "")
                $AwsS3->deleteFile("karyawan_file/" . $data->FileSertifikatAsuransi);
        }

        $sertifikat_asuransiName = $AwsS3->uploadFileDirect("karyawan_file",  $_FILES['file_sertifikat_asuransi']);
        $sqlCond .= ", FileSertifikatAsuransi='$sertifikat_asuransiName'";
    }

    if ($_FILES['file_bpjs_kesehatan']) {
        // unlink the old one
        $data = $db->get_row("SELECT * FROM tb_karyawan WHERE IDKaryawan='$id'");
        if ($data) {
            if ($data->FileBPJSKesehatan != "")
                $AwsS3->deleteFile("karyawan_file/" . $data->FileBPJSKesehatan);
        }

        $bpjs_kesehatanName = $AwsS3->uploadFileDirect("karyawan_file",  $_FILES['file_bpjs_kesehatan']);
        $sqlCond .= ", FileBPJSKesehatan='$bpjs_kesehatanName'";
    }

    if ($_FILES['file_bpjs_ketenagakerjaan']) {
        // unlink the old one
        $data = $db->get_row("SELECT * FROM tb_karyawan WHERE IDKaryawan='$id'");
        if ($data) {
            if ($data->FileBPJSKetenagakerjaan != "")
                $AwsS3->deleteFile("karyawan_file/" . $data->FileBPJSKetenagakerjaan);
        }

        $bpjs_ketenagakerjaanName = $AwsS3->uploadFileDirect("karyawan_file",  $_FILES['file_bpjs_ketenagakerjaan']);
        $sqlCond .= ", FileBPJSKetenagakerjaan='$bpjs_ketenagakerjaanName'";
    }

    if ($_FILES['file_sertifikat_keterangan_kerja']) {
        // unlink the old one
        $data = $db->get_row("SELECT * FROM tb_karyawan WHERE IDKaryawan='$id'");
        if ($data) {
            if ($data->FileSertifikatKeteranganKerja != "")
                $AwsS3->deleteFile("karyawan_file/" . $data->FileSertifikatKeteranganKerja);
        }

        $sertifikat_keterangan_kerjaName = $AwsS3->uploadFileDirect("karyawan_file",  $_FILES['file_sertifikat_keterangan_kerja']);
        $sqlCond .= ", FileSertifikatKeteranganKerja='$sertifikat_keterangan_kerjaName'";
    }

    $query = "UPDATE tb_karyawan SET NIK='$nik', NIK_Manual='$nik2', IDJabatan='$jabatan', IDDepartement='$departement', IDJabatan2='$jabatan2', NoKTP='$no_ktp', TahunMasuk='$thn_masuk', BulanMasuk='$bln_masuk', TanggalMasuk='$tgl_masuk', Nama='$nama', JenisKelamin='$jenis_kelamin', AlamatSementara='$alamat_sementara', AlamatKTP='$alamat_ktp', NoTelp='$no_telp', EmailPribadi='$email', StatusKaryawan='$stts_karyawan', Agama='$agama', StatusLainnya='$stts_lainnya', NamaAyah='$nama_ayah', AlamatAyah='$alamat_ayah', NoTelpAyah='$no_telp_ayah', NamaIbu='$nama_ibu', AlamatIbu='$alamat_ibu', NoTelpIbu='$no_telp_ibu', NamaSuami='$nama_suami', AlamatSuami='$alamat_suami', NoTelpSuami='$no_telp_suami', NamaWali='$nama_wali', AlamatWali='$alamat_wali', NoTelpWali='$no_telp_wali', Usernm='$usrname', Status='$status', MartialStatus='$martial_stts', NamaBank1='$namabank', NoRekening1='$norekening', TempatLahir='$tempat_lahir', TglLahir='$tanggal_lahir', PendidikanTerakhir='$pendidikan_terakhir', JumlahAnak='$jumlah_anak', AbsentID='$absen_id', IDProyek='$id_proyek', TanggalResign='$tanggal_resign' $sqlCond";

    if ($pass != "") $query .= ", Passwd='" . md5($pass) . "'";
    if ($id != "") $query .= " WHERE IDKaryawan='$id'";

    $bulan = array(1 => "JAN", 2 => "FEB", 3 => "MAR", 4 => "APR", 5 => "MEI", 6 => "JUN", 7 => "JUL", 8 => "AGT", 9 => "SEP", 10 => "OKT", 11 => "NOV", 12 => "DES");
    //$periodeAwal = $bulan[intval($bln_masuk)]." ".$thn_masuk;

    //$db->query("INSERT INTO tb_history_jabatan SET IDKaryawan='$idk', PeriodeAwal='$periodeAwal', PeriodeAkhir='SEKARANG',  IDJabatan='$jabatan'");
    $cekHistoryJabatan = $db->get_row("SELECT * FROM tb_history_jabatan WHERE IDKaryawan='$id' ORDER BY IDHistory DESC");
    if ($cekHistoryJabatan) {
        if ($cekHistoryJabatan->IDJabatan != $jabatan) {
            $periodeAwal = $bulan[intval(date("m"))] . " " . date("Y");
            $db->query("UPDATE tb_history_jabatan SET PeriodeAkhir='$periodeAwal' WHERE IDHistory='" . $cekHistoryJabatan->IDHistory . "'");
            $db->query("INSERT INTO tb_history_jabatan SET IDKaryawan='$id', PeriodeAwal='$periodeAwal', PeriodeAkhir='SEKARANG',  IDJabatan='$jabatan'");
        }
    } else {
        $periodeAwal = $bulan[intval($bln_masuk)] . " " . $thn_masuk;

        $db->query("INSERT INTO tb_history_jabatan SET IDKaryawan='$id', PeriodeAwal='$periodeAwal', PeriodeAkhir='SEKARANG',  IDJabatan='$jabatan'");
    }

    $db->query($query);

    echo "1";
}
