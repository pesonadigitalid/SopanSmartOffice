tripApp.controller('AssignAssetController', function ($rootScope, $scope, $route, $http, ngToast, CommonServices) {
  $scope.year_selected = CommonServices.currentYear().toString();
  $scope.karyawan = "";

  $scope.tahunList = CommonServices.yearList();

  $scope.getdata = function () {
    $http.get('api/assign/data-assign.php?act=DataAssign&tahun=' + $scope.year_selected + '&karyawan=' + $scope.karyawan).success(function (data, status) {
      $scope.data_assign = data.DataAssign;
      $scope.data_karyawan = data.DataKaryawan;
    });
  };

  $scope.getdata();

  $scope.refreshData = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }

  $scope.showModal = function (a) {
    $scope.idAssign = a;
    $scope.rfidcode = "";
    $('#myModal').modal('show');
    $('#myModal').children('.modal-dialog').removeClass('modal-lg');
    $('#myModal').on('shown.bs.modal', function () {
      $('#rfidcode').focus();
    });
  };

  $scope.base64 = function (str) {
    return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g,
      function toSolidBytes(match, p1) {
        return String.fromCharCode('0x' + p1);
      }));
  }

  $scope.base64URL = function (url) {
    return $scope.base64(url);
  }

  $scope.setFingerPrintURL = function (fingerType, idKaryawan) {
    var base64url = $scope.base64URL('http://10.1.1.141/smartoffice/verification.php?IDKaryawan=' + idKaryawan + '&FingerType=' + fingerType);
    console.log('http://10.1.1.141/smartoffice/verification.php?IDKaryawan=' + idKaryawan + '&FingerType=' + fingerType);
    location.href = 'finspot:FingerspotVer;' + base64url;
  }

  $scope.showModal2 = function (fingerType, val, idKaryawan) {
    $scope.idAssign = val;

    $('#myModal2').modal('show');
    $('#myModal2').children('.modal-dialog').removeClass('modal-lg');
    $('#myModal2').on('shown.bs.modal', function () {
      $scope.setFingerPrintURL(fingerType, idKaryawan);

      // Waiting for result
      $('body').ajaxMask();
      try {
        timer_register.stop();
      }
      catch (err) {
        console.log('Registration timer has been init');
      }

      var limit = 4;
      var ct = 1;
      var timeout = 5000;

      timer_register = $.timer(timeout, function () {
        $http.get('api/assign/authenticate-finger-print.php').success(function (data, status) {
          if (data === '1') {
            timer_register.stop();
            $('body').ajaxMask({ stop: true });
            $('#myModal2').modal('hide');
            setTimeout(() => {
              $scope.updateStatus2(val);
            }, 500);
          }
        });

        if (ct >= limit) {
          timer_register.stop();

          alert("Authentikasi gagal!");
          $('body').ajaxMask({ stop: true });
          $('#myModal2').modal('hide');
        }

        ct++;
      });
    });
  };

  $scope.afterScan = function (keyEvent) {
    if (keyEvent.which === 13)
      $scope.updateStatus();
  }

  $scope.forceUpdate = function (idAssign) {
    if (confirm('Anda yakin ingin set assign ini sebagai telah diterima?')) {
      $http.get('api/assign/update-status-force.php?idAssign=' + idAssign).success(function (data, status) {
        if (data === "2") {
          ngToast.create({
            className: 'danger',
            content: 'Data Assign gagal diupdate. Silahkan coba kembali lagi.. <i class="fa fa-remove"></i>'
          });
        } else if (data === "3") {
          ngToast.create({
            className: 'danger',
            content: 'Data Assign tidak dapat ditemukan.. <i class="fa fa-remove"></i>'
          });
        } else {
          ngToast.create({
            className: 'success',
            content: 'Data Assign berhasil diupdate! <i class="fa fa-remove"></i>'
          });
          $scope.refreshData();
        }
      });
    }
  }

  $scope.updateStatus = function () {
    $http.get('api/assign/update-status.php?key=' + $scope.rfidcode + '&idAssign=' + $scope.idAssign).success(function (data, status) {
      $('#myModal').modal('hide');
      if (data === "0") {
        ngToast.create({
          className: 'danger',
          content: 'RFID tidak terdaftar di dalam sistem! <i class="fa fa-remove"></i>'
        });
      } else if (data === "2") {
        ngToast.create({
          className: 'danger',
          content: 'Data Assign gagal diupdate. Silahkan coba kembali lagi.. <i class="fa fa-remove"></i>'
        });
      } else if (data === "3") {
        ngToast.create({
          className: 'danger',
          content: 'RFID Salah! Tidak sesuai dengan Karyawan yang di Assign.. <i class="fa fa-remove"></i>'
        });
      } else {
        ngToast.create({
          className: 'success',
          content: 'Data Assign berhasil diupdate! <i class="fa fa-remove"></i>'
        });
        $scope.refreshData();
      }
    });
  }

  $scope.updateStatus2 = function () {
    $http.get('api/assign/update-status-fp.php?idAssign=' + $scope.idAssign).success(function (data, status) {
      $('#myModal2').modal('hide');
      if (data === "1") {
        ngToast.create({
          className: 'success',
          content: 'Data Assign berhasil diupdate! <i class="fa fa-remove"></i>'
        });
        $scope.refreshData();
      } else {
        ngToast.create({
          className: 'danger',
          content: 'Ada sesuatu yang salah. Silahkan coba kembali nanti.. <i class="fa fa-remove"></i>'
        });
      }
    });
  }

  $scope.removeRow = function (val) {
    if (confirm("Anda yakin ingin menghapus data ini?")) {
      $http({
        method: "POST",
        url: 'api/assign/delete.php',
        data: $.param({
          'idr': val
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data assign asset berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data assign asset gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }
});

tripApp.controller('AssignAssetNewController', function ($routeParams, $scope, $route, $http, ngToast, CommonServices) {
  $scope.processing = false;
  $scope.disablecode = false;
  var cartArray = [];
  var noUrut = 1;
  var TotalItem = 1;
  $scope.displayCartArray = [];
  $scope.total_item = 0;
  CommonServices.setDatePickerJQuery();
  $scope.tanggal = CommonServices.currentDateID();
  $scope.karyawan = '';

  $scope.showModal = function () {
    $('#myModal').modal('show');
    $('#myModal').children('.modal-dialog').removeClass('modal-lg');
    $('#myModal').on('shown.bs.modal', function () {
      $('#rfidcode').focus();
    });
  };

  /*$scope.getdatakaryawan = function() {
    $http.get('api/karyawan/data-karyawan.php').success(function(data, status) {
      $scope.data_karyawan = data;
    });
  };

  $scope.getdatakaryawan();*/

  $scope.getdata = function () {
    $http.get('api/asset/data-asset.php?param=assign').success(function (data, status) {
      $scope.data_asset = data.assetArray;
      $scope.data_karyawan = data.karyawanArray;
    });
  };

  $scope.getdata();

  $("#kode").on("change", function (e) {
    $scope.kode = this.value;
    //alert($scope.kode);
    $scope.changeKode();
  });

  $scope.changeKode = function () {
    if ($scope.kode != "") {
      $scope.anama = $scope.data_asset[$scope.kode].Nama;
      $scope.akode = $scope.data_asset[$scope.kode].KodeAsset;
      $scope.idasset = $scope.data_asset[$scope.kode].IDAsset;

      //FORCE DISPLAY TO ELEMENT THOUGHT JQUERY - BUG SELECT2 AJAX MODEL CANNOT UPDATE DOM
      $('#nama').val($scope.anama);
    } else {
      $('#nama').val("");
    }
  }

  $scope.addtocart = function () {
    var IDAsset = $scope.idasset;
    var KodeAsset = $scope.akode;
    var NamaAsset = $scope.anama;

    if (typeof cartArray[IDAsset] != 'undefined') {
      ngToast.create({
        className: 'danger',
        content: 'Asset tersebut sudah dipilih. <i class="fa fa-remove"></i>'
      });
    } else {
      cartArray[IDAsset] = { NoUrut: noUrut, IDAsset: IDAsset, KodeAsset: KodeAsset, NamaAsset: NamaAsset };
      noUrut += 1;
    }
    $scope.displayCart();
  }

  $scope.displayCart = function () {
    function sortFunction(a, b) {
      if (a['NoUrut'] == b['NoUrut']) {
        return 0;
      } else {
        return (a['NoUrut'] < b['NoUrut']) ? -1 : 1;
      }
    }
    $scope.displayCartArray = cartArray.filter(function () {
      return true
    });
    $scope.displayCartArray = $scope.displayCartArray.sort(sortFunction);
    $scope.total_item = $scope.displayCartArray.length;
  }

  $scope.deleteCart = function (a) {
    delete cartArray[a];
    $scope.displayCart();

    return false;
  };

  $scope.afterScan = function (keyEvent) {
    if (keyEvent.which === 13)
      $scope.doTabApproval();
  }

  $scope.kirimApproval = function () {
    $scope.status = 0;
    $scope.approve_method = 1;
    $scope.rfidcode = "";
    //alert($scope.status + ' / ' + $scope.approve_method + ' / ' +$scope.rfidcode);
    $scope.submitForm(true);
  }

  $scope.doTabApproval = function () {
    $scope.status = 1;
    $scope.approve_method = 2;
    //alert($scope.status + ' / ' + $scope.approve_method + ' / ' +$scope.rfidcode);
    $scope.submitForm(true);
  }

  $scope.submitForm = function (isValid) {
    if ($scope.displayCartArray.length == "0") {
      ngToast.create({
        className: 'danger',
        content: 'Detail assign asset anda masih kosong. <i class="fa fa-remove"></i>'
      });
    } else {
      if (isValid) {
        $scope.processing = true;
        $http({
          method: "POST",
          url: 'api/assign/new.php',
          data: $.param({
            'tanggal': $scope.tanggal,
            'karyawan': $scope.karyawan,
            'total_item': $scope.total_item,
            'uploaded': $scope.userLoginID,
            'status': $scope.status,
            'approve_method': $scope.approve_method,
            'rfidcode': $scope.rfidcode,
            'cc': $scope.cc,
            'cart': JSON.stringify(cartArray)
          }),
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).success(function (data, status) {
          $('#myModal').modal('hide');
          $scope.rfidcode = "";
          $scope.processing = false;
          if (data == "1") {
            ngToast.create({
              className: 'success',
              content: 'Data assign asset berhasil disimpan <i class="fa fa-remove"></i>'
            });
            setTimeout(function () {
              window.document.location = '#/data-assign-asset';
            }, 500);
          } else if (data == "2") {
            ngToast.create({
              className: 'danger',
              content: 'Anda tidak dapat melalukan approval assign melalui tab karena karyawan ini belum teregister kartu RFID. <i class="fa fa-remove"></i>'
            });
          } else if (data == "3") {
            ngToast.create({
              className: 'danger',
              content: 'Kartu RFID yang di tap salah! <i class="fa fa-remove"></i>'
            });
          } else {
            ngToast.create({
              className: 'danger',
              content: 'Data assign asset gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
            });
          }
        });
      }
    }
  };
});

tripApp.controller('AssignAssetDetailController', function ($scope, $route, $rootScope, $routeParams, $http, ngToast, Upload) {
  $scope.processing = false;
  $scope.jabatan = "";
  $scope.departement = "";

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.doPrint = function () {
    window.open($rootScope.baseURL + 'api/print/print-karyawan.php?id=' + $routeParams.karyawanId, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }

  $scope.getAllData = function () {
    $http.get('api/assign/data-assign.php?act=DetailAssign&id=' + $routeParams.idAssign).success(function (data, status) {
      $scope.data_detail = data.DataDetail;

      $scope.no_assign = data.DataMaster[0].NoAssign;
      $scope.tanggal = data.DataMaster[0].Tanggal;
      $scope.tanggal_diterima = data.DataMaster[0].DateApproved;
      $scope.karyawan = data.DataMaster[0].Karyawan;
      $scope.status_assign = data.DataMaster[0].Status;
      $scope.total_item = data.DataMaster[0].TotalItem;
      $scope.CCTo = data.DataMaster[0].CCTo;

    });
  };

  $scope.getAllData();
});

