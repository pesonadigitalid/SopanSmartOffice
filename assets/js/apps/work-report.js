tripApp.controller('WorkReportController', function ($rootScope, $scope, $route, $http, $routeParams, ngToast, CommonServices) {
  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();
  $scope.work_schedule = "";

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  if (typeof $routeParams.scheduleId != 'undefined') {
    $scope.work_schedule = $routeParams.scheduleId;
  }

  $scope.getdata = function () {
    $http.get('api/work-report/data-work-report.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&work_schedule=' + $scope.work_schedule).success(function (data, status) {
      $scope.data_work_report = data.workReportArray;
      $scope.data_work_schedule = data.workScheduleArray;
    });
  };

  $scope.getdata();

  $scope.filterData = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }

  $scope.removeRow = function (val) {
    if (confirm("Anda yakin ingin menghapus data ini?")) {
      $http({
        method: "POST",
        url: 'api/work-report/delete.php',
        data: $.param({
          'idr': val
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data work report berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data work report gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }

  // $scope.doPrint = function () {
  //   window.open($rootScope.baseURL + 'api/print/print-asset.php?id_karyawan=' + $scope.id_karyawan + '&kategori=' + $scope.kategori+ '&status=' + $scope.status, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  // }
});

tripApp.controller('WorkReportNewController', function ($scope, $route, $http, $routeParams, ngToast, Upload, $rootScope) {
  $scope.processing = false;
  $scope.disablecode = false;
  $scope.files = [];
  $scope.fileIds = [];
  $scope.foto = [];
  $scope.category_file = [];
  $scope.tanggal = $rootScope.currentDateID;
  let number = 0;

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.getdata = function () {
    $http.get('api/work-report/load-all-requirement.php').success(function (data, status) {
      $scope.data_work_schedule = data.workScheduleArray;
      $scope.data_file_category = data.workReportKategoriArray;
    });
  };

  $scope.getdata();

  $scope.addItem = function () {
    $scope.files.push(number)
    $scope.fileIds.push(null)
    number++;
  }

  $scope.deleteItem = function (index) {
    $scope.files.splice(index, 1);
    $scope.fileIds.splice(index, 1);
    $scope.category_file.splice(index, 1);
    $scope.fotoName.splice(index, 1);
  }

  if (typeof $routeParams.scheduleId != 'undefined') {
    $scope.work_schedule = $routeParams.scheduleId;
    setTimeout(() => {
      $('#work_schedule').val($routeParams.scheduleId).trigger('change');
    }, 1000);
  }

  $scope.submitForm = function (isValid) {
    if (isValid) {
      if ($scope.foto != null) {
        $scope.processing = true;
        Upload.upload({
          url: 'api/work-report/new.php',
          data: {
            'work_schedule': $scope.work_schedule,
            'tanggal': $scope.tanggal,
            'keterangan': $scope.keterangan,
            'foto_array': $scope.foto,
            'category_file_array': $scope.category_file,
            'is_completed': $scope.isCompleted,
          }
        }).then(function (resp) {
          var data = resp.data;
          if (data == "1") {
            ngToast.create({
              className: 'success',
              content: 'Data work report berhasil disimpan <i class="fa fa-remove"></i>'
            });

            window.document.location = (typeof $routeParams.scheduleId != 'undefined')
              ? '#/data-work-report/' + $routeParams.scheduleId
              : '#/data-work-report';
          } else {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Data work report gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
            });
          }
        });
      } else {
        $scope.processing = true;
        $http({
          method: "POST",
          url: 'api/work-report/new.php',
          data: $.param({
            'work_schedule': $scope.work_schedule,
            'tanggal': $scope.tanggal,
            'keterangan': $scope.keterangan,
            'is_completed': $scope.isCompleted,
          }),
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).success(function (data, status) {
          if (data == "1") {
            ngToast.create({
              className: 'success',
              content: 'Data work report berhasil disimpan <i class="fa fa-remove"></i>'
            });
            window.document.location = '#/data-work-report';
          } else {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Data work report gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
            });
          }
        });
      }
    }
  };
});

tripApp.controller('WorkReportEditController', function ($scope, $route, $routeParams, $http, ngToast, Upload) {
  $scope.processing = false;
  $scope.disablecode = true;
  $scope.files = [];
  $scope.fileIds = [];
  $scope.foto = [];
  $scope.fotoName = [];
  $scope.category_file = [];
  let number = 0;

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.getdata = function () {
    $http.get('api/work-report/load-all-requirement.php').success(function (data, status) {
      $scope.data_work_schedule = data.workScheduleArray;
      $scope.data_file_category = data.workReportKategoriArray;
    });
  };

  $scope.getdata();

  $http.get('api/work-report/detail.php?id=' + $routeParams.reportId).success(function (data, status) {
    $scope.no_report = data.no_report;
    $scope.work_schedule = data.work_schedule;
    $scope.tanggal = data.tanggal;
    $scope.keterangan = data.keterangan;
    $scope.isCompleted = data.is_completed;

    setTimeout(() => {
      $('#work_schedule').val($scope.work_schedule).trigger('change');
    }, 1000);

    if (data.files != null) {
      data.files.forEach((value, index) => {
        $scope.files.push(number)
        $scope.fileIds.push(parseInt(value.IDWorkReportFile))
        $scope.category_file.push(value.IDFileWorkReportCategory)
        $scope.fotoName.push(value.File)
        number++;
      });
    }
  });

  $scope.addItem = function () {
    $scope.files.push(number)
    $scope.fileIds.push(null)
    number++;
  }

  $scope.deleteItem = function (index) {
    $scope.files.splice(index, 1);
    $scope.fileIds.splice(index, 1);
    $scope.category_file.splice(index, 1);
    $scope.fotoName.splice(index, 1);
  }

  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/work-report/edit.php',
        data: {
          'work_schedule': $scope.work_schedule,
          'tanggal': $scope.tanggal,
          'keterangan': $scope.keterangan,
          'file_id_array': $scope.fileIds,
          'foto_array': $scope.foto,
          'old_foto_array': $scope.fotoName,
          'category_file_array': $scope.category_file,
          'is_completed': $scope.isCompleted,
          'id': $routeParams.reportId
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data work report berhasil diperbaharui <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-work-report';
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data work report gagal diperbaharui. Silahkan coba kembali <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});
