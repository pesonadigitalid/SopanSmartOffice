tripApp.controller('LaporanDataProyekController', function ($scope, $route, $http, ngToast) {
  $scope.tahun = "";

  $scope.getdata = function () {
    $http.get('api/proyek/data-proyek.php?tahun=' + $scope.tahun).success(function (data, status) {
      $scope.data_proyek = data;
    });
  };

  $scope.getdata();

  $scope.filterdata = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }
});

tripApp.controller('LaporanOrderBarangController', function ($scope, $route, $http, ngToast) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true
  });
  $scope.datestart = "";
  $scope.dateend = "";
  $scope.kode_proyek = "";

  $scope.getdata = function () {
    $http.get('api/purchase-order/data-purchase-order.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&kode_proyek=' + $scope.kode_proyek).success(function (data, status) {
      $scope.data_purchase = data;
    });
  };

  $scope.getdata();

  $scope.getdataproyek = function () {
    $http.get('api/proyek/data-proyek.php').success(function (data, status) {
      $scope.data_proyek = data;
    });
  };

  $scope.getdataproyek();

  $scope.filterdata = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }
});

tripApp.controller('LaporanPembelianController', function ($scope, $route, $http, ngToast) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true
  });
  $scope.datestart = "";
  $scope.dateend = "";
  $scope.kode_proyek = "";

  $scope.getdata = function () {
    $http.get('api/pembelian/data-pembelian.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&kode_proyek=' + $scope.kode_proyek).success(function (data, status) {
      $scope.data_pembelian = data;
    });
  };

  $scope.getdata();

  $scope.getdataproyek = function () {
    $http.get('api/proyek/data-proyek.php').success(function (data, status) {
      $scope.data_proyek = data;
    });
  };

  $scope.getdataproyek();

  $scope.refreshData = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }
});

tripApp.controller('LaporanPengirimanController', function ($scope, $route, $http, ngToast) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true
  });
  $scope.datestart = "";
  $scope.dateend = "";
  $scope.kode_proyek = "";

  $scope.getdata = function () {
    $http.get('api/pengiriman-barang/data-pengiriman-barang.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&kode_proyek=' + $scope.kode_proyek).success(function (data, status) {
      $scope.data_pengiriman = data;
    });
  };

  $scope.getdata();

  $scope.getdataproyek = function () {
    $http.get('api/proyek/data-proyek.php').success(function (data, status) {
      $scope.data_proyek = data;
    });
  };

  $scope.getdataproyek();

  $scope.refreshData = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }
});

tripApp.controller('LaporanCutiKaryawanController', function ($scope, $route, $routeParams, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true
  });

  var d = new Date();
  var mm = d.getMonth() + 1;
  var yyyy = d.getFullYear().toString();
  if (parseInt(mm) < 10) mm = '0' + mm.toString();

  if (typeof $routeParams.tahun !== 'undefined' && typeof $routeParams.karyawan !== 'undefined') {
    $scope.tahun = $routeParams.tahun;
    $scope.karyawan = $routeParams.karyawan;
  } else {
    $scope.tahun = yyyy;
    $scope.karyawan = "";
  }

  $scope.tahunList = CommonServices.yearList();

  $scope.getdata = function () {
    $http.get('api/laporan/laporan-cuti-karyawan.php?tahun=' + $scope.tahun + '&karyawan=' + $scope.karyawan).success(function (data, status) {
      $scope.data_laporan_cuti = data.data;
      $scope.data_karyawan = data.karyawanArray;
    });
  };

  $scope.getdata();

  $scope.refreshData = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }
});

tripApp.controller('LaporanDataAbsentController', function ($scope, $route, $routeParams, $http, ngToast, CommonServices, Upload) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true
  });

  $scope.dept = "LD";

  var d = new Date();
  var mm = d.getMonth() + 1;
  var yyyy = d.getFullYear().toString();
  if (parseInt(mm) < 10) mm = '0' + mm.toString();

  if (typeof $routeParams.bulan !== 'undefined' && typeof $routeParams.tahun !== 'undefined' && typeof $routeParams.karyawan !== 'undefined') {
    $scope.bulan = $routeParams.bulan;
    $scope.tahun = $routeParams.tahun;
    $scope.karyawan = $routeParams.karyawan;
  } else {
    $scope.bulan = mm.toString();
    $scope.tahun = yyyy;
    $scope.karyawan = "";
  }



  $scope.bulanList = CommonServices.monthList();
  $scope.tahunList = CommonServices.yearList();


  $scope.getdata = function () {
    $http.get('api/laporan/laporan-data-absent.php?tahun=' + $scope.tahun + '&bulan=' + $scope.bulan + '&karyawan=' + $scope.karyawan).success(function (data, status) {
      $scope.data_laporan_cuti = data.data;
      $scope.data_karyawan = data.karyawanArray;
      $scope.totalCutiTahunan = data.totalCutiTahunan;
      $scope.totalCutiSakit = data.totalCutiSakit;
      $scope.totalCutiSpecial = data.totalCutiSpecial;
      $scope.totalCutiAlpha = data.totalCutiAlpha;
      $scope.totalCutiTugasKeluar = data.totalCutiTugasKeluar;
      $scope.totalJamKerja = data.totalJamKerja;
      $scope.totalJamLembur = data.totalJamLembur;
      $scope.totalHariKerja = data.totalHariKerja;
      $scope.totalTerlambat = data.totalTerlambat;
    });
  };

  $scope.getdata();

  $scope.refreshData = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }

  $scope.showModal = function () {
    $('#myModal').modal('show');
    $('#myModal').children('.modal-dialog').removeClass('modal-lg');
  };

  $scope.importAbsent = function () {
    if ($scope.file) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/karyawan/import-absent-fp.php',
        data: {
          'file': $scope.file,
          'dept': $scope.dept
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data.res == "1") {
          ngToast.create({
            className: 'success',
            content: data.msg + ' data absen baru berhasil di import <i class="fa fa-remove"></i>'
          });
          $scope.processing = false;
          $('#myModal').modal('hide');
          $scope.getdata();
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data Absent gagal import. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    } else {
      alert("Silahkan pilih file absen yang ingin di import");
    }
  }
});

tripApp.controller('LaporanDataAbsentHarianProyekController', function ($scope, $route, $routeParams, $http, ngToast, CommonServices, Upload) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true
  });

  $scope.proyek = "0";

  var d = new Date();
  var mm = d.getMonth() + 1;
  var yyyy = d.getFullYear().toString();
  if (parseInt(mm) < 10) mm = '0' + mm.toString();

  if (typeof $routeParams.bulan !== 'undefined' && typeof $routeParams.tahun !== 'undefined' && typeof $routeParams.karyawan !== 'undefined') {
    $scope.bulan = $routeParams.bulan;
    $scope.tahun = $routeParams.tahun;
    $scope.karyawan = $routeParams.karyawan;
  } else {
    $scope.bulan = mm.toString();
    $scope.tahun = yyyy;
    $scope.karyawan = "";
  }

  $scope.bulanList = CommonServices.monthList();
  $scope.tahunList = CommonServices.yearList();
  $scope.kode_proyek = "";

  $scope.getdata = function () {
    $http.get('api/laporan/laporan-data-absent-harian-proyek.php?tahun=' + $scope.tahun + '&bulan=' + $scope.bulan + '&karyawan=' + $scope.karyawan).success(function (data, status) {
      $scope.data_laporan_cuti = data.data;
      $scope.data_karyawan = data.karyawanArray;
      $scope.data_karyawan_original = data.karyawanArray;
      $scope.data_proyek = data.proyekArray;
      $scope.totalCutiTahunan = data.totalCutiTahunan;
      $scope.totalCutiSakit = data.totalCutiSakit;
      $scope.totalCutiSpecial = data.totalCutiSpecial;
      $scope.totalCutiAlpha = data.totalCutiAlpha;
      $scope.totalCutiTugasKeluar = data.totalCutiTugasKeluar;
      $scope.totalJamKerja = data.totalJamKerja;
      $scope.totalJamLembur = data.totalJamLembur;
      $scope.totalHariKerja = data.totalHariKerja;
      $scope.totalTerlambat = data.totalTerlambat;
    });
  };

  $scope.filterKaryawan = function () {
    // console.log($scope.kode_proyek);
    if (parseInt($scope.kode_proyek) === 0) {
      $scope.data_karyawan = $scope.data_karyawan_original.filter((x) => x.IDProyek === $scope.kode_proyek || x.IDProyek === '' || x.IDProyek === null);
    } else if (parseInt($scope.kode_proyek) > 0) {
      $scope.data_karyawan = $scope.data_karyawan_original.filter((x) => x.IDProyek === $scope.kode_proyek);
    } else {
      $scope.data_karyawan = $scope.data_karyawan_original;
    }
  }

  $scope.getdata();

  $scope.refreshData = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }

  $scope.showModal = function () {
    $('#myModal').modal('show');
    $('#myModal').children('.modal-dialog').removeClass('modal-lg');
  };

  $scope.importAbsent = function () {
    if ($scope.file) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/karyawan/import-absent-fp-proyek.php',
        data: {
          'file': $scope.file,
          'proyek': $scope.proyek
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data.res == "1") {
          ngToast.create({
            className: 'success',
            content: data.msg + ' data absen baru berhasil di import <i class="fa fa-remove"></i>'
          });
          $scope.processing = false;
          $('#myModal').modal('hide');
          $scope.getdata();
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data Absent gagal import. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    } else {
      alert("Silahkan pilih file absen yang ingin di import");
    }
  }
});

tripApp.controller('UpdateDataAbsentController', function ($scope, $route, $http, ngToast, CommonServices, Upload, $routeParams) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true
  });

  var d = new Date();
  var mm = d.getMonth() + 1;
  var yyyy = d.getFullYear().toString();
  if (parseInt(mm) < 10) mm = '0' + mm.toString();


  $param = $routeParams.param.split("-");
  $scope.bulan = $param[0];
  $scope.tahun = $param[1];
  $scope.karyawan = $param[2];

  $scope.bulanList = CommonServices.monthList();
  $scope.tahunList = CommonServices.yearList();

  $scope.getdata = function () {
    $http.get('api/laporan/laporan-data-absent.php?tahun=' + $scope.tahun + '&bulan=' + $scope.bulan + '&karyawan=' + $scope.karyawan).success(function (data, status) {
      $scope.data_laporan_cuti = data.data;
      $scope.data_karyawan = data.karyawanArray;
    });
  };

  $scope.getdata();

  $scope.refreshData = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }

  $scope.showModal = function () {
    $('#myModal').modal('show');
    $('#myModal').children('.modal-dialog').removeClass('modal-lg');
  };

  $scope.someModel = "";

  $scope.updateData = function (a, b, c) {
    var val = $('#' + a + b).val();
    var cat = a;
    var tanggal = c;

    $http({
      method: "POST",
      url: 'api/karyawan/update-absent.php',
      data: $.param({
        'cat': cat,
        'val': val,
        'tanggal': tanggal,
        'karyawan': $scope.karyawan
      }),
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).success(function (data, status) {
      if (data == "1") {

      } else {
        $scope.processing = false;
        ngToast.create({
          className: 'danger',
          content: 'Data absen gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
        });
      }
    });
  }

  $scope.importAbsent = function () {
    if ($scope.file) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/karyawan/import-absent-fp.php',
        data: {
          'file': $scope.file
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data.res == "1") {
          ngToast.create({
            className: 'success',
            content: data.msg + ' data absen baru berhasil di import <i class="fa fa-remove"></i>'
          });
          $scope.processing = false;
          $('#myModal').modal('hide');
          $scope.getdata();
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data Absent gagal import. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    } else {
      alert("Silahkan pilih file absen yang ingin di import");
    }
  }
});

tripApp.controller('UpdateDataAbsentHarianProyekController', function ($scope, $route, $http, ngToast, CommonServices, Upload, $routeParams) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true
  });

  $scope.periode = false;

  var d = new Date();
  var mm = d.getMonth() + 1;
  var yyyy = d.getFullYear().toString();
  if (parseInt(mm) < 10) mm = '0' + mm.toString();


  $param = $routeParams.param.split("-");
  $scope.bulan = $param[0];
  $scope.tahun = $param[1];
  $scope.karyawan = $param[2];

  $scope.bulanList = CommonServices.monthList();
  $scope.tahunList = CommonServices.yearList();

  $scope.statusKaryawan = '';
  $scope.statusLainnya = '';

  $scope.getdata = function () {
    $http.get('api/laporan/laporan-data-absent-harian-proyek.php?tahun=' + $scope.tahun + '&bulan=' + $scope.bulan + '&karyawan=' + $scope.karyawan).success(function (data, status) {
      $scope.data_laporan_cuti = data.data;
      $scope.data_karyawan = data.karyawanArray;
      $scope.data_proyek = data.proyekArray;
      $scope.statusKaryawan = data.statusKaryawan;
      $scope.statusLainnya = data.statusLainnya;
    });
  };

  $scope.getdata();

  $scope.refreshData = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }

  $scope.showModal = function () {
    $('#myModal').modal('show');
    $('#myModal').children('.modal-dialog').removeClass('modal-lg');
  };

  $scope.someModel = "";
  $scope.proyek = "";

  $scope.updateData = function (a, b, c) {
    var id_proyek = $('#id_proyek' + b).val();
    var val = $('#' + a + b).val();
    var cat = a;
    var tanggal = c;

    if (a === 'hitunggapok') {
      val = ($scope.data_laporan_cuti[b].HitungGapok) ? 1 : 0;
    }

    $http({
      method: "POST",
      url: 'api/karyawan/update-absent.php',
      data: $.param({
        'cat': cat,
        'val': val,
        'tanggal': tanggal,
        'karyawan': $scope.karyawan,
        'proyek': 1,
        'id_proyek': id_proyek,
      }),
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).success(function (data, status) {
      if (data == "1") {

      } else {
        $scope.processing = false;
        ngToast.create({
          className: 'danger',
          content: 'Data absen gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
        });
      }
    });
  }

  $scope.importAbsent = function () {
    if ($scope.file) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/karyawan/import-absent-fp.php',
        data: {
          'file': $scope.file
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data.res == "1") {
          ngToast.create({
            className: 'success',
            content: data.msg + ' data absen baru berhasil di import <i class="fa fa-remove"></i>'
          });
          $scope.processing = false;
          $('#myModal').modal('hide');
          $scope.getdata();
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data Absent gagal import. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    } else {
      alert("Silahkan pilih file absen yang ingin di import");
    }
  }
});

tripApp.controller('UpdateDataAbsentHarianProyekPeriodeController', function ($scope, $route, $http, ngToast, CommonServices, Upload, $routeParams) {

  $scope.periode = true;

  $scope.bulan = $routeParams.start.split("-").join("/");
  $scope.tahun = $routeParams.end.split("-").join("/");
  $scope.karyawan = $routeParams.karyawan;

  $scope.getdata = function () {
    $http.get('api/laporan/laporan-data-absent-harian-proyek-periode.php?start=' + $scope.bulan + '&end=' + $scope.tahun + '&karyawan=' + $scope.karyawan).success(function (data, status) {
      $scope.data_laporan_cuti = data.data;
      $scope.data_karyawan = data.karyawanArray;
      $scope.data_proyek = data.proyekArray;
    });
  };

  $scope.getdata();

  $scope.refreshData = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }

  $scope.showModal = function () {
    $('#myModal').modal('show');
    $('#myModal').children('.modal-dialog').removeClass('modal-lg');
  };

  $scope.someModel = "";
  $scope.proyek = "";

  $scope.updateData = function (a, b, c) {
    var id_proyek = $('#id_proyek' + b).val();
    var val = $('#' + a + b).val();
    var cat = a;
    var tanggal = c;

    $http({
      method: "POST",
      url: 'api/karyawan/update-absent.php',
      data: $.param({
        'cat': cat,
        'val': val,
        'tanggal': tanggal,
        'karyawan': $scope.karyawan,
        'proyek': 1,
        'id_proyek': id_proyek,
      }),
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).success(function (data, status) {
      if (data == "1") {

      } else {
        $scope.processing = false;
        ngToast.create({
          className: 'danger',
          content: 'Data absen gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
        });
      }
    });
  }

  $scope.importAbsent = function () {
    if ($scope.file) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/karyawan/import-absent-fp.php',
        data: {
          'file': $scope.file
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data.res == "1") {
          ngToast.create({
            className: 'success',
            content: data.msg + ' data absen baru berhasil di import <i class="fa fa-remove"></i>'
          });
          $scope.processing = false;
          $('#myModal').modal('hide');
          $scope.getdata();
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data Absent gagal import. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    } else {
      alert("Silahkan pilih file absen yang ingin di import");
    }
  }
});


tripApp.controller('LaporanPOProyekController', function ($scope, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });
  $scope.jenispo = "";
  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();

  $scope.doPrint3 = function () {
    window.open($rootScope.baseURL + 'api/print/print-po-proyek.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&jenispo=' + $scope.jenispo, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('LaporanPOTahunan', function ($scope, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
  $scope.years = CommonServices.yearList();
  $scope.tahun = CommonServices.currentYear().toString();
  $scope.pajakpo = "";

  $scope.doPrint3 = function () {
    window.open($rootScope.baseURL + 'api/print/print-po-proyek-tahunan.php?tahun=' + $scope.tahun + '&pajakpo=' + $scope.pajakpo, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('LaporanPOSupplierController', function ($scope, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();

  $scope.doPrint3 = function () {
    window.open($rootScope.baseURL + 'api/print/print-po-supplier.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});



tripApp.controller('LaporanPOProyekSplitController', function ($scope, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.jenispo = "";
  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();

  $scope.doPrint3 = function () {
    window.open($rootScope.baseURL + 'api/print/print-po-proyek-split.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&jenispo=' + $scope.jenispo, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('MonitoringAccountingController', function ($scope, $route, $routeParams, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true
  });
  $scope.tanggal = CommonServices.currentDateID();
  $scope.getdata = function () {
    $http.get('api/monitoring/monitoring-accounting.php?tanggal=' + $scope.tanggal).success(function (data, status) {
      $scope.data_display = data;
    });
  };
  $scope.getdata();
  $scope.refreshData = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }
});

tripApp.controller('MonitoringRegularController', function ($scope, $route, $routeParams, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true
  });
  $scope.tanggal = CommonServices.currentDateID();
  $scope.getdata = function () {
    $http.get('api/monitoring/monitoring-regular.php?tanggal=' + $scope.tanggal).success(function (data, status) {
      $scope.data_display = data;
    });
  };
  $scope.getdata();
  $scope.refreshData = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }
});


tripApp.controller('LaporanReimburseController', function ($rootScope, $scope, $route, $routeParams, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();
  $scope.jenis = "";
  $scope.stts_reimburse = "";

  $scope.doPrint = function () {
    window.open($rootScope.baseURL + 'api/print/print-data-reimburse.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&jenis=' + $scope.jenis + '&stts_reimburse=' + $scope.stts_reimburse, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});


tripApp.controller('LaporanRekapReimburseController', function ($rootScope, $scope, $route, $routeParams, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();
  $scope.stts_reimburse = "";
  $scope.data_karyawan = [];
  $scope.karyawan = "";

  $scope.getdata = function () {
    $http.get('api/reimburse/reimburse.php?act=ReportRequirement').success(function (data, status) {
      $scope.data_karyawan = data.karyawan;
    });
  };

  $scope.getdata();

  $scope.doPrint = function () {
    window.open($rootScope.baseURL + 'api/print/print-rekap-reimburse.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&stts_reimburse=' + $scope.stts_reimburse + '&karyawan=' + $scope.karyawan, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('LaporanReimburseKendaraan', function ($rootScope, $scope, $route, $routeParams, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();

  $scope.doPrint = function () {
    window.open($rootScope.baseURL + 'api/print/print-laporan-reimburse.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('LaporanDataAbsenKaryawanController', function ($rootScope, $scope, $route, $routeParams, $http, ngToast, CommonServices) {
  var d = new Date();
  var mm = d.getMonth() + 1;
  var yyyy = d.getFullYear().toString();
  if (parseInt(mm) < 10) mm = '0' + mm.toString();

  $scope.month = CommonServices.monthList();

  $scope.bulan = mm.toString();
  $scope.tahun = yyyy;

  $scope.doPrint = function () {
    window.open($rootScope.baseURL + 'api/print/print-data-absen-karyawan.php?datestart=' + $scope.bulan + '&dateend=' + $scope.tahun + '&jenis=' + $scope.jenis, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('LaporanDataCutiKaryawanController', function ($rootScope, $scope, $route, $routeParams, $http, ngToast, CommonServices) {
  var d = new Date();
  var mm = d.getMonth() + 1;
  var yyyy = d.getFullYear().toString();
  if (parseInt(mm) < 10) mm = '0' + mm.toString();

  $scope.month = CommonServices.monthList();

  $scope.bulan = mm.toString();
  $scope.tahun = yyyy;

  $scope.doPrint = function () {
    window.open($rootScope.baseURL + 'api/print/print-data-cuti-karyawan.php?datestart=' + $scope.bulan + '&dateend=' + $scope.tahun, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('LaporanDataCutiKaryawanController2', function ($rootScope, $scope, $route, $routeParams, $http, ngToast, CommonServices) {
  var d = new Date();
  var mm = d.getMonth() + 1;
  var yyyy = d.getFullYear().toString();
  if (parseInt(mm) < 10) mm = '0' + mm.toString();

  $scope.month = CommonServices.monthList();

  $scope.bulan = mm.toString();
  $scope.tahun = yyyy;

  $scope.doPrint = function () {
    window.open($rootScope.baseURL + 'api/print/print-data-cuti-karyawan2.php?datestart=' + $scope.bulan + '&dateend=' + $scope.tahun, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('LaporanPengirimanBarangProyekController', function ($scope, $route, $routeParams, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true
  });
  $scope.tanggal = CommonServices.currentDateID();
  $scope.getdata = function () {
    $http.get('api/laporan/laporan-pengiriman-barang-proyek.php?proyek=' + $routeParams.id).success(function (data, status) {
      $scope.data_display = data.data;
      $scope.NamaProyek = data.NamaProyek;
    });
  };
  $scope.getdata();
  $scope.refreshData = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }
});

tripApp.controller('LaporanPOBarangProyekController', function ($scope, $route, $routeParams, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true
  });
  $scope.tanggal = CommonServices.currentDateID();
  $scope.getdata = function () {
    $http.get('api/laporan/laporan-po-barang-proyek.php?proyek=' + $routeParams.id).success(function (data, status) {
      $scope.data_display = data.data;
      $scope.NamaProyek = data.NamaProyek;
    });
  };
  $scope.getdata();
  $scope.refreshData = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }
});

tripApp.controller('LaporanSummaryGajiKaryawanController', function ($rootScope, $scope, $route, $routeParams, $http, ngToast, CommonServices) {
  var d = new Date();
  var mm = d.getMonth() + 1;
  var yyyy = d.getFullYear().toString();
  if (parseInt(mm) < 10) mm = '0' + mm.toString();

  $scope.month = CommonServices.monthList();

  $scope.bulan = mm.toString();
  $scope.tahun = yyyy;

  $scope.doPrint = function () {
    window.open($rootScope.baseURL + 'api/print/print-summary-gaji-karyawan.php?bulan=' + $scope.bulan + '&tahun=' + $scope.tahun, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('LaporanGajiKaryawanController', function ($rootScope, $scope, $route, $routeParams, $http, ngToast, CommonServices) {
  var d = new Date();
  var mm = d.getMonth() + 1;
  var yyyy = d.getFullYear().toString();
  if (parseInt(mm) < 10) mm = '0' + mm.toString();

  $scope.month = CommonServices.monthList();

  $scope.bulan = mm.toString();
  $scope.tahun = yyyy;

  $scope.doPrint = function () {
    window.open($rootScope.baseURL + 'api/print/print-gaji-karyawan.php?bulan=' + $scope.bulan + '&tahun=' + $scope.tahun, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('LaporanDetailGajiKaryawanController', function ($rootScope, $scope, $route, $routeParams, $http, ngToast, CommonServices) {
  var d = new Date();
  var mm = d.getMonth() + 1;
  var yyyy = d.getFullYear().toString();
  if (parseInt(mm) < 10) mm = '0' + mm.toString();

  $scope.month = CommonServices.monthList();

  $scope.bulan = mm.toString();
  $scope.tahun = yyyy;

  $scope.doPrint = function () {
    window.open($rootScope.baseURL + 'api/print/print-detail-gaji-karyawan.php?bulan=' + $scope.bulan + '&tahun=' + $scope.tahun, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('LaporanDetailGajiKaryawanController', function ($rootScope, $scope, $route, $routeParams, $http, ngToast, CommonServices) {
  var d = new Date();
  var mm = d.getMonth() + 1;
  var yyyy = d.getFullYear().toString();
  if (parseInt(mm) < 10) mm = '0' + mm.toString();

  $scope.month = CommonServices.monthList();

  $scope.bulan = mm.toString();
  $scope.tahun = yyyy;

  $scope.doPrint = function () {
    window.open($rootScope.baseURL + 'api/print/print-detail-gaji-karyawan.php?bulan=' + $scope.bulan + '&tahun=' + $scope.tahun, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('LaporanRekapProyekController', function ($rootScope, $scope, $route, $routeParams, $http, ngToast, CommonServices) {
  var d = new Date();
  var mm = d.getMonth() + 1;
  var yyyy = d.getFullYear().toString();
  if (parseInt(mm) < 10) mm = '0' + mm.toString();

  $scope.month = CommonServices.monthList();

  $scope.bulan = mm.toString();
  $scope.tahun = yyyy;
  $scope.departement = "";

  $scope.doPrint = function () {
    window.open($rootScope.baseURL + 'api/print/print-rekap-proyek.php?bulan=' + $scope.bulan + '&tahun=' + $scope.tahun + '&departement=' + $scope.departement, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});


tripApp.controller('LaporanDetailPengirimanBarang', function ($scope, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();

  $scope.doPrint3 = function () {
    window.open($rootScope.baseURL + 'api/print/print-rekap-detail-pengiriman-barang.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('LaporanPengirimanPerProyek', function ($scope, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.kode_proyek = "";

  $http.get('api/proyek/data-proyek.php?status=all').success(function (data, status) {
    $scope.data_proyek = data;
  });

  $scope.datestart = "01/01/2000";
  $scope.dateend = CommonServices.lastDateMonth();

  $scope.doPrint3 = function () {
    if ($scope.kode_proyek === "") alert("Pilih Proyek terlebih dahulu!");
    else
      window.open($rootScope.baseURL + 'api/print/print-pengiriman-barang-per-proyek.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&kode_proyek=' + $scope.kode_proyek, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('LaporanRekapPengirimanBarang', function ($scope, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();
  $scope.jenis_stok = "";
  $scope.id_proyek = "";

  $http.get('api/proyek/data-proyek.php?status=all').success(function (data, status) {
    $scope.data_proyek = data;
  });

  $scope.doPrint3 = function () {
    window.open($rootScope.baseURL + 'api/print/print-rekap-pengiriman-barang.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&jenis_stok=' + $scope.jenis_stok + '&id_proyek=' + $scope.id_proyek, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('LaporanStok', function ($scope, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();

  $scope.jenis_stok = "1";

  $scope.doPrint = function () {
    window.open($rootScope.baseURL + 'api/print/print-laporan-stok-barang.php?tipe=' + $scope.jenis_stok + '&datestart=' + $scope.datestart + '&dateend=' + $scope.dateend, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('LaporanReturProyek', function ($scope, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();

  $http.get('api/proyek/data-proyek.php?status=all').success(function (data, status) {
    $scope.data_proyek = data;
  });

  $scope.doPrint = function () {
    window.open($rootScope.baseURL + 'api/print/print-laporan-retur-proyek.php?id_proyek=' + $scope.id_proyek + '&datestart=' + $scope.datestart + '&dateend=' + $scope.dateend, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('LaporanRekapProyeksiProyekController', function ($rootScope, $scope, $route, $routeParams, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();
  $scope.departement = "";

  $scope.doPrint = function () {
    window.open($rootScope.baseURL + 'api/print/print-rekap-proyeksi-proyek.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&departement=' + $scope.departement, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('RekapPOTahunan', function ($scope, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
  $scope.years = CommonServices.yearList();
  $scope.tahun = CommonServices.currentYear().toString();
  $scope.pajakpo = "";

  $scope.doPrint3 = function () {
    window.open($rootScope.baseURL + 'api/print/print-rekap-po-tahunan.php?tahun=' + $scope.tahun + '&pajakpo=' + $scope.pajakpo, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('RekapPOTahunan2', function ($scope, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
  $scope.years = CommonServices.yearList();
  $scope.tahun = CommonServices.currentYear().toString();

  $scope.doPrint3 = function () {
    window.open($rootScope.baseURL + 'api/print/print-rekap-po-tahunan2.php?tahun=' + $scope.tahun, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('RekapDOTahunan', function ($scope, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
  $scope.years = CommonServices.yearList();
  $scope.tahun = CommonServices.currentYear().toString();

  $scope.doPrint3 = function () {
    window.open($rootScope.baseURL + 'api/print/print-rekap-do-tahunan.php?tahun=' + $scope.tahun, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('RekapDOTahunan2', function ($scope, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
  $scope.years = CommonServices.yearList();
  $scope.tahun = CommonServices.currentYear().toString();

  $scope.doPrint3 = function () {
    window.open($rootScope.baseURL + 'api/print/print-rekap-do-tahunan2.php?tahun=' + $scope.tahun, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('RekapCutiKaryawanTahunan', function ($scope, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
  $scope.years = CommonServices.yearList();
  $scope.tahun = CommonServices.currentYear().toString();
  $scope.jenis = 'CutiTahunan';

  $scope.doPrint3 = function () {
    window.open($rootScope.baseURL + 'api/print/print-rekap-cuti-karyawan-tahunan.php?tahun=' + $scope.tahun + '&jenis=' + $scope.jenis, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('RekapKeterlambatanKaryawanTahunan', function ($scope, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
  $scope.years = CommonServices.yearList();
  $scope.tahun = CommonServices.currentYear().toString();

  $scope.doPrint3 = function () {
    window.open($rootScope.baseURL + 'api/print/print-rekap-keterlambatan-karyawan-tahunan.php?tahun=' + $scope.tahun, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('RekapReturTahunan', function ($scope, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
  $scope.years = CommonServices.yearList();
  $scope.tahun = CommonServices.currentYear().toString();

  $scope.doPrint3 = function () {
    window.open($rootScope.baseURL + 'api/print/print-rekap-retur-tahunan.php?tahun=' + $scope.tahun, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('RekapReturTahunan2', function ($scope, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
  $scope.years = CommonServices.yearList();
  $scope.tahun = CommonServices.currentYear().toString();

  $scope.doPrint3 = function () {
    window.open($rootScope.baseURL + 'api/print/print-rekap-retur-tahunan2.php?tahun=' + $scope.tahun, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('LaporanCashflowPettyCashController', function ($scope, $rootScope, $route, $routeParams, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true
  });

  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();
  $scope.id_periode = "";

  $scope.getdata = function () {
    $http.get('api/proyek/proyek-petty-cash.php?act=DisplayData&IDProyek=' + $routeParams.proyekId).success(function (data, status) {
      $scope.data = data.payload.data;
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;
      $scope.data_periode = data.payload.periode;
    });
  };
  $scope.getdata();

  // $scope.doPrint3 = function () {
  //   window.open($rootScope.baseURL + 'api/print/print-laporan-cashflow-petty-cash.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&IDProyek=' + $routeParams.proyekId, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  // }

  $scope.doPrint3 = function () {
    if ($scope.id_periode === '') alert("Silahkan pilih Periode!");
    else {
      window.open($rootScope.baseURL + 'api/print/print-laporan-cashflow-petty-cash.php?id_periode=' + $scope.id_periode, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
    }
  }
});

tripApp.controller('LaporanRekapSlipGajiProyek', function ($rootScope, $scope, $route, $routeParams, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();

  $scope.doPrint = function () {
    window.open($rootScope.baseURL + 'api/print/print-rekap-slip-gaji-per-proyek.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('LaporanKaryawanTahun', function ($scope, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
  $scope.years = CommonServices.yearList();
  $scope.tahun = CommonServices.currentYear().toString();

  $scope.doPrint3 = function () {
    window.open($rootScope.baseURL + 'api/print/print-karyawan-tahunan.php?tahun=' + $scope.tahun, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('LabaRugiSPBController', function ($scope, $route, $routeParams, $http, ngToast, CommonServices) {
  $scope.IDPenjualan = $routeParams.id;
  $scope.urut = "1";

  $scope.tanggal = CommonServices.lastDateMonth();

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.doFilter = function () {
    $http.get('api/laporan/laporan.php?act=LaporanLabaRugi&id=' + $routeParams.id + '&urut=' + $scope.urut + '&tanggal=' + $scope.tanggal).success(function (data, status) {

      $scope.SPB = data.SPB;

      $scope.lTenaga = data.lTenaga;
      $scope.lPendapatan = data.lPendapatan;
      $scope.lPengiriman = data.lPengiriman;
      $scope.lMaterial = data.lMaterial;
      $scope.lPO = data.lPO;

      $scope.Pendapatan1 = data.Pendapatan1;
      $scope.Pendapatan2 = data.Pendapatan2;
      $scope.Pendapatan3 = data.Pendapatan3;
      $scope.Pengiriman1 = data.Pengiriman1;
      $scope.Pengiriman2 = data.Pengiriman2;
      $scope.Pengiriman3 = data.Pengiriman3;
      $scope.Tenaga1 = data.Tenaga1;
      $scope.Tenaga2 = data.Tenaga2;
      $scope.Tenaga3 = data.Tenaga3;
      $scope.Material1 = data.Material1;
      $scope.Material2 = data.Material2;
      $scope.Material3 = data.Material3;

      $scope.Pengeluaran1 = data.Pengeluaran1;
      $scope.Pengeluaran2 = data.Pengeluaran2;
      $scope.Pengeluaran3 = data.Pengeluaran3;

      $scope.PO1 = data.PO1;
      $scope.PO2 = data.PO2;
      $scope.PO3 = data.PO3;

      $scope.Profit = data.Profit;

      $scope.PPN10 = data.PPN10;
      $scope.PPH2 = data.PPH2;
      $scope.DPP = data.DPP;
      $scope.TotalPajak = data.TotalPajak;
    });
  }

  $scope.doFilter();
});