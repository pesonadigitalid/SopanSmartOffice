tripApp.controller('TrainingRecordController', function($rootScope, $scope, $route, $http, ngToast, CommonServices) {
  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.getdata = function() {
    $http.get('api/training-record/training-record.php?act=DataList&datestart=' + $scope.datestart + '&dateend=' + $scope.dateend).success(function(data, status) {
      $scope.data_training = data;
    });
  };
  $scope.getdata();

  $scope.filtersupplier = function() {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }

  $scope.removeRow = function(val) {
    if (confirm("Anda yakin ingin menghapus data ini?")) {
      $http({
        method: "POST",
        url: 'api/training-record/training-record.php?act=DeleteRecord',
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
            content: 'Data training record berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data training record tidak dapat dihapus. Silahkan coba lagi nanti... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }

  $scope.doPrint = function(a) {
    window.open($rootScope.baseURL + 'api/print/print-supplier.php?id=' + a, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('TrainingRecordNewController', function($rootScope, $scope, $route, $routeParams, $http, ngToast, Upload) {
  $scope.processing = false;
  $scope.disablecode = true;

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.loadAllRequirement = function() {
    $http.get('api/training-record/training-record.php?act=LoadAllRequirement').success(function(data, status) {
      $scope.data_karyawan = data.karyawanArray;
    });
  };
  $scope.loadAllRequirement();

  $scope.submitForm = function(isValid) {
    if (isValid) {
      if ($scope.file_sertifikat) {
        $scope.processing = true;
        Upload.upload({
          url: 'api/training-record/training-record.php?act=AddRecord',
          data: {
            'file_sertifikat': $scope.file_sertifikat,
            'karyawan': $scope.karyawan,
            'nama_training': $scope.nama_training,
            'keterangan': $scope.keterangan,
            'tgl_mulai': $scope.tgl_mulai,
            'tgl_selesai': $scope.tgl_selesai,
            'lokasi_training': $scope.lokasi_training
          }
        }).success(function (data) {
          if (data == "1") {
            ngToast.create({
              className: 'success',
              content: 'Data training record berhasil disimpan <i class="fa fa-remove"></i>'
            });
            window.document.location = '#/data-training-record';
          } else {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Data training record gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
            });
          }
        });
      } else {
        $scope.processing = true;
        $http({
          method: "POST",
          url: 'api/training-record/training-record.php?act=AddRecord',
          data: $.param({
            'karyawan': $scope.karyawan,
            'nama_training': $scope.nama_training,
            'keterangan': $scope.keterangan,
            'tgl_mulai': $scope.tgl_mulai,
            'tgl_selesai': $scope.tgl_selesai,
            'lokasi_training': $scope.lokasi_training
          }),
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          }
        }).success(function(data, status) {
          if (data == "1") {
            ngToast.create({
              className: 'success',
              content: 'Data training record berhasil disimpan <i class="fa fa-remove"></i>'
            });
            window.document.location = '#/data-training-record';
          } else {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Data training record gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
            });
          }
        });
      }
    }
  };
});

tripApp.controller('TrainingRecordEditController', function($rootScope, $scope, $route, $routeParams, $http, ngToast, Upload) {
  $scope.processing = false;
  $scope.disablecode = true;

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $http.get('api/training-record/training-record.php?act=Detail&id=' + $routeParams.tRecordId).success(function(data, status) {
    $scope.karyawan = data.karyawan;
    $scope.nama_training = data.nama_training;
    $scope.keterangan = data.keterangan;
    $scope.tgl_mulai = data.tgl_mulai;
    $scope.tgl_selesai = data.tgl_selesai;
    $scope.lokasi_training = data.lokasi_training;
    $scope.file_sertifikat = data.file_sertifikat;
  });

  $scope.loadAllRequirement = function() {
    $http.get('api/training-record/training-record.php?act=LoadAllRequirement').success(function(data, status) {
      $scope.data_karyawan = data.karyawanArray;
    });
  };
  $scope.loadAllRequirement();

  $scope.submitForm = function(isValid) {
    if (isValid) {
      if ($scope.file_sertifikat) {
        $scope.processing = true;
        Upload.upload({
          url: 'api/training-record/training-record.php?act=EditRecord',
          data: {
            'file_sertifikat': $scope.file_sertifikat,
            'karyawan': $scope.karyawan,
            'nama_training': $scope.nama_training,
            'keterangan': $scope.keterangan,
            'tgl_mulai': $scope.tgl_mulai,
            'tgl_selesai': $scope.tgl_selesai,
            'lokasi_training': $scope.lokasi_training,
            'id': $routeParams.tRecordId
          }
        }).success(function (data) {
          if (data == "1") {
            ngToast.create({
              className: 'success',
              content: 'Data training record berhasil disimpan <i class="fa fa-remove"></i>'
            });
            window.document.location = '#/data-training-record';
          } else {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Data training record gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
            });
          }
        });
      } else {
        $scope.processing = true;
        $http({
          method: "POST",
          url: 'api/training-record/training-record.php?act=EditRecord',
          data: $.param({
            'karyawan': $scope.karyawan,
            'nama_training': $scope.nama_training,
            'keterangan': $scope.keterangan,
            'tgl_mulai': $scope.tgl_mulai,
            'tgl_selesai': $scope.tgl_selesai,
            'lokasi_training': $scope.lokasi_training,
            'id': $routeParams.tRecordId
          }),
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          }
        }).success(function(data, status) {
          if (data == "1") {
            ngToast.create({
              className: 'success',
              content: 'Data training record berhasil diperbaharui <i class="fa fa-remove"></i>'
            });
            window.document.location = '#/data-training-record';
          } else {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Data training record gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
            });
          }
        });
      }
    }
  };
});
