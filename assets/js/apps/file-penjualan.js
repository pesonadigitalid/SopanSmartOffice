tripApp.controller('FilePenjualanController', function ($rootScope, $scope, $route, $http, ngToast, $routeParams, Upload) {
  $scope.kategoriSpbId = $routeParams.kategoriSpbId;
  $scope.idPenjualan = $routeParams.idPenjualan;

  $scope.getdata = function () {
    $http.get('api/file-penjualan/file.php?act=DisplayData&IDPenjualanFileCategory=' + $scope.kategoriSpbId + '&IDPenjualan=' + $scope.idPenjualan).success(function (data, status) {
      $scope.data = data.payload.data;
      $scope.fileCategory = data.payload.fileCategory;
      $scope.penjualanName = data.payload.penjualanName;
    });
  };
  $scope.getdata();

  $scope.refreshData = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }

  $scope.removeRow = function (val) {
    if (confirm("Anda yakin ingin menghapus data ini?")) {
      $http({
        method: "POST",
        url: 'api/file-penjualan/file.php?act=Delete',
        data: $.param({
          'IDFile': val
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data ' + $scope.fileCategory + ' berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data ' + $scope.fileCategory + ' gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }
});

tripApp.controller('FilePenjualanNewController', function ($scope, $route, $q, $http, ngToast, $routeParams, Upload) {
  $scope.New = true;
  $scope.kategoriSpbId = $routeParams.kategoriSpbId;
  $scope.idPenjualan = $routeParams.idPenjualan;

  $scope.getdata = function () {
    $http.get('api/file-penjualan/file.php?act=LoadAllRequirement&IDPenjualanFileCategory=' + $scope.kategoriSpbId + '&IDPenjualan=' + $scope.idPenjualan).success(function (data, status) {
      $scope.fileCategory = data.payload.fileCategory;
      $scope.penjualanName = data.payload.penjualanName;
    });
  };
  $scope.getdata();

  $scope.uploadFile = function () {
    var filename = event.target.files[0].name;
    var files = filename.split('.')
    files.pop()
    $scope.Nama = files.join('.');
  };

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/file-penjualan/file.php?act=InsertNew',
        data: {
          'IDPenjualan': $scope.idPenjualan,
          'IDPenjualanFileCategory': $scope.kategoriSpbId,
          'Nama': $scope.Nama,
          'Keterangan': $scope.Keterangan,
          'file': $scope.file
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data ' + $scope.fileCategory + ' berhasil disimpan <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-file-penjualan/' + $scope.kategoriSpbId + '/' + $scope.idPenjualan;
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data ' + $scope.fileCategory + ' gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('FilePenjualanEditController', function ($scope, $route, $q, $http, ngToast, $routeParams, Upload) {
  $scope.New = false;
  $scope.kategoriSpbId = $routeParams.kategoriSpbId;
  $scope.idPenjualan = $routeParams.idPenjualan;
  $scope.idFile = $routeParams.id;

  $scope.getdata = function () {
    $http.get('api/file-penjualan/file.php?act=Detail&IDPenjualanFileCategory=' + $scope.kategoriSpbId + '&IDFile=' +  $scope.idFile + '&IDPenjualan=' + $scope.idPenjualan).success(function (data, status) {
      $scope.Nama = data.payload.data.Nama;
      $scope.Keterangan = data.payload.data.Keterangan;
      $scope.file = data.payload.data.FileName;
      $scope.fileview = data.payload.data.FileName;

      $scope.fileCategory = data.payload.fileCategory;
      $scope.penjualanName = data.payload.penjualanName;
    });
  };
  $scope.getdata();

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/file-penjualan/file.php?act=Update',
        data: {
          'IDPenjualan': $scope.idPenjualan,
          'IDFile':  $scope.idFile,
          'IDPenjualanFileCategory': $scope.kategoriSpbId,
          'Nama': $scope.Nama,
          'Keterangan': $scope.Keterangan,
          'file': $scope.file
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data ' + $scope.fileCategory + ' berhasil diperbaharui <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-file-penjualan/' + $scope.kategoriSpbId + '/' + $scope.idPenjualan;
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data ' + $scope.fileCategory + ' gagal diperbaharui. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});
