tripApp.controller('PengirimanBarangController', function($scope, $routeParams, $route, $http, ngToast) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true
  });
  $scope.datestart = "";
  $scope.dateend = "";
  $scope.kode_proyek = "";
  $scope.filterstatus = "";
  $scope.activeMenu = '';

  if(typeof $routeParams.idProyek !== 'undefined'){
    $scope.kode_proyek = $routeParams.idProyek;
  }

  $scope.getAllData = function() {
    $http.get('api/pengiriman-barang/pengiriman-barang.php?act=DataList&datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&kode_proyek=' + $scope.kode_proyek + '&status=' + $scope.filterstatus).success(function(data, status) {
      $scope.data_pengiriman = data.pengiriman;
      $scope.data_proyek = data.proyek;

      $scope.all = data.all;
      $scope.new = data.new;
      $scope.success = data.success;
      $scope.rejected = data.rejected;
    });
  };

  $scope.getAllData();

  $scope.doFilter = function(a) {
    $scope.filterstatus = a;
    $scope.activeMenu = a;
    $scope.refreshData();
  }

  $scope.refreshData = function() {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getAllData();
  }

  $scope.idPengiriman = '';

  $scope.showModal = function(a) {
    $scope.idPengiriman = a;
    $scope.rfidcode = "";
    $('#myModal').modal('show');
    $('#myModal').children('.modal-dialog').removeClass('modal-lg');
    $('#myModal').on('shown.bs.modal', function() {
      $('#rfidcode').focus();
    });
  };

  $scope.afterScan = function(keyEvent) {
    if (keyEvent.which === 13)
      $scope.updatePenerimaanBarang();
  }

  $scope.updatePenerimaanBarang = function() {
    $http.get('api/pengiriman-barang/pengiriman-barang.php?act=PenerimaanBarang&key=' + $scope.rfidcode + '&idPengiriman=' + $scope.idPengiriman).success(function(data, status) {
      $('#myModal').modal('hide');
      if (data === "0") {
        ngToast.create({
          className: 'danger',
          content: 'RFID tidak terdaftar di dalam sistem! <i class="fa fa-remove"></i>'
        });
      } else if (data === "2"){
        ngToast.create({
          className: 'danger',
          content: 'Data Pengiriman gagal diupdate. Silahkan coba kembali lagi.. <i class="fa fa-remove"></i>'
        });
      } else {
        ngToast.create({
          className: 'success',
          content: 'Data Pengiriman berhasil diupdate! <i class="fa fa-remove"></i>'
        });
        $scope.refreshData();
      }
    });
  }

  $scope.rejectRow = function(val) {
    if (confirm("Anda yakin ingin membatalkan pengiriman ini?")) {
      $http({
        method: "POST",
        url: 'api/pengiriman-barang/reject.php',
        data: $.param({
          'idr': val
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function(data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data pengiriman berhasil dibatalkan <i class="fa fa-remove"></i>'
          });
          $scope.refreshData();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data pengiriman gagal dibatalkan. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }
});

tripApp.controller('PengirimanBarangNewController', function($scope, $routeParams, $rootScope, $route, $http, ngToast) {
  var cartArray = [];
  var noUrut = 1;
  $scope.displayCartArray = [];
  $scope.totalitem = 0;
  $scope.totaljenisitem = 0;
  $scope.statusPengiriman = "Baru";
  $scope.id_proyek = "0";

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.diterima_oleh = '';
  $scope.diterima_id = '';

  $scope.showModal = function() {
    $scope.rfidcode = "";
    $('#myModal').modal('show');
    $('#myModal').children('.modal-dialog').removeClass('modal-lg');
    $('#myModal').on('shown.bs.modal', function() {
      $('#rfidcode').focus();
    });
  };

  $scope.afterScan = function(keyEvent) {
    if (keyEvent.which === 13)
      $scope.checkRFID();
  }

  $scope.checkRFID = function() {
    $http.get('api/karyawan/check-rfid.php?key=' + $scope.rfidcode).success(function(data, status) {
      if (data === "0") {
        ngToast.create({
          className: 'danger',
          content: 'RFID tidak terdaftar di dalam sistem! <i class="fa fa-remove"></i>'
        });
        $scope.rfidcode = "";
        $scope.diterima_oleh = '';
        $scope.diterima_id = '';
      } else if (data === "2"){
        ngToast.create({
          className: 'danger',
          content: 'Karyawan pemilik RFID ini telah dimemiliki akses ke dalam module sistem! <i class="fa fa-remove"></i>'
        });
        $scope.rfidcode = "";
        $scope.diterima_oleh = '';
        $scope.diterima_id = '';
      } else {
        $scope.diterima_oleh = data.Nama;
        $scope.diterima_id = data.IDKaryawan;
        $('#myModal').modal('hide');
      }
    });
  }

  $scope.getdata = function() {
    $http.get('api/pengiriman-barang/pengiriman-barang.php?act=LoadAllRequirement&id_proyek=' + $scope.id_proyek).success(function(data, status) {
      $scope.data_proyek = data.proyek;
      $scope.data_barang = data.barang;
      $scope.data_po = data.po;
    });
  };

  $scope.getdata();

  $scope.loadListPO = function() {
    $scope.po = "";
    $scope.getdata();
  }

  $scope.displayDetailPO = function() {
    $http.get('api/pengiriman-barang/pengiriman-barang.php?act=LoadDetailPO&idPO=' + $scope.po).success(function(data, status) {
      cartArray = [];
      cartArray = data;
      console.log(cartArray);
      $scope.displayCart();
    });
  }

  $scope.usrlogin = $rootScope.userLoginName;
  $scope.tanggal = $rootScope.currentDateID;

  $("#kode").on("change", function(e) {
    $scope.kode = this.value;
    $scope.changeKode();
  });

  $scope.changeKode = function() {
    if ($scope.kode != "") {
      $scope.anama = $scope.data_barang[$scope.kode].Nama;
      $scope.aidbarang = $scope.data_barang[$scope.kode].IDBarang;
      $scope.isserialize = $scope.data_barang[$scope.kode].IsSerialize;

      $scope.stokpurchasing = parseInt($scope.data_barang[$scope.kode].StokPurchasing);
      $scope.stokgudang = parseInt($scope.data_barang[$scope.kode].StokGudang);

      $scope.limit = $scope.stokpurchasing + $scope.stokgudang;

      $('#nama_barang').val($scope.anama);
      $scope.qty = parseInt($scope.stokpurchasing);
      $scope.serialnumber = "";

      if ($scope.data_barang[$scope.kode].IsSerialize == "1") {
        $scope.disabledQty = true;
        $scope.disabledSN = false;
      } else {
        $scope.disabledQty = false;
        $scope.disabledSN = true;
      }
    } else {
      $('#nama_barang').val("");
      $scope.qty = "";
      $scope.serialnumber = "";
    }
  }

  $scope.noMoreThanLimit = function() {
    if ($scope.qty > $scope.limit)
      $scope.qty = $scope.limit;
  }

  $scope.addtocart = function() {
    if ($scope.id_proyek == "0") {
      ngToast.create({
        className: 'danger',
        content: 'Silahkan pilih Data Proyek sebelum anda menambahkan daftar pengiriman stok barang. <i class="fa fa-remove"></i>'
      });
    } else {
      if ($scope.aidbarang > 0) {
        var IDBarang = $scope.aidbarang;
        var NamaBarang = $scope.anama;
        var Qty = $scope.qty;
        var Limit = $scope.limit;
        var StokPurchasing = $scope.stokpurchasing;
        var StokGudang = $scope.stokgudang;
        var SN = $scope.serialnumber;
        var IsSerialize = $scope.isserialize;
        var updated = false;

        if (IsSerialize == 0) {
          cartArray.forEach(function(entry) {
            if (IDBarang == entry["IDBarang"]) {
              updated = true;
              entry["QtyBarang"] += parseFloat(Qty);
            }
          });
        }

        if (!updated) {
          cartArray[noUrut] = { NoUrut: noUrut, IDBarang: IDBarang, NamaBarang: NamaBarang, QtyBarang: Qty, SNBarang: SN, IsSerialize: IsSerialize, Limit: Limit, StokPurchasing: StokPurchasing, StokGudang: StokGudang };
          noUrut += 1;
        }

        $('#nama_barang').val('');
        $scope.qty = "";
        $scope.serialnumber = "";
        $scope.isserialize = "";
        $scope.anama = "";
        $scope.aidbarang = 0;
        $scope.kode = "";
        $scope.limit = "";
        $scope.stokpurchasing = "";
        $scope.stokgudang = "";

        $scope.displayCart();
      } else {
        alert('Ada sesuatu yang salah. Silahkan ulang pilih data barang!');
      }
    }
  }

  $scope.displayCart = function() {
    console.log(cartArray);
    var lastIDBarang = "";

    function sortFunction(a, b) {
      if (a['NoUrut'] == b['NoUrut']) {
        return 0;
      } else {
        return (a['NoUrut'] < b['NoUrut']) ? -1 : 1;
      }
    }
    $scope.totalitem = 0;
    $scope.totaljenisitem = 0;
    $scope.displayCartArray = cartArray.filter(function() {
      return true
    });
    $scope.displayCartArray = $scope.displayCartArray.sort(sortFunction);
    $scope.displayCartArray.forEach(function(entry) {
      $scope.totalitem += parseFloat(entry["QtyBarang"]);
      if (lastIDBarang != entry["IDBarang"]) {
        $scope.totaljenisitem++;
        lastIDBarang = entry["IDBarang"];
      }
    });
  }

  $scope.changeQty = function(a) {
    var QtyVal = parseFloat($('#QtyBarang' + a).val());
    if (QtyVal > cartArray[a]['Limit']) QtyVal = cartArray[a]['Limit'];
    cartArray[a]['QtyBarang'] = QtyVal;
    $('#QtyBarang' + a).val(QtyVal);
    $scope.displayCart();
  }

  $scope.changeSN = function(a) {
    var SNVal = $('#SNBarang' + a).val();
    cartArray[a]['SNBarang'] = SNVal;
    $scope.displayCart();
  }

  $scope.removeRow = function(a) {
    delete cartArray[a];
    $scope.displayCart();

    return false;
  };

  $scope.processing = false;
  $scope.submitForm = function(isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/pengiriman-barang/pengiriman-barang.php?act=InsertNew',
        data: $.param({
          'id_proyek': $scope.id_proyek,
          'tanggal': $scope.tanggal,
          'totalqty': $scope.totalitem,
          'keterangan': $scope.keterangan,
          'status_pengiriman': $scope.statusPengiriman,
          'diterima_id': $scope.diterima_id,
          'cart': JSON.stringify(cartArray)
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function(data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data pengiriman berhasil disimpan <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-pengiriman-barang/';
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data pengiriman gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('PengirimanBarangDetailController', function($scope, $route, $routeParams, $http, ngToast) {
  $http.get('api/pengiriman-barang/pengiriman-barang.php?act=Detail&id=' + $routeParams.pbarangId).success(function(data, status) {
    $scope.NoPengiriman = data.master.NoPengiriman;
    $scope.NoPO = data.master.NoPO;
    $scope.Proyek = data.master.Proyek;
    $scope.Supplier = data.master.Supplier;
    $scope.Tanggal = data.master.Tanggal;
    $scope.usrlogin = data.master.usrlogin;
    $scope.Total = data.master.Total;
    $scope.GrandTotal = data.master.GrandTotal;
    $scope.Keterangan = data.master.Keterangan;
    $scope.RecievedBy = data.master.RecievedBy;
    $scope.DateTimeRecieved = data.master.DateTimeRecieved;
    $scope.Status = data.master.Status;

    $scope.data_detail = data.detail;
  });
});
