tripApp.controller('ManageDeviceController', function($scope, $routeParams, $route, $http, ngToast) {

  $scope.getdata = function() {
    $http.get('api/manage-device/data-manage-device.php?id_karyawan=' + $routeParams.karyawanId).success(function(data, status) {
      $scope.data_device = data;
    });
  };

  $scope.getdata();
  $scope.idkaryawan = $routeParams.karyawanId;

  $scope.removeRow = function(val) {
    if (confirm("Anda yakin ingin menghapus data ini?")) {
      $http({
        method: "POST",
        url: 'api/manage-device/delete.php',
        data: $.param({
          'idr': val
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function(data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data manage device berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data manage device gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }
});

tripApp.controller('ManageDeviceNewController', function($scope, $routeParams, $route, $http, ngToast) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true
  });

  $scope.idkaryawan = $routeParams.karyawanId;

  $scope.lenghtdeviceid = function() {
    //alert("OK");
    /*var DeviceID = $('#device_id').val();
    if(DeviceID.length>10){
        ngToast.create({
          className: 'danger',
          content: 'Mohon maaf Device ID terlalu panjang. Maksimal 10 digit <i class="fa fa-remove"></i>'
        });
    }*/
  }

  $scope.processing = false;
  $scope.submitForm = function(isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/manage-device/new.php',
        data: $.param({
          'device_id': $scope.device_id,
          'os_type': $scope.os_type,
          'id_karyawan': $scope.idkaryawan,
          'uploaded': $scope.userLoginID
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function(data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data manage device berhasil disimpan <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-manage-device/' + $routeParams.karyawanId;
        } else if (data == "2") {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Device ID sudah digunakan. Silahkan gunakan ID lainnya <i class="fa fa-remove"></i>'
          });
        } else if (data == "3") {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data manage device gagal disimpan. Device ID maksimal 10 digit <i class="fa fa-remove"></i>'
          });
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data manage device gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('HakAksesCtrl', function($scope, $routeParams, $route, $http, ngToast) {
  $scope.data_hak_akses = [];
  $scope.getdata = function() {
    $http.get('api/karyawan/karyawan.php?act=GetHakAksesAll&idk=' + $routeParams.karyawanId).success(function(data, status) {
      $scope.data_hak_akses = data.permission;
      $scope.data_module = data.module;
      console.log($scope.data_hak_akses)
    });
  };
  $scope.getdata();

  $scope.updateHover = function(a){
    $('.tbody').removeClass('hoveredRow');
    $('#row'+a).addClass('hoveredRow');
  }

  $scope.doUpdate = function(a,b,c,d){
    console.log(a,b,c,d);
    if(b==="19"){
      $scope.showModal(a,b,c,d);
    } else
      $scope.save(a,b,c,d);
  }

  $scope.save = function(a,b,c,d){
    $http({
      method: "POST",
      url: 'api/karyawan/karyawan.php?act=SaveHakAksesModule',
      data: $.param({
        'idk': a,
        'idmodule': b,
        'tipe': c,
        'value': $("#"+c+a+b).prop('checked')
      }),
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    }).success(function(data, status) {
      if (data == "1") {
        ngToast.create({
          className: 'success',
          content: 'Hak Akses karyawan berhasil diperbaharui <i class="fa fa-remove"></i>'
        });
      } else {
        ngToast.create({
          className: 'danger',
          content: 'Hak Akses gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
        });
      }
    });
  }

  $scope.showModal = function(a,b,c,d){
    $('#myModal').modal('show');
    $('#myModal').children('.modal-dialog').removeClass('modal-lg');
    $('#myModal').on('shown.bs.modal', function() {
      $('#password').focus();
      $('#a').val(a);
      $('#b').val(b);
      $('#c').val(c);
      $('#d').val(d);
    });
  }

  $scope.grantAccess = function(){
    $http.get('api/karyawan/karyawan.php?act=GrantAccessGaji&pass=' + $scope.password).success(function(data, status) {
      if(data=="1"){
        var a = $('#a').val();
        var b = $('#b').val();
        var c = $('#c').val();
        var d = $('#d').val();
        $scope.save(a,b,c,d);
        ngToast.create({
          className: 'success',
          content: 'Access diterima! Menyimpan hak akses ke database. <i class="fa fa-remove"></i>'
        });
        $('#myModal').modal('hide');
      } else {
        ngToast.create({
          className: 'danger',
          content: 'Password salah. Akses ke data gaji ditolak! <i class="fa fa-remove"></i>'
        });
        $('#myModal').modal('hide');
      }
    });
    
  }

});
