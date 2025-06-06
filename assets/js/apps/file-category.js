tripApp.controller('FileCategoryCtrl', function ($rootScope, $scope, $route, $http, ngToast) {

  $scope.getdata = function () {
    $http.get('api/file-category/data-file-category.php').success(function (data, status) {
      $scope.data_file_category = data;
    });
  };
  $scope.getdata();

  $scope.removeRow = function (val) {
    if (confirm("Anda yakin ingin menghapus data ini?")) {
      $http({
        method: "POST",
        url: 'api/file-category/delete.php',
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
            content: 'Data Kategori File berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else if (data == "2") {
          ngToast.create({
            className: 'danger',
            content: 'Data Kategori File tidak bisa dihapus karena sudah terhubung dengan data file. Menghapus paksa dapat merusak sistem <i class="fa fa-remove"></i>'
          });
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data Kategori File gagal dihapus. Silahkan coba kembali lagi <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }
});

tripApp.controller('FileCategoryNewCtrl', function ($scope, $route, $http, ngToast, $routeParams) {
  $scope.processing = false;
  $scope.disablecode = false;
  $scope.New = true;
  $scope.Status = '1';

  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/file-category/new.php',
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
            content: 'Data Kategori File berhasil ditambahkan <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-file-category';
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data Kategori File gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('FileCategoryEditCtrl', function ($rootScope, $scope, $route, $routeParams, $http, ngToast) {
  $scope.processing = false;
  $scope.disablecode = true;
  $scope.New = false;
  $scope.Status = '1';

  $scope.getAllData = function () {
    $http.get('api/file-category/detail.php?id=' + $routeParams.fileCategoryId).success(function (data, status) {
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
        url: 'api/file-category/edit.php',
        data: $.param({
          'Nama': $scope.Nama,
          'Status': $scope.Status,
          'IDFileCategory': $routeParams.fileCategoryId
        }),
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data Kategori File berhasil diperbaharui <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-file-category';
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data Kategori File gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});