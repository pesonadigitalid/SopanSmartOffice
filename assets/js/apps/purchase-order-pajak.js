tripApp.controller('PurchaseOrderPajakController', function ($rootScope, $scope, $q, $routeParams, $route, $http, ngToast, CommonServices) {
  $scope.kode_proyek = "";
  $scope.supplier = "";
  $scope.kategori = "";
  $scope.filterstatus = "";
  $scope.activeMenu = '';

  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();

  $scope.isPajak = 1;
  $scope.isLD = 0;

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });


  $scope.getdata = function () {
    $http.get('api/purchase-order/data-purchase-order.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&kode_proyek=' + $scope.kode_proyek + '&supplier=' + $scope.supplier + '&kategori=' + $scope.kategori + '&status=' + $scope.filterstatus + '&ispajak=' + $scope.isPajak + '&isld=' + $scope.isLD).success(function (data, status) {
      $scope.data_purchase = data.po;
      $scope.data_proyek = data.proyek;
      $scope.data_supplier = data.supplier;

      $scope.GrandTotal = data.grandTotal;
      $scope.SisaHutang = data.sisa;

      $scope.all = data.all;
      $scope.new = data.new;
      $scope.complete = data.completed;
    });
  };

  $scope.getdata();

  $scope.doFilter = function (a) {
    $scope.filterstatus = a;
    $scope.activeMenu = a;
    $scope.refreshData();
  }

  $scope.refreshData = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }

  $scope.setComplete = function (val) {
    $http({
      method: "POST",
      url: 'api/purchase-order/setComplete.php',
      data: $.param({
        'idr': val
      }),
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    }).success(function (data, status) {
      ngToast.create({
        className: 'success',
        content: 'Status PO ' + val + ' berhasil diubah menjadi complete. <i class="fa fa-remove"></i>'
      });
      $route.reload();
    });
  }

  $scope.removeRow = function (val) {
    $scope.noPO = val;
    $('#modalDelete').modal('show');
  }

  $scope.submitFormDelete = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/purchase-order/delete.php',
        data: $.param({
          'idr': $scope.noPO,
          'remark': $scope.remark,
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        $scope.processing = false;
        $scope.remark = "";
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data purchase order berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else if (data == "2") {
          ngToast.create({
            className: 'danger',
            content: 'PO tidak dapat dihapus karena telah tereferensi dalam data Penerimaan Stok... <i class="fa fa-remove"></i>'
          });
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data purchase order gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
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

  $scope.doPrint = function (a, hideDiskon = false) {
    const param = hideDiskon ? '&hideDiskon=1' : '';
    window.open($rootScope.baseURL + 'api/print/print-po.php?id=' + a + param, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }

  $scope.doPrint2 = function () {
    window.open($rootScope.baseURL + 'api/print/print-data-po-pajak.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&kode_proyek=' + $scope.kode_proyek + '&supplier=' + $scope.supplier + '&kategori=' + $scope.kategori, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('PurchaseOrderNewPajakController', function ($rootScope, $scope, $q, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
  var cartArray = [];
  var noUrut = 1;

  $scope.displayCartArray = [];
  $scope.total = 0;
  $scope.ppn = 0;
  $scope.ppn_persen = 11;
  $scope.diskon = 0;
  $scope.diskon_persen = 0;
  $scope.total2 = 0;
  $scope.grand_total = 0;
  $scope.sisa = 0;
  $scope.kembali = 0;
  $scope.pembayarandp = 0;
  $scope.metode_pembayaran = "Tunai";
  $scope.metode_pembayaran2 = "Kas Kecil";
  $scope.showBG = false;
  $scope.showBG2 = false;
  $scope.nobg = "";
  $scope.jatuhtempobg = "";
  $scope.jenis_po = "1";
  $scope.spb = "0";

  $scope.hideKode = false;
  $scope.hideName = true;

  $scope.isLD = "0";
  $scope.isPajak = "1";

  $scope.id_proyek = "0";
  $scope.jenis_po = "1";

  $scope.isMMSMaterialBantu = "0";

  $scope.changeJenisPO = function () {
    if ($scope.jenis_po === "1") {
      $scope.hideKode = false;
      $scope.hideName = true;
    } else {
      $scope.hideKode = true;
      $scope.hideName = false;
    }
    cartArray = [];
    $scope.displayCartArray = [];
    $scope.displayCart();

    $scope.kode = "";
    $scope.anama = "";
    $scope.asatuan = "";

    $scope.aharga = "";
    $scope.qty = "";
    $scope.aidbarang = 0;

    $('#nama_barang').val($scope.anama);
    $('#harga').val($scope.aharga);
    $('#satuan').val($scope.asatuan);

    $(".select2").select2("val", "");
  }


  $scope.changeMetodePayment2 = function () {
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

  $scope.refreshDataBarang = function () {
    $q.all({
      apiv1: $http.get('api/purchase-order/load-all-requirement.php?isPajak=true&spb=' + $scope.spb + '&isMMSMaterialBantu=' + $scope.isMMSMaterialBantu)
    }).then(function (results) {
      $scope.data_barang = results.apiv1.data.barang;
    });
    $scope.loadAll();
  }

  $scope.changeSPB = function () {
    cartArray = [];
    $scope.displayCart();
    $scope.refreshDataBarang();
  }

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true,
    setDate: new Date(2008, 9, 03)
  });

  $scope.getdata = function () {
    $http.get('api/proyek/data-proyek.php?stts=2').success(function (data, status) {
      $scope.data_proyek = data;
    });
  };

  $scope.getdatabarang = function () {
    $http.get('api/barang/data-barang.php?departement=2').success(function (data, status) {
      $scope.data_barang = data;
    });
  };

  $scope.loadAll = function () {
    $q.all({
      apiv1: $http.get(`api/purchase-order/load-all-requirement.php?isPajak=true&isMMSMaterialBantu=${$scope.isMMSMaterialBantu}`)
    }).then(function (results) {
      $scope.data_barang = results.apiv1.data.barang;
      $scope.data_proyek = results.apiv1.data.proyek;
      $scope.data_supplier = results.apiv1.data.supplier;
      $scope.data_spb = results.apiv1.data.spb;
    });
  }
  $scope.loadAll();

  $scope.$watch('supplier', function () {
    for (var i in $scope.data_supplier) {
      if ($scope.data_supplier[i].IDSupplier === $scope.supplier) {
        if ($scope.data_supplier[i].Bank !== null)
          $scope.inv_bank = $scope.data_supplier[i].Bank + "\n" + $scope.data_supplier[i].NoRek;
        else
          $scope.inv_bank = "";
      }
    }
  });

  //$scope.getdata();
  //$scope.getdatabarang();

  $scope.usrlogin = $rootScope.userLoginName;

  $("#kode").on("change", function (e) {
    $scope.kode = this.value;
    //alert($scope.kode);
    $scope.changeKode();
  });

  $scope.changeKode = function () {
    if ($scope.kode != "") {
      console.log($scope.data_barang[$scope.kode])
      $scope.anama = $scope.data_barang[$scope.kode].Nama;
      $scope.aidbarang = $scope.data_barang[$scope.kode].IDBarang;
      $scope.ahargaPublish = $scope.data_barang[$scope.kode].HargaPublish;
      $scope.adiskon = $scope.data_barang[$scope.kode].DiskonPersen;
      $scope.aharga = $scope.data_barang[$scope.kode].Harga;
      $scope.aoriginalharga = $scope.data_barang[$scope.kode].Harga;
      $scope.asatuan = $scope.data_barang[$scope.kode].Satuan;
      $scope.adpp = $scope.data_barang[$scope.kode].DPP;
      $scope.appnPersen = $scope.data_barang[$scope.kode].PPNPersen;

      $('#nama_barang').val($scope.anama);
      $('#harga').val($scope.aharga);
      $('#satuan').val($scope.asatuan);
      $('#qty').focus();
      $scope.qty = 1;
    } else {
      $('#nama_barang').val("");
      $('#harga').val("");
      $('#satuan').val("");
      $scope.qty = "";
    }
  }

  $scope.addtocart = function () {
    if ($scope.anama !== "") {

      if ($scope.aidbarang === "" || $scope.aidbarang === 0)
        $scope.aidbarang = noUrut;

      var IDBarang = $scope.aidbarang;
      var NamaBarang = $scope.anama;
      var Satuan = $scope.asatuan;
      var HargaPublish = CommonServices.parseFloat($scope.ahargaPublish);
      var Diskon = CommonServices.parseNumber($scope.adiskon);
      var Harga = CommonServices.parseFloat($scope.aharga);
      var HargaOriginal = CommonServices.parseFloat($scope.aoriginalharga);

      if (Harga != HargaOriginal) {
        HargaPublish = Harga;
        Diskon = 0;
      }

      var DPP = CommonServices.parseFloat($scope.adpp);
      var PPNPersen = CommonServices.parseFloat($scope.appnPersen);
      var Qty = $scope.qty;
      var QtyCurrent = $('#QtyBarang' + IDBarang).val();

      if (typeof cartArray[IDBarang] != 'undefined') {
        cartArray[IDBarang]["HargaPublish"] = HargaPublish;
        cartArray[IDBarang]["Diskon"] = Diskon;
        cartArray[IDBarang]["Harga"] = Harga;
        cartArray[IDBarang]["QtyBarang"] = parseInt(QtyCurrent) + parseInt(Qty);
        cartArray[IDBarang]["SubTotal"] = cartArray[IDBarang]["QtyBarang"] * Harga;
        $('#HargaPublish' + IDBarang).val(numberWithCommas(cartArray[IDBarang]["HargaPublish"]));
        $('#Diskon' + IDBarang).val(cartArray[IDBarang]["Diskon"]);
        $('#Harga' + IDBarang).val(numberWithCommas(cartArray[IDBarang]["Harga"]));
        $('#QtyBarang' + IDBarang).val(cartArray[IDBarang]["QtyBarang"]);
      } else {
        SubTotal = Harga * Qty;
        cartArray[IDBarang] = {
          NoUrut: noUrut,
          IDBarang: IDBarang,
          NamaBarang: NamaBarang,
          QtyBarang: Qty,
          SubTotal: SubTotal,
          Satuan: Satuan,
          HargaPublish: numberWithCommas(HargaPublish),
          Diskon: Diskon,
          Harga: numberWithCommas(Harga),
          DPP: DPP,
          PPNPersen: PPNPersen,
          UseDiskonForCalc: true
        };
        noUrut += 1;
      }

      $('#nama_barang').val('');
      $("#kode").select2("val", "");
      $scope.qty = 1;
      $scope.aharga = 0;
      $scope.ahargaPublish = 0;
      $scope.adiskon = 0;
      $scope.anama = "";
      $scope.aidbarang = 0;
      $scope.kode = "";

      $scope.displayCart();
    } else {
      alert('Ada sesuatu yang salah. Silahkan coba pilih barang anda kembali.');
    }
  }

  $scope.displayCart = function () {
    console.log(cartArray);

    function sortFunction(a, b) {
      if (a['NoUrut'] == b['NoUrut']) {
        return 0;
      } else {
        return (a['NoUrut'] < b['NoUrut']) ? -1 : 1;
      }
    }
    $scope.total = 0;
    $scope.total_dpp = 0;
    $scope.displayCartArray = cartArray.filter(function () {
      return true
    });
    $scope.displayCartArray = $scope.displayCartArray.sort(sortFunction);
    $scope.displayCartArray.forEach(function (entry) {
      $scope.total_dpp += (entry["DPP"] * entry["QtyBarang"]);
      $scope.total = parseFloat($scope.total.toString().replace(/,/g, "")) + parseFloat(entry["SubTotal"].toString().replace(/,/g, ""));
    });
    $scope.countingGrandTotal();
  }

  $scope.countingGrandTotal = function () {
    $scope.changeDiskon();
    $scope.changePPN();
    $scope.changePembayaran();
  }

  $scope.removeRow = function (a) {
    delete cartArray[a];
    $scope.displayCart();

    return false;
  };

  $scope.calcHargaAndSubTotal = function (IDBarang, useDiskonForCalc) {
    if (useDiskonForCalc !== null) cartArray[IDBarang]['UseDiskonForCalc'] = useDiskonForCalc;

    var HargaPublish = CommonServices.getInputValueAsFloat('#HargaPublish' + IDBarang);
    var Diskon = CommonServices.getInputValueAsNumber('#Diskon' + IDBarang);
    var Harga = CommonServices.getInputValueAsNumber('#Harga' + IDBarang);
    var QtyBarang = CommonServices.getInputValueAsInt('#QtyBarang' + IDBarang);

    cartArray[IDBarang]['HargaPublish'] = numberWithCommas(HargaPublish);
    cartArray[IDBarang]['Diskon'] = Diskon;
    cartArray[IDBarang]['Harga'] = numberWithCommas(Harga);
    cartArray[IDBarang]['QtyBarang'] = QtyBarang;

    if (cartArray[IDBarang]['UseDiskonForCalc']) {
      cartArray[IDBarang]['Harga'] = HargaPublish - CommonServices.getDiscountValue(Diskon, HargaPublish);
      CommonServices.setValueWithNumberFormat('#Harga' + IDBarang, numberWithCommas(cartArray[IDBarang]['Harga']));
    } else {
      cartArray[IDBarang]['Diskon'] = ((HargaPublish - Harga) / HargaPublish * 100).toFixed(2) + "%";
      CommonServices.setValueWithNumberFormat('#Diskon' + IDBarang, cartArray[IDBarang]['Diskon']);
    }

    cartArray[IDBarang]["DPP"] = Math.round((100 / (100 + parseFloat(cartArray[IDBarang]["PPNPersen"]))) * parseFloat(cartArray[IDBarang]["Harga"]));
    cartArray[IDBarang]["SubTotal"] = CommonServices.parseFloat(cartArray[IDBarang]['Harga']) * cartArray[IDBarang]['QtyBarang'];
    $scope.displayCart();
  }

  // $scope.changeDiskon = function() {
  //   var DiskonPersen = $('#diskon_persen').val();
  //   $scope.diskon = Math.round(((DiskonPersen / 100) * parseFloat($scope.total)));
  //   $scope.total2 = parseFloat($scope.total) - $scope.diskon;
  //   $scope.changePembayaran();
  // }

  $scope.changeDiskon = function () {
    $scope.diskon = CommonServices.getDiscountValue($scope.diskon_persen, $scope.total);
    $scope.total2 = parseFloat($scope.total) - $scope.diskon;
    $scope.changePPN();
  }

  $scope.changePPN = function () {
    // var PPNPersen = parseFloat($scope.ppn_persen);
    // $scope.ppn = Math.round(((PPNPersen / 100) * parseFloat($scope.total2)));
    $scope.grand_total = parseFloat($scope.total2);
    if ($scope.jenis_po === '1') {
      $scope.ppn = $scope.grand_total - $scope.total_dpp;
      $scope.ppn_persen = ($scope.ppn / $scope.total_dpp * 100).toFixed(2);
    } else {
      $scope.total_dpp = Math.round((100 / (100 + parseFloat($scope.ppn_persen))) * $scope.grand_total);
      $scope.ppn = $scope.grand_total - $scope.total_dpp;
    }
    $scope.changePembayaran();
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

  $scope.tanggal = $rootScope.currentDateID;

  function encodeURL(a) {
    return encodeURIComponent(a);
  }

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (typeof $scope.supplier === 'undefined' || $scope.supplier === null || $scope.supplier === '') {
      alert("Pilih Supplier terlebih dahulu!");
    } else if (isValid) {
      console.log(cartArray);
      if (cartArray.length === 0) {
        alert("Keranjang belanja anda kosong!");
      } else {
        $scope.processing = true;
        $http({
          method: "POST",
          url: 'api/purchase-order/new.php',
          data: $.param({
            'id_proyek': $scope.id_proyek,
            'spb': $scope.spb,
            'tanggal': $scope.tanggal,
            'kategori': $scope.kategori,
            'usrlogin': $rootScope.userLoginName,
            'supplier': $scope.supplier,
            'total': $scope.total,
            'diskon_persen': $scope.diskon_persen,
            'diskon': $scope.diskon,
            'total2': $scope.total2,
            'ppn_persen': $scope.ppn_persen,
            'ppn': $scope.ppn,
            'total_dpp': $scope.total_dpp,
            'grand_total': $scope.grand_total,
            'pembayarandp': $scope.pembayarandp,
            'sisa': $scope.sisa,
            'keterangan': $scope.keterangan,
            'uploaded': $scope.userLoginID,
            'metode_pembayaran': $scope.metode_pembayaran,
            'metode_pembayaran2': $scope.metode_pembayaran2,
            'nobg': $scope.nobg,
            'jatuhtempobg': $scope.jatuhtempobg,
            'inv_pembayaran': $scope.inv_pembayaran,
            'inv_bank': $scope.inv_bank,
            'inv_delivery': $scope.inv_delivery,
            'inv_expedisi': $scope.inv_expedisi,
            'inv_alamat_pengiriman': $scope.inv_alamat_pengiriman,
            'jenis_po': $scope.jenis_po,
            'isLD': $scope.isLD,
            'isPajak': $scope.isPajak,
            'isMMSMaterialBantu': $scope.isMMSMaterialBantu,
            'cart': JSON.stringify(cartArray)
          }),
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).success(function (data, status) {
          if (data == "1") {
            ngToast.create({
              className: 'success',
              content: 'Data purchase order berhasil disimpan <i class="fa fa-remove"></i>'
            });
            window.document.location = '#/data-purchase-order-pajak/';
          } else if (data == "2") {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Proyek telah memasuki limit pembelanjaan! Purchase Order tidak dapat disimpan. <i class="fa fa-remove"></i>'
            });
          } else {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Data purchase order gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
            });
          }
        });
      }
    }
  };
});

tripApp.controller('PurchaseOrderDetailPajakController', function ($rootScope, $scope, $route, $routeParams, $http, ngToast, $location, $timeout) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  //alert($routeParams.poId);

  $scope.processing = false;
  $http.get('api/purchase-order/detail.php?id=' + $routeParams.poId).success(function (data, status) {
    $scope.no_po = data.detail.no_po;
    $scope.proyek = data.detail.proyek;
    if ($scope.proyek === '' || $scope.proyek === null) $scope.proyek = 'UMUM';
    $scope.tanggal = data.detail.tanggal;
    $scope.kategori = data.detail.kategori;
    $scope.usrlogin = data.detail.usrlogin;
    $scope.total = data.detail.total;
    $scope.diskon_persen = data.detail.diskon_persen;
    $scope.total2 = data.detail.total2;
    $scope.ppn_persen = data.detail.ppn_persen;
    $scope.ppn = data.detail.ppn;
    $scope.total_dpp = data.detail.total_dpp;
    $scope.grand_total = data.detail.grand_total;
    $scope.pembayarandp = data.detail.pembayarandp;
    $scope.sisa = data.detail.sisa;
    $scope.keterangan = data.detail.keterangan;
    $scope.supplier = data.detail.supplier;
    $scope.metode_pembayaran = data.detail.metode_pembayaran;
    $scope.metode_pembayaran2 = data.detail.metode_pembayaran2;
    $scope.nobg = data.detail.nobg;
    $scope.jatuhtempobg = data.detail.jatuhtempobg;
    $scope.kembali = data.detail.kembali;
    $scope.total_pembayaran = data.detail.total_pembayaran;
    $scope.completed = data.detail.completed;
    $scope.jenis_po = data.detail.jenis_po;
    $scope.spb = data.detail.spb;
    $scope.isMMSMaterialBantu = data.detail.isMMSMaterialBantu;

    $scope.inv_pembayaran = data.detail.inv_pembayaran;
    $scope.inv_bank = data.detail.inv_bank;
    $scope.inv_delivery = data.detail.inv_delivery;
    $scope.inv_expedisi = data.detail.inv_expedisi;
    $scope.inv_alamat_pengiriman = data.detail.inv_alamat_pengiriman;

    $scope.data_spb = data.spb;

    $scope.dataPenerimaan = data.dataPenerimaan;
    $scope.dataSuratJalan = data.dataSuratJalan;

    setTimeout(() => {
      $('#spb').val($scope.spb).trigger('change');
      console.log($scope.spb);
    }, 1000);

    if ($scope.metode_pembayaran != "")
      $scope.showMethodPembayaran = false;
    else
      $scope.showMethodPembayaran = true;

    if ($scope.metode_pembayaran2 != "")
      $scope.showMethodPembayaran2 = false;
    else
      $scope.showMethodPembayaran2 = true;

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
    $scope.historypenerimaan = data.historypenerimaan;

    $scope.detailpembayaran = data.dataPembayaran;

    $scope.detailFakturPajak = data.dataFakturPajak;
    $scope.totalNilaiFaktur = data.totalNilaiFaktur;
    $scope.sisaPPN = data.sisaPPN;

    $scope.deleted_by = data.detail.deleted_by;
    $scope.deleted_date = data.detail.deleted_date;
    $scope.deleted_remark = data.detail.deleted_remark;
    $scope.completedFakturPajak = data.detail.completedFakturPajak;
  });

  $scope.gotoLink = function (a, b) {
    $('#myModal2').modal('hide');
    $timeout(function () {
      $location.path(a + b);
    }, 500);
  }

  $scope.doPrint = function () {
    window.open($rootScope.baseURL + 'api/print/print-po.php?id=' + $scope.no_po, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }

  $scope.printHistory = function (item) {
    window.open($rootScope.baseURL + 'api/print/print-penerimaan-barang.php?id=' + item.IDPenerimaan, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }

  $scope.showModal = function () {
    $('#myModal').modal('show');
    $('#myModal').children('.modal-dialog').removeClass('modal-lg');
  };

  $scope.closeModal = function () {
    $('#myModal').modal('hide');
  }

  $scope.showModal2 = function () {
    $('#myModal2').modal('show');
    $('#myModal2').children('.modal-dialog').removeClass('modal-lg');
  };

  $scope.closeModal2 = function () {
    $('#myModal2').modal('hide');
  }

  $scope.showModal3 = function () {
    $('#myModal3').modal('show');
    $('#myModal3').children('.modal-dialog').removeClass('modal-lg');
  };

  $scope.closeModal3 = function () {
    $('#myModal3').modal('hide');
  }

  $scope.showModal4 = function () {
    $('#myModal4').modal('show');
    $('#myModal4').children('.modal-dialog').removeClass('modal-lg');
  };

  $scope.closeModal4 = function () {
    $('#myModal4').modal('hide');
  }

  $scope.submitForm = function (isValid) {
    $http({
      method: "POST",
      url: 'api/purchase-order/edit-detail.php',
      data: $.param({
        'id': $routeParams.poId,
        'spb': $scope.spb,
        'kategori': $scope.kategori,
        'inv_pembayaran': $scope.inv_pembayaran,
        'inv_bank': $scope.inv_bank,
        'inv_delivery': $scope.inv_delivery,
        'inv_expedisi': $scope.inv_expedisi,
        'inv_alamat_pengiriman': $scope.inv_alamat_pengiriman,
        'completed': $scope.completed,
        'completedFakturPajak': $scope.completedFakturPajak
      }),
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    }).success(function (data, status) {
      if (data == "1") {
        ngToast.create({
          className: 'success',
          content: 'Data purchase order berhasil disimpan <i class="fa fa-remove"></i>'
        });
      } else {
        $scope.processing = false;
        ngToast.create({
          className: 'danger',
          content: 'Data purchase order gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
        });
      }
    });
  };
});

tripApp.controller('PurchaseOrderEditPajakController', function ($rootScope, $scope, $route, $routeParams, $http, ngToast, $q, CommonServices) {
  var cartArray = [];
  var noUrut = 0;

  $scope.isLD = "0";
  $scope.isPajak = "1";
  $scope.isMMSMaterialBantu = "0";

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.processing = false;
  $http.get('api/purchase-order/detail.php?id=' + $routeParams.poId).success(function (data, status) {
    $scope.no_po = data.detail.no_po;
    $scope.proyek = data.detail.proyek;
    if ($scope.proyek === '' || $scope.proyek === null) $scope.proyek = 'UMUM';
    $scope.id_proyek = data.detail.id_proyek;
    $scope.tanggal = data.detail.tanggal;
    $scope.kategori = data.detail.kategori;
    $scope.usrlogin = data.detail.usrlogin;
    $scope.total = data.detail.total;
    $scope.diskon_persen = data.detail.diskon_persen;
    $scope.total2 = data.detail.total2;
    $scope.ppn_persen = parseFloat(data.detail.ppn_persen);
    $scope.ppn = data.detail.ppn;
    $scope.total_dpp = data.detail.total_dpp;
    $scope.grand_total = data.detail.grand_total;
    $scope.pembayarandp = data.detail.pembayarandp;
    $scope.sisa = data.detail.sisa;
    $scope.keterangan = data.detail.keterangan;
    $scope.supplier = data.detail.id_supplier;
    $scope.metode_pembayaran = data.detail.metode_pembayaran;
    $scope.metode_pembayaran2 = data.detail.metode_pembayaran2;
    $scope.nobg = data.detail.nobg;
    $scope.jatuhtempobg = data.detail.jatuhtempobg;
    $scope.kembali = data.detail.kembali;
    $scope.total_pembayaran = data.detail.total_pembayaran;
    $scope.completed = data.detail.completed;
    $scope.completedFakturPajak = data.detail.completedFakturPajak;
    $scope.jenis_po = data.detail.jenis_po2;
    $scope.jenis_po2 = data.detail.jenis_po;
    $scope.spb = data.detail.spb;

    $scope.inv_pembayaran = data.detail.inv_pembayaran;
    $scope.inv_bank = data.detail.inv_bank;
    $scope.inv_delivery = data.detail.inv_delivery;
    $scope.inv_expedisi = data.detail.inv_expedisi;
    $scope.inv_alamat_pengiriman = data.detail.inv_alamat_pengiriman;

    if ($scope.metode_pembayaran != "")
      $scope.showMethodPembayaran = false;
    else
      $scope.showMethodPembayaran = true;

    if ($scope.metode_pembayaran2 != "")
      $scope.showMethodPembayaran2 = false;
    else
      $scope.showMethodPembayaran2 = true;

    if ($scope.metode_pembayaran == "Rekening BG") {
      $scope.showBG = true;
      $scope.showBG2 = true;
    } else {
      $scope.showBG = false;
      $scope.showBG2 = false;
    }


    $scope.changeJenisPO();

    data.detailcart.forEach((cart) => {
      cartArray[cart.IDBarang] = {
        NoUrut: cart.NoUrut,
        IDBarang: cart.IDBarang,
        NamaBarang: cart.NamaBarang,
        HargaPublish: numberWithCommas(cart.HargaPublish),
        Diskon: cart.Diskon,
        Harga: numberWithCommas(cart.Harga),
        QtyBarang: cart.Qty,
        SubTotal: cart.SubTotal,
        Satuan: cart.Satuan,
        DPP: parseFloat(cart.DPP),
        PPNPersen: parseFloat(cart.PPNPersen),
        UseDiskonForCalc: true
      };
      if (cart.IDBarang > noUrut) noUrut = parseInt(cart.IDBarang);
    });
    noUrut++;

    $scope.displayCart();
  });

  $scope.changeJenisPO = function () {
    if ($scope.jenis_po === "1") {
      $scope.hideKode = false;
      $scope.hideName = true;
    } else {
      $scope.hideKode = true;
      $scope.hideName = false;
    }
    cartArray = [];
    $scope.displayCartArray = [];
    $scope.displayCart();

    $scope.kode = "";
    $scope.anama = "";
    $scope.asatuan = "";

    $scope.aharga = "";
    $scope.qty = "";
    $scope.aidbarang = 0;

    $('#nama_barang').val($scope.anama);
    $('#harga').val($scope.aharga);
    $('#satuan').val($scope.asatuan);

    $(".select2").select2("val", "");
    $("#spb").select2("val", "0");
  }

  $scope.loadAll = function () {
    $q.all({
      apiv1: $http.get(`api/purchase-order/load-all-requirement.php?isPajak=true&isMMSMaterialBantu=${$scope.isMMSMaterialBantu}`)
    }).then(function (results) {
      $scope.data_barang = results.apiv1.data.barang;
      $scope.data_proyek = results.apiv1.data.proyek;
      $scope.data_supplier = results.apiv1.data.supplier;
      $scope.data_spb = results.apiv1.data.spb;
    });
  }
  $scope.loadAll();

  $scope.refreshDataBarang = function () {
    $q.all({
      apiv1: $http.get('api/purchase-order/load-all-requirement.php?isPajak=true&spb=' + $scope.spb + '&isMMSMaterialBantu=' + $scope.isMMSMaterialBantu)
    }).then(function (results) {
      $scope.data_barang = results.apiv1.data.barang;
    });
    $scope.loadAll();
  }

  $scope.changeSPB = function () {
    cartArray = [];
    $scope.displayCart();
    $scope.refreshDataBarang();
  }

  $("#kode").on("change", function (e) {
    $scope.kode = this.value;
    //alert($scope.kode);
    $scope.changeKode();
  });

  $scope.changeKode = function () {
    if ($scope.kode != "") {
      console.log($scope.data_barang[$scope.kode])
      $scope.anama = $scope.data_barang[$scope.kode].Nama;
      $scope.aidbarang = $scope.data_barang[$scope.kode].IDBarang;
      $scope.ahargaPublish = $scope.data_barang[$scope.kode].HargaPublish;
      $scope.adiskon = $scope.data_barang[$scope.kode].DiskonPersen;
      $scope.aharga = $scope.data_barang[$scope.kode].Harga;
      $scope.aoriginalharga = $scope.data_barang[$scope.kode].Harga;
      $scope.asatuan = $scope.data_barang[$scope.kode].Satuan;
      $scope.adpp = $scope.data_barang[$scope.kode].DPP;
      $scope.appnPersen = $scope.data_barang[$scope.kode].PPNPersen;

      $('#nama_barang').val($scope.anama);
      $('#harga').val($scope.aharga);
      $('#satuan').val($scope.asatuan);
      $('#qty').focus();
      $scope.qty = 1;
    } else {
      $('#nama_barang').val("");
      $('#harga').val("");
      $('#satuan').val("");
      $scope.qty = "";
    }
  }

  $scope.addtocart = function () {
    if ($scope.anama !== "") {

      if ($scope.aidbarang === "" || $scope.aidbarang === 0)
        $scope.aidbarang = noUrut;

      var IDBarang = $scope.aidbarang;
      var NamaBarang = $scope.anama;
      var Satuan = $scope.asatuan;
      var HargaPublish = CommonServices.parseFloat($scope.ahargaPublish);
      var Diskon = CommonServices.parseNumber($scope.adiskon);
      var Harga = CommonServices.parseFloat($scope.aharga);
      var HargaOriginal = CommonServices.parseFloat($scope.aoriginalharga);

      if (Harga != HargaOriginal) {
        HargaPublish = Harga;
        Diskon = 0;
      }

      var DPP = CommonServices.parseFloat($scope.adpp);
      var PPNPersen = CommonServices.parseFloat($scope.appnPersen);
      var Qty = $scope.qty;
      var QtyCurrent = $('#QtyBarang' + IDBarang).val();

      if (typeof cartArray[IDBarang] != 'undefined') {
        cartArray[IDBarang]["HargaPublish"] = HargaPublish;
        cartArray[IDBarang]["Diskon"] = Diskon;
        cartArray[IDBarang]["Harga"] = Harga;
        cartArray[IDBarang]["QtyBarang"] = parseInt(QtyCurrent) + parseInt(Qty);
        cartArray[IDBarang]["SubTotal"] = cartArray[IDBarang]["QtyBarang"] * Harga;
        $('#HargaPublish' + IDBarang).val(numberWithCommas(cartArray[IDBarang]["HargaPublish"]));
        $('#Diskon' + IDBarang).val(cartArray[IDBarang]["Diskon"]);
        $('#Harga' + IDBarang).val(numberWithCommas(cartArray[IDBarang]["Harga"]));
        $('#QtyBarang' + IDBarang).val(cartArray[IDBarang]["QtyBarang"]);
      } else {
        SubTotal = Harga * Qty;
        cartArray[IDBarang] = {
          NoUrut: noUrut,
          IDBarang: IDBarang,
          NamaBarang: NamaBarang,
          QtyBarang: Qty,
          SubTotal: SubTotal,
          Satuan: Satuan,
          HargaPublish: numberWithCommas(HargaPublish),
          Diskon: Diskon,
          Harga: numberWithCommas(Harga),
          DPP: DPP,
          PPNPersen: PPNPersen,
          UseDiskonForCalc: true
        };
        noUrut += 1;
      }

      $('#nama_barang').val('');
      $("#kode").select2("val", "");
      $scope.qty = 1;
      $scope.aharga = 0;
      $scope.ahargaPublish = 0;
      $scope.adiskon = 0;
      $scope.anama = "";
      $scope.aidbarang = 0;
      $scope.kode = "";

      $scope.displayCart();
    } else {
      alert('Ada sesuatu yang salah. Silahkan coba pilih barang anda kembali.');
    }
  }

  $scope.displayCart = function () {
    console.log(cartArray);

    function sortFunction(a, b) {
      if (a['NoUrut'] == b['NoUrut']) {
        return 0;
      } else {
        return (a['NoUrut'] < b['NoUrut']) ? -1 : 1;
      }
    }
    $scope.total = 0;
    $scope.total_dpp = 0;
    $scope.displayCartArray = cartArray.filter(function () {
      return true
    });
    $scope.displayCartArray = $scope.displayCartArray.sort(sortFunction);
    $scope.displayCartArray.forEach(function (entry) {
      $scope.total_dpp += (entry["DPP"] * entry["QtyBarang"]);
      $scope.total = parseFloat($scope.total.toString().replace(/,/g, "")) + parseFloat(entry["SubTotal"].toString().replace(/,/g, ""));
    });
    $scope.countingGrandTotal();
  }

  $scope.countingGrandTotal = function () {
    $scope.changeDiskon();
    $scope.changePPN();
    $scope.changePembayaran();
  }

  $scope.removeRow = function (a) {
    delete cartArray[a];
    $scope.displayCart();

    return false;
  };

  $scope.calcHargaAndSubTotal = function (IDBarang, useDiskonForCalc) {
    if (useDiskonForCalc !== null) cartArray[IDBarang]['UseDiskonForCalc'] = useDiskonForCalc;

    var HargaPublish = CommonServices.getInputValueAsFloat('#HargaPublish' + IDBarang);
    var Diskon = CommonServices.getInputValueAsNumber('#Diskon' + IDBarang);
    var Harga = CommonServices.getInputValueAsNumber('#Harga' + IDBarang);
    var QtyBarang = CommonServices.getInputValueAsInt('#QtyBarang' + IDBarang);

    cartArray[IDBarang]['HargaPublish'] = numberWithCommas(HargaPublish);
    cartArray[IDBarang]['Diskon'] = Diskon;
    cartArray[IDBarang]['Harga'] = numberWithCommas(Harga);
    cartArray[IDBarang]['QtyBarang'] = QtyBarang;

    if (cartArray[IDBarang]['UseDiskonForCalc']) {
      cartArray[IDBarang]['Harga'] = HargaPublish - CommonServices.getDiscountValue(Diskon, HargaPublish);
      CommonServices.setValueWithNumberFormat('#Harga' + IDBarang, numberWithCommas(cartArray[IDBarang]['Harga']));
    } else {
      cartArray[IDBarang]['Diskon'] = ((HargaPublish - Harga) / HargaPublish * 100).toFixed(2) + "%";
      CommonServices.setValueWithNumberFormat('#Diskon' + IDBarang, cartArray[IDBarang]['Diskon']);
    }

    cartArray[IDBarang]["DPP"] = Math.round((100 / (100 + parseFloat(cartArray[IDBarang]["PPNPersen"]))) * parseFloat(cartArray[IDBarang]["Harga"]));
    cartArray[IDBarang]["SubTotal"] = CommonServices.parseFloat(cartArray[IDBarang]['Harga']) * cartArray[IDBarang]['QtyBarang'];
    $scope.displayCart();
  }

  // $scope.changeDiskon = function() {
  //   var DiskonPersen = $('#diskon_persen').val();
  //   $scope.diskon = Math.round(((DiskonPersen / 100) * parseFloat($scope.total)));
  //   $scope.total2 = parseFloat($scope.total) - $scope.diskon;
  //   $scope.changePembayaran();
  // }

  $scope.changeDiskon = function () {
    $scope.diskon = CommonServices.getDiscountValue($scope.diskon_persen, $scope.total);
    $scope.total2 = parseFloat($scope.total) - $scope.diskon;
    $scope.changePPN();
  }

  $scope.changePPN = function () {
    // var PPNPersen = parseFloat($scope.ppn_persen);
    // $scope.ppn = Math.round(((PPNPersen / 100) * parseFloat($scope.total2)));
    $scope.grand_total = parseFloat($scope.total2);
    if ($scope.jenis_po === '1') {
      $scope.ppn = $scope.grand_total - $scope.total_dpp;
      $scope.ppn_persen = ($scope.ppn / $scope.total_dpp * 100).toFixed(2);
    } else {
      $scope.total_dpp = Math.round((100 / (100 + parseFloat($scope.ppn_persen))) * $scope.grand_total);
      $scope.ppn = $scope.grand_total - $scope.total_dpp;
    }
    $scope.changePembayaran();
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

  $scope.tanggal = $rootScope.currentDateID;

  function encodeURL(a) {
    return encodeURIComponent(a);
  }

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      console.log(cartArray);
      if (cartArray.length === 0) {
        alert("Keranjang belanja anda kosong!");
      } else {
        $scope.processing = true;
        $http({
          method: "POST",
          url: 'api/purchase-order/edit.php',
          data: $.param({
            'no_po': $routeParams.poId,
            'id_proyek': $scope.id_proyek,
            'spb': $scope.spb,
            'tanggal': $scope.tanggal,
            'kategori': $scope.kategori,
            'usrlogin': $rootScope.userLoginName,
            'supplier': $scope.supplier,
            'total': $scope.total,
            'diskon_persen': $scope.diskon_persen,
            'diskon': $scope.diskon,
            'total2': $scope.total2,
            'ppn_persen': $scope.ppn_persen,
            'ppn': $scope.ppn,
            'total_dpp': $scope.total_dpp,
            'grand_total': $scope.grand_total,
            'pembayarandp': $scope.pembayarandp,
            'sisa': $scope.sisa,
            'keterangan': $scope.keterangan,
            'uploaded': $scope.userLoginID,
            'metode_pembayaran': $scope.metode_pembayaran,
            'metode_pembayaran2': $scope.metode_pembayaran2,
            'nobg': $scope.nobg,
            'jatuhtempobg': $scope.jatuhtempobg,
            'inv_pembayaran': $scope.inv_pembayaran,
            'inv_bank': $scope.inv_bank,
            'inv_delivery': $scope.inv_delivery,
            'inv_expedisi': $scope.inv_expedisi,
            'inv_alamat_pengiriman': $scope.inv_alamat_pengiriman,
            'jenis_po': $scope.jenis_po,
            'isLD': $scope.isLD,
            'isPajak': $scope.isPajak,
            'isMMSMaterialBantu': $scope.isMMSMaterialBantu,
            'completed': $scope.completed,
            'completedFakturPajak': $scope.completedFakturPajak,
            'cart': JSON.stringify(cartArray)
          }),
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).success(function (data, status) {
          if (data == "1") {
            ngToast.create({
              className: 'success',
              content: 'Data purchase order berhasil disimpan <i class="fa fa-remove"></i>'
            });
            window.document.location = '#/data-purchase-order-pajak/';
          } else if (data == "2") {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Proyek telah memasuki limit pembelanjaan! Purchase Order tidak dapat disimpan. <i class="fa fa-remove"></i>'
            });
          } else if (data == "0") {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Data purchase order gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
            });
          } else {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: data + ' <i class="fa fa-remove"></i>'
            });
          }
        });
      }
    }
  };
});
