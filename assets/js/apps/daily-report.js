tripApp.controller('ReportDailyController', function($scope, $route, $http, ngToast, CommonServices) {
  CommonServices.setDatePickerJQuery();

  $scope.datestart = CommonServices.currentDateID();
  $scope.dateend = "";
  $scope.no_surat_jalan = "0";

  $scope.getAllData = function() {
    $http.get('api/daily-report/daily-report.php?act=DailyReport&datestart=' + $scope.datestart + '&no_surat_jalan=' + $scope.no_surat_jalan).success(function(data, status) {
      $scope.reports = data.report;
      $scope.data_surat_jalan = data.SuratJalanArray;
    });
  };

  $scope.getAllData();

  $scope.refreshData = function() {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getAllData();
  }
});

tripApp.controller('ReportUserController', function($scope, $route, $http, ngToast, CommonServices) {

  var d = new Date();
  var mm = d.getMonth() + 1;
  var yyyy = d.getFullYear().toString();
  if (parseInt(mm) < 10) mm = '0' + mm;

  $scope.bulan = mm;
  $scope.tahun = yyyy;

  $scope.bulanList = CommonServices.monthList();
  $scope.tahunList = CommonServices.yearList();

  $http.get('api/karyawan/data-karyawan.php?status=1').success(function(data, status) {
    $scope.karyawan_list = data;
  });

  $scope.getAllData = function() {
    $http.get('api/daily-report/daily-report.php?act=ReportPerKaryawan&bulan=' + $scope.bulan + '&tahun=' + $scope.tahun + '&karyawan=' + $scope.karyawan).success(function(data, status) {
      $scope.reports = data.report;
      $scope.totalCount = data.totalCount;
    });
  };

  $scope.refreshData = function() {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getAllData();
  }
});
