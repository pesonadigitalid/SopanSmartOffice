tripApp.controller('SettingController', function($scope, $route, $http, ngToast) {
  $scope.processing = false;
  $scope.disablecode = false;
  $scope.tanggal = $scope.datenow();
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.getdata = function() {
    $http.get('api/setting/setting.php?act=Detail&id=7').success(function(data, status) {
      $scope.jml_cuti_tahunan = data.detail.jml_cuti_tahunan;
    });
  };

  $scope.getdata();

  $scope.submitForm = function(isValid) {
    if (isValid) {
      $http({
        method: "POST",
        url: 'api/setting/setting.php?act=EditRecord',
        data: $.param({
          'jml_cuti_tahunan': $scope.jml_cuti_tahunan,
          'id': '7'
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function(data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Setting berhasil disimpan <i class="fa fa-remove"></i>'
          });
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Setting gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});