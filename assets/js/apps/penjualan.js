tripApp.controller('PenjualanController', function ($scope, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
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
  $scope.pelanggan = '';

  $scope.getAllData = function () {
    $http.get('api/penjualan/penjualan.php?act=DataList&datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&pelanggan=' + $scope.pelanggan + '&sales=' + $scope.sales + '&filterstatus=' + $scope.filterstatus).success(function (data, status) {
      $scope.data_penjualan = data.penjualan;
      $scope.data_pelanggan = data.pelanggan;
      $scope.data_sales = data.sales;
      $scope.data_pelanggan = data.pelanggan;
      $scope.kategori_spb = data.kategori_spb;

      $scope.all = data.all;
      $scope.lunas = data.lunas;
      $scope.hutang = data.hutang;
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

  $scope.removeComplete = function (val) {
    if (confirm("Anda yakin ingin membuat SPB ini menjadi belum selesai?")) {
      $http({
        method: "POST",
        url: 'api/penjualan/removeComplete.php',
        data: $.param({
          'idr': val
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        ngToast.create({
          className: 'success',
          content: 'Status SPB ' + val + ' berhasil diubah menjadi belum selesai. <i class="fa fa-remove"></i>'
        });
        $route.reload();
      });
    }
  }

  $scope.removeRow = function (val, noPenjualan) {
    $scope.noPenjualan = noPenjualan;
    $scope.idPenjualan = val;
    $('#modalDelete').modal('show');
  }
  $scope.submitFormDelete = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/penjualan/penjualan.php?act=Delete',
        data: $.param({
          'idr': $scope.idPenjualan,
          'remark': $scope.remark,
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        $scope.processing = false;
        $scope.remark = "";
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data SPB berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $scope.refreshData();
        } else if (data == "2") {
          ngToast.create({
            className: 'danger',
            content: 'Data SPB tidak dapat dihapus karena telah terintegrasi dengan data invoice dan surat jalan... <i class="fa fa-remove"></i>'
          });
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data SPB gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
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
    window.open($rootScope.baseURL + 'api/print/print-penjualan.php?id=' + a, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }

  $scope.doPrintRincian = function (a) {
    window.open($rootScope.baseURL + 'api/print/print-penjualan-rincian.php?id=' + a, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }

  $scope.doPrint2 = function () {
    window.open($rootScope.baseURL + 'api/print/print-data-penjualan-retail.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&sales=' + $scope.sales + '&pelanggan=' + $scope.pelanggan, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }

  $scope.doPrint3 = function () {
    window.open($rootScope.baseURL + 'api/print/print-rekap-penjualan.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('PenjualanNewController', function ($scope, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
  var cartArray = [];
  var noUrut = 0;
  $scope.displayCartArray = [];
  $scope.totalItem = 0;

  $scope.total = 0;
  $scope.sph = "";

  $scope.pem_dp = 0;
  $scope.pem_termin1 = 0;
  $scope.pem_termin2 = 0;
  $scope.pem_termin3 = 0;
  $scope.pem_pelunasan = 0;

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
  $scope.jenis = "Umum";
  $scope.sales = "";
  $scope.noPOKonsumen = "";
  $scope.edit = false;

  $scope.tinymceOptions = {
    menubar: false,
    plugins: 'link lists',
    toolbar: 'undo redo | bold italic | numlist bullist | alignleft aligncenter alignright | link'
  };

  $scope.pelanggan = "";

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.getdata = function () {
    $http.get('api/penjualan/penjualan.php?act=LoadAllRequirement').success(function (data, status) {
      $scope.data_pelanggan = data.pelanggan;
      $scope.data_barang = data.barang;
      $scope.data_sph = data.sph;
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
      $scope.HPP = $scope.data_barang[$scope.kode].HPP;
      $scope.HPPReal = $scope.data_barang[$scope.kode].HPPReal;

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

  $scope.changeSPH = function () {
    var ID = $('#sph').val();

    if (ID.toString() !== '0') {
      //alert(ID);
      $http.get('api/penjualan/penjualan.php?act=LoadSPHDetail&nosph=' + ID).success(function (data, status) {
        cartArray = [];
        noUrut = 0;
        for (i in data.detail) {
          //console.log(i);
          cartArray[noUrut] = { NoUrut: noUrut, IDBarang: data.detail[i].IDBarang, NamaBarang: data.detail[i].NamaBarang, NamaBarangDisplay: data.detail[i].NamaBarangDisplay, QtyBarang: data.detail[i].Qty, SNBarang: data.detail[i].SN, IsSerialize: data.detail[i].IsSerialize, Limit: data.detail[i].Limit, Harga: data.detail[i].Harga, HPP: data.detail[i].HPP, HPPReal: data.detail[i].HPPReal, Margin: data.detail[i].Margin, SubTotal: data.detail[i].SubTotal, isParent: data.detail[i].isParent, isChild: data.detail[i].isChild, Diskon: data.detail[i].Diskon, HargaDiskon: data.detail[i].HargaDiskon, UseDiskonForCalc: true };
          noUrut++;
        }
        $scope.diskon_persen = data.DiskonPersen;
        $scope.ppn_persen = data.PPNPersen;
        $scope.displayCart();
        $scope.pelanggan = data.pelanggan;
        $scope.sales = data.sales;
        console.log(cartArray);
        $("#pelanggan").select2("val", $scope.pelanggan);
        $("#sales").select2("val", $scope.sales);
      });
    }
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
          cartArray[noUrut] = { NoUrut: noUrut, IDBarang: IDBarang, NamaBarang: NamaBarang, NamaBarangDisplay: NamaBarang, QtyBarang: Qty, SNBarang: SN, IsSerialize: IsSerialize, Limit: Limit, Harga: Harga, HargaDiskon: Harga, HPP: HPP, HPPReal: HPPReal, Margin: Margin, SubTotal: SubTotal, Diskon: 0, UseDiskonForCalc: true };
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
        url: 'api/penjualan/penjualan.php?act=InsertNew',
        data: $.param({
          'tanggal': $scope.tanggal,
          'sph': $scope.sph,
          'kategori': $scope.kategori,
          'jenis': $scope.jenis,
          'usrlogin': $rootScope.userLoginName,
          'pelanggan': $scope.pelanggan,
          'sales': $scope.sales,
          'no_po_konsumen': $scope.noPOKonsumen,
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
          'metode_pembayaran': $scope.metode_pembayaran,
          'metode_pembayaran2': $scope.metode_pembayaran2,
          'kembali': $scope.kembali,
          'totalHPP': $scope.totalHPP,
          'totalHPPReal': $scope.totalHPPReal,
          'totalMargin': $scope.totalMargin,
          'prihal': $scope.prihal,
          'term_condition': $scope.term_condition,
          'included': $scope.included,
          'tanggal_pemasangan': $scope.tanggal_pemasangan,
          'kondisi_pembayaran': $scope.kondisi_pembayaran,
          'ongkos_kirim': $scope.ongkos_kirim,
          'pem_dp': $scope.pem_dp,
          'pem_termin1': $scope.pem_termin1,
          'pem_termin2': $scope.pem_termin2,
          'pem_termin3': $scope.pem_termin3,
          'pem_pelunasan': $scope.pem_pelunasan,
          'cart': JSON.stringify(cartArray)
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data.res == "1") {
          ngToast.create({
            className: 'success',
            content: data.mes + ' <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-penjualan/';
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

tripApp.controller('PenjualanDetailController', function ($rootScope, $scope, $route, $routeParams, $http, ngToast) {
  $http.get('api/penjualan/penjualan.php?act=Detail&id=' + $routeParams.penjualanId).success(function (data, status) {
    $scope.no_penjualan = data.master.NoPenjualan;
    $scope.pelanggan = data.master.Pelanggan;
    $scope.tanggal = data.master.Tanggal;
    $scope.noPOKonsumen = data.master.NoPOKonsumen;
    $scope.sales = data.master.Sales;
    $scope.kategori = data.master.Kategori;
    $scope.totalItem = data.master.TotalItem;
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
    $scope.metode_pembayaran = data.master.MetodePembayaran1;
    $scope.metode_pembayaran2 = data.master.MetodePembayaran2;
    $scope.totalHPP = data.master.TotalHPP;
    $scope.totalMargin = data.master.TotalMargin;
    $scope.sph = data.master.NoSPH;
    $scope.jenis = data.master.Jenis;

    $scope.pem_dp = data.master.pem_dp;
    $scope.pem_termin1 = data.master.pem_termin1;
    $scope.pem_termin2 = data.master.pem_termin2;
    $scope.pem_termin3 = data.master.pem_termin3;
    $scope.pem_pelunasan = data.master.pem_pelunasan;

    $scope.keterangan = data.master.Keterangan;
    $scope.prihal = data.master.Prihal;
    $scope.term_condition = data.master.TermAndCondition;
    $scope.included = data.master.Included;
    $scope.tanggal_pemasangan = data.master.TanggalPemasangan;
    $scope.kondisi_pembayaran = data.master.KondisiPembayaran;
    $scope.ongkos_kirim = data.master.OngkosKirim;

    $scope.data_detail = data.detail;
    $scope.data_detail_history = data.detailHistory;
    $scope.detailpengiriman = data.pengiriman;
    $scope.dataSupplier = data.dataSupplier;
    $scope.notifikasiMaintenance = data.notifikasiMaintenance;

    $scope.is_complete = data.master.is_complete;

    $scope.deleted_by = data.master.deleted_by;
    $scope.deleted_date = data.master.deleted_date;
    $scope.deleted_remark = data.master.deleted_remark;
  });

  $scope.doPrint = function () {
    window.open($rootScope.baseURL + 'api/print/print-penjualan.php?id=' + $routeParams.penjualanId, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }

  $scope.showModal = function () {
    $('#myModal').modal('show');
    $('#myModal').children('.modal-dialog').removeClass('modal-lg');
  };

  $scope.showModalHistoryVO = function () {
    $('#modalHistoryVO').modal('show');
    $('#modalHistoryVO').children('.modal-dialog').removeClass('modal-lg');
  };

  $scope.showModalNotifikasiMaintenance = function () {
    $('#modalNotifikasiMaintenance').modal('show');
    $('#modalNotifikasiMaintenance').children('.modal-dialog').removeClass('modal-lg');
  };

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    $scope.processing = true;
    $http({
      method: "POST",
      url: 'api/penjualan/penjualan.php?act=SetComplete',
      data: $.param({
        'id': $routeParams.penjualanId,
        'is_complete': $scope.is_complete,
        'noPOKonsumen': $scope.noPOKonsumen,
        'keterangan': $scope.keterangan,
        'prihal': $scope.prihal,
        'term_condition': $scope.term_condition,
        'included': $scope.included,
        'tanggal_pemasangan': $scope.tanggal_pemasangan,
        'kondisi_pembayaran': $scope.kondisi_pembayaran,
        'pem_dp': $scope.pem_dp,
        'pem_termin1': $scope.pem_termin1,
        'pem_termin2': $scope.pem_termin2,
        'pem_termin3': $scope.pem_termin3,
        'pem_pelunasan': $scope.pem_pelunasan
      }),
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    }).success(function (data, status) {
      $scope.processing = false;
      if (data.res == "1") {
        ngToast.create({
          className: 'success',
          content: data.mes + ' <i class="fa fa-remove"></i>'
        });
      } else {
        ngToast.create({
          className: 'danger',
          content: data.mes + ' <i class="fa fa-remove"></i>'
        });
      }
    });
  };
});

tripApp.controller('PenjualanEditController', function ($rootScope, $scope, $route, $routeParams, $http, ngToast, CommonServices) {
  var cartArray = [];
  var noUrut = 0;
  $scope.displayCartArray = [];
  $scope.totalItem = 0;
  $scope.isParentCheckbox = 0;

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
  $scope.edit = true;

  $scope.tinymceOptions = {
    menubar: false,
    plugins: 'link lists',
    toolbar: 'undo redo | bold italic | numlist bullist | alignleft aligncenter alignright | link'
  };

  $scope.pelanggan = "";

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.getdata = function () {
    $http.get('api/penjualan/penjualan.php?act=LoadAllRequirement').success(function (data, status) {
      $scope.data_pelanggan = data.pelanggan;
      $scope.data_barang = data.barang;
      $scope.data_sph = data.sph;
      $scope.data_sales = data.sales;
    });
  };

  $scope.getdata();

  $http.get('api/penjualan/penjualan.php?act=Detail&skipVO=1&id=' + $routeParams.penjualanId).success(function (data, status) {
    $scope.no_penjualan = data.master.NoPenjualan;
    $scope.pelanggan = data.master.IDPelanggan;
    $scope.tanggal = data.master.Tanggal;
    $scope.noPOKonsumen = data.master.NoPOKonsumen;
    $scope.sales = data.master.IDSales;
    $scope.kategori = data.master.Kategori;
    $scope.totalItem = data.master.TotalItem;
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
    $scope.metode_pembayaran = data.master.MetodePembayaran1;
    $scope.metode_pembayaran2 = data.master.MetodePembayaran2;
    $scope.totalHPP = data.master.TotalHPP;
    $scope.totalMargin = data.master.TotalMargin;
    $scope.sph = data.master.NoSPH;
    $scope.jenis = data.master.Jenis;

    $scope.pem_dp = data.master.pem_dp;
    $scope.pem_termin1 = data.master.pem_termin1;
    $scope.pem_termin2 = data.master.pem_termin2;
    $scope.pem_termin3 = data.master.pem_termin3;
    $scope.pem_pelunasan = data.master.pem_pelunasan;

    $scope.prihal = data.master.Prihal;
    $scope.term_condition = data.master.TermAndCondition;
    $scope.included = data.master.Included;
    $scope.tanggal_pemasangan = data.master.TanggalPemasangan;
    $scope.kondisi_pembayaran = data.master.KondisiPembayaran;
    $scope.ongkos_kirim = data.master.OngkosKirim;

    $scope.locked_top = data.master.locked_top;

    $scope.is_complete = data.master.IsComplete;

    $scope.can_edit = data.master.can_edit;

    $scope.data_detail = data.detail;
    $scope.data_sph = data.sph;

    cartArray = data.detailCart;
    noUrut = data.nourut;

    setTimeout(() => {
      console.log($scope.sph)
      $("#sph").select2("val", $scope.sph);
      $("#pelanggan").select2("val", $scope.pelanggan);
      $("#sales").select2("val", $scope.sales);
    }, 500);
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
      $scope.HPP = $scope.data_barang[$scope.kode].HPP;

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

  $scope.changeSPH = function () {
    var ID = $('#sph').val();

    if (ID.toString() !== '0') {
      //alert(ID);
      $http.get('api/penjualan/penjualan.php?act=LoadSPHDetail&nosph=' + ID).success(function (data, status) {
        cartArray = [];
        noUrut = 0;
        for (i in data.detail) {
          //console.log(i);
          cartArray[noUrut] = { NoUrut: noUrut, IDBarang: data.detail[i].IDBarang, NamaBarang: data.detail[i].NamaBarang, NamaBarangDisplay: data.detail[i].NamaBarangDisplay, QtyBarang: data.detail[i].Qty, SNBarang: data.detail[i].SN, IsSerialize: data.detail[i].IsSerialize, Limit: data.detail[i].Limit, Harga: data.detail[i].Harga, HPP: data.detail[i].HPP, HPPReal: data.detail[i].HPPReal, Margin: data.detail[i].Margin, SubTotal: data.detail[i].SubTotal, isParent: data.detail[i].isParent, isChild: data.detail[i].isChild, Diskon: data.detail[i].Diskon, HargaDiskon: data.detail[i].HargaDiskon, UseDiskonForCalc: true };
          noUrut++;
        }
        $scope.diskon_persen = data.DiskonPersen;
        $scope.ppn_persen = data.PPNPersen;
        $scope.displayCart();
        $scope.pelanggan = data.pelanggan;
        $scope.sales = data.sales;
        console.log(cartArray);
        $("#pelanggan").select2("val", $scope.pelanggan);
        $("#sales").select2("val", $scope.sales);
      });
    }
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
          cartArray[noUrut] = { NoUrut: noUrut, IDBarang: IDBarang, NamaBarang: NamaBarang, NamaBarangDisplay: NamaBarang, QtyBarang: Qty, SNBarang: SN, IsSerialize: IsSerialize, Limit: Limit, Harga: Harga, HargaDiskon: Harga, HPP: HPP, HPPReal: HPPReal, Margin: Margin, SubTotal: SubTotal, Diskon: 0, UseDiskonForCalc: true };
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
      $scope.totalMargin += (parseFloat(entry["QtyBarang"]) * parseFloat(entry["Margin"]));
      $scope.total = parseFloat($scope.total.toString().replace(/,/g, "")) + parseFloat(entry["SubTotal"].toString().replace(/,/g, ""));
    });
    console.log($scope.totalHPP);
    console.log($scope.totalMargin);
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

  $scope.calcHargaAndSubTotal = function (NoUrut, useDiskonForCalc) {
    if (useDiskonForCalc !== null) cartArray[NoUrut]['UseDiskonForCalc'] = useDiskonForCalc;

    var Harga = CommonServices.getInputValueAsFloat('#Harga' + NoUrut);
    var Diskon = CommonServices.getInputValueAsNumber('#Diskon' + NoUrut);
    var HargaDiskon = CommonServices.getInputValueAsFloat('#HargaDiskon' + NoUrut);
    var QtyBarang = CommonServices.getInputValueAsInt('#QtyBarang' + NoUrut);
    var HPP = CommonServices.getInputValueAsFloat('#HPP' + NoUrut);

    cartArray[NoUrut]['HPP'] = HPP;
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
        url: 'api/penjualan/penjualan.php?act=Edit',
        data: $.param({
          'id': $routeParams.penjualanId,
          'no_penjualan': $scope.no_penjualan,
          'tanggal': $scope.tanggal,
          'kategori': $scope.kategori,
          'jenis': $scope.jenis,
          'sph': $scope.sph,
          'usrlogin': $rootScope.userLoginName,
          'pelanggan': $scope.pelanggan,
          'sales': $scope.sales,
          'no_po_konsumen': $scope.noPOKonsumen,
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
          'metode_pembayaran': $scope.metode_pembayaran,
          'metode_pembayaran2': $scope.metode_pembayaran2,
          'kembali': $scope.kembali,
          'totalHPP': $scope.totalHPP,
          'totalHPPReal': $scope.totalHPPReal,
          'totalMargin': $scope.totalMargin,
          'prihal': $scope.prihal,
          'term_condition': $scope.term_condition,
          'included': $scope.included,
          'tanggal_pemasangan': $scope.tanggal_pemasangan,
          'kondisi_pembayaran': $scope.kondisi_pembayaran,
          'ongkos_kirim': $scope.ongkos_kirim,
          'pem_dp': $scope.pem_dp,
          'pem_termin1': $scope.pem_termin1,
          'pem_termin2': $scope.pem_termin2,
          'pem_termin3': $scope.pem_termin3,
          'pem_pelunasan': $scope.pem_pelunasan,
          'is_complete': $scope.is_complete,
          'cart': JSON.stringify(cartArray)
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data.res == "1") {
          ngToast.create({
            className: 'success',
            content: data.mes + ' <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-penjualan/';
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

