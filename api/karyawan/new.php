<?php
include_once "../config/connection.php";

include_once "../library/class.awss3.php";
$AwsS3 = new AwsS3();

$nik2 = antiSQLInjection($_POST['nik2']);
$jabatan = antiSQLInjection($_POST['jabatan']);
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

if ($jenis_kelamin == "L") $intkelamin = "1";
else $intkelamin = "2";

$departement = antiSQLInjection($_POST['departement']);
$jabatan2 = antiSQLInjection($_POST['jabatan2']);
$no_ktp = antiSQLInjection($_POST['no_ktp']);
$absen_id = antiSQLInjection($_POST['absen_id']);
$id_proyek = antiSQLInjection($_POST['id_proyek']);

$dataLast = $db->get_row("SELECT * FROM tb_karyawan WHERE  IDKaryawan!='0' ORDER BY IDKaryawan DESC");
if ($dataLast) {
    $last = substr($dataLast->NIK, -3);
    $last++;
    if ($last < 10)
        $last = "00" . $last;
    else if ($last < 100)
        $last = "0" . $last;
    $nik = $thn_masuk . $bln_masuk . $intkelamin . $last;
} else {
    $nik = $thn_masuk . $bln_masuk . $intkelamin . "001";
}

if ($status == "") $status = "0";
$cek = $db->get_row("SELECT * FROM tb_karyawan WHERE Usernm='$usrname'");
if ($cek) {
    echo "2";
} else {
    if ($_FILES['file']) {
        $photoName = $AwsS3->uploadFileDirect("karyawan_photo",  $_FILES['file']);
    } else {
        $photoName = "";
    }

    if ($_FILES['file_surat_lamaran']) {
        $surat_lamaranName = $AwsS3->uploadFileDirect("karyawan_file",  $_FILES['file_surat_lamaran']);
    } else {
        $surat_lamaranName = "";
    }

    if ($_FILES['file_ktp']) {
        $ktpName = $AwsS3->uploadFileDirect("karyawan_file",  $_FILES['file_ktp']);
    } else {
        $ktpName = "";
    }

    if ($_FILES['file_kartu_keluarga']) {
        $kartu_keluargaName = $AwsS3->uploadFileDirect("karyawan_file",  $_FILES['file_kartu_keluarga']);
    } else {
        $kartu_keluargaName = "";
    }

    if ($_FILES['file_ijasah']) {
        $ijasahName = $AwsS3->uploadFileDirect("karyawan_file",  $_FILES['file_ijasah']);
    } else {
        $ijasahName = "";
    }

    if ($_FILES['file_sertifikat_asuransi']) {
        $sertifikat_asuransiName = $AwsS3->uploadFileDirect("karyawan_file",  $_FILES['file_sertifikat_asuransi']);
    } else {
        $sertifikat_asuransiName = "";
    }

    if ($_FILES['file_bpjs_kesehatan']) {
        $bpjs_kesehatanName = $AwsS3->uploadFileDirect("karyawan_file",  $_FILES['file_bpjs_kesehatan']);
    } else {
        $bpjs_kesehatanName = "";
    }

    if ($_FILES['file_bpjs_ketenagakerjaan']) {
        $bpjs_ketenagakerjaanName = $AwsS3->uploadFileDirect("karyawan_file",  $_FILES['file_bpjs_ketenagakerjaan']);
    } else {
        $bpjs_ketenagakerjaanName = "";
    }

    if ($_FILES['file_sertifikat_keterangan_kerja']) {
        $sertifikat_keterangan_kerjaName = $AwsS3->uploadFileDirect("karyawan_file",  $_FILES['file_sertifikat_keterangan_kerja']);
    } else {
        $sertifikat_keterangan_kerjaName = "";
    }

    $query = $db->query("INSERT INTO tb_karyawan SET NIK='$nik', NIK_Manual='$nik2', IDJabatan='$jabatan', IDDepartement='$departement', IDJabatan2='$jabatan2', NoKTP='$no_ktp', TahunMasuk='$thn_masuk', BulanMasuk='$bln_masuk', TanggalMasuk='$tgl_masuk', Nama='$nama', JenisKelamin='$jenis_kelamin', AlamatSementara='$alamat_sementara', AlamatKTP='$alamat_ktp', NoTelp='$no_telp', EmailPribadi='$email', StatusKaryawan='$stts_karyawan', Agama='$agama', StatusLainnya='$stts_lainnya', NamaAyah='$nama_ayah', AlamatAyah='$alamat_ayah', NoTelpAyah='$no_telp_ayah', NamaIbu='$nama_ibu', AlamatIbu='$alamat_ibu', NoTelpIbu='$no_telp_ibu', NamaSuami='$nama_suami', AlamatSuami='$alamat_suami', NoTelpSuami='$no_telp_suami', NamaWali='$nama_wali', AlamatWali='$alamat_wali', NoTelpWali='$no_telp_wali', Usernm='$usrname', Passwd=MD5('$pass'), Status='$status', MartialStatus='$martial_stts', Foto='$photoName', NamaBank1='$namabank', NoRekening1='$norekening', TempatLahir='$tempat_lahir', TglLahir='$tanggal_lahir', PendidikanTerakhir='$pendidikan_terakhir', JumlahAnak='$jumlah_anak', AbsentID='$absen_id', IDProyek='$id_proyek', FileSuratLamaran='$surat_lamaranName', FileKTP='$ktpName', FileKartuKeluarga='$kartu_keluargaName', FileIjasah='$ijasahName', FileSertifikatAsuransi='$sertifikat_asuransiName', FileBPJSKesehatan='$bpjs_kesehatanName', FileBPJSKetenagakerjaan='$bpjs_ketenagakerjaanName', FileSertifikatKeteranganKerja='$sertifikat_keterangan_kerjaName', TanggalResign='$tanggal_resign'");
    if ($query) {
        //INSERT TO HISTORY JABATAN
        $idk = $db->get_var("SELECT LAST_INSERT_ID()");

        $bulan = array(1 => "JAN", 2 => "FEB", 3 => "MAR", 4 => "APR", 5 => "MEI", 6 => "JUN", 7 => "JUL", 8 => "AGT", 9 => "SEP", 10 => "OKT", 11 => "NOV", 12 => "DES");
        $periodeAwal = $bulan[intval($bln_masuk)] . " " . $thn_masuk;

        $db->query("INSERT INTO tb_history_jabatan SET IDKaryawan='$idk', PeriodeAwal='$periodeAwal', PeriodeAkhir='SEKARANG',  IDJabatan='$jabatan'");

        echo "1";
    } else {
        echo "0";
    }
}
