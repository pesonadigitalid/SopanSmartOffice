tripApp.controller('NotifikasiMaintenanceController', function ($rootScope, $scope, $route, $http, ngToast, CommonServices) {
  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();
  $scope.tipe = "";
  $scope.status = "";

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.getdata = function () {
    $http.get('api/notifikasi/data-notifikasi-maintenance.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&tipe=' + $scope.tipe + '&status=' + $scope.status).success(function (data, status) {
      $scope.data = data;
    });
  };

  $scope.getdata();

  $scope.filterData = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }

  $scope.refreshData = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }
});

tripApp.controller('NotifikasiMaintenanceEditController', function ($scope, $route, $routeParams, $http, ngToast, Upload) {
  $scope.processing = false;

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $http.get('api/notifikasi/detail.php?id=' + $routeParams.scheduleId).success(function (data, status) {
    $scope.noFaktur = data.NoFaktur;
    $scope.tanggalFaktur = data.TanggalFakturID;
    $scope.namaPelanggan = data.NamaPelanggan;
    $scope.keterangan = data.Keterangan;
    $scope.tanggalAkhirMaintenance = data.TanggalAkhirMaintenanceID;
    $scope.status = data.Status;
    $scope.keteranganStatus = data.KeteranganStatus;
  });

  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/notifikasi/edit.php',
        data: $.param({
          'keteranganStatus': $scope.keteranganStatus,
          'status': $scope.status,
          'id': $routeParams.scheduleId
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data notifikasi berhasil diperbaharui <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-notifikasi-maintenance';
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data notifikasi gagal diperbaharui. Silahkan coba kembali <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});
