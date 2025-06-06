tripApp.controller('VOSPBController', function ($scope, $rootScope, $routeParams, $route, $http, ngToast) {

  $scope.getdata = function () {
    $http.get('api/vo-spb/data-vo.php?id_penjualan=' + $routeParams.idPenjualan).success(function (data, status) {
      $scope.data_vo = data.data;
      $scope.GrandTotal = data.GrandTotal;
      $scope.GrandTotalInvoice = data.GrandTotalInvoice;
      $scope.PiutangProgress = data.PiutangProgress;
      $scope.SisaPenagihan = data.SisaPenagihan;
      $scope.DetailSPB = data.DetailSPB;
    });
  };

  $scope.getdata();
  $scope.idPenjualan = $routeParams.idPenjualan;

  $scope.removeRow = function (val, noVO) {
    $scope.idVO = val;
    $scope.noVO = noVO;
    $('#modalDelete').modal('show');
  }

  $scope.submitFormDelete = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/vo-spb/delete.php',
        data: $.param({
          'idr': $scope.idVO,
          'remark': $scope.remark,
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data Variant Order SPB berhasil dihapus. <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data Variant Order SPB gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
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
    window.open($rootScope.baseURL + 'api/print/print-invoice.php?id=' + a, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('VOController', function ($scope, $rootScope, $routeParams, $route, $http, ngToast, CommonServices) {

  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.getdata = function () {
    $http.get('api/vo-spb/data-vo-all.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&kode_proyek=' + $scope.kode_proyek).success(function (data, status) {
      $scope.data_vo = data.data;
      $scope.proyek = data.proyek;
    });
  };

  $scope.kode_proyek = "";

  $scope.refreshData = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }

  $scope.getdata();

  $scope.removeRow = function (val) {
    if (confirm("Anda yakin ingin menghapus data ini?")) {
      $http({
        method: "POST",
        url: 'api/invoice-spb/delete.php',
        data: $.param({
          'idr': val
        }),
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data proyek invoice berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else if (data == "2") {
          ngToast.create({
            className: 'danger',
            content: 'Data proyek invoice gagal dihapus karena terintegrasi dengan jurnal... <i class="fa fa-remove"></i>'
          });
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data proyek invoice gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }

  $scope.doPrint = function (a) {
    window.open($rootScope.baseURL + 'api/print/print-invoice.php?id=' + a, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }

  $scope.showModal2 = function (a) {
    $http.get('api/invoice-spb/data-pembayaran.php?id=' + a.IDInvoice).success(function (data, status) {
      $scope.NoInv = a.NoInv;
      $scope.detailpembayaran = data.DetailPembayaran;
      $scope.GrandTotal = data.GrandTotal;
      $scope.Terbayar = data.Terbayar;
      $scope.Sisa = data.Sisa;
    });
    $('#myModal2').modal('show');
    $('#myModal2').children('.modal-dialog').removeClass('modal-lg');
  };

  $scope.closeModal2 = function () {
    $('#myModal2').modal('hide');
  }
});

tripApp.controller('VOSPBNewController', function ($scope, $rootScope, $routeParams, $route, $http, ngToast, CommonServices) {
  var cartArray = [];
  var noUrut = 0;
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
  $scope.edit = false;

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.getdata = function () {
    $http.get('api/vo-spb/load-all-requirement.php?id_penjualan=' + $routeParams.idPenjualan).success(function (data, status) {
      $scope.data_barang = data.barang;
      $scope.data_spb = data.spb;
      $scope.diskon_persen = $scope.data_spb.DiskonPersen;
      $scope.ppn_persen = $scope.data_spb.PPNPersen;
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
      $scope.HPP = $scope.data_barang[$scope.kode].HPP;
      $scope.HPPReal = $scope.data_barang[$scope.kode].HPPReal;

      $scope.Diskon = $scope.data_barang[$scope.kode].Diskon;
      $scope.HargaDiskon = $scope.data_barang[$scope.kode].HargaDiskon;
      $scope.EditableDiskonAndPrice = $scope.data_barang[$scope.kode].EditableDiskonAndPrice;

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
      // console.log($scope.qty);
      if ($scope.aidbarang > 0) {
        var IDBarang = $scope.aidbarang;
        var NamaBarang = $scope.anama;
        var Qty = $scope.qty;
        var Limit = $scope.limit;
        var SN = $scope.serialnumber;
        var Harga = parseFloat($scope.harga);
        var HPP = parseFloat($scope.HPP);
        var HPPReal = parseFloat($scope.HPPReal);
        var IsSerialize = $scope.isserialize;
        var updated = false;

        var Diskon = $scope.Diskon;
        var EditableDiskonAndPrice = $scope.EditableDiskonAndPrice;

        var HargaDiskon = ($scope.HargaDiskon > 0)
          ? parseFloat($scope.HargaDiskon)
          : Harga - CommonServices.getDiscountValue(Diskon, Harga);

        var SubTotal = HargaDiskon * Qty;
        var Margin = SubTotal - (HPP * Qty);

        if (IsSerialize == 0) {
          cartArray.forEach(function (entry) {
            if (IDBarang == entry["IDBarang"]) {
              updated = true;
              entry["QtyBarang"] = parseInt(entry["QtyBarang"]) + parseInt(Qty);
              entry["SubTotal"] = parseFloat(entry["QtyBarang"]) * (parseFloat(entry["Harga"]) - parseFloat(entry["Diskon"]));
              entry["Margin"] = parseFloat(entry["SubTotal"]) - (parseFloat(entry["QtyBarang"]) * parseFloat(entry["HPP"]));
            }
          });
        }
        //alert(cartArray);
        if (!updated) {
          //if(typeof cartArray[IDBarang] != 'undefined'){
          //Qty = $('#QtyBarang'+IDBarang).val();
          //cartArray[IDBarang]['Qty'] = parseInt(Qty)+1;
          //cartArray[IDBarang]['Total'] = (cartArray[IDBarang]['Qty']*HargaJual);
          //console.log("OK");
          //} else {
          cartArray[noUrut] = { NoUrut: noUrut, IDBarang: IDBarang, NamaBarang: NamaBarang, NamaBarangDisplay: NamaBarang, QtyBarang: Qty, SNBarang: SN, IsSerialize: IsSerialize, Limit: Limit, Harga: Harga, HPP: HPP, HPPReal: HPPReal, Margin: Margin, SubTotal: SubTotal, Diskon: Diskon, HargaDiskon: HargaDiskon, EditableDiskonAndPrice: EditableDiskonAndPrice, UseDiskonForCalc: true };
          noUrut += 1;
          //}
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
        console.log(cartArray);
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
      if (entry["isParent"] !== 1)
        $scope.totalItem += parseFloat(entry["QtyBarang"]);
      $scope.totalHPP += (parseFloat(entry["QtyBarang"]) * parseFloat(entry["HPP"]));
      $scope.totalHPPReal += (parseFloat(entry["QtyBarang"]) * parseFloat(entry["HPPReal"]));
      $scope.total = parseFloat($scope.total.toString().replace(/,/g, "")) + parseFloat(entry["SubTotal"].toString().replace(/,/g, ""));
    });
    $scope.countingGrandTotal();
  }

  $scope.changeQty = function (a) {
    var QtyVal = parseFloat($('#QtyBarang' + a).val());
    var HPP = parseFloat(cartArray[a]['HPP']);
    var Harga = cartArray[a].HargaDiskon;

    cartArray[a]['QtyBarang'] = QtyVal;
    cartArray[a]["SubTotal"] = Harga * QtyVal;

    cartArray[a]["Margin"] = parseFloat(cartArray[a]["SubTotal"]) - (HPP * QtyVal);
    $scope.displayCart();
  }

  $scope.changeIsParent = function (a) {
    var isParent = $('#isParent' + a).is(':checked') ? $('#isParent' + a).val() : '1';
    cartArray[a]['isParent'] = parseInt(isParent);

    var HPP = parseFloat(cartArray[a]['HPP']);
    var QtyVal = parseFloat(cartArray[a]['QtyBarang']);

    cartArray[a]["Margin"] = parseFloat(cartArray[a]["SubTotal"]) - (HPP * QtyVal);
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

  $scope.validate = function () {
    if ($scope.tanggal === '') {
      ngToast.create({
        className: 'danger',
        content: 'Silahkan lengkapi Tanggal.'
      });
      return false;
    }

    if ($scope.grand_total === 0) {
      ngToast.create({
        className: 'danger',
        content: 'Grand Total tidak boleh sama dengan nol.'
      });
      return false;
    }

    return true;
  }

  $scope.processing = false;
  $scope.submitForm = function () {
    if ($scope.validate()) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/vo-spb/new.php',
        data: $.param({
          'id_penjualan': $routeParams.idPenjualan,
          'tanggal': $scope.tanggal,
          'usrlogin': $rootScope.userLoginName,
          'total_item': $scope.totalItem,
          'total': $scope.total,
          'diskon_persen': $scope.diskon_persen,
          'diskon': $scope.diskon,
          'total2': $scope.total2,
          'ppn_persen': $scope.ppn_persen,
          'ppn': $scope.ppn,
          'grand_total': $scope.grand_total,
          'keterangan': $scope.keterangan,
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
          window.document.location = '#/data-vo-spb/' + $routeParams.idPenjualan;
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

tripApp.controller('VOSPBEditController', function ($scope, $rootScope, $route, $routeParams, $http, ngToast, CommonServices) {
  var cartArray = [];
  var noUrut = 0;
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
  $scope.edit = true;

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.getdata = function () {
    $http.get('api/vo-spb/load-all-requirement.php?id_penjualan=' + $routeParams.idPenjualan).success(function (data, status) {
      $scope.data_barang = data.barang;
    });
  };

  $scope.getdata();

  $http.get('api/vo-spb/detail.php?id=' + $routeParams.voId).success(function (data, status) {
    cartArray = data.detail;
    $scope.displayCartArray = data.detail;
    $scope.no_vo = data.master.NoVO;
    $scope.tanggal = data.master.TanggalID;
    $scope.data_spb = data.spb;
    $scope.totalItem = data.master.TotalItem;
    $scope.total = data.master.Total;
    $scope.ppn = data.master.PPN;
    $scope.ppn_persen = data.master.PPNPersen;
    $scope.diskon = data.master.Diskon;
    $scope.diskon_persen = data.master.DiskonPersen;
    $scope.total2 = data.master.Total2;
    $scope.grand_total = data.master.GrandTotal;
    $scope.keterangan = data.master.Keterangan;
    $scope.idPenjualan = data.master.IDPenjualan;
    $scope.displayCart();
  });

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
      $scope.HPP = $scope.data_barang[$scope.kode].HPP;
      $scope.HPPReal = $scope.data_barang[$scope.kode].HPPReal;

      $scope.Diskon = $scope.data_barang[$scope.kode].Diskon;
      $scope.DiskonPersen = $scope.data_barang[$scope.kode].DiskonPersen;
      $scope.DiskonValue = $scope.data_barang[$scope.kode].DiskonValue;
      $scope.DiskonType = $scope.data_barang[$scope.kode].DiskonType;
      $scope.EditableDiskonAndPrice = $scope.data_barang[$scope.kode].EditableDiskonAndPrice;

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
      // console.log($scope.qty);
      if ($scope.aidbarang > 0) {
        var IDBarang = $scope.aidbarang;
        var NamaBarang = $scope.anama;
        var Qty = $scope.qty;
        var Limit = $scope.limit;
        var SN = $scope.serialnumber;
        var Harga = parseFloat($scope.harga);
        var HPP = parseFloat($scope.HPP);
        var HPPReal = parseFloat($scope.HPPReal);
        var IsSerialize = $scope.isserialize;
        var updated = false;

        var Diskon = $scope.Diskon;
        var EditableDiskonAndPrice = $scope.EditableDiskonAndPrice;

        var HargaDiskon = Harga - CommonServices.getDiscountValue(Diskon, Harga);

        var SubTotal = HargaDiskon * Qty;
        var Margin = SubTotal - (HPP * Qty);

        if (IsSerialize == 0) {
          cartArray.forEach(function (entry) {
            if (IDBarang == entry["IDBarang"]) {
              updated = true;
              entry["QtyBarang"] = parseInt(entry["QtyBarang"]) + parseInt(Qty);
              entry["SubTotal"] = parseFloat(entry["QtyBarang"]) * (parseFloat(entry["Harga"]) - parseFloat(entry["Diskon"]));
              entry["Margin"] = parseFloat(entry["SubTotal"]) - (parseFloat(entry["QtyBarang"]) * parseFloat(entry["HPP"]));
            }
          });
        }
        //alert(cartArray);
        if (!updated) {
          //if(typeof cartArray[IDBarang] != 'undefined'){
          //Qty = $('#QtyBarang'+IDBarang).val();
          //cartArray[IDBarang]['Qty'] = parseInt(Qty)+1;
          //cartArray[IDBarang]['Total'] = (cartArray[IDBarang]['Qty']*HargaJual);
          //console.log("OK");
          //} else {
          cartArray[noUrut] = { NoUrut: noUrut, IDBarang: IDBarang, NamaBarang: NamaBarang, NamaBarangDisplay: NamaBarang, QtyBarang: Qty, SNBarang: SN, IsSerialize: IsSerialize, Limit: Limit, Harga: Harga, HPP: HPP, HPPReal: HPPReal, Margin: Margin, SubTotal: SubTotal, Diskon: Diskon, HargaDiskon: HargaDiskon, EditableDiskonAndPrice: EditableDiskonAndPrice, UseDiskonForCalc: true };
          noUrut += 1;
          //}
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
        console.log(cartArray);
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
      if (entry["isParent"] !== 1)
        $scope.totalItem += parseFloat(entry["QtyBarang"]);
      $scope.totalHPP += (parseFloat(entry["QtyBarang"]) * parseFloat(entry["HPP"]));
      $scope.totalHPPReal += (parseFloat(entry["QtyBarang"]) * parseFloat(entry["HPPReal"]));
      $scope.total = parseFloat($scope.total.toString().replace(/,/g, "")) + parseFloat(entry["SubTotal"].toString().replace(/,/g, ""));
    });
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

  $scope.changeIsParent = function (a) {
    var isParent = $('#isParent' + a).is(':checked') ? $('#isParent' + a).val() : '1';
    cartArray[a]['isParent'] = parseInt(isParent);

    var HPP = parseFloat(cartArray[a]['HPP']);
    var QtyVal = parseFloat(cartArray[a]['QtyBarang']);

    cartArray[a]["Margin"] = parseFloat(cartArray[a]["SubTotal"]) - (HPP * QtyVal);
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

  $scope.changeHargaJual = function (a) {
    var Harga = parseFloat($('#HargaJual' + a).val());
    var QtyVal = parseFloat(cartArray[a]['QtyBarang']);
    var HPP = parseFloat(cartArray[a]['HPP']);
    var DiskonValue = cartArray[a]['DiskonValue'];

    if (DiskonValue.substr(DiskonValue.length - 1) === '%') {
      var DiskonPersen = parseInt(DiskonValue.substr(0, DiskonValue.length - 1));
      var Diskon = Harga * DiskonPersen / 100;
      var DiskonType = 1;
    } else {
      var Diskon = parseFloat(DiskonValue);
      var DiskonPersen = Diskon / Harga * 100;
      var DiskonType = 0;
    }

    cartArray[a]['DiskonValue'] = DiskonValue;
    cartArray[a]['DiskonType'] = DiskonType;
    cartArray[a]['DiskonPersen'] = DiskonPersen;
    cartArray[a]['Diskon'] = Diskon;

    cartArray[a]['Harga'] = Harga;
    cartArray[a]["SubTotal"] = (Harga - Diskon) * QtyVal;

    cartArray[a]["Margin"] = parseFloat(cartArray[a]["SubTotal"]) - (HPP * QtyVal);

    $scope.displayCart();
  }

  $scope.changeDiskonCart = function (a) {
    var DiskonValue = $('#DiskonValue' + a).val();
    console.log(DiskonValue);
    var Harga = parseFloat(cartArray[a]['Harga']);
    var QtyVal = parseFloat(cartArray[a]['QtyBarang']);
    var HPP = parseFloat(cartArray[a]['HPP']);

    if (DiskonValue.substr(DiskonValue.length - 1) === '%') {
      var DiskonPersen = parseInt(DiskonValue.substr(0, DiskonValue.length - 1));
      var Diskon = Harga * DiskonPersen / 100;
      var DiskonType = 1;
    } else {
      var Diskon = parseFloat(DiskonValue);
      var DiskonPersen = Diskon / Harga * 100;
      var DiskonType = 0;
    }

    cartArray[a]['DiskonValue'] = DiskonValue;
    cartArray[a]['DiskonType'] = DiskonType;
    cartArray[a]['DiskonPersen'] = DiskonPersen;
    cartArray[a]['Diskon'] = Diskon;

    cartArray[a]['Harga'] = Harga;
    cartArray[a]["SubTotal"] = (Harga - Diskon) * QtyVal;

    cartArray[a]["Margin"] = parseFloat(cartArray[a]["SubTotal"]) - (HPP * QtyVal);

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

  // $scope.changeDiskon = function() {
  //   var DiskonPersen = $('#diskon_persen').val();
  //   $scope.diskon = (DiskonPersen / 100) * parseFloat($scope.total);
  //   $scope.total2 = parseFloat($scope.total) - $scope.diskon;
  // }
  $scope.changeDiskon = function () {
    var d = $scope.diskon_persen.toString();
    var DiskonPersen = d.split("+");
    var totalDiskon = 0;
    var totalNilai = parseFloat($scope.total);
    var diskon = 0;
    for (i = 0; i < DiskonPersen.length; i++) {
      diskon = Math.round(((parseFloat(DiskonPersen[i]) / 100) * totalNilai));
      totalNilai = totalNilai - diskon;
      totalDiskon = totalDiskon + diskon;
    }
    $scope.diskon = totalDiskon;
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
        url: 'api/vo-spb/edit.php',
        data: $.param({
          'id_vo': $routeParams.voId,
          'id_penjualan': $scope.idPenjualan,
          'tanggal': $scope.tanggal,
          'usrlogin': $rootScope.userLoginName,
          'total_item': $scope.totalItem,
          'total': $scope.total,
          'diskon_persen': $scope.diskon_persen,
          'diskon': $scope.diskon,
          'total2': $scope.total2,
          'ppn_persen': $scope.ppn_persen,
          'ppn': $scope.ppn,
          'grand_total': $scope.grand_total,
          'keterangan': $scope.keterangan,
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
          window.document.location = '#/data-vo-spb/' + $scope.idPenjualan;
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

tripApp.controller('VOSPBDetailController', function ($scope, $rootScope, $route, $routeParams, $http, ngToast) {
  $scope.totalItem = 0;
  $scope.total = 0;
  $scope.ppn = 0;
  $scope.ppn_persen = 0;
  $scope.diskon = 0;
  $scope.diskon_persen = 0;
  $scope.total2 = 0;
  $scope.grand_total = 0;

  $http.get('api/vo-spb/detail.php?id=' + $routeParams.voId).success(function (data, status) {
    $scope.displayCartArray = data.detail;
    $scope.no_vo = data.master.NoVO;
    $scope.tanggal = data.master.TanggalID;
    $scope.data_spb = data.spb;
    $scope.totalItem = data.master.TotalItem;
    $scope.total = data.master.Total;
    $scope.ppn = data.master.PPN;
    $scope.ppn_persen = data.master.PPNPersen;
    $scope.diskon = data.master.Diskon;
    $scope.diskon_persen = data.master.DiskonPersen;
    $scope.total2 = data.master.Total2;
    $scope.grand_total = data.master.GrandTotal;
    $scope.keterangan = data.master.Keterangan;
    $scope.idPenjualan = data.master.IDPenjualan;

    $scope.deleted_by = data.master.DeletedBy;
    $scope.deleted_date = data.master.DeletedDateID;
    $scope.deleted_remark = data.master.DeletedRemark;
  });
});
