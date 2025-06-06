tripApp.controller('PengumumanController', function ($rootScope, $scope, $route, $http, ngToast, CommonServices) {
  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();

  $('.datepick').datepicker({
      format: 'dd/mm/yyyy',
      showOtherMonths: true,
      selectOtherMonths: true,
      autoclose: true
  });

  $scope.getdata = function () {
    $http.get('api/pengumuman/pengumuman.php?act=DataList&datestart=' + $scope.datestart + '&dateend=' + $scope.dateend).success(function (data, status) {
      $scope.data_pengumuman = data;
    });
  };
  $scope.getdata();

  $scope.filtersupplier = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }

  $scope.removeRow = function (val) {
    if (confirm("Anda yakin ingin menghapus data ini?")) {
      $http({
        method: "POST",
        url: 'api/pengumuman/pengumuman.php?act=DeleteRecord',
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
            content: 'Data pengumuman record berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data pengumuman record tidak dapat dihapus. Silahkan coba lagi nanti... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }

  $scope.doPrint = function (a) {
    window.open($rootScope.baseURL + 'api/print/print-supplier.php?id=' + a, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('PengumumanNewController', function ($rootScope, $scope, $route, $routeParams, $http, ngToast) {
  $scope.processing = false;
  $scope.disablecode = true;

  $('.datepick').datepicker({
      format: 'dd/mm/yyyy',
      showOtherMonths: true,
      selectOtherMonths: true,
      autoclose: true
  });

  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/pengumuman/pengumuman.php?act=NewRecord',
        data: $.param({
          'judul': $scope.judul,
          'keterangan': $scope.keterangan
        }),
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data pengumuman berhasil disimpan <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-pengumuman';
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data pengumuman gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});