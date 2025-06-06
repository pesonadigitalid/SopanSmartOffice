tripApp.controller('PelangganController', function ($rootScope, $scope, $route, $http, ngToast) {
  $scope.kategori = "";
  $scope.nama = "";

  //$scope.kategori = $rootScope.departement;

  $scope.getdata = function () {
    $http.get('api/pelanggan/data-pelanggan.php?kategori=' + $scope.kategori + '&nama=' + $scope.nama).success(function (data, status) {
      $scope.data_pelanggan = data;
    });
  };

  $scope.getdata();

  $scope.getdatadepartement = function () {
    $http.get('api/departement/data-departement.php').success(function (data, status) {
      $scope.data_departement = data;
    });
  };

  $scope.getdatadepartement();

  $scope.getFileCategory = function () {
    $http.get('api/file-category/data-file-category.php').success(function (data, status) {
      $scope.data_file_category = data;
    });
  };

  $scope.getFileCategory();

  $scope.filtersupplier = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  };

  $scope.removeRow = function (val) {
    if (confirm("Anda yakin ingin menghapus data ini?")) {
      $http({
        method: "POST",
        url: 'api/pelanggan/delete.php',
        data: $.param({
          'idr': val
        }),
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data pelanggan berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'success',
            content: 'Data pelanggan gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }

  $scope.doPrint = function (a) {
    window.open($rootScope.baseURL + 'api/print/print-pelanggan.php?id=' + a, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('PelangganNewController', function ($scope, $rootScope, $route, $http, ngToast) {
  $scope.processing = false;
  $scope.statusUser = "0";
  $scope.disablecode = false;

  $scope.kategori = '';

  $scope.$watch(function () {
    return $scope.statusUser;
  }, function () {
    $scope.statusUser = Number($scope.statusUser);
    console.log($scope.statusUser, typeof $scope.statusUser);
  }, true);

  $scope.getdatadepartement = function () {
    $http.get('api/departement/data-departement.php').success(function (data, status) {
      $scope.data_departement = data;
    });
  };

  $scope.getdatadepartement();

  $scope.lenghtkdpelanggan = function () {
    var KodePelanggan = $('#kode_pelanggan').val();
    if (KodePelanggan.length > 10) {
      ngToast.create({
        className: 'danger',
        content: 'Mohon maaf kode pelanggan terlalu panjang. Maksimal 10 digit <i class="fa fa-remove"></i>'
      });
    }
  }

  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/pelanggan/new.php',
        data: $.param({
          'kode_pelanggan': $scope.kode_pelanggan,
          'nama': $scope.nama,
          'alamat': $scope.alamat,
          'kota': $scope.kota,
          'provinsi': $scope.provinsi,
          'kode_pos': $scope.kode_pos,
          'no_telp': $scope.no_telp,
          'no_fax': $scope.no_fax,
          'email': $scope.email,
          'website': $scope.website,
          'kategori': $scope.kategori,
          'jenis': $scope.jenis,
          'status': $scope.statusUser,
          'namakp1': $scope.namakp1,
          'jabatankp1': $scope.jabatankp1,
          'emailkp1': $scope.emailkp1,
          'hpkp1': $scope.hpkp1,
          'namakp2': $scope.namakp2,
          'jabatankp2': $scope.jabatankp2,
          'emailkp2': $scope.emailkp2,
          'hpkp2': $scope.hpkp2,
          'nama_npwp': $scope.nama_npwp,
          'no_npwp': $scope.no_npwp,
          'alamat_npwp': $scope.alamat_npwp
        }),
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data pelanggan berhasil ditambahkan <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-pelanggan';
        } else if (data == "2") {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Kode pelanggan telah digunakan. Silahkan gunakan kode lain. <i class="fa fa-remove"></i>'
          });
        } else if (data == "3") {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data pelanggan gagal ditambahkan. Kode pelanggan maksimal 10 digit <i class="fa fa-remove"></i>'
          });
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data pelanggan gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('PelangganEditController', function ($rootScope, $scope, $route, $routeParams, $http, ngToast) {
  $scope.processing = false;
  $scope.disablecode = true;
  $scope.IDPelanggan = $routeParams.pelangganId;
  $http.get('api/pelanggan/detail.php?id=' + $routeParams.pelangganId).success(function (data, status) {
    $scope.id_pelanggan = data.id_pelanggan;
    $scope.kode_pelanggan = data.kode_pelanggan;
    $scope.nama = data.nama;
    $scope.alamat = data.alamat;
    $scope.kota = data.kota;
    $scope.provinsi = data.provinsi;
    $scope.kode_pos = data.kode_pos;
    $scope.no_telp = data.no_telp;
    $scope.no_fax = data.no_fax;
    $scope.email = data.email;
    $scope.email2 = data.email2;
    $scope.website = data.website;
    $scope.kontak_person = data.kontak_person;
    $scope.hp = data.hp;
    $scope.kategori = data.kategori;
    $scope.jenis = data.jenis;
    $scope.statusUser = data.status;
    $scope.namakp1 = data.namakp1;
    $scope.jabatankp1 = data.jabatankp1;
    $scope.emailkp1 = data.emailkp1;
    $scope.hpkp1 = data.hpkp1;
    $scope.namakp2 = data.namakp2;
    $scope.jabatankp2 = data.jabatankp2;
    $scope.emailkp2 = data.emailkp2;
    $scope.hpkp2 = data.hpkp2;

    $scope.no_npwp = data.no_npwp;
    $scope.nama_npwp = data.nama_npwp;
    $scope.alamat_npwp = data.alamat_npwp;
  });

  $scope.$watch(function () {
    return $scope.statusUser;
  }, function () {
    $scope.statusUser = Number($scope.statusUser);
    console.log($scope.statusUser, typeof $scope.statusUser);
  }, true);

  $scope.getdatadepartement = function () {
    $http.get('api/departement/data-departement.php').success(function (data, status) {
      $scope.data_departement = data;
    });
  };

  $scope.getdatadepartement();

  $scope.lenghtkdpelanggan = function () {
    //alert("OK");
    var KodePelanggan = $('#kode_pelanggan').val();
    if (KodePelanggan.length > 10) {
      ngToast.create({
        className: 'danger',
        content: 'Mohon maaf kode pelanggan terlalu panjang. Maksimal 10 digit <i class="fa fa-remove"></i>'
      });
    }
  };

  $scope.doPrint = function () {
    window.open($rootScope.baseURL + 'api/print/print-pelanggan.php?id=' + $scope.id_pelanggan, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }

  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/pelanggan/edit.php',
        data: $.param({
          'kode_pelanggan': $scope.kode_pelanggan,
          'nama': $scope.nama,
          'alamat': $scope.alamat,
          'kota': $scope.kota,
          'provinsi': $scope.provinsi,
          'kode_pos': $scope.kode_pos,
          'no_telp': $scope.no_telp,
          'no_fax': $scope.no_fax,
          'email': $scope.email,
          'website': $scope.website,
          'kategori': $scope.kategori,
          'jenis': $scope.jenis,
          'status': $scope.statusUser,
          'namakp1': $scope.namakp1,
          'jabatankp1': $scope.jabatankp1,
          'emailkp1': $scope.emailkp1,
          'hpkp1': $scope.hpkp1,
          'namakp2': $scope.namakp2,
          'jabatankp2': $scope.jabatankp2,
          'emailkp2': $scope.emailkp2,
          'hpkp2': $scope.hpkp2,
          'nama_npwp': $scope.nama_npwp,
          'no_npwp': $scope.no_npwp,
          'alamat_npwp': $scope.alamat_npwp,
          'id': $routeParams.pelangganId
        }),
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data pelanggan berhasil diperbaharui <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-pelanggan';
        } else if (data == "2") {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data pelanggan gagal ditambahkan. Kode pelanggan maksimal 10 digit <i class="fa fa-remove"></i>'
          });
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data pelanggan gagal diperbaharui. Silahkan coba kembali <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});