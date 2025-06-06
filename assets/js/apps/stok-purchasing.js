tripApp.controller('StokPurchasingController', function ($rootScope, $scope, $route, $http, ngToast) {
  $scope.jenis = "";
  $scope.spb = "";
  $scope.gudang = "";
  $scope.tipe = "0";

  $scope.getdata = function () {
    $http.get('api/stok-purchasing/data-stok-purchasing.php?act=ListData&jenis=' + $scope.jenis + '&gudang=' + $scope.gudang + '&spb=' + $scope.spb + '&tipe=' + $scope.tipe).success(function (data, status) {
      $scope.data_stok_purchasing = data.DataStok;
      $scope.data_material = data.DataMaterial;
      $scope.data_spb = data.DataSPB;
      $scope.data_gudang = data.DataGudang;
    });
  };
  $scope.getdata();

  $scope.refreshData = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }

  $scope.showModalStokPurchasing = function (a, b) {
    $scope.getdataStok = function () {
      $http.get('api/stok-purchasing/stok-purchasing-modal.php?id=' + a).success(function (data, status) {
        $scope.stok_purchasing_modal = data;
      });
    };
    $scope.getdataStok();

    $('#myModal').modal('show');
    $('#myModal').children('.modal-dialog').removeClass('modal-lg');
    $scope.StokAkhir = b;
  };

  $scope.showModalStokPurchasing = function (a, b) {
    $scope.getdataStok = function () {
      $http.get('api/stok-purchasing/stok-purchasing-modal.php?id=' + a).success(function (data, status) {
        $scope.stok_purhasing_modal = data;
      });
    };
    $scope.getdataStok();

    $('#myModal2').modal('show');
    $('#myModal2').children('.modal-dialog').removeClass('modal-lg');
    $scope.StokAkhir = b;
  };

  $scope.doPrint = function () {
    window.open($rootScope.baseURL + 'api/print/print-data-stok-purchasing.php?jenis=' + $scope.jenis + '&gudang=' + $scope.gudang + '&spb=' + $scope.spb, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('KartuStokPurchasingController', function ($rootScope, $scope, $routeParams, $route, $http, ngToast) {
  $scope.getdata = function () {
    $http.get('api/stok-purchasing/stok-purchasing-modal.php?id=' + $routeParams.id + '&gudang=' + $routeParams.idGudang + '&idPenjualan=' + $routeParams.idPenjualan).success(function (data, status) {
      $scope.stok_purchasing_modal = data.results;
      $scope.StokAkhir = data.StokAkhir;
      $scope.HPPAkhir = data.HPPAkhir;
      $scope.NamaBarang = data.NamaBarang;
    });
  };
  $scope.getdata();

  $scope.refreshData = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }

  $scope.doPrint = function () {
    window.open($rootScope.baseURL + 'api/print/print-data-stok-purchasing.php?jenis=' + $scope.jenis, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('StokPurchasingDetailController', function ($rootScope, $scope, $routeParams, $route, $http, ngToast) {
  $scope.getdata = function () {
    $http.get('api/stok-purchasing/data-stok-purchasing.php?act=DetailStokPurchasing&id=' + $routeParams.idBarang).success(function (data, status) {
      $scope.data_stok_purchasing = data;
    });
  };
  $scope.getdata();
});

tripApp.controller('StokPurchasingEditController', function ($rootScope, $scope, $routeParams, $route, $http, ngToast) {
  $scope.getdata = function () {
    $http.get('api/stok-purchasing/data-stok-purchasing.php?act=DetailSN&id=' + $routeParams.idStok).success(function (data, status) {
      $scope.serial_number = data.SerialNumber;
      $scope.id_barang = data.IDBarang;
    });
  };
  $scope.getdata();

  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/stok-purchasing/data-stok-purchasing.php?act=EditSNStokPurchasing',
        data: $.param({
          'serial_number': $scope.serial_number,
          'id': $routeParams.idStok
        }),
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Serial number berhasil diperbaharui <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/detail-stok-purchasing/' + $scope.id_barang;
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Serial number barang gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});