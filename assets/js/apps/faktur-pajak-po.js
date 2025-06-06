tripApp.controller('FakturPajakPOController', function ($rootScope, $scope, $q, $routeParams, $route, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });
  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();
  $scope.nopo = "";
  $scope.supplier = "";
  $scope.status = "";

  $scope.getdata = function () {
    $http.get('api/faktur-pajak-po/faktur-pajak-po.php?act=DisplayData&datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&supplier=' + $scope.supplier + '&nopo=' + $scope.nopo + '&status=' + $scope.status).success(function (data) {
      $scope.data_pajakpo = data.pajakpo;
      $scope.data_supplier = data.supplier;
    });
  };

  $scope.getdata();

  $scope.refreshData = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }

  $scope.removeRow = function (val) {
    if (confirm("Anda yakin ingin menghapus data ini?")) {
      $http({
        method: "POST",
        url: 'api/faktur-pajak-po/faktur-pajak-po.php?act=Delete',
        data: $.param({
          'idr': val
        }),
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).success(function (data, status) {
        if (data.res == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data faktur pajak po berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data faktur pajak po gagal dihapus. Silahkan coba kembali... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }

  $scope.setApproveData = function (idr, statusData) {
    let confirmMessage = statusData == "1" ? "Approve" : "Reject";
    if (confirm("Anda yakin ingin " + confirmMessage + " faktur pajak ini?")) {
      $http({
        method: "POST",
        url: 'api/faktur-pajak-po/faktur-pajak-po.php?act=ApproveData',
        data: $.param({
          'idr': idr,
          'status': statusData
        }),
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).success(function (data, status) {
        if (data.res == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data faktur pajak po berhasil di' + confirmMessage + ' <i class="fa fa-remove"></i>'
          });
          $scope.refreshData();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data faktur pajak po gagal divalidasi. Silahkan coba kembali... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }

  $scope.doPrint2 = function () {
    window.open($rootScope.baseURL + 'api/print/print-data-faktur-po-pajak.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&supplier=' + $scope.supplier + '&nopo=' + $scope.nopo, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('FakturPajakPONewController', function ($rootScope, $scope, $q, $routeParams, $rootScope, $route, $http, ngToast, Upload) {
  $scope.processing = false;
  $scope.disablecode = false;
  $scope.statusUser = "0";
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.tanggal = $rootScope.currentDateID;
  $scope.outstanding = 0;

  $scope.getdata = function () {
    $http.get('api/faktur-pajak-po/faktur-pajak-po.php?act=LoadAllRequirement').success(function (data) {
      $scope.data_po = data.po;
    });
  };

  $scope.getdata();

  $scope.changePO = function () {
    $scope.outstanding = $scope.data_po.filter((x) => x.IDPO === $scope.id_po)[0].Outstanding;
  }

  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/faktur-pajak-po/faktur-pajak-po.php?act=InsertNew',
        data: {
          'no_faktur': $scope.no_faktur,
          'id_po': $scope.id_po,
          'tanggal': $scope.tanggal,
          'keterangan': $scope.keterangan,
          'nilai': $scope.nilai,
          'file': $scope.file,
          'uploaded': $scope.userLoginID
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data.res == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data faktur pajak po berhasil disimpan <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-faktur-pajak-po';
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data faktur pajak po gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('FakturPajakPOEditController', function ($rootScope, $scope, $route, $routeParams, $http, ngToast, $location, Upload) {

  $scope.getdata = function (id) {
    $http.get('api/faktur-pajak-po/faktur-pajak-po.php?act=LoadAllRequirement&id=' + id).success(function (data) {
      $scope.data_po = data.po;
      setTimeout(() => {
        $('#id_po').val($scope.id_po).trigger("change");
      }, 2000);
    });
  };

  if (typeof $routeParams.id === 'undefined') {
    $scope.getdata();
  }

  $http.get('api/faktur-pajak-po/faktur-pajak-po.php?act=Detail&id=' + $routeParams.id).success(function (data, status) {
    $scope.idfaktur = data.detail.IDPOFakturPajak;
    $scope.no_faktur = data.detail.NoFaktur;
    $scope.id_po = data.detail.IDPO;
    $scope.tanggal = data.detail.Tanggal;
    $scope.outstanding = data.detail.Outstanding;
    $scope.keterangan = data.detail.Keterangan;
    $scope.nilai = data.detail.Nilai;
    $scope.file = data.detail.File;
    $scope.fileview = data.detail.File;

    $scope.getdata(data.detail.IDPO);
  });

  $scope.changePO = function () {
    $scope.outstanding = $scope.data_po.filter((x) => x.IDPO === $scope.id_po)[0].Outstanding;
  }

  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/faktur-pajak-po/faktur-pajak-po.php?act=EditRecord',
        data: {
          'idfaktur': $scope.idfaktur,
          'no_faktur': $scope.no_faktur,
          'id_po': $scope.id_po,
          'tanggal': $scope.tanggal,
          'keterangan': $scope.keterangan,
          'nilai': $scope.nilai,
          'file': $scope.file,
          'uploaded': $scope.userLoginID
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data.res == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data faktur pajak po berhasil diperbaharui <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-faktur-pajak-po';
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data faktur pajak po gagal diperbaharui. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});