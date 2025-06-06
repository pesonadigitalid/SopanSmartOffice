tripApp.controller('PayrollController', function ($routeParams, $rootScope, $scope, $route, $http, ngToast, CommonServices) {
  var d = new Date();
  var mm = d.getMonth() + 1;
  var yyyy = d.getFullYear().toString();
  if (parseInt(mm) < 10) mm = '0' + mm;
  else mm = mm.toString();

  $scope.month = CommonServices.monthList();

  $scope.bulan = mm;
  $scope.tahun = yyyy;

  if (typeof $routeParams.bulan !== 'undefined' && typeof $routeParams.tahun !== 'undefined') {
    $scope.bulan = $routeParams.bulan;
    $scope.tahun = $routeParams.tahun;
  } else {
    $scope.bulan = mm;
    $scope.tahun = yyyy;
  }

  $scope.filterstatus = "0";
  $scope.activeMenu = '0';

  $scope.getdata = function () {
    $http.get('api/payroll/data-payroll.php?bulan=' + $scope.bulan + '&tahun=' + $scope.tahun + '&status=' + $scope.filterstatus).success(function (data, status) {
      $scope.data_payroll = data.data;
      $scope.new = data.new;
      $scope.approved = data.approved;
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
  }

  $scope.removeRow = function (val) {
    if (confirm("Anda yakin ingin menghapus data ini?")) {
      $http({
        method: "POST",
        url: 'api/payroll/delete.php',
        data: $.param({
          'idr': val
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data Slip Gaji berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $scope.filterdata();
        } else {
          ngToast.create({
            className: 'success',
            content: 'Data Slip Gaji gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }

  $scope.paid = function (val) {
    if (confirm("Anda yakin Slip Gaji ini telah terbayarkan?")) {
      $http({
        method: "POST",
        url: 'api/payroll/paid.php',
        data: $.param({
          'idr': val
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data Slip Gaji berhasil diperbaharui <i class="fa fa-remove"></i>'
          });
          $scope.filterdata();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data Slip Gaji gagal diperbahuri. Silahkan coba kembali nanti... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }

  $scope.doPrint = function (a) {
    window.open($rootScope.baseURL + 'api/print/print-slip-gaji.php?id=' + a, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('PayrollNewController', function ($routeParams, $scope, $route, $routeParams, $http, ngToast, CommonServices) {
  $scope.processing = false;
  $scope.disablecode = false;
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true
  });

  $scope.setDefault = function () {
    $scope.date = new Date();
    $scope.statusUser = "0";
    $scope.total_absen = 0;
    $scope.bulan = "";
    $scope.tahun = "";
    $scope.karyawan = "";
    $scope.tanggal = $scope.datenow();
    $scope.setDefaultTotal();
  }

  $scope.setDefaultTotal = function () {
    $scope.gaji_pokok = 0;
    $scope.total_uang_makan = 0;
    $scope.total_uang_transport = 0;
    $scope.uang_pulsa = 0;
    $scope.tunjangan_performance = 0;
    $scope.tunjangan_khusus = 0;
    $scope.tunjangan_luar_kota = 0;
    $scope.potongan_kasbon = 0;
    $scope.potongan_cuti = 0;
    $scope.potongan_alpha = 0;
    $scope.potongan_pinjaman = 0;
    $scope.potongan_lain = 0;
    $scope.potongan_jamsostek = 0;
    $scope.uang_lembur = 0;
    $scope.uang_makan_lembur = 0;
  }

  $scope.setDefault();

  var param = $routeParams.param.split("-");
  $scope.bulan = param[0];
  $scope.tahun = param[1];
  $http.get('api/payroll/load-all-requirement.php?bulan=' + $scope.bulan + '&tahun=' + $scope.tahun).success(function (data, status) {
    $scope.data_karyawan = data.karyawan;
    $scope.total_hari = data.totalHariKerjaBulan;
    $scope.bulan_name = data.bulan;
  });

  $scope.changeKaryawan = function () {
    var i = $scope.karyawan;
    $scope.setDefaultTotal();
    $scope.karyawan_selected = $scope.data_karyawan[i].IDKaryawan;
    $scope.gaji_pokok = parseFloat($scope.data_karyawan[i].GajiPokok);
    $scope.uang_makan_perhari = parseFloat($scope.data_karyawan[i].UangMakan);
    $scope.total_uang_makan = parseFloat($scope.uang_makan_perhari) * parseFloat($scope.total_absen);
    $scope.uang_transport_perhari = parseFloat($scope.data_karyawan[i].UangTransport);
    $scope.total_uang_transport = parseFloat($scope.uang_transport_perhari) * parseFloat($scope.total_absen);
    $scope.uang_pulsa = parseFloat($scope.data_karyawan[i].UangPulsa);
    $scope.tunjangan_performance = parseFloat($scope.data_karyawan[i].UangPerformance);
    $scope.tunjangan_khusus = parseFloat($scope.data_karyawan[i].LainLain);
    $scope.isSM = parseFloat($scope.data_karyawan[i].isSM);

    $http.get('api/payroll/get-total-cuti-karyawan.php?bulan=' + $scope.bulan + '&tahun=' + $scope.tahun + '&uang_makan_perhari=' + $scope.uang_makan_perhari + '&karyawan=' + $scope.karyawan_selected).success(function (data, status) {
      $scope.total_cuti_minus = data.Minus;
      $scope.total_absen = data.TotalHariKerja;
      $scope.total_lembur_normal = parseFloat(data.TotalJamLemburNormal) * 1.5;
      $scope.total_lembur_holiday = parseFloat(data.TotalJamLemburHoliday) * 2;
      $scope.total_alpha = parseFloat(data.TotalAlpha);
      $scope.total_lembur_hari = parseFloat(data.TotalLemburHari);
      $scope.uang_makan_lembur = parseFloat(data.TotalUangMakanLembur);

      $scope.summary();
    });
  }

  $scope.summary = function () {

    var gajiPerJam = $scope.gaji_pokok / 173;

    //$scope.uang_makan_lembur = $scope.total_lembur_hari * $scope.uang_makan_perhari;

    if ($scope.uang_lembur === 0) {
      if ($scope.isSM === 0) {
        var uangLembur = ($scope.total_lembur_normal * gajiPerJam) + ($scope.total_lembur_holiday * gajiPerJam);
        $scope.uang_lembur = Math.ceil(uangLembur / 100) * 100;
      } else {
        $scope.uang_lembur = 0;
      }
    }

    if ($scope.potongan_alpha === 0) {
      var potonganAlpha = parseFloat($scope.gaji_pokok) / 25 * parseFloat($scope.total_alpha);
      $scope.potongan_alpha = Math.ceil(potonganAlpha / 100) * 100;
    }

    $scope.total_uang_makan = parseFloat($scope.uang_makan_perhari) * parseFloat($scope.total_absen);
    $scope.total_uang_transport = parseFloat($scope.uang_transport_perhari) * parseFloat($scope.total_absen);

    if ($scope.potongan_cuti === 0) {
      var potonganCuti = parseFloat($scope.gaji_pokok) / 25 * parseFloat($scope.total_cuti_minus);
      $scope.potongan_cuti = Math.ceil(potonganCuti / 100) * 100;
    }

    $scope.total1 = parseFloat($scope.gaji_pokok) + parseFloat($scope.total_uang_makan) + parseFloat($scope.total_uang_transport) + parseFloat($scope.uang_pulsa) + parseFloat($scope.tunjangan_performance) + parseFloat($scope.tunjangan_khusus) + parseFloat($scope.tunjangan_luar_kota) + parseFloat($scope.uang_lembur) + parseFloat($scope.uang_makan_lembur);

    $scope.total_potongan = parseFloat($scope.potongan_cuti) + parseFloat($scope.potongan_pinjaman) + parseFloat($scope.potongan_kasbon) + parseFloat($scope.potongan_lain) + parseFloat($scope.potongan_jamsostek) + parseFloat($scope.potongan_alpha);

    $scope.total_gaji = parseFloat($scope.total1) - parseFloat($scope.total_potongan);
  }

  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/payroll/new.php',
        data: $.param({
          'idkaryawan': $scope.karyawan_selected,
          'bulan': $scope.bulan,
          'tahun': $scope.tahun,
          'total_absen': $scope.total_absen,
          'gaji_pokok': $scope.gaji_pokok,
          'total_uang_makan': $scope.total_uang_makan,
          'total_uang_transport': $scope.total_uang_transport,
          'uang_pulsa': $scope.uang_pulsa,
          'tunjangan_performance': $scope.tunjangan_performance,
          'tunjangan_khusus': $scope.tunjangan_khusus,
          'tunjangan_luar_kota': $scope.tunjangan_luar_kota,
          'potongan_cuti': $scope.potongan_cuti,
          'potongan_pinjaman': $scope.potongan_pinjaman,
          'potongan_kasbon': $scope.potongan_kasbon,
          'potongan_lain': $scope.potongan_lain,
          'potongan_jamsostek': $scope.potongan_jamsostek,
          'total_gaji': $scope.total_gaji,
          'total_cuti_minus': $scope.total_cuti_minus,
          'total1': $scope.total1,
          'total_potongan': $scope.total_potongan,
          'uang_makan_perhari': $scope.uang_makan_perhari,
          'uang_transport_perhari': $scope.uang_transport_perhari,
          'keterangan': $scope.keterangan,
          'uang_lembur': $scope.uang_lembur,
          'uang_makan_lembur': $scope.uang_makan_lembur,
          'total_alpha': $scope.total_alpha,
          'potongan_alpha': $scope.potongan_alpha
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data payroll berhasil ditambahkan <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-payroll/' + $scope.bulan + '/' + $scope.tahun;
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data payroll gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('PayrollEditController', function ($rootScope, $scope, $route, $routeParams, $http, ngToast) {
  $scope.processing = false;
  $scope.disablecode = true;
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true
  });

  $http.get('api/payroll/detail.php?id=' + $routeParams.karyawanId).success(function (data, status) {
    $scope.no_slip = data.no_slip;
    $scope.tanggal = data.tanggal;
    $scope.bulan = data.bulan;
    $scope.tahun = data.tahun;
    $scope.total_absen = data.total_absen;
    $scope.nik = data.nik;
    $scope.nama = data.nama;
    $scope.gaji_pokok = data.gaji_pokok;
    $scope.total_uang_makan = data.total_uang_makan;
    $scope.total_uang_transport = data.total_uang_transport;
    $scope.uang_pulsa = data.uang_pulsa;
    $scope.tunjangan_performance = data.tunjangan_performance;
    $scope.total_gaji = data.total_gaji;
    $scope.uang_makan_perhari = data.uang_makan_perhari;
    $scope.uang_transport_perhari = data.uang_transport_perhari;
  });

  $scope.setDefault = function () {
    $scope.date = new Date();
    $scope.statusUser = "0";
    $scope.total_absen = 28;
    $scope.bulan = "";
    $scope.tahun = "";
    $scope.tanggal = $scope.datenow();
    $scope.gaji_pokok = 0;
    $scope.total_uang_makan = 0;
    $scope.total_uang_transport = 0;
    $scope.uang_pulsa = 0;
    $scope.tunjangan_performance = 0;
    $scope.potongan = 0;
    $scope.potongan_lain2 = 0;
  }

  $scope.setDefault();

  $scope.idkaryawan = $routeParams.karyawanId;

  $http.get('api/karyawan/data-karyawan.php?idk=' + $routeParams.karyawanId).success(function (data, status) {
    $scope.nik = data[0]['NIK'];
    $scope.nama = data[0]['Nama'];
  });

  $http.get('api/payroll/get-gaji-karyawan.php?id=' + $routeParams.karyawanId).success(function (data, status) {
    $scope.gaji_pokok = data.gaji_pokok;
    $scope.uang_makan_perhari = data.uang_makan;
    $scope.total_uang_makan = $scope.uang_makan_perhari * $scope.total_absen;
    $scope.uang_transport_perhari = data.uang_transport;
    $scope.total_uang_transport = $scope.uang_transport_perhari * $scope.total_absen;
    $scope.sumarygaji();
  });

  $scope.sumarygaji = function () {
    $scope.total_gaji = (parseFloat($scope.gaji_pokok)) + (parseFloat($scope.total_uang_makan) + parseFloat($scope.total_uang_transport) + parseFloat($scope.uang_pulsa) + parseFloat($scope.tunjangan_performance)) - (parseFloat($scope.potongan) + parseFloat($scope.potongan_lain2));
  }

  $scope.sumarygaji();

  $scope.summary = function () {
    $scope.sumarygaji();
  }

  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/payroll/edit.php',
        data: $.param({
          'idkaryawan': $scope.idkaryawan,
          'tanggal': $scope.tanggal,
          'bulan': $scope.bulan,
          'tahun': $scope.tahun,
          'total_absen': $scope.total_absen,
          'nik': $scope.nik,
          'nama': $scope.nama,
          'gaji_pokok': $scope.gaji_pokok,
          'total_uang_makan': $scope.total_uang_makan,
          'total_uang_transport': $scope.total_uang_transport,
          'uang_pulsa': $scope.uang_pulsa,
          'tunjangan_performance': $scope.tunjangan_performance,
          'potongan': $scope.potongan,
          'potongan_lain2': $scope.potongan_lain2,
          'total_gaji': $scope.total_gaji,
          'uang_makan_perhari': $scope.uang_makan_perhari,
          'uang_transport_perhari': $scope.uang_transport_perhari,
          'uploaded': $scope.userLoginID,
          'id': $routeParams.karyawanId
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data payroll berhasil diperbaharui <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-payroll';
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data payroll gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});
