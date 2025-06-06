tripApp.controller('CutiKaryawanController', function ($scope, $route, $routeParams, $http, ngToast, CommonServices) {
  // $scope.datestart = CommonServices.firstDateMonth();
  // $scope.dateend = CommonServices.lastDateMonth();
  $scope.datestart = CommonServices.currentYear().toString();
  $scope.yearlist = CommonServices.yearList();
  $scope.id_karyawan = "0";

  $scope.filterstatus = "1";
  $scope.activeMenu = '1';

  $scope.getdata = function () {
    $http.get('api/cuti-karyawan/cuti-karyawan.php?act=DataList&datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&status=' + $scope.filterstatus + '&id_karyawan=' + $scope.id_karyawan).success(function (data, status) {
      $scope.data_cuti = data.data;
      $scope.data_karyawan = data.karyawanArray;

      $scope.all = data.all;
      $scope.new = data.new;
      $scope.approved = data.approved;
      $scope.rejected = data.rejected;
    });
  };

  $scope.getdata();

  $scope.doFilter = function (a) {
    $scope.filterstatus = a;
    $scope.activeMenu = a;
    $scope.filterdata();
  }

  $scope.filterdata = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  };



  $scope.removeRow = function (val) {
    if (confirm("Anda yakin ingin menghapus data ini?")) {
      $http({
        method: "POST",
        url: 'api/cuti-karyawan/cuti-karyawan.php?act=DeleteRecord',
        data: $.param({
          'idr': val
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data cuti karyawan berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data cuti karyawan gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }
});

tripApp.controller('CutiKaryawanNewController', function ($scope, $route, $http, ngToast) {
  $scope.processing = false;
  $scope.disablecode = false;
  $scope.tanggal = $scope.datenow();
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });
  $scope.sampai_tanggal = '';
  $scope.jenis = 'CUTI TAHUNAN';
  $scope.stts_cuti = '1';
  $scope.limit = 1;
  $scope.limitTop = 1;

  $scope.getJumlahHariCuti = function (startDate2, endDate2) {
    var oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds
    var startDate = new Date(startDate2);
    var endDate = new Date(endDate2);
    var diffDays = Math.round(Math.abs((endDate.getTime() - startDate.getTime()) / (oneDay))) + 1;

    diff = endDate - startDate;
    var holidays = $scope.holidays;
    idx_holidays = 0;
    num_holidays = 0;
    while (idx_holidays < holidays.length) {
      holiday = new Date(holidays[idx_holidays]);
      holiday.setHours(0, 0, 0, 0);
      if (holiday >= startDate && holiday <= endDate) {
        num_holidays++;
      }
      idx_holidays++;
    }

    var totalSundays = 0;
    var i = startDate;
    while (i <= endDate) {
      if (i.getDay() === 0) {
        totalSundays++;
      }
      i.setDate(i.getDate() + 1);
    }

    var totalCuti = diffDays - num_holidays - totalSundays;
    return totalCuti;
  }

  $scope.getdata = function () {
    $http.get('api/cuti-karyawan/cuti-karyawan.php?act=LoadAllRequirement').success(function (data, status) {
      $scope.data_karyawan = data.karyawan;
      $scope.holidays = data.holiday;
      //$scope.jml_hari = $scope.getJumlahHariCuti();
    });
  };

  $scope.countDate = function () {
    if ($scope.dari_tanggal !== '') {
      if ($scope.sampai_tanggal !== '') {
        if ($scope.dari_tanggal === $scope.sampai_tanggal) {
          var diffDays = 1;
        } else {
          var dd = $scope.dari_tanggal.split("/");
          var fd = $scope.sampai_tanggal.split("/");
          var firstDate = new Date(dd[1] + "-" + dd[0] + "-" + dd[2]);
          var secondDate = new Date(fd[1] + "-" + fd[0] + "-" + fd[2]);
          var diffDays = $scope.getJumlahHariCuti(firstDate, secondDate);
        }
      } else {
        var diffDays = 1;
      }
      $scope.jml_hari = diffDays;
      $scope.limit = Math.floor(parseInt(diffDays) - 1);
      $scope.limitTop = diffDays;
      // $scope.sisa_cuti = parseInt($scope.sisacuti) + parseInt($scope.cutilama) - parseInt($scope.jml_hari);
    }
  }

  $scope.getdata();

  $scope.submitForm = function (isValid) {
    if (isValid) {
      if ($scope.limit >= $scope.jml_hari || $scope.limitTop < $scope.jml_hari) {
        alert("Jumlah Hari tidak boleh kurang atau sama dengan " + $scope.limit + " atau lebih dari " + $scope.limitTop + " hari");
      } else {
        $scope.processing = true;
        $http({
          method: "POST",
          url: 'api/cuti-karyawan/cuti-karyawan.php?act=NewRecord',
          data: $.param({
            'karyawan': $scope.karyawan,
            'dari_tanggal': $scope.dari_tanggal,
            'sampai_tanggal': $scope.sampai_tanggal,
            'jml_hari': $scope.jml_hari,
            'keterangan': $scope.keterangan,
            'lokasi': $scope.lokasi,
            'jenis': $scope.jenis,
            'stts_cuti': $scope.stts_cuti
          }),
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).success(function (data, status) {
          if (data.res == "1") {
            ngToast.create({
              className: 'success',
              content: 'Data cuti karyawan berhasil disimpan <i class="fa fa-remove"></i>'
            });
            window.document.location = '#/data-cuti-karyawan';
          } else {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: data.msg + '<i class="fa fa-remove"></i>'
            });
          }
        });
      }
    }
  };
});

tripApp.controller('CutiKaryawanEditController', function ($scope, $route, $routeParams, $http, ngToast) {
  $scope.processing = false;
  $scope.disablecode = true;
  $scope.hideSave = false;
  $scope.sisacuti = 0;
  $scope.cutilama = 0;
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });
  $scope.limit = 1;
  $scope.limitTop = 1;

  $scope.getJumlahHariCuti = function (startDate2, endDate2) {
    var oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds
    var startDate = new Date(startDate2);
    var endDate = new Date(endDate2);
    var diffDays = Math.round(Math.abs((endDate.getTime() - startDate.getTime()) / (oneDay))) + 1;

    diff = endDate - startDate;
    var holidays = $scope.holidays;
    console.log(holidays);
    idx_holidays = 0;
    num_holidays = 0;
    while (idx_holidays < holidays.length) {
      holiday = new Date(holidays[idx_holidays]);
      holiday.setHours(0, 0, 0, 0);
      if (holiday >= startDate && holiday <= endDate) {
        num_holidays++;
      }
      idx_holidays++;
    }

    var totalSundays = 0;
    var i = startDate;
    while (i <= endDate) {
      if (i.getDay() === 0) {
        ++totalSundays;
      }
      i.setDate(i.getDate() + 1);
    }
    var totalCuti = diffDays - num_holidays - totalSundays;
    console.log(diffDays);
    console.log(num_holidays);
    console.log(totalSundays);
    return totalCuti;
  }

  $scope.getdata = function () {
    $http.get('api/cuti-karyawan/cuti-karyawan.php?act=LoadAllRequirement').success(function (data, status) {
      $scope.data_karyawan = data.karyawan;
      $scope.holidays = data.holiday;
      // $scope.sisacuti = data.sisaCuti;
      // $scope.sisa_cuti = data.sisaCuti;
    });
  };

  $scope.getdata();

  $scope.getdata = function () {
    $http.get('api/cuti-karyawan/cuti-karyawan.php?act=Detail&id=' + $routeParams.idCuti).success(function (data, status) {
      $scope.karyawan = data.detail.karyawan;
      $scope.dari_tanggal = data.detail.dari_tanggal;
      $scope.sampai_tanggal = data.detail.sampai_tanggal;
      $scope.jml_hari = data.detail.jml_hari;
      $scope.cutilama = $scope.jml_hari;
      $scope.lokasi = data.detail.lokasi;
      $scope.keterangan = data.detail.keterangan;
      $scope.jenis = data.detail.jenis;
      $scope.stts_cuti = data.detail.stts_cuti;
      $scope.limit = Math.floor(parseInt($scope.jml_hari) - 1);
      $scope.limitTop = $scope.jml_hari;
    });
  };

  $scope.getdata();

  $scope.countDate = function () {
    if ($scope.dari_tanggal !== '') {
      if ($scope.sampai_tanggal !== '') {
        if ($scope.dari_tanggal === $scope.sampai_tanggal) {
          var diffDays = 1;
        } else {
          var fd = $scope.sampai_tanggal.split("/");
          var dd = $scope.dari_tanggal.split("/");
          var firstDate = new Date(fd[1] + "-" + fd[0] + "-" + fd[2]);
          var secondDate = new Date(dd[1] + "-" + dd[0] + "-" + dd[2]);
          var diffDays = $scope.getJumlahHariCuti(firstDate, secondDate);
        }
      } else {
        var diffDays = 1;
      }
      $scope.jml_hari = diffDays;
      $scope.limit = Math.floor(parseInt(diffDays) - 1);
      $scope.limitTop = diffDays;
      // $scope.sisa_cuti = parseInt($scope.sisacuti) + parseInt($scope.cutilama) - parseInt($scope.jml_hari);
    }
  }

  $scope.submitForm = function (isValid) {
    if (isValid) {
      if ($scope.limit >= $scope.jml_hari || $scope.limitTop < $scope.jml_hari) {
        alert("Jumlah Hari tidak boleh kurang atau sama dengan " + $scope.limit + " atau lebih dari " + $scope.limitTop + " hari");
      } else {
        $scope.processing = true;
        $http({
          method: "POST",
          url: 'api/cuti-karyawan/cuti-karyawan.php?act=EditRecord',
          data: $.param({
            'karyawan': $scope.karyawan,
            'dari_tanggal': $scope.dari_tanggal,
            'sampai_tanggal': $scope.sampai_tanggal,
            'jml_hari': $scope.jml_hari,
            'keterangan': $scope.keterangan,
            'jenis': $scope.jenis,
            'lokasi': $scope.lokasi,
            'stts_cuti': $scope.stts_cuti,
            'id': $routeParams.idCuti
          }),
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).success(function (data, status) {
          if (data.res == "1") {
            ngToast.create({
              className: 'success',
              content: 'Data cuti karyawan berhasil diperbaharui <i class="fa fa-remove"></i>'
            });
            window.document.location = '#/data-cuti-karyawan';
          } else {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: data.msg + '<i class="fa fa-remove"></i>'
            });
          }
        });
      }
    }
  };
});
