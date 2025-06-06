tripApp.controller('AssetUsahaController', function ($rootScope, $scope, $route, $http, ngToast) {
  $scope.id_karyawan = "";
  $scope.kategori = "";
  $scope.status = "";

  $scope.getdata = function () {
    $http.get('api/asset-usaha/data-asset.php?id_karyawan=' + $scope.id_karyawan + '&kategori=' + $scope.kategori + '&status=' + $scope.status).success(function (data, status) {
      $scope.data_asset = data.assetArray;
      $scope.data_karyawan = data.karyawanArray;
      $scope.data_catAsset = data.assetCatArray;
    });
  };

  $scope.getdata();

  $scope.refreshData = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }



  $scope.showHistory = function (a) {
    $http.get('api/asset/load-all-requirement.php?id=' + a).success(function (data, status) {
      $scope.data_assign = data.dataAssign;
    });
    $('#myModal').modal('show');
  }


  $scope.removeRow = function (val) {
    if (confirm("Anda yakin ingin menghapus data ini?")) {
      $http({
        method: "POST",
        url: 'api/asset-usaha/delete.php',
        data: $.param({
          'idr': val
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data asset ijin usaha berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data asset ijin usaha gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }

  $scope.doPrint = function () {
    window.open($rootScope.baseURL + 'api/print/print-asset-usaha.php?id_karyawan=' + $scope.id_karyawan + '&kategori=' + $scope.kategori + '&status=' + $scope.status, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('AssetUsahaPerpanjanganIjinUsahaController', function ($rootScope, $scope, $route, $http, ngToast) {
  $scope.getdata = function () {
    $http.get('api/asset-usaha/jatuh-tempo-usaha.php').success(function (data, status) {
      $scope.data_display = data;
    });
  };

  $scope.getdata();

  $scope.doPrint = function () {
    window.open($rootScope.baseURL + 'api/print/print-asset.php?id_karyawan=' + $scope.id_karyawan + '&kategori=' + $scope.kategori, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('AssetUsahaNewController', function ($scope, $route, $http, ngToast, Upload) {
  $scope.processing = false;
  $scope.disablecode = false;
  $scope.getfoto1 = false;
  $scope.getfoto2 = false;
  $scope.getfoto3 = false;
  $scope.stts_asset = "1";
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.setDefault = function () {
    $scope.show_kendaraan_field = false;
    $scope.jns_kendaraan = "";
    $scope.jns_bbm = "";
  };

  $scope.setDefault();

  $scope.getdata = function () {
    $http.get('api/assetcategory/data-asset-category.php?jenis=Ijin-Usaha').success(function (data, status) {
      $scope.data_assetcategory = data;
    });
  };

  $scope.getdata();

  $scope.lenghtkdasset = function () {
    //alert("OK");
    var KodeAsset = $('#kode_asset').val();
    if (KodeAsset.length > 20) {
      ngToast.create({
        className: 'danger',
        content: 'Mohon maaf kode asset terlalu panjang. Maksimal 20 digit <i class="fa fa-remove"></i>'
      });
    }
  }

  $scope.submitForm = function (isValid) {
    if (isValid) {
      if ($scope.file != null) {
        $scope.processing = true;
        Upload.upload({
          url: 'api/asset-usaha/new.php',
          data: {
            'kode_asset': $scope.kode_asset,
            'category': $scope.category,
            'nama': $scope.nama,
            'deskripsi': $scope.deskripsi,
            'no_ijin_usaha': $scope.no_ijin_usaha,
            'jatuh_tempo_usaha': $scope.jatuh_tempo_usaha,
            'stts_asset': $scope.stts_asset,
            'file_usaha': $scope.file_usaha,
          }
        }).then(function (resp) {
          var data = resp.data;
          if (data == "1") {
            ngToast.create({
              className: 'success',
              content: 'Data asset ijin usaha berhasil disimpan <i class="fa fa-remove"></i>'
            });
            window.document.location = '#/data-asset-usaha';
          } else if (data == "2") {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Kode asset ijin usaha telah digunakan. Silahkan gunakan kode lain <i class="fa fa-remove"></i>'
            });
          } else if (data == "3") {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Data asset ijin usaha gagal disimpan. Kode asset ijin usaha maksimal 20 digit <i class="fa fa-remove"></i>'
            });
          } else {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Data asset gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
            });
          }
        });
      } else {
        $scope.processing = true;
        $http({
          method: "POST",
          url: 'api/asset-usaha/new.php',
          data: $.param({
            'kode_asset': $scope.kode_asset,
            'category': $scope.category,
            'nama': $scope.nama,
            'deskripsi': $scope.deskripsi,
            'no_ijin_usaha': $scope.no_ijin_usaha,
            'jatuh_tempo_usaha': $scope.jatuh_tempo_usaha,
            'stts_asset': $scope.stts_asset,
          }),
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).success(function (data, status) {
          if (data == "1") {
            ngToast.create({
              className: 'success',
              content: 'Data asset ijin usaha berhasil disimpan <i class="fa fa-remove"></i>'
            });
            window.document.location = '#/data-asset-usaha';
          } else if (data == "2") {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Kode asset ijin usaha telah digunakan. Silahkan gunakan kode lain <i class="fa fa-remove"></i>'
            });
          } else if (data == "3") {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Data asset ijin usaha gagal disimpan. Kode asset ijin usaha maksimal 10 digit <i class="fa fa-remove"></i>'
            });
          } else {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Data asset ijin usaha gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
            });
          }
        });
      }
    }
  };
});

tripApp.controller('AssetUsahaEditController', function ($scope, $route, $routeParams, $http, ngToast, Upload) {
  $scope.processing = false;
  $scope.disablecode = true;
  $scope.getfoto1 = true;
  $scope.getfoto2 = true;
  $scope.getfoto3 = true;
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.setDefault = function () {
    $scope.show_kendaraan_field = false;
    $scope.jns_kendaraan = "";
    $scope.jns_bbm = "";
  };

  $scope.setDefault();

  $http.get('api/asset-usaha/detail.php?id=' + $routeParams.assetId).success(function (data, status) {
    $scope.kode_asset = data.kode_asset;
    $scope.category = data.category;
    $scope.nama = data.nama;
    $scope.deskripsi = data.deskripsi;
    $scope.no_ijin_usaha = data.no_ijin_usaha;
    $scope.jatuh_tempo_usaha = data.jatuh_tempo_usaha;
    $scope.stts_asset = data.stts_asset;
  });

  $scope.getdata = function () {
    $http.get('api/assetcategory/data-asset-category.php?jenis=Ijin-Usaha').success(function (data, status) {
      $scope.data_assetcategory = data;
    });
  };

  $scope.getdata();

  $scope.lenghtkdasset = function () {
    //alert("OK");
    var KodeAsset = $('#kode_asset').val();
    if (KodeAsset.length > 20) {
      ngToast.create({
        className: 'danger',
        content: 'Mohon maaf kode asset terlalu panjang. Maksimal 20 digit <i class="fa fa-remove"></i>'
      });
    }
  }

  $scope.submitForm = function (isValid) {
    if (isValid) {
      if ($scope.foto1 != null || $scope.foto2 != null || $scope.foto3 != null) {
        $scope.processing = true;
        Upload.upload({
          url: 'api/asset-usaha/edit.php',
          data: {
            'kode_asset': $scope.kode_asset,
            'category': $scope.category,
            'nama': $scope.nama,
            'deskripsi': $scope.deskripsi,
            'no_ijin_usaha': $scope.no_ijin_usaha,
            'jatuh_tempo_usaha': $scope.jatuh_tempo_usaha,
            'stts_asset': $scope.stts_asset,
            'file_usaha': $scope.file_usaha,
            'id': $routeParams.assetId
          }
        }).then(function (resp) {
          var data = resp.data;
          if (data == "1") {
            ngToast.create({
              className: 'success',
              content: 'Data asset ijin usaha berhasil diperbaharui <i class="fa fa-remove"></i>'
            });
            window.document.location = '#/data-asset-usaha';
          } else if (data == "2") {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Kode asset ijin usaha telah digunakan. Silahkan gunakan kode lain <i class="fa fa-remove"></i>'
            });
          } else if (data == "3") {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Data asset ijin usaha gagal diperbaharui. Kode asset ijin usaha maksimal 20 digit <i class="fa fa-remove"></i>'
            });
          } else {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Data asset ijin usaha gagal diperbaharui. Silahkan coba kembali <i class="fa fa-remove"></i>'
            });
          }
        });
      } else {
        $scope.processing = true;
        $http({
          method: "POST",
          url: 'api/asset-usaha/edit.php',
          data: $.param({
            'kode_asset': $scope.kode_asset,
            'category': $scope.category,
            'nama': $scope.nama,
            'deskripsi': $scope.deskripsi,
            'no_ijin_usaha': $scope.no_ijin_usaha,
            'jatuh_tempo_usaha': $scope.jatuh_tempo_usaha,
            'stts_asset': $scope.stts_asset,
            'id': $routeParams.assetId
          }),
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).success(function (data, status) {
          if (data == "1") {
            ngToast.create({
              className: 'success',
              content: 'Data asset ijin usaha berhasil diperbaharui <i class="fa fa-remove"></i>'
            });
            window.document.location = '#/data-asset-usaha';
          } else if (data == "2") {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Kode asset ijin usaha telah digunakan. Silahkan gunakan kode lain <i class="fa fa-remove"></i>'
            });
          } else if (data == "3") {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Data asset ijin usaha gagal diperbaharui. Kode asset ijin usaha maksimal 10 digit <i class="fa fa-remove"></i>'
            });
          } else {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Data asset ijin usaha gagal diperbaharui. Silahkan coba kembali <i class="fa fa-remove"></i>'
            });
          }
        });
      }
    }
  };
});
