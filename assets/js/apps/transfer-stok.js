tripApp.controller('TransferStokController', function ($rootScope, $scope, $q, $routeParams, $route, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });
  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();

  $scope.getdata = function () {
    $http.get('api/transfer-stok/transfer-stok.php?act=DisplayData&datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&supplier=' + $scope.supplier + '&ppn=false').success(function (data, status) {
      $scope.data_transfer = data.data;
    });
  };

  $scope.getdata();

  $scope.refreshData = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }

  $scope.removeRow = function (val, noTransfer) {
    $scope.idTransfer = val;
    $scope.noTransfer = noTransfer;
    $('#modalDelete').modal('show');
  }

  $scope.submitFormDelete = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/transfer-stok/transfer-stok.php?act=Delete',
        data: $.param({
          'idr': $scope.idTransfer,
          'remark': $scope.remark,
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data Transfer Stok berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else if (data == "2") {
          ngToast.create({
            className: 'danger',
            content: 'Data Transfer Stok tidak dapat dihapus karena stok telah digunakan... <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data Transfer Stok gagal dihapus. Silahkan coba kembali... <i class="fa fa-remove"></i>'
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
    window.open($rootScope.baseURL + 'api/print/print-data-transfer-stok.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&supplier=' + $scope.supplier, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('TransferStokNewController', function ($rootScope, $scope, $q, $routeParams, $rootScope, $route, $http, ngToast) {
  var cartArray = [];
  var noUrut = 0;

  $scope.displayCartArray = [];
  $scope.totalitem = 0;
  $scope.totaljenisitem = 0;

  $scope.id_gudang_from = "";
  $scope.id_gudang_to = "";
  $scope.spb = "0";

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.getdata = function () {
    $http.get('api/transfer-stok/transfer-stok.php?act=LoadAllRequirement&id_gudang=' + $scope.id_gudang_from + '&spb=' + $scope.spb).success(function (data, status) {
      $scope.data_barang = data.barang;
      $scope.data_gudang = data.gudang;
      $scope.data_spb = data.spb;
    });
  };
  $scope.getdata();

  $("#id_gudang_from").on("change", function (e) {
    $scope.id_gudang_from = this.value;
    $scope.reloadItem();
  });

  $scope.reloadItem = function () {
    console.log($scope.id_gudang_from)
    if (!$scope.id_gudang_from) return;

    $('#nama_barang').val('');
    $scope.qty = "";
    $scope.serialnumber = "";
    $scope.isserialize = "";
    $scope.anama = "";
    $scope.aidbarang = 0;
    $scope.kode = "";
    $scope.limit = "";

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
      $scope.hpp = $scope.data_barang[$scope.kode].HPP;
      $scope.IsPaket = $scope.data_barang[$scope.kode].IsPaket;
      $scope.limit = $scope.data_barang[$scope.kode].StokGudang;
      $scope.serialnumber = "";

      if ($scope.data_barang[$scope.kode].IsSerialize == "1") {
        $scope.qty = 1;
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
      $scope.hpp = "";
      $scope.limit = "";
    }
  }

  $scope.addtocart = function (supress = false) {
    if ($scope.id_gudang_from == "") {
      ngToast.create({
        className: 'danger',
        content: 'Silahkan pilih Gudang Awal sebelum anda menambahkan daftar penerimaan stok barang. <i class="fa fa-remove"></i>'
      });
    } else {
      if ($scope.aidbarang > 0) {
        var IDBarang = $scope.aidbarang;
        var NamaBarang = $scope.anama;
        var Qty = parseInt($scope.qty);
        var Limit = parseInt($scope.limit);
        var HPP = $scope.hpp;
        var IsPaket = $scope.IsPaket;
        var SN = $scope.serialnumber;
        var IsSerialize = $scope.isserialize;
        var updated = false;
        var SubTotal = HPP * Qty;

        var lanjut = true;

        if (Qty > Limit) {
          ngToast.create({
            className: 'danger',
            content: 'Jumlah Qty yang anda masukan tidak boleh melebihi Jumlah Stok saat ini. <i class="fa fa-remove"></i>'
          });
          lanjut = false;
        }

        if (IsSerialize === "1" && SN === "") {
          ngToast.create({
            className: 'danger',
            content: 'Silahkan masukan SN terlebih dahulu. <i class="fa fa-remove"></i>'
          });
          lanjut = false;
        }

        var duplicateSN = cartArray.filter(x => x.SNBarang === SN)
        if (IsSerialize == "1" && duplicateSN.length > 0) {
          ngToast.create({
            className: 'danger',
            content: 'Anda tidak dapat memasukan SN yang sama. Silahkan gunakan SN yang lain. <i class="fa fa-remove"></i>'
          });
          lanjut = false;
        }

        if (lanjut) {
          if (IsSerialize == 0) {
            cartArray.forEach(function (entry) {
              if (IDBarang == entry["IDBarang"] && IsPaket == entry["IsPaket"]) {
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
              TotalAvailableStok: Limit,
              IsPaket: IsPaket,
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
        }
      } else {
        if (!supress) alert('Ada sesuatu yang salah. Silahkan ulang pilih data barang!');
      }
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

  $scope.showbarcodemodal = function () {
    $('#modalBarcode').modal('show');
    $('#barcodes').val('');
    setTimeout(() => {
      $('#barcodes').focus();
    }, 500);
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
            $scope.sn = barang.SerialNumberArray;

            $scope.hpp = barang.HPP;
            $scope.IsPaket = barang.IsPaket;
            $scope.limit = barang.Limit;
            $scope.serialnumber = codes[1];

            $scope.qty = ($scope.isserialize === '0') ? parseInt($scope.limit) : 1;
            $scope.addtocart(true);
          }
        }
      }

      $('#modalBarcode').modal('hide');
      $('.modal-backdrop').remove();
    }
  }

  $scope.tanggal = $rootScope.currentDateID;

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      if ($scope.id_gudang_from === $scope.id_gudang_to) {
        alert("Anda tidak dapat melakukan pengiriman stok dari dan ke gudang yang sama.");
      } else {
        $scope.processing = true;
        $http({
          method: "POST",
          url: 'api/transfer-stok/transfer-stok.php?act=InsertNew',
          data: $.param({
            'tanggal': $scope.tanggal,
            'spb': $scope.spb,
            'id_gudang_from': $scope.id_gudang_from,
            'id_gudang_to': $scope.id_gudang_to,
            'totalitem': $scope.totalitem,
            'totaljenisitem': $scope.totaljenisitem,
            'keterangan': $scope.keterangan,
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
            window.document.location = '#/data-transfer-stok';
          } else {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: data.mes + ' <i class="fa fa-remove"></i>'
            });
          }
        });
      }
    }
  };
});

tripApp.controller('TransferStokDetailController', function ($rootScope, $scope, $route, $routeParams, $http, ngToast) {
  $http.get('api/transfer-stok/transfer-stok.php?act=Detail&id=' + $routeParams.id).success(function (data, status) {
    $scope.master = data.master;
    $scope.data_detail = data.detail;
  });

  $scope.doPrint = function () {
    window.open($rootScope.baseURL + 'api/print/print-transfer-stok.php?id=' + $routeParams.id, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});
