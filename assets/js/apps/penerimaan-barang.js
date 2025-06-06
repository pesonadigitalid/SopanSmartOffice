tripApp.controller('PenerimaanBarangController', function ($rootScope, $scope, $q, $routeParams, $route, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });
  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();
  $scope.kode_proyek = "";
  $scope.supplier = "";

  $scope.getdata = function () {
    $http.get('api/penerimaan-barang/penerimaan-barang.php?act=DisplayData&datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&supplier=' + $scope.supplier + '&ppn=false').success(function (data, status) {
      $scope.data_penerimaan = data.penerimaan;
      $scope.data_supplier = data.supplier;
    });
  };

  $scope.getdata();

  $scope.refreshData = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }

  $scope.removeRow = function (val, idPenerimaan) {
    $scope.idPenerimaan = val;
    $scope.noPenerimaan = idPenerimaan;
    $('#modalDelete').modal('show');
  }

  $scope.submitFormDelete = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/penerimaan-barang/penerimaan-barang.php?act=Delete',
        data: $.param({
          'idr': $scope.idPenerimaan,
          'remark': $scope.remark,
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data penerimaan barang berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else if (data == "2") {
          ngToast.create({
            className: 'danger',
            content: 'Data Penerimaan Barang tidak dapat dihapus karena stok telah digunakan... <i class="fa fa-remove"></i>'
          });
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data penerimaan barang gagal dihapus. Silahkan coba kembali... <i class="fa fa-remove"></i>'
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

  $scope.doPrint2 = function () {
    window.open($rootScope.baseURL + 'api/print/print-data-penerimaan-barang.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&supplier=' + $scope.supplier, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('PenerimaanBarangPPNController', function ($rootScope, $scope, $q, $routeParams, $route, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });
  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();
  $scope.kode_proyek = "";
  $scope.supplier = "";

  $scope.getdata = function () {
    $http.get('api/penerimaan-barang/penerimaan-barang.php?act=DisplayData&datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&supplier=' + $scope.supplier + '&ppn=true').success(function (data, status) {
      $scope.data_penerimaan = data.penerimaan;
      $scope.data_supplier = data.supplier;
    });
  };

  $scope.getdata();

  $scope.refreshData = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }

  $scope.removeRow = function (val, idPenerimaan) {
    $scope.idPenerimaan = val;
    $scope.noPenerimaan = idPenerimaan;
    $('#modalDelete').modal('show');
  }

  $scope.submitFormDelete = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/penerimaan-barang/penerimaan-barang.php?act=Delete',
        data: $.param({
          'idr': $scope.idPenerimaan,
          'remark': $scope.remark,
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data penerimaan barang berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else if (data == "2") {
          ngToast.create({
            className: 'danger',
            content: 'Data Penerimaan Barang tidak dapat dihapus karena stok telah digunakan... <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data penerimaan barang gagal dihapus. Silahkan coba kembali... <i class="fa fa-remove"></i>'
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

  $scope.doPrint2 = function () {
    window.open($rootScope.baseURL + 'api/print/print-data-penerimaan-barang.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&supplier=' + $scope.supplier, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('PenerimaanBarangNewController', function ($rootScope, $scope, $q, $routeParams, $rootScope, $route, $http, ngToast) {
  var cartArray = [];
  var noUrut = 0;

  $scope.displayCartArray = [];
  $scope.totalitem = 0;
  $scope.totaljenisitem = 0;
  $scope.po = "";
  $scope.id_po = "";
  $scope.proyek = "";
  $scope.supplier = "";
  $scope.gudang = "";

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  if ($routeParams.ppn == "ppn") {
    $scope.nDataBreadcrum = "Data Penerimaan Barang PPN";
    $scope.nNewBreadcrum = "Penerimaan Barang PPN Baru";
    $scope.link = '#/data-penerimaan-barang-ppn/';
  } else {
    $scope.nDataBreadcrum = "Data Penerimaan Barang Non-PPN";
    $scope.nNewBreadcrum = "Penerimaan Barang Non-PPN Baru";
    $scope.link = '#/data-penerimaan-barang/';
  }

  $scope.getdata = function () {
    $http.get('api/penerimaan-barang/penerimaan-barang.php?act=LoadAllRequirement&idPO=' + $scope.id_po + '&ppn=' + $routeParams.ppn).success(function (data, status) {
      $scope.data_barang = data.barang;
      $scope.data_po = data.po;
      $scope.limitHPP = data.limitHPP;
      $scope.data_gudang = data.gudang;
      $scope.gudang = data.gudang.filter(x => x.IsDefault === 1)[0].IDGudang;

      $scope.data_barang_unique = [];
      $scope.data_barang.forEach(function (entry) {
        let item = $scope.data_barang_unique.find(x => x.IDBarang === entry.IDBarang);
        if (item) {
          item.Sisa += entry.Sisa;
        } else {
          $scope.data_barang_unique.push(JSON.parse(JSON.stringify(entry)));
        }
      });
    });
  };
  $scope.getdata();

  $scope.displayDetailPO = function () {
    if ($scope.po != "") {
      $scope.id_po = $scope.data_po[$scope.po].NoPO;
      $scope.proyek = $scope.data_po[$scope.po].Proyek;
      $scope.supplier = $scope.data_po[$scope.po].Supplier;
    } else {
      $scope.id_po = 0;
      $scope.proyek = 0;
      $scope.supplier = 0;
    }
    cartArray = [];
    $scope.getdata();
    $scope.displayCart();
  }

  $scope.usrlogin = $rootScope.userLoginName;

  $("#kode").on("change", function (e) {
    $scope.kode = this.value;
    $scope.changeKode();
  });

  $scope.changeKode = function () {
    if ($scope.kode != "") {
      $scope.anama = $scope.data_barang[$scope.kode].Nama;
      $scope.aidbarang = $scope.data_barang[$scope.kode].IDBarang;
      $scope.isserialize = $scope.data_barang[$scope.kode].IsSerialize;

      $('#nama_barang').val($scope.anama);
      $scope.qty = $scope.data_barang[$scope.kode].Limit;
      $scope.limit = $scope.data_barang[$scope.kode].Limit;
      $scope.hpp = $scope.data_barang[$scope.kode].HPP;
      $scope.IsPaket = $scope.data_barang[$scope.kode].IsPaket;
      $scope.IsChild = $scope.data_barang[$scope.kode].IsChild;
      $scope.sisa = $scope.data_barang[$scope.kode].Sisa;
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
    }
  }

  $scope.noMoreThanLimit = function () {
    if ($scope.qty > $scope.limit)
      $scope.qty = $scope.limit;
  }

  $scope.addtocart = function (supress = false) {
    if ($scope.po == "") {
      ngToast.create({
        className: 'danger',
        content: 'Silahkan pilih No PO sebelum anda menambahkan daftar penerimaan stok barang. <i class="fa fa-remove"></i>'
      });
    } else {
      if ($scope.aidbarang > 0) {
        var IDBarang = $scope.aidbarang;
        var NamaBarang = $scope.anama;
        var Qty = parseInt($scope.qty);
        var Limit = $scope.limit;
        var HPP = $scope.hpp;
        var IsPaket = $scope.IsPaket;
        var IsChild = $scope.IsChild;
        var SN = $scope.serialnumber;
        var IsSerialize = $scope.isserialize;
        var updated = false;
        var SubTotal = HPP * Qty;

        var Sisa = ($scope.limit - $scope.qty);
        var Allow = Sisa >= 0;

        if (IsSerialize !== "0") {
          var qtyAll = cartArray.filter(x => x.IDBarang === IDBarang).reduce((qtyAll, y) => qtyAll + y.QtyBarang, 0);
          Sisa = ($scope.data_barang_unique.find(x => x.IDBarang === IDBarang).Sisa - qtyAll);
          Allow = Sisa > 0;
        }

        if (Allow) {
          console.log(SN)

          if (IsSerialize == 0) {
            cartArray.forEach(function (entry) {
              if (IDBarang == entry["IDBarang"] && IsPaket == entry["IsPaket"] && NamaBarang == entry["NamaBarang"]) {
                updated = true;
                entry["QtyBarang"] += parseFloat(Qty);
              }
            });
          }

          if (!updated) {
            cartArray[noUrut] = {
              NoUrut: noUrut,
              IDBarang: IDBarang,
              NamaBarang: NamaBarang,
              QtyBarang: Qty,
              SNBarang: SN,
              IsSerialize: IsSerialize,
              Limit: Limit,
              IsPaket: IsPaket,
              IsChild: IsChild,
              HPP: HPP,
              SubTotal: SubTotal
            };
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

          $scope.displayCart();
        } else {
          if (!supress) alert('Anda tidak dapat memasukan Qty melebihi Limit!');
        }
      } else {
        if (!supress) alert('Ada sesuatu yang salah. Silahkan ulang pilih data barang!');
      }
    }
  }

  $scope.showbarcodemodal = function () {
    if (!$scope.po) {
      alert('Silahkan pilih PO terlebih dahulu.');
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
            cart.SNBarang = codes[1];
          }
        });
      }

      for (var barcode of barcodes) {
        var codes = barcode.split(',');
        var snAdded = cartArray.filter(x => x.SNBarang.startsWith(codes[1]))[0];
        if (!snAdded) {
          var barang = $scope.data_barang.filter(x => x.LibCode === codes[0])[0];
          if (barang) {
            $scope.anama = barang.Nama;
            $scope.aidbarang = barang.IDBarang;
            $scope.isserialize = barang.IsSerialize;

            $scope.qty = barang.Limit;
            $scope.limit = barang.Limit;
            $scope.hpp = barang.HPP;
            $scope.IsPaket = barang.IsPaket;
            $scope.sisa = barang.Sisa;
            $scope.serialnumber = codes[1];
            $scope.addtocart(true);
          }
        }
      }

      $('#modalBarcode').modal('hide');
      $('.modal-backdrop').remove();
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
    $scope.displayCartArray = cartArray.filter(function () {
      return true
    });
    $scope.displayCartArray = $scope.displayCartArray.sort(sortFunction);
    $scope.displayCartArray.forEach(function (entry) {
      $scope.totalitem += parseFloat(entry["QtyBarang"]);
      if (entry["IDBarang"]) {
        $scope.totaljenisitem++;
        entry["SubTotal"] = (parseFloat(entry["HPP"]) * parseFloat(entry["QtyBarang"]));
        $scope.totalHPP += entry["SubTotal"];
        lastIDBarang = entry["IDBarang"];
      }
    });
  }

  $scope.changeQty = function (a) {
    var QtyVal = parseFloat($('#QtyBarang' + a).val());
    if (QtyVal > cartArray[a]['Limit']) QtyVal = cartArray[a]['Limit'];
    cartArray[a]['QtyBarang'] = QtyVal;
    $('#QtyBarang' + a).val(QtyVal);
    $scope.displayCart();
  }

  $scope.changeHPP = function (a) {
    var HPPVal = parseFloat($('#HPP' + a).val());
    cartArray[a]['HPP'] = HPPVal;
    cartArray[a]['SubTotal'] = HPPVal * parseFloat(cartArray[a]['Qty']);
    $('#HPP' + a).val(HPPVal);
    $scope.displayCart();
  }

  $scope.changeSN = function (a) {
    var SNVal = $('#SNBarang' + a).val();
    cartArray[a]['SNBarang'] = SNVal;
    $scope.displayCart();
  }

  $scope.removeRow = function (a) {
    delete cartArray[a];
    $scope.displayCart();

    return false;
  };

  $scope.tanggal = $rootScope.currentDateID;

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    const barangWithoutSN = cartArray.find(x => x.IsSerialize === "1" && !x.SNBarang);
    const barangExceedLImit = cartArray.find(x => x.QtyBarang > x.Limit);
    if (isValid) {
      if (barangWithoutSN) {
        alert('Silahkan masukan SN untuk + ' + barangWithoutSN.NamaBarang);
        return;
      } else if (barangExceedLImit) {
        alert('Qty barang ' + barangExceedLImit.NamaBarang + ' melebihi limit');
        return;
      }
      // if($scope.totalHPP > $scope.limitHPP){
      //   alert("Nilai HPP Penerimaan Barang tidak boleh melebihi Limit dari HPP Nilai PO");
      // } else {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/penerimaan-barang/penerimaan-barang.php?act=InsertNew',
        data: $.param({
          'po': $scope.id_po,
          'tanggal': $scope.tanggal,
          'gudang': $scope.gudang,
          'totalitem': $scope.totalitem,
          'totaljenisitem': $scope.totaljenisitem,
          'keterangan': $scope.keterangan,
          'completePO': $scope.completePO,
          'totalHPP': $scope.totalHPP,
          'cart': JSON.stringify(cartArray)
        }),
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).success(function (data, status) {
        if (data.res == "1") {
          ngToast.create({
            className: 'success',
            content: data.mes + ' <i class="fa fa-remove"></i>'
          });
          window.document.location = $scope.link;
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: data.mes + ' <i class="fa fa-remove"></i>'
          });
        }
      });
      // }
    }
  };
});

tripApp.controller('PenerimaanBarangDetailController', function ($rootScope, $scope, $route, $routeParams, $http, ngToast) {
  $http.get('api/penerimaan-barang/penerimaan-barang.php?act=Detail&id=' + $routeParams.id).success(function (data, status) {
    $scope.no_penerimaan = data.master.no_penerimaan;
    $scope.no_po = data.master.no_po;
    $scope.proyek = data.master.proyek;
    $scope.tanggal = data.master.tanggal;
    $scope.supplier = data.master.supplier;
    $scope.keterangan = data.master.keterangan;
    $scope.usrlogin = data.master.usrlogin;
    $scope.total_qty = data.master.total_qty;
    $scope.total_jenis = data.master.total_jenis;
    $scope.totalHPP = data.master.totalHPP;

    $scope.data_detail = data.detail;

    $scope.deleted_by = data.master.deleted_by;
    $scope.deleted_date = data.master.deleted_date;
    $scope.deleted_remark = data.master.deleted_remark;

    if ($scope.no_po.indexOf('/P/') > -1) {
      $scope.nDataBreadcrum = "Data Penerimaan Barang PPN";
      $scope.nNewBreadcrum = "Detail Penerimaan Barang PPN";
      $scope.link = '#/data-penerimaan-barang-ppn/';
    } else {
      $scope.nDataBreadcrum = "Data Penerimaan Barang Non-PPN";
      $scope.nNewBreadcrum = "Detail Penerimaan Barang Non-PPN";
      $scope.link = '#/data-penerimaan-barang/';
    }
  });

  $scope.doPrint = function () {
    window.open($rootScope.baseURL + 'api/print/print-penerimaan-barang.php?id=' + $routeParams.id, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});
