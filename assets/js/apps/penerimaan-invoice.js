tripApp.controller('PenerimaanInvoiceController', function($scope, $rootScope, $routeParams, $route, $http, ngToast, CommonServices) {

  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();
  $scope.filterstatus = "";
  $scope.activeMenu = '';

  $scope.spb = '';

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.getdata = function() {
    $http.get('api/penerimaan-invoice/penerimaan-invoice.php?act=DataList&datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&spb=' + $scope.spb + '&filterstatus=' + $scope.filterstatus).success(function(data, status) {
      console.log($scope.data_penerimaan);
      $scope.data_penerimaan = data.data;
      $scope.TotalInvoice = data.TotalInvoice;
      $scope.TotalPenerimaan = data.TotalPenerimaan;
      $scope.PiutangProgress = data.PiutangProgress;
      $scope.SisaPiutang = data.SisaPiutang;
      $scope.spb = data.spb;
      $scope.all = data.All;
      $scope.approved = data.Approved;
      $scope.unapproved = data.UnApproved;
    });
  };

  $scope.getdata();

  $scope.refreshData = function() {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }

  $scope.doFilter = function(a) {
    $scope.filterstatus = a;
    $scope.activeMenu = a;
    $scope.refreshData();
  }

  $scope.removeRow = function(val) {
    if (confirm("Anda yakin ingin menghapus data ini?")) {
      $http({
        method: "POST",
        url: 'api/penerimaan-invoice/penerimaan-invoice.php?act=Delete',
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
            content: 'Data Penerimaan Pembayaran Invoice berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else if (data == "2") {
          ngToast.create({
            className: 'danger',
            content: 'Data Penerimaan Pembayaran Invoice gagal dihapus karena terintegrasi dengan jurnal... <i class="fa fa-remove"></i>'
          });
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data Penerimaan Pembayaran Invoice gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }
});

tripApp.controller('PenerimaanInvoiceNewController', function($scope, $rootScope, $routeParams, $route, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.tanggal = $rootScope.currentDateID;

  $scope.getdata = function() {
    $http.get('api/penerimaan-invoice/penerimaan-invoice.php?act=LoadAllRequirement').success(function(data, status) {
      $scope.data_invoice = data;
    });
  };

  $scope.getdata();

  $scope.changeInvoice = function() {
    $scope.idInvoice = $scope.data_invoice[$scope.invoice]['IDInvoice'];
    $scope.nilaiInvoice = $scope.data_invoice[$scope.invoice]['Jumlah'];
    $scope.totalPenerimaan = $scope.data_invoice[$scope.invoice]['Terbayar'];
    $scope.sisaPiutang = $scope.data_invoice[$scope.invoice]['Sisa'];
    $scope.jumlah = $scope.sisaPiutang;
  }

  $scope.processing = false;
  $scope.submitForm = function(isValid) {
    if (isValid) {
      if (parseFloat($scope.jumlah) > parseFloat($scope.sisaPiutang)) {
        ngToast.create({
          className: 'danger',
          content: 'Jumlah Penerimaan tidak boleh lebih dari Sisa Piutang <i class="fa fa-remove"></i>'
        });
      } else {
        $scope.processing = true;
        $http({
          method: "POST",
          url: 'api/penerimaan-invoice/penerimaan-invoice.php?act=InsertNew',
          data: $.param({
            'idInvoice': $scope.idInvoice,
            'tanggal': $scope.tanggal,
            'jumlah': $scope.jumlah,
            'keterangan': $scope.keterangan
          }),
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          }
        }).success(function(data, status) {
          if (data == 1) {
            ngToast.create({
              className: 'success',
              content: 'Data Penerimaan Pembayaran Invoice berhasil disimpan <i class="fa fa-remove"></i>'
            });
            window.document.location = '#/data-penerimaan-invoice/';
          } else {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Data Penerimaan Pembayaran Invoice gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
            });
          }
        });
      }
    }
  };

  $scope.edited = false;
});

tripApp.controller('PenerimaanInvoiceEditController', function($scope, $rootScope, $route, $routeParams, $http, ngToast) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.edited = true;

  $scope.processing = false;
  $http.get('api/penerimaan-invoice/penerimaan-invoice.php?act=Detail&id=' + $routeParams.id).success(function(data, status) {
    $scope.data_invoice = data.invoice;
    $scope.idInvoice = data.data.IDInvoice;

    for (var i in $scope.data_invoice) {
      if ($scope.data_invoice[i]['IDInvoice'] === $scope.idInvoice)
        $scope.invoice = i;
    }

    $scope.noinv = data.data.NoPenerimaan;
    $scope.tanggal = data.data.Tanggal;
    $scope.jumlah = data.data.Jumlah;
    $scope.keterangan = data.data.Keterangan;
  });

  $scope.submitForm = function(isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/penerimaan-invoice/penerimaan-invoice.php?act=Edit',
        data: $.param({
          'noinv': $scope.noinv,
          'keterangan': $scope.keterangan,
        }),
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).success(function(data, status) {
        if (data == 1) {
          ngToast.create({
            className: 'success',
            content: 'Data Penerimaan Invoice berhasil diperbaharui <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-penerimaan-invoice';
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data Penerimaan Invoice gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});
