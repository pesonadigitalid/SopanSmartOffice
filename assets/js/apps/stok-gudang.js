tripApp.controller('StokGudangController', function ($rootScope, $scope, $route, $http, ngToast) {
  $scope.jenis = "";
  $scope.gudang = "";

  $scope.getdata = function () {
    $http.get('api/stok-gudang/data-stok-gudang.php?act=ListData&jenis=' + $scope.jenis + '&gudang=' + $scope.gudang).success(function (data, status) {
      $scope.data_stok_gudang = data.DataStok;
      $scope.data_material = data.DataMaterial;
      $scope.data_gudang = data.DataGudang;
    });
  };
  $scope.getdata();

  $scope.refreshData = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }

  $scope.showModalStokGudang = function (a, b) {
    $scope.getdataStok = function () {
      $http.get('api/stok-gudang/stok-gudang-modal.php?id=' + a).success(function (data, status) {
        $scope.stok_gudang_modal = data;
      });
    };
    $scope.getdataStok();

    $('#myModal').modal('show');
    $('#myModal').children('.modal-dialog').removeClass('modal-lg');
    $scope.StokAkhir = b;
  };

  $scope.showModalStokPurchasing = function (a, b) {
    $scope.getdataStok = function () {
      $http.get('api/stok-gudang/stok-purchasing-modal.php?id=' + a).success(function (data, status) {
        $scope.stok_purhasing_modal = data;
      });
    };
    $scope.getdataStok();

    $('#myModal2').modal('show');
    $('#myModal2').children('.modal-dialog').removeClass('modal-lg');
    $scope.StokAkhir = b;
  };

  $scope.doPrint = function () {
    window.open($rootScope.baseURL + 'api/print/print-data-stok-gudang.php?jenis=' + $scope.jenis + '&gudang=' + $scope.gudang, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('KartuStokGudangController', function ($rootScope, $scope, $routeParams, $route, $http, ngToast) {
  $scope.getdata = function () {
    $http.get('api/stok-gudang/stok-gudang-modal.php?id=' + $routeParams.id + '&gudang=' + $routeParams.idGudang).success(function (data, status) {
      $scope.stok_gudang_modal = data.results;
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
    window.open($rootScope.baseURL + 'api/print/print-data-stok-gudang.php?jenis=' + $scope.jenis, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('StokGudangDetailController', function ($rootScope, $scope, $routeParams, $route, $http, ngToast) {
  $scope.getdata = function () {
    $http.get('api/stok-gudang/data-stok-gudang.php?act=DetailStokGudang&id=' + $routeParams.idBarang).success(function (data, status) {
      $scope.data_stok_gudang = data;
    });
  };
  $scope.getdata();
});

tripApp.controller('StokGudangEditController', function ($rootScope, $scope, $routeParams, $route, $http, ngToast) {
  $scope.getdata = function () {
    $http.get('api/stok-gudang/data-stok-gudang.php?act=DetailSN&id=' + $routeParams.idStok).success(function (data, status) {
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
        url: 'api/stok-gudang/data-stok-gudang.php?act=EditSNStokGudang',
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
          window.document.location = '#/detail-stok-gudang/' + $scope.id_barang;
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