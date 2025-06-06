tripApp.controller('PublicHolidayController', function($scope, $route, $routeParams, $http, ngToast, CommonServices) {
  // $scope.datestart = CommonServices.firstDateMonth();
  // $scope.dateend = CommonServices.lastDateMonth();
  $scope.datestart = CommonServices.currentYear().toString();
  $scope.yearlist = CommonServices.yearList();

  $scope.getdata = function() {
    $http.get('api/public-holiday/public-holiday.php?act=DataList&datestart=' + $scope.datestart + '&dateend=' + $scope.dateend).success(function(data, status) {
      $scope.data_holiday = data.data;
    });
  };

  $scope.getdata();

  $scope.filterdata = function() {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  };

  $scope.removeRow = function(val) {
    if (confirm("Anda yakin ingin menghapus data ini?")) {
      $http({
        method: "POST",
        url: 'api/public-holiday/public-holiday.php?act=Delete',
        data: $.param({
          'idr': val
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function(data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data Public Holiday berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data Public Holiday gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }
});

tripApp.controller('PublicHolidayNewController', function($scope, $route, $http, ngToast) {
  $scope.processing = false;
  $scope.disablecode = false;

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.submitForm = function(isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/public-holiday/public-holiday.php?act=NewRecord',
        data: $.param({
          'nama_hari_libur': $scope.nama_hari_libur,
          'dari_tanggal': $scope.dari_tanggal,
          'sampai_tanggal': $scope.sampai_tanggal,
          'keterangan': $scope.keterangan
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function(data, status) {
        if (data.res == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data Public Holiday berhasil disimpan <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-public-holiday';
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: data.msg + '<i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('PublicHolidayEditController', function($scope, $route, $routeParams, $http, ngToast) {
  $scope.processing = false;
  $scope.disablecode = true;
  $scope.hideSave = false;

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.getdata = function() {
    $http.get('api/public-holiday/public-holiday.php?act=Detail&id=' + $routeParams.id).success(function(data, status) {
      $scope.nama_hari_libur = data.detail.nama_hari_libur;
      $scope.dari_tanggal = data.detail.dari_tanggal;
      $scope.sampai_tanggal = data.detail.sampai_tanggal;
      $scope.keterangan = data.detail.keterangan;
    });
  };

  $scope.getdata();

  $scope.submitForm = function(isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/public-holiday/public-holiday.php?act=EditRecord',
        data: $.param({
          'nama_hari_libur': $scope.nama_hari_libur,
          'dari_tanggal': $scope.dari_tanggal,
          'sampai_tanggal': $scope.sampai_tanggal,
          'keterangan': $scope.keterangan,
          'id': $routeParams.id
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function(data, status) {
        if (data.res == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data Public Holiday berhasil diperbaharui <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-public-holiday';
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: data.msg + '<i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});
