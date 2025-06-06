tripApp.controller('BukuBesarMMSController', function($rootScope, $scope, $q, $routeParams, $route, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();

  $scope.getRequirementData = function() {
    $http.get('api/kas/buku-besar.php?act=DataList&datestart=' + $scope.datestart + '&dateend=' + $scope.dateend).success(function(data, status) {
      $scope.data_buku_besar = data.data_buku_besar
      $scope.Debet = data.debet;
      $scope.Kredit = data.kredit;
      $scope.Closing = data.closing;
    });
  };

  $scope.getRequirementData();

  $scope.refreshData = function() {
    $scope.getRequirementData();
  }
});