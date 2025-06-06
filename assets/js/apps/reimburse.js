tripApp.controller('ReimburseController', function($scope, $route, $routeParams, $http, ngToast) {
  $scope.bulan = "";
  $scope.tahun = "";

  if(typeof $routeParams.idProyek !== 'undefined'){
    $scope.kode_proyek = $routeParams.idProyek;
  }

  $scope.filterstatus = "";
  $scope.activeMenu = '';
  $scope.Detail = [];

  $scope.getdata = function() {
    $http.get('api/reimburse/data-reimburse.php?bulan=' + $scope.bulan + '&tahun=' + $scope.tahun  + '&status=' + $scope.filterstatus).success(function(data, status) {
      $scope.data_reimburse = data.data;

      $scope.all = data.all;
      $scope.new = data.new;
      $scope.approved = data.approved;
      $scope.complete = data.completed;
    });
  };

  $scope.getdata();

  $scope.doFilter = function(a) {
    $scope.filterstatus = a;
    $scope.activeMenu = a;
    $scope.filterdata();
  }

  $scope.filterdata = function() {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  };

  $scope.showModal = function(a, b, c, d, e, f) {
    $scope.Detail.NoReimburse = a;
    $scope.Detail.Jumlah = b;
    $scope.Detail.Karyawan = c;
    $scope.Detail.Bank = d;
    $scope.Detail.NoRekening = e;
    $scope.Detail.IDReimburse = f;

    $('#myModal').modal('show');
    $('#myModal').children('.modal-dialog').removeClass('modal-lg');
  };

  $scope.setTerbayar = function(){
    if (confirm("Anda yakin ingin menandai Terbayar pada Reimburse ini ?")) {
      $('#myModal').modal('hide');
      $http({
        method: "POST",
        url: 'api/reimburse/setTerbayar.php',
        data: $.param({
          'idr': $scope.Detail.IDReimburse
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function(data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data reimburse berhasil diupdate <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data reimburse gagal diupdate. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }

  $scope.removeRow = function(val) {
    if (confirm("Anda yakin ingin menghapus data ini?")) {
      $http({
        method: "POST",
        url: 'api/reimburse/delete.php',
        data: $.param({
          'idr': val
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function(data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data reimburse berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data reimburse gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }
});

tripApp.controller('ReimburseNewController', function($scope, $route, $http, ngToast) {
  $scope.processing = false;
  $scope.disablecode = false;
  $scope.proyek = '0';
  $scope.tanggal = $scope.datenow();
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.getdata = function() {
    $http.get('api/reimburse/reimburse.php?act=LoadAllRequirement').success(function(data, status) {
      $scope.data_karyawan = data.karyawan;
      $scope.data_proyek = data.proyek;
      $scope.data_pembayaran = data.pembayaran;
      $scope.data_sub_pembayaran = data.subpembayaran;
    });
  };

  $scope.getdata();

  $scope.metode_pem = "";

  $scope.changeMetodePembayaran = function() {
    /*var id = $('#metode_pem').find(":selected").attr('data-rekening');
    $scope.metode_pem1 = id;

    $scope.metode_pem2 = '';
    $("#metode_pem2 option").hide();
    $("#metode_pem2 option[value='']").show();
    $("#metode_pem2 option[data-parent='" + id + "']").show();

    $scope.no_bg = '';
    $scope.jatuh_tempo = '';

    if ($scope.metode_pem == "BG") {
      $('.bg_container').show();
    } else {
      $('.bg_container').hide();
    }*/
  }


  $scope.submitForm = function(isValid) {
    if (typeof $scope.karyawan !== 'undefined' && typeof $scope.category !== 'undefined' && typeof $scope.total_nilai !== 'undefined' && typeof $scope.keterangan !== 'undefined') {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/reimburse/new.php',
        data: $.param({
          'tanggal': $scope.tanggal,
          'karyawan': $scope.karyawan,
          'category': $scope.category,
          'proyek': $scope.proyek,
          'keterangan': $scope.keterangan,
          'no_kendaraan': $scope.no_kendaraan,
          'total_nilai': $scope.total_nilai,
          'jumlah_liter': $scope.jumlah_liter,
          'km_kendaraan': $scope.km_kendaraan,
          'lokasi_service': $scope.lokasi_service,
          'stts': $scope.stts,
          'metode_pem': $scope.metode_pem,
          'metode_pem1': $scope.metode_pem1,
          'metode_pem2': $scope.metode_pem2,
          'no_bg': $scope.no_bg,
          'jatuh_tempo': $scope.jatuh_tempo,
          'uploaded': $scope.userLoginID
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function(data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data reimburse berhasil disimpan <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-reimburse';
        } else if (data == "2") {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'KM Kendaraan yang anda masukan lebih kecil dari pada KM Kendaraan saat pembelian sebelumnya. <i class="fa fa-remove"></i>'
          });
        } else if (data == "3") {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Pembelian jumlah bbm lebih besar dari pada kapasitas tangki. Reimburse tidak dapat diterima oleh sistem! <i class="fa fa-remove"></i>'
          });
        } else if (data == "4") {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'No Kendaraan yang anda masukan tidak ada. <i class="fa fa-remove"></i>'
          });
        } else if (data == "5") {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Reimburse gagal disimpan. Biaya overhead proyek sudah melampaui batas. Silahkan cek di detail proyek. <i class="fa fa-remove"></i>'
          });
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data reimburse gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    } else {
      alert("Silahkan lengkapi data terlebih dahulu.");
    }
  };
});

tripApp.controller('ReimburseEditController', function($scope, $route, $routeParams, $http, ngToast) {
  $scope.processing = false;
  $scope.disablecode = true;
  $scope.hideSave = false;
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.getdata = function() {
    $http.get('api/reimburse/reimburse.php?act=Detail&id=' + $routeParams.reimburseId).success(function(data, status) {
      $scope.data_karyawan = data.karyawan;
      $scope.data_proyek = data.proyek;
      $scope.data_pembayaran = data.pembayaran;
      $scope.data_sub_pembayaran = data.subpembayaran;

      $scope.no_reimburse = data.detail.no_reimburse;
      $scope.tanggal = data.detail.tanggal;
      $scope.karyawan = data.detail.karyawan;
      $scope.category = data.detail.category;
      $scope.no_kendaraan = data.detail.no_kendaraan;
      $scope.total_nilai = data.detail.total_nilai;
      $scope.jumlah_liter = data.detail.jumlah_liter;
      $scope.km_kendaraan = data.detail.km_kendaraan;
      $scope.lokasi_service = data.detail.lokasi_service;
      $scope.stts = data.detail.stts;
      $scope.metode_pem = data.detail.metode_pem;
      $scope.metode_pem1 = data.detail.metode_pem1;
      $scope.metode_pem2 = data.detail.metode_pem2;
      $scope.no_bg = data.detail.no_bg;
      $scope.jatuh_tempo = data.detail.jatuh_tempo;
      $scope.proyek = data.detail.proyek;
      $scope.keterangan = data.detail.keterangan;


      $scope.Image1 = data.detail.Image1;
      $scope.Image2 = data.detail.Image2;
      $scope.Image3 = data.detail.Image3;

      if ($scope.stts === "2")
        $scope.hideSave = true;
    });
  };

  $scope.getdata();

  $scope.changeMetodePembayaran = function() {
    var id = $('#metode_pem').find(":selected").attr('data-rekening');
    $scope.metode_pem1 = id;

    $scope.metode_pem2 = '';
    $("#metode_pem2 option").hide();
    $("#metode_pem2 option[value='']").show();
    $("#metode_pem2 option[data-parent='" + id + "']").show();

    $scope.no_bg = '';
    $scope.jatuh_tempo = '';

    if ($scope.metode_pem == "BG") {
      $('.bg_container').show();
    } else {
      $('.bg_container').hide();
    }
  }

  $scope.submitForm = function(isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/reimburse/edit.php',
        data: $.param({
          'tanggal': $scope.tanggal,
          'karyawan': $scope.karyawan,
          'category': $scope.category,
          'proyek': $scope.proyek,
          'keterangan': $scope.keterangan,
          'no_kendaraan': $scope.no_kendaraan,
          'total_nilai': $scope.total_nilai,
          'jumlah_liter': $scope.jumlah_liter,
          'km_kendaraan': $scope.km_kendaraan,
          'lokasi_service': $scope.lokasi_service,
          'stts': $scope.stts,
          'metode_pem': $scope.metode_pem,
          'metode_pem1': $scope.metode_pem1,
          'metode_pem2': $scope.metode_pem2,
          'no_bg': $scope.no_bg,
          'jatuh_tempo': $scope.jatuh_tempo,
          'id': $routeParams.reimburseId
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function(data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data reimburse berhasil diperbaharui <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-reimburse';
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data reimburse gagal diperbaharui. Silahkan coba kembali <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});
