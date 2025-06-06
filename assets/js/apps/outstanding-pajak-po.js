tripApp.controller('OutstandingPajakPOController', function ($rootScope, $scope, $q, $routeParams, $route, $http, ngToast, CommonServices) {
  $scope.kode_proyek = "";
  $scope.supplier = "";
  $scope.jenispo = "";
  $scope.filterstatus = "";
  $scope.activeMenu = '';
  $scope.keterangan = '';
  $scope.nopo = '';

  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();

  $scope.isPajak = 1;
  $scope.isLD = 0;

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });


  $scope.getdata = function () {
    $http.get('api/purchase-order/data-purchase-order.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&kode_proyek=' + $scope.kode_proyek + '&supplier=' + $scope.supplier + '&jenispo=' + $scope.jenispo + '&status=' + $scope.filterstatus + '&ispajak=' + $scope.isPajak + '&isld=' + $scope.isLD + '&keterangan=' + $scope.keterangan + '&nopo=' + $scope.nopo).success(function (data, status) {
      $scope.data_purchase = data.po;
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

  $scope.doPrint = function () {
    window.open($rootScope.baseURL + 'api/print/print-data-outstanding-pajak.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&supplier=' + $scope.supplier + '&ispajak=' + $scope.isPajak + '&isld=' + $scope.isLD + '&nopo=' + $scope.nopo, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});