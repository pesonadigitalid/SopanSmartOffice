tripApp.controller('SPHController', function ($scope, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();
  $scope.pelanggan = "";
  $scope.filterstatus = "";
  $scope.activeMenu = '';
  $scope.sales = '';

  $scope.getAllData = function () {
    $http.get('api/sph/sph.php?act=DataList&datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&pelanggan=' + $scope.pelanggan + '&sales=' + $scope.sales + '&filterstatus=' + $scope.filterstatus).success(function (data, status) {
      $scope.data_sph = data.sph;
      $scope.data_pelanggan = data.pelanggan;
      $scope.data_sales = data.sales;

      $scope.all = data.all;
      $scope.approved = data.approved;
      $scope.declined = data.declined;
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

  $scope.updateStatus = function (val, status) {
    if (confirm("Anda yakin ingin mengubah status dari SPH ini?")) {
      $http({
        method: "POST",
        url: 'api/sph/sph.php?act=UpdateStatus',
        data: $.param({
          'id': val,
          'status': status
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        ngToast.create({
          className: 'success',
          content: 'Status SPH berhasil diupdate. <i class="fa fa-remove"></i>'
        });
        $route.reload();
      });
    }
  }

  $scope.removeRow = function (val, noSPH) {
    $scope.idSPH = val;
    $scope.noSPH = noSPH;
    $('#modalDelete').modal('show');
  }

  $scope.submitFormDelete = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/sph/sph.php?act=Delete',
        data: $.param({
          'idr': $scope.idSPH,
          'remark': $scope.remark,
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        $scope.processing = false;
        if (data === "1") {
          ngToast.create({
            className: 'success',
            content: 'Data SPH berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $scope.refreshData();
        } else if (data === "2") {
          ngToast.create({
            className: 'danger',
            content: 'Data SPH tidak dapat dihapus karena telah terelasi dengan data SPB <i class="fa fa-remove"></i>'
          });
          $scope.refreshData();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data SPH gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
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

  $scope.doPrint = function (a) {
    window.open($rootScope.baseURL + 'api/print/print-sph.php?id=' + a, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }

  $scope.doPrint2 = function () {
    window.open($rootScope.baseURL + 'api/print/print-data-penjualan-retail.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('SPHNewController', function ($scope, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
  var cartArray = [];
  var noUrut = 1;
  $scope.displayCartArray = [];
  $scope.totalItem = 0;

  $scope.total = 0;
  $scope.ppn = 0;
  $scope.ppn_persen = 0;
  $scope.diskon = 0;
  $scope.diskon_persen = 0;
  $scope.total2 = 0;
  $scope.grand_total = 0;
  $scope.sisa = 0;
  $scope.kembali = 0;
  $scope.pembayarandp = 0;
  $scope.metode_pembayaran = "Tunai";
  $scope.metode_pembayaran2 = "Kas Kecil";

  $scope.pelanggan = "";

  $scope.tinymceOptions = {
    menubar: false,
    plugins: 'link lists',
    toolbar: 'undo redo | bold italic | numlist bullist | alignleft aligncenter alignright | link'
  };

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.getdata = function () {
    $http.get('api/sph/sph.php?act=LoadAllRequirement').success(function (data, status) {
      $scope.data_pelanggan = data.pelanggan;
      $scope.data_barang = data.barang;
      $scope.data_sales = data.sales;
    });
  };

  $scope.getdata();

  $scope.usrlogin = $rootScope.userLoginName;
  $scope.tanggal = $rootScope.currentDateID;

  $("#kode").on("change", function (e) {
    $scope.kode = this.value;
    $scope.changeKode();
  });

  $scope.changeKode = function () {
    if ($scope.kode != "") {
      $scope.anama = $scope.data_barang[$scope.kode].Nama;
      $scope.aidbarang = $scope.data_barang[$scope.kode].IDBarang;
      $scope.isserialize = $scope.data_barang[$scope.kode].IsSerialize;

      $scope.limit = $scope.data_barang[$scope.kode].Limit;
      $scope.harga = $scope.data_barang[$scope.kode].HargaJual;
      $scope.HPP = $scope.data_barang[$scope.kode].Harga;
      $scope.HPPReal = $scope.data_barang[$scope.kode].HPP;

      $('#nama_barang').val($scope.anama);
      $scope.qty = 1;
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

  $scope.addtocart = function () {
    if ($scope.isserialize === "1" && $scope.serialnumber === "") {
      ngToast.create({
        className: 'danger',
        content: 'Silahkan tambahkan No Seri Barang untuk barang yang terserialisasi. <i class="fa fa-remove"></i>'
      });
    } else {
      if ($scope.aidbarang > 0) {
        var IDBarang = $scope.aidbarang;
        var NamaBarang = $scope.anama;
        var Qty = parseFloat($scope.qty);
        var Limit = $scope.limit;
        var SN = $scope.serialnumber;
        var Harga = parseFloat($scope.harga);
        var HPP = parseFloat($scope.HPP);
        var HPPReal = parseFloat($scope.HPPReal);
        var IsSerialize = $scope.isserialize;
        var updated = false;
        var SubTotal = Harga * Qty;
        var Margin = SubTotal - (HPP * Qty);

        if (IsSerialize == 0) {
          cartArray.forEach(function (entry) {
            if (IDBarang == entry["IDBarang"]) {
              updated = true;
              entry["QtyBarang"] += parseFloat(Qty);
            }
          });
        }

        if (!updated) {

          cartArray[noUrut] = { NoUrut: noUrut, IDBarang: IDBarang, NamaBarang: NamaBarang, QtyBarang: Qty, SNBarang: SN, IsSerialize: IsSerialize, Limit: Limit, Harga: Harga, HargaDiskon: Harga, HPP: HPP, HPPReal: HPPReal, Margin: Margin, SubTotal: SubTotal, Diskon: 0, UseDiskonForCalc: true };
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
        $scope.harga = "";
        $scope.HPP = "";

        $scope.displayCart();
      } else {
        alert('Ada sesuatu yang salah. Silahkan ulang pilih data barang!');
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
    $scope.total = 0;
    $scope.totalItem = 0;
    $scope.totalHPP = 0;
    $scope.totalHPPReal = 0;
    $scope.totalMargin = 0;
    $scope.displayCartArray = cartArray.filter(function () {
      return true
    });
    $scope.displayCartArray = $scope.displayCartArray.sort(sortFunction);
    $scope.displayCartArray.forEach(function (entry) {
      $scope.totalItem++;
      const qty = entry["QtyBarang"].toString().replace(/,/g, "")
      const hpp = entry["HPP"].toString().replace(/,/g, "")
      const hppReal = entry["HPPReal"].toString().replace(/,/g, "")
      $scope.totalHPP += (parseFloat(qty) * parseFloat(hpp));
      $scope.totalHPPReal += (parseFloat(qty) * parseFloat(hppReal));
      $scope.total = parseFloat($scope.total.toString().replace(/,/g, "")) + parseFloat(entry["SubTotal"].toString().replace(/,/g, ""));
    });
    console.log($scope.totalHPPReal);
    $scope.countingGrandTotal();
  }

  $scope.changeQty = function (a) {
    var QtyVal = parseFloat($('#QtyBarang' + a).val());
    var HPP = parseFloat(cartArray[a]['HPP']);
    var Harga = parseFloat(cartArray[a]["Harga"].toString().replace(/,/g, ""));
    var Diskon = parseFloat(cartArray[a]['Diskon']);

    cartArray[a]['QtyBarang'] = QtyVal;
    cartArray[a]["SubTotal"] = (Harga - Diskon) * QtyVal;

    cartArray[a]["Margin"] = parseFloat(cartArray[a]["SubTotal"]) - (HPP * QtyVal);
    $scope.displayCart();
  }

  $scope.changeHPP = function (a) {
    var HPP = parseFloat($('#HPP' + a).val());
    var QtyVal = parseFloat(cartArray[a]['QtyBarang']);
    var Harga = parseFloat(cartArray[a]["Harga"].toString().replace(/,/g, ""));
    var Diskon = parseFloat(cartArray[a]['Diskon']);

    cartArray[a]['HPP'] = HPP
    cartArray[a]["SubTotal"] = (Harga - Diskon) * QtyVal;

    cartArray[a]["Margin"] = parseFloat(cartArray[a]["SubTotal"]) - (HPP * QtyVal);
    $scope.displayCart();
  }

  $scope.calcHargaAndSubTotal = function (NoUrut, useDiskonForCalc) {
    if (useDiskonForCalc !== null) cartArray[NoUrut]['UseDiskonForCalc'] = useDiskonForCalc;

    var Harga = CommonServices.getInputValueAsFloat('#Harga' + NoUrut);
    var Diskon = CommonServices.getInputValueAsNumber('#Diskon' + NoUrut);
    var HargaDiskon = CommonServices.getInputValueAsFloat('#HargaDiskon' + NoUrut);
    var QtyBarang = CommonServices.getInputValueAsInt('#QtyBarang' + NoUrut);
    var HPP = CommonServices.getInputValueAsFloat('#HPP' + NoUrut);

    cartArray[NoUrut]['Harga'] = Harga;
    cartArray[NoUrut]['Diskon'] = Diskon;
    cartArray[NoUrut]['HargaDiskon'] = HargaDiskon;
    cartArray[NoUrut]['QtyBarang'] = QtyBarang;

    if (cartArray[NoUrut]['UseDiskonForCalc']) {
      cartArray[NoUrut]['HargaDiskon'] = Harga - CommonServices.getDiscountValue(Diskon, Harga);
      CommonServices.setValueWithNumberFormat('#HargaDiskon' + NoUrut, numberWithCommas(cartArray[NoUrut]['HargaDiskon']));
    } else {
      cartArray[NoUrut]['Diskon'] = ((Harga - HargaDiskon) / Harga * 100).toFixed(2) + "%";
      CommonServices.setValueWithNumberFormat('#Diskon' + NoUrut, cartArray[NoUrut]['Diskon']);
    }

    cartArray[NoUrut]["SubTotal"] = cartArray[NoUrut]['HargaDiskon'] * cartArray[NoUrut]['QtyBarang'];
    cartArray[NoUrut]["Margin"] = cartArray[NoUrut]["SubTotal"] - (HPP * QtyBarang);

    $scope.displayCart();
  }

  $scope.changeSN = function (a) {
    var SNVal = $('#SNBarang' + a).val();
    cartArray[a]['SNBarang'] = SNVal;
    $scope.displayCart();
  }

  $scope.countingGrandTotal = function () {
    $scope.changeDiskon();
    $scope.changePPN();
    $scope.changePembayaran();
  }

  $scope.changeDiskon = function () {
    $scope.diskon = CommonServices.getDiscountValue($scope.diskon_persen, $scope.total);
    $scope.total2 = parseFloat($scope.total) - $scope.diskon;
    $scope.changePPN();
  }

  $scope.changePPN = function () {
    var PPNPersen = parseFloat($scope.ppn_persen);
    $scope.ppn = (PPNPersen / 100) * parseFloat($scope.total2);
    $scope.grand_total = parseFloat($scope.total2) + $scope.ppn;
    $scope.totalMargin = parseFloat($scope.total2) - $scope.totalHPP;
  }

  $scope.changePembayaran = function () {
    var GrandTotal = parseFloat($scope.grand_total);
    var Pembayaran = parseFloat($scope.pembayarandp);

    $scope.kembali = Pembayaran - GrandTotal;
    $scope.sisa = GrandTotal - Pembayaran;

    if ($scope.kembali < 0)
      $scope.kembali = 0;

    if ($scope.sisa < 0)
      $scope.sisa = 0;
  }

  $scope.removeRow = function (a) {
    delete cartArray[a];
    $scope.displayCart();

    return false;
  };

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/sph/sph.php?act=InsertNew',
        data: $.param({
          'tanggal': $scope.tanggal,
          'usrlogin': $rootScope.userLoginName,
          'pelanggan': $scope.pelanggan,
          'sales': $scope.sales,
          'total_item': $scope.totalItem,
          'total': $scope.total,
          'diskon_persen': $scope.diskon_persen,
          'diskon': $scope.diskon,
          'total2': $scope.total2,
          'ppn_persen': $scope.ppn_persen,
          'ppn': $scope.ppn,
          'grand_total': $scope.grand_total,
          'pembayarandp': $scope.pembayarandp,
          'sisa': $scope.sisa,
          'keterangan': $scope.keterangan,
          'uploaded': $scope.userLoginID,
          'kembali': $scope.kembali,
          'totalHPP': $scope.totalHPP,
          'totalHPPReal': $scope.totalHPPReal,
          'totalMargin': $scope.totalMargin,
          'cart': JSON.stringify(cartArray)
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data.res == "1") {
          ngToast.create({
            className: 'success',
            content: data.mes + ' <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-sph/';
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

tripApp.controller('SPHDetailController', function ($rootScope, $scope, $route, $routeParams, $http, ngToast) {
  $http.get('api/sph/sph.php?act=Detail&id=' + $routeParams.sphId).success(function (data, status) {

    $scope.no_sph = data.master.NoSPH;
    $scope.pelanggan = data.master.Pelanggan;
    $scope.sales = data.master.sales;
    $scope.tanggal = data.master.Tanggal;
    $scope.totalItem = data.master.TotalItem;
    $scope.keterangan = data.master.Keterangan;
    $scope.total = data.master.Total;
    $scope.ppn = data.master.PPN;
    $scope.ppn_persen = data.master.PPNPersen;
    $scope.diskon = data.master.Diskon;
    $scope.diskon_persen = data.master.DiskonPersen;
    $scope.total2 = data.master.Total2;
    $scope.grand_total = data.master.GrandTotal;
    $scope.sisa = data.master.Sisa;
    $scope.kembali = data.master.Kembali;
    $scope.pembayarandp = data.master.TotalPembayaran;
    $scope.totalHPP = data.master.TotalHPP;
    $scope.totalHPPReal = data.master.TotalHPPReal;
    $scope.totalMargin = data.master.TotalMargin;
    $scope.status = data.master.Status;

    $scope.data_detail = data.detail;

    $scope.deleted_by = data.master.deleted_by;
    $scope.deleted_date = data.master.deleted_date;
    $scope.deleted_remark = data.master.deleted_remark;
  });

  $scope.doPrint = function () {
    window.open($rootScope.baseURL + 'api/print/print-sph.php?id=' + $routeParams.sphId, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('SPHEditController', function ($rootScope, $scope, $route, $routeParams, $http, ngToast, CommonServices) {
  var cartArray = [];
  var noUrut = 1;
  $scope.displayCartArray = [];
  $scope.totalItem = 0;

  $scope.total = 0;
  $scope.ppn = 0;
  $scope.ppn_persen = 0;
  $scope.diskon = 0;
  $scope.diskon_persen = 0;
  $scope.total2 = 0;
  $scope.grand_total = 0;
  $scope.sisa = 0;
  $scope.kembali = 0;
  $scope.pembayarandp = 0;
  $scope.metode_pembayaran = "Tunai";
  $scope.metode_pembayaran2 = "Kas Kecil";

  $scope.pelanggan = "";

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.tinymceOptions = {
    menubar: false,
    plugins: 'link lists',
    toolbar: 'undo redo | bold italic | numlist bullist | alignleft aligncenter alignright | link'
  };

  $scope.getdata = function () {
    $http.get('api/sph/sph.php?act=LoadAllRequirement').success(function (data, status) {
      $scope.data_pelanggan = data.pelanggan;
      $scope.data_barang = data.barang;
    });
  };

  $scope.getdata();

  $http.get('api/sph/sph.php?act=Detail&id=' + $routeParams.sphId).success(function (data, status) {

    $scope.no_sph = data.master.NoSPH;
    $scope.pelanggan = data.master.IDPelanggan;
    $scope.sales = data.master.sales;
    $scope.tanggal = data.master.Tanggal;
    $scope.totalItem = data.master.TotalItem;
    $scope.keterangan = data.master.Keterangan;
    $scope.total = data.master.Total;
    $scope.ppn = data.master.PPN;
    $scope.ppn_persen = data.master.PPNPersen;
    $scope.diskon = data.master.Diskon;
    $scope.diskon_persen = data.master.DiskonPersen;
    $scope.total2 = data.master.Total2;
    $scope.grand_total = data.master.GrandTotal;
    $scope.sisa = data.master.Sisa;
    $scope.kembali = data.master.Kembali;
    $scope.pembayarandp = data.master.TotalPembayaran;
    $scope.totalHPP = data.master.TotalHPP;
    $scope.totalHPPReal = data.master.TotalHPPReal;
    $scope.totalMargin = data.master.TotalMargin;
    $scope.status = data.master.Status;

    $scope.data_detail = data.detail;

    cartArray = data.detailCart;
    noUrut = data.nourut;

    $scope.displayCart();
  });

  $("#kode").on("change", function (e) {
    $scope.kode = this.value;
    $scope.changeKode();
  });

  $scope.changeKode = function () {
    if ($scope.kode != "") {
      $scope.anama = $scope.data_barang[$scope.kode].Nama;
      $scope.aidbarang = $scope.data_barang[$scope.kode].IDBarang;
      $scope.isserialize = $scope.data_barang[$scope.kode].IsSerialize;

      $scope.limit = $scope.data_barang[$scope.kode].Limit;
      $scope.harga = $scope.data_barang[$scope.kode].HargaJual;
      $scope.HPP = $scope.data_barang[$scope.kode].Harga;
      $scope.HPPReal = $scope.data_barang[$scope.kode].HPP;

      $('#nama_barang').val($scope.anama);
      $scope.qty = 1;
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



  $scope.addtocart = function () {
    if ($scope.isserialize === "1" && $scope.serialnumber === "") {
      ngToast.create({
        className: 'danger',
        content: 'Silahkan tambahkan No Seri Barang untuk barang yang terserialisasi. <i class="fa fa-remove"></i>'
      });
    } else {
      if ($scope.aidbarang > 0) {
        var IDBarang = $scope.aidbarang;
        var NamaBarang = $scope.anama;
        var Qty = parseFloat($scope.qty);
        var Limit = $scope.limit;
        var SN = $scope.serialnumber;
        var Harga = parseFloat($scope.harga);
        var HPP = parseFloat($scope.HPP);
        var HPPReal = parseFloat($scope.HPPReal);
        var IsSerialize = $scope.isserialize;
        var updated = false;
        var SubTotal = Harga * Qty;
        var Margin = SubTotal - (HPP * Qty);

        if (IsSerialize == 0) {
          cartArray.forEach(function (entry) {
            if (IDBarang == entry["IDBarang"]) {
              updated = true;
              entry["QtyBarang"] += parseFloat(Qty);
            }
          });
        }

        if (!updated) {

          cartArray[noUrut] = { NoUrut: noUrut, IDBarang: IDBarang, NamaBarang: NamaBarang, QtyBarang: Qty, SNBarang: SN, IsSerialize: IsSerialize, Limit: Limit, Harga: Harga, HargaDiskon: Harga, HPP: HPP, HPPReal: HPPReal, Margin: Margin, SubTotal: SubTotal, Diskon: 0, UseDiskonForCalc: true };
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
        $scope.harga = "";
        $scope.HPP = "";

        $scope.displayCart();
      } else {
        alert('Ada sesuatu yang salah. Silahkan ulang pilih data barang!');
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
    $scope.total = 0;
    $scope.totalItem = 0;
    $scope.totalHPP = 0;
    $scope.totalHPPReal = 0;
    $scope.totalMargin = 0;
    $scope.displayCartArray = cartArray.filter(function () {
      return true
    });
    $scope.displayCartArray = $scope.displayCartArray.sort(sortFunction);
    $scope.displayCartArray.forEach(function (entry) {
      $scope.totalItem++;
      const qty = entry["QtyBarang"].toString().replace(/,/g, "")
      const hpp = entry["HPP"].toString().replace(/,/g, "")
      const hppReal = entry["HPPReal"].toString().replace(/,/g, "")
      $scope.totalHPP += (parseFloat(qty) * parseFloat(hpp));
      $scope.totalHPPReal += (parseFloat(qty) * parseFloat(hppReal));
      $scope.total = parseFloat($scope.total.toString().replace(/,/g, "")) + parseFloat(entry["SubTotal"].toString().replace(/,/g, ""));
    });
    console.log($scope.totalHPPReal);
    $scope.countingGrandTotal();
  }

  $scope.changeQty = function (a) {
    var QtyVal = parseFloat($('#QtyBarang' + a).val());
    var HPP = parseFloat(cartArray[a]['HPP']);
    var Harga = parseFloat(cartArray[a]["Harga"].toString().replace(/,/g, ""));
    var Diskon = parseFloat(cartArray[a]['Diskon']);

    cartArray[a]['QtyBarang'] = QtyVal;
    cartArray[a]["SubTotal"] = (Harga - Diskon) * QtyVal;

    cartArray[a]["Margin"] = parseFloat(cartArray[a]["SubTotal"]) - (HPP * QtyVal);
    $scope.displayCart();
  }

  $scope.changeHPP = function (a) {
    var HPP = parseFloat($('#HPP' + a).val());
    var QtyVal = parseFloat(cartArray[a]['QtyBarang']);
    var Harga = parseFloat(cartArray[a]["Harga"].toString().replace(/,/g, ""));
    var Diskon = parseFloat(cartArray[a]['Diskon']);

    cartArray[a]['HPP'] = HPP
    cartArray[a]["SubTotal"] = (Harga - Diskon) * QtyVal;

    cartArray[a]["Margin"] = parseFloat(cartArray[a]["SubTotal"]) - (HPP * QtyVal);
    $scope.displayCart();
  }

  $scope.calcHargaAndSubTotal = function (NoUrut, useDiskonForCalc) {
    if (useDiskonForCalc !== null) cartArray[NoUrut]['UseDiskonForCalc'] = useDiskonForCalc;

    var Harga = CommonServices.getInputValueAsFloat('#Harga' + NoUrut);
    var Diskon = CommonServices.getInputValueAsNumber('#Diskon' + NoUrut);
    var HargaDiskon = CommonServices.getInputValueAsFloat('#HargaDiskon' + NoUrut);
    var QtyBarang = CommonServices.getInputValueAsInt('#QtyBarang' + NoUrut);
    var HPP = CommonServices.getInputValueAsFloat('#HPP' + NoUrut);

    cartArray[NoUrut]['Harga'] = Harga;
    cartArray[NoUrut]['Diskon'] = Diskon;
    cartArray[NoUrut]['HargaDiskon'] = HargaDiskon;
    cartArray[NoUrut]['QtyBarang'] = QtyBarang;

    if (cartArray[NoUrut]['UseDiskonForCalc']) {
      cartArray[NoUrut]['HargaDiskon'] = Harga - CommonServices.getDiscountValue(Diskon, Harga);
      CommonServices.setValueWithNumberFormat('#HargaDiskon' + NoUrut, numberWithCommas(cartArray[NoUrut]['HargaDiskon']));
    } else {
      cartArray[NoUrut]['Diskon'] = ((Harga - HargaDiskon) / Harga * 100).toFixed(2) + "%";
      CommonServices.setValueWithNumberFormat('#Diskon' + NoUrut, cartArray[NoUrut]['Diskon']);
    }

    cartArray[NoUrut]["SubTotal"] = cartArray[NoUrut]['HargaDiskon'] * cartArray[NoUrut]['QtyBarang'];
    cartArray[NoUrut]["Margin"] = cartArray[NoUrut]["SubTotal"] - (HPP * QtyBarang);

    $scope.displayCart();
  }

  $scope.changeSN = function (a) {
    var SNVal = $('#SNBarang' + a).val();
    cartArray[a]['SNBarang'] = SNVal;
    $scope.displayCart();
  }

  $scope.countingGrandTotal = function () {
    $scope.changeDiskon();
    $scope.changePPN();
    $scope.changePembayaran();
  }

  $scope.changeDiskon = function () {
    $scope.diskon = CommonServices.getDiscountValue($scope.diskon_persen, $scope.total);
    $scope.total2 = parseFloat($scope.total) - $scope.diskon;
    $scope.changePPN();
  }

  $scope.changePPN = function () {
    var PPNPersen = parseFloat($scope.ppn_persen);
    $scope.ppn = (PPNPersen / 100) * parseFloat($scope.total2);
    $scope.grand_total = parseFloat($scope.total2) + $scope.ppn;
    $scope.totalMargin = $scope.total2 - $scope.totalHPP;
  }

  $scope.changePembayaran = function () {
    var GrandTotal = parseFloat($scope.grand_total);
    var Pembayaran = parseFloat($scope.pembayarandp);

    $scope.kembali = Pembayaran - GrandTotal;
    $scope.sisa = GrandTotal - Pembayaran;

    if ($scope.kembali < 0)
      $scope.kembali = 0;

    if ($scope.sisa < 0)
      $scope.sisa = 0;
  }

  $scope.removeRow = function (a) {
    delete cartArray[a];
    $scope.displayCart();

    return false;
  };

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/sph/sph.php?act=Edit',
        data: $.param({
          'id': $routeParams.sphId,
          'no_sph': $scope.no_sph,
          'tanggal': $scope.tanggal,
          'usrlogin': $rootScope.userLoginName,
          'pelanggan': $scope.pelanggan,
          'total_item': $scope.totalItem,
          'total': $scope.total,
          'diskon_persen': $scope.diskon_persen,
          'diskon': $scope.diskon,
          'total2': $scope.total2,
          'ppn_persen': $scope.ppn_persen,
          'ppn': $scope.ppn,
          'grand_total': $scope.grand_total,
          'pembayarandp': $scope.pembayarandp,
          'sisa': $scope.sisa,
          'keterangan': $scope.keterangan,
          'uploaded': $scope.userLoginID,
          'kembali': $scope.kembali,
          'totalHPP': $scope.totalHPP,
          'totalHPPReal': $scope.totalHPPReal,
          'totalMargin': $scope.totalMargin,
          'cart': JSON.stringify(cartArray)
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data.res == "1") {
          ngToast.create({
            className: 'success',
            content: data.mes + ' <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-sph/';
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
