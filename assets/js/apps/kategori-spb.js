tripApp.controller('KategoriSPBController', function ($rootScope, $scope, $route, $http, ngToast) {

  $scope.getdata = function () {
    $http.get('api/kategori-spb/data-kategori-spb.php').success(function (data, status) {
      $scope.data_file_category = data;
    });
  };
  $scope.getdata();

  $scope.removeRow = function (val) {
    if (confirm("Anda yakin ingin menghapus data ini?")) {
      $http({
        method: "POST",
        url: 'api/kategori-spb/delete.php',
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
            content: 'Data Kategori SPB berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else if (data == "2") {
          ngToast.create({
            className: 'danger',
            content: 'Data Kategori SPB tidak bisa dihapus karena sudah terhubung dengan data file. Menghapus paksa dapat merusak sistem <i class="fa fa-remove"></i>'
          });
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data Kategori SPB gagal dihapus. Silahkan coba kembali lagi <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }
});

tripApp.controller('KategoriSPBNewController', function ($scope, $route, $http, ngToast, $routeParams) {
  $scope.processing = false;
  $scope.disablecode = false;
  $scope.New = true;
  $scope.Status = '1';

  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/kategori-spb/new.php',
        data: $.param({
          'Nama': $scope.Nama,
          'Status': $scope.Status
        }),
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data Kategori SPB berhasil ditambahkan <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-kategori-spb';
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data Kategori SPB gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('KategoriSPBEditController', function ($rootScope, $scope, $route, $routeParams, $http, ngToast) {
  $scope.processing = false;
  $scope.disablecode = true;
  $scope.New = false;
  $scope.Status = '1';

  $scope.getAllData = function () {
    $http.get('api/kategori-spb/detail.php?id=' + $routeParams.kategoriSpbId).success(function (data, status) {
      $scope.Nama = data.Nama;
      $scope.Status = data.Status;
    });
  };

  $scope.getAllData();

  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/kategori-spb/edit.php',
        data: $.param({
          'Nama': $scope.Nama,
          'Status': $scope.Status,
          'IDPenjualanFileCategory': $routeParams.kategoriSpbId
        }),
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data Kategori SPB berhasil diperbaharui <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-kategori-spb';
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data Kategori SPB gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});
