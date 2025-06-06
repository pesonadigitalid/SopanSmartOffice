tripApp.controller('BarangController', function ($scope, $route, $http, ngToast) {
  $scope.kategori = '';
  $scope.material = "";
  $scope.nama = "";
  $scope.getdata = function () {
    $http.get('api/barang/data-barang-all.php?id_jenis=' + $scope.material + '&departement=' + $scope.kategori + '&nama=' + $scope.nama).success(function (data, status) {
      $scope.data_barang = data.barang;
      $scope.data_departement = data.departement;
      $scope.data_material = data.material;
    });
  };
  $scope.getdata();

  $scope.refreshData = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }

  $scope.removeRow = function (val) {
    if (confirm("Anda yakin ingin menghapus data ini?")) {
      $http({
        method: "POST",
        url: 'api/barang/delete.php',
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
            content: 'Data barang berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else if (data == "2") {
          ngToast.create({
            className: 'danger',
            content: 'Data barang gagal dihapus. Karena telah terhubung dengan paket barang lainnya. <i class="fa fa-remove"></i>'
          });
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data barang gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }
});

tripApp.controller('BarangNewController', function ($rootScope, $scope, $route, $http, ngToast, Upload, CommonServices) {
  $scope.processing = false;
  $scope.disablecode = false;
  $scope.statusUser = "0";
  $scope.parent = "0";
  $scope.useDiskonPersenForCalc = true;
  $scope.usePPNPersenForCalc = true;

  $scope.id = '';

  $scope.kategori = '';
  $scope.libCode = '';

  $scope.hargaPublish = 0;
  $scope.diskonPersen = 0;
  $scope.harga = 0;
  $scope.hargajual = 0;
  $scope.margin = 0;
  $scope.parent = 0;
  $scope.hargajualgrosir = 0;
  $scope.margingrosir = 0;
  $scope.isBarang = "";
  $scope.isSellingProduct = 1;
  $scope.IsBarangPPN = 0;
  $scope.ppnPersen = 0;
  $scope.dpp = 0;
  $scope.IsNotifiedService6 = 0;
  $scope.IsNotifiedService12 = 0;
  $scope.IsNotifiedService18 = 0;

  $scope.getAllData = function () {
    $http.get('api/barang/load-all-requirement.php').success(function (data, status) {
      $scope.data_material = data.material;
      $scope.data_supplier = data.supplier;
      $scope.data_departement = data.departement;
      $scope.data_satuan = data.satuan;
      $scope.data_barang = data.barang;
    });
  };

  $scope.getAllData();

  $scope.countMargin = function (useDiskonPersenForCalc) {
    if (useDiskonPersenForCalc !== null) $scope.useDiskonPersenForCalc = useDiskonPersenForCalc;

    if ($scope.useDiskonPersenForCalc) {
      $scope.harga = $scope.hargaPublish - CommonServices.getDiscountValue($scope.diskonPersen, $scope.hargaPublish);
    } else {
      $scope.diskonPersen = (($scope.hargaPublish - $scope.harga) / $scope.hargaPublish * 100).toFixed(2) + "%";
    }

    if (!$scope.harga && !$scope.hargajual) return;

    $scope.margin = $scope.hargajual - $scope.harga;
    $scope.margingrosir = $scope.hargajualgrosir - $scope.harga;

    $scope.countDpp(null);
  }

  $scope.countDpp = function (usePPNPersenForCalc) {
    if (usePPNPersenForCalc !== null) $scope.usePPNPersenForCalc = usePPNPersenForCalc;

    if ($scope.usePPNPersenForCalc) {
      $scope.ppnPersen = $scope.ppnPersen.replace(/[^0-9.,]/g, "");
      $scope.dpp = Math.round((100 / (100 + parseFloat($scope.ppnPersen))) * parseFloat($scope.harga));
    } else {
      $scope.ppnPersen = (((parseFloat($scope.harga) / parseFloat($scope.dpp)) * 100) - 100).toFixed(2);
    }
  }

  $('#kode').select2({
    ajax: {
      url: "api/barang/data-barang-select2.php",
      dataType: 'json',
      data: function (term, page) {
        return {
          q: term
        };
      },
      results: function (data, page) {
        $scope.data_barang = data;
        return { results: data }
      }
    },
    dropdownCssClass: 'bigdrop',
    placeholder: 'Pilih paket penjualan tour...',
    formatSearching: 'Searching...',
  });

  $("#kode").on("change", function (e) {
    $scope.kode = this.value;
    $scope.changeKode();
  });

  $scope.changeKode = function () {
    if ($scope.kode != "") {
      $scope.anama = $scope.data_barang[$scope.kode].Nama;

      $('#anama').val($scope.anama);
    } else {
      $scope.anama = "";
    }
  }

  $scope.finishForm = function (data) {
    if (data == "1") {
      ngToast.create({
        className: 'success',
        content: 'Data barang berhasil ditambahkan <i class="fa fa-remove"></i>'
      });
      window.document.location = '#/data-barang';
    } else if (data == "2") {
      $scope.processing = false;
      ngToast.create({
        className: 'danger',
        content: 'Kode barang sudah digunakan. Silahkan gunakan kode lainnya <i class="fa fa-remove"></i>'
      });
    } else if (data == "3") {
      $scope.processing = false;
      ngToast.create({
        className: 'danger',
        content: 'Data barang gagal disimpan. Kode barang maksimal 10 digit <i class="fa fa-remove"></i>'
      });
    } else if (data == "4") {
      $scope.processing = false;
      ngToast.create({
        className: 'danger',
        content: 'Library Code sudah digunakan. Silahkan gunakan kode lainnya <i class="fa fa-remove"></i>'
      });
    } else {
      $scope.processing = false;
      ngToast.create({
        className: 'danger',
        content: 'Data barang gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
      });
    }
  }

  $scope.submitForm = function (isValid) {
    if (isValid) {
      var body = {
        'kode_barang': $scope.kode_barang,
        'nama': $scope.nama,
        'kategori': $scope.kategori,
        'jenis': $scope.jenis,
        'satuan': $scope.satuan,
        'supplier': $scope.supplier,
        'hargaPublish': $scope.hargaPublish,
        'diskonPersen': $scope.diskonPersen,
        'harga': $scope.harga,
        'hargajual': $scope.hargajual,
        'hargajualgrosir': $scope.hargajualgrosir,
        'margin': $scope.margin,
        'margingrosir': $scope.margingrosir,
        'parent': $scope.parent,
        'isBarang': $scope.isBarang,
        'isSerial': $scope.isSerial,
        'isSellingProduct': $scope.isSellingProduct,
        'IsBarangPPN': $scope.IsBarangPPN,
        'PPNPersen': $scope.ppnPersen,
        'DPP': $scope.dpp,
        'IsNotifiedService6': $scope.IsNotifiedService6,
        'IsNotifiedService12': $scope.IsNotifiedService12,
        'IsNotifiedService18': $scope.IsNotifiedService18,
      };
      if ($scope.foto1 || $scope.foto2 || $scope.foto3) {
        body['foto1'] = $scope.foto1;
        body['foto2'] = $scope.foto2;
        body['foto3'] = $scope.foto3;
        $scope.processing = true;
        Upload.upload({
          url: 'api/barang/new.php',
          data: body
        }).then(function (resp) {
          var data = resp.data;
          $scope.finishForm(data);
        });
      } else {
        $scope.processing = true;
        $http({
          method: "POST",
          url: 'api/barang/new.php',
          data: $.param(body),
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          }
        }).success(function (data, status) {
          $scope.finishForm(data);
        });
      }
    }
  };
});

tripApp.controller('BarangEditController', function ($rootScope, $scope, $route, $routeParams, $http, ngToast, Upload, CommonServices) {
  $scope.processing = false;
  $scope.disablecode = true;
  $scope.id = $routeParams.barangId;
  $scope.useDiskonPersenForCalc = true;
  $scope.usePPNPersenForCalc = true;
  $scope.isSellingProduct = 1;

  $scope.getAllData = function () {
    $http.get('api/barang/load-all-requirement.php?id=' + $routeParams.barangId).success(function (data, status) {
      $scope.data_material = data.material;
      $scope.data_supplier = data.supplier;
      $scope.data_departement = data.departement;
      $scope.data_satuan = data.satuan;
      $scope.data_barang = data.barang;
      $scope.barang_child = data.barang_child;

      $scope.kode_barang = data.detail.kode_barang;
      $scope.nama = data.detail.nama;
      $scope.kategori = data.detail.kategori;
      $scope.jenis = data.detail.jenis;
      $scope.satuan = data.detail.satuan;
      $scope.supplier = data.detail.supplier;
      $scope.hargaPublish = data.detail.hargaPublish;
      $scope.diskonPersen = data.detail.diskonPersen;
      $scope.harga = data.detail.harga;
      $scope.hargajual = data.detail.hargajual;
      $scope.margin = data.detail.margin;
      $scope.hargajualgrosir = data.detail.hargajualgrosir;
      $scope.margingrosir = data.detail.margingrosir;
      $scope.parent = data.detail.parent;
      $scope.isSerial = data.detail.isSerial;
      $scope.isBarang = data.detail.isBarang;
      $scope.isSellingProduct = data.detail.isSellingProduct;

      $scope.libCode = data.detail.libCode;

      $scope.getfoto1 = data.detail.foto1;
      $scope.getfoto2 = data.detail.foto2;
      $scope.getfoto3 = data.detail.foto3;

      if ($scope.getfoto1 == "") $scope.getfoto1 = true;
      if ($scope.getfoto2 == "") $scope.getfoto2 = true;
      if ($scope.getfoto3 == "") $scope.getfoto3 = true;

      $scope.IsBarangPPN = data.detail.IsBarangPPN;
      $scope.ppnPersen = data.detail.PPNPersen;
      $scope.dpp = data.detail.DPP;

      $scope.IsNotifiedService6 = data.detail.IsNotifiedService6;
      $scope.IsNotifiedService12 = data.detail.IsNotifiedService12;
      $scope.IsNotifiedService18 = data.detail.IsNotifiedService18;
    });
  };

  $scope.getAllData();

  $scope.countMargin = function (useDiskonPersenForCalc) {
    if (useDiskonPersenForCalc !== null) $scope.useDiskonPersenForCalc = useDiskonPersenForCalc;

    if ($scope.useDiskonPersenForCalc) {
      $scope.harga = $scope.hargaPublish - CommonServices.getDiscountValue($scope.diskonPersen, $scope.hargaPublish);
    } else {
      $scope.diskonPersen = (($scope.hargaPublish - $scope.harga) / $scope.hargaPublish * 100).toFixed(2) + "%";
    }

    if (!$scope.harga && !$scope.hargajual) return;

    $scope.margin = $scope.hargajual - $scope.harga;
    $scope.margingrosir = $scope.hargajualgrosir - $scope.harga;

    $scope.countDpp(null);
  }

  $scope.countDpp = function (usePPNPersenForCalc) {
    if (usePPNPersenForCalc !== null) $scope.usePPNPersenForCalc = usePPNPersenForCalc;

    if ($scope.usePPNPersenForCalc) {
      $scope.ppnPersen = $scope.ppnPersen.replace(/[^0-9.,]/g, "");
      $scope.dpp = Math.round((100 / (100 + parseFloat($scope.ppnPersen))) * parseFloat($scope.harga));
    } else {
      $scope.ppnPersen = (((parseFloat($scope.harga) / parseFloat($scope.dpp)) * 100) - 100).toFixed(2);
    }
  }

  $scope.calcSubTotal = function (IDBarangChildren) {
    var barang = $scope.barang_child.find(x => x.IDBarangChildren == IDBarangChildren);
    if (barang) {
      barang.Harga = CommonServices.getInputValueAsFloat('#Harga' + IDBarangChildren);
      barang.Qty = CommonServices.getInputValueAsFloat('#Qty' + IDBarangChildren);
      barang.Total = barang.Harga * barang.Qty;
    }
  }

  $('#kode').select2({
    ajax: {
      url: "api/barang/data-barang-select2.php?id=" + $routeParams.barangId,
      dataType: 'json',
      data: function (term, page) {
        return {
          q: term
        };
      },
      results: function (data, page) {
        $scope.data_barang = data;
        return { results: data }
      }
    },
    dropdownCssClass: 'bigdrop',
    placeholder: 'Pilih Data Barang...',
    formatSearching: 'Searching...',
  });

  $("#kode").on("change", function (e) {
    $scope.kode = this.value;
    $scope.changeKode();
  });

  $scope.changeKode = function () {
    if ($scope.kode != "") {
      $scope.anama = $scope.data_barang[$scope.kode].Nama;
      $scope.aidbarang = $scope.data_barang[$scope.kode].IDBarang;
      $scope.aqty = "1";
      $('#anama').val($scope.anama);
      $('#aqty').val($scope.aqty);
    } else {
      $scope.anama = "";
    }
  }

  $scope.removeRowChild = function (a) {
    if (confirm("Anda yakin ingin menghapus barang dari paket ini?")) {
      $http({
        method: "POST",
        url: 'api/barang/delete-child.php',
        data: $.param({
          'idr': a
        }),
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).success(function (data, status) {
        if (data == "1") {
          $scope.getAllData();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data barang gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }

  $scope.addToCart = function () {
    if ($scope.aidbarang != "") {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/barang/add-child-barang.php',
        data: $.param({
          'id_parent': $routeParams.barangId,
          'id_barang': $scope.aidbarang,
          'qty': $scope.aqty
        }),
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).success(function (data, status) {
        if (data == "1") {
          $scope.getAllData();
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data barang gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }

  $scope.finishForm = function (data) {
    if (data == "1") {
      ngToast.create({
        className: 'success',
        content: 'Data barang berhasil diperbaharui <i class="fa fa-remove"></i>'
      });
      window.document.location = '#/data-barang';
    } else if (data === "4") {
      $scope.processing = false;
      ngToast.create({
        className: 'danger',
        content: 'Data barang gagal disimpan. Library Code telah digunakan produk lain. Silahkan gunakan Library Code lain. <i class="fa fa-remove"></i>'
      });
    } else {
      $scope.processing = false;
      ngToast.create({
        className: 'danger',
        content: 'Data barang gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
      });
    }
  }

  $scope.submitForm = function (isValid) {
    var shouldSave = false;
    var isPaket = $scope.barang_child.length > 0;

    if (isValid) {
      if (isPaket) {
        var subTotal = $scope.barang_child.reduce((a, b) => a + b.Total, 0);
        if (subTotal !== parseFloat($scope.hargaPublish)) {
          if (window.confirm("Total harga paket tidak sama dengan harga publish.\r\rTotal Harga Paket: " + numberWithCommas(subTotal) + "\rHarga Publish: " + numberWithCommas(parseFloat($scope.hargaPublish)) + "\r\rApakah anda yakin ingin menyimpan data ini?")) shouldSave = true;
        } else shouldSave = true;
      } else shouldSave = true;
    }

    if (shouldSave) {
      var body = {
        'kode_barang': $scope.kode_barang,
        'nama': $scope.nama,
        'kategori': $scope.kategori,
        'jenis': $scope.jenis,
        'satuan': $scope.satuan,
        'supplier': $scope.supplier,
        'hargaPublish': $scope.hargaPublish,
        'diskonPersen': $scope.diskonPersen,
        'harga': $scope.harga,
        'hargajual': $scope.hargajual,
        'hargajualgrosir': $scope.hargajualgrosir,
        'margin': $scope.margin,
        'margingrosir': $scope.margingrosir,
        'parent': $scope.parent,
        'iduser': $rootScope.userLoginID,
        'isSerial': $scope.isSerial,
        'isSellingProduct': $scope.isSellingProduct,
        'IsBarangPPN': $scope.IsBarangPPN,
        'PPNPersen': $scope.ppnPersen,
        'DPP': $scope.dpp,
        'IsNotifiedService6': $scope.IsNotifiedService6,
        'IsNotifiedService12': $scope.IsNotifiedService12,
        'IsNotifiedService18': $scope.IsNotifiedService18,
        'isBarang': $scope.isBarang,
        'libCode': $scope.libCode,
        'barang_child': JSON.stringify($scope.barang_child),
        'id': $routeParams.barangId
      };
      console.log($scope.foto1)
      console.log($scope.foto2)
      console.log($scope.foto3)
      if ($scope.foto1 || $scope.foto2 || $scope.foto3) {
        body['foto1'] = $scope.foto1;
        body['foto2'] = $scope.foto2;
        body['foto3'] = $scope.foto3;
        $scope.processing = true;
        Upload.upload({
          url: 'api/barang/edit.php',
          data: body
        }).then(function (resp) {
          var data = resp.data;
          $scope.finishForm(data);
        });
      } else {
        $scope.processing = true;
        $http({
          method: "POST",
          url: 'api/barang/edit.php',
          data: $.param(body),
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          }
        }).success(function (data, status) {
          $scope.finishForm(data);
        });
      }
    }
  };
});
