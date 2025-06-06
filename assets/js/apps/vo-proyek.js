tripApp.controller('VOProyekController', function($scope, $rootScope, $routeParams, $route, $http, ngToast) {

  $scope.getdata = function() {
    $http.get('api/vo-proyek/data-vo.php?id_proyek=' + $routeParams.proyekId).success(function(data, status) {
      $scope.data_vo = data.data;
      $scope.GrandTotal = data.GrandTotal;
      $scope.GrandTotalInvoice = data.GrandTotalInvoice;
      $scope.PiutangProgress = data.PiutangProgress;
      $scope.SisaPenagihan = data.SisaPenagihan;
      $scope.DetailProyek = data.DetailProyek;
    });
  };

  $scope.getdata();
  $scope.idproyek = $routeParams.proyekId;

  $scope.removeRow = function(val) {
    if (confirm("Anda yakin ingin menghapus data ini?")) {
      $http({
        method: "POST",
        url: 'api/vo-proyek/delete.php',
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
            content: 'Data variant order proyek berhasil dihapus. Silahkan sesuaikan kembali Limit Belanja Proyek! <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data variant order proyek gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }

  $scope.doPrint = function(a) {
    window.open($rootScope.baseURL + 'api/print/print-invoice.php?id=' + a, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('VOController', function($scope, $rootScope, $routeParams, $route, $http, ngToast, CommonServices) {

  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.getdata = function() {
    $http.get('api/vo-proyek/data-vo-all.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&kode_proyek=' + $scope.kode_proyek).success(function(data, status) {
      $scope.data_vo = data.data;
      $scope.proyek = data.proyek;
    });
  };

  $scope.kode_proyek = "";

  $scope.refreshData = function() {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }

  $scope.getdata();

  $scope.removeRow = function(val) {
    if (confirm("Anda yakin ingin menghapus data ini?")) {
      $http({
        method: "POST",
        url: 'api/invoice-proyek/delete.php',
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
            content: 'Data proyek invoice berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else if (data == "2") {
          ngToast.create({
            className: 'danger',
            content: 'Data proyek invoice gagal dihapus karena terintegrasi dengan jurnal... <i class="fa fa-remove"></i>'
          });
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data proyek invoice gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }

  $scope.doPrint = function(a) {
    window.open($rootScope.baseURL + 'api/print/print-invoice.php?id=' + a, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }

  $scope.showModal2 = function(a) {
    $http.get('api/invoice-proyek/data-pembayaran.php?id=' + a.IDInvoice).success(function(data, status) {
      $scope.NoInv = a.NoInv;
      $scope.detailpembayaran = data.DetailPembayaran;
      $scope.GrandTotal = data.GrandTotal;
      $scope.Terbayar = data.Terbayar;
      $scope.Sisa = data.Sisa;
    });
    $('#myModal2').modal('show');
    $('#myModal2').children('.modal-dialog').removeClass('modal-lg');
  };

  $scope.closeModal2 = function() {
    $('#myModal2').modal('hide');
  }
});

tripApp.controller('VOProyekNewController', function($scope, $rootScope, $routeParams, $route, $http, ngToast) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.tanggal = $rootScope.currentDateID;
  console.log($scope.tanggal);

  $scope.idproyek = $routeParams.proyekId;
  $scope.nilai_vo = 0;

  $scope.getdata = function() {
    $http.get('api/proyek/data-proyek.php?id_proyek=' + $routeParams.proyekId + '&status=all').success(function(data, status) {
      $scope.data_proyek = data;
      console.log(data);
      $scope.id_proyek = data[0]["IDProyek"];
      $scope.kode_proyek = data[0]["KodeProyek"];
      $scope.NamaProyek = data[0]["NamaProyek"];
      $scope.Tahun = data[0]["Tahun"];
      $scope.nilai_proyek = parseFloat(data[0]["GrandTotal"]);
      $scope.countNilaiAkhir();
    });
  };

  $scope.countNilaiAkhir = function() {
    $scope.nilai_proyek2 = parseFloat($scope.nilai_proyek) + parseFloat($scope.nilai_vo);
  }

  $scope.getdata();

  $scope.processing = false;
  $scope.submitForm = function(isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/vo-proyek/new.php',
        data: $.param({
          'no_vo': $scope.no_vo,
          'id_proyek': $scope.id_proyek,
          'tanggal': $scope.tanggal,
          'keterangan': $scope.keterangan,
          'nilai_vo': $scope.nilai_vo,
          'nilai_proyek': $scope.nilai_proyek,
          'nilai_proyek2': $scope.nilai_proyek2,
          'id': $routeParams.invoiceid
        }),
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).success(function(data, status) {
        if (data.status == 1) {
          ngToast.create({
            className: 'success',
            content: 'Data variant order proyek berhasil disimpan. Silahkan sesuaikan kembali Limit Belanja Proyek! <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-vo-proyek/' + $routeParams.proyekId;
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: data.msg + ' <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };

  $scope.detailed = false;
});



tripApp.controller('VOProyekEditController', function($scope, $rootScope, $route, $routeParams, $http, ngToast) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.detailed = false;

  $scope.countNilaiAkhir = function() {
    var diff = parseFloat($scope.nilai_vo) - parseFloat($scope.nilai_vo_old);
    $scope.nilai_proyek2 = parseFloat($scope.nilai_proyek) + parseFloat($scope.nilai_vo);
    $scope.diff = diff;
  }

  $http.get('api/vo-proyek/detail.php?id=' + $routeParams.voId).success(function(data, status) {
    $scope.tanggal = data.tanggal;
    $scope.no_vo = data.no_vo;
    $scope.keterangan = data.keterangan;
    $scope.nilai_vo = data.nilai_vo;
    $scope.nilai_vo_old = data.nilai_vo;
    $scope.nilai_proyek = data.nilai_proyek;
    $scope.nilai_proyek2 = data.nilai_proyek2;
    $scope.nilai_proyek2_old = data.nilai_proyek2;
    $scope.idproyek = data.id_proyek;
  });

  $scope.processing = false;
  $scope.submitForm = function(isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/vo-proyek/edit.php',
        data: $.param({
          'idproyek': $scope.idproyek,
          'tanggal': $scope.tanggal,
          'keterangan': $scope.keterangan,
          'nilai_vo': $scope.nilai_vo,
          'nilai_proyek': $scope.nilai_proyek,
          'nilai_proyek2': $scope.nilai_proyek2,
          'diff': $scope.diff,
          'id': $routeParams.voId
        }),
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).success(function(data, status) {
        if (data.status == 1) {
          ngToast.create({
            className: 'success',
            content: 'Data variant order proyek berhasil diperbaharui. Silahkan sesuaikan kembali Limit Belanja Proyek! <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-vo-proyek/' + $scope.idproyek;
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: data.msg + ' <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});


tripApp.controller('VOProyekDetailController', function($scope, $rootScope, $route, $routeParams, $http, ngToast) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.detailed = true;

  $scope.processing = false;
  $http.get('api/vo-proyek/detail.php?id=' + $routeParams.voId).success(function(data, status) {
    $scope.tanggal = data.tanggal;
    $scope.no_vo = data.no_vo;
    $scope.keterangan = data.keterangan;
    $scope.nilai_vo = data.nilai_vo;
    $scope.nilai_proyek = data.nilai_proyek;
    $scope.nilai_proyek2 = data.nilai_proyek2;
    $scope.idproyek = data.id_proyek;
  });
});
