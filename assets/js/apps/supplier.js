tripApp.controller('SupplierController', function ($rootScope, $scope, $route, $http, ngToast) {
  $scope.kategori = "";
  $scope.nama = "";

  $scope.getdata = function () {
    $http.get('api/supplier/data-supplier.php?kategori=' + $scope.kategori + '&nama=' + $scope.nama).success(function (data, status) {
      $scope.data_supplier = data;
    });
  };
  $scope.getdata();

  $scope.getdatadepartement = function () {
    $http.get('api/departement/data-departement.php').success(function (data, status) {
      $scope.data_departement = data;
    });
  };

  $scope.getdatadepartement();

  $scope.filtersupplier = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }

  $scope.removeRow = function (val) {
    if (confirm("Anda yakin ingin menghapus data ini?")) {
      $http({
        method: "POST",
        url: 'api/supplier/delete.php',
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
            content: 'Data supplier berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'success',
            content: 'Data supplier gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }

  $scope.doPrint = function (a) {
    window.open($rootScope.baseURL + 'api/print/print-supplier.php?id=' + a, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('SupplierNewController', function ($scope, $rootScope, $route, $http, ngToast) {
  $scope.processing = false;
  $scope.disablecode = false;
  $scope.statusUser = "0";
  $scope.kategori2 = [];

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

  $scope.getdatamaterial = function () {
    $http.get('api/material/data-material.php').success(function (data, status) {
      $scope.data_material = data;
    });
  };

  $scope.getdatamaterial();

  $scope.lenghtkdsupplier = function () {
    //alert("OK");
    var KodeSupplier = $('#kode_supplier').val();
    if (KodeSupplier.length > 10) {
      ngToast.create({
        className: 'danger',
        content: 'Kode supplier terlalu panjang. Maksimal 10 digit <i class="fa fa-remove"></i>'
      });
    }
  }

  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/supplier/new.php',
        data: $.param({
          'kode_supplier': $scope.kode_supplier,
          'nama_perusahaan': $scope.nama_perusahaan,
          'alamat': $scope.alamat,
          'kota': $scope.kota,
          'provinsi': $scope.provinsi,
          'kode_pos': $scope.kode_pos,
          'no_telp': $scope.no_telp,
          'no_fax': $scope.no_fax,
          'email': $scope.email,
          'website': $scope.website,
          'deskripsi': $scope.deskripsi,
          'kategori': $scope.kategori,
          'kategori2': JSON.stringify($scope.kategori2),
          'status': $scope.statusUser,
          'namakp1': $scope.namakp1,
          'jabatankp1': $scope.jabatankp1,
          'emailkp1': $scope.emailkp1,
          'hpkp1': $scope.hpkp1,
          'namakp2': $scope.namakp2,
          'jabatankp2': $scope.jabatankp2,
          'emailkp2': $scope.emailkp2,
          'hpkp2': $scope.hpkp2
        }),
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data supplier berhasil ditambahkan <i class="fa fa-remove"></i>'
          });
          $scope.processing = false;
          window.document.location = '#/data-supplier';
        } else if (data == "2") {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Kode supplier telah digunakan. Silahkan gunakan kode lain. <i class="fa fa-remove"></i>'
          });
        } else if (data == "3") {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data supplier gagal disimpan. Kode supplier maksimal 10 digit <i class="fa fa-remove"></i>'
          });
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data supplier gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('SupplierEditController', function ($rootScope, $scope, $route, $routeParams, $http, ngToast) {
  $scope.processing = false;
  $scope.disablecode = true;

  $http.get('api/supplier/detail.php?id=' + $routeParams.supplierId).success(function (data, status) {
    $scope.id_supplier = data.id_supplier;
    $scope.kode_supplier = data.kode_supplier;
    $scope.nama_perusahaan = data.nama_perusahaan;
    $scope.alamat = data.alamat;
    $scope.kota = data.kota;
    $scope.provinsi = data.provinsi;
    $scope.kode_pos = data.kode_pos;
    $scope.no_telp = data.no_telp;
    $scope.no_fax = data.no_fax;
    $scope.email = data.email;
    $scope.website = data.website;
    $scope.deskripsi = data.deskripsi;
    $scope.kategori = data.kategori;
    $scope.kategori2 = data.kategori2;
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

  $scope.getdatamaterial = function () {
    $http.get('api/material/data-material.php').success(function (data, status) {
      $scope.data_material = data;
    });
  };

  $scope.getdatamaterial();

  $scope.lenghtkdsupplier = function () {
    var KodeSupplier = $('#kode_supplier').val();
    if (KodeSupplier.length > 10) {
      ngToast.create({
        className: 'danger',
        content: 'Kode supplier terlalu panjang. Maksimal 10 digit <i class="fa fa-remove"></i>'
      });
    }
  }

  $scope.doPrint = function () {
    window.open($rootScope.baseURL + 'api/print/print-supplier.php?id=' + $scope.id_supplier, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }

  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/supplier/edit.php',
        data: $.param({
          'kode_supplier': $scope.kode_supplier,
          'nama_perusahaan': $scope.nama_perusahaan,
          'alamat': $scope.alamat,
          'kota': $scope.kota,
          'provinsi': $scope.provinsi,
          'kode_pos': $scope.kode_pos,
          'no_telp': $scope.no_telp,
          'no_fax': $scope.no_fax,
          'email': $scope.email,
          'website': $scope.website,
          'deskripsi': $scope.deskripsi,
          'kategori': $scope.kategori,
          'kategori2': JSON.stringify($scope.kategori2),
          'status': $scope.statusUser,
          'namakp1': $scope.namakp1,
          'jabatankp1': $scope.jabatankp1,
          'emailkp1': $scope.emailkp1,
          'hpkp1': $scope.hpkp1,
          'namakp2': $scope.namakp2,
          'jabatankp2': $scope.jabatankp2,
          'emailkp2': $scope.emailkp2,
          'hpkp2': $scope.hpkp2,
          'id': $routeParams.supplierId
        }),
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data supplier berhasil diperbaharui <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-supplier';
        } else if (data == "2") {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data supplier gagal disimpan. Kode supplier maksimal 10 digit <i class="fa fa-remove"></i>'
          });
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data supplier gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});