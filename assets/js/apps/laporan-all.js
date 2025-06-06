tripApp.controller('LaporanStokKeluarMasuk', function ($scope, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();
  $scope.filter = "";
  $scope.keyword = "";

  $scope.doPrint3 = function () {
    window.open($rootScope.baseURL + 'api/print/print-stok-keluar-masuk.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&filter=' + $scope.filter + '&keyword=' + $scope.keyword, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('LaporanOutstandingSPB', function ($scope, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();

  $scope.doPrint3 = function () {
    window.open($rootScope.baseURL + 'api/print/print-rekap-penjualan.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('LaporanOutstandingPO', function ($scope, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();

  $scope.doPrint3 = function () {
    window.open($rootScope.baseURL + 'api/print/print-rekap-po.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('LaporanOutstandingInvoice', function ($scope, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();

  $scope.doPrint3 = function () {
    window.open($rootScope.baseURL + 'api/print/print-rekap-invoice.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('LaporanLabaRugi', function ($scope, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.bulan = CommonServices.currentMonth();
  $scope.tahun = CommonServices.currentYear().toString();
  $scope.years = CommonServices.yearList();



  $scope.doPrint3 = function () {
    window.open($rootScope.baseURL + 'api/print/print-laba-rugi.php?bulan=' + $scope.bulan + '&tahun=' + $scope.tahun, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('LaporanInvoiceJatuhTempo', function ($scope, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();

  $scope.getdata = function () {
    $http.get('api/laporan/laporan.php?act=LaporanInvoiceJatuhTempo&datestart=' + $scope.datestart + '&dateend=' + $scope.dateend).success(function (data, status) {
      $scope.data_report = data;
    });
  };

  $scope.getdata();

  $scope.doPrint3 = function () {
    window.open($rootScope.baseURL + 'api/print/print-rekap-penjualan.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('LaporanPenjualan', function ($scope, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();

  $scope.doPrint3 = function () {
    window.open($rootScope.baseURL + 'api/print/print-rekap-penjualan-periode.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('LaporanPenjualanSPB', function ($scope, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();
  $scope.sales = "";

  $scope.doPrint3 = function () {
    window.open($rootScope.baseURL + 'api/print/print-rekap-penjualan-spb-periode.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&sales=' + $scope.sales, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('LaporanPenjualanPerkategori', function ($scope, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();
  $scope.kategori = "";

  $scope.doPrint3 = function () {
    window.open($rootScope.baseURL + 'api/print/print-rekap-penjualan-spb-perkategori.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&kategori=' + $scope.kategori, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('LaporanPenjualanSJ', function ($scope, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();

  $scope.doPrint3 = function () {
    window.open($rootScope.baseURL + 'api/print/print-rekap-penjualan-sj-periode.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('LaporanHPPPeriode', function ($scope, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();

  $scope.doPrint3 = function () {
    window.open($rootScope.baseURL + 'api/print/print-laporan-hpp-periode.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});



tripApp.controller('LaporanDataProyekController', function ($scope, $route, $http, ngToast) {
  $scope.tahun = "";

  $scope.getdata = function () {
    $http.get('api/proyek/data-proyek.php?tahun=' + $scope.tahun).success(function (data, status) {
      $scope.data_proyek = data;
    });
  };

  $scope.getdata();

  $scope.filterdata = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }
});

tripApp.controller('LaporanOrderBarangController', function ($scope, $route, $http, ngToast) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true
  });
  $scope.datestart = "";
  $scope.dateend = "";
  $scope.kode_proyek = "";

  $scope.getdata = function () {
    $http.get('api/purchase-order/data-purchase-order.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&kode_proyek=' + $scope.kode_proyek).success(function (data, status) {
      $scope.data_purchase = data;
    });
  };

  $scope.getdata();

  $scope.getdataproyek = function () {
    $http.get('api/proyek/data-proyek.php').success(function (data, status) {
      $scope.data_proyek = data;
    });
  };

  $scope.getdataproyek();

  $scope.filterdata = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }
});

tripApp.controller('LaporanPembelianController', function ($scope, $route, $http, ngToast) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true
  });
  $scope.datestart = "";
  $scope.dateend = "";
  $scope.kode_proyek = "";

  $scope.getdata = function () {
    $http.get('api/pembelian/data-pembelian.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&kode_proyek=' + $scope.kode_proyek).success(function (data, status) {
      $scope.data_pembelian = data;
    });
  };

  $scope.getdata();

  $scope.getdataproyek = function () {
    $http.get('api/proyek/data-proyek.php').success(function (data, status) {
      $scope.data_proyek = data;
    });
  };

  $scope.getdataproyek();

  $scope.refreshData = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }
});

tripApp.controller('LaporanPengirimanController', function ($scope, $route, $http, ngToast) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true
  });
  $scope.datestart = "";
  $scope.dateend = "";
  $scope.kode_proyek = "";

  $scope.getdata = function () {
    $http.get('api/pengiriman-barang/data-pengiriman-barang.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&kode_proyek=' + $scope.kode_proyek).success(function (data, status) {
      $scope.data_pengiriman = data;
    });
  };

  $scope.getdata();

  $scope.getdataproyek = function () {
    $http.get('api/proyek/data-proyek.php').success(function (data, status) {
      $scope.data_proyek = data;
    });
  };

  $scope.getdataproyek();

  $scope.refreshData = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }
});

tripApp.controller('LaporanRekapSPBController', function ($scope, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();
  $scope.tipe = "108";

  $scope.doPrint3 = function () {
    window.open($rootScope.baseURL + 'api/print/print-rekap-spb.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&tipe=' + $scope.tipe, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('LabaRugiSPBController', function ($scope, $route, $routeParams, $http, ngToast, CommonServices) {
  $scope.IDPenjualan = $routeParams.id;
  $scope.urut = "1";

  $scope.tanggal = CommonServices.lastDateMonth();

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.doFilter = function () {
    $http.get('api/laporan/laporan.php?act=LaporanLabaRugi&id=' + $routeParams.id + '&urut=' + $scope.urut + '&tanggal=' + $scope.tanggal).success(function (data, status) {

      $scope.SPB = data.SPB;

      $scope.lTenaga = data.lTenaga;
      $scope.lPendapatan = data.lPendapatan;
      $scope.lPengiriman = data.lPengiriman;
      $scope.lMaterial = data.lMaterial;
      $scope.lOverhead = data.lOverhead;
      $scope.lPO = data.lPO;

      $scope.Pendapatan1 = data.Pendapatan1;
      $scope.Pendapatan2 = data.Pendapatan2;
      $scope.Pendapatan3 = data.Pendapatan3;

      $scope.Pengiriman1 = data.Pengiriman1;
      $scope.Pengiriman2 = data.Pengiriman2;
      $scope.Pengiriman3 = data.Pengiriman3;

      $scope.Tenaga1 = data.Tenaga1;
      $scope.Tenaga2 = data.Tenaga2;
      $scope.Tenaga3 = data.Tenaga3;

      $scope.Material1 = data.Material1;
      $scope.Material2 = data.Material2;
      $scope.Material3 = data.Material3;

      $scope.Pengeluaran1 = data.Pengeluaran1;
      $scope.Pengeluaran2 = data.Pengeluaran2;
      $scope.Pengeluaran3 = data.Pengeluaran3;

      $scope.Overhead1 = data.Overhead1;
      $scope.Overhead2 = data.Overhead2;
      $scope.Overhead3 = data.Overhead3;

      $scope.PO1 = data.PO1;
      $scope.PO2 = data.PO2;
      $scope.PO3 = data.PO3;

      $scope.Profit = data.Profit;

      $scope.PPN10 = data.PPN10;
      $scope.PPH2 = data.PPH2;
      $scope.DPP = data.DPP;
      $scope.TotalPajak = data.TotalPajak;
    });
  }

  $scope.doFilter();
});

tripApp.controller('LaporanPOSupplierController', function ($scope, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();

  $scope.doPrint3 = function () {
    window.open($rootScope.baseURL + 'api/print/print-po-supplier.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('LaporanStok', function ($scope, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();

  $scope.jenis_stok = "1";

  $scope.doPrint = function () {
    window.open($rootScope.baseURL + 'api/print/print-laporan-stok-barang.php?tipe=' + $scope.jenis_stok + '&datestart=' + $scope.datestart + '&dateend=' + $scope.dateend, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('LaporanKaryawanTahun', function ($scope, $routeParams, $rootScope, $route, $http, ngToast, CommonServices) {
  $scope.years = CommonServices.yearList();
  $scope.tahun = CommonServices.currentYear().toString();

  $scope.doPrint3 = function () {
    window.open($rootScope.baseURL + 'api/print/print-karyawan-tahunan.php?tahun=' + $scope.tahun, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});