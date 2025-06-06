tripApp.controller('HistoryTrackingController', function($scope, $route, $http, ngToast, CommonServices) {
  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();
  $scope.serial_number = "";

  $('.datepick').datepicker({
      format: 'dd/mm/yyyy',
      showOtherMonths: true,
      selectOtherMonths: true,
      autoclose: true
  });

  $scope.getdata = function() {
    $http.get('api/history-tracking/history-tracking.php?act=DataList&datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&serial_number=' + $scope.serial_number).success(function(data, status) {
        $scope.data_tracking = data;
    });
  };
  $scope.getdata();

  $scope.refreshData = function() {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }

  $scope.removeRow = function(val) {
    if (confirm("Anda yakin ingin menghapus data ini?")) {
      $http({
        method: "POST",
        url: 'api/history-tracking/history-tracking.php?act=DeleteRecord',
        data: $.param({
          'idr': val
        }),
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).success(function(data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data history tracking berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data history tracking gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }
});

tripApp.controller('HistoryTrackingNewController', function($rootScope, $scope, $route, $routeParams, $http, ngToast) {
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
        url: 'api/history-tracking/history-tracking.php?act=NewRecord',
        data: $.param({
          'tgl_perbaikan': $scope.tgl_perbaikan,
          'serial_number': $scope.serial_number,
          'detail_perbaikan': $scope.detail_perbaikan
        }),
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).success(function(data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data history tracking berhasil disimpan <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-history-tracking';
        } else if (data == "2") {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Serial Number tidak terdaftar pada data Surat Jalan <i class="fa fa-remove"></i>'
          });
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data history tracking gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('HistoryTrackingEditController', function($rootScope, $scope, $route, $routeParams, $http, ngToast) {
  $scope.processing = false;
  $scope.disablecode = true;

  $('.datepick').datepicker({
      format: 'dd/mm/yyyy',
      showOtherMonths: true,
      selectOtherMonths: true,
      autoclose: true
  });

  $scope.getAllData = function() {
    $http.get('api/history-tracking/history-tracking.php?act=Detail&id=' + $routeParams.historyId).success(function(data, status) {
      $scope.tgl_perbaikan = data.tgl_perbaikan;
      $scope.serial_number = data.serial_number;
      $scope.detail_perbaikan = data.detail_perbaikan;
    });
  };

  $scope.getAllData();

  $scope.submitForm = function(isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/history-tracking/history-tracking.php?act=EditRecord',
        data: $.param({
          'tgl_perbaikan': $scope.tgl_perbaikan,
          'serial_number': $scope.serial_number,
          'detail_perbaikan': $scope.detail_perbaikan,
          'id': $routeParams.historyId
        }),
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).success(function(data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data history tracking berhasil diperbaharui <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-history-tracking';
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data history tracking gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});