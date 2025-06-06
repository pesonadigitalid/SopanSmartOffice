tripApp.controller('ReturnPenjualanController', function($rootScope, $scope, $q, $routeParams, $route, $http, ngToast) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });
  $scope.datestart = "";
  $scope.dateend = "";
  $scope.kode_proyek = "";
  $scope.supplier = "";
  $scope.filterstatus = "";
  $scope.activeMenu = '';


  $scope.getdata = function() {
    $http.get('api/return-penjualan/data-return-penjualan.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&kode_proyek=' + $scope.kode_proyek + '&supplier=' + $scope.supplier + '&status=' + $scope.filterstatus).success(function(data, status) {
      $scope.data_return = data.returnPenjualan;

      $scope.all = data.all;
      $scope.new = data.new;
      $scope.complete = data.completed;
    });
  };

  $scope.getdata();

  $scope.doFilter = function(a) {
    $scope.filterstatus = a;
    $scope.activeMenu = a;
    $scope.refreshData();
  }

  $scope.refreshData = function() {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }

  $scope.removeRow = function(val) {
    if (confirm("Anda yakin ingin menghapus data ini?")) {
      $http({
        method: "POST",
        url: 'api/return-penjualan/delete.php',
        data: $.param({
          'idr': val
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function(data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data return penjualan berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data return penjualan gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }

  $scope.doPrint = function(a) {
    window.open($rootScope.baseURL + 'api/print/print-return-po.php?id=' + a, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }

  $scope.doPrint2 = function() {
    window.open($rootScope.baseURL + 'api/print/print-data-return-penjualan.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('ReturnPenjualanNewController', function($rootScope, $scope, $q, $routeParams, $rootScope, $route, $http, ngToast) {
  var cartArray = [];
  var noUrut = 1;

  $scope.displayCartArray = [];
  $scope.totalitem = 0;
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
  $scope.ongkos_kirim = 0;
  $scope.metode_pembayaran = "Tunai";
  $scope.metode_pembayaran2 = "Kas Kecil";
  $scope.showBG = false;
  $scope.showBG2 = false;
  $scope.nobg = "";
  $scope.jatuhtempobg = "";
  $scope.tipe = "ITEM RUSAK";

  $scope.changeMetodePayment2 = function() {
    $scope.nobg = "";
    $scope.jatuhtempobg = "";
    if ($scope.metode_pembayaran == "Rekening BG") {
      $scope.showBG = true;
      $scope.showBG2 = true;
    } else {
      $scope.showBG = false;
      $scope.showBG2 = false;
    }
  };

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.getdata = function() {
    $http.get('api/proyek/data-proyek.php?stts=2').success(function(data, status) {
      $scope.data_proyek = data;
    });
  };

  $scope.getdatabarang = function() {
    $http.get('api/barang/data-barang.php?departement=2').success(function(data, status) {
      $scope.data_barang = data;
    });
  };

  $q.all({
    apiv1: $http.get('api/return-penjualan/load-all-requirement.php')
  }).then(function(results) {
    $scope.data_barang = results.apiv1.data.barang;
    $scope.data_proyek = results.apiv1.data.proyek;
    $scope.data_penjualan = results.apiv1.data.penjualan;
  });

  //$scope.getdata();
  //$scope.getdatabarang();

  $scope.usrlogin = $rootScope.userLoginName;

  $("#penjualan").on("change", function(e) {
    $scope.penjualan = this.value;
    //alert($scope.penjualan);
    $scope.changeKodePenjualan();
  });

  $scope.changeKodePenjualan = function() {
    if ($scope.penjualan != "") {
      $scope.apelanggan = $scope.data_penjualan[$scope.penjualan].NamaPelanggan;
      $scope.aidpelanggan = $scope.data_penjualan[$scope.penjualan].IDPelanggan;
      $scope.anopenjualan = $scope.data_penjualan[$scope.penjualan].NoPenjualan;
      //alert($scope.apelanggan);

      $http.get('api/return-penjualan/load-all-requirement.php?no_penjualan=' + $scope.anopenjualan).success(function(data, status) {
        $scope.data_barang = data.barang;
      });

      $('#pelanggan').val($scope.apelanggan);
    } else {
      $('#pelanggan').val("");
    }
  }

  $("#kode").on("change", function(e) {
    $scope.kode = this.value;
    //alert($scope.kode);
    $scope.changeKode();
  });

  $scope.changeKode = function() {
    if ($scope.kode != "") {
      $scope.anama = $scope.data_barang[$scope.kode].Nama;
      $scope.aidbarang = $scope.data_barang[$scope.kode].IDBarang;
      $scope.isserialize = $scope.data_barang[$scope.kode].IsSerialize;

      $scope.limit = $scope.data_barang[$scope.kode].Limit;
      $scope.HPP = $scope.data_barang[$scope.kode].HPP;

      $scope.IsPaket = $scope.data_barang[$scope.kode].IsPaket;

      $('#nama_barang').val($scope.anama);
      $('#limit').val($scope.limit);

      $scope.qty = parseInt($scope.limit);
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

  $scope.addtocart = function() {
    if ($scope.aidbarang > 0 && $scope.qty > 0) {
      var IDBarang = $scope.aidbarang;
      var NamaBarang = $scope.anama;
      var Qty = $scope.qty;
      var Limit = $scope.limit;
      var HPP = $scope.HPP;
      var Sisa = ($scope.limit - $scope.qty);

      var SN = $scope.serialnumber;
      var IsSerialize = $scope.isserialize;
      var SubTotal = Qty * HPP;
      var updated = false;

      var IsPaket = $scope.IsPaket;
      var Tipe = "ITEM RUSAK";

      if (Sisa >= 0) {
        if (IsSerialize == 0) {
          cartArray.forEach(function(entry) {
            if (IDBarang == entry["IDBarang"] && IsPaket == entry["IsPaket"]) {
              updated = true;
              entry["QtyBarang"] += parseFloat(Qty);
            }
          });
        }

        if (!updated) {
          cartArray[noUrut] = { NoUrut: noUrut, IDBarang: IDBarang, NamaBarang: NamaBarang, QtyBarang: Qty, SNBarang: SN, IsSerialize: IsSerialize, Limit: Limit, Sisa: Sisa, HPP: HPP, SubTotal: SubTotal, IsPaket: IsPaket, Tipe: Tipe };
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

        $scope.displayCart();
      } else {
        alert('Anda tidak dapat memasukan Qty melebihi Limit dari Penjualan!');
      }
    } else {
      alert('Ada sesuatu yang salah. Silahkan ulang pilih data barang dan pastikan Qty Barang tidak sama dengan NOL!');
    }
  }

  $scope.displayCart = function() {
    function sortFunction(a, b) {
      if (a['NoUrut'] == b['NoUrut']) {
        return 0;
      } else {
        return (a['NoUrut'] < b['NoUrut']) ? -1 : 1;
      }
    }
    $scope.total = 0;
    $scope.totalitem = 0;

    $scope.displayCartArray = cartArray.filter(function() {
      return true
    });
    $scope.displayCartArray = $scope.displayCartArray.sort(sortFunction);
    $scope.displayCartArray.forEach(function(entry) {
      $scope.totalitem += parseFloat(entry["QtyBarang"]);
      $scope.total = parseFloat($scope.total.toString().replace(/,/g, "")) + parseFloat(entry["SubTotal"].toString().replace(/,/g, ""));
    });
    $scope.countingGrandTotal();
  }

  $scope.countingGrandTotal = function() {
    $scope.changeSummary();
    $scope.changePembayaran();
  }

  $scope.changeQty = function(a) {
    var QtyVal = $('#QtyBarang' + a).val();
    cartArray[a]['QtyBarang'] = QtyVal;
    cartArray[a]["SubTotal"] = numberWithCommas(cartArray[a]["Harga"].toString().replace(/,/g, "") * QtyVal);
    $scope.displayCart();
  }

  $scope.changeSN = function(a) {
    var SNVal = $('#SNBarang' + a).val();
    cartArray[a]['SNBarang'] = SNVal;
    $scope.displayCart();
  }

  $scope.changeGaransi = function(a) {
    var GaransiBarang = $('#GaransiBarang' + a).val();
    cartArray[a]['Garansi'] = GaransiBarang;
    $scope.displayCart();
  }

  $scope.removeRow = function(a) {
    delete cartArray[a];
    $scope.displayCart();

    return false;
  };

  $scope.changeSummary = function() {
    var DiskonPersen = $('#diskon_persen').val();
    $scope.diskon = (DiskonPersen / 100) * parseFloat($scope.total);
    $scope.total2 = parseFloat($scope.total) - $scope.diskon;


    var PPNPersen = parseFloat($scope.ppn_persen);
    $scope.ppn = (PPNPersen / 100) * parseFloat($scope.total2);
    $scope.grand_total = parseFloat($scope.total2) + parseFloat($scope.ppn) + parseFloat($scope.ongkos_kirim);

    $scope.changePembayaran();

  }

  $scope.changePembayaran = function() {
    var GrandTotal = parseFloat($scope.grand_total);
    var Pembayaran = parseFloat($scope.pembayarandp);

    $scope.kembali = Pembayaran - GrandTotal;
    $scope.sisa = GrandTotal - Pembayaran;

    if ($scope.kembali < 0)
      $scope.kembali = 0;

    if ($scope.sisa < 0)
      $scope.sisa = 0;
  }

  $scope.tanggal = $rootScope.currentDateID;

  $scope.processing = false;
  $scope.submitForm = function(isValid) {
    if (isValid) {
      console.log(cartArray);
      if (cartArray.length === 0) {
        alert("Keranjang belanja anda kosong!");
      } else {
        $scope.processing = true;
        $http({
          method: "POST",
          url: 'api/return-penjualan/new.php',
          data: $.param({
            'no_return_konsumen': $scope.no_return_konsumen,
            'nopenjualan': $scope.anopenjualan,
            'idpelanggan': $scope.aidpelanggan,
            'tanggal': $scope.tanggal,
            'usrlogin': $rootScope.userLoginName,
            'supplier': $scope.supplier,
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
            'metode_pembayaran': $scope.metode_pembayaran,
            'metode_pembayaran2': $scope.metode_pembayaran2,
            'nobg': $scope.nobg,
            'jatuhtempobg': $scope.jatuhtempobg,
            'kembali': $scope.kembali,
            'ongkos_kirim': $scope.ongkos_kirim,
            'totalitem': $scope.totalitem,
            'cart': JSON.stringify(cartArray)
          }),
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).success(function(data, status) {
          if (data.res == "1") {
            ngToast.create({
              className: 'success',
              content: data.mes + ' <i class="fa fa-remove"></i>'
            });
            window.document.location = '#/data-return-penjualan/';
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

tripApp.controller('ReturnPenjualanDetailController', function($rootScope, $scope, $route, $routeParams, $http, ngToast, $location, $timeout) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.processing = false;
  $http.get('api/return-penjualan/detail.php?id=' + $routeParams.returnPenjualanId).success(function(data, status) {
    $scope.no_return = data.detail.no_return;
    $scope.nopenjualan = data.detail.nopenjualan;
    $scope.tanggal = data.detail.tanggal;
    $scope.usrlogin = data.detail.usrlogin;
    $scope.total = data.detail.total_qty;
    $scope.diskon_persen = data.detail.diskon_persen;
    $scope.total2 = data.detail.total2;
    $scope.ppn_persen = data.detail.ppn_persen;
    $scope.grand_total = data.detail.grand_total;
    $scope.pembayarandp = data.detail.pembayarandp;
    $scope.sisa = data.detail.sisa;
    $scope.keterangan = data.detail.keterangan;
    $scope.pelanggan = data.detail.pelanggan;
    $scope.metode_pembayaran = data.detail.metode_pembayaran;
    $scope.metode_pembayaran2 = data.detail.metode_pembayaran2;
    $scope.nobg = data.detail.nobg;
    $scope.jatuhtempobg = data.detail.jatuhtempobg;
    $scope.kembali = data.detail.kembali;
    $scope.total_pembayaran = data.detail.total_pembayaran;
    $scope.completed = data.detail.completed;
    $scope.ongkos_kirim = data.detail.ongkos_kirim;


    if ($scope.metode_pembayaran != null)
      $scope.showMethodPembayaran = true;
    else
      $scope.showMethodPembayaran = false;

    if ($scope.metode_pembayaran2 != null)
      $scope.showMethodPembayaran2 = true;
    else
      $scope.showMethodPembayaran2 = false;

    if ($scope.metode_pembayaran == "Rekening BG") {
      $scope.showBG = true;
      $scope.showBG2 = true;
    } else {
      $scope.showBG = false;
      $scope.showBG2 = false;
    }

    $scope.data_detail = data.detailcart;

    $scope.TotalPenerimaan = data.masterpenerimaan.TotalPenerimaan;
    $scope.NoPenerimaan = data.masterpenerimaan.NoPenerimaan;
    $scope.Tanggal = data.masterpenerimaan.Tanggal;
    $scope.By = data.masterpenerimaan.By;


    $scope.detailpenerimaan = data.detailpenerimaan;

    $scope.detailpembayaran = data.dataPembayaran;
  });

  $scope.gotoLink = function(a, b) {
    $('#myModal2').modal('hide');
    $timeout(function() {
      $location.path(a + b);
    }, 500);
  }

  $scope.doPrint = function() {
    window.open($rootScope.baseURL + 'api/print/print-return-po.php?id=' + $routeParams.returnPOId, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }



  $scope.showModal = function() {
    $('#myModal').modal('show');
    $('#myModal').children('.modal-dialog').removeClass('modal-lg');
  };

  $scope.closeModal = function() {
    $('#myModal').modal('hide');
  }

  $scope.showModal2 = function() {
    $('#myModal2').modal('show');
    $('#myModal2').children('.modal-dialog').removeClass('modal-lg');
  };

  $scope.closeModal2 = function() {
    $('#myModal2').modal('hide');
  }
});
