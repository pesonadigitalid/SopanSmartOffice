tripApp.controller('ManageBMController', function($scope, $route, $http, ngToast) {

  $scope.getdata = function() {
    $http.get('api/mailblasting/mailblasting.php?act=DataList').success(function(data, status) {
      $scope.data_blasting = data.data;
    });
  };

  $scope.getdata();

  $scope.removeRow = function(val) {
    if (confirm("Anda yakin ingin menghapus data ini?")) {
      $http({
        method: "POST",
        url: 'api/mailblasting/mailblasting.php?act=Delete',
        data: $.param({
          'idr': val
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function(data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Mail Blasting berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'success',
            content: 'Mail Blasting gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }
});

tripApp.controller('ManageBMNewController', function($scope, $route, $http, ngToast, $routeParams, Upload) {
  $scope.getdata = function() {
    $http.get('api/mailblasting/mailblasting.php?act=LoadAllRequirement').success(function(data, status) {
      $scope.to_list = data.to;
    });
  };

  $scope.getdata();

  $scope.file1_e ='';
  $scope.file2_e ='';
  $scope.file3_e ='';

  $scope.tinymceOptions = {
    menubar: false,
    plugins: 'link',
    toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | link'
  };

  $scope.processing = false;
  $scope.submitForm = function(isValid) {
    if (isValid) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/mailblasting/mailblasting.php?act=InsertNew',
        data: {
          'file': $scope.file,
          'file1': $scope.file1,
          'file2': $scope.file2,
          'file3': $scope.file3,
          'file1_e': $scope.file1_e,
          'file2_e': $scope.file2_e,
          'file3_e': $scope.file3_e,
          'kepada': $scope.kepada,
          'subject': $scope.subject,
          'message': $scope.message,
          'from': $scope.from
        }
      }).then(function(resp) {
        var data = resp.data;
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Mail Blasting berhasil dikirimkan <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-mail-blasting';
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Mail Blasting gagal dikirimkan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});
tripApp.controller('ManageBMResendController', function($scope, $route, $http, ngToast, $routeParams, Upload) {
  $scope.getdata = function() {
    $http.get('api/mailblasting/mailblasting.php?act=Detail&id='+$routeParams.id).success(function(data, status) {
      $scope.to_list = data.to;
      $scope.subject = data.detail.Subject;
      $scope.from = data.detail.From;
      $scope.message = data.detail.Message;
      $scope.file1_e = data.detail.Image1;
      $scope.file2_e = data.detail.Image2;
      $scope.file3_e = data.detail.Image3;
    });
  };

  $scope.getdata();

  $scope.processing = false;
  $scope.submitForm = function(isValid) {
    if (isValid) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/mailblasting/mailblasting.php?act=InsertNew',
        data: {
          'file': $scope.file,
          'file2': $scope.file2,
          'file3': $scope.file3,
          'file1_e': $scope.file1_e,
          'file2_e': $scope.file2_e,
          'file3_e': $scope.file3_e,
          'kepada': $scope.kepada,
          'subject': $scope.subject,
          'message': $scope.message,
          'from': $scope.from
        }
      }).then(function(resp) {
        var data = resp.data;
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Mail Blasting berhasil dikirimkan <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-mail-blasting';
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Mail Blasting gagal dikirimkan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});
