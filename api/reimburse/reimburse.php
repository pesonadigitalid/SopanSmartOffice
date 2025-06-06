<?php
include_once "../config/connection.php";

$act = antiSQLInjection($_GET['act']);
switch ($act) {
    case "ReportRequirement":
        $karyawan = array();

        $query = $db->get_results("SELECT * FROM tb_karyawan WHERE Status='1' ORDER BY Nama ASC");
        if ($query) {
            foreach ($query as $data) {
                array_push($karyawan, array("IDKaryawan" => $data->IDKaryawan, "Nama" => $data->Nama));
            }
        }
        echo json_encode(array("karyawan" => $karyawan));
        break;

    case "LoadAllRequirement":
        $proyek = array();
        $karyawan = array();
        $pembayaran = array();
        $subpembayaran = array();

        $query = $db->get_results("SELECT * FROM tb_karyawan WHERE Status='1' ORDER BY IDKaryawan ASC");
        if ($query) {
            foreach ($query as $data) {
                array_push($karyawan, array("IDKaryawan" => $data->IDKaryawan, "Nama" => $data->Nama));
            }
        }

        $proyek = array();
        $query = $db->get_results("SELECT * FROM tb_proyek WHERE Status='2' ORDER BY Tahun, KodeProyek, NamaProyek ASC");
        if ($query) {
            foreach ($query as $data) {
                array_push($proyek, array("IDProyek" => $data->IDProyek, "Tahun" => $data->Tahun, "KodeProyek" => $data->KodeProyek, "NamaProyek" => $data->NamaProyek, "Status" => $status, "Departement" => $data->NamaDepartement));
            }
        }

        $query = $db->get_results("SELECT * FROM tb_master_rekening WHERE IDParent='3' AND Tipe='H'");
        if ($query) {
            foreach ($query as $data) {
                array_push($pembayaran, array("IDRekening" => $data->IDRekening, "KodeRekening" => $data->KodeRekening, "NamaRekening" => $data->NamaRekening));
            }
        }

        $query = $db->get_results("SELECT * FROM tb_master_rekening WHERE (IDParent='4' OR IDParent='6') AND Tipe='D' ORDER BY IDParent, IDRekening ASC");
        if ($query) {
            foreach ($query as $data) {
                array_push($subpembayaran, array("IDRekening" => $data->IDRekening, "KodeRekening" => $data->KodeRekening, "NamaRekening" => $data->NamaRekening, "IDParent" => $data->IDParent));
            }
        }
        echo json_encode(array("karyawan" => $karyawan, "proyek" => $proyek, "pembayaran" => $pembayaran, "subpembayaran" => $subpembayaran));
        break;

    case "Detail":
        $detail = array();
        $proyek = array();
        $pembayaran = array();
        $subpembayaran = array();

        $id = antiSQLInjection($_GET['id']);

        $query = $db->get_row("SELECT *, DATE_FORMAT(Tanggal, '%d/%m/%Y') AS TanggalID FROM tb_reimburse WHERE IDReimburse='$id' ORDER BY IDReimburse ASC");
        if ($query) {
            $karyawan = $db->get_var("SELECT NamaKaryawan FROM tb_karyawan WHERE IDKaryawan='" . $query->IDKaryawan . "'");
            $detail = array("no_reimburse" => $query->NoReimburse, "tanggal" => $query->TanggalID, "karyawan" => $karyawan, "category" => $query->Kategori, "no_kendaraan" => $query->NoKendaraan, "total_nilai" => $query->TotalNilai, "jumlah_liter" => $query->JumlahLiterBBM, "km_kendaraan" => $query->KMKendaraan, "lokasi_service" => $query->LokasiService, "stts" => $query->Status, "karyawan" => $query->IDKaryawan, "proyek" => $query->IDProyek, "metode_pem" => $query->MetodePembayaran, "metode_pem1" => $query->MetodePembayaran1, "metode_pem2" => $query->MetodePembayaran2, "keterangan" => $query->Keterangan, "no_bg" => $query->NoBG, "jatuh_tempo" => $query->BGJatuhTempo, "Image1" => $query->Image1, "Image2" => $query->Image2, "Image3" => $query->Image3);
        }

        $karyawan = array();
        $query = $db->get_results("SELECT * FROM tb_karyawan WHERE Status='1' ORDER BY IDKaryawan ASC");
        if ($query) {
            foreach ($query as $data) {
                array_push($karyawan, array("IDKaryawan" => $data->IDKaryawan, "Nama" => $data->Nama));
            }
        }

        $proyek = array();
        $query = $db->get_results("SELECT * FROM tb_proyek WHERE Status='2' ORDER BY Tahun, KodeProyek, NamaProyek ASC");
        if ($query) {
            foreach ($query as $data) {
                array_push($proyek, array("IDProyek" => $data->IDProyek, "Tahun" => $data->Tahun, "KodeProyek" => $data->KodeProyek, "NamaProyek" => $data->NamaProyek, "Status" => $status, "Departement" => $data->NamaDepartement));
            }
        }

        $query = $db->get_results("SELECT * FROM tb_master_rekening WHERE IDParent='3' AND Tipe='H'");
        if ($query) {
            foreach ($query as $data) {
                array_push($pembayaran, array("IDRekening" => $data->IDRekening, "KodeRekening" => $data->KodeRekening, "NamaRekening" => $data->NamaRekening));
            }
        }

        $query = $db->get_results("SELECT * FROM tb_master_rekening WHERE (IDParent='4' OR IDParent='6') AND Tipe='D' ORDER BY IDParent, IDRekening ASC");
        if ($query) {
            foreach ($query as $data) {
                array_push($subpembayaran, array("IDRekening" => $data->IDRekening, "KodeRekening" => $data->KodeRekening, "NamaRekening" => $data->NamaRekening, "IDParent" => $data->IDParent));
            }
        }
        echo json_encode(array("detail" => $detail, "karyawan" => $karyawan, "proyek" => $proyek, "pembayaran" => $pembayaran, "subpembayaran" => $subpembayaran));
        break;

    default:
        echo "";
}
