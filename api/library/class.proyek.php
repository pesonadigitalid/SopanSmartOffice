<?php
class Proyek
{
    private $db;

    function __construct(){
        $this->db = new ezSQL_mysql("root","diadmin","sopan","localhost");
    }

    function calcGrandTotalProyek($idProyek){
        $db = $this->db;
        $dataProyek = $db->get_row("SELECT * FROM tb_proyek WHERE IDProyek='$idProyek'");
        if($dataProyek){
            $PPN = $dataProyek->Nominal * intval($dataProyek->PPNPersen) / 100;
            $Total2 = $dataProyek->Nominal + $PPN;
            $GrantTotalVO = $db->get_var("SELECT SUM(NilaiVO) FROM tb_proyek_vo WHERE IDProyek='$idProyek'");
            if(!$GrantTotalVO) $GrantTotalVO = 0;
            $GrandTotal =  $Total2 + $GrantTotalVO;

            //Update Limit
            $limit = $GrandTotal * $dataProyek->LimitPengeluaranPersen / 100;
            $totalPengeluaran = $db->get_var("SELECT SUM(GrandTotal) FROM tb_po WHERE IDProyek='".$dataProyek->IDProyek."' AND JenisPO='1'");
            if(!$totalPengeluaran) $totalPengeluaran=0;
            $totalPengeluaran2 = $db->get_var("SELECT SUM(GrandTotal) FROM tb_po WHERE IDProyek='".$dataProyek->IDProyek."' AND JenisPO='2'");
            if(!$totalPengeluaran2) $totalPengeluaran2=0;
            $totalPengeluaran3 = $db->get_var("SELECT SUM(GrandTotal) FROM tb_po WHERE IDProyek='".$dataProyek->IDProyek."' AND JenisPO='3'");
            if(!$totalPengeluaran3) $totalPengeluaran3=0;

            //Cek Pembayaran
            $totalPembayaran = $db->get_var("SELECT SUM(Debet) FROM tb_jurnal WHERE Tipe='1' AND IDProyek='$dataProyek->IDProyek'");
            if(!$totalPembayaran) $totalPembayaran=0;
            $sisaPembayaran = $GrandTotal - $totalPembayaran;
            if($sisaPembayaran>-1 && $sisaPembayaran<1) $sisaPembayaran = 0;

            $db->query("UPDATE tb_proyek SET Nominal='$dataProyek->Nominal', PPN='$PPN', Total2='$Total2', GrandTotalVO='$GrantTotalVO', GrandTotal='$GrandTotal', LimitPengeluaran='$limit', PengeluaranMaterial='$totalPengeluaran', PengeluaranGaji='$totalPengeluaran2', PengeluaranOverHead='$totalPengeluaran3', JumlahPembayaran='$totalPembayaran', SisaPembayaran='$sisaPembayaran' WHERE IDProyek='$idProyek'");

            //var_dump("UPDATE tb_proyek SET Nominal='$dataProyek->Nominal', PPN='$PPN', Total2='$Total2', GrandTotalVO='$GrantTotalVO', GrandTotal='$GrandTotal', LimitPengeluaran='$limit', PengeluaranMaterial='$totalPengeluaran', PengeluaranGaji='$totalPengeluaran2', PengeluaranOverHead='$totalPengeluaran3', JumlahPembayaran='$totalPembayaran', SisaPembayaran='$sisaPembayaran' WHERE IDProyek='$idProyek'<br/>");

            //Update VO
            $qVO = $db->get_results("SELECT * FROM tb_proyek_vo WHERE IDProyek='$idProyek' ORDER BY IDVO ASC");
            if($qVO){
                $NilaiProyek = $Total2;
                foreach($qVO as $dataVO){
                    $NilaiProyekAkhir = $NilaiProyek + $dataVO->NilaiVO;
                    $db->query("UPDATE tb_proyek_vo SET NilaiProyek='$NilaiProyek', NilaiAkhirProyek='$NilaiProyekAkhir' WHERE IDVO='$dataVO->IDVO'");
                    $NilaiProyek = $NilaiProyekAkhir;
                }
            }
        }
    }
}
?>
