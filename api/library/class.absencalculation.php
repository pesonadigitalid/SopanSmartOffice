<?php
class AbsenCalculation
{
    private $holiday = array();
    private $db;
    private $minimunWaktuKedatangan = "08:35";

    function __construct()
    {
        $this->db = new ezSQL_mysql("root", "diadmin", "sopan", "localhost");
    }

    function calcHolidayWithoutSunday($tahun)
    {
        $holiday = array();
        $db = $this->db;
        $query = $db->get_results("SELECT * FROM tb_public_holiday WHERE DATE_FORMAT(DateCreated,'%Y')>='" . $tahun . "' ORDER BY DariTanggal ASC");
        if ($query) {
            foreach ($query as $data) {
                $begin = new DateTime($data->DariTanggal);
                $end = new DateTime($data->SampaiTanggal);

                $interval = DateInterval::createFromDateString('1 day');
                $period = new DatePeriod($begin, $interval, $end);
                $i = 0;
                foreach ($period as $dt) {
                    $i++;
                    //Jangan ngambil data hari libur yang memang hari minggu.
                    if ($dt->format("D") != "Sun") {
                        $nDate = $dt->format("Y-m-d");
                        array_push($holiday, $nDate);
                    }
                }

                if ($i == 0) {
                    $dt = new DateTime($data->DariTanggal);
                    if ($dt->format("D") != "Sun") {
                        array_push($holiday, $data->DariTanggal);
                    }
                } else if ($data->DariTanggal != $data->SampaiTanggal) {
                    $dt = new DateTime($data->SampaiTanggal);
                    if ($dt->format("D") != "Sun") {
                        array_push($holiday, $data->SampaiTanggal);
                    }
                }
            }
        }

        $this->holiday = $holiday;
        return true;
    }

    function generateAbsentBulananKaryawan($tahun, $bulan, $karyawan)
    {
        if ($karyawan != "") {
            $db = $this->db;
            $holiday = $this->holiday;
            $return = array();

            $bulan2 = intval($bulan) - 1;
            if ($bulan2 < 10) $bulan2 = "0" . $bulan2;

            if ($bulan == "01") {
                $tahun2 = $tahun - 1;
                $bulan2 = "12";
            } else {
                $tahun2 = $tahun;
            }

            $db->query("DELETE FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='$karyawan' AND (Tanggal BETWEEN '$tahun2-$bulan2-26' AND '$tahun-$bulan-25')");

            // Absent Tanggal 26 Bulan Lalu
            for ($i = 26; $i <= cal_days_in_month(CAL_GREGORIAN, $bulan2, $tahun2); $i++) {
                if ($i < 10) $tgl = "0" . $i;
                else $tgl = $i;
                $tanggal = $tahun2 . "-" . $bulan2 . "-" . $tgl;
                $tanggalID = $tgl . "/" . $bulan2 . "/" . $tahun2;
                $isHoliday = false;
                $keteranganHoliday = "";

                $cek  = new DateTime($tanggal);
                if ($cek->format("D") == "Sun") {
                    $isHoliday = true;
                    $keteranganHoliday = "Hari Minggu";
                }

                if (in_array($tanggal, $holiday)) {
                    $isHoliday = true;
                    $cekHoliday = $db->get_row("SELECT * FROM tb_public_holiday WHERE '$tanggal' BETWEEN DariTanggal AND SampaiTanggal");
                    if ($keteranganHoliday == "Hari Minggu")
                        $keteranganHoliday .= " / " . $cekHoliday->NamaHariLibur;
                    else
                        $keteranganHoliday = $cekHoliday->NamaHariLibur;
                }

                $datang = $db->get_var("SELECT DATE_FORMAT(DateTimeAbsent,'%H:%i') FROM tb_absent WHERE IDKaryawan='$karyawan' AND Tipe='I' AND DATE_FORMAT(DateTimeAbsent,'%Y-%m-%d')='$tanggal' ORDER BY DateTimeAbsent ASC");
                if (!$datang) $datang = "-";

                $pulang = $db->get_var("SELECT DATE_FORMAT(DateTimeAbsent,'%H:%i') FROM tb_absent WHERE IDKaryawan='$karyawan' AND Tipe='O' AND DATE_FORMAT(DateTimeAbsent,'%Y-%m-%d')='$tanggal' ORDER BY  DateTimeAbsent ASC");
                if (!$pulang) $pulang = "-";

                $proyekDatang = $db->get_var("SELECT DATE_FORMAT(`Datetime`,'%H:%i') FROM tb_proyek_absent WHERE DATE_FORMAT(`Datetime`,'%Y-%m-%d')='$tanggal' AND Jenis='1' AND IDKaryawan='$karyawan' ORDER BY `Datetime` ASC");
                if (!$proyekDatang) $proyekDatang = "-";

                $proyekPulang = $db->get_var("SELECT DATE_FORMAT(`Datetime`,'%H:%i') FROM tb_proyek_absent WHERE DATE_FORMAT(`Datetime`,'%Y-%m-%d')='$tanggal' AND Jenis='2' AND IDKaryawan='$karyawan' ORDER BY `Datetime` ASC");
                if (!$proyekPulang) $proyekPulang = "-";

                if ($datang == "-" && $pulang == "-" && $proyekDatang == "-" && $proyekPulang == "-") $totalJamKerja = 0;
                else if ($datang != "-" && $pulang == "-" && $proyekDatang == "-" && $proyekPulang == "-") $totalJamKerja = 8;
                else if ($datang == "-" && $pulang != "-" && $proyekDatang == "-" && $proyekPulang == "-") $totalJamKerja = 8;
                else if ($datang == "-" && $pulang == "-" && $proyekDatang != "-" && $proyekPulang == "-") $totalJamKerja = 8;
                else if ($datang == "-" && $pulang == "-" && $proyekDatang == "-" && $proyekPulang != "-") $totalJamKerja = 8;
                else if ($datang != "-" && $pulang != "-" && $proyekDatang == "-" && $proyekPulang == "-") {
                    $totalJamKerja = ceil((strtotime($pulang) - strtotime($datang)) / 3600);
                } else if ($datang == "-" && $pulang == "-" && $proyekDatang != "-" && $proyekPulang != "-") {
                    $totalJamKerja = ceil((strtotime($proyekPulang) - strtotime($proyekDatang)) / 3600);
                } else if ($datang != "-" && $pulang == "-" && $proyekDatang == "-" && $proyekPulang != "-") {
                    $totalJamKerja = ceil((strtotime($proyekPulang) - strtotime($datang)) / 3600);
                } else if ($datang == "-" && $pulang != "-" && $proyekDatang != "-" && $proyekPulang == "-") {
                    $totalJamKerja = ceil((strtotime($pulang) - strtotime($proyekDatang)) / 3600);
                } else if ($datang != "-" && $pulang != "-" && $proyekDatang != "-" && $proyekPulang != "-") {
                    $totalJamKerja = ceil((strtotime($pulang) - strtotime($datang)) / 3600);
                    $totalJamKerja = intval($totalJamKerja) + ceil((strtotime($proyekPulang) - strtotime($proyekDatang)) / 3600);
                }

                if ($datang != "-") {
                    if (strtotime($datang) < strtotime("10:00") && strtotime($datang) > strtotime($this->minimunWaktuKedatangan))
                        $terlambat = ceil((strtotime($datang) - strtotime($this->minimunWaktuKedatangan)) / 60);
                    else
                        $terlambat = "";
                } else
                    $terlambat = "";

                $datangLembur = $db->get_var("SELECT DATE_FORMAT(DateTimeAbsent,'%H:%i') FROM tb_absent_lembur WHERE IDKaryawan='$karyawan' AND Tipe='I' AND DATE_FORMAT(DateTimeAbsent,'%Y-%m-%d')='$tanggal' ORDER BY DateTimeAbsent ASC");
                if (!$datangLembur) $datangLembur = "-";

                $pulangLembur = $db->get_var("SELECT DATE_FORMAT(DateTimeAbsent,'%H:%i') FROM tb_absent_lembur WHERE IDKaryawan='$karyawan' AND Tipe='O' AND DATE_FORMAT(DateTimeAbsent,'%Y-%m-%d')='$tanggal' ORDER BY  DateTimeAbsent ASC");
                if (!$pulangLembur) $pulangLembur = "-";

                if ($datangLembur != "-" && $pulangLembur == "-") $totalJamKerjaLembur = 0;
                else if ($datangLembur == "-" && $pulangLembur != "-") $totalJamKerjaLembur = 0;
                else if ($datangLembur != "-" && $pulangLembur != "-") {
                    $pulangLemburStrToTime = strtotime($pulangLembur);
                    if (intval($pulangLembur) < 6) {
                        $pulangLemburStrToTime += (60 * 60 * 24);
                    }
                    $totalJamKerjaLembur = ceil(($pulangLemburStrToTime - strtotime($datangLembur)) / 3600);
                } else $totalJamKerjaLembur = 0;

                if ($isHoliday == true) {
                    $cutiTahunan = "";
                    $cutiSpecial = "";
                    $cutiSakit = "";
                    $alpha = "";
                    $tugas = "";
                } else {
                    $cutiTahunan = $db->get_var("SELECT SUM(JumlahHari) FROM tb_cuti WHERE Jenis='CUTI TAHUNAN' AND IDKaryawan='$karyawan' AND (DariTanggal='$tanggal' OR '$tanggal' BETWEEN DariTanggal AND SampaiTanggal) AND Status='2'");
                    if (!$cutiTahunan) $cutiTahunan = "";
                    else if ($cutiTahunan > 1) $cutiTahunan = 1;

                    $cutiSakit = $db->get_var("SELECT SUM(JumlahHari) FROM tb_cuti WHERE Jenis='SAKIT' AND IDKaryawan='$karyawan' AND (DariTanggal='$tanggal' OR '$tanggal' BETWEEN DariTanggal AND SampaiTanggal) AND Status='2'");
                    if (!$cutiSakit) $cutiSakit = "";
                    else if ($cutiSakit > 1) $cutiSakit = 1;

                    $cutiSpecial = $db->get_var("SELECT SUM(JumlahHari) FROM tb_cuti WHERE Jenis='CUTI SPECIAL' AND IDKaryawan='$karyawan' AND (DariTanggal='$tanggal' OR '$tanggal' BETWEEN DariTanggal AND SampaiTanggal) AND Status='2'");
                    if (!$cutiSpecial) $cutiSpecial = "";
                    else if ($cutiSpecial > 1) $cutiSpecial = 1;

                    $alpha = $db->get_var("SELECT SUM(JumlahHari) FROM tb_cuti WHERE Jenis='ALPHA' AND IDKaryawan='$karyawan' AND (DariTanggal='$tanggal' OR '$tanggal' BETWEEN DariTanggal AND SampaiTanggal)");
                    if (!$alpha) $alpha = "";
                    else if ($alpha > 1) $alpha = 1;

                    $tugas = $db->get_var("SELECT SUM(JumlahHari) FROM tb_cuti WHERE Jenis='TUGAS LUAR KANTOR' AND IDKaryawan='$karyawan' AND (DariTanggal='$tanggal' OR '$tanggal' BETWEEN DariTanggal AND SampaiTanggal)");
                    if (!$tugas) $tugas = "";
                    else if ($tugas > 1) $tugas = 1;

                    if ($cutiTahunan >= 1 || $cutiSakit >= 1 || $cutiSpecial >= 1 || $alpha >= 1 || $tugas >= 1) {
                        $totalJamKerja = 0;
                        $totalJamKerjaLembur = 0;
                    }
                }

                if ($totalJamKerja > 0) $totalJamKerja = $totalJamKerja - 1;
                //if($totalJamKerjaLembur>0) $totalJamKerjaLembur = $totalJamKerjaLembur - 1;
                if ($totalJamKerjaLembur > 0) $totalJamKerjaLembur = $totalJamKerjaLembur;

                $keterangan = $db->get_var("SELECT Keterangan FROM tb_absent WHERE IDKaryawan='$karyawan' AND DATE_FORMAT(DateTimeAbsent,'%Y-%m-%d')='$tanggal' ORDER BY DateTimeAbsent ASC");

                $db->query("INSERT INTO tb_absent_karyawan_bulanan SET Tanggal='$tanggal', IDKaryawan='$karyawan', JamMasuk='$datang', JamKeluar='$pulang', TotalJam='0', ProyekMasuk='$proyekDatang', ProyekKeluar='$proyekPulang', TotalJamProyek='0', LemburMasuk='$datangLembur', LemburKeluar='$pulangLembur', TotalJamLembur='$totalJamKerjaLembur', CutiTahunan='$cutiTahunan', CutiSakit='$cutiSakit', CutiSpecial='$cutiSpecial', CutiAlpha='$alpha', CutiTugasKeluar='$tugas', TotalJamKerja='$totalJamKerja', Keterangan='$keterangan', KeteranganHoliday='$keteranganHoliday', Terlambat='$terlambat'");

                array_push($return, array("Tanggal" => $tanggalID, "Datang" => $datang, "Pulang" => $pulang, "ProyekDatang" => $proyekDatang, "ProyekPulang" => $proyekPulang, "CutiTahunan" => $cutiTahunan, "CutiSakit" => $cutiSakit, "CutiSpecial" => $cutiSpecial, "CutiAlpha" => $alpha, "CutiTugasKeluar" => $tugas, "TotalJamKerja" => $totalJamKerja, "IsHoliday" => $isHoliday, "KeteranganHoliday" => $keteranganHoliday, "Format" => $cek->format("D"), "DatangLembur" => $datangLembur, "PulangLembur" => $pulangLembur, "TotalJamKerjaLembur" => $totalJamKerjaLembur, "Terlambat" => $terlambat, "Keterangan" => $keterangan));
            }

            // Absent Bulan Sekarang 01-25
            for ($i = 1; $i <= 25; $i++) {
                if ($i < 10) $tgl = "0" . $i;
                else $tgl = $i;
                $tanggal = $tahun . "-" . $bulan . "-" . $tgl;
                $tanggalID = $tgl . "/" . $bulan . "/" . $tahun;
                $isHoliday = false;
                $keteranganHoliday = "";

                $cek  = new DateTime($tanggal);
                if ($cek->format("D") == "Sun") {
                    $isHoliday = true;
                    $keteranganHoliday = "Hari Minggu";
                }

                if (in_array($tanggal, $holiday)) {
                    $isHoliday = true;
                    $cekHoliday = $db->get_row("SELECT * FROM tb_public_holiday WHERE '$tanggal' BETWEEN DariTanggal AND SampaiTanggal");
                    if ($keteranganHoliday == "Hari Minggu")
                        $keteranganHoliday .= " / " . $cekHoliday->NamaHariLibur;
                    else
                        $keteranganHoliday = $cekHoliday->NamaHariLibur;
                }

                $datang = $db->get_var("SELECT DATE_FORMAT(DateTimeAbsent,'%H:%i') FROM tb_absent WHERE IDKaryawan='$karyawan' AND Tipe='I' AND DATE_FORMAT(DateTimeAbsent,'%Y-%m-%d')='$tanggal' ORDER BY DateTimeAbsent ASC");
                if (!$datang) $datang = "-";

                $pulang = $db->get_var("SELECT DATE_FORMAT(DateTimeAbsent,'%H:%i') FROM tb_absent WHERE IDKaryawan='$karyawan' AND Tipe='O' AND DATE_FORMAT(DateTimeAbsent,'%Y-%m-%d')='$tanggal' ORDER BY  DateTimeAbsent ASC");
                if (!$pulang) $pulang = "-";

                $proyekDatang = $db->get_var("SELECT DATE_FORMAT(`Datetime`,'%H:%i') FROM tb_proyek_absent WHERE DATE_FORMAT(`Datetime`,'%Y-%m-%d')='$tanggal' AND Jenis='1' AND IDKaryawan='$karyawan' ORDER BY `Datetime` ASC");
                if (!$proyekDatang) $proyekDatang = "-";

                $proyekPulang = $db->get_var("SELECT DATE_FORMAT(`Datetime`,'%H:%i') FROM tb_proyek_absent WHERE DATE_FORMAT(`Datetime`,'%Y-%m-%d')='$tanggal' AND Jenis='2' AND IDKaryawan='$karyawan' ORDER BY `Datetime` ASC");
                if (!$proyekPulang) $proyekPulang = "-";

                if ($datang == "-" && $pulang == "-" && $proyekDatang == "-" && $proyekPulang == "-") $totalJamKerja = 0;
                else if ($datang != "-" && $pulang == "-" && $proyekDatang == "-" && $proyekPulang == "-") $totalJamKerja = 8;
                else if ($datang == "-" && $pulang != "-" && $proyekDatang == "-" && $proyekPulang == "-") $totalJamKerja = 8;
                else if ($datang == "-" && $pulang == "-" && $proyekDatang != "-" && $proyekPulang == "-") $totalJamKerja = 8;
                else if ($datang == "-" && $pulang == "-" && $proyekDatang == "-" && $proyekPulang != "-") $totalJamKerja = 8;
                else if ($datang != "-" && $pulang != "-" && $proyekDatang == "-" && $proyekPulang == "-") {
                    $totalJamKerja = ceil((strtotime($pulang) - strtotime($datang)) / 3600);
                } else if ($datang == "-" && $pulang == "-" && $proyekDatang != "-" && $proyekPulang != "-") {
                    $totalJamKerja = ceil((strtotime($proyekPulang) - strtotime($proyekDatang)) / 3600);
                } else if ($datang != "-" && $pulang == "-" && $proyekDatang == "-" && $proyekPulang != "-") {
                    $totalJamKerja = ceil((strtotime($proyekPulang) - strtotime($datang)) / 3600);
                } else if ($datang == "-" && $pulang != "-" && $proyekDatang != "-" && $proyekPulang == "-") {
                    $totalJamKerja = ceil((strtotime($pulang) - strtotime($proyekDatang)) / 3600);
                } else if ($datang != "-" && $pulang != "-" && $proyekDatang != "-" && $proyekPulang != "-") {
                    $totalJamKerja = ceil((strtotime($pulang) - strtotime($datang)) / 3600);
                    $totalJamKerja = intval($totalJamKerja) + ceil((strtotime($proyekPulang) - strtotime($proyekDatang)) / 3600);
                }

                if ($datang != "-") {
                    if (strtotime($datang) < strtotime("10:00") && strtotime($datang) > strtotime($this->minimunWaktuKedatangan))
                        $terlambat = ceil((strtotime($datang) - strtotime($this->minimunWaktuKedatangan)) / 60);
                    else
                        $terlambat = "";
                } else
                    $terlambat = "";

                $datangLembur = $db->get_var("SELECT DATE_FORMAT(DateTimeAbsent,'%H:%i') FROM tb_absent_lembur WHERE IDKaryawan='$karyawan' AND Tipe='I' AND DATE_FORMAT(DateTimeAbsent,'%Y-%m-%d')='$tanggal' ORDER BY DateTimeAbsent ASC");
                if (!$datangLembur) $datangLembur = "-";

                $pulangLembur = $db->get_var("SELECT DATE_FORMAT(DateTimeAbsent,'%H:%i') FROM tb_absent_lembur WHERE IDKaryawan='$karyawan' AND Tipe='O' AND DATE_FORMAT(DateTimeAbsent,'%Y-%m-%d')='$tanggal' ORDER BY  DateTimeAbsent ASC");
                if (!$pulangLembur) $pulangLembur = "-";

                if ($datangLembur != "-" && $pulangLembur == "-") $totalJamKerjaLembur = 4;
                else if ($datangLembur == "-" && $pulangLembur != "-") $totalJamKerjaLembur = 4;
                else if ($datangLembur != "-" && $pulangLembur != "-") {
                    $pulangLemburStrToTime = strtotime($pulangLembur);
                    if (intval($pulangLembur) < 6) {
                        $pulangLemburStrToTime += (60 * 60 * 24);
                    }
                    $totalJamKerjaLembur = ceil(($pulangLemburStrToTime - strtotime($datangLembur)) / 3600);
                } else $totalJamKerjaLembur = 0;

                if ($isHoliday == true) {
                    $cutiTahunan = "";
                    $cutiSpecial = "";
                    $cutiSakit = "";
                    $alpha = "";
                    $tugas = "";
                } else {
                    $cutiTahunan = $db->get_var("SELECT SUM(JumlahHari) FROM tb_cuti WHERE Jenis='CUTI TAHUNAN' AND IDKaryawan='$karyawan' AND (DariTanggal='$tanggal' OR '$tanggal' BETWEEN DariTanggal AND SampaiTanggal) AND Status='2'");
                    if (!$cutiTahunan) $cutiTahunan = "";
                    else if ($cutiTahunan > 1) $cutiTahunan = 1;

                    $cutiSakit = $db->get_var("SELECT SUM(JumlahHari) FROM tb_cuti WHERE Jenis='SAKIT' AND IDKaryawan='$karyawan' AND (DariTanggal='$tanggal' OR '$tanggal' BETWEEN DariTanggal AND SampaiTanggal) AND Status='2'");
                    if (!$cutiSakit) $cutiSakit = "";
                    else if ($cutiSakit > 1) $cutiSakit = 1;

                    $cutiSpecial = $db->get_var("SELECT SUM(JumlahHari) FROM tb_cuti WHERE Jenis='CUTI SPECIAL' AND IDKaryawan='$karyawan' AND (DariTanggal='$tanggal' OR '$tanggal' BETWEEN DariTanggal AND SampaiTanggal) AND Status='2'");
                    if (!$cutiSpecial) $cutiSpecial = "";
                    else if ($cutiSpecial > 1) $cutiSpecial = 1;

                    $alpha = $db->get_var("SELECT SUM(JumlahHari) FROM tb_cuti WHERE Jenis='ALPHA' AND IDKaryawan='$karyawan' AND (DariTanggal='$tanggal' OR '$tanggal' BETWEEN DariTanggal AND SampaiTanggal)");
                    if (!$alpha) $alpha = "";
                    else if ($alpha > 1) $alpha = 1;

                    $tugas = $db->get_var("SELECT SUM(JumlahHari) FROM tb_cuti WHERE Jenis='TUGAS LUAR KANTOR' AND IDKaryawan='$karyawan' AND (DariTanggal='$tanggal' OR '$tanggal' BETWEEN DariTanggal AND SampaiTanggal)");
                    if (!$tugas) $tugas = "";
                    else if ($tugas > 1) $tugas = 1;

                    if ($cutiTahunan >= 1 || $cutiSakit >= 1 || $cutiSpecial >= 1 || $alpha >= 1 || $tugas >= 1) {
                        $totalJamKerja = 0;
                        $totalJamKerjaLembur = 0;
                    }
                }

                if ($totalJamKerja > 0) $totalJamKerja = $totalJamKerja - 1;
                //if($totalJamKerjaLembur>0) $totalJamKerjaLembur = $totalJamKerjaLembur - 1;

                $keterangan = $db->get_var("SELECT Keterangan FROM tb_absent WHERE IDKaryawan='$karyawan' AND DATE_FORMAT(DateTimeAbsent,'%Y-%m-%d')='$tanggal' ORDER BY DateTimeAbsent ASC");

                $db->query("INSERT INTO tb_absent_karyawan_bulanan SET Tanggal='$tanggal', IDKaryawan='$karyawan', JamMasuk='$datang', JamKeluar='$pulang', TotalJam='0', ProyekMasuk='$proyekDatang', ProyekKeluar='$proyekPulang', TotalJamProyek='0', LemburMasuk='$datangLembur', LemburKeluar='$pulangLembur', TotalJamLembur='$totalJamKerjaLembur', CutiTahunan='$cutiTahunan', CutiSakit='$cutiSakit', CutiSpecial='$cutiSpecial', CutiAlpha='$alpha', CutiTugasKeluar='$tugas', TotalJamKerja='$totalJamKerja', Keterangan='$keterangan', KeteranganHoliday='$keteranganHoliday', Terlambat='$terlambat'");

                array_push($return, array("Tanggal" => $tanggalID, "Datang" => $datang, "Pulang" => $pulang, "ProyekDatang" => $proyekDatang, "ProyekPulang" => $proyekPulang, "CutiTahunan" => $cutiTahunan, "CutiSakit" => $cutiSakit, "CutiSpecial" => $cutiSpecial, "CutiAlpha" => $alpha, "CutiTugasKeluar" => $tugas, "TotalJamKerja" => $totalJamKerja, "IsHoliday" => $isHoliday, "KeteranganHoliday" => $keteranganHoliday, "Format" => $cek->format("D"), "DatangLembur" => $datangLembur, "PulangLembur" => $pulangLembur, "TotalJamKerjaLembur" => $totalJamKerjaLembur, "Terlambat" => $terlambat, "Keterangan" => $keterangan));
            }
            return $return;
        }
    }

    function generateAbsentBulananKaryawanHarian($tahun, $bulan, $karyawan, $useSunday = false)
    {
        if ($karyawan != "") {
            $db = $this->db;
            $holiday = $this->holiday;
            $return = array();

            $bulan2 = intval($bulan) - 1;
            if ($bulan2 < 10) $bulan2 = "0" . $bulan2;

            if ($bulan == "01") {
                $tahun2 = $tahun - 1;
                $bulan2 = "12";
            } else {
                $tahun2 = $tahun;
            }

            $db->query("DELETE FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='$karyawan' AND (Tanggal BETWEEN '$tahun2-$bulan2-26' AND '$tahun-$bulan-25')");

            // Absent Tanggal 26 Bulan Lalu
            for ($i = 26; $i <= cal_days_in_month(CAL_GREGORIAN, $bulan2, $tahun2); $i++) {
                if ($i < 10) $tgl = "0" . $i;
                else $tgl = $i;
                $tanggal = $tahun2 . "-" . $bulan2 . "-" . $tgl;
                $tanggalID = $tgl . "/" . $bulan2 . "/" . $tahun2;
                $isHoliday = false;
                $keteranganHoliday = "";

                $cek  = new DateTime($tanggal);
                if ($cek->format("D") == "Sun" && $useSunday) {
                    $isHoliday = true;
                    $keteranganHoliday = "Hari Minggu";
                }

                if (in_array($tanggal, $holiday)) {
                    $isHoliday = true;
                    $cekHoliday = $db->get_row("SELECT * FROM tb_public_holiday WHERE '$tanggal' BETWEEN DariTanggal AND SampaiTanggal");
                    if ($keteranganHoliday == "Hari Minggu")
                        $keteranganHoliday .= " / " . $cekHoliday->NamaHariLibur;
                    else
                        $keteranganHoliday = $cekHoliday->NamaHariLibur;
                }

                $datang = $db->get_var("SELECT DATE_FORMAT(DateTimeAbsent,'%H:%i') FROM tb_absent WHERE IDKaryawan='$karyawan' AND Tipe='I' AND DATE_FORMAT(DateTimeAbsent,'%Y-%m-%d')='$tanggal' ORDER BY DateTimeAbsent ASC");
                if (!$datang) $datang = "-";

                $pulang = $db->get_var("SELECT DATE_FORMAT(DateTimeAbsent,'%H:%i') FROM tb_absent WHERE IDKaryawan='$karyawan' AND Tipe='O' AND DATE_FORMAT(DateTimeAbsent,'%Y-%m-%d')='$tanggal' ORDER BY  DateTimeAbsent ASC");
                if (!$pulang) $pulang = "-";

                $proyekDatang = $db->get_var("SELECT DATE_FORMAT(`Datetime`,'%H:%i') FROM tb_proyek_absent WHERE DATE_FORMAT(`Datetime`,'%Y-%m-%d')='$tanggal' AND Jenis='1' AND IDKaryawan='$karyawan' ORDER BY `Datetime` ASC");
                if (!$proyekDatang || $proyekDatang == "00:00") $proyekDatang = "-";

                $proyekPulang = $db->get_var("SELECT DATE_FORMAT(`Datetime`,'%H:%i') FROM tb_proyek_absent WHERE DATE_FORMAT(`Datetime`,'%Y-%m-%d')='$tanggal' AND Jenis='2' AND IDKaryawan='$karyawan' ORDER BY `Datetime` ASC");
                if (!$proyekPulang || $proyekPulang == "00:00") $proyekPulang = "-";

                $hitungGapok = $db->get_var("SELECT HitungGapok FROM tb_proyek_absent WHERE DATE_FORMAT(`Datetime`,'%Y-%m-%d')='$tanggal' AND Jenis='1' AND IDKaryawan='$karyawan' ORDER BY `Datetime` ASC");
                if ($hitungGapok == "1") $hitungGapok = true;
                else $hitungGapok = false;

                if ($datang == "-" && $pulang == "-" && $proyekDatang == "-" && $proyekPulang == "-") $totalJamKerja = 0;
                else if ($datang != "-" && $pulang == "-" && $proyekDatang == "-" && $proyekPulang == "-") $totalJamKerja = 8;
                else if ($datang == "-" && $pulang != "-" && $proyekDatang == "-" && $proyekPulang == "-") $totalJamKerja = 8;
                else if ($datang == "-" && $pulang == "-" && $proyekDatang != "-" && $proyekPulang == "-") $totalJamKerja = 8;
                else if ($datang == "-" && $pulang == "-" && $proyekDatang == "-" && $proyekPulang != "-") $totalJamKerja = 8;
                else if ($datang != "-" && $pulang != "-" && $proyekDatang == "-" && $proyekPulang == "-") {
                    $totalJamKerja = ceil((strtotime($pulang) - strtotime($datang)) / 3600);
                } else if ($datang == "-" && $pulang == "-" && $proyekDatang != "-" && $proyekPulang != "-") {
                    $totalJamKerja = ceil((strtotime($proyekPulang) - strtotime($proyekDatang)) / 3600);
                } else if ($datang != "-" && $pulang == "-" && $proyekDatang == "-" && $proyekPulang != "-") {
                    $totalJamKerja = ceil((strtotime($proyekPulang) - strtotime($datang)) / 3600);
                } else if ($datang == "-" && $pulang != "-" && $proyekDatang != "-" && $proyekPulang == "-") {
                    $totalJamKerja = ceil((strtotime($pulang) - strtotime($proyekDatang)) / 3600);
                } else if ($datang != "-" && $pulang != "-" && $proyekDatang != "-" && $proyekPulang != "-") {
                    $totalJamKerja = ceil((strtotime($pulang) - strtotime($datang)) / 3600);
                    $totalJamKerja = intval($totalJamKerja) + ceil((strtotime($proyekPulang) - strtotime($proyekDatang)) / 3600);
                }

                if ($proyekDatang != "-") {
                    if (strtotime($proyekDatang) < strtotime("10:00") && strtotime($proyekDatang) > strtotime($this->minimunWaktuKedatangan))
                        $terlambat = ceil((strtotime($proyekDatang) - strtotime($this->minimunWaktuKedatangan)) / 60);
                    else
                        $terlambat = "";
                } else
                    $terlambat = "";

                $datangLembur = $db->get_var("SELECT DATE_FORMAT(DateTimeAbsent,'%H:%i') FROM tb_absent_lembur WHERE IDKaryawan='$karyawan' AND Tipe='I' AND DATE_FORMAT(DateTimeAbsent,'%Y-%m-%d')='$tanggal' ORDER BY DateTimeAbsent ASC");
                if (!$datangLembur) $datangLembur = "-";

                $pulangLembur = $db->get_var("SELECT DATE_FORMAT(DateTimeAbsent,'%H:%i') FROM tb_absent_lembur WHERE IDKaryawan='$karyawan' AND Tipe='O' AND DATE_FORMAT(DateTimeAbsent,'%Y-%m-%d')='$tanggal' ORDER BY  DateTimeAbsent ASC");
                if (!$pulangLembur) $pulangLembur = "-";

                if ($datangLembur != "-" && $pulangLembur == "-") $totalJamKerjaLembur = 0;
                else if ($datangLembur == "-" && $pulangLembur != "-") $totalJamKerjaLembur = 0;
                else if ($datangLembur != "-" && $pulangLembur != "-") {
                    $pulangLemburStrToTime = strtotime($pulangLembur);
                    if (intval($pulangLembur) < 6) {
                        $pulangLemburStrToTime += (60 * 60 * 24);
                    }
                    $totalJamKerjaLembur = ceil(($pulangLemburStrToTime - strtotime($datangLembur)) / 3600);
                } else $totalJamKerjaLembur = 0;

                if ($isHoliday == true) {
                    $cutiTahunan = "";
                    $cutiSpecial = "";
                    $cutiSakit = "";
                    $alpha = "";
                    $tugas = "";
                } else {
                    $cutiTahunan = $db->get_var("SELECT SUM(JumlahHari) FROM tb_cuti WHERE Jenis='CUTI TAHUNAN' AND IDKaryawan='$karyawan' AND (DariTanggal='$tanggal' OR '$tanggal' BETWEEN DariTanggal AND SampaiTanggal) AND Status='2'");
                    if (!$cutiTahunan) $cutiTahunan = "";
                    else if ($cutiTahunan > 1) $cutiTahunan = 1;

                    $cutiSakit = $db->get_var("SELECT SUM(JumlahHari) FROM tb_cuti WHERE Jenis='SAKIT' AND IDKaryawan='$karyawan' AND (DariTanggal='$tanggal' OR '$tanggal' BETWEEN DariTanggal AND SampaiTanggal) AND Status='2'");
                    if (!$cutiSakit) $cutiSakit = "";
                    else if ($cutiSakit > 1) $cutiSakit = 1;

                    $cutiSpecial = $db->get_var("SELECT SUM(JumlahHari) FROM tb_cuti WHERE Jenis='CUTI SPECIAL' AND IDKaryawan='$karyawan' AND (DariTanggal='$tanggal' OR '$tanggal' BETWEEN DariTanggal AND SampaiTanggal) AND Status='2'");
                    if (!$cutiSpecial) $cutiSpecial = "";
                    else if ($cutiSpecial > 1) $cutiSpecial = 1;

                    $alpha = $db->get_var("SELECT SUM(JumlahHari) FROM tb_cuti WHERE Jenis='ALPHA' AND IDKaryawan='$karyawan' AND (DariTanggal='$tanggal' OR '$tanggal' BETWEEN DariTanggal AND SampaiTanggal)");
                    if (!$alpha) $alpha = "";
                    else if ($alpha > 1) $alpha = 1;

                    $tugas = $db->get_var("SELECT SUM(JumlahHari) FROM tb_cuti WHERE Jenis='TUGAS LUAR KANTOR' AND IDKaryawan='$karyawan' AND (DariTanggal='$tanggal' OR '$tanggal' BETWEEN DariTanggal AND SampaiTanggal)");
                    if (!$tugas) $tugas = "";
                    else if ($tugas > 1) $tugas = 1;

                    if ($cutiTahunan >= 1 || $cutiSakit >= 1 || $cutiSpecial >= 1 || $alpha >= 1 || $tugas >= 1) {
                        $totalJamKerja = 0;
                        $totalJamKerjaLembur = 0;
                    }
                }

                if ($totalJamKerja > 0) $totalJamKerja = $totalJamKerja - 1;
                //if($totalJamKerjaLembur>0) $totalJamKerjaLembur = $totalJamKerjaLembur - 1;
                if ($totalJamKerjaLembur > 0) $totalJamKerjaLembur = $totalJamKerjaLembur;

                $keterangan = $db->get_var("SELECT Keterangan FROM tb_proyek_absent WHERE IDKaryawan='$karyawan' AND DATE_FORMAT(Datetime,'%Y-%m-%d')='$tanggal' ORDER BY Datetime ASC");
                $idProyek = $db->get_var("SELECT IDProyek FROM tb_proyek_absent WHERE IDKaryawan='$karyawan' AND DATE_FORMAT(Datetime,'%Y-%m-%d')='$tanggal' ORDER BY Datetime ASC");
                if (!$idProyek) {
                    $idProyek = $db->get_var("SELECT IDProyek FROM tb_karyawan WHERE IDKaryawan='$karyawan'");
                }

                $proyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='$idProyek'");
                $proyek = $proyek->Tahun . "/" . $proyek->KodeProyek;

                $db->query("INSERT INTO tb_absent_karyawan_bulanan SET Tanggal='$tanggal', IDKaryawan='$karyawan', JamMasuk='$datang', JamKeluar='$pulang', TotalJam='0', ProyekMasuk='$proyekDatang', ProyekKeluar='$proyekPulang', TotalJamProyek='0', LemburMasuk='$datangLembur', LemburKeluar='$pulangLembur', TotalJamLembur='$totalJamKerjaLembur', CutiTahunan='$cutiTahunan', CutiSakit='$cutiSakit', CutiSpecial='$cutiSpecial', CutiAlpha='$alpha', CutiTugasKeluar='$tugas', TotalJamKerja='$totalJamKerja', Keterangan='$keterangan', IDProyek='$idProyek', KeteranganHoliday='$keteranganHoliday', Terlambat='$terlambat', HitungGapok='$hitungGapok'");

                if ($cutiTahunan > 0 || $cutiSakit > 0 || $cutiSpecial > 0 || $alpha > 0 || $tugas > 0) {
                    $isHoliday = true;
                    $keteranganCuti = $db->get_var("SELECT Keterangan FROM tb_cuti WHERE IDKaryawan='$karyawan' AND (DariTanggal='$tanggal' OR '$tanggal' BETWEEN DariTanggal AND SampaiTanggal) AND Status='2'");
                    if ($cutiTahunan > 0) $keteranganHoliday = "Cuti Tahunan " . $keteranganCuti;
                    else if ($cutiSakit > 0) $keteranganHoliday = "Sakit" . $keteranganCuti;
                    else if ($cutiSpecial > 0) $keteranganHoliday = "Cuti Special" . $keteranganCuti;
                    else if ($alpha > 0) $keteranganHoliday = "Alpha" . $keteranganCuti;
                    else if ($tugas > 0) $keteranganHoliday = "Tugas Keluar Kantor" . $keteranganCuti;
                }

                if ($hitungGapok) {
                    $keterangan = "[Hitung Gapok] " . $keterangan;
                }

                array_push($return, array("Tanggal" => $tanggalID, "Datang" => $datang, "Pulang" => $pulang, "ProyekDatang" => $proyekDatang, "ProyekPulang" => $proyekPulang, "CutiTahunan" => $cutiTahunan, "CutiSakit" => $cutiSakit, "CutiSpecial" => $cutiSpecial, "CutiAlpha" => $alpha, "CutiTugasKeluar" => $tugas, "TotalJamKerja" => $totalJamKerja, "IsHoliday" => $isHoliday, "KeteranganHoliday" => $keteranganHoliday, "Format" => $cek->format("D"), "DatangLembur" => $datangLembur, "PulangLembur" => $pulangLembur, "TotalJamKerjaLembur" => $totalJamKerjaLembur, "Terlambat" => $terlambat, "Keterangan" => $keterangan, "IDProyek" => $idProyek, "Proyek" => $proyek, "HitungGapok" => $hitungGapok));
            }

            // Absent Bulan Sekarang 01-25
            for ($i = 1; $i <= 25; $i++) {
                if ($i < 10) $tgl = "0" . $i;
                else $tgl = $i;
                $tanggal = $tahun . "-" . $bulan . "-" . $tgl;
                $tanggalID = $tgl . "/" . $bulan . "/" . $tahun;
                $isHoliday = false;
                $keteranganHoliday = "";

                $cek  = new DateTime($tanggal);
                if ($cek->format("D") == "Sun" && $useSunday) {
                    $isHoliday = true;
                    $keteranganHoliday = "Hari Minggu";
                }

                if (in_array($tanggal, $holiday)) {
                    $isHoliday = true;
                    $cekHoliday = $db->get_row("SELECT * FROM tb_public_holiday WHERE '$tanggal' BETWEEN DariTanggal AND SampaiTanggal");
                    if ($keteranganHoliday == "Hari Minggu")
                        $keteranganHoliday .= " / " . $cekHoliday->NamaHariLibur;
                    else
                        $keteranganHoliday = $cekHoliday->NamaHariLibur;
                }

                $datang = $db->get_var("SELECT DATE_FORMAT(DateTimeAbsent,'%H:%i') FROM tb_absent WHERE IDKaryawan='$karyawan' AND Tipe='I' AND DATE_FORMAT(DateTimeAbsent,'%Y-%m-%d')='$tanggal' ORDER BY DateTimeAbsent ASC");
                if (!$datang) $datang = "-";

                $pulang = $db->get_var("SELECT DATE_FORMAT(DateTimeAbsent,'%H:%i') FROM tb_absent WHERE IDKaryawan='$karyawan' AND Tipe='O' AND DATE_FORMAT(DateTimeAbsent,'%Y-%m-%d')='$tanggal' ORDER BY  DateTimeAbsent ASC");
                if (!$pulang) $pulang = "-";

                $proyekDatang = $db->get_var("SELECT DATE_FORMAT(`Datetime`,'%H:%i') FROM tb_proyek_absent WHERE DATE_FORMAT(`Datetime`,'%Y-%m-%d')='$tanggal' AND Jenis='1' AND IDKaryawan='$karyawan' ORDER BY `Datetime` ASC");
                if (!$proyekDatang || $proyekDatang == "00:00") $proyekDatang = "-";

                $proyekPulang = $db->get_var("SELECT DATE_FORMAT(`Datetime`,'%H:%i') FROM tb_proyek_absent WHERE DATE_FORMAT(`Datetime`,'%Y-%m-%d')='$tanggal' AND Jenis='2' AND IDKaryawan='$karyawan' ORDER BY `Datetime` ASC");
                if (!$proyekPulang || $proyekPulang == "00:00") $proyekPulang = "-";

                $hitungGapok = $db->get_var("SELECT HitungGapok FROM tb_proyek_absent WHERE DATE_FORMAT(`Datetime`,'%Y-%m-%d')='$tanggal' AND Jenis='1' AND IDKaryawan='$karyawan' ORDER BY `Datetime` ASC");
                if ($hitungGapok == "1") $hitungGapok = true;
                else $hitungGapok = false;

                if ($datang == "-" && $pulang == "-" && $proyekDatang == "-" && $proyekPulang == "-") $totalJamKerja = 0;
                else if ($datang != "-" && $pulang == "-" && $proyekDatang == "-" && $proyekPulang == "-") $totalJamKerja = 8;
                else if ($datang == "-" && $pulang != "-" && $proyekDatang == "-" && $proyekPulang == "-") $totalJamKerja = 8;
                else if ($datang == "-" && $pulang == "-" && $proyekDatang != "-" && $proyekPulang == "-") $totalJamKerja = 8;
                else if ($datang == "-" && $pulang == "-" && $proyekDatang == "-" && $proyekPulang != "-") $totalJamKerja = 8;
                else if ($datang != "-" && $pulang != "-" && $proyekDatang == "-" && $proyekPulang == "-") {
                    $totalJamKerja = ceil((strtotime($pulang) - strtotime($datang)) / 3600);
                } else if ($datang == "-" && $pulang == "-" && $proyekDatang != "-" && $proyekPulang != "-") {
                    $totalJamKerja = ceil((strtotime($proyekPulang) - strtotime($proyekDatang)) / 3600);
                } else if ($datang != "-" && $pulang == "-" && $proyekDatang == "-" && $proyekPulang != "-") {
                    $totalJamKerja = ceil((strtotime($proyekPulang) - strtotime($datang)) / 3600);
                } else if ($datang == "-" && $pulang != "-" && $proyekDatang != "-" && $proyekPulang == "-") {
                    $totalJamKerja = ceil((strtotime($pulang) - strtotime($proyekDatang)) / 3600);
                } else if ($datang != "-" && $pulang != "-" && $proyekDatang != "-" && $proyekPulang != "-") {
                    $totalJamKerja = ceil((strtotime($pulang) - strtotime($datang)) / 3600);
                    $totalJamKerja = intval($totalJamKerja) + ceil((strtotime($proyekPulang) - strtotime($proyekDatang)) / 3600);
                }

                if ($proyekDatang != "-") {
                    if (strtotime($proyekDatang) < strtotime("10:00") && strtotime($proyekDatang) > strtotime($this->minimunWaktuKedatangan))
                        $terlambat = ceil((strtotime($proyekDatang) - strtotime($this->minimunWaktuKedatangan)) / 60);
                    else
                        $terlambat = "";
                } else
                    $terlambat = "";

                $datangLembur = $db->get_var("SELECT DATE_FORMAT(DateTimeAbsent,'%H:%i') FROM tb_absent_lembur WHERE IDKaryawan='$karyawan' AND Tipe='I' AND DATE_FORMAT(DateTimeAbsent,'%Y-%m-%d')='$tanggal' ORDER BY DateTimeAbsent ASC");
                if (!$datangLembur) $datangLembur = "-";

                $pulangLembur = $db->get_var("SELECT DATE_FORMAT(DateTimeAbsent,'%H:%i') FROM tb_absent_lembur WHERE IDKaryawan='$karyawan' AND Tipe='O' AND DATE_FORMAT(DateTimeAbsent,'%Y-%m-%d')='$tanggal' ORDER BY  DateTimeAbsent ASC");
                if (!$pulangLembur) $pulangLembur = "-";

                if ($datangLembur != "-" && $pulangLembur == "-") $totalJamKerjaLembur = 4;
                else if ($datangLembur == "-" && $pulangLembur != "-") $totalJamKerjaLembur = 4;
                else if ($datangLembur != "-" && $pulangLembur != "-") {
                    $pulangLemburStrToTime = strtotime($pulangLembur);
                    if (intval($pulangLembur) < 6) {
                        $pulangLemburStrToTime += (60 * 60 * 24);
                    }
                    $totalJamKerjaLembur = ceil(($pulangLemburStrToTime - strtotime($datangLembur)) / 3600);
                } else $totalJamKerjaLembur = 0;

                if ($isHoliday == true) {
                    $cutiTahunan = "";
                    $cutiSpecial = "";
                    $cutiSakit = "";
                    $alpha = "";
                    $tugas = "";
                } else {
                    $cutiTahunan = $db->get_var("SELECT SUM(JumlahHari) FROM tb_cuti WHERE Jenis='CUTI TAHUNAN' AND IDKaryawan='$karyawan' AND (DariTanggal='$tanggal' OR '$tanggal' BETWEEN DariTanggal AND SampaiTanggal) AND Status='2'");
                    if (!$cutiTahunan) $cutiTahunan = "";
                    else if ($cutiTahunan > 1) $cutiTahunan = 1;

                    $cutiSakit = $db->get_var("SELECT SUM(JumlahHari) FROM tb_cuti WHERE Jenis='SAKIT' AND IDKaryawan='$karyawan' AND (DariTanggal='$tanggal' OR '$tanggal' BETWEEN DariTanggal AND SampaiTanggal) AND Status='2'");
                    if (!$cutiSakit) $cutiSakit = "";
                    else if ($cutiSakit > 1) $cutiSakit = 1;

                    $cutiSpecial = $db->get_var("SELECT SUM(JumlahHari) FROM tb_cuti WHERE Jenis='CUTI SPECIAL' AND IDKaryawan='$karyawan' AND (DariTanggal='$tanggal' OR '$tanggal' BETWEEN DariTanggal AND SampaiTanggal) AND Status='2'");
                    if (!$cutiSpecial) $cutiSpecial = "";
                    else if ($cutiSpecial > 1) $cutiSpecial = 1;

                    $alpha = $db->get_var("SELECT SUM(JumlahHari) FROM tb_cuti WHERE Jenis='ALPHA' AND IDKaryawan='$karyawan' AND (DariTanggal='$tanggal' OR '$tanggal' BETWEEN DariTanggal AND SampaiTanggal)");
                    if (!$alpha) $alpha = "";
                    else if ($alpha > 1) $alpha = 1;

                    $tugas = $db->get_var("SELECT SUM(JumlahHari) FROM tb_cuti WHERE Jenis='TUGAS LUAR KANTOR' AND IDKaryawan='$karyawan' AND (DariTanggal='$tanggal' OR '$tanggal' BETWEEN DariTanggal AND SampaiTanggal)");
                    if (!$tugas) $tugas = "";
                    else if ($tugas > 1) $tugas = 1;

                    if ($cutiTahunan >= 1 || $cutiSakit >= 1 || $cutiSpecial >= 1 || $alpha >= 1 || $tugas >= 1) {
                        $totalJamKerja = 0;
                        $totalJamKerjaLembur = 0;
                    }
                }

                if ($totalJamKerja > 0) $totalJamKerja = $totalJamKerja - 1;
                //if($totalJamKerjaLembur>0) $totalJamKerjaLembur = $totalJamKerjaLembur - 1;

                $keterangan = $db->get_var("SELECT Keterangan FROM tb_proyek_absent WHERE IDKaryawan='$karyawan' AND DATE_FORMAT(Datetime,'%Y-%m-%d')='$tanggal' ORDER BY Datetime ASC");
                $idProyek = $db->get_var("SELECT IDProyek FROM tb_proyek_absent WHERE IDKaryawan='$karyawan' AND DATE_FORMAT(Datetime,'%Y-%m-%d')='$tanggal' ORDER BY Datetime ASC");
                if (!$idProyek) {
                    $idProyek = $db->get_var("SELECT IDProyek FROM tb_karyawan WHERE IDKaryawan='$karyawan'");
                }

                $proyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='$idProyek'");
                $proyek = $proyek->Tahun . "/" . $proyek->KodeProyek;

                $db->query("INSERT INTO tb_absent_karyawan_bulanan SET Tanggal='$tanggal', IDKaryawan='$karyawan', JamMasuk='$datang', JamKeluar='$pulang', TotalJam='0', ProyekMasuk='$proyekDatang', ProyekKeluar='$proyekPulang', TotalJamProyek='0', LemburMasuk='$datangLembur', LemburKeluar='$pulangLembur', TotalJamLembur='$totalJamKerjaLembur', CutiTahunan='$cutiTahunan', CutiSakit='$cutiSakit', CutiSpecial='$cutiSpecial', CutiAlpha='$alpha', CutiTugasKeluar='$tugas', TotalJamKerja='$totalJamKerja', Keterangan='$keterangan', IDProyek='$idProyek', KeteranganHoliday='$keteranganHoliday', Terlambat='$terlambat', HitungGapok='$hitungGapok'");

                if ($cutiTahunan > 0 || $cutiSakit > 0 || $cutiSpecial > 0 || $alpha > 0 || $tugas > 0) {
                    $isHoliday = true;
                    $keteranganCuti = $db->get_var("SELECT Keterangan FROM tb_cuti WHERE IDKaryawan='$karyawan' AND (DariTanggal='$tanggal' OR '$tanggal' BETWEEN DariTanggal AND SampaiTanggal) AND Status='2'");
                    if ($cutiTahunan > 0) $keteranganHoliday = "Cuti Tahunan " . $keteranganCuti;
                    else if ($cutiSakit > 0) $keteranganHoliday = "Sakit" . $keteranganCuti;
                    else if ($cutiSpecial > 0) $keteranganHoliday = "Cuti Special" . $keteranganCuti;
                    else if ($alpha > 0) $keteranganHoliday = "Alpha" . $keteranganCuti;
                    else if ($tugas > 0) $keteranganHoliday = "Tugas Keluar Kantor" . $keteranganCuti;
                }

                if ($hitungGapok) {
                    $keterangan = "[Hitung Gapok] " . $keterangan;
                }

                array_push($return, array("Tanggal" => $tanggalID, "Datang" => $datang, "Pulang" => $pulang, "ProyekDatang" => $proyekDatang, "ProyekPulang" => $proyekPulang, "CutiTahunan" => $cutiTahunan, "CutiSakit" => $cutiSakit, "CutiSpecial" => $cutiSpecial, "CutiAlpha" => $alpha, "CutiTugasKeluar" => $tugas, "TotalJamKerja" => $totalJamKerja, "IsHoliday" => $isHoliday, "KeteranganHoliday" => $keteranganHoliday, "Format" => $cek->format("D"), "DatangLembur" => $datangLembur, "PulangLembur" => $pulangLembur, "TotalJamKerjaLembur" => $totalJamKerjaLembur, "Terlambat" => $terlambat, "Keterangan" => $keterangan, "IDProyek" => $idProyek, "Proyek" => $proyek, "HitungGapok" => $hitungGapok));
            }
            return $return;
        }
    }

    function generateAbsentPeriodeKaryawanHarian($start, $end, $karyawan, $useSunday = false)
    {
        if ($karyawan != "") {
            $db = $this->db;
            $holiday = $this->holiday;
            $return = array();

            $db->query("DELETE FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='$karyawan' AND (Tanggal BETWEEN '$start' AND '$end')");

            $begin = new DateTime($start);
            $end   = new DateTime($end);

            for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
                $tanggal = $i->format("Y-m-d");
                $tanggalID = $i->format("d/m/Y");

                $isHoliday = false;
                $keteranganHoliday = "";

                $cek  = new DateTime($tanggal);
                if ($cek->format("D") == "Sun" && $useSunday) {
                    $isHoliday = true;
                    $keteranganHoliday = "Hari Minggu";
                }

                if (in_array($tanggal, $holiday)) {
                    $isHoliday = true;
                    $cekHoliday = $db->get_row("SELECT * FROM tb_public_holiday WHERE '$tanggal' BETWEEN DariTanggal AND SampaiTanggal");
                    if ($keteranganHoliday == "Hari Minggu")
                        $keteranganHoliday .= " / " . $cekHoliday->NamaHariLibur;
                    else
                        $keteranganHoliday = $cekHoliday->NamaHariLibur;
                }

                $datang = $db->get_var("SELECT DATE_FORMAT(DateTimeAbsent,'%H:%i') FROM tb_absent WHERE IDKaryawan='$karyawan' AND Tipe='I' AND DATE_FORMAT(DateTimeAbsent,'%Y-%m-%d')='$tanggal' ORDER BY DateTimeAbsent ASC");
                if (!$datang) $datang = "-";

                $pulang = $db->get_var("SELECT DATE_FORMAT(DateTimeAbsent,'%H:%i') FROM tb_absent WHERE IDKaryawan='$karyawan' AND Tipe='O' AND DATE_FORMAT(DateTimeAbsent,'%Y-%m-%d')='$tanggal' ORDER BY  DateTimeAbsent ASC");
                if (!$pulang) $pulang = "-";

                $proyekDatang = $db->get_var("SELECT DATE_FORMAT(`Datetime`,'%H:%i') FROM tb_proyek_absent WHERE DATE_FORMAT(`Datetime`,'%Y-%m-%d')='$tanggal' AND Jenis='1' AND IDKaryawan='$karyawan' ORDER BY `Datetime` ASC");
                if (!$proyekDatang || $proyekDatang == "00:00") $proyekDatang = "-";

                $proyekPulang = $db->get_var("SELECT DATE_FORMAT(`Datetime`,'%H:%i') FROM tb_proyek_absent WHERE DATE_FORMAT(`Datetime`,'%Y-%m-%d')='$tanggal' AND Jenis='2' AND IDKaryawan='$karyawan' ORDER BY `Datetime` ASC");
                if (!$proyekPulang || $proyekPulang == "00:00") $proyekPulang = "-";

                $hitungGapok = $db->get_var("SELECT HitungGapok FROM tb_proyek_absent WHERE DATE_FORMAT(`Datetime`,'%Y-%m-%d')='$tanggal' AND Jenis='1' AND IDKaryawan='$karyawan' ORDER BY `Datetime` ASC");
                if ($hitungGapok == "1") $hitungGapok = true;
                else $hitungGapok = false;

                if ($datang == "-" && $pulang == "-" && $proyekDatang == "-" && $proyekPulang == "-") $totalJamKerja = 0;
                else if ($datang != "-" && $pulang == "-" && $proyekDatang == "-" && $proyekPulang == "-") $totalJamKerja = 8;
                else if ($datang == "-" && $pulang != "-" && $proyekDatang == "-" && $proyekPulang == "-") $totalJamKerja = 8;
                else if ($datang == "-" && $pulang == "-" && $proyekDatang != "-" && $proyekPulang == "-") $totalJamKerja = 8;
                else if ($datang == "-" && $pulang == "-" && $proyekDatang == "-" && $proyekPulang != "-") $totalJamKerja = 8;
                else if ($datang != "-" && $pulang != "-" && $proyekDatang == "-" && $proyekPulang == "-") {
                    $totalJamKerja = ceil((strtotime($pulang) - strtotime($datang)) / 3600);
                } else if ($datang == "-" && $pulang == "-" && $proyekDatang != "-" && $proyekPulang != "-") {
                    $totalJamKerja = ceil((strtotime($proyekPulang) - strtotime($proyekDatang)) / 3600);
                } else if ($datang != "-" && $pulang == "-" && $proyekDatang == "-" && $proyekPulang != "-") {
                    $totalJamKerja = ceil((strtotime($proyekPulang) - strtotime($datang)) / 3600);
                } else if ($datang == "-" && $pulang != "-" && $proyekDatang != "-" && $proyekPulang == "-") {
                    $totalJamKerja = ceil((strtotime($pulang) - strtotime($proyekDatang)) / 3600);
                } else if ($datang != "-" && $pulang != "-" && $proyekDatang != "-" && $proyekPulang != "-") {
                    $totalJamKerja = ceil((strtotime($pulang) - strtotime($datang)) / 3600);
                    $totalJamKerja = intval($totalJamKerja) + ceil((strtotime($proyekPulang) - strtotime($proyekDatang)) / 3600);
                }

                if ($proyekDatang != "-") {
                    if (strtotime($proyekDatang) < strtotime("10:00") && strtotime($proyekDatang) > strtotime($this->minimunWaktuKedatangan))
                        $terlambat = ceil((strtotime($proyekDatang) - strtotime($this->minimunWaktuKedatangan)) / 60);
                    else
                        $terlambat = "";
                } else
                    $terlambat = "";

                $datangLembur = $db->get_var("SELECT DATE_FORMAT(DateTimeAbsent,'%H:%i') FROM tb_absent_lembur WHERE IDKaryawan='$karyawan' AND Tipe='I' AND DATE_FORMAT(DateTimeAbsent,'%Y-%m-%d')='$tanggal' ORDER BY DateTimeAbsent ASC");
                if (!$datangLembur) $datangLembur = "-";

                $pulangLembur = $db->get_var("SELECT DATE_FORMAT(DateTimeAbsent,'%H:%i') FROM tb_absent_lembur WHERE IDKaryawan='$karyawan' AND Tipe='O' AND DATE_FORMAT(DateTimeAbsent,'%Y-%m-%d')='$tanggal' ORDER BY  DateTimeAbsent ASC");
                if (!$pulangLembur) $pulangLembur = "-";

                if ($datangLembur != "-" && $pulangLembur == "-") $totalJamKerjaLembur = 0;
                else if ($datangLembur == "-" && $pulangLembur != "-") $totalJamKerjaLembur = 0;
                else if ($datangLembur != "-" && $pulangLembur != "-") {
                    $pulangLemburStrToTime = strtotime($pulangLembur);
                    if (intval($pulangLembur) < 6) {
                        $pulangLemburStrToTime += (60 * 60 * 24);
                    }
                    $totalJamKerjaLembur = ceil(($pulangLemburStrToTime - strtotime($datangLembur)) / 3600);
                } else $totalJamKerjaLembur = 0;

                if ($isHoliday == true) {
                    $cutiTahunan = "";
                    $cutiSpecial = "";
                    $cutiSakit = "";
                    $alpha = "";
                    $tugas = "";
                } else {
                    $cutiTahunan = $db->get_var("SELECT SUM(JumlahHari) FROM tb_cuti WHERE Jenis='CUTI TAHUNAN' AND IDKaryawan='$karyawan' AND (DariTanggal='$tanggal' OR '$tanggal' BETWEEN DariTanggal AND SampaiTanggal) AND Status='2'");
                    if (!$cutiTahunan) $cutiTahunan = "";
                    else if ($cutiTahunan > 1) $cutiTahunan = 1;

                    $cutiSakit = $db->get_var("SELECT SUM(JumlahHari) FROM tb_cuti WHERE Jenis='SAKIT' AND IDKaryawan='$karyawan' AND (DariTanggal='$tanggal' OR '$tanggal' BETWEEN DariTanggal AND SampaiTanggal) AND Status='2'");
                    if (!$cutiSakit) $cutiSakit = "";
                    else if ($cutiSakit > 1) $cutiSakit = 1;

                    $cutiSpecial = $db->get_var("SELECT SUM(JumlahHari) FROM tb_cuti WHERE Jenis='CUTI SPECIAL' AND IDKaryawan='$karyawan' AND (DariTanggal='$tanggal' OR '$tanggal' BETWEEN DariTanggal AND SampaiTanggal) AND Status='2'");
                    if (!$cutiSpecial) $cutiSpecial = "";
                    else if ($cutiSpecial > 1) $cutiSpecial = 1;

                    $alpha = $db->get_var("SELECT SUM(JumlahHari) FROM tb_cuti WHERE Jenis='ALPHA' AND IDKaryawan='$karyawan' AND (DariTanggal='$tanggal' OR '$tanggal' BETWEEN DariTanggal AND SampaiTanggal)");
                    if (!$alpha) $alpha = "";
                    else if ($alpha > 1) $alpha = 1;

                    $tugas = $db->get_var("SELECT SUM(JumlahHari) FROM tb_cuti WHERE Jenis='TUGAS LUAR KANTOR' AND IDKaryawan='$karyawan' AND (DariTanggal='$tanggal' OR '$tanggal' BETWEEN DariTanggal AND SampaiTanggal)");
                    if (!$tugas) $tugas = "";
                    else if ($tugas > 1) $tugas = 1;

                    if ($cutiTahunan >= 1 || $cutiSakit >= 1 || $cutiSpecial >= 1 || $alpha >= 1 || $tugas >= 1) {
                        $totalJamKerja = 0;
                        $totalJamKerjaLembur = 0;
                    }
                }

                if ($totalJamKerja > 0) $totalJamKerja = $totalJamKerja - 1;
                //if($totalJamKerjaLembur>0) $totalJamKerjaLembur = $totalJamKerjaLembur - 1;
                if ($totalJamKerjaLembur > 0) $totalJamKerjaLembur = $totalJamKerjaLembur;

                $keterangan = $db->get_var("SELECT Keterangan FROM tb_proyek_absent WHERE IDKaryawan='$karyawan' AND DATE_FORMAT(Datetime,'%Y-%m-%d')='$tanggal' ORDER BY Datetime ASC");
                $idProyek = $db->get_var("SELECT IDProyek FROM tb_proyek_absent WHERE IDKaryawan='$karyawan' AND DATE_FORMAT(Datetime,'%Y-%m-%d')='$tanggal' ORDER BY Datetime ASC");
                if (!$idProyek) {
                    $idProyek = $db->get_var("SELECT IDProyek FROM tb_karyawan WHERE IDKaryawan='$karyawan'");
                }

                $proyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='$idProyek'");
                $proyek = $proyek->Tahun . "/" . $proyek->KodeProyek;

                $db->query("INSERT INTO tb_absent_karyawan_bulanan SET Tanggal='$tanggal', IDKaryawan='$karyawan', JamMasuk='$datang', JamKeluar='$pulang', TotalJam='0', ProyekMasuk='$proyekDatang', ProyekKeluar='$proyekPulang', TotalJamProyek='0', LemburMasuk='$datangLembur', LemburKeluar='$pulangLembur', TotalJamLembur='$totalJamKerjaLembur', CutiTahunan='$cutiTahunan', CutiSakit='$cutiSakit', CutiSpecial='$cutiSpecial', CutiAlpha='$alpha', CutiTugasKeluar='$tugas', TotalJamKerja='$totalJamKerja', Keterangan='$keterangan', IDProyek='$idProyek', KeteranganHoliday='$keteranganHoliday', Terlambat='$terlambat', HitungGapok='$hitungGapok'");

                if ($cutiTahunan > 0 || $cutiSakit > 0 || $cutiSpecial > 0 || $alpha > 0 || $tugas > 0) {
                    $isHoliday = true;
                    $keteranganCuti = $db->get_var("SELECT Keterangan FROM tb_cuti WHERE IDKaryawan='$karyawan' AND (DariTanggal='$tanggal' OR '$tanggal' BETWEEN DariTanggal AND SampaiTanggal) AND Status='2'");
                    if ($cutiTahunan > 0) $keteranganHoliday = "Cuti Tahunan " . $keteranganCuti;
                    else if ($cutiSakit > 0) $keteranganHoliday = "Sakit" . $keteranganCuti;
                    else if ($cutiSpecial > 0) $keteranganHoliday = "Cuti Special" . $keteranganCuti;
                    else if ($alpha > 0) $keteranganHoliday = "Alpha" . $keteranganCuti;
                    else if ($tugas > 0) $keteranganHoliday = "Tugas Keluar Kantor" . $keteranganCuti;
                }

                if ($hitungGapok) {
                    $keterangan = "[Hitung Gapok] " . $keterangan;
                }

                array_push($return, array("Tanggal" => $tanggalID, "Datang" => $datang, "Pulang" => $pulang, "ProyekDatang" => $proyekDatang, "ProyekPulang" => $proyekPulang, "CutiTahunan" => $cutiTahunan, "CutiSakit" => $cutiSakit, "CutiSpecial" => $cutiSpecial, "CutiAlpha" => $alpha, "CutiTugasKeluar" => $tugas, "TotalJamKerja" => $totalJamKerja, "IsHoliday" => $isHoliday, "KeteranganHoliday" => $keteranganHoliday, "Format" => $cek->format("D"), "DatangLembur" => $datangLembur, "PulangLembur" => $pulangLembur, "TotalJamKerjaLembur" => $totalJamKerjaLembur, "Terlambat" => $terlambat, "Keterangan" => $keterangan, "IDProyek" => $idProyek, "Proyek" => $proyek, "HitungGapok" => $hitungGapok));
            }

            return $return;
        }
    }

    function getTotalTerlambat($tahun, $bulan, $karyawan)
    {
        $db = $this->db;

        $bulan2 = intval($bulan) - 1;
        if ($bulan2 < 10) $bulan2 = "0" . $bulan2;
        if ($bulan == "01") {
            $tahun2 = $tahun - 1;
            $bulan2 = "12";
        } else {
            $tahun2 = $tahun;
        }

        $var = $db->get_var("SELECT SUM(Terlambat) FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='$karyawan' AND Tanggal BETWEEN '$tahun2-$bulan2-26' AND '$tahun-$bulan-25'");
        if (!$var) $var = 0;
        return $var;
    }

    function getTotalTerlambatPeriode($start, $end, $karyawan)
    {
        $db = $this->db;

        $var = $db->get_var("SELECT SUM(Terlambat) FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='$karyawan' AND Tanggal BETWEEN '$start' AND '$end'");
        if (!$var) $var = 0;
        return $var;
    }

    function getTotalCutiTahunanBulanKaryawan($tahun, $bulan, $karyawan)
    {
        $db = $this->db;

        $bulan2 = intval($bulan) - 1;
        if ($bulan2 < 10) $bulan2 = "0" . $bulan2;
        if ($bulan == "01") {
            $tahun2 = $tahun - 1;
            $bulan2 = "12";
        } else {
            $tahun2 = $tahun;
        }

        $var = $db->get_var("SELECT SUM(CutiTahunan) FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='$karyawan' AND Tanggal BETWEEN '$tahun2-$bulan2-26' AND '$tahun-$bulan-25'");
        if (!$var) $var = 0;
        return $var;
    }

    function getTotalCutiTahunanPeriodeKaryawan($start, $end, $karyawan)
    {
        $db = $this->db;

        $var = $db->get_var("SELECT SUM(CutiTahunan) FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='$karyawan' AND Tanggal BETWEEN '$start' AND '$end'");
        if (!$var) $var = 0;
        return $var;
    }

    function getTotalCutiSetahunKaryawan($tahun, $bulan, $karyawan)
    {
        $db = $this->db;
        $var = $db->get_var("SELECT SUM(CutiTahunan) FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='$karyawan' AND DATE_FORMAT(Tanggal, '%Y-%m') <= '$tahun-$bulan' AND DATE_FORMAT(Tanggal, '%Y') >= '$tahun'");
        if (!$var) $var = 0;
        return $var;
    }

    function getTotalCutiSakitBulanKaryawan($tahun, $bulan, $karyawan)
    {
        $db = $this->db;

        $bulan2 = intval($bulan) - 1;
        if ($bulan2 < 10) $bulan2 = "0" . $bulan2;
        if ($bulan == "01") {
            $tahun2 = $tahun - 1;
            $bulan2 = "12";
        } else {
            $tahun2 = $tahun;
        }

        $var = $db->get_var("SELECT SUM(CutiSakit) FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='$karyawan' AND Tanggal BETWEEN '$tahun2-$bulan2-26' AND '$tahun-$bulan-25'");
        if (!$var) $var = 0;
        return $var;
    }

    function getTotalCutiSakitPeriodeKaryawan($start, $end, $karyawan)
    {
        $db = $this->db;

        $var = $db->get_var("SELECT SUM(CutiSakit) FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='$karyawan' AND Tanggal BETWEEN '$start' AND '$end'");
        if (!$var) $var = 0;
        return $var;
    }

    function getTotalCutiSpecialBulanKaryawan($tahun, $bulan, $karyawan)
    {
        $db = $this->db;

        $bulan2 = intval($bulan) - 1;
        if ($bulan2 < 10) $bulan2 = "0" . $bulan2;
        if ($bulan == "01") {
            $tahun2 = $tahun - 1;
            $bulan2 = "12";
        } else {
            $tahun2 = $tahun;
        }

        $var = $db->get_var("SELECT SUM(CutiSpecial) FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='$karyawan' AND Tanggal BETWEEN '$tahun2-$bulan2-26' AND '$tahun-$bulan-25'");
        if (!$var) $var = 0;
        return $var;
    }

    function getTotalCutiSpecialPeriodeKaryawan($start, $end, $karyawan)
    {
        $db = $this->db;

        $var = $db->get_var("SELECT SUM(CutiSpecial) FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='$karyawan' AND Tanggal BETWEEN '$start' AND '$end'");
        if (!$var) $var = 0;
        return $var;
    }

    function getTotalAlphaBulanKaryawan($tahun, $bulan, $karyawan)
    {
        $db = $this->db;

        $bulan2 = intval($bulan) - 1;
        if ($bulan2 < 10) $bulan2 = "0" . $bulan2;
        if ($bulan == "01") {
            $tahun2 = $tahun - 1;
            $bulan2 = "12";
        } else {
            $tahun2 = $tahun;
        }

        $var = $db->get_var("SELECT SUM(CutiAlpha) FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='$karyawan' AND Tanggal BETWEEN '$tahun2-$bulan2-26' AND '$tahun-$bulan-25'");
        if (!$var) $var = 0;
        return $var;
    }

    function getTotalAlphaPeriodeKaryawan($start, $end, $karyawan)
    {
        $db = $this->db;

        $var = $db->get_var("SELECT SUM(CutiAlpha) FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='$karyawan' AND Tanggal BETWEEN '$start' AND '$end'");
        if (!$var) $var = 0;
        return $var;
    }

    function getTotalTugasBulanKaryawan($tahun, $bulan, $karyawan)
    {
        $db = $this->db;

        $bulan2 = intval($bulan) - 1;
        if ($bulan2 < 10) $bulan2 = "0" . $bulan2;
        if ($bulan == "01") {
            $tahun2 = $tahun - 1;
            $bulan2 = "12";
        } else {
            $tahun2 = $tahun;
        }

        $var = $db->get_var("SELECT SUM(CutiTugasKeluar) FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='$karyawan' AND Tanggal BETWEEN '$tahun2-$bulan2-26' AND '$tahun-$bulan-25'");
        if (!$var) $var = 0;
        return $var;
    }


    function getTotalTugasPeriodeKaryawan($start, $end, $karyawan)
    {
        $db = $this->db;

        $var = $db->get_var("SELECT SUM(CutiTugasKeluar) FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='$karyawan' AND Tanggal BETWEEN '$start' AND '$end'");
        if (!$var) $var = 0;
        return $var;
    }

    function getTotalJamKerjaBulanKaryawan($tahun, $bulan, $karyawan)
    {
        $db = $this->db;

        $bulan2 = intval($bulan) - 1;
        if ($bulan2 < 10) $bulan2 = "0" . $bulan2;
        if ($bulan == "01") {
            $tahun2 = $tahun - 1;
            $bulan2 = "12";
        } else {
            $tahun2 = $tahun;
        }

        $var = $db->get_var("SELECT SUM(TotalJamKerja) FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='$karyawan' AND Tanggal BETWEEN '$tahun2-$bulan2-26' AND '$tahun-$bulan-25'");
        if (!$var) $var = 0;
        return $var;
    }

    function getTotalJamKerjaPeriodeKaryawan($start, $end, $karyawan)
    {
        $db = $this->db;

        $var = $db->get_var("SELECT SUM(TotalJamKerja) FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='$karyawan' AND Tanggal BETWEEN '$start' AND '$end'");
        if (!$var) $var = 0;
        return $var;
    }

    function getTotalJamLemburBulanKaryawan($tahun, $bulan, $karyawan)
    {
        $db = $this->db;

        $bulan2 = intval($bulan) - 1;
        if ($bulan2 < 10) $bulan2 = "0" . $bulan2;
        if ($bulan == "01") {
            $tahun2 = $tahun - 1;
            $bulan2 = "12";
        } else {
            $tahun2 = $tahun;
        }

        $var = $db->get_var("SELECT SUM(TotalJamLembur) FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='$karyawan' AND Tanggal BETWEEN '$tahun2-$bulan2-26' AND '$tahun-$bulan-25'");
        if (!$var) $var = 0;
        return $var;
    }

    function getTotalJamLemburPeriodeKaryawan($start, $end, $karyawan)
    {
        $db = $this->db;

        $var = $db->get_var("SELECT SUM(TotalJamLembur) FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='$karyawan' AND Tanggal BETWEEN '$start' AND '$end'");
        if (!$var) $var = 0;
        return $var;
    }

    function getTotalHariKerjaBulanKaryawan($tahun, $bulan, $karyawan)
    {
        $db = $this->db;

        $bulan2 = intval($bulan) - 1;
        if ($bulan2 < 10) $bulan2 = "0" . $bulan2;
        if ($bulan == "01") {
            $tahun2 = $tahun - 1;
            $bulan2 = "12";
        } else {
            $tahun2 = $tahun;
        }

        $var = $db->get_var("SELECT COUNT(*) FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='$karyawan' AND Tanggal BETWEEN '$tahun2-$bulan2-26' AND '$tahun-$bulan-25' AND TotalJamKerja>0");
        if (!$var) $var = 0;

        //kurangi cuti setengah hari
        $cset = $db->get_var("SELECT SUM(CutiTahunan) FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='$karyawan' AND Tanggal <= '$tahun-$bulan-25' AND Tanggal >= '$tahun' AND CutiTahunan>0  AND CutiTahunan<1");
        if (!$cset) $cset = 0;

        //ditambah tugas keluar kantor
        $ctugas = $db->get_var("SELECT SUM(CutiTugasKeluar) FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='$karyawan' AND Tanggal BETWEEN '$tahun2-$bulan2-26' AND '$tahun-$bulan-25'");
        if (!$ctugas) $ctugas = 0;

        return ($var - $cset + $ctugas);
    }

    function getTotalHariKerjaPeriodeKaryawan($start, $end, $karyawan)
    {
        $db = $this->db;

        $var = $db->get_var("SELECT COUNT(*) FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='$karyawan' AND Tanggal BETWEEN '$start' AND '$end' AND TotalJamKerja>0");
        if (!$var) $var = 0;

        //kurangi cuti setengah hari
        $cset = $db->get_var("SELECT SUM(CutiTahunan) FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='$karyawan' AND (Tanggal BETWEEN '$start' AND '$end') AND CutiTahunan>0  AND CutiTahunan<1");
        if (!$cset) $cset = 0;

        //ditambah tugas keluar kantor
        $ctugas = $db->get_var("SELECT SUM(CutiTugasKeluar) FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='$karyawan' AND Tanggal BETWEEN '$start' AND '$end'");
        if (!$ctugas) $ctugas = 0;

        return ($var - $cset + $ctugas);
    }

    function getTotalLemburHariBulanan($tahun, $bulan, $karyawan)
    {
        $db = $this->db;

        $bulan2 = intval($bulan) - 1;
        if ($bulan2 < 10) $bulan2 = "0" . $bulan2;
        if ($bulan == "01") {
            $tahun2 = $tahun - 1;
            $bulan2 = "12";
        } else {
            $tahun2 = $tahun;
        }

        $var = $db->get_var("SELECT COUNT(*) FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='$karyawan' AND Tanggal BETWEEN '$tahun2-$bulan2-26' AND '$tahun-$bulan-25' AND TotalJamLembur>'3'");
        if (!$var) $var = 0;
        return $var;
    }

    function getTotalLemburHariPeriode($start, $end, $karyawan)
    {
        $db = $this->db;

        $var = $db->get_var("SELECT COUNT(*) FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='$karyawan' AND Tanggal BETWEEN '$start' AND '$end' AND TotalJamLembur>'0'");
        if (!$var) $var = 0;
        return intval($var);
    }

    function getTotalUangMakanLembur($tahun, $bulan, $karyawan, $uang_makan_perhari)
    {
        $db = $this->db;

        $bulan2 = intval($bulan) - 1;
        if ($bulan2 < 10) $bulan2 = "0" . $bulan2;
        if ($bulan == "01") {
            $tahun2 = $tahun - 1;
            $bulan2 = "12";
        } else {
            $tahun2 = $tahun;
        }

        $uangmakanlembur = 0;

        $query = $db->get_results("SELECT * FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='$karyawan' AND Tanggal BETWEEN '$tahun2-$bulan2-26' AND '$tahun-$bulan-25' AND TotalJamLembur>'3'");
        if ($query) {
            foreach ($query as $data) {
                $tanggal = $data->Tanggal;
                $jamLembur = $data->TotalJamLembur;
                $cek  = new DateTime($tanggal);
                if ($cek->format("D") == "Sun" || (in_array($tanggal, $this->holiday))) {
                    if ($jamLembur > 8) {
                        $uangmakanlembur += $uang_makan_perhari;
                        $j = $jamLembur - 8;
                        $rest = floor($j / 4);
                        $uangmakanlembur += ($rest * $uang_makan_perhari);
                    } else if ($jamLembur == 8) {
                        $uangmakanlembur += $uang_makan_perhari;
                    }
                } else {
                    if ($jamLembur > 4) {
                        $uangmakanlembur += $uang_makan_perhari;
                        $j = $jamLembur - 4;
                        $rest = floor($j / 4);
                        $uangmakanlembur += ($rest * $uang_makan_perhari);
                    } else if ($jamLembur == 4) {
                        $uangmakanlembur += $uang_makan_perhari;
                    }
                }
            }
        }
        return $uangmakanlembur;
    }

    function getTotalUangMakanLemburPeriode($start, $end, $karyawan, $uang_makan_perhari)
    {
        $db = $this->db;

        $uangmakanlembur = 0;

        $query = $db->get_results("SELECT * FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='$karyawan' AND Tanggal BETWEEN '$start' AND '$end' AND TotalJamLembur>'3'");
        if ($query) {
            foreach ($query as $data) {
                $tanggal = $data->Tanggal;
                $jamLembur = $data->TotalJamLembur;
                $cek  = new DateTime($tanggal);
                if ($cek->format("D") == "Sun" || (in_array($tanggal, $this->holiday))) {
                    if ($jamLembur > 8) {
                        $uangmakanlembur += $uang_makan_perhari;
                        $j = $jamLembur - 8;
                        $rest = floor($j / 4);
                        $uangmakanlembur += ($rest * $uang_makan_perhari);
                    } else if ($jamLembur == 8) {
                        $uangmakanlembur += $uang_makan_perhari;
                    }
                } else {
                    if ($jamLembur > 4) {
                        $uangmakanlembur += $uang_makan_perhari;
                        $j = $jamLembur - 4;
                        $rest = floor($j / 4);
                        $uangmakanlembur += ($rest * $uang_makan_perhari);
                    } else if ($jamLembur == 4) {
                        $uangmakanlembur += $uang_makan_perhari;
                    }
                }
            }
        }
        return $uangmakanlembur;
    }

    function getTotalJamLemburHoliday($tahun, $bulan, $karyawan)
    {
        $holiday = $this->holiday;
        $db = $this->db;
        $totalJamLembur = 0;

        $bulan2 = intval($bulan) - 1;
        if ($bulan2 < 10) $bulan2 = "0" . $bulan2;
        if ($bulan == "01") {
            $tahun2 = $tahun - 1;
            $bulan2 = "12";
        } else {
            $tahun2 = $tahun;
        }

        for ($i = 26; $i <= cal_days_in_month(CAL_GREGORIAN, $bulan2, $tahun2); $i++) {
            if ($i < 10) $tgl = "0" . $i;
            else $tgl = $i;
            $tanggal = $tahun2 . "-" . $bulan2 . "-" . $tgl;

            $cek  = new DateTime($tanggal);
            if ($cek->format("D") == "Sun" || (in_array($tanggal, $holiday))) {
                $var = $db->get_var("SELECT SUM(TotalJamLembur) FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='$karyawan' AND Tanggal = '$tanggal' AND TotalJamLembur>0");
                if (!$var) $var = 0;
                $totalJamLembur += $var;
            }
        }

        for ($i = 1; $i <= 25; $i++) {
            if ($i < 10) $tgl = "0" . $i;
            else $tgl = $i;
            $tanggal = $tahun . "-" . $bulan . "-" . $tgl;

            $cek  = new DateTime($tanggal);
            if ($cek->format("D") == "Sun" || (in_array($tanggal, $holiday))) {
                $var = $db->get_var("SELECT SUM(TotalJamLembur) FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='$karyawan' AND Tanggal = '$tanggal' AND TotalJamLembur>0");
                if (!$var) $var = 0;
                $totalJamLembur += $var;
            }
        }
        return $totalJamLembur;
    }

    function getTotalJamLemburHolidayPeriode($start, $end, $karyawan)
    {
        $holiday = $this->holiday;
        $db = $this->db;
        $totalJamLembur = 0;

        $begin = new DateTime($start);
        $end   = new DateTime($end);

        for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
            $tanggal = $i->format("Y-m-d");

            $cek  = new DateTime($tanggal);
            if ($cek->format("D") == "Sun" || (in_array($tanggal, $holiday))) {
                $var = $db->get_var("SELECT SUM(TotalJamLembur) FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='$karyawan' AND Tanggal = '$tanggal' AND TotalJamLembur>0");
                if (!$var) $var = 0;
                $totalJamLembur += $var;
            }
        }

        return $totalJamLembur;
    }

    function getTotalJamLemburNormal($tahun, $bulan, $karyawan)
    {
        $holiday = $this->holiday;
        $db = $this->db;
        $totalJamLembur = 0;

        $bulan2 = intval($bulan) - 1;
        if ($bulan2 < 10) $bulan2 = "0" . $bulan2;
        if ($bulan == "01") {
            $tahun2 = $tahun - 1;
            $bulan2 = "12";
        } else {
            $tahun2 = $tahun;
        }

        for ($i = 26; $i <= cal_days_in_month(CAL_GREGORIAN, $bulan2, $tahun2); $i++) {
            if ($i < 10) $tgl = "0" . $i;
            else $tgl = $i;
            $tanggal = $tahun2 . "-" . $bulan2 . "-" . $tgl;

            $cek  = new DateTime($tanggal);
            if ($cek->format("D") != "Sun" && (!in_array($tanggal, $holiday))) {
                $var = $db->get_var("SELECT SUM(TotalJamLembur) FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='$karyawan' AND Tanggal = '$tanggal' AND TotalJamLembur>0");
                if (!$var) $var = 0;
                $totalJamLembur += $var;
            }
        }

        for ($i = 1; $i <= 25; $i++) {
            if ($i < 10) $tgl = "0" . $i;
            else $tgl = $i;
            $tanggal = $tahun . "-" . $bulan . "-" . $tgl;

            $cek  = new DateTime($tanggal);
            if ($cek->format("D") != "Sun" && (!in_array($tanggal, $holiday))) {
                $var = $db->get_var("SELECT SUM(TotalJamLembur) FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='$karyawan' AND Tanggal = '$tanggal' AND TotalJamLembur>0");
                if (!$var) $var = 0;
                $totalJamLembur += $var;
            }
        }
        return $totalJamLembur;
    }

    function getTotalJamLemburNormalPeriode($start, $end, $karyawan)
    {
        $holiday = $this->holiday;
        $db = $this->db;
        $totalJamLembur = 0;

        $begin = new DateTime($start);
        $end   = new DateTime($end);

        for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
            $tanggal = $i->format("Y-m-d");

            $cek  = new DateTime($tanggal);
            if ($cek->format("D") != "Sun" && (!in_array($tanggal, $holiday))) {
                $var = $db->get_var("SELECT SUM(TotalJamLembur) FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='$karyawan' AND Tanggal = '$tanggal' AND TotalJamLembur>0");
                if (!$var) $var = 0;
                $totalJamLembur += $var;
            }
        }

        return $totalJamLembur;
    }

    function getTotalJamLemburPeriode($start, $end, $karyawan)
    {
        $db = $this->db;
        $totalJamLembur = 0;

        $begin = new DateTime($start);
        $end   = new DateTime($end);

        for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
            $tanggal = $i->format("Y-m-d");

            $var = $db->get_var("SELECT SUM(TotalJamLembur) FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='$karyawan' AND Tanggal = '$tanggal' AND TotalJamLembur>0");
            if (!$var) $var = 0;
            $totalJamLembur += $var;
        }

        return $totalJamLembur;
    }

    function getTotalHitungGapokPeriode($start, $end, $karyawan)
    {
        $db = $this->db;

        $var = $db->get_var("SELECT COUNT(*) FROM tb_absent_karyawan_bulanan WHERE IDKaryawan='$karyawan' AND HitungGapok='1' AND Tanggal BETWEEN '$start' AND '$end'");
        if (!$var) $var = 0;

        return intval($var);
    }

    function getTotalHariKerjaBulanan($tahun, $bulan)
    {
        $harikerja = 0;
        $holiday = $this->holiday;

        $bulan2 = intval($bulan) - 1;
        if ($bulan2 < 10) $bulan2 = "0" . $bulan2;
        if ($bulan == "01") {
            $tahun2 = $tahun - 1;
            $bulan2 = "12";
        } else {
            $tahun2 = $tahun;
        }

        for ($i = 26; $i <= cal_days_in_month(CAL_GREGORIAN, $bulan2, $tahun2); $i++) {
            if ($i < 10) $tgl = "0" . $i;
            else $tgl = $i;
            $tanggal = $tahun2 . "-" . $bulan2 . "-" . $tgl;

            $cek  = new DateTime($tanggal);
            if ($cek->format("D") == "Sun") {
                // do nothing
            } else if (in_array($tanggal, $holiday)) {
                // do nothing
            } else {
                $harikerja++;
            }
        }

        for ($i = 1; $i <= 25; $i++) {
            if ($i < 10) $tgl = "0" . $i;
            else $tgl = $i;
            $tanggal = $tahun . "-" . $bulan . "-" . $tgl;

            $cek  = new DateTime($tanggal);
            if ($cek->format("D") == "Sun") {
                // do nothing
            } else if (in_array($tanggal, $holiday)) {
                // do nothing
            } else {
                $harikerja++;
            }
        }
        return $harikerja;
    }

    function getTotalHariKerjaBulananWithoutSunday($tahun, $bulan)
    {
        $harikerja = 0;

        $bulan2 = intval($bulan) - 1;
        if ($bulan2 < 10) $bulan2 = "0" . $bulan2;
        if ($bulan == "01") {
            $tahun2 = $tahun - 1;
            $bulan2 = "12";
        } else {
            $tahun2 = $tahun;
        }

        for ($i = 26; $i <= cal_days_in_month(CAL_GREGORIAN, $bulan2, $tahun2); $i++) {
            $harikerja++;
        }

        for ($i = 1; $i <= 25; $i++) {
            $harikerja++;
        }
        return $harikerja;
    }

    function isDateHoliday($tanggal)
    {
        $holiday = $this->holiday;
        $cek  = new DateTime($tanggal);
        if ($cek->format("D") == "Sun") {
            return true;
        } else if (in_array($tanggal, $holiday)) {
            return true;
        } else {
            return false;
        }
    }
}
