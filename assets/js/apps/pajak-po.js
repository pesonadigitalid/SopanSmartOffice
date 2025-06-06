tripApp.controller('PajakPOController', function ($rootScope, $scope, $route, $http, ngToast, Upload, CommonServices) {
  $scope.kode_proyek = "";
  $scope.supplier = "";
  $scope.jenispo = "";
  $scope.filterstatus = "";
  $scope.activeMenu = '';
  $scope.keterangan = '';

  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();

  $scope.isPajak = 0;
  $scope.isLD = 0;

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  //$scope.kategori = $rootScope.departement;

  $scope.getdata = function () {
    $http.get('api/pajak-po/data-pajak-po.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&kode_proyek=' + $scope.kode_proyek + '&supplier=' + $scope.supplier + '&jenispo=' + $scope.jenispo + '&status=' + $scope.filterstatus + '&ispajak=' + $scope.isPajak + '&isld=' + $scope.isLD + '&keterangan=' + $scope.keterangan).success(function (data, status) {
      $scope.data_pajak = data.po;

      console.log($scope.data_pajak);
      $scope.data_proyek = data.proyek;
      $scope.data_supplier = data.supplier;

      $scope.GrandTotal = data.grandTotal;
      $scope.SisaHutang = data.sisa;

      $scope.all = data.all;
      $scope.new = data.new;
      $scope.complete = data.completed;
    });
  };

  $scope.getdata();

  $scope.doFilter = function (a) {
    $scope.filterstatus = a;
    $scope.activeMenu = a;
    $scope.refreshData();
  }

  $scope.refreshData = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }

  $scope.modalFile = function (a, b, c) {
    $scope.detailNoPO = a;
    $scope.detailFaktur = b;
    $scope.daftarFakturPajak = c;
    $('#UploadFile').modal('show');
  }

  $scope.prosesUpload = function () {
    if ($scope.file) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/pajak-po/upload.php',
        data: {
          'file': $scope.file,
          'nopo': $scope.detailNoPO,
          'daftarFakturPajak': $scope.daftarFakturPajak
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'File berhasil diupload <i class="fa fa-remove"></i>'
          });
          $('#UploadFile').hide();
          $('.modal-backdrop.in').hide();
          $route.reload();
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'File gagal diupload. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }

  $scope.filtersupplier = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  };
});