tripApp.controller('InvoiceProyekController', function ($scope, $rootScope, $routeParams, $route, $http, ngToast) {

  $scope.getdata = function () {
    $http.get('api/invoice-proyek/data-invoice.php?id_proyek=' + $routeParams.idPenjualan).success(function (data, status) {
      $scope.data_invoice = data.data;
      $scope.GrandTotal = data.GrandTotal;
      $scope.GrandTotalInvoice = data.GrandTotalInvoice;
      $scope.PiutangProgress = data.PiutangProgress;
      $scope.SisaPenagihan = data.SisaPenagihan;
      $scope.DetailPenjualan = data.DetailPenjualan;
    });
  };

  $scope.getdata();
  $scope.idPenjualan = $routeParams.idPenjualan;

  $scope.paidRow = function (val) {
    if (confirm("Anda yakin ingin menandakan invoice ini telah dibayar ? Menandakan secara manual dapat menghilangkan sinkronisasi data pada pembayaran invoice.")) {
      $http({
        method: "POST",
        url: 'api/invoice-proyek/set-paid.php',
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
            content: 'Status Invoice berhasil diubah ! <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Status Invoice gagal diubah. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }

  $scope.removeRow = function (val) {
    if (confirm("Anda yakin ingin menghapus data ini?")) {
      $http({
        method: "POST",
        url: 'api/invoice-proyek/delete.php',
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
            content: 'Data invoice berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else if (data == "2") {
          ngToast.create({
            className: 'danger',
            content: 'Data invoice gagal dihapus karena terintegrasi dengan pembayaran invoice... <i class="fa fa-remove"></i>'
          });
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data invoice gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }

  $scope.doPrint = function (a) {
    window.open($rootScope.baseURL + 'api/print/print-invoice.php?id=' + a, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }

  $scope.showModal2 = function (a) {
    $http.get('api/invoice-proyek/data-pembayaran.php?id=' + a.IDInvoice).success(function (data, status) {
      $scope.NoInv = a.NoInv;
      $scope.detailpembayaran = data.DetailPembayaran;
      $scope.GrandTotal = data.GrandTotal;
      $scope.Terbayar = data.Terbayar;
      $scope.Sisa = data.Sisa;
    });
    $('#myModal2').modal('show');
    $('#myModal2').children('.modal-dialog').removeClass('modal-lg');
  };

  $scope.closeModal2 = function () {
    $('#myModal2').modal('hide');
  }
});

tripApp.controller('InvoiceController', function ($scope, $rootScope, $routeParams, $route, $http, ngToast, CommonServices) {

  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();
  $scope.filterstatus = "";
  $scope.activeMenu = '';
  $scope.spb = '';
  $scope.jenis = '';
  $scope.pelanggan = '';
  $scope.marketing = '';

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.getdata = function () {
    $http.get('api/invoice-proyek/data-invoice-all.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&kode_proyek=' + $scope.kode_proyek + '&departement=' + $scope.departement + '&filterstatus=' + $scope.filterstatus + '&spb=' + $scope.spb + '&jenis=' + $scope.jenis + '&pelanggan=' + $scope.pelanggan + '&marketing=' + $scope.marketing).success(function (data, status) {
      $scope.data_invoice = data.data;
      $scope.data_penjualan = data.penjualan;
      $scope.data_pelanggan = data.pelanggan;
      $scope.data_marketing = data.marketing;

      $scope.all = data.all;
      $scope.lunas = data.lunas;
      $scope.hutang = data.hutang;
    });
  };

  $scope.departement = "";
  $scope.kode_proyek = "";

  $scope.refreshData = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }

  $scope.getdata();

  $scope.doFilter = function (a) {
    $scope.filterstatus = a;
    $scope.activeMenu = a;
    $scope.refreshData();
  }

  $scope.removeRow = function (val) {
    if (confirm("Anda yakin ingin menghapus data ini?")) {
      $http({
        method: "POST",
        url: 'api/invoice-proyek/delete.php',
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
            content: 'Data invoice berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else if (data == "2") {
          ngToast.create({
            className: 'danger',
            content: 'Data invoice gagal dihapus karena terintegrasi dengan jurnal... <i class="fa fa-remove"></i>'
          });
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data invoice gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }

  $scope.doPrint = function (a) {
    window.open($rootScope.baseURL + 'api/print/print-invoice.php?id=' + a, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }

  $scope.doPrint2 = function () {
    window.open($rootScope.baseURL + 'api/print/print-rekap-invoice2.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&kode_proyek=' + $scope.kode_proyek + '&departement=' + $scope.departement + '&filterstatus=' + $scope.filterstatus + '&spb=' + $scope.spb + '&jenis=' + $scope.jenis + '&pelanggan=' + $scope.pelanggan + '&marketing=' + $scope.marketing, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }

  $scope.doPrint4 = function () {
    window.open($rootScope.baseURL + 'api/print/print-piutang-pelanggan.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }

  $scope.showModal2 = function (a) {
    $http.get('api/invoice-proyek/data-pembayaran.php?id=' + a.IDInvoice).success(function (data, status) {
      $scope.NoInv = a.NoInv;
      $scope.detailpembayaran = data.DetailPembayaran;
      $scope.GrandTotal = data.GrandTotal;
      $scope.Terbayar = data.Terbayar;
      $scope.Sisa = data.Sisa;
    });
    $('#myModal2').modal('show');
    $('#myModal2').children('.modal-dialog').removeClass('modal-lg');
  };

  $scope.closeModal2 = function () {
    $('#myModal2').modal('hide');
  }
});

tripApp.controller('InvoiceProyekNewController', function ($scope, $rootScope, $routeParams, $route, $http, ngToast) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.tanggal = $rootScope.currentDateID;
  $scope.sisaPenagihan = 0;
  $scope.diskon_persen = 0;
  $scope.ppn_persen = 0;
  $scope.per_penagihan = 0;
  $scope.total = 0;
  $scope.isPajak = '0';

  var cartArray = [];
  var noUrut = 0;
  $scope.displayCartArray = [];
  $scope.totalItem = 0;
  $scope.toplist = [];
  $scope.top = "";

  $scope.note1 = "BANK CENTRAL ASIA ( BCA )\nNo. 772 586 8880 \na.n. CV. SOLUSI PEMANAS AIR NUSANTARA";
  $scope.note2 = "PERHATIAN\nTIDAK MENERIMA PEMBAYARAN TUNAI\nBARANG YANG SUDAH DIBELI TIDAK DAPAT DITUKAR/DIKEMBALIKAN\nCEK/BG DITULIS AN : CV. SOLUSI PEMANAS AIR NUSANTARA";

  $scope.idPenjualan = $routeParams.idPenjualan;

  $scope.getdata = function () {
    $http.get('api/invoice-proyek/loadallrequirement.php').success(function (data, status) {
      $scope.data_penjualan = data.penjualan;
      $scope.data_surat_jalan2 = data.surat_jalan;
      console.log($scope.data_penjualan);
    });
  };

  $scope.getdata();

  $("#kode").on("change", function (e) {
    $scope.kode = this.value;
    $scope.changeKode();
  });

  $scope.changeKode = function () {
    if ($scope.kode != "") {
      $scope.anama = $scope.data_barang[$scope.kode].Nama;
      $scope.aidbarang = $scope.data_barang[$scope.kode].IDBarang;
      $scope.isserialize = $scope.data_barang[$scope.kode].IsSerialize;

      $scope.limit = parseInt($scope.data_barang[$scope.kode].Limit);
      $scope.harga = $scope.data_barang[$scope.kode].HargaJual;
      $scope.HPP = $scope.data_barang[$scope.kode].HPP;

      $scope.Diskon = $scope.data_barang[$scope.kode].Diskon;
      $scope.HargaDiskon = $scope.data_barang[$scope.kode].HargaDiskon;

      $('#nama_barang').val($scope.anama);
      $scope.qty = 1;
      $scope.serialnumber = "";
    }
  }

  $scope.addtocart = function () {
    if ($scope.aidbarang > 0) {
      var IDBarang = $scope.aidbarang;
      var NamaBarang = $scope.anama;
      var Qty = parseFloat($scope.qty);
      var Limit = $scope.limit;
      var SN = $scope.serialnumber;
      var Harga = parseFloat($scope.harga);
      var Diskon = $scope.Diskon;
      var HargaDiskon = parseFloat($scope.HargaDiskon);
      var HPP = parseFloat($scope.HPP);
      var IsSerialize = $scope.isserialize;
      var updated = false;
      var SubTotal = HargaDiskon * Qty;
      var Margin = SubTotal - (HPP * Qty);

      if (IsSerialize == 0) {
        cartArray.forEach(function (entry) {
          if (IDBarang == entry["IDBarang"]) {
            updated = true;
            // entry["QtyBarang"] = parseInt(entry["QtyBarang"]) + parseInt(Qty);
          }
        });
      }

      if (!updated) {
        cartArray[noUrut] = { NoUrut: noUrut, IDBarang: IDBarang, NamaBarang: NamaBarang, NamaBarangDisplay: NamaBarang, QtyBarang: Qty, SNBarang: SN, IsSerialize: IsSerialize, Limit: Limit, Harga: Harga, HPP: HPP, Margin: Margin, SubTotal: SubTotal, Diskon: Diskon, HargaDiskon: HargaDiskon };
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
      $scope.harga = "";
      $scope.HPP = "";

      $scope.displayCart();
      console.log(cartArray);
    } else {
      alert('Ada sesuatu yang salah. Silahkan ulang pilih data barang!');
    }
  }

  $scope.displayCart = function () {
    var lastIDBarang = "";

    function sortFunction(a, b) {
      if (a['NoUrut'] == b['NoUrut']) {
        return 0;
      } else {
        return (a['NoUrut'] < b['NoUrut']) ? -1 : 1;
      }
    }
    $scope.total = 0;
    $scope.totalItem = 0;
    $scope.totalHPP = 0;
    $scope.totalMargin = 0;
    $scope.displayCartArray = cartArray.filter(function () {
      return true
    });
    $scope.displayCartArray = $scope.displayCartArray.sort(sortFunction);
    $scope.displayCartArray.forEach(function (entry) {
      if (entry["isParent"] !== 1)
        $scope.totalItem += parseFloat(entry["QtyBarang"]);
      $scope.totalHPP += (parseFloat(entry["QtyBarang"]) * parseFloat(entry["HPP"]));
      $scope.total = parseFloat($scope.total.toString().replace(/,/g, "")) + parseFloat(entry["SubTotal"].toString().replace(/,/g, ""));
    });
    $scope.totalMargin = $scope.total - $scope.totalHPP;
    $scope.nilaiJual = $scope.total;
    $scope.countPenagihanPersen();
    $scope.calcGrandTotal();
  }

  $scope.changeQty = function (a) {
    var QtyVal = parseFloat($('#QtyBarang' + a).val());
    var HPP = parseFloat(cartArray[a]['HPP']);
    var Harga = parseFloat(cartArray[a]["Harga"].toString().replace(/,/g, ""));
    var Diskon = parseFloat(cartArray[a]['Diskon']);

    cartArray[a]['QtyBarang'] = QtyVal;
    cartArray[a]["SubTotal"] = (Harga - Diskon) * QtyVal;

    cartArray[a]["Margin"] = parseFloat(cartArray[a]["SubTotal"]) - (HPP * QtyVal);
    $scope.displayCart();
  }

  $scope.removeRow = function (a) {
    delete cartArray[a];
    $scope.displayCart();

    return false;
  };

  $scope.changeTOP = function () {
    $scope.per_penagihan = $scope.top;
    //console.log($scope.top);
    $scope.countPenagihanPersen()
    $scope.calcGrandTotal();
  }

  $scope.countPersen = function () {
    var total = parseFloat($scope.jumlah);
    var ppn_persen = parseFloat($scope.ppn_persen);
    var ppn = ppn_persen / 100 * total;
    $scope.ppn = ppn;
    $scope.grand_total = total + ppn;
  }

  $scope.countPenagihanPersen = function () {
    var NilaiJulan = parseFloat($scope.nilaiJual);
    var PersenPenagihan = parseFloat($scope.per_penagihan);
    var TotalPenagihan = parseFloat($scope.jumlah);

    $scope.jumlah = Math.round(NilaiJulan * (PersenPenagihan / 100));
    $scope.calcGrandTotal();
  }


  $scope.calcGrandTotal = function () {
    var jumlah = parseFloat($scope.jumlah);

    var d = $scope.diskon_persen.toString();
    var DiskonPersen = d.split("+");
    var totalDiskon = 0;
    var totalNilai = jumlah;
    var diskon = 0;
    for (i = 0; i < DiskonPersen.length; i++) {
      diskon = Math.round(((parseFloat(DiskonPersen[i]) / 100) * totalNilai));
      totalNilai = totalNilai - diskon;
      totalDiskon = totalDiskon + diskon;
    }

    $scope.diskon = totalDiskon;
    $scope.jumlah2 = totalNilai;

    var jumlah2 = parseFloat($scope.jumlah2);
    var ppn_persen = parseFloat($scope.ppn_persen);
    var ppn = jumlah2 * ppn_persen / 100;
    $scope.ppn = ppn;
    $scope.grand_total = jumlah2 + ppn;

    var NilaiJulan = parseFloat($scope.nilaiJual);
    var PersenPenagihan = parseFloat($scope.per_penagihan);

    // if (jumlah == "0")
    //   $scope.per_penagihan = "0";
    // else
    //   $scope.per_penagihan = jumlah / NilaiJulan * 100;
  }

  $scope.changeSPH = function () {
    //console.log("SPH",$scope.spb2);
    $scope.data_surat_jalan = [];
    cartArray = [];
    noUrut = 0;
    $scope.displayCartArray = [];
    $scope.totalItem = 0;
    $scope.displayCart();

    $scope.toplist = $scope.data_penjualan[$scope.spb2]['TOP'];
    $scope.data_barang = $scope.data_penjualan[$scope.spb2]['BarangList'];
    if (typeof $scope.toplist[0] === 'undefined')
      $scope.top = "";
    else
      $scope.top = $scope.toplist[0].Value;
    $scope.per_penagihan = $scope.top;

    $scope.spb = $scope.data_penjualan[$scope.spb2]['IDPenjualan'];
    $scope.nilaiJual = $scope.data_penjualan[$scope.spb2]['nilaiJual'];
    $scope.nilaiJual2 = $scope.data_penjualan[$scope.spb2]['nilaiJual'];
    $scope.totalPenagihan = $scope.data_penjualan[$scope.spb2]['totalPenagihan'];
    $scope.sisaPenagihan = $scope.data_penjualan[$scope.spb2]['SisaPenagihan'];

    $scope.diskon_persen = $scope.data_penjualan[$scope.spb2]['Diskon'];
    $scope.ppn_persen = $scope.data_penjualan[$scope.spb2]['PPN'];

    if ($scope.ppn_persen > 0) {
      $scope.note1 = "BANK NEGARA INDONESIA ( BNI )\nNo. 8915 8915 91 \na.n. CV. LINTAS DAYA";
      $scope.note2 = "PERHATIAN\nTIDAK MENERIMA PEMBAYARAN TUNAI\nBARANG YANG SUDAH DIBELI TIDAK DAPAT DITUKAR/DIKEMBALIKAN\nCEK/BG DITULIS AN : CV. LINTAS DAYA";
      $scope.isPajak = '1';
    } else {
      $scope.note1 = "BANK CENTRAL ASIA ( BCA )\nNo. 772 586 8880 \na.n. CV. SOLUSI PEMANAS AIR NUSANTARA";
      $scope.note2 = "PERHATIAN\nTIDAK MENERIMA PEMBAYARAN TUNAI\nBARANG YANG SUDAH DIBELI TIDAK DAPAT DITUKAR/DIKEMBALIKAN\nCEK/BG DITULIS AN : CV. SOLUSI PEMANAS AIR NUSANTARA";
      $scope.isPajak = '0';
    }

    //console.log("SPH",$scope.data_penjualan[$scope.spb2]);

    if ($scope.total > 0) {
      $scope.countPenagihanPersen();
      $scope.calcGrandTotal();
    }

    for (var i in $scope.data_surat_jalan2) {
      if ($scope.data_surat_jalan2[i]['IDPenjualan'] === $scope.spb && $scope.data_surat_jalan2[i]['IsInvoiced'] === 0) {
        $scope.data_surat_jalan.push({
          'IDSuratJalan': $scope.data_surat_jalan2[i]['IDSuratJalan'],
          'NoSuratJalan': $scope.data_surat_jalan2[i]['NoSuratJalan'],
          'IDPenjualan': $scope.data_surat_jalan2[i]['IDPenjualan'],
          'NoPenjualan': $scope.data_surat_jalan2[i]['NoPenjualan'],
          'GrandTotal': $scope.data_surat_jalan2[i]['GrandTotal'],
          'Cart': $scope.data_surat_jalan2[i]['Cart'],
          'CartNo': $scope.data_surat_jalan2[i]['CartNo']
        });
      }
    }
  }

  /*$scope.calcTotal = function() {
    //console.log($scope.surat_jalan);
    $scope.jumlah = 0;
    $scope.surat_jalan_selected = [];
    for (var i in $scope.surat_jalan) {
      $scope.jumlah += parseFloat($scope.data_surat_jalan[i]['GrandTotal']);
      $scope.surat_jalan_selected.push($scope.data_surat_jalan[i]['IDSuratJalan']);
    }
    //console.log($scope.surat_jalan_selected);
    $scope.calcGrandTotal();
  }

  $scope.calcTotal();*/

  $scope.changeDO = function () {
    var i = $scope.surat_jalan;
    if (typeof $scope.data_surat_jalan[i] !== 'undefined') {
      $scope.surat_jalan_selected = $scope.data_surat_jalan[i]['IDSuratJalan'];
      cartArray = $scope.data_surat_jalan[i]['Cart'];
      console.log($scope.data_surat_jalan[i]['Cart']);
      noUrut = $scope.data_surat_jalan[i]['CartNo'];
      console.log($scope.data_surat_jalan[i]['CartNo']);
      $scope.displayCart();
    }
  }

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/invoice-proyek/new.php',
        data: $.param({
          'noinv': $scope.noinv,
          'NoFakturPajak': $scope.NoFakturPajak,
          'TOP': $scope.top,
          'spb': $scope.spb,
          'suratJalan': $scope.surat_jalan_selected,
          'tanggal': $scope.tanggal,
          'jatuh_tempo': $scope.jatuh_tempo,
          'jumlah_persen': $scope.per_penagihan,
          'jumlah': $scope.jumlah,
          'diskon_persen': $scope.diskon_persen,
          'diskon': $scope.diskon,
          'jumlah2': $scope.jumlah2,
          'ppn_persen': $scope.ppn_persen,
          'ppn': $scope.ppn,
          'grand_total': $scope.grand_total,
          'keterangan': $scope.keterangan,
          'terbilang': $scope.terbilang,
          'note1': $scope.note1,
          'note2': $scope.note2,
          'sign': $scope.sign,
          'npwp': $scope.npwp,
          'IsPajak': $scope.isPajak,
          'uploaded': $scope.userLoginID,
          'cart': JSON.stringify(cartArray)
        }),
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).success(function (data, status) {
        if (data.status == 1) {
          ngToast.create({
            className: 'success',
            content: 'Data Invoice berhasil disimpan <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-invoice/';
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: data.msg + ' <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };

  $scope.edited = false;
});

tripApp.controller('InvoiceProyekEditController', function ($scope, $rootScope, $route, $routeParams, $http, ngToast) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.edited = true;

  var cartArray = [];
  var noUrut = 0;
  $scope.displayCartArray = [];
  $scope.totalItem = 0;



  $scope.processing = false;
  $http.get('api/invoice-proyek/detail.php?id=' + $routeParams.invoiceid).success(function (data, status) {
    $scope.data_penjualan = data.penjualan;
    $scope.data_surat_jalan = data.surat_jalan;

    $scope.Pelanggan = data.detail.Pelanggan;
    $scope.NoPenjualan = data.detail.NoPenjualan;

    $scope.tanggal = data.detail.tanggal;
    $scope.jatuh_tempo = data.detail.jatuh_tempo;
    $scope.per_penagihan = data.detail.per_penagihan;
    $scope.jumlah = data.detail.jumlah;
    $scope.ppn_persen = data.detail.PPNPersen;
    $scope.ppn = data.detail.PPN;
    $scope.grand_total = data.detail.GrandTotal;
    $scope.keterangan = data.detail.Keterangan;

    $scope.spb = data.detail.id_penjualan;
    $scope.changeSPH();

    $scope.surat_jalan_selected = data.detail.surat_jalan;
    //$scope.revertTotal();

    $scope.top = data.detail.top;

    cartArray = data.detail.cart;
    $scope.displayCartArray = data.detail.cart;

    $scope.noinv = data.detail.noinv;
    $scope.terbilang = data.detail.Terbilang;
    $scope.note1 = data.detail.Note1;
    $scope.note2 = data.detail.Note2;
    $scope.sign = data.detail.Sign;
    $scope.npwp = data.detail.NPWP;
    $scope.isPajak = data.detail.IsPajak;

    $scope.nilaiJual2 = data.detail.nilaiJual2;
    $scope.total = data.detail.jumlah;

    $scope.diskon_persen = data.detail.DiskonPersen;
    $scope.diskon = data.detail.Diskon;
    $scope.jumlah2 = data.detail.Jumlah2;

    $scope.NoFakturPajak = data.detail.NoFakturPajak;

    if ($scope.note1 === null)
      $scope.note1 = '';

    if ($scope.note2 === null)
      $scope.note2 = '';

    $scope.prevState = $rootScope.previousState;
    if ($rootScope.previousState === "/data-invoice") {
      $scope.prevState = $rootScope.previousState;
    } else {
      $scope.prevState = "/data-invoice-penjualan/" + $scope.id_penjualan;
    }
  });

  $scope.countPenagihanPersen = function () {
    //alert("OKOKOK");
    var NilaiJulan = parseFloat($scope.nilaiJual);
    var PersenPenagihan = parseFloat($scope.per_penagihan);
    var TotalPenagihan = parseFloat($scope.jumlah);
    //console.log(PersenPenagihan);

    $scope.jumlah = Math.round(NilaiJulan * (PersenPenagihan / 100));
    //$scope.calcGrandTotal();
  }

  $scope.calcGrandTotal = function () {
    var jumlah = parseFloat($scope.jumlah);

    var d = $scope.diskon_persen.toString();
    var DiskonPersen = d.split("+");
    var totalDiskon = 0;
    var totalNilai = jumlah;
    var diskon = 0;
    for (i = 0; i < DiskonPersen.length; i++) {
      diskon = Math.round(((parseFloat(DiskonPersen[i]) / 100) * totalNilai));
      totalNilai = totalNilai - diskon;
      totalDiskon = totalDiskon + diskon;
    }

    $scope.diskon = totalDiskon;
    $scope.jumlah2 = totalNilai;

    var jumlah2 = parseFloat($scope.jumlah2);
    var ppn_persen = parseFloat($scope.ppn_persen);
    var ppn = jumlah2 * ppn_persen / 100;
    $scope.ppn = ppn;
    $scope.grand_total = jumlah2 + ppn;

    var NilaiJulan = parseFloat($scope.nilaiJual);
    var PersenPenagihan = parseFloat($scope.per_penagihan);

    // if (jumlah == "0")
    //   $scope.per_penagihan = "0";
    // else
    //   $scope.per_penagihan = jumlah / NilaiJulan * 100;
  }

  $scope.revertTotal = function () {
    console.log($scope.surat_jalan_selected);
    console.log($scope.data_surat_jalan);
    $scope.surat_jalan = [];
    for (var i in $scope.surat_jalan_selected) {
      for (var j in $scope.data_surat_jalan) {
        console.log($scope.data_surat_jalan[j]['IDSuratJalan'] + '/' + $scope.surat_jalan_selected[i] + '/' + j);
        if ($scope.data_surat_jalan[j]['IDSuratJalan'] === $scope.surat_jalan_selected[i]) {
          $scope.surat_jalan.push(j);
        }
      }
    }
    console.log($scope.surat_jalan);
  }

  $scope.calcTotal = function () {
    //console.log($scope.surat_jalan);
    $scope.jumlah = 0;
    $scope.surat_jalan_selected = [];
    for (var i in $scope.surat_jalan) {
      $scope.jumlah += parseFloat($scope.data_surat_jalan[i]['GrandTotal']);
      $scope.surat_jalan_selected.push($scope.data_surat_jalan[i]['IDSuratJalan']);
    }
    //console.log($scope.surat_jalan_selected);
  }

  $scope.changeSPH = function () {
    for (var i in $scope.data_penjualan) {
      if ($scope.data_penjualan[i]['IDPenjualan'] === $scope.spb) {
        $scope.spb2 = i;
        $scope.nilaiJual = $scope.data_penjualan[$scope.spb2]['nilaiJual'];
        $scope.totalPenagihan = $scope.data_penjualan[$scope.spb2]['totalPenagihan'];
        $scope.sisaPenagihan = $scope.data_penjualan[$scope.spb2]['SisaPenagihan'];
      }
    }
  }

  $scope.displayCart = function () {
    var lastIDBarang = "";
    function sortFunction(a, b) {
      if (a['NoUrut'] == b['NoUrut']) {
        return 0;
      } else {
        return (a['NoUrut'] < b['NoUrut']) ? -1 : 1;
      }
    }
    $scope.totalItem = 0;
    $scope.displayCartArray = cartArray.filter(function () {
      return true
    });
    $scope.displayCartArray = $scope.displayCartArray.sort(sortFunction);
    $scope.displayCartArray.forEach(function (entry) {
      if (entry["isParent"] !== 1)
        $scope.totalItem += parseFloat(entry["QtyBarang"]);
    });
  }

  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/invoice-proyek/edit.php',
        data: $.param({
          'noinv': $scope.noinv,
          'NoFakturPajak': $scope.NoFakturPajak,
          'jatuh_tempo': $scope.jatuh_tempo,
          'keterangan': $scope.keterangan,
          'note1': $scope.note1,
          'note2': $scope.note2,
          'sign': $scope.sign,
          'npwp': $scope.npwp,
          'isPajak': $scope.isPajak,
          'id': $routeParams.invoiceid
        }),
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).success(function (data, status) {
        if (data.status == 1) {
          ngToast.create({
            className: 'success',
            content: 'Data invoice berhasil diperbaharui <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-invoice';
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: data.msg + ' <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };

  $scope.showModal2 = function (NoInv) {
    $http.get('api/invoice-proyek/data-pembayaran.php?id=' + $routeParams.invoiceid).success(function (data, status) {
      $scope.NoInv = NoInv;
      $scope.detailpembayaran = data.DetailPembayaran;
      $scope.GrandTotal = data.GrandTotal;
      $scope.Terbayar = data.Terbayar;
      $scope.Sisa = data.Sisa;
    });
    $('#myModal2').modal('show');
    $('#myModal2').children('.modal-dialog').removeClass('modal-lg');
  };

  $scope.closeModal2 = function () {
    $('#myModal2').modal('hide');
  }
});
