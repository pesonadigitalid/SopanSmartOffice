tripApp.controller('WorkScheduleController', function ($rootScope, $scope, $route, $http, ngToast, CommonServices) {
  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();
  $scope.tipe = "";
  $scope.status = "";
  $scope.spb = "";
  $scope.karyawan = "";

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.getdata = function () {
    $http.get('api/work-schedule/data-work-schedule.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&tipe=' + $scope.tipe + '&status=' + $scope.status + '&spb=' + $scope.spb + '&karyawan=' + $scope.karyawan).success(function (data, status) {
      $scope.data_work_schedule = data.workScheduleArray;
      $scope.data_spb = data.spbArray;
      $scope.data_karyawan = data.karyawanArray;
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

  $scope.removeRow = function (val) {
    if (confirm("Anda yakin ingin menghapus data ini?")) {
      $http({
        method: "POST",
        url: 'api/work-schedule/delete.php',
        data: $.param({
          'idr': val
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data work order berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data work order gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }

  $scope.doPrint = function (a) {
    window.open($rootScope.baseURL + 'api/print/print-work-schedule.php?id=' + a, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }

  $scope.doPrint2 = function () {
    window.open($rootScope.baseURL + 'api/print/print-data-work-schedule.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&tipe=' + $scope.tipe + '&status=' + $scope.status + '&spb=' + $scope.spb + '&karyawan=' + $scope.karyawan, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('WorkScheduleNewController', function ($scope, $route, $http, ngToast, Upload, $rootScope) {
  $scope.processing = false;
  $scope.disablecode = false;
  $scope.disablePelanggan = false;
  $scope.tanggal = $rootScope.currentDateID;
  $scope.karyawan_ids = [''];

  $scope.addTeknisi = function () {
    $scope.karyawan_ids.push('');
  }

  $scope.removeTeknisi = function (index) {
    $scope.karyawan_ids.splice(index, 1);
  }

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.getdata = function () {
    $http.get('api/work-schedule/load-all-requirement.php').success(function (data, status) {
      $scope.data_spb = data.spbArray;
      $scope.data_karyawan = data.karyawanArray;
      $scope.data_pelanggan = data.pelangganArray;
    });
  };

  $scope.getdata();

  $("#spb").on("change", function (e) {
    if (this.value != "") {
      $scope.disablePelanggan = true;
      $http.get('api/penjualan/penjualan.php?act=Detail&id=' + this.value).success(function (data, status) {
        $scope.pelanggan = data.master.IDPelanggan;
      });
    } else {
      $scope.disablePelanggan = false;
    }
  });

  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/work-schedule/new.php',
        data: $.param({
          'tipe': $scope.tipe,
          'spb': $scope.spb,
          'pelanggan': $scope.pelanggan,
          'karyawan': $scope.karyawan,
          'karyawan_ids': $scope.karyawan_ids.filter(function (id) {
            return id && id.trim() !== '';
          }).join(','),
          'tanggal': $scope.tanggal,
          'judul': $scope.judul,
          'keterangan': $scope.keterangan,
          'pic_pelanggan': $scope.pic_pelanggan,
          'jenis_unit': $scope.jenis_unit,
          'no_tangki': $scope.no_tangki,
          'no_panel_a': $scope.no_panel_a,
          'no_panel_b': $scope.no_panel_b,
          'no_panel_c': $scope.no_panel_c,
          'no_tangki_heatpump': $scope.no_tangki_heatpump,
          'no_outdoor_heatpump': $scope.no_outdoor_heatpump,
          'status': $scope.status,
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data work order berhasil disimpan <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-work-schedule';
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data work order gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('WorkScheduleEditController', function ($scope, $route, $routeParams, $http, ngToast, Upload) {
  $scope.processing = false;
  $scope.disablecode = true;
  $scope.disablePelanggan = false;
  $scope.karyawan_ids = [''];

  $scope.addTeknisi = function () {
    $scope.karyawan_ids.push('');
  }

  $scope.removeTeknisi = function (index) {
    $scope.karyawan_ids.splice(index, 1);
  }

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.getdata = function () {
    $http.get('api/work-schedule/load-all-requirement.php').success(function (data, status) {
      $scope.data_spb = data.spbArray;
      $scope.data_karyawan = data.karyawanArray;
      $scope.data_pelanggan = data.pelangganArray;
    });
  };

  $scope.getdata();

  $http.get('api/work-schedule/detail.php?id=' + $routeParams.scheduleId).success(function (data, status) {
    $scope.tipe = data.tipe;
    $scope.no_schedule = data.no_schedule;
    $scope.spb = data.spb;
    $scope.pelanggan = data.pelanggan;
    $scope.karyawan = data.karyawan;
    $scope.judul = data.judul;
    $scope.tanggal = data.tanggal;
    $scope.keterangan = data.keterangan;
    $scope.pic_pelanggan = data.pic_pelanggan;
    $scope.jenis_unit = data.jenis_unit;
    $scope.no_tangki = data.no_tangki;
    $scope.no_panel_a = data.no_panel_a;
    $scope.no_panel_b = data.no_panel_b;
    $scope.no_panel_c = data.no_panel_c;
    $scope.no_tangki_heatpump = data.no_tangki_heatpump;
    $scope.no_outdoor_heatpump = data.no_outdoor_heatpump;
    $scope.status = data.status;
    $scope.karyawan_ids = data.karyawan_ids.split(',');

    setTimeout(() => {
      $('#spb').val($scope.spb).trigger('change');
      $('#pelanggan').val($scope.pelanggan).trigger('change');
    }, 1000);

    if (data.tipe == 1) {
      $scope.disablePelanggan = true;
    }
  });

  $("#spb").on("change", function (e) {
    if (this.value != "") {
      $scope.disablePelanggan = true;
      $http.get('api/penjualan/penjualan.php?act=Detail&id=' + this.value).success(function (data, status) {
        $scope.pelanggan = data.master.IDPelanggan;
      });
    } else {
      $scope.disablePelanggan = false;
    }
  });

  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/work-schedule/edit.php',
        data: $.param({
          'tipe': $scope.tipe,
          'spb': $scope.spb,
          'pelanggan': $scope.pelanggan,
          'karyawan': $scope.karyawan,
          'karyawan_ids': $scope.karyawan_ids.filter(function (id) {
            return id && id.trim() !== '';
          }).join(','),
          'tanggal': $scope.tanggal,
          'judul': $scope.judul,
          'keterangan': $scope.keterangan,
          'pic_pelanggan': $scope.pic_pelanggan,
          'jenis_unit': $scope.jenis_unit,
          'no_tangki': $scope.no_tangki,
          'no_panel_a': $scope.no_panel_a,
          'no_panel_b': $scope.no_panel_b,
          'no_panel_c': $scope.no_panel_c,
          'no_tangki_heatpump': $scope.no_tangki_heatpump,
          'no_outdoor_heatpump': $scope.no_outdoor_heatpump,
          'status': $scope.status,
          'id': $routeParams.scheduleId
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data work order berhasil diperbaharui <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-work-schedule';
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data work order gagal diperbaharui. Silahkan coba kembali <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});
