tripApp.controller('JabatanCtrl', function ($rootScope, $scope, $route, $http, ngToast) {

  $scope.getdata = function () {
    $http.get('api/jabatan/data-jabatan.php').success(function (data, status) {
      $scope.data_jabatan = data;
    });
  };
  $scope.getdata();

  $scope.removeRow = function (val) {
    if (confirm("Anda yakin ingin menghapus data ini?")) {
      $http({
        method: "POST",
        url: 'api/jabatan/delete.php',
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
            content: 'Data jabatan berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else if (data == "2") {
          ngToast.create({
            className: 'danger',
            content: 'Data jabatan tidak bisa dihapus karena sudah terhubung dengan data karyawan. Menghapus paksa dapat merusak sistem <i class="fa fa-remove"></i>'
          });
        } else if (data == "3") {
          ngToast.create({
            className: 'danger',
            content: 'Data jabatan tidak bisa dihapus karena digunakan oleh sistem untuk module tertentu. <i class="fa fa-remove"></i>'
          });
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data jabatan gagal dihapus. Silahkan coba kembali lagi <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }
});

tripApp.controller('JabatanNewCtrl', function ($scope, $route, $http, ngToast) {
  $scope.processing = false;
  $scope.disablecode = false;
  $scope.statusUser = "0";

  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/jabatan/new.php',
        data: $.param({
          'nama': $scope.nama
        }),
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data jabatan berhasil ditambahkan <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-jabatan';
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data jabatan gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('JabatanEditCtrl', function ($rootScope, $scope, $route, $routeParams, $http, ngToast) {
  $scope.processing = false;
  $scope.disablecode = true;

  $http.get('api/jabatan/detail.php?id=' + $routeParams.jabatanId).success(function (data, status) {
    $scope.nama = data.nama;
  });

  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/jabatan/edit.php',
        data: $.param({
          'nama': $scope.nama,
          'id': $routeParams.jabatanId
        }),
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data jabatan berhasil diperbaharui <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-jabatan';
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data jabatan gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});