tripApp.controller('AssetController', function ($rootScope, $scope, $route, $http, ngToast) {
  $scope.id_karyawan = "";
  $scope.kategori = "";
  $scope.status = "";

  $scope.getdata = function () {
    $http.get('api/asset/data-asset.php?id_karyawan=' + $scope.id_karyawan + '&kategori=' + $scope.kategori + '&status=' + $scope.status).success(function (data, status) {
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
        url: 'api/asset/delete.php',
        data: $.param({
          'idr': val
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data asset berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data asset gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }

  $scope.doPrint = function () {
    window.open($rootScope.baseURL + 'api/print/print-asset.php?id_karyawan=' + $scope.id_karyawan + '&kategori=' + $scope.kategori+ '&status=' + $scope.status, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('AssetPerpanjanganSTNKController', function ($rootScope, $scope, $route, $http, ngToast) {
  $scope.getdata = function () {
    $http.get('api/asset/jatuh-tempo-samsat.php').success(function (data, status) {
      $scope.data_display = data;
    });
  };

  $scope.getdata();

  $scope.doPrint = function () {
    window.open($rootScope.baseURL + 'api/print/print-asset.php?id_karyawan=' + $scope.id_karyawan + '&kategori=' + $scope.kategori, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('AssetNewController', function ($scope, $route, $http, ngToast, Upload) {
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
    $http.get('api/assetcategory/data-asset-category.php?jenis=Asset').success(function (data, status) {
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

  $scope.changeCat = function () {
    if ($scope.jenis === "Kendaraan") {
      $scope.show_kendaraan_field = true;
    } else {
      $scope.setDefault();
    }
  }

  $scope.deleteAttach = function(field) {
    if (confirm("Anda yakin ingin menghapus file ini?")) {
      $http({
        method: "POST",
        url: 'api/asset/delete-file.php',
        data: $.param({
          'id': $routeParams.assetId,
          'field': field
        }),
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'File berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'File gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }

  $scope.submitForm = function (isValid) {
    if (isValid) {
      if ($scope.foto1 != null || $scope.foto2 != null || $scope.foto3 != null || $scope.file1 != null || $scope.file2 != null || $scope.file3 != null || $scope.file4 != null || $scope.file5 != null) {
        $scope.processing = true;
        Upload.upload({
          url: 'api/asset/new.php',
          data: {
            'kode_asset': $scope.kode_asset,
            'category': $scope.category,
            'jenis': $scope.jenis,
            'nama': $scope.nama,
            'foto1': $scope.foto1,
            'foto2': $scope.foto2,
            'foto3': $scope.foto3,
            'deskripsi': $scope.deskripsi,
            'jns_kendaraan': $scope.jns_kendaraan,
            'manufaktur': $scope.manufaktur,
            'thn_rakit': $scope.thn_rakit,
            'no_stnk': $scope.no_stnk,
            'no_kendaraan': $scope.no_kendaraan,
            'jatuh_tempo_samsat': $scope.jatuh_tempo_samsat,
            'max_tangki': $scope.max_tangki,
            'km_kendaraan': $scope.km_kendaraan,
            'jns_bbm': $scope.jns_bbm,
            'tanggal': $scope.tanggal,
            'harga': $scope.harga,
            'unit': $scope.unit,
            'stts_asset': $scope.stts_asset,
            'file1': $scope.file1,
            'file2': $scope.file2,
            'file3': $scope.file3,
            'file4': $scope.file4,
            'file5': $scope.file5
          }
        }).then(function (resp) {
          var data = resp.data;
          if (data == "1") {
            ngToast.create({
              className: 'success',
              content: 'Data asset berhasil disimpan <i class="fa fa-remove"></i>'
            });
            window.document.location = '#/data-asset';
          } else if (data == "2") {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Kode asset telah digunakan. Silahkan gunakan kode lain <i class="fa fa-remove"></i>'
            });
          } else if (data == "3") {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Data asset gagal disimpan. Kode asset maksimal 10 digit <i class="fa fa-remove"></i>'
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
          url: 'api/asset/new.php',
          data: $.param({
            'kode_asset': $scope.kode_asset,
            'category': $scope.category,
            'jenis': $scope.jenis,
            'nama': $scope.nama,
            'deskripsi': $scope.deskripsi,
            'jns_kendaraan': $scope.jns_kendaraan,
            'manufaktur': $scope.manufaktur,
            'thn_rakit': $scope.thn_rakit,
            'no_stnk': $scope.no_stnk,
            'no_kendaraan': $scope.no_kendaraan,
            'jatuh_tempo_samsat': $scope.jatuh_tempo_samsat,
            'max_tangki': $scope.max_tangki,
            'km_kendaraan': $scope.km_kendaraan,
            'jns_bbm': $scope.jns_bbm,
            'tanggal': $scope.tanggal,
            'harga': $scope.harga,
            'unit': $scope.unit,
            'stts_asset': $scope.stts_asset
          }),
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).success(function (data, status) {
          if (data == "1") {
            ngToast.create({
              className: 'success',
              content: 'Data asset berhasil disimpan <i class="fa fa-remove"></i>'
            });
            window.document.location = '#/data-asset';
          } else if (data == "2") {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Kode asset telah digunakan. Silahkan gunakan kode lain <i class="fa fa-remove"></i>'
            });
          } else if (data == "3") {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Data asset gagal disimpan. Kode asset maksimal 10 digit <i class="fa fa-remove"></i>'
            });
          } else {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Data asset gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
            });
          }
        });
      }
    }
  };
});

tripApp.controller('AssetEditController', function ($scope, $route, $routeParams, $http, ngToast, Upload) {
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

  $http.get('api/asset/detail.php?id=' + $routeParams.assetId).success(function (data, status) {
    $scope.kode_asset = data.kode_asset;
    $scope.category = data.category;
    $scope.nama = data.nama;
    $scope.getfoto1 = data.foto1;
    $scope.getfoto2 = data.foto2;
    $scope.getfoto3 = data.foto3;

    if ($scope.getfoto1 == "") $scope.getfoto1 = true;
    if ($scope.getfoto2 == "") $scope.getfoto2 = true;
    if ($scope.getfoto3 == "") $scope.getfoto3 = true;


    $scope.deskripsi = data.deskripsi;
    $scope.jns_kendaraan = data.jns_kendaraan;
    $scope.manufaktur = data.manufaktur;
    $scope.thn_rakit = data.thn_rakit;
    $scope.no_stnk = data.no_stnk;
    $scope.no_kendaraan = data.no_kendaraan;
    $scope.jatuh_tempo_samsat = data.jatuh_tempo_samsat;
    $scope.max_tangki = data.max_tangki;
    $scope.km_kendaraan = data.km_kendaraan;
    $scope.jns_bbm = data.jns_bbm;
    $scope.jenis = data.jenis;
    $scope.tanggal = data.tanggal;
    $scope.harga = data.harga;
    $scope.unit = data.unit;
    $scope.stts_asset = data.stts_asset;

    $scope.getfile1 = data.file1;
    $scope.getfile2 = data.file2;
    $scope.getfile3 = data.file3;
    $scope.getfile4 = data.file4;
    $scope.getfile5 = data.file5;

    if ($scope.getfile1 == "") $scope.getfile1 = true;
    if ($scope.getfile2 == "") $scope.getfile2 = true;
    if ($scope.getfile3 == "") $scope.getfile3 = true;
    if ($scope.getfile4 == "") $scope.getfile4 = true;
    if ($scope.getfile5 == "") $scope.getfile5 = true;

    if ($scope.jenis === "Kendaraan") {
      $scope.show_kendaraan_field = true;
    } else {
      $scope.setDefault();
    }
  });

  $scope.getdata = function () {
    $http.get('api/assetcategory/data-asset-category.php?jenis=Asset').success(function (data, status) {
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

  $scope.changeCat = function () {
    if ($scope.jenis == "Kendaraan") {
      //alert($scope.category);
      $scope.show_kendaraan_field = true;
    } else {
      //alert("NO");
      $scope.setDefault();
    }
  }

  $scope.deleteAttach = function(field) {
    if (confirm("Anda yakin ingin menghapus file ini?")) {
      $http({
        method: "POST",
        url: 'api/asset/delete-file.php',
        data: $.param({
          'id': $routeParams.assetId,
          'field': field
        }),
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'File berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'File gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }

  $scope.submitForm = function (isValid) {
    if (isValid) {
      if ($scope.foto1 != null || $scope.foto2 != null || $scope.foto3 != null || $scope.file1 != null || $scope.file2 != null || $scope.file3 != null || $scope.file4 != null || $scope.file5 != null) {
        $scope.processing = true;
        Upload.upload({
          url: 'api/asset/edit.php',
          data: {
            'kode_asset': $scope.kode_asset,
            'category': $scope.category,
            'jenis': $scope.jenis,
            'nama': $scope.nama,
            'foto1': $scope.foto1,
            'foto2': $scope.foto2,
            'foto3': $scope.foto3,
            'deskripsi': $scope.deskripsi,
            'jns_kendaraan': $scope.jns_kendaraan,
            'manufaktur': $scope.manufaktur,
            'thn_rakit': $scope.thn_rakit,
            'no_stnk': $scope.no_stnk,
            'no_kendaraan': $scope.no_kendaraan,
            'jatuh_tempo_samsat': $scope.jatuh_tempo_samsat,
            'max_tangki': $scope.max_tangki,
            'km_kendaraan': $scope.km_kendaraan,
            'jns_bbm': $scope.jns_bbm,
            'tanggal': $scope.tanggal,
            'harga': $scope.harga,
            'unit': $scope.unit,
            'stts_asset': $scope.stts_asset,
            'file1': $scope.file1,
            'file2': $scope.file2,
            'file3': $scope.file3,
            'file4': $scope.file4,
            'file5': $scope.file5,
            'id': $routeParams.assetId
          }
        }).then(function (resp) {
          var data = resp.data;
          if (data == "1") {
            ngToast.create({
              className: 'success',
              content: 'Data asset berhasil diperbaharui <i class="fa fa-remove"></i>'
            });
            window.document.location = '#/data-asset';
          } else if (data == "2") {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Kode asset telah digunakan. Silahkan gunakan kode lain <i class="fa fa-remove"></i>'
            });
          } else if (data == "3") {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Data asset gagal diperbaharui. Kode asset maksimal 10 digit <i class="fa fa-remove"></i>'
            });
          } else {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Data asset gagal diperbaharui. Silahkan coba kembali <i class="fa fa-remove"></i>'
            });
          }
        });
      } else {
        $scope.processing = true;
        $http({
          method: "POST",
          url: 'api/asset/edit.php',
          data: $.param({
            'kode_asset': $scope.kode_asset,
            'category': $scope.category,
            'jenis': $scope.jenis,
            'nama': $scope.nama,
            'foto1': $scope.foto1,
            'foto2': $scope.foto2,
            'foto3': $scope.foto3,
            'deskripsi': $scope.deskripsi,
            'jns_kendaraan': $scope.jns_kendaraan,
            'manufaktur': $scope.manufaktur,
            'thn_rakit': $scope.thn_rakit,
            'no_stnk': $scope.no_stnk,
            'no_kendaraan': $scope.no_kendaraan,
            'jatuh_tempo_samsat': $scope.jatuh_tempo_samsat,
            'max_tangki': $scope.max_tangki,
            'km_kendaraan': $scope.km_kendaraan,
            'jns_bbm': $scope.jns_bbm,
            'tanggal': $scope.tanggal,
            'harga': $scope.harga,
            'unit': $scope.unit,
            'stts_asset': $scope.stts_asset,
            'id': $routeParams.assetId
          }),
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).success(function (data, status) {
          if (data == "1") {
            ngToast.create({
              className: 'success',
              content: 'Data asset berhasil diperbaharui <i class="fa fa-remove"></i>'
            });
            window.document.location = '#/data-asset';
          } else if (data == "2") {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Kode asset telah digunakan. Silahkan gunakan kode lain <i class="fa fa-remove"></i>'
            });
          } else if (data == "3") {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Data asset gagal diperbaharui. Kode asset maksimal 10 digit <i class="fa fa-remove"></i>'
            });
          } else {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Data asset gagal diperbaharui. Silahkan coba kembali <i class="fa fa-remove"></i>'
            });
          }
        });
      }
    }
  };
});
