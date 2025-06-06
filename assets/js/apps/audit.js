tripApp.controller('AuditController', function ($rootScope, $scope, $q, $routeParams, $route, $http, ngToast, CommonServices) {
  CommonServices.setDatePickerJQuery();

  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();
  $scope.kode_proyek = "";
  $scope.supplier = "";
  $scope.filterstatus = "";
  $scope.activeMenu = '';

  $scope.getdata = function () {
    $http.get('api/audit/data-audit.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend).success(function (data, status) {
      $scope.data_audit = data.data;
    });
  };

  $scope.getdata();

  $scope.refreshData = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }

  $scope.removeRow = function (val) {
    $scope.noAudit = val;
    $('#modalDelete').modal('show');
  }

  $scope.submitFormDelete = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/audit/delete.php',
        data: $.param({
          'idr': $scope.noAudit,
          'remark': $scope.remark,
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        $scope.processing = false;
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data Audit berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $scope.remark = "";
          $scope.refreshData();
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data Audit order tidak dapat dihapus karena stok penyesuaian tersebut telah terdistribusi... <i class="fa fa-remove"></i>'
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
    window.open($rootScope.baseURL + 'api/print/print-po.php?id=' + a, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('AuditNewController', function ($rootScope, $scope, $q, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
  CommonServices.setDatePickerJQuery();

  var cartArray = [];
  var noUrut = 1;
  $scope.disabledSN = true;
  $scope.serialnumber = "";
  $scope.spb = "";
  $scope.id_gudang = "";

  $scope.displayCartArray = [];
  $scope.total = 0;

  $scope.loadData = function () {
    $q.all({
      apiv1: $http.get('api/audit/load-all-requirement.php?id_gudang=' + $scope.id_gudang)
    }).then(function (results) {
      $scope.data_barang = results.apiv1.data.barang;
      $scope.data_penjualan = results.apiv1.data.penjualan;
      $scope.data_gudang = results.apiv1.data.gudang;
      console.log(results);
    });
  }
  $scope.loadData();

  $scope.usrlogin = $rootScope.userLoginName;

  $("#kode").on("change", function (e) {
    $scope.kode = this.value;
    $scope.changeKode();
  });

  $("#id_gudang").on("change", function (e) {
    $scope.id_gudang = this.value;
    $scope.loadData();
  });

  $scope.changeKode = function () {
    if ($scope.kode != "") {
      $scope.aidbarang = $scope.data_barang[$scope.kode].IDBarang;
      $scope.anama = $scope.data_barang[$scope.kode].Nama;
      $scope.aStokGudang = $scope.data_barang[$scope.kode].StokGudang;
      $scope.harga = $scope.data_barang[$scope.kode].Harga;
      $scope.IsSerialize = $scope.data_barang[$scope.kode].IsSerialize;

      if ($scope.IsSerialize === "1") {
        $scope.disabledSN = false;
        // $scope.MinStok = parseInt($scope.aStokGudang) - 1;
        // $scope.MaxStok = parseInt($scope.aStokGudang) + 1;
        $scope.MinStok = -1;
        $scope.MaxStok = 1;
        $scope.DefaultQty = 1;
      } else {
        $scope.disabledSN = true;
        $scope.MinStok = 0;
        $scope.MaxStok = 1000000;
        $scope.DefaultQty = $scope.aStokGudang;
      }

      $('#nama_barang').val($scope.anama);
      $('#qty').focus();
      $('#qty').val($scope.DefaultQty);
      $scope.qty = parseInt($scope.DefaultQty);
    } else {
      $('#nama_barang').val("");
      $scope.qty = "";
      $scope.harga = "";
    }
  }

  $scope.addtocart = function () {
    $scope.qty = $('#qty').val();
    console.log("IsSN", $scope.IsSerialize);
    console.log("SN", $scope.serialnumber);
    console.log("Qty", parseInt($scope.qty));
    if ($scope.aidbarang !== "") {
      if (parseInt($scope.qty) !== "") {
        if ($scope.IsSerialize === "1" && $scope.serialnumber === "") {
          alert('Silahkan Serial Number untuk barang ini.');
        } else if ($scope.IsSerialize === "1" && ((parseInt($scope.qty) > 1) || (parseInt($scope.qty) < -1) || (parseInt($scope.qty) === 0))) {
          alert('Qty barang yang terserial number harus 1 atau -1');
        } else {
          var IDBarang = $scope.aidbarang;
          var NamaBarang = $scope.anama;
          var SN = $scope.serialnumber;

          var StokGudang = parseInt($scope.aStokGudang);
          var Qty = parseInt($scope.qty);
          var Harga = parseFloat($scope.harga);
          var IsSerialize = parseInt($scope.IsSerialize);
          var MinStok = parseInt($scope.MinStok);
          var MaxStok = parseInt($scope.MaxStok);
          var updated = false;


          if (IsSerialize === 0) {
            cartArray.forEach(function (entry) {
              if (IDBarang == entry["IDBarang"]) {
                updated = true;
                entry["QtyBarang"] = parseFloat(entry["QtyBarang"]) + parseFloat(Qty);
                entry["Selisih"] = parseFloat(entry["QtyBarang"]) - parseFloat(entry["StokGudang"]);
                entry["SubTotal"] = parseFloat(entry["Harga"]) * parseFloat(entry["Selisih"]);
              }
            });
          }

          if (!updated) {
            var Selisih = Qty - StokGudang;

            if (IsSerialize === 0)
              var SubTotal = parseFloat(Harga) * parseFloat(Selisih);
            else
              var SubTotal = parseFloat(Harga) * parseFloat(Qty);

            var StokAkhir = Qty;

            if (IsSerialize === 1) {
              StokAkhir = StokGudang + Qty;
              Selisih = Qty;
            }

            cartArray[noUrut] = {
              NoUrut: noUrut,
              Kode: $scope.kode,
              IDBarang: IDBarang,
              NamaBarang: NamaBarang,
              StokGudang: StokGudang,
              QtyBarang: Qty,
              Selisih: Selisih,
              Harga: Harga,
              SubTotal: SubTotal,
              SN: SN,
              MinStok: MinStok,
              MaxStok: MaxStok,
              IsSerialize: IsSerialize,
              StokAkhir: StokAkhir
            };
            if (IsSerialize === 1)
              $scope.data_barang[$scope.kode].StokGudang = parseInt($scope.data_barang[$scope.kode].StokGudang) + parseInt(Qty);
            noUrut += 1;
          }

          $('#nama_barang').val('');
          $scope.qty = "";
          $scope.aStokGudang = "";
          $scope.anama = "";
          $scope.aidbarang = 0;
          $scope.kode = "";
          $scope.harga = "";
          $scope.serialnumber = "";
          $scope.MinStok = "";
          $scope.MaxStok = "";

          $scope.displayCart();
        }
      } else {
        alert('Silahkan tambahkan kuantitas barang');
      }
    } else {
      alert('Ada sesuatu yang salah. Silahkan coba pilih barang anda kembali.');
    }
  }

  $scope.displayCart = function () {
    function sortFunction(a, b) {
      if (a['NoUrut'] == b['NoUrut']) {
        return 0;
      } else {
        return (a['NoUrut'] < b['NoUrut']) ? -1 : 1;
      }
    }
    console.log(cartArray);
    $scope.total = 0;
    $scope.totalHPP = 0;
    $scope.displayCartArray = cartArray.filter(function () {
      return true
    });
    $scope.displayCartArray = $scope.displayCartArray.sort(sortFunction);
    $scope.displayCartArray.forEach(function (entry) {
      $scope.total++;
      $scope.totalHPP += parseFloat(entry["SubTotal"]);
      console.log(parseFloat(entry["SubTotal"]));
    });
  }

  $scope.changeQty = function (a) {
    var QtyVal = $('#QtyBarang' + a).val();
    if (QtyVal > cartArray[a]['MaxStok']) QtyVal = cartArray[a]['MaxStok'];
    if (QtyVal < cartArray[a]['MinStok']) QtyVal = cartArray[a]['MinStok'];
    $('#QtyBarang' + a).val(QtyVal);
    cartArray[a]['QtyBarang'] = QtyVal;
    cartArray[a]['StokAkhir'] = QtyVal;
    cartArray[a]['Selisih'] = QtyVal - parseInt(cartArray[a]['StokGudang']);
    cartArray[a]['SubTotal'] = parseInt(cartArray[a]['Harga']) * parseInt(cartArray[a]['Selisih']);
    $scope.displayCart();
  }

  $scope.changeSN = function (a) {
    var SNVal = $('#SN' + a).val();
    cartArray[a]['SN'] = SNVal;
    $scope.displayCart();
  }

  $scope.removeRow = function (a) {
    var deletedCart = cartArray[a]
    if (deletedCart.IsSerialize === 1) {
      $scope.data_barang[deletedCart.Kode].StokGudang = parseInt($scope.data_barang[deletedCart.Kode].StokGudang) - parseInt(deletedCart.QtyBarang);

      var cartWithTheSameIDBarang = cartArray.filter(x => x.IDBarang === deletedCart.IDBarang && x.NoUrut > deletedCart.NoUrut);
      cartWithTheSameIDBarang.forEach(function (entry) {
        entry['StokGudang'] = parseInt(entry['StokGudang']) - parseInt(entry['QtyBarang']);
        entry['StokAkhir'] = parseInt(entry['StokAkhir']) - parseInt(entry['QtyBarang']);
      });
    }
    delete cartArray[a];
    $scope.displayCart();
    return false;
  };

  $scope.tanggal = CommonServices.currentDateID();

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      console.log(cartArray);
      if (cartArray.length === 0) {
        alert("Silahkan lengkapi produk anda!");
      } else {
        $scope.processing = true;
        $http({
          method: "POST",
          url: 'api/audit/new.php',
          data: $.param({
            'IsLD': 0,
            'tanggal': $scope.tanggal,
            'id_penjualan': $scope.spb,
            'id_gudang': $scope.id_gudang,
            'usrlogin': $rootScope.userLoginName,
            'total': $scope.total,
            'totalHPP': $scope.totalHPP,
            'keterangan': $scope.keterangan,
            'uploaded': $scope.userLoginID,
            'cart': JSON.stringify(cartArray)
          }),
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).success(function (data, status) {
          if (data.res == "1") {
            ngToast.create({
              className: 'success',
              content: data.mes + ' <i class="fa fa-remove"></i>'
            });
            window.document.location = '#/data-audit-stok';
          } else {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: data.mes + '. Audit stok gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
            });
          }
        });
      }
    }
  };
});

tripApp.controller('AuditDetailController', function ($rootScope, $scope, $route, $routeParams, $http, ngToast, $location, $timeout) {
  $http.get('api/audit/detail.php?id=' + $routeParams.id).success(function (data, status) {
    $scope.NoAudit = data.detail.NoAudit;
    $scope.Tanggal = data.detail.Tanggal;
    $scope.CreatedBy = data.detail.CreatedBy;
    $scope.TotalItem = data.detail.TotalItem;
    $scope.TotalHPP = data.detail.TotalHPP;
    $scope.Keterangan = data.detail.Keterangan;
    $scope.Gudang = data.detail.Gudang;

    $scope.data_detail = data.detailcart;
    // $scope.data_penjualan = data.penjualan;

    $scope.deleted_by = data.detail.deleted_by;
    $scope.deleted_date = data.detail.deleted_date;
    $scope.deleted_remark = data.detail.deleted_remark;

    setTimeout(() => {
      // $scope.spb = data.detail.IDPenjualan;
      $('#spb').val($scope.spb).trigger('change');
    }, 2000);
  });

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/audit/edit.php',
        data: $.param({
          'id_penjualan': $scope.spb,
          'no_audit': $routeParams.id
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        ngToast.create({
          className: 'success',
          content: data.mes + ' <i class="fa fa-remove"></i>'
        });
        window.document.location = '#/data-audit-stok';
      });
    }
  };
});
