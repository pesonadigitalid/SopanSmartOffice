<?php

class Stok
{
	private $db;

	function __construct($db)
	{
		$this->db = $db;
	}

	function GetSourceText($Source)
	{
		if ($Source == "1") {
			return "Penerimaan Barang";
		} else if ($Source == "2") {
			return "Transfer Stok Barang";
		} else if ($Source == "3") {
			return "Audit Stok Barang";
		} else if ($Source == "4") {
			return "Penggunaan Stok Bahan Baku Penjualan/Sales Order";
		} else if ($Source == "5") {
			return "Retur Purchase Order";
		} else if ($Source == "6") {
			return "Surat Jalan";
		} else if ($Source == "31") {
			return "Pembatalan Penerimaan Barang";
		} else if ($Source == "32") {
			return "Pembatalan Transfer Stok Barang";
		} else if ($Source == "33") {
			return "Pembatalan Audit Stok Barang";
		} else if ($Source == "34") {
			return "Reset Penggunaan Stok Bahan Baku Penjualan/Sales Order";
		} else if ($Source == "35") {
			return "Pembatalan Retur Purchase Order";
		} else if ($Source == "36") {
			return "Pembatalan Surat Jalan";
		} else if ($Source == "41") {
			return "Pengiriman Transfer Stok Gudang";
		} else if ($Source == "42") {
			return "Penerimaan Transfer Stok Gudang";
		} else if ($Source == "43") {
			return "Pembatalan Pengiriman Transfer Stok Gudang";
		} else if ($Source == "44") {
			return "Pembatalan Penerimaan Transfer Stok Gudang";
		}
		return "";
	}

	function PenerimaanStok($NoPenerimaanBarang, $IDGudang, $Tanggal, $IDPenjualan = 0)
	{
		$query = $this->db->get_results("SELECT * FROM tb_penerimaan_stok_detail WHERE NoPenerimaanBarang='$NoPenerimaanBarang' ORDER BY NoUrut");
		if ($query) {
			$IDFaktur = $this->db->get_var("SELECT IDPenerimaan FROM tb_penerimaan_stok WHERE NoPenerimaanBarang='$NoPenerimaanBarang'");
			if (!$IDFaktur) $IDFaktur = 0;
			foreach ($query as $data) {
				$isPaket = $this->db->get_results("SELECT * FROM tb_barang_child WHERE IDParent='$data->IDBarang'");
				if (!$isPaket) {
					$this->InsertKartuStok($IDFaktur, $NoPenerimaanBarang, $Tanggal, $IDGudang, $IDPenjualan, $data->IDBarang, $data->Qty, $data->HPP, $data->SN, 1, 1, false, $data->IDDetail);
				}
			}
		}
	}

	function InsertKartuStok($IDFaktur, $NoFaktur, $Tanggal, $IDGudang, $IDPenjualan, $IDBarang, $Qty, $HPP, $SN, $Tipe, $Source, $ShouldRemoveSN = false, $IDDetailPenerimaan = 0, $TipePenerimaan = '1')
	{
		if ($Qty == 0) return;

		$TableName = ($IDPenjualan == 0) ? "tb_kartu_stok_gudang" : "tb_kartu_stok_purchasing";
		$AdditionalConditions = ($IDPenjualan > 0) ? " AND IDPenjualan='$IDPenjualan' " : "";
		$AdditionalFields = ($IDPenjualan == 0) ? "" : ", IDPenjualan='$IDPenjualan' ";

		$StokAkhir = $this->db->get_var("SELECT StokAkhir FROM $TableName WHERE IDGudang='$IDGudang' AND IDBarang='$IDBarang' $AdditionalConditions ORDER BY IDKartuStok DESC");
		if (!$StokAkhir) $StokAkhir = 0;

		$StokAkhir += $Qty;
		$SubTotalHPP = $HPP * $Qty;

		$Keterangan = $this->GetSourceText($Source) . "; No. Faktur $NoFaktur; Tgl. " . $this->IDDate($Tanggal);

		$this->db->query("INSERT INTO $TableName SET IDGudang='$IDGudang', IDBarang='$IDBarang', Tipe='$Tipe', Source='$Source', NoFaktur='$NoFaktur', StokPenyesuaian='$Qty', StokAkhir='$StokAkhir', HPP='$HPP', SN='$SN', SubTotalHPP='$SubTotalHPP', Keterangan='$Keterangan', Tanggal='$Tanggal', CreatedBy='" . $_SESSION['uid'] . "' $AdditionalFields ");

		$HPPMethod = $this->db->get_var("SELECT HPPMethod FROM tb_barang WHERE IDBarang='$IDBarang'");

		if ($HPPMethod == "AVG") $HPP = $this->GetHPPStokAVG($IDGudang, $IDBarang, $IDPenjualan);
		$this->UpdateStok($IDFaktur, $IDGudang, $IDPenjualan, $IDBarang, $StokAkhir, $HPP, $SN, ($Qty < 0), $Qty, $HPPMethod, $ShouldRemoveSN, $IDDetailPenerimaan, $TipePenerimaan);
	}

	function CombineKartuStok($NoFaktur, $Tanggal, $IDGudang, $IDPenjualan, $IDBarang, $Qty, $SN, $Tipe, $Source)
	{
		if ($Qty == 0) return;

		$Keterangan = $this->GetSourceText($Source) . "; No. Faktur $NoFaktur; Tgl. " . $this->IDDate($Tanggal);

		$TableName = ($IDPenjualan == 0) ? "tb_kartu_stok_gudang" : "tb_kartu_stok_purchasing";
		$MainConditions = " AND IDGudang='$IDGudang' AND IDBarang='$IDBarang' AND Tipe='$Tipe' AND Source='$Source' AND NoFaktur='$NoFaktur' AND Keterangan='$Keterangan' AND Tanggal='$Tanggal'";
		$AdditionalConditions = ($IDPenjualan > 0) ? " AND IDPenjualan='$IDPenjualan' " : "";
		$AdditionalFields = ($IDPenjualan == 0) ? "" : ", IDPenjualan='$IDPenjualan' ";

		$StokAkhir = $this->db->get_var("SELECT StokAkhir FROM $TableName WHERE IDGudang='$IDGudang' AND IDBarang='$IDBarang' $MainConditions $AdditionalConditions ORDER BY IDKartuStok DESC");
		if (!$StokAkhir) $StokAkhir = 0;

		$HPP = $this->db->get_var("SELECT (SUM(SubTotalHPP)/SUM(StokPenyesuaian)) FROM $TableName WHERE IDGudang='$IDGudang' AND IDBarang='$IDBarang' $MainConditions $AdditionalConditions ORDER BY IDKartuStok DESC");
		if (!$HPP) $HPP = 0;

		$SubTotalHPP = $HPP * $Qty;

		$this->db->query("DELETE FROM $TableName WHERE IDGudang='$IDGudang' AND IDBarang='$IDBarang' $MainConditions $AdditionalConditions ORDER BY IDKartuStok DESC");

		$this->db->query("INSERT INTO $TableName SET IDGudang='$IDGudang', IDBarang='$IDBarang', Tipe='$Tipe', Source='$Source', NoFaktur='$NoFaktur', StokPenyesuaian='$Qty', StokAkhir='$StokAkhir', HPP='$HPP', SN='$SN', SubTotalHPP='$SubTotalHPP', Keterangan='$Keterangan', Tanggal='$Tanggal', CreatedBy='" . $_SESSION['uid'] . "' $AdditionalFields ");
	}

	function UpdateStok($IDFaktur, $IDGudang, $IDPenjualan, $IDBarang, $StokAkhir, $HPP, $SN, $DeductedStock, $Qty, $HPPMethod = "AVG", $ShouldRemoveSN = false, $IDDetailPenerimaan = 0, $TipePenerimaan = '1')
	{
		$TableName = ($IDPenjualan > 0) ? "tb_stok_purchasing" : "tb_stok_gudang";
		$AdditionalConditions = ($IDPenjualan > 0) ? " AND IDPenjualan='$IDPenjualan' " : "";
		$AdditionalFields = ($IDPenjualan > 0) ? ", IDPenjualan='$IDPenjualan' " : "";


		if ($SN != "" && $HPPMethod != "AVG") {
			$TableName2 = ($IDPenjualan > 0) ? "tb_stok_purchasing_serial_number" : "tb_stok_gudang_serial_number";
			$StokSN = $this->db->get_row("SELECT * FROM $TableName2 WHERE SN='$SN' AND Stok>0");
			if ($StokSN) {
				$AdditionalConditions = " AND IDStok='$StokSN->IDStok'";
			}
		}

		$updateStokHandled = false;
		$check = $this->db->get_row("SELECT * FROM $TableName WHERE IDGudang='$IDGudang' AND IDBarang='$IDBarang' $AdditionalConditions");
		if ($check) {
			$IDStok = $check->IDStok;
			if ($HPPMethod == "AVG") {
				// For AVG, we just use single records for maintaining stock hence we just update it
				$this->db->query("UPDATE $TableName SET SisaStok='$StokAkhir', HPP='$HPP', DateModified=NOW(), ModifiedBy='" . $_SESSION['uid'] . "' $AdditionalFields WHERE IDGudang='$IDGudang' AND IDBarang='$IDBarang' $AdditionalConditions");

				$updateStokHandled = true;
			} else if ($DeductedStock && $HPPMethod != "AVG") {
				// If the process is stock deduction and not AVG, we need to deduct the stock records with the same price to make it balance
				$queryStok = $this->db->get_results("SELECT * FROM $TableName WHERE IDGudang='$IDGudang' AND IDBarang='$IDBarang' AND HPP='$HPP' AND SisaStok>0 $AdditionalConditions");

				if ($queryStok) {
					$Qty = abs($Qty);

					foreach ($queryStok as $dataStok) {
						if ($dataStok->SisaStok <= $Qty) {
							$SisaStok = 0;
							$Qty = $Qty - $dataStok->SisaStok;
						} else {
							$SisaStok = $dataStok->SisaStok - $Qty;
							$Qty = 0;
						}

						$this->db->query("UPDATE $TableName SET SisaStok='$SisaStok', DateModified=NOW(), ModifiedBy='" . $_SESSION['uid'] . "' $AdditionalFields WHERE IDStok='$dataStok->IDStok'");

						if ($Qty == 0) break;
					}

					$updateStokHandled = true;
				}
			}
		}

		if (!$DeductedStock && !$updateStokHandled) {
			// For FIFO/LIFO, we didn't maintain the stock in single records hence we insert the records with the actual QTY
			if (!$DeductedStock && $HPPMethod != "AVG") $StokAkhir = $Qty;
			if ($HPPMethod != "AVG") $AdditionalFields .= ", RefID='$IDFaktur', RefIDDetail='$IDDetailPenerimaan', Tipe='$TipePenerimaan'";
			$this->db->query("INSERT INTO $TableName SET IDGudang='$IDGudang', IDBarang='$IDBarang', SisaStok='$StokAkhir', HPP='$HPP', CreatedBy='" . $_SESSION['uid'] . "' $AdditionalFields");

			$IDStok = $this->db->get_var("SELECT LAST_INSERT_ID()");
		}

		if ($IDStok > 0 && $SN != "") {
			// Handle Stock Serial Number Update if SN is not blank and existing stock is found.
			$TableName = ($IDPenjualan > 0) ? "tb_stok_purchasing_serial_number" : "tb_stok_gudang_serial_number";
			$StokSN = $this->db->get_row("SELECT * FROM $TableName WHERE IDStok='$IDStok' AND SN='$SN' AND Stok>0");

			if ($StokSN && $ShouldRemoveSN) {
				$this->db->query("DELETE FROM $TableName WHERE IDStokSN='$StokSN->IDStokSN'");
			} else if ($StokSN && $DeductedStock) {
				$this->db->query("UPDATE $TableName SET Stok='0', DateModified=NOW(), ModifiedBy='" . $_SESSION['uid'] . "' WHERE IDStokSN='$StokSN->IDStokSN'");
			} else if (!$DeductedStock) {
				$this->db->query("INSERT INTO $TableName SET IDStok='$IDStok', SN='$SN', Stok='1', CreatedBy='" . $_SESSION['uid'] . "'");
			}
		}
	}

	function GetHPPStokAVG($IDGudang, $IDBarang, $IDPenjualan)
	{
		$TableName = ($IDPenjualan > 0) ? "tb_kartu_stok_purchasing" : "tb_kartu_stok_gudang";
		$AdditionalConditions = ($IDPenjualan > 0) ? " AND IDPenjualan='$IDPenjualan' " : "";

		$HPP = $this->db->get_var("SELECT (SUM(SubTotalHPP)/SUM(StokPenyesuaian)) FROM $TableName WHERE IDGudang='$IDGudang' AND IDBarang='$IDBarang' $AdditionalConditions");
		if (!$HPP) $HPP = 0;
		return $HPP;
	}

	function CheckAllowDeletePenerimaanStok($NoPenerimaanBarang)
	{
		$Penerimaan = $this->db->get_row("SELECT * FROM tb_penerimaan_stok WHERE NoPenerimaanBarang='$NoPenerimaanBarang'");
		$IDGudang = $Penerimaan->IDGudang;
		$IDPenjualan = $Penerimaan->IDPenjualan;

		$TableName = ($IDPenjualan > 0) ? "tb_stok_purchasing" : "tb_stok_gudang";
		$TableNameStokSN = ($IDPenjualan > 0) ? "tb_stok_purchasing_serial_number" : "tb_stok_gudang_serial_number";

		$query = $this->db->get_results("SELECT * FROM tb_penerimaan_stok_detail WHERE NoPenerimaanBarang='$NoPenerimaanBarang' ORDER BY NoUrut");
		if ($query) {
			foreach ($query as $data) {
				$HPPMethod = $this->db->get_var("SELECT HPPMethod FROM tb_barang WHERE IDBarang='$data->IDBarang'");

				$AdditionalConditions = ($IDPenjualan > 0) ? " AND IDPenjualan='$IDPenjualan' " : "";
				$AdditionalConditionsForSN = ($IDPenjualan > 0) ? " AND b.IDPenjualan='$IDPenjualan' " : "";
				if ($HPPMethod != "AVG") {
					$AdditionalConditions .= " AND HPP='$data->HPP'";
					$AdditionalConditionsForSN .= " AND b.HPP='$data->HPP'";
				}

				if ($data->SN != "") {
					$StokSN = $this->db->get_row("SELECT a.* FROM $TableNameStokSN a, $TableName b WHERE a.IDStok=b.IDStok AND b.IDBarang='$data->IDBarang' AND b.IDGudang='$IDGudang' AND a.SN='$data->SN' AND a.Stok>0 $AdditionalConditionsForSN");
					if (!$StokSN) {
						return false;
					}
				} else {
					$StokAkhir = $this->db->get_var("SELECT SUM(SisaStok) FROM $TableName WHERE IDGudang='$IDGudang' AND IDBarang='$data->IDBarang' $AdditionalConditions");
					if (!$StokAkhir || $StokAkhir < $data->Qty) {
						return false;
					}
				}
			}
		}
		return true;
	}

	function DeletePenerimaanStok($NoPenerimaanBarang)
	{
		$Penerimaan = $this->db->get_row("SELECT * FROM tb_penerimaan_stok WHERE NoPenerimaanBarang='$NoPenerimaanBarang'");
		$IDGudang = $Penerimaan->IDGudang;
		$IDPenjualan = $Penerimaan->IDPenjualan;
		$IDFaktur = $Penerimaan->IDPenerimaan;

		$Tanggal = date("Y-m-d");

		$query = $this->db->get_results("SELECT * FROM tb_penerimaan_stok_detail WHERE NoPenerimaanBarang='$NoPenerimaanBarang' ORDER BY NoUrut");
		if ($query) {
			foreach ($query as $data) {
				$this->InsertKartuStok($IDFaktur, $NoPenerimaanBarang, $Tanggal, $IDGudang, $IDPenjualan, $data->IDBarang, (0 - $data->Qty), $data->HPP, $data->SN, 2, 31, true);
			}
		}
		return true;
	}

	function AuditStokGudang($NoAudit)
	{
		$dataAudit = $this->db->get_row("SELECT * FROM tb_audit WHERE NoAudit='$NoAudit'");
		$IDGudang = $dataAudit->IDGudang;
		$Tanggal = $dataAudit->Tanggal;
		$IDFaktur = $dataAudit->IDAudit;

		$query = $this->db->get_results("SELECT * FROM tb_audit_detail WHERE NoAudit='$NoAudit' ORDER BY NoUrut");
		if ($query) {
			foreach ($query as $data) {
				$StokSimpan = abs($data->SPGudang);
				$originalRecordUpdated = false;

				if ($data->SPGudang > 0) {
					// New stock
					$HPP = $data->Harga;

					$this->UpdateItemAudit($IDFaktur, $StokSimpan, $QtyToSave, $data->StokGudang, $HPP, $data, $NoAudit, $Tanggal, $IDGudang, $originalRecordUpdated, false);
				} else {
					// Deducted stock
					$HPPMethod = $this->db->get_var("SELECT HPPMethod FROM tb_barang WHERE IDBarang='$data->IDBarang'");

					if ($data->SN != "") {
						// Handle Stock with Serial Number
						$SisaStok = $this->db->get_var("SELECT SUM(SisaStok) FROM tb_stok_gudang WHERE IDGudang='$IDGudang' AND IDBarang='$data->IDBarang'");
						if (!$SisaStok) $SisaStok = 0;

						if ($SisaStok >= $StokSimpan) {
							$stokFound = false;
							if ($HPPMethod == "AVG") {
								// Get HPP for AVG Method
								$HPP = $this->GetHPPStokAVG($IDGudang, $data->IDBarang, 0);
								$stokFound = true;
							} else {
								// Get HPP for FIFO/LIFO; Use HPP for specific Serial Number
								$Order = ($HPPMethod == "FIFO") ? "ASC" : "DESC";

								$queryStok = $this->db->get_results("SELECT * FROM tb_stok_gudang WHERE IDGudang='$IDGudang' AND IDBarang='$data->IDBarang' AND SisaStok>0 ORDER BY IDStok $Order");
								if ($queryStok) {
									foreach ($queryStok as $dataStok) {
										$stokSerialNumber = $this->db->get_row("SELECT * FROM tb_stok_gudang_serial_number WHERE IDStok='$dataStok->IDStok' AND SN='$data->SN' AND Stok>0");
										if ($stokSerialNumber) {
											$HPP = $dataStok->HPP;
											$stokFound = true;
											break;
										}
									}
								}
							}

							if ($stokFound) {
								$this->InsertKartuStok($IDFaktur, $NoAudit, $Tanggal, $IDGudang, 0, $data->IDBarang, (0 - $StokSimpan), $HPP, $data->SN, 2, 3);

								$this->db->query("UPDATE tb_audit_detail SET Harga='$HPP', SubTotal=(Harga*SPGudang) WHERE NoAudit='$NoAudit' AND NoUrut='$data->NoUrut' AND IDBarang='$data->IDBarang' AND SN='$data->SN'");
							}
						}
					} else {
						// Handle Non-Serial Number Stock
						$sisaStokGudang = $this->db->get_var("SELECT SUM(SisaStok) FROM tb_stok_gudang WHERE IDGudang='$IDGudang' AND IDBarang='$data->IDBarang'");
						if ($sisaStokGudang > abs($data->SPGudang)) {

							if ($HPPMethod == "AVG") {
								$HPP = $this->GetHPPStokAVG($IDGudang, $data->IDBarang, 0);

								$this->UpdateItemAudit($IDFaktur, $StokSimpan, $QtyToSave, $data->StokGudang, $HPP, $data, $NoAudit, $Tanggal, $IDGudang, $originalRecordUpdated);
							} else {
								$Order = ($HPPMethod == "FIFO") ? "ASC" : "DESC";

								$queryStok = $this->db->get_results("SELECT * FROM tb_stok_gudang WHERE IDGudang='$IDGudang' AND IDBarang='$data->IDBarang' AND SisaStok>0 ORDER BY IDStok $Order");
								if ($queryStok) {
									$TotalQtyToSave = 0;
									foreach ($queryStok as $dataStok) {
										$HPP = $dataStok->HPP;
										$this->UpdateItemAudit($IDFaktur, $StokSimpan, $QtyToSave, $dataStok->SisaStok, $HPP, $data, $NoAudit, $Tanggal, $IDGudang, $originalRecordUpdated);
										$TotalQtyToSave += $QtyToSave;
										if ($StokSimpan == 0) break;
									}
									$this->CombineKartuStok($NoAudit, $Tanggal, $IDGudang, 0, $data->IDBarang, (0 - $TotalQtyToSave), $data->SN, 2, 3);
								}
							}
						}
					}
				}
			}

			$GrandTotal = $this->db->get_var("SELECT SUM(SubTotal) FROM tb_audit_detail WHERE NoAudit='$NoAudit'");
			$this->db->query("UPDATE tb_audit SET GrandTotal='$GrandTotal' WHERE NoAudit='$NoAudit'");
		}
	}

	function UpdateItemAudit($IDFaktur, &$StokSimpan, &$QtyToSave, $SisaStok, $HPP, $DetailAudit, $NoAudit, $Tanggal, $IDGudang, &$OriginalRecordUpdated, $DeductedStock = true)
	{
		if ($DeductedStock) {
			if ($StokSimpan < $SisaStok) {
				$QtyToSave = $StokSimpan;
			} else {
				$QtyToSave = $SisaStok;
			}
			$SPGudang = 0 - $QtyToSave;
		} else {
			$QtyToSave = $StokSimpan;
			$SPGudang = $QtyToSave;
		}

		$this->InsertKartuStok($IDFaktur, $NoAudit, $Tanggal, $IDGudang, 0, $DetailAudit->IDBarang, $SPGudang, $HPP, $DetailAudit->SN, 2, 3, false, $DetailAudit->IDDetail, 2);

		$StokSimpan -= $SisaStok;
		if ($StokSimpan < 0) $StokSimpan = 0;

		if (!$OriginalRecordUpdated) {
			$this->db->query("UPDATE tb_audit_detail SET SPGudang='$SPGudang', Harga='$HPP', SubTotal=(Harga*SPGudang) WHERE NoAudit='$NoAudit' AND NoUrut='$DetailAudit->NoUrut' AND IDBarang='$DetailAudit->IDBarang'");

			$OriginalRecordUpdated = true;
		} else {
			$this->db->query("INSERT INTO tb_audit_detail SET NoAudit='$DetailAudit->NoAudit', NoUrut='" . $DetailAudit->NoUrut . "', IDBarang='" . $DetailAudit->IDBarang . "', NamaBarang='" . $DetailAudit->NamaBarang . "', StokPurchasingAwal='0', StokGudangAwal='" . $DetailAudit->StokGudangAwal . "', StokPurchasing='0', StokGudang='" . $DetailAudit->StokGudang . "', SPPurchasing='0', SPGudang='$SPGudang', Harga='" . $HPP . "', SubTotal=(Harga*SPGudang), SN='" . $DetailAudit->SN . "'");
		}
	}

	function CheckAllowDeleteAuditStokGudang($NoAudit)
	{
		$IDGudang = $this->db->get_var("SELECT IDGudang FROM tb_audit WHERE NoAudit='$NoAudit'");
		$query = $this->db->get_results("SELECT *, SUM(SPGudang) AS TOTAL_QTY FROM tb_audit_detail WHERE NoAudit='$NoAudit' GROUP BY IDBarang ORDER BY NoUrut");
		if ($query) {
			foreach ($query as $data) {
				if ($data->TOTAL_QTY > 0) {
					$HPPMethod = $this->db->get_var("SELECT HPPMethod FROM tb_barang WHERE IDBarang='$data->IDBarang'");
					$AdditionalConditions = ($HPPMethod != "AVG") ? " AND HPP='$data->Harga'" : "";
					$AdditionalConditionsForSN = ($HPPMethod != "AVG") ? " AND b.HPP='$data->Harga'" : "";

					if ($data->SN != "") {
						$StokSN = $this->db->get_row("SELECT a.* FROM tb_stok_gudang_serial_number a, tb_stok_gudang b WHERE a.IDStok=b.IDStok AND b.IDBarang='$data->IDBarang' AND b.IDGudang='$IDGudang' AND a.SN='$data->SN' AND a.Stok>0 $AdditionalConditionsForSN");
						if (!$StokSN) {
							return false;
						}
					} else {
						$StokAkhir = $this->db->get_var("SELECT SUM(SisaStok) FROM tb_stok_gudang WHERE IDGudang='$IDGudang' AND IDBarang='$data->IDBarang' $AdditionalConditions");
						if (!$StokAkhir || $StokAkhir < $data->TOTAL_QTY) {
							return false;
						}
					}
				}
			}
		}
		return true;
	}

	function DeleteAuditStokGudang($NoAudit)
	{
		$dataAudit = $this->db->get_row("SELECT * FROM tb_audit WHERE NoAudit='$NoAudit'");
		$IDGudang = $dataAudit->IDGudang;
		$Tanggal = date("Y-m-d");
		$IDFaktur = $dataAudit->IDAudit;

		// Return stock based on stock distributions
		$query = $this->db->get_results("SELECT * FROM tb_audit_detail WHERE NoAudit='$NoAudit' ORDER BY NoUrut");
		if ($query) {
			foreach ($query as $data) {
				$Tipe = ($data->SPGudang < 0) ? 1 : 2;
				$Qty = ($data->SPGudang < 0) ? abs($data->SPGudang) : (0 - $data->SPGudang);

				$this->InsertKartuStok($IDFaktur, $NoAudit, $Tanggal, $IDGudang, 0, $data->IDBarang, $Qty, $data->Harga, $data->SN, $Tipe, 33);
			}
		}

		// Combine Kartu Stock
		$query = $this->db->get_results("SELECT *, SUM(SPGudang) AS TotalSPGudang FROM tb_audit_detail WHERE NoAudit='$NoAudit'  AND (SN IS NULL OR SN='') GROUP BY `IDBarang`");
		if ($query) {
			foreach ($query as $data) {
				$Tipe = ($data->TotalSPGudang < 0) ? 1 : 2;
				$Qty = ($data->TotalSPGudang < 0) ? abs($data->TotalSPGudang) : (0 - $data->TotalSPGudang);

				$this->CombineKartuStok($NoAudit, $Tanggal, $IDGudang, 0, $data->IDBarang, $Qty, $data->SN, $Tipe, 33);
			}
		}

		return true;
	}

	function TransferStokGudang($NoTransferStok)
	{
		$dataTransferStok = $this->db->get_row("SELECT * FROM tb_transfer_stok WHERE NoTransferStok='$NoTransferStok'");
		$IDGudangFrom = $dataTransferStok->IDGudangFrom;
		$IDGudangTo = $dataTransferStok->IDGudangTo;
		$Tanggal = $dataTransferStok->Tanggal;
		$IDPenjualan = $dataTransferStok->IDPenjualan;
		if (!$IDPenjualan) $IDPenjualan = 0;
		$IDFaktur = $dataTransferStok->IDTransferStok;

		$query = $this->db->get_results("SELECT * FROM tb_transfer_stok_detail WHERE NoTransferStok='$NoTransferStok' ORDER BY NoUrut");
		if ($query) {
			foreach ($query as $data) {

				$TableName = $IDPenjualan > 0 ? "tb_stok_purchasing" : "tb_stok_gudang";
				$TableNameCond = $IDPenjualan > 0 ? " AND IDPenjualan='$IDPenjualan'" : "";
				$TableNameStokSN = $IDPenjualan > 0 ? "tb_stok_purchasing_serial_number" : "tb_stok_gudang_serial_number";

				$stokGudang = $this->db->get_row("SELECT *, SUM(SisaStok) AS SisaStokFinal FROM $TableName WHERE IDGudang='$IDGudangFrom' AND IDBarang='$data->IDBarang'");

				if ($stokGudang && $stokGudang->SisaStokFinal >= $data->Qty) {
					$HPPMethod = $this->db->get_var("SELECT HPPMethod FROM tb_barang WHERE IDBarang='$data->IDBarang'");

					if ($data->SN != "") {
						$data->SN = trim(str_replace("(GUDANG)", "", $data->SN));
						if ($HPPMethod == "AVG") {
							// Get HPP for AVG Method
							$HPP = $this->GetHPPStokAVG($IDGudangFrom, $data->IDBarang, 0);
							$stokFound = true;
						} else {
							// Get HPP for FIFO/LIFO; Use HPP for specific Serial Number
							$Order = ($HPPMethod == "FIFO") ? "ASC" : "DESC";

							$queryStok = $this->db->get_results("SELECT * FROM $TableName WHERE IDGudang='$IDGudangFrom' AND IDBarang='$data->IDBarang' AND SisaStok>0 $TableNameCond ORDER BY IDStok $Order");
							if ($queryStok) {
								foreach ($queryStok as $dataStok) {
									$stokSerialNumber = $this->db->get_row("SELECT * FROM $TableNameStokSN WHERE IDStok='$dataStok->IDStok' AND SN='$data->SN' AND Stok>0");
									if ($stokSerialNumber) {
										$HPP = $dataStok->HPP;
										$stokFound = true;
										break;
									}
								}
							}
						}

						if ($stokFound) {
							$this->InsertKartuStok($IDFaktur, $NoTransferStok, $Tanggal, $IDGudangFrom, $IDPenjualan, $data->IDBarang, (0 - $data->Qty), $HPP, $data->SN, 2, 41);

							$this->InsertKartuStok($IDFaktur, $NoTransferStok, $Tanggal, $IDGudangTo, $IDPenjualan, $data->IDBarang, $data->Qty, $HPP, $data->SN, 1, 42);

							$this->db->query("UPDATE tb_transfer_stok_detail SET SN='$data->SN', HPP='$HPP', SubTotal=(HPP*Qty) WHERE IDDetail='$data->IDDetail'");
						}
					} else {
						if ($HPPMethod == "AVG") {
							$HPP = $this->GetHPPStokAVG($IDGudangFrom, $data->IDBarang, 0);

							$this->InsertKartuStok($IDFaktur, $NoTransferStok, $Tanggal, $IDGudangFrom, $IDPenjualan, $data->IDBarang, (0 - $data->Qty), $data->HPP, $data->SN, 2, 41);

							$this->InsertKartuStok($IDFaktur, $NoTransferStok, $Tanggal, $IDGudangTo, $IDPenjualan, $data->IDBarang, $data->Qty, $data->HPP, $data->SN, 1, 42);

							$SubTotal = $data->Qty * $HPP;

							$this->db->query("UPDATE tb_transfer_stok_detail SET HPP='$HPP', SubTotal='$SubTotal' WHERE NoTransferStok='$NoTransferStok' AND NoUrut='$data->NoUrut' AND IDBarang='$data->IDBarang'");
						} else {
							$StokSimpan = $data->Qty;
							$Order = ($HPPMethod == "FIFO") ? "ASC" : "DESC";

							$queryStok = $this->db->get_results("SELECT * FROM $TableName WHERE IDGudang='$IDGudangFrom' AND IDBarang='$data->IDBarang' AND SisaStok>0 $TableNameCond ORDER BY IDStok $Order");
							if ($queryStok) {
								$TotalQtyToSave = 0;
								foreach ($queryStok as $dataStok) {
									$HPP = $dataStok->HPP;
									$this->UpdateItemTransferStok($IDFaktur, $StokSimpan, $QtyToSave, $dataStok->SisaStok, $HPP, $data, $NoTransferStok, $Tanggal, $IDGudangFrom, $IDGudangTo, $originalRecordUpdated, $IDPenjualan);
									$TotalQtyToSave += $QtyToSave;
									if ($StokSimpan == 0) break;
								}
								$this->CombineKartuStok($NoTransferStok, $Tanggal, $IDGudangFrom, $IDPenjualan, $data->IDBarang, (0 - $TotalQtyToSave), $data->SN, 2, 41);

								$this->CombineKartuStok($NoTransferStok, $Tanggal, $IDGudangTo, $IDPenjualan, $data->IDBarang,  $TotalQtyToSave, $data->SN, 1, 42);
							}
						}
					}
				}
			}

			$TotalHPP = $this->db->get_var("SELECT SUM(SubTotal) FROM tb_transfer_stok_detail WHERE NoTransferStok='$NoTransferStok'");
			$this->db->query("UPDATE tb_transfer_stok SET TotalHPP='$TotalHPP' WHERE NoTransferStok='$NoTransferStok'");
		}
	}

	function UpdateItemTransferStok($IDFaktur, &$StokSimpan, &$QtyToSave, $SisaStok, $HPP, $DetailTransferStok, $NoTransferStok, $Tanggal, $IDGudangFrom, $IDGudangTo, &$OriginalRecordUpdated, $IDPenjualan)
	{
		if ($StokSimpan < $SisaStok) {
			$QtyToSave = $StokSimpan;
		} else {
			$QtyToSave = $SisaStok;
		}

		$this->InsertKartuStok($IDFaktur, $NoTransferStok, $Tanggal, $IDGudangFrom, $IDPenjualan, $DetailTransferStok->IDBarang, (0 - $QtyToSave), $HPP, $DetailTransferStok->SN, 2, 41);

		$this->InsertKartuStok($IDFaktur, $NoTransferStok, $Tanggal, $IDGudangTo, $IDPenjualan, $DetailTransferStok->IDBarang, $QtyToSave, $HPP, $DetailTransferStok->SN, 1, 42);

		$StokSimpan -= $SisaStok;
		if ($StokSimpan < 0) $StokSimpan = 0;

		if (!$OriginalRecordUpdated) {
			$this->db->query("UPDATE tb_transfer_stok_detail SET Qty='$QtyToSave', HPP='$HPP', SubTotal=(HPP*Qty) WHERE NoTransferStok='$NoTransferStok' AND NoUrut='$DetailTransferStok->NoUrut' AND IDBarang='$DetailTransferStok->IDBarang'");

			$OriginalRecordUpdated = true;
		} else {
			$this->db->query("INSERT INTO tb_transfer_stok_detail SET NoTransferStok='$DetailTransferStok->NoTransferStok', NoUrut='" . $DetailTransferStok->NoUrut . "', IDBarang='" . $DetailTransferStok->IDBarang . "', NamaBarang='" . $DetailTransferStok->NamaBarang . "', SN='" . $DetailTransferStok->SN . "', HPP='" . $HPP . "', Qty='" . $QtyToSave . "', SubTotal=(HPP*Qty)");
		}
	}

	function CheckAllowDeleteTransferStokGudang($NoTransferStok)
	{
		$dataTransferStok = $this->db->get_row("SELECT * FROM tb_transfer_stok WHERE NoTransferStok='$NoTransferStok'");
		$IDGudangTo = $dataTransferStok->IDGudangTo;
		$IDPenjualan = $dataTransferStok->IDPenjualan;
		if (!$IDPenjualan) $IDPenjualan = 0;

		$TableName = $IDPenjualan > 0 ? "tb_stok_purchasing" : "tb_stok_gudang";
		$TableNameCond = $IDPenjualan > 0 ? " AND IDPenjualan='$IDPenjualan'" : "";
		$TableNameCond2 = $IDPenjualan > 0 ? " AND b.IDPenjualan='$IDPenjualan'" : "";
		$TableNameStokSN = $IDPenjualan > 0 ? "tb_stok_purchasing_serial_number" : "tb_stok_gudang_serial_number";

		$query = $this->db->get_results("SELECT *, SUM(Qty) AS TOTAL_QTY FROM tb_transfer_stok_detail WHERE NoTransferStok='$NoTransferStok' GROUP BY NoUrut ORDER BY NoUrut");
		if ($query) {
			foreach ($query as $data) {
				if ($data->SN != "") {
					$StokSN = $this->db->get_row("SELECT a.* FROM $TableNameStokSN a, $TableName b WHERE a.IDStok=b.IDStok AND b.IDBarang='$data->IDBarang' AND b.IDGudang='$IDGudangTo' AND a.SN='$data->SN' AND a.Stok>0 $TableNameCond2");
					if (!$StokSN) {
						return false;
					}
				} else {
					$StokAkhir = $this->db->get_var("SELECT SUM(SisaStok) FROM $TableName WHERE IDGudang='$IDGudangTo' AND IDBarang='$data->IDBarang' $TableNameCond");
					if (!$StokAkhir || $StokAkhir < $data->TOTAL_QTY) {
						return false;
					}
				}
			}
		}
		return true;
	}

	function DeleteTransferStokGudang($NoTransferStok)
	{
		$dataTransferStok = $this->db->get_row("SELECT * FROM tb_transfer_stok WHERE NoTransferStok='$NoTransferStok'");
		$IDGudangFrom = $dataTransferStok->IDGudangFrom;
		$IDGudangTo = $dataTransferStok->IDGudangTo;
		$Tanggal = date("Y-m-d");
		$IDPenjualan = $dataTransferStok->IDPenjualan;
		if (!$IDPenjualan) $IDPenjualan = 0;
		$IDFaktur = $dataTransferStok->IDTransferStok;

		$query = $this->db->get_results("SELECT * FROM tb_transfer_stok_detail WHERE NoTransferStok='$NoTransferStok' ORDER BY NoUrut");
		if ($query) {
			foreach ($query as $data) {
				$this->InsertKartuStok($IDFaktur, $NoTransferStok, $Tanggal, $IDGudangFrom, $IDPenjualan, $data->IDBarang,  $data->Qty, $data->HPP, $data->SN, 1, 43);

				$this->InsertKartuStok($IDFaktur, $NoTransferStok, $Tanggal, $IDGudangTo, $IDPenjualan, $data->IDBarang, (0 - $data->Qty), $data->HPP, $data->SN, 2, 44);
			}
		}

		// Combine Kartu Stock
		$query = $this->db->get_results("SELECT *, SUM(Qty) AS TotalQty FROM tb_transfer_stok_detail WHERE NoTransferStok='$NoTransferStok' AND (SN IS NULL OR SN='') GROUP BY `IDBarang`");
		if ($query) {
			foreach ($query as $data) {
				$this->CombineKartuStok($NoTransferStok, $Tanggal, $IDGudangFrom, $IDPenjualan, $data->IDBarang, $data->TotalQty, $data->SN, 1, 43);

				$this->CombineKartuStok($NoTransferStok, $Tanggal, $IDGudangTo, $IDPenjualan, $data->IDBarang, (0 - $data->TotalQty), $data->SN, 2, 44);
			}
		}

		return true;
	}

	function ValidateStokUsage($Cart, $IDGudang, $IDPenjualan = 0, $PrefixMessage)
	{
		$CartAccumulation = array();
		foreach ($Cart as $DataBarang) {
			if (isset($DataBarang) && $DataBarang->IsSerialize == "1") {
				if ($CartAccumulation[$DataBarang->IDBarang] == null) {
					$CartAccumulation[$DataBarang->IDBarang] = new stdClass();
					$CartAccumulation[$DataBarang->IDBarang]->QtyBarang = $DataBarang->QtyBarang;
					$CartAccumulation[$DataBarang->IDBarang]->Limit = $DataBarang->Limit;
					$CartAccumulation[$DataBarang->IDBarang]->TotalAvailableStok = $DataBarang->TotalAvailableStok;
					if ($DataBarang->SNBarang != "") {
						$CartAccumulation[$DataBarang->IDBarang]->SN = array($DataBarang->SNBarang);
					}
				} else {
					$CartAccumulation[$DataBarang->IDBarang]->QtyBarang += $DataBarang->QtyBarang;
					if ($DataBarang->SNBarang != "") {
						if (in_array($DataBarang->SNBarang, $CartAccumulation[$DataBarang->IDBarang]->SN)) {
							return $PrefixMessage . " tidak dapat disimpan. SN " . $DataBarang->SNBarang . " disimpan lebih daru 1 kali.";
						} else {
							array_push($CartAccumulation[$DataBarang->IDBarang]->SN, $DataBarang->SNBarang);
						}
					}
				}
			}
		}

		foreach ($Cart as $data) {
			if (isset($data) && $data->IsSerialize == "1") {
				if ($CartAccumulation[$data->IDBarang]->QtyBarang > $CartAccumulation[$data->IDBarang]->TotalAvailableStok) {
					return $PrefixMessage . " tidak dapat disimpan. Stok untuk barang " . $data->NamaBarang . " hanya tersisa sebesar " . number_format($data->TotalAvailableStok);
				} else {
					$cond = "";
					if ($data->SNBarang != "") {
						$isGudang = strpos($data->SNBarang, "(GUDANG)") > 0 || $IDPenjualan == 0;
						$tableName1 = $isGudang ? "tb_stok_gudang" : "tb_stok_purchasing";
						$tableName2 = $isGudang ? "tb_stok_gudang_serial_number" : "tb_stok_purchasing_serial_number";
						$sn = trim(str_replace("(GUDANG)", "", $data->SNBarang));
						$cond = " AND b.SN='$sn' AND b.Stok>0";
					}
					$cek = $this->db->get_row("select a.* from $tableName1 a, $tableName2 b where a.IDBarang='$data->IDBarang' AND a.IDGudang='$IDGudang' AND a.`IDStok`=b.`IDStok` $cond");
					if (!$cek) {
						return $PrefixMessage . " tidak dapat disimpan. SN " . $data->SNBarang . " tidak terdaftar di dalam sistem.";
					} else if ($data->QtyBarang > $cek->SisaStok) {
						return  $PrefixMessage . " tidak dapat disimpan. Stok untuk barang " . $data->NamaBarang . " hanya tersisa sebesar " . number_format($cek->SisaStok);
					}
				}
			}
		}

		return "";
	}

	function SuratJalan($NoSuratJalan)
	{
		$dataSuratJalan = $this->db->get_row("SELECT * FROM tb_penjualan_surat_jalan WHERE NoSuratJalan='$NoSuratJalan'");
		$IDGudang = $dataSuratJalan->IDGudang;
		$IDPenjualan = $dataSuratJalan->IDPenjualan;
		$Tanggal = $dataSuratJalan->Tanggal;
		$IDFaktur = $dataSuratJalan->IDSuratJalan;

		$query = $this->db->get_results("SELECT a.* FROM tb_penjualan_surat_jalan_detail a, tb_barang b WHERE a.IDBarang=b.IDBarang AND a.NoSuratJalan='$NoSuratJalan' AND b.IsBarang='1' ORDER BY a.NoUrut");
		if ($query) {
			foreach ($query as $data) {
				$this->UpdateHPPSuratJalanDetail($data, $IDGudang, $IDPenjualan, $IDFaktur, $NoSuratJalan, $Tanggal);
			}

			$SubTotal = $this->db->get_var("SELECT SUM(SubTotal) FROM tb_penjualan_surat_jalan_detail WHERE NoSuratJalan='$NoSuratJalan'");
			$PPN = $SubTotal * $dataSuratJalan->PPNPersen / 100;
			$GrandTotal = $SubTotal + $PPN;

			$SubTotalHPP = $this->db->get_var("SELECT SUM(SubTotalHPP) FROM tb_penjualan_surat_jalan_detail WHERE NoSuratJalan='$NoSuratJalan'");

			$this->db->query("UPDATE tb_penjualan_surat_jalan SET TotalNilai='$SubTotal', TotalNilai2='$SubTotal', PPN='$PPN', GrandTotal='$GrandTotal', TotalHPP='$SubTotalHPP', TotalHPPReal='$SubTotalHPP', TotalMargin=(GrandTotal-TotalHPP) WHERE NoSuratJalan='$NoSuratJalan'");
		}
	}

	function UpdateHPPSuratJalanDetail($data, $IDGudang, $IDPenjualan, $IDFaktur, $NoSuratJalan, $Tanggal, $IsStokGudang = false)
	{
		$IsPaket = $this->db->get_results("SELECT * FROM tb_barang_child WHERE IDParent='$data->IDBarang'");
		if (!$IsPaket) {

			$HPPMethod = $this->db->get_var("SELECT HPPMethod FROM tb_barang WHERE IDBarang='$data->IDBarang'");

			if ($data->SN != "") {
				// Handle Stock with Serial Number
				$IsGudang = $IsStokGudang || strpos($data->SN, "(GUDANG)");
				$TableName = $IsGudang ? "tb_stok_gudang" : "tb_stok_purchasing";
				$TableNameStokSN = $IsGudang ? "tb_stok_gudang_serial_number" : "tb_stok_purchasing_serial_number";
				$StokFrom = $IsGudang ? 0 : 1;

				$data->SN = trim(str_replace("(GUDANG)", "", $data->SN));

				$SisaStok = $this->db->get_var("SELECT SUM(SisaStok) FROM $TableName WHERE IDGudang='$IDGudang' AND IDBarang='$data->IDBarang'");
				if (!$SisaStok) $SisaStok = 0;

				if ($SisaStok >= $data->Qty) {
					$stokFound = false;
					$IDStok = 0;
					if ($HPPMethod == "AVG") {
						// Get HPP for AVG Method
						$HPP = $this->GetHPPStokAVG($IDGudang, $data->IDBarang, ($IsGudang ? 0 : $IDPenjualan));
						$stokFound = true;
					} else {
						// Get HPP for FIFO/LIFO; Use HPP for specific Serial Number
						$Order = ($HPPMethod == "FIFO") ? "ASC" : "DESC";

						$queryStok = $this->db->get_results("SELECT * FROM $TableName WHERE IDGudang='$IDGudang' AND IDBarang='$data->IDBarang' AND SisaStok>0 ORDER BY IDStok $Order");
						if ($queryStok) {
							foreach ($queryStok as $dataStok) {
								$stokSerialNumber = $this->db->get_row("SELECT * FROM $TableNameStokSN WHERE IDStok='$dataStok->IDStok' AND SN='$data->SN' AND Stok>0");
								if ($stokSerialNumber) {
									$HPP = $dataStok->HPP;
									$IDStok = $dataStok->IDStok;
									$stokFound = true;
									break;
								}
							}
						}
					}

					if ($stokFound) {
						$this->InsertKartuStok($IDFaktur, $NoSuratJalan, $Tanggal, $IDGudang, ($IsGudang ? 0 : $IDPenjualan), $data->IDBarang, (0 - $data->Qty), $HPP, $data->SN, 2, 6);

						$this->db->query("UPDATE tb_penjualan_surat_jalan_detail SET SN='$data->SN', HPP='$HPP', HPPReal='$HPP', SubTotalHPP=(HPP*Qty), StokFrom='$StokFrom', IDStok='$IDStok' WHERE IDetail='$data->IDetail'");
					}
				}
			} else {
				// Handle Non-Serial Number Stock
				$StokSimpan = $data->Qty;
				$originalRecordUpdated = false;

				// Prioritise to deduct the Stock Purchasing
				if ($HPPMethod == "AVG") {
					$stokPurchasing = $this->db->get_row("SELECT * FROM tb_stok_purchasing WHERE IDGudang='$IDGudang' AND IDBarang='$data->IDBarang' AND IDPenjualan='$IDPenjualan'");

					if ($stokPurchasing) {
						$HPP = $this->GetHPPStokAVG($IDGudang, $data->IDBarang, $IDPenjualan);
						$this->UpdateItemSuratJalan($IDFaktur, $StokSimpan, $QtyToSave, $stokPurchasing->SisaStok, $HPP, $data, $NoSuratJalan, $Tanggal, $IDGudang, $originalRecordUpdated, 1, $IDPenjualan);
					}
				} else {
					$Order = ($HPPMethod == "FIFO") ? "ASC" : "DESC";

					$queryStok = $this->db->get_results("SELECT * FROM tb_stok_purchasing WHERE IDGudang='$IDGudang' AND IDBarang='$data->IDBarang' AND IDPenjualan='$IDPenjualan' AND SisaStok>0 ORDER BY IDStok $Order");
					if ($queryStok) {
						$TotalQtyToSave = 0;
						foreach ($queryStok as $dataStok) {
							$HPP = $dataStok->HPP;
							$this->UpdateItemSuratJalan($IDFaktur, $StokSimpan, $QtyToSave, $dataStok->SisaStok, $HPP, $data, $NoSuratJalan, $Tanggal, $IDGudang, $originalRecordUpdated, 1, $IDPenjualan, $dataStok->IDStok);
							$TotalQtyToSave += $QtyToSave;
							if ($StokSimpan == 0) break;
						}
						$this->CombineKartuStok($NoSuratJalan, $Tanggal, $IDGudang, $IDPenjualan, $data->IDBarang, (0 - $TotalQtyToSave), $data->SN, 2, 6);
					}
				}

				if ($StokSimpan > 0) {
					if ($HPPMethod == "AVG") {
						$stokGudang = $this->db->get_row("SELECT * FROM tb_stok_gudang WHERE IDGudang='$IDGudang' AND IDBarang='$data->IDBarang'");

						if ($stokGudang) {
							$HPP = $this->GetHPPStokAVG($IDGudang, $data->IDBarang, 0);
							$this->UpdateItemSuratJalan($IDFaktur, $StokSimpan, $QtyToSave, $stokGudang->SisaStok, $HPP, $data, $NoSuratJalan, $Tanggal, $IDGudang, $originalRecordUpdated, 0, 0);
						}
					} else {
						$Order = ($HPPMethod == "FIFO") ? "ASC" : "DESC";

						$queryStok = $this->db->get_results("SELECT * FROM tb_stok_gudang WHERE IDGudang='$IDGudang' AND IDBarang='$data->IDBarang' AND SisaStok>0 ORDER BY IDStok $Order");
						if ($queryStok) {
							$TotalQtyToSave = 0;
							foreach ($queryStok as $dataStok) {
								$HPP = $dataStok->HPP;
								$this->UpdateItemSuratJalan($IDFaktur, $StokSimpan, $QtyToSave, $dataStok->SisaStok, $HPP, $data, $NoSuratJalan, $Tanggal, $IDGudang, $originalRecordUpdated, 0, 0, $dataStok->IDStok);
								$TotalQtyToSave += $QtyToSave;
								if ($StokSimpan == 0) break;
							}
							$this->CombineKartuStok($NoSuratJalan, $Tanggal, $IDGudang, 0, $data->IDBarang, (0 - $TotalQtyToSave), $data->SN, 2, 6);
						}
					}
				}
			}
		}
	}

	function UpdateItemSuratJalan($IDFaktur, &$StokSimpan, &$QtyToSave, $SisaStok, $HPP, $DetailSuratJalan, $NoSuratJalan, $Tanggal, $IDGudang, &$OriginalRecordUpdated, $StokFrom, $IDPenjualan, $IDStok = "0")
	{
		if ($StokSimpan < $SisaStok) {
			$QtyToSave = $StokSimpan;
		} else {
			$QtyToSave = $SisaStok;
		}

		$this->InsertKartuStok($IDFaktur, $NoSuratJalan, $Tanggal, $IDGudang, $IDPenjualan, $DetailSuratJalan->IDBarang, (0 - $QtyToSave), $HPP, $DetailSuratJalan->SN, 2, 6);

		$StokSimpan -= $SisaStok;
		if ($StokSimpan < 0) $StokSimpan = 0;

		if (!$OriginalRecordUpdated) {
			$this->db->query("UPDATE tb_penjualan_surat_jalan_detail SET Qty='$QtyToSave', HPP='$HPP', HPPReal='$HPP', Margin=(HargaDiskon-HPP), SubTotal=(HargaDiskon*Qty), SubTotalHPP=(HPP*Qty), SubTotalMargin=(SubTotal-SubTotalHPP), StokFrom='$StokFrom', IDStok='$IDStok' WHERE IDetail='$DetailSuratJalan->IDetail'");

			$OriginalRecordUpdated = true;
		} else {
			$this->db->query("INSERT INTO tb_penjualan_surat_jalan_detail SET NoSuratJalan='$DetailSuratJalan->NoSuratJalan', NoUrut='$DetailSuratJalan->NoUrut', IDBarang='$DetailSuratJalan->IDBarang', NamaBarang='$DetailSuratJalan->NamaBarang', Harga='$DetailSuratJalan->Harga', Diskon='$DetailSuratJalan->Diskon', HargaDiskon='$DetailSuratJalan->HargaDiskon', IsPaket='$DetailSuratJalan->IsPaket', IsChild='$DetailSuratJalan->IsChild', Garansi='$DetailSuratJalan->Garansi', IDStok='$IDStok', StokFrom='$StokFrom', IsInstallasi='$DetailSuratJalan->IsInstallasi', Qty='$QtyToSave', HPP='$HPP', HPPReal='$HPP', Margin=(HargaDiskon-HPP), SubTotal=(HargaDiskon*Qty), SubTotalHPP=(HPP*Qty), SubTotalMargin=(SubTotal-SubTotalHPP)");
		}
	}

	function DeleteSuratJalan($NoSuratJalan)
	{
		$dataTransferStok = $this->db->get_row("SELECT * FROM tb_penjualan_surat_jalan WHERE NoSuratJalan='$NoSuratJalan'");
		$IDGudang = $dataTransferStok->IDGudang;
		$IDPenjualan = $dataTransferStok->IDPenjualan;
		$Tanggal = date("Y-m-d");
		$IDFaktur = $dataTransferStok->IDSuratJalan;

		// Return stock based on stock distributions
		$query = $this->db->get_results("SELECT * FROM tb_penjualan_surat_jalan_detail WHERE NoSuratJalan='$NoSuratJalan' ORDER BY NoUrut");
		if ($query) {
			foreach ($query as $data) {
				$IsPaket = $this->db->get_results("SELECT * FROM tb_barang_child WHERE IDParent='$data->IDBarang'");
				if (!$IsPaket) {
					$this->InsertKartuStok($IDFaktur, $NoSuratJalan, $Tanggal, $IDGudang, ($data->StokFrom == "0" ? 0 : $IDPenjualan), $data->IDBarang,  $data->Qty, $data->HPP, $data->SN, 1, 36);
				}
			}
		}

		// Combine Kartu Stock
		$query = $this->db->get_results("SELECT *, SUM(Qty) AS TotalQty FROM tb_penjualan_surat_jalan_detail WHERE NoSuratJalan='$NoSuratJalan' AND (SN IS NULL OR SN='') GROUP BY `IDBarang`");
		if ($query) {
			foreach ($query as $data) {
				$IsPaket = $this->db->get_results("SELECT * FROM tb_barang_child WHERE IDParent='$data->IDBarang'");
				if (!$IsPaket) {
					$this->CombineKartuStok($NoSuratJalan, $Tanggal, $IDGudang, ($data->StokFrom == "0" ? 0 : $IDPenjualan), $data->IDBarang, $data->TotalQty, $data->SN, 1, 36);
				}
			}
		}

		return true;
	}

	function IDDate($date)
	{
		$exp = explode("-", $date);
		return $exp[2] . "/" . $exp[1] . "/" . $exp[0];
	}

	function getStokAndHPPPaket($IDBarang, $StokTable, $KartuStokTable, $OtherConditions = "")
	{
		$stok = 0;
		$hpp = 0;
		$break = false;
		$queryChild = $this->db->get_results("SELECT * FROM tb_barang_child WHERE IDParent='$IDBarang' AND IDBarang>0");
		if ($queryChild) {
			foreach ($queryChild as $child) {
				if ($break) continue;

				$stokChild = $this->db->get_var("SELECT SUM(SisaStok) FROM $StokTable WHERE IDBarang='" . $child->IDBarang . "' $OtherConditions");
				if (!$stokChild || $stokChild == 0) {
					$break = true;
					$stok = 0;
					$hpp = 0;
					continue;
				} else if (($stok == 0 && $stokChild > 0) || $stokChild < $stok) {
					$stok = $stokChild;
				}

				$hppChild = $this->db->get_var("SELECT SUM(SubTotalHPP)/SUM(StokPenyesuaian) FROM $KartuStokTable WHERE IDBarang='" . $child->IDBarang . "' $OtherConditions");
				if (!$hppChild) $hppChild = 0;
				$hpp += $hppChild;
			}
		}
		return array("Stok" => $stok, "HPP" => $hpp);
	}
}
