<?php
class CutiCalculation
{
    private $holiday = array();
    private $db;
    private $cutiKaryawan = array();

    function __construct(){
        $this->db = new ezSQL_mysql("root","diadmin","sopan","localhost");
    }

    function calcHolidayWithoutSunday(){
        $holiday = array();
        $db = $this->db;
        $query = $db->get_results("SELECT * FROM tb_public_holiday WHERE DATE_FORMAT(DateCreated,'%Y')>='".date("Y")."' ORDER BY DariTanggal ASC");
        if($query){
            foreach($query as $data){
                $begin = new DateTime($data->DariTanggal);
                $end = new DateTime($data->SampaiTanggal);

                $interval = DateInterval::createFromDateString('1 day');
                $period = new DatePeriod($begin, $interval, $end);
                $i = 0;
                foreach ( $period as $dt ){
                    $i++;
                    //Jangan ngambil data hari libur yang memang hari minggu.
                    if($dt->format("D") != "Sun"){
                        $nDate = $dt->format("Y-m-d");
                        array_push($holiday,$nDate);
                    }

                }

                if($i==0) {
                    $dt = new DateTime($data->DariTanggal);
                    if($dt->format("D") != "Sun"){
                        array_push($holiday,$data->DariTanggal);
                    }
                } else if($data->DariTanggal != $data->SampaiTanggal){
                    $dt = new DateTime($data->SampaiTanggal);
                    if($dt->format("D") != "Sun"){
                        array_push($holiday,$data->SampaiTanggal);
                    }
                }
            }
        }

        $this->holiday = $holiday;
        return true;
    }

    function generateKalendarCutiKaryawan($tahun,$karyawan){
        $db = $this->db;
        $holiday = $this->holiday;

        $db->query("DELETE FROM tb_cuti_kalendar_karyawan WHERE IDKaryawan='$karyawan' AND DATE_FORMAT(Tanggal, '%Y') = '$tahun'");
        $query = $db->get_results("SELECT *, DATE_FORMAT(DariTanggal, '%m') AS BulanStart, DATE_FORMAT(SampaiTanggal, '%m') AS BulanEnd  FROM tb_cuti WHERE Status='2' AND (DATE_FORMAT(DariTanggal, '%Y') = '$tahun' OR DATE_FORMAT(SampaiTanggal, '%Y') = '$tahun') AND IDKaryawan='$karyawan' ORDER BY DariTanggal ASC");
        if($query){
            foreach($query as $data){
                $totalCuti = $data->JumlahHari;
                $startDate = new DateTime($data->DariTanggal);
                $endDate = new DateTime($data->SampaiTanggal);

                $interval = DateInterval::createFromDateString('1 day');
                $period = new DatePeriod($startDate, $interval, $endDate);

                foreach ($period as $dt){
                    if($dt->format("D") != "Sun"){
                        $nDate = $dt->format("Y-m-d");
                        if(!in_array($nDate,$holiday)){
                            if($totalCuti>1) {
                                $jumlah = 1;
                                $totalCuti = $totalCuti-1;
                            } else if($totalCuti<1 && $totalCuti>0){
                                $jumlah = 0.5;
                            } else {
                                $jumlah = $totalCuti;
                            }
                            $query = $db->query("INSERT INTO tb_cuti_kalendar_karyawan SET Tanggal='$nDate', IDKaryawan='$karyawan', JumlahHari='$jumlah', Tipe='".$data->Jenis."'");
                        }
                    }
                }

                //Last Day
                if($endDate->format("D") != "Sun"){
                    $nDate = $endDate->format("Y-m-d");
                    if(!in_array($nDate,$holiday)){
                        if($totalCuti>1) {
                            $jumlah = 1;
                            $totalCuti = $totalCuti-1;
                        } else
                            $jumlah = $totalCuti;
                        $query = $db->query("INSERT INTO tb_cuti_kalendar_karyawan SET Tanggal='$nDate', IDKaryawan='$karyawan', JumlahHari='$jumlah', Tipe='".$data->Jenis."'");
                    }
                }
            }
        }
        return true;
    }

    function getTotalCutiBulananKaryawan($tahun,$bulan,$karyawan,$tipe){
        $db = $this->db;
        $var = $db->get_var("SELECT SUM(JumlahHari) FROM tb_cuti_kalendar_karyawan WHERE IDKaryawan='$karyawan'  AND DATE_FORMAT(Tanggal, '%Y-%m') = '$tahun-$bulan' AND Tipe='$tipe'");
        if(!$var) $var=0;
        return $var;
    }

    function getTotalCutiKaryawanSetahun($tahun,$bulan,$karyawan,$tipe){
        $db = $this->db;
        $var = $db->get_var("SELECT SUM(JumlahHari) FROM tb_cuti_kalendar_karyawan WHERE IDKaryawan='$karyawan'  AND DATE_FORMAT(Tanggal, '%Y-%m') <= '$tahun-$bulan' AND DATE_FORMAT(Tanggal, '%Y') >= '$tahun' AND Tipe='$tipe'");
        if(!$var) $var=0;
        return $var;
    }

    function calcHolidayAndSunday($dateStart,$dateEnd){
        $total = 0;
        $holiday = $this->holiday;

        $begin = new DateTime($dateStart);
        $end = new DateTime($dateEnd);

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);
        $i = 0;
        foreach ( $period as $dt ){
            $i++;
            if($dt->format("D") == "Sun"){
                $total++;
            } else {
                $nDate = $dt->format("Y-m-d");
                if (in_array($nDate, $holiday)) {
                    $total++;
                }
            }

        }

        return $total;
    }
}
?>
