tripApp.controller('KasMasukController', function($rootScope, $scope, $q, $routeParams, $route, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();

  $scope.getRequirementData = function() {
    $http.get('api/kas/kas-masuk.php?act=DataList&datestart=' + $scope.datestart + '&dateend=' + $scope.dateend).success(function(data, status) {
      $scope.data_pembayaran = data.pembayaran;
    });
  };

  $scope.getRequirementData();

  $scope.refreshData = function() {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getRequirementData();
  }

  $scope.removeRow = function(val) {
    if (confirm("Anda yakin ingin menghapus data ini?")) {
      $http({
        method: "POST",
        url: 'api/kas/kas-masuk.php?act=Delete',
        data: $.param({
          'idr': val
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function(data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data Kas Masuk berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data Kas Masuk gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }

  $scope.doPrint = function(a) {
    window.open($rootScope.baseURL + 'api/print/print-kas-masuk.php?id=' + a, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('KasMasukNewController', function($rootScope, $scope, $q, $routeParams, $rootScope, $route, $http, ngToast, $timeout) {

  $scope.processing = false;
  $scope.disablecode = false;

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.tanggal = $scope.datenow();
  $scope.kerekening = "112"

  $scope.getAllData = function() {
    $http.get('api/kas/kas-masuk.php?act=LoadAllRequirement').success(function(data, status) {
      $scope.dari_rekening = data.dariRekening;
      $scope.ke_rekening = data.keRekening;
    });
  };

  $scope.getAllData();

  $scope.submitForm = function(isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/kas/kas-masuk.php?act=InsertNew',
        data: $.param({
          'tanggal': $scope.tanggal,
          'cp': $scope.cp,
          'jumlah_pembayaran': $scope.jumlah_pembayaran,
          'darirekening': $scope.darirekening,
          'kerekening': $scope.kerekening,
          'keterangan': $scope.keterangan
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function(data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Kas Masuk berhasil disimpan <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-kas-masuk/';
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Kas Masuk gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('KasMasukEditController', function($rootScope, $scope, $q, $routeParams, $rootScope, $route, $http, ngToast, $timeout) {
  $scope.processing = false;
  $scope.disablecode = true;

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $http.get('api/kas/kas-masuk.php?act=loadData&id=' + $routeParams.id).success(function(data, status) {
    $scope.dari_rekening = data.dariRekening;
    $scope.ke_rekening = data.keRekening;

    $scope.no_bukti = data.detail.NoBukti;
    $scope.tanggal = data.detail.Tanggal;
    $scope.cp = data.detail.ContactPerson;
    $scope.jumlah_pembayaran = data.detail.Jumlah;
    $scope.darirekening = data.detail.DariRekening;
    $scope.kerekening = data.detail.KeRekening;
    $scope.keterangan = data.detail.Keterangan;
  });

  $scope.submitForm = function(isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/kas/kas-masuk.php?act=Edit',
        data: $.param({
          'no_bukti': $routeParams.id,
          'tanggal': $scope.tanggal,
          'cp': $scope.cp,
          'jumlah_pembayaran': $scope.jumlah_pembayaran,
          'darirekening': $scope.darirekening,
          'kerekening': $scope.kerekening,
          'keterangan': $scope.keterangan
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function(data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Kas Masuk berhasil disimpan <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-kas-masuk/';
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Kas Masuk gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});
