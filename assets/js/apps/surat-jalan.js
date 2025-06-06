tripApp.controller('SuratJalanController', function ($scope, $rootScope, $routeParams, $route, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();
  $scope.nama = "";
  $scope.material_bantu = "";

  $scope.kode_proyek = "";
  $scope.filterstatus = "";
  $scope.activeMenu = '';

  if (typeof $routeParams.idPenjualan !== 'undefined') {
    $scope.idPenjualan = $routeParams.idPenjualan;
  } else
    $scope.idPenjualan = "";

  $scope.getAllData = function () {
    $http.get('api/surat-jalan/surat-jalan.php?act=DataList&datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&idPenjualan=' + $scope.idPenjualan + '&status=' + $scope.filterstatus + '&nama=' + $scope.nama + '&material_bantu=' + $scope.material_bantu).success(function (data, status) {
      $scope.data_pengiriman = data.pengiriman;
      console.log($scope.data_pengiriman);
      $scope.DetailPenjualan = data.penjualan;
      /*$scope.TotalQtyPenjualan = data.penjualan.TotalItem;
      $scope.GrandTotal = data.penjualan.GrandTotal;
      $scope.GrandTotalPengiriman = data.grandTotalPengiriman;
      $scope.TotalQtyPengiriman = data.totalQtyPengiriman;
      $scope.totalItemPenjualan = data.totalItemPenjualan;*/
    });
  };

  $scope.getAllData();

  $scope.doFilter = function (a) {
    $scope.filterstatus = a;
    $scope.activeMenu = a;
    $scope.refreshData();
  }

  $scope.refreshData = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getAllData();
  }


  $scope.removeRow = function (val, noSurat) {
    $scope.idSurat = val;
    $scope.noSurat = noSurat;
    $('#modalDelete').modal('show');
  }

  $scope.submitFormDelete = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/surat-jalan/surat-jalan.php?act=Delete',
        data: $.param({
          'idr': $scope.idSurat,
          'remark': $scope.remark,
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data pengiriman berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $scope.refreshData();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data pengiriman gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
        $scope.closeModalDelete();
      });
    }
  };
  $scope.closeModalDelete = function () {
    $('#modalDelete').modal('hide');
    $('.modal-backdrop').remove();
  }

  $scope.doPrint = function (IDSuratJalan) {
    window.open($rootScope.baseURL + 'api/print/print-pengiriman-barang.php?id=' + IDSuratJalan, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }

  $scope.doPrint2 = function () {
    window.open($rootScope.baseURL + 'api/print/print-data-pengiriman-barang.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('RekapSuratJalanController', function ($scope, $routeParams, $route, $http, ngToast) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });
  $scope.datestart = "";
  $scope.dateend = "";
  $scope.kode_proyek = "";
  $scope.filterstatus = "";
  $scope.activeMenu = '';

  if (typeof $routeParams.idProyek !== 'undefined') {
    $scope.kode_proyek = $routeParams.idProyek;
  }

  $scope.getAllData = function () {
    $http.get('api/pengiriman-barang/pengiriman-barang.php?act=DataList&datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&kode_proyek=' + $scope.kode_proyek + '&status=' + $scope.filterstatus).success(function (data, status) {
      $scope.data_pengiriman = data.pengiriman;
      $scope.data_proyek = data.proyek;

      $scope.all = data.all;
      $scope.new = data.new;
      $scope.success = data.success;
      $scope.rejected = data.rejected;
    });
  };

  $scope.getAllData();

  $scope.doFilter = function (a) {
    $scope.filterstatus = a;
    $scope.activeMenu = a;
    $scope.refreshData();
  }

  $scope.refreshData = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getAllData();
  }

  $scope.idPengiriman = '';

  $scope.showModal = function (a) {
    $scope.idPengiriman = a;
    $scope.rfidcode = "";
    $('#myModal').modal('show');
    $('#myModal').children('.modal-dialog').removeClass('modal-lg');
    $('#myModal').on('shown.bs.modal', function () {
      $('#rfidcode').focus();
    });
  };

  $scope.afterScan = function (keyEvent) {
    if (keyEvent.which === 13)
      $scope.updatePenerimaanBarang();
  }

  $scope.updatePenerimaanBarang = function () {
    $http.get('api/pengiriman-barang/pengiriman-barang.php?act=PenerimaanBarang&key=' + $scope.rfidcode + '&idPengiriman=' + $scope.idPengiriman).success(function (data, status) {
      $('#myModal').modal('hide');
      if (data === "0") {
        ngToast.create({
          className: 'danger',
          content: 'RFID tidak terdaftar di dalam sistem! <i class="fa fa-remove"></i>'
        });
      } else if (data === "2") {
        ngToast.create({
          className: 'danger',
          content: 'Data Pengiriman gagal diupdate. Silahkan coba kembali lagi.. <i class="fa fa-remove"></i>'
        });
      } else {
        ngToast.create({
          className: 'success',
          content: 'Data Pengiriman berhasil diupdate! <i class="fa fa-remove"></i>'
        });
        $scope.refreshData();
      }
    });
  }

  $scope.rejectRow = function (val) {
    if (confirm("Anda yakin ingin membatalkan pengiriman ini?")) {
      $http({
        method: "POST",
        url: 'api/pengiriman-barang/reject.php',
        data: $.param({
          'idr': val
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data pengiriman berhasil dibatalkan <i class="fa fa-remove"></i>'
          });
          $scope.refreshData();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data pengiriman gagal dibatalkan. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }
});

tripApp.controller('SuratJalanNewController', function ($scope, $routeParams, $rootScope, $route, $http, ngToast) {
  var cartArray = [];
  var noUrut = 0;
  $scope.displayCartArray = [];
  $scope.sn = [];
  $scope.totalitem = 0;
  $scope.totalHPP = 0;
  $scope.totaljenisitem = 0;
  $scope.statusPengiriman = "Baru";
  $scope.material_bantu = '0';

  $scope.idPenjualan = $routeParams.idPenjualan;
  $scope.no_penjualan = "";
  $scope.id_gudang = "";

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.getdata = function () {
    $http.get('api/surat-jalan/surat-jalan.php?act=LoadAllRequirement&no_penjualan=' + $scope.spb + '&material_bantu=' + $scope.material_bantu + '&id_gudang=' + $scope.id_gudang).success(function (data, status) {
      $scope.no_penjualan = data.penjualan;
      $scope.pelanggan = data.pelanggan;
      $scope.data_barang = data.barang;
      $scope.data_penjualan = data.penjualan;
      $scope.data_gudang = data.gudang;
      if ($scope.id_gudang === "") {
        $scope.id_gudang = data.gudang.filter(x => x.IsDefault === '1')[0].IDGudang;
      }
    });
  };

  $scope.getdata();

  $scope.changeSuratJalanType = function () {
    $scope.getdata();
    cartArray = [];
    $scope.displayCart();
  }

  $scope.changeSPH = function () {
    $scope.spb = $('#spb').val();
    $scope.getdata();
  }

  $scope.loadListPO = function () {
    $scope.po = "";
    $scope.getdata();
  }

  $scope.displayDetailPO = function () {
    $http.get('api/pengiriman-barang/pengiriman-barang.php?act=LoadDetailPO&idPO=' + $scope.po).success(function (data, status) {
      cartArray = [];
      cartArray = data;
      console.log(cartArray);
      $scope.displayCart();
    });
  }

  $scope.usrlogin = $rootScope.userLoginName;
  $scope.tanggal = $rootScope.currentDateID;

  $("#kode").on("change", function (e) {
    $scope.kode = this.value;
    $scope.changeKode();
  });

  $scope.changeKode = function () {
    if ($scope.kode != "") {
      $scope.serialnumber = "";
      $("#serialnumber").select2("val", "");
      $scope.anama = $scope.data_barang[$scope.kode].Nama;
      $scope.aidbarang = $scope.data_barang[$scope.kode].IDBarang;
      $scope.isserialize = $scope.data_barang[$scope.kode].IsSerialize;
      $scope.sn = $scope.data_barang[$scope.kode].SerialNumberArray;

      $scope.limit = $scope.data_barang[$scope.kode].Limit;
      $scope.totalAvailableStok = $scope.data_barang[$scope.kode].TotalAvailableStok;
      $scope.HPP = $scope.data_barang[$scope.kode].HPP;
      $scope.sisa = $scope.data_barang[$scope.kode].Sisa;

      $scope.Harga = $scope.data_barang[$scope.kode].Harga;
      $scope.HPPReal = $scope.data_barang[$scope.kode].HPPReal;

      $scope.IsPaket = $scope.data_barang[$scope.kode].IsPaket;
      $scope.IsChild = $scope.data_barang[$scope.kode].IsChild;
      $scope.IDParent = $scope.data_barang[$scope.kode].IDParent;
      $scope.StokGudang = $scope.data_barang[$scope.kode].StokGudang;
      $scope.StokPurchasing = $scope.data_barang[$scope.kode].StokPurchasing;

      $('#nama_barang').val($scope.anama);
      $('#limit').val($scope.limit);

      $scope.qty = ($scope.material_bantu === '0') ? parseInt($scope.limit) : 1;
      $scope.serialnumber = "";

      if ($scope.data_barang[$scope.kode].IsSerialize == "1") {
        $scope.disabledQty = true;
        $scope.disabledSN = false;
      } else {
        $scope.disabledQty = false;
        $scope.disabledSN = true;
      }
    } else {
      $('#nama_barang').val("");
      $scope.qty = "";
      $scope.serialnumber = "";
      $scope.limit = "";
    }
  }

  $scope.noMoreThanLimit = function () {
    if ($scope.qty > $scope.limit)
      $scope.qty = $scope.limit;
  }

  $scope.changeIsInstallasi = function (a) {
    var isInstallasi = $('#isInstallasi' + a).is(':checked') ? '1' : $('#isInstallasi' + a).val();
    cartArray[a]['IsInstallasi'] = parseInt(isInstallasi);
    console.log(cartArray);
  }

  $scope.addtocart = function (supress = false) {
    if ($scope.aidbarang > 0 && $scope.qty > 0) {
      var IDBarang = $scope.aidbarang;
      var NamaBarang = $scope.anama;
      var Qty = $scope.qty;
      var Limit = $scope.limit;
      var TotalAvailableStok = $scope.totalAvailableStok;
      var HPP = $scope.HPP;
      var IDParent = $scope.IDParent;

      var qtyAll = cartArray.filter(x => x.IDBarang === IDBarang && x.IDParent === IDParent).reduce((qtyAll, y) => qtyAll + y.QtyBarang, 0);
      var Sisa = $scope.sisa - (qtyAll + parseFloat(Qty));

      var SN = $scope.serialnumber;
      var IsSerialize = $scope.isserialize;
      if (IsSerialize !== "0") {
        if (!SN) {
          if (!supress) alert('Silahkan pilih Serial Number terlebih dahulu.');
          return;
        }

        var snIsAdded = cartArray.find(x => x && x.SNBarang && x.SNBarang === SN);
        if (snIsAdded) {
          if (!supress) alert('Serial Number telah diinput kedalam Daftar Barang. Silahkan pilih Serial Number lain.');
          return;
        }
      }

      var updated = false;

      var Garansi = $scope.garansi;
      var IsPaket = $scope.IsPaket;
      var IsChild = $scope.IsChild;
      var IDParent = $scope.IDParent;
      var StokGudang = $scope.StokGudang;
      var StokPurchasing = $scope.StokPurchasing;

      var Harga = $scope.Harga;
      var HPPReal = $scope.HPPReal;
      var SubTotal = parseInt(Qty) * parseInt(Harga);
      var Margin = parseInt(Harga) - parseInt(HPP);
      var SubTotalMargin = parseInt(Qty) * parseInt(Margin);
      var SubTotalHPP = parseInt(Qty) * parseInt(HPP);

      if (Sisa >= 0 && $scope.sisa > 0) {

        if (IsSerialize === "0") {
          var entry = cartArray.find(x => x && x.IDBarang && x.IDBarang === IDBarang && x.IDParent === IDParent);
          if (entry) {
            updated = true;
            entry["QtyBarang"] += parseFloat(Qty);
            if (entry["QtyBarang"] > entry["Limit"]) entry["QtyBarang"] = entry["Limit"];
          }
        }

        if (!updated) {
          cartArray[noUrut] = { NoUrut: noUrut, IDBarang: IDBarang, NamaBarang: NamaBarang, QtyBarang: Qty, SNBarang: SN, IsSerialize: IsSerialize, Limit: Limit, TotalAvailableStok: TotalAvailableStok, Sisa: Sisa, HPP: HPP, SubTotal: SubTotal, IsPaket: IsPaket, IsChild: IsChild, IDParent: IDParent, Garansi: Garansi, Harga: Harga, Margin: Margin, SubTotalMargin: SubTotalMargin, SubTotalHPP: SubTotalHPP, HPPReal: HPPReal, StokGudang: StokGudang, StokPurchasing: StokPurchasing, IsInstallasi: 0 };
          noUrut += 1;
        }

        $('#nama_barang').val('');
        $scope.qty = "";
        $scope.serialnumber = "";
        $scope.isserialize = "";
        $scope.anama = "";
        $scope.aidbarang = 0;
        $scope.kode = "";
        $scope.limit = "";
        $scope.garansi = "";
        $scope.IsPaket = "";
        $scope.sisa = "";

        $scope.displayCart();
      } else {
        if (!supress) alert('Anda tidak dapat memasukan Qty melebihi Limit dari Penjualan!');
      }
    } else {
      if (!supress) alert('Ada sesuatu yang salah. Silahkan ulang pilih data barang dan pastikan Qty Barang tidak sama dengan NOL!');
    }
  }

  $scope.displayCart = function () {
    console.log(cartArray);
    var lastIDBarang = "";

    function sortFunction(a, b) {
      if (a['NoUrut'] == b['NoUrut']) {
        return 0;
      } else {
        return (a['NoUrut'] < b['NoUrut']) ? -1 : 1;
      }
    }
    $scope.totalitem = 0;
    $scope.totaljenisitem = 0;
    $scope.totalHPP = 0;
    $scope.totalNilai = 0;
    $scope.totalMargin = 0;
    $scope.displayCartArray = JSON.parse(JSON.stringify(cartArray.filter(x => x)));
    $scope.displayCartArray = $scope.displayCartArray.sort(sortFunction);
    $scope.displayCartArray.forEach(function (entry) {
      if (entry["IsPaket"] !== "1")
        $scope.totalitem += parseFloat(entry["QtyBarang"]);

      $scope.totalHPP += parseFloat(entry["SubTotalHPP"]);
      $scope.totalNilai += parseFloat(entry["SubTotal"]);
      $scope.totalMargin += parseFloat(entry["SubTotalMargin"]);
      if (lastIDBarang != entry["IDBarang"]) {
        $scope.totaljenisitem++;
        lastIDBarang = entry["IDBarang"];
      }
    });

    setTimeout(() => {
      $('.datepick').datepicker({
        format: 'dd/mm/yyyy',
        showOtherMonths: true,
        selectOtherMonths: true,
        autoclose: true
      });
    }, 1000);
  }

  $scope.changeQty = function (a) {
    var QtyVal = parseFloat($('#QtyBarang' + a).val());
    var totalAvailableStok = parseFloat(cartArray[a]['StokPurchasing']) + parseFloat(cartArray[a]['StokGudang']);
    if (QtyVal > cartArray[a]['Limit']) QtyVal = cartArray[a]['Limit'];
    if (QtyVal > totalAvailableStok) QtyVal = totalAvailableStok;
    cartArray[a]['QtyBarang'] = QtyVal;
    $('#QtyBarang' + a).val(QtyVal);
    $scope.displayCart();
  }

  $scope.changeSN = function (a) {
    var SNVal = $('#SNBarang' + a).val();
    cartArray[a]['SNBarang'] = SNVal;
    $scope.displayCart();
  }

  $scope.changeGaransi = function (a) {
    var GaransiBarang = $('#Garansi' + a).val();
    cartArray[a]['Garansi'] = GaransiBarang;
    $scope.displayCart();
  }

  $scope.removeRow = function (a) {
    delete cartArray[a];
    $scope.displayCart();

    return false;
  };

  $scope.showbarcodemodal = function () {
    if (!$scope.spb) {
      alert('Silahkan pilih SPB terlebih dahulu.');
    } else {
      $('#modalBarcode').modal('show');
      $('#barcodes').val('');
      setTimeout(() => {
        $('#barcodes').focus();
      }, 500);
    }
  }

  $scope.submitFormBarcode = function () {
    if ($scope.barcodes) {
      var barcodes = $scope.barcodes.split(/\r?\n/);
      for (var barcode of barcodes) {
        var codes = barcode.split(',');
        cartArray.forEach(function (cart) {
          var barang = $scope.data_barang.filter(x => x.IDBarang === cart.IDBarang && x.LibCode === codes[0])[0];
          if (barang) {
            var sn = barang.SerialNumberArray.filter(x => x.SN.startsWith(codes[1]))[0];
            if (sn) {
              cart.SNBarang = sn.SN;
            }
          }
        });
      }

      for (var barcode of barcodes) {
        var codes = barcode.split(',');
        var snAdded = cartArray.filter(x => x.SNBarang.startsWith(codes[1]))[0];
        if (!snAdded) {
          var barang = $scope.data_barang.filter(x => x.LibCode === codes[0])[0];
          if (barang) {
            var sn = barang.SerialNumberArray.filter(x => x.SN.startsWith(codes[1]))[0];
            if (sn) {
              $scope.anama = barang.Nama;
              $scope.aidbarang = barang.IDBarang;
              $scope.isserialize = barang.IsSerialize;
              $scope.sn = barang.SerialNumberArray;

              $scope.limit = barang.Limit;
              $scope.HPP = barang.HPP;
              $scope.sisa = barang.Sisa;

              $scope.Harga = barang.Harga;
              $scope.HPPReal = barang.HPPReal;

              $scope.IsPaket = barang.IsPaket;
              $scope.IsChild = barang.IsChild;
              $scope.IDParent = barang.IDParent;
              $scope.StokGudang = barang.StokGudang;
              $scope.StokPurchasing = barang.StokPurchasing;

              $scope.qty = ($scope.material_bantu === '0') ? parseInt($scope.limit) : 1;
              $scope.serialnumber = sn.SN;
              $scope.addtocart(true);
            }
          }
        }
      }

      $('#modalBarcode').modal('hide');
      $('.modal-backdrop').remove();
    }
  }

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/surat-jalan/surat-jalan.php?act=InsertNew',
        data: $.param({
          'idPenjualan': $scope.spb,
          'no_penjualan': $scope.spb,
          'id_gudang': $scope.id_gudang,
          'tanggal': $scope.tanggal,
          'totalqty': $scope.totalitem,
          'totalHPP': $scope.totalHPP,
          'totalNilai': $scope.totalNilai,
          'totalMargin': $scope.totalMargin,
          'keterangan': $scope.keterangan,
          'completeSPB': $scope.completeSPB,
          'material_bantu': $scope.material_bantu,
          'cart': JSON.stringify(cartArray)
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data.res == "1") {
          ngToast.create({
            className: 'success',
            content: data.mes + ' <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-surat-jalan';
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: data.mes + ' <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('SuratJalanDetailController', function ($rootScope, $scope, $route, $routeParams, $http, ngToast) {
  $http.get('api/surat-jalan/surat-jalan.php?act=Detail&id=' + $routeParams.idSuratJalan).success(function (data, status) {
    $scope.idPenjualan = data.master.idPenjualan;
    $scope.no_pengiriman = data.master.NoSuratJalan;
    $scope.no_penjualan = data.master.NoPenjualan;
    $scope.tanggal = data.master.Tanggal;
    $scope.pelanggan = data.master.Pelanggan;
    $scope.usrlogin = data.master.usrlogin;
    $scope.keterangan = data.master.Keterangan;
    $scope.total_qty = data.master.Total;
    $scope.totalHPP = data.master.TotalHPP;
    $scope.totalMargin = data.master.TotalMargin;

    $scope.totalNilai = data.master.TotalNilai;
    $scope.diskon = data.master.Diskon;
    $scope.diskonPersen = data.master.DiskonPersen;
    $scope.totalNilai2 = data.master.TotalNilai2;
    $scope.ppn = data.master.PPN;
    $scope.ppnPersen = data.master.PPNPersen;
    $scope.grandtotal = data.master.GrandTotal;

    $scope.data_detail = data.detail;

    $scope.deleted_by = data.master.deleted_by;
    $scope.deleted_date = data.master.deleted_date;
    $scope.deleted_remark = data.master.deleted_remark;
  });

  $scope.changeIsInstallasi = function (a) {
    var isInstallasi = $('#isInstallasi' + a).is(':checked') ? '1' : $('#isInstallasi' + a).val();
    $scope.data_detail[a].IsInstallasi = parseInt(isInstallasi);

    $http({
      method: "POST",
      url: 'api/surat-jalan/surat-jalan.php?act=UpdateIsInstalasi',
      data: $.param({
        'IDetail': $scope.data_detail[a].IDetail,
        'IsInstallasi': $scope.data_detail[a].IsInstallasi
      }),
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    }).success(function (data, status) {
      if (data == "1") {
        ngToast.create({
          className: 'success',
          content: 'Data berhasil disimpan <i class="fa fa-remove"></i>'
        });
      } else {
        $scope.processing = false;
        ngToast.create({
          className: 'danger',
          content: 'Data gagal disimpan. Silahkan coba kembali nanti. <i class="fa fa-remove"></i>'
        });
      }
    });
  }

  $scope.doPrint = function () {
    window.open($rootScope.baseURL + 'api/print/print-pengiriman-barang.php?id=' + $routeParams.idSuratJalan, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});
