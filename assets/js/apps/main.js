var tripApp = angular.module('tripApp', ['ngRoute', 'ngResource', 'ngToast', 'fcsa-number', 'ngFileUpload', 'ui.tinymce', 'ngIdle']);
var base_url = '/sopan/';

angular
  .module('tripApp')
  .config(['ngToastProvider', function (ngToast) {
    ngToast.configure({
      verticalPosition: 'bottom',
      horizontalPosition: 'center',
      animation: 'fade',
      maxNumber: 3
    });
  }]);

tripApp.config(['KeepaliveProvider', 'IdleProvider', function (KeepaliveProvider, IdleProvider) {
  IdleProvider.idle(1800);
  IdleProvider.timeout(1);
  KeepaliveProvider.interval(10);
}]);

tripApp.config(['$routeProvider', function ($routeProvider) {
  $routeProvider.
    when('/', {
      templateUrl: 'partial/index.html',
      controller: 'DashboardController',
      resolve: {
        access: ["PermisionAccess", function (PermisionAccess) {
          return PermisionAccess.allowAccess('general', 'read');
        }],
      }
    }).
    when('/login', {
      templateUrl: 'partial/trip-detail.html',
      controller: 'TripDetailController',
    }).


    when('/data-audit-stok', {
      templateUrl: 'partial/data-audit.html',
      controller: 'AuditController'
    }).
    when('/audit-stok/new/', {
      templateUrl: 'partial/audit-form.html',
      controller: 'AuditNewController'
    }).
    when('/audit-stok/detail/:id*', {
      templateUrl: 'partial/detail-audit.html',
      controller: 'AuditDetailController'
    }).

    when('/data-transfer-stok', {
      templateUrl: 'partial/data-transfer-stok.html',
      controller: 'TransferStokController'
    }).
    when('/transfer-stok/new/', {
      templateUrl: 'partial/transfer-stok-form.html',
      controller: 'TransferStokNewController'
    }).
    when('/transfer-stok/detail/:id*', {
      templateUrl: 'partial/detail-transfer-stok.html',
      controller: 'TransferStokDetailController'
    }).

    when('/data-public-holiday', {
      templateUrl: 'partial/data-public-holiday.html',
      controller: 'PublicHolidayController',
    }).
    when('/public-holiday/new', {
      templateUrl: 'partial/public-holiday-form.html',
      controller: 'PublicHolidayNewController',
    }).
    when('/public-holiday/edit/:id', {
      templateUrl: 'partial/public-holiday-form.html',
      controller: 'PublicHolidayEditController',
    }).

    when('/data-cuti-karyawan', {
      templateUrl: 'partial/data-cuti-karyawan.html',
      controller: 'CutiKaryawanController',
    }).
    when('/cuti-karyawan/new', {
      templateUrl: 'partial/cuti-karyawan-form.html',
      controller: 'CutiKaryawanNewController',
    }).
    when('/cuti-karyawan/edit/:idCuti', {
      templateUrl: 'partial/cuti-karyawan-form.html',
      controller: 'CutiKaryawanEditController',
    }).

    when('/laporan-data-absent', {
      templateUrl: 'partial/laporan-data-absent.html',
      controller: 'LaporanDataAbsentController',
    }).
    when('/laporan-data-absent/:bulan/:tahun/:karyawan', {
      templateUrl: 'partial/laporan-data-absent.html',
      controller: 'LaporanDataAbsentController',
    }).

    when('/data-absent-proyek', {
      templateUrl: 'partial/data-absent-proyek.html',
      controller: 'AbsentProyekController'
    }).

    when('/laporan-data-absen-karyawan', {
      templateUrl: 'partial/laporan-data-absen-karyawan.html',
      controller: 'LaporanDataAbsenKaryawanController',
    }).
    when('/laporan-cuti-karyawan', {
      templateUrl: 'partial/laporan-cuti-karyawan.html',
      controller: 'LaporanCutiKaryawanController',
    }).
    when('/laporan-cuti-karyawan/:tahun/:karyawan', {
      templateUrl: 'partial/laporan-cuti-karyawan.html',
      controller: 'LaporanCutiKaryawanController',
    }).
    when('/laporan-data-cuti-karyawan', {
      templateUrl: 'partial/laporan-data-cuti-karyawan.html',
      controller: 'LaporanDataCutiKaryawanController',
    }).
    when('/laporan-data-cuti-karyawan2', {
      templateUrl: 'partial/laporan-data-cuti-karyawan2.html',
      controller: 'LaporanDataCutiKaryawanController2',
    }).
    when('/rekap-cuti-karyawan-tahunan', {
      templateUrl: 'partial/rekap-cuti-karyawan-tahunan.html',
      controller: 'RekapCutiKaryawanTahunan',
    }).
    when('/rekap-keterlambatan-karyawan-tahunan', {
      templateUrl: 'partial/rekap-keterlambatan-karyawan-tahunan.html',
      controller: 'RekapKeterlambatanKaryawanTahunan',
    }).

    when('/data-gaji-karyawan/', {
      templateUrl: 'partial/data-gaji-karyawan.html',
      controller: 'GajiKaryawanController',
    }).
    when('/gaji-karyawan/:karyawanId', {
      templateUrl: 'partial/gaji-karyawan-form.html',
      controller: 'GajiKaryawanNewController',
    }).

    when('/data-payroll', {
      templateUrl: 'partial/data-payroll.html',
      controller: 'PayrollController',
    }).
    when('/data-payroll/:bulan/:tahun', {
      templateUrl: 'partial/data-payroll.html',
      controller: 'PayrollController',
    }).
    when('/payroll/new/:param', {
      templateUrl: 'partial/payroll-form.html',
      controller: 'PayrollNewController',
    }).

    when('/laporan-summary-gaji-karyawan', {
      templateUrl: 'partial/laporan-summary-gaji-karyawan.html',
      controller: 'LaporanSummaryGajiKaryawanController',
    }).
    when('/laporan-gaji-karyawan', {
      templateUrl: 'partial/laporan-gaji-karyawan.html',
      controller: 'LaporanGajiKaryawanController',
    }).
    when('/laporan-detail-gaji-karyawan', {
      templateUrl: 'partial/laporan-detail-gaji-karyawan.html',
      controller: 'LaporanDetailGajiKaryawanController',
    }).

    when('/settings', {
      templateUrl: 'partial/setting-form.html',
      controller: 'SettingController'
    }).

    /* TRAINING RECORD */
    when('/data-training-record', {
      templateUrl: 'partial/data-training-record.html',
      controller: 'TrainingRecordController',
    }).
    when('/training-record/new', {
      templateUrl: 'partial/training-record-form.html',
      controller: 'TrainingRecordNewController',
    }).
    when('/training-record/edit/:tRecordId', {
      templateUrl: 'partial/training-record-form.html',
      controller: 'TrainingRecordEditController',
    }).

    /* PENGUMUMAN */
    when('/data-pengumuman', {
      templateUrl: 'partial/data-pengumuman.html',
      controller: 'PengumumanController',
    }).
    when('/pengumuman/new/', {
      templateUrl: 'partial/pengumuman-form.html',
      controller: 'PengumumanNewController',
    }).

    when('/surat-keluar', {
      templateUrl: 'partial/data-surat-keluar.html',
      controller: 'SuratKeluarController',
    }).
    when('/surat-keluar/new', {
      templateUrl: 'partial/surat-keluar-form.html',
      controller: 'SuratKeluarNewController',
    }).
    when('/surat-keluar/edit/:suratkeluarId', {
      templateUrl: 'partial/surat-keluar-form.html',
      controller: 'SuratKeluarEditController',
    }).

    when('/surat-masuk', {
      templateUrl: 'partial/data-surat-masuk.html',
      controller: 'SuratMasukController',
    }).
    when('/surat-masuk/new', {
      templateUrl: 'partial/surat-masuk-form.html',
      controller: 'SuratMasukNewController',
    }).
    when('/surat-masuk/edit/:suratmasukId', {
      templateUrl: 'partial/surat-masuk-form.html',
      controller: 'SuratMasukEditController',
    }).

    when('/data-surat-perintah-kerja', {
      templateUrl: 'partial/data-spk.html',
      controller: 'SPKController',
    }).
    when('/surat-perintah-kerja/new', {
      templateUrl: 'partial/spk-form.html',
      controller: 'SPKNewController',
    }).
    when('/surat-perintah-kerja/edit/:spkId', {
      templateUrl: 'partial/spk-form.html',
      controller: 'SPKEditController',
    }).

    when('/data-surat-perintah-perjalanan-dinas', {
      templateUrl: 'partial/data-sppd.html',
      controller: 'SPPDController',
    }).
    when('/surat-perintah-perjalanan-dinas/new', {
      templateUrl: 'partial/sppd-form.html',
      controller: 'SPPDNewController',
    }).
    when('/surat-perintah-perjalanan-dinas/edit/:sppdId', {
      templateUrl: 'partial/sppd-form.html',
      controller: 'SPPDEditController',
    }).

    when('/data-reimburse', {
      templateUrl: 'partial/data-reimburse.html',
      controller: 'ReimburseController',
    }).
    when('/reimburse/new', {
      templateUrl: 'partial/reimburse-form.html',
      controller: 'ReimburseNewController',
    }).
    when('/reimburse/edit/:reimburseId', {
      templateUrl: 'partial/reimburse-form.html',
      controller: 'ReimburseEditController',
    }).
    when('/laporan-data-reimburse', {
      templateUrl: 'partial/laporan-data-reimburse.html',
      controller: 'LaporanReimburseController',
    }).
    when('/laporan-rekap-reimburse', {
      templateUrl: 'partial/laporan-rekap-reimburse.html',
      controller: 'LaporanRekapReimburseController',
    }).
    when('/laporan-reimburse-kendaraan', {
      templateUrl: 'partial/laporan-reimburse-kendaraan.html',
      controller: 'LaporanReimburseKendaraan',
    }).

    when('/data-asset', {
      templateUrl: 'partial/data-asset.html',
      controller: 'AssetController',
    }).
    when('/asset/new', {
      templateUrl: 'partial/asset-form.html',
      controller: 'AssetNewController',
    }).
    when('/asset/edit/:assetId', {
      templateUrl: 'partial/asset-form.html',
      controller: 'AssetEditController',
    }).

    when('/data-asset-usaha', {
      templateUrl: 'partial/data-asset-usaha.html',
      controller: 'AssetUsahaController',
    }).
    when('/asset-usaha/new', {
      templateUrl: 'partial/asset-usaha-form.html',
      controller: 'AssetUsahaNewController',
    }).
    when('/asset-usaha/edit/:assetId', {
      templateUrl: 'partial/asset-usaha-form.html',
      controller: 'AssetUsahaEditController',
    }).

    when('/data-asset-category', {
      templateUrl: 'partial/data-asset-category.html',
      controller: 'AssetCategoryController',
    }).
    when('/assetcategory/new', {
      templateUrl: 'partial/asset-category-form.html',
      controller: 'AssetCategoryNewController',
    }).
    when('/assetcategory/edit/:assetcategoryId', {
      templateUrl: 'partial/asset-category-form.html',
      controller: 'AssetCategoryEditController',
    }).

    when('/data-assign-asset', {
      templateUrl: 'partial/data-assign-asset.html',
      controller: 'AssignAssetController',
    }).
    when('/assign-asset', {
      templateUrl: 'partial/assign-asset-form.html',
      controller: 'AssignAssetNewController',
    }).
    when('/assign-asset/detail/:idAssign', {
      templateUrl: 'partial/assign-asset-detail.html',
      controller: 'AssignAssetDetailController',
    }).

    when('/data-return-asset', {
      templateUrl: 'partial/data-return-asset.html',
      controller: 'ReturnAssetController',
    }).
    when('/return-asset', {
      templateUrl: 'partial/return-asset-form.html',
      controller: 'ReturnAssetNewController',
    }).
    when('/return-asset/detail/:idReturn', {
      templateUrl: 'partial/return-asset-detail.html',
      controller: 'ReturnAssetDetailController',
    }).

    when('/data-jadwal-perpanjangan-stnk', {
      templateUrl: 'partial/data-jadwal-perpanjangan-stnk.html',
      controller: 'AssetPerpanjanganSTNKController'
    }).
    when('/data-jadwal-perpanjangan-usaha', {
      templateUrl: 'partial/data-jadwal-perpanjangan-usaha.html',
      controller: 'AssetUsahaPerpanjanganIjinUsahaController'
    }).

    when('/data-kas-masuk', {
      templateUrl: 'partial/data-kas-masuk.html',
      controller: 'KasMasukController'
    }).
    when('/kas-masuk/new/', {
      templateUrl: 'partial/kas-masuk-form.html',
      controller: 'KasMasukNewController'
    }).
    when('/kas-masuk/edit/:id*', {
      templateUrl: 'partial/kas-masuk-form.html',
      controller: 'KasMasukEditController'
    }).

    when('/data-kas-keluar', {
      templateUrl: 'partial/data-kas-keluar.html',
      controller: 'KasKeluarController'
    }).
    when('/kas-keluar/new/', {
      templateUrl: 'partial/kas-keluar-form.html',
      controller: 'KasKeluarNewController'
    }).
    when('/kas-keluar/edit/:id*', {
      templateUrl: 'partial/kas-keluar-form.html',
      controller: 'KasKeluarEditController'
    }).

    when('/buku-kas-mms', {
      templateUrl: 'partial/buku-kas-mms.html',
      controller: 'BukuBesarMMSController'
    }).



    when('/data-invoice-penjualan/:idPenjualan', {
      templateUrl: 'partial/data-invoice-proyek.html',
      controller: 'InvoiceProyekController',
    }).
    when('/data-invoice', {
      templateUrl: 'partial/data-invoice.html',
      controller: 'InvoiceController',
    }).
    when('/invoice/new', {
      templateUrl: 'partial/invoice-proyek-form.html',
      controller: 'InvoiceProyekNewController',
    }).
    when('/invoice/edit/:invoiceid', {
      templateUrl: 'partial/invoice-proyek-form-edit.html',
      controller: 'InvoiceProyekEditController',
    }).

    when('/data-penerimaan-invoice', {
      templateUrl: 'partial/data-penerimaan-invoice.html',
      controller: 'PenerimaanInvoiceController',
    }).
    when('/penerimaan-invoice/new', {
      templateUrl: 'partial/penerimaan-invoice-form.html',
      controller: 'PenerimaanInvoiceNewController',
    }).
    when('/penerimaan-invoice/edit/:id', {
      templateUrl: 'partial/penerimaan-invoice-form.html',
      controller: 'PenerimaanInvoiceEditController',
    }).

    when('/data-notifikasi-maintenance', {
      templateUrl: 'partial/data-notifikasi-maintenance.html',
      controller: 'NotifikasiMaintenanceController',
    }).
    when('/notifikasi-maintenance/edit/:scheduleId', {
      templateUrl: 'partial/notifikasi-maintenance-form.html',
      controller: 'NotifikasiMaintenanceEditController',
    }).

    when('/data-work-schedule', {
      templateUrl: 'partial/data-work-schedule.html',
      controller: 'WorkScheduleController',
    }).
    when('/work-schedule/new', {
      templateUrl: 'partial/work-schedule-form.html',
      controller: 'WorkScheduleNewController',
    }).
    when('/work-schedule/edit/:scheduleId', {
      templateUrl: 'partial/work-schedule-form.html',
      controller: 'WorkScheduleEditController',
    }).

    when('/data-work-report', {
      templateUrl: 'partial/data-work-report.html',
      controller: 'WorkReportController',
    }).
    when('/data-work-report/:scheduleId', {
      templateUrl: 'partial/data-work-report.html',
      controller: 'WorkReportController',
    }).
    when('/work-report/new', {
      templateUrl: 'partial/work-report-form.html',
      controller: 'WorkReportNewController',
    }).
    when('/work-report/new/:scheduleId', {
      templateUrl: 'partial/work-report-form.html',
      controller: 'WorkReportNewController',
    }).
    when('/work-report/edit/:reportId', {
      templateUrl: 'partial/work-report-form.html',
      controller: 'WorkReportEditController',
    }).

    when('/data-surat-jalan', {
      templateUrl: 'partial/data-surat-jalan.html',
      controller: 'SuratJalanController',
    }).
    when('/data-rekap-surat-jalan', {
      templateUrl: 'partial/data-rekap-surat-jalan.html',
      controller: 'RekapSuratJalanController',
    }).
    when('/surat-jalan/new', {
      templateUrl: 'partial/surat-jalan-form.html',
      controller: 'SuratJalanNewController',
    }).
    when('/surat-jalan/detail/:idSuratJalan', {
      templateUrl: 'partial/detail-surat-jalan.html',
      controller: 'SuratJalanDetailController',
    }).
    /* BARANG */
    when('/data-barang', {
      templateUrl: 'partial/data-barang-mms.html',
      controller: 'BarangController',
      /*resolve: {
        access: ["PermisionAccess", function(PermisionAccess) {
          return PermisionAccess.allowAccess('barang', 'read');
        }],
      }*/
    }).
    when('/barang/new', {
      templateUrl: 'partial/barang-mms-form.html',
      controller: 'BarangNewController',
      /*resolve: {
        access: ["PermisionAccess", function(PermisionAccess) {
          return PermisionAccess.allowAccess('barang', 'write');
        }],
      }*/
    }).
    when('/barang/edit/:barangId', {
      templateUrl: 'partial/barang-mms-form.html',
      controller: 'BarangEditController',
      /*resolve: {
        access: ["PermisionAccess", function(PermisionAccess) {
          return PermisionAccess.allowAccess('barang', 'write');
        }],
      }*/
    }).

    /* KARYAWAN */
    when('/data-karyawan', {
      templateUrl: 'partial/data-karyawan.html',
      controller: 'KaryawanController',
      /*resolve: {
        access: ["PermisionAccess", function (PermisionAccess) {
          return PermisionAccess.allowAccess('karyawan', 'read');
        }],
      }*/
    }).
    when('/karyawan/new', {
      templateUrl: 'partial/karyawan-form.html',
      controller: 'KaryawanNewController',
      /*resolve: {
        access: ["PermisionAccess", function (PermisionAccess) {
          return PermisionAccess.allowAccess('karyawan', 'write');
        }],
      }*/
    }).
    when('/karyawan/edit/:karyawanId', {
      templateUrl: 'partial/karyawan-form.html',
      controller: 'KaryawanEditController',
      /*resolve: {
        access: ["PermisionAccess", function (PermisionAccess) {
          return PermisionAccess.allowAccess('karyawan', 'write');
        }],
      }*/
    }).
    when('/data-manage-device/:karyawanId', {
      templateUrl: 'partial/data-manage-device.html',
      controller: 'ManageDeviceController',
      /*resolve: {
        access: ["PermisionAccess", function (PermisionAccess) {
          return PermisionAccess.allowAccess('karyawan', 'write');
        }],
      }*/
    }).
    when('/manage-device/new/:karyawanId', {
      templateUrl: 'partial/manage-device-form.html',
      controller: 'ManageDeviceNewController',
      /*resolve: {
        access: ["PermisionAccess", function (PermisionAccess) {
          return PermisionAccess.allowAccess('karyawan', 'write');
        }],
      }*/
    }).
    when('/hak-akses', {
      templateUrl: 'partial/data-hak-akses.html',
      controller: 'HakAksesCtrl'
    }).
    when('/karyawan/profile/:karyawanId', {
      templateUrl: 'partial/karyawan-profile.html',
      controller: 'KaryawanEditController'
    }).
    when('/karyawan-harian/profile/:karyawanId', {
      templateUrl: 'partial/karyawan-profile.html',
      controller: 'KaryawanEditController'
    }).

    when('/laporan-karyawan-tahunan', {
      templateUrl: 'partial/laporan-karyawan-tahunan.html',
      controller: 'LaporanKaryawanTahun',
    }).

    when('/data-jadwal-perpanjangan-stnk', {
      templateUrl: 'partial/data-jadwal-perpanjangan-stnk.html',
      controller: 'AssetPerpanjanganSTNKController'
    }).
    when('/data-jadwal-perpanjangan-usaha', {
      templateUrl: 'partial/data-jadwal-perpanjangan-usaha.html',
      controller: 'AssetUsahaPerpanjanganIjinUsahaController'
    }).

    /* HISTORY TRACKING */
    when('/data-history-tracking', {
      templateUrl: 'partial/data-history-tracking.html',
      controller: 'HistoryTrackingController',
      /*resolve: {
        access: ["PermisionAccess", function(PermisionAccess) {
          return PermisionAccess.allowAccess('barang', 'read');
        }],
      }*/
    }).
    when('/history-tracking/new/', {
      templateUrl: 'partial/history-tracking-form.html',
      controller: 'HistoryTrackingNewController',
      /*resolve: {
        access: ["PermisionAccess", function(PermisionAccess) {
          return PermisionAccess.allowAccess('barang', 'write');
        }],
      }*/
    }).
    when('/history-tracking/edit/:historyId', {
      templateUrl: 'partial/history-tracking-form.html',
      controller: 'HistoryTrackingEditController',
      /*resolve: {
        access: ["PermisionAccess", function(PermisionAccess) {
          return PermisionAccess.allowAccess('barang', 'write');
        }],
      }*/
    }).

    /* PELANGGAN */
    when('/data-pelanggan', {
      templateUrl: 'partial/data-pelanggan.html',
      controller: 'PelangganController',
      /*resolve: {
        access: ["PermisionAccess", function(PermisionAccess) {
          return PermisionAccess.allowAccess('pelanggan', 'read');
        }],
      }*/
    }).
    when('/pelanggan/new', {
      templateUrl: 'partial/pelanggan-form.html',
      controller: 'PelangganNewController',
      /*resolve: {
        access: ["PermisionAccess", function(PermisionAccess) {
          return PermisionAccess.allowAccess('pelanggan', 'write');
        }],
      }*/
    }).
    when('/pelanggan/edit/:pelangganId', {
      templateUrl: 'partial/pelanggan-form.html',
      controller: 'PelangganEditController',
      /*resolve: {
        access: ["PermisionAccess", function(PermisionAccess) {
          return PermisionAccess.allowAccess('pelanggan', 'write');
        }],
      }*/
    }).

    /* SUPPLIER */
    when('/data-supplier', {
      templateUrl: 'partial/data-supplier.html',
      controller: 'SupplierController'
      /* resolve: {
         access: ["PermisionAccess", function(PermisionAccess) {
           return PermisionAccess.allowAccess('supplier', 'read');
         }],
       }*/
    }).
    when('/supplier/new', {
      templateUrl: 'partial/supplier-form.html',
      controller: 'SupplierNewController'
      /*resolve: {
        access: ["PermisionAccess", function(PermisionAccess) {
          return PermisionAccess.allowAccess('supplier', 'write');
        }],
      }*/
    }).
    when('/supplier/edit/:supplierId', {
      templateUrl: 'partial/supplier-form.html',
      controller: 'SupplierEditController'
      /*resolve: {
        access: ["PermisionAccess", function(PermisionAccess) {
          return PermisionAccess.allowAccess('supplier', 'write');
        }],
      }*/
    }).

    /* JENIS MATERIAL */
    when('/data-jenis-barang', {
      templateUrl: 'partial/data-jenis-barang.html',
      controller: 'MaterialController',
      /*resolve: {
        access: ["PermisionAccess", function(PermisionAccess) {
          return PermisionAccess.allowAccess('jenismaterial', 'read');
        }],
      }*/
    }).
    when('/jenis-barang/new', {
      templateUrl: 'partial/jenis-barang-form.html',
      controller: 'MaterialNewController',
      /*resolve: {
        access: ["PermisionAccess", function(PermisionAccess) {
          return PermisionAccess.allowAccess('jenismaterial', 'write');
        }],
      }*/
    }).
    when('/jenis-barang/edit/:materialId', {
      templateUrl: 'partial/jenis-barang-form.html',
      controller: 'MaterialEditController',
      /*resolve: {
        access: ["PermisionAccess", function(PermisionAccess) {
          return PermisionAccess.allowAccess('jenismaterial', 'write');
        }],
      }*/
    }).

    /* DEPARTMENT */
    when('/data-departement', {
      templateUrl: 'partial/data-departement.html',
      controller: 'DepartementController',
      /*resolve: {
        access: ["PermisionAccess", function(PermisionAccess) {
          return PermisionAccess.allowAccess('departement', 'read');
        }],
      }*/
    }).
    when('/departement/new', {
      templateUrl: 'partial/departement-form.html',
      controller: 'DepartementNewController',
      /*resolve: {
        access: ["PermisionAccess", function(PermisionAccess) {
          return PermisionAccess.allowAccess('departement', 'write');
        }],
      }*/
    }).
    when('/departement/edit/:departementId', {
      templateUrl: 'partial/departement-form.html',
      controller: 'DepartementEditController',
      /*resolve: {
        access: ["PermisionAccess", function(PermisionAccess) {
          return PermisionAccess.allowAccess('departement', 'write');
        }],
      }*/
    }).



    /* JABATAN */
    when('/data-jabatan', {
      templateUrl: 'partial/data-jabatan.html',
      controller: 'JabatanCtrl'
    }).
    when('/jabatan/new', {
      templateUrl: 'partial/jabatan-form.html',
      controller: 'JabatanNewCtrl'
    }).
    when('/jabatan/edit/:jabatanId', {
      templateUrl: 'partial/jabatan-form.html',
      controller: 'JabatanEditCtrl'
    }).

    /* JABATAN */
    when('/data-jabatan', {
      templateUrl: 'partial/data-jabatan.html',
      controller: 'JabatanCtrl'
    }).
    when('/jabatan/new', {
      templateUrl: 'partial/jabatan-form.html',
      controller: 'JabatanNewCtrl'
    }).
    when('/jabatan/edit/:jabatanId', {
      templateUrl: 'partial/jabatan-form.html',
      controller: 'JabatanEditCtrl'
    }).

    /* SATUAN */
    when('/data-satuan', {
      templateUrl: 'partial/data-satuan.html',
      controller: 'SatuanController',
      /*resolve: {
        access: ["PermisionAccess", function(PermisionAccess) {
          return PermisionAccess.allowAccess('satuan', 'read');
        }],
      }*/
    }).
    when('/satuan/new', {
      templateUrl: 'partial/satuan-form.html',
      controller: 'SatuanNewController',
      /*resolve: {
        access: ["PermisionAccess", function(PermisionAccess) {
          return PermisionAccess.allowAccess('satuan', 'write');
        }],
      }*/
    }).
    when('/satuan/edit/:satuanId', {
      templateUrl: 'partial/satuan-form.html',
      controller: 'SatuanEditController',
      /*resolve: {
        access: ["PermisionAccess", function(PermisionAccess) {
          return PermisionAccess.allowAccess('satuan', 'write');
        }],
      }*/
    }).

    when('/data-kategori-work-report', {
      templateUrl: 'partial/data-kategori-work-report.html',
      controller: 'KategoriWorkReportController',
    }).
    when('/kategori-work-report/new', {
      templateUrl: 'partial/kategori-work-report-form.html',
      controller: 'KategoriWorkReportNewController',
    }).
    when('/kategori-work-report/edit/:kategoriWorkReportId', {
      templateUrl: 'partial/kategori-work-report-form.html',
      controller: 'KategoriWorkReportEditController',
    }).

    when('/data-kategori-spb', {
      templateUrl: 'partial/data-kategori-spb.html',
      controller: 'KategoriSPBController',
    }).
    when('/kategori-spb/new', {
      templateUrl: 'partial/kategori-spb-form.html',
      controller: 'KategoriSPBNewController',
    }).
    when('/kategori-spb/edit/:kategoriSpbId', {
      templateUrl: 'partial/kategori-spb-form.html',
      controller: 'KategoriSPBEditController',
    }).

    when('/data-file-penjualan/:kategoriSpbId/:idPenjualan', {
      templateUrl: 'partial/data-file-penjualan.html',
      controller: 'FilePenjualanController'
    }).
    when('/file-penjualan/new/:kategoriSpbId/:idPenjualan', {
      templateUrl: 'partial/file-penjualan-form.html',
      controller: 'FilePenjualanNewController'
    }).
    when('/file-penjualan/edit/:kategoriSpbId/:idPenjualan/:id', {
      templateUrl: 'partial/file-penjualan-form.html',
      controller: 'FilePenjualanEditController'
    }).


    /* GUDANG */
    when('/data-gudang', {
      templateUrl: 'partial/data-gudang.html',
      controller: 'GudangController',
      /*resolve: {
        access: ["PermisionAccess", function(PermisionAccess) {
          return PermisionAccess.allowAccess('gudang', 'read');
        }],
      }*/
    }).
    when('/gudang/new', {
      templateUrl: 'partial/gudang-form.html',
      controller: 'GudangNewController',
      /*resolve: {
        access: ["PermisionAccess", function(PermisionAccess) {
          return PermisionAccess.allowAccess('gudang', 'write');
        }],
      }*/
    }).
    when('/gudang/edit/:gudangId', {
      templateUrl: 'partial/gudang-form.html',
      controller: 'GudangEditController',
      /*resolve: {
        access: ["PermisionAccess", function(PermisionAccess) {
          return PermisionAccess.allowAccess('gudang', 'write');
        }],
      }*/
    }).

    /* PURCHASING */
    when('/data-purchase-order/', {
      templateUrl: 'partial/data-purchase-order.html',
      controller: 'PurchaseOrderController',
      /*resolve: {
          access: ["PermisionAccess", function(PermisionAccess) {
              return PermisionAccess.allowAccess('purchasing', 'read');
          }],
      }*/
    }).
    when('/purchase-order/new/', {
      templateUrl: 'partial/purchase-order-form.html',
      controller: 'PurchaseOrderNewController',
      /*resolve: {
          access: ["PermisionAccess", function(PermisionAccess) {
              return PermisionAccess.allowAccess('purchasing', 'write');
          }],
      }*/
    }).
    when('/purchase-order/detail/:poId*', {
      templateUrl: 'partial/detail-purchase-order.html',
      controller: 'PurchaseOrderDetailController',
      /*resolve: {
          access: ["PermisionAccess", function(PermisionAccess) {
              return PermisionAccess.allowAccess('purchasing', 'read');
          }],
      }*/
    }).
    when('/purchase-order/edit/:poId*', {
      templateUrl: 'partial/edit-purchase-order.html',
      controller: 'PurchaseOrderEditController',
      /*resolve: {
          access: ["PermisionAccess", function(PermisionAccess) {
              return PermisionAccess.allowAccess('purchasing', 'write');
          }],
      }*/
    }).

    when('/data-purchase-order-pajak/', {
      templateUrl: 'partial/data-purchase-order-pajak.html',
      controller: 'PurchaseOrderPajakController',
      /*resolve: {
          access: ["PermisionAccess", function(PermisionAccess) {
              return PermisionAccess.allowAccess('purchasing', 'read');
          }],
      }*/
    }).
    when('/purchase-order-pajak/new/', {
      templateUrl: 'partial/purchase-order-pajak-form.html',
      controller: 'PurchaseOrderNewPajakController',
      /*resolve: {
          access: ["PermisionAccess", function(PermisionAccess) {
              return PermisionAccess.allowAccess('purchasing', 'write');
          }],
      }*/
    }).
    when('/purchase-order-pajak/detail/:poId*', {
      templateUrl: 'partial/detail-purchase-order-pajak.html',
      controller: 'PurchaseOrderDetailPajakController',
      /*resolve: {
          access: ["PermisionAccess", function(PermisionAccess) {
              return PermisionAccess.allowAccess('purchasing', 'read');
          }],
      }*/
    }).
    when('/purchase-order-pajak/edit/:poId*', {
      templateUrl: 'partial/edit-purchase-order-pajak.html',
      controller: 'PurchaseOrderEditPajakController',
      /*resolve: {
          access: ["PermisionAccess", function(PermisionAccess) {
              return PermisionAccess.allowAccess('purchasing', 'write');
          }],
      }*/
    }).

    /* RETURN PO */
    when('/data-return-barang/', {
      templateUrl: 'partial/data-return-po.html',
      controller: 'ReturnPOController',
      /*resolve: {
          access: ["PermisionAccess", function(PermisionAccess) {
              return PermisionAccess.allowAccess('purchasing', 'read');
          }],
      }*/
    }).
    when('/return-barang/new/', {
      templateUrl: 'partial/return-po-form.html',
      controller: 'ReturnPONewController',
      /*resolve: {
          access: ["PermisionAccess", function(PermisionAccess) {
              return PermisionAccess.allowAccess('purchasing', 'write');
          }],
      }*/
    }).
    when('/return-barang/detail/:returnPOId*', {
      templateUrl: 'partial/detail-return-po.html',
      controller: 'ReturnPODetailController',
      /*resolve: {
          access: ["PermisionAccess", function(PermisionAccess) {
              return PermisionAccess.allowAccess('purchasing', 'read');
          }],
      }*/
    }).
    when('/return-barang/edit/:poId', {
      templateUrl: 'partial/edit-return-po.html',
      controller: 'ReturnPOEditController',
      /*resolve: {
          access: ["PermisionAccess", function(PermisionAccess) {
              return PermisionAccess.allowAccess('purchasing', 'write');
          }],
      }*/
    }).

    /* FAKTUR PAJAK PO */
    when('/data-faktur-pajak-po/', {
      templateUrl: 'partial/data-faktur-pajak-po.html',
      controller: 'FakturPajakPOController',
      /*resolve: {
          access: ["PermisionAccess", function(PermisionAccess) {
              return PermisionAccess.allowAccess('purchasing', 'write');
          }],
      }*/
    }).
    when('/faktur-pajak-po/new/', {
      templateUrl: 'partial/faktur-pajak-po-form.html',
      controller: 'FakturPajakPONewController',
      /*resolve: {
          access: ["PermisionAccess", function(PermisionAccess) {
              return PermisionAccess.allowAccess('purchasing', 'write');
          }],
      }*/
    }).
    when('/faktur-pajak-po/edit/:id', {
      templateUrl: 'partial/faktur-pajak-po-form.html',
      controller: 'FakturPajakPOEditController',
      /*resolve: {
          access: ["PermisionAccess", function(PermisionAccess) {
              return PermisionAccess.allowAccess('purchasing', 'write');
          }],
      }*/
    }).

    /* OUTSTANDING PAJAK PO */
    when('/data-outstanding-pajak-po/', {
      templateUrl: 'partial/data-outstanding-pajak-po.html',
      controller: 'OutstandingPajakPOController',
      /*resolve: {
          access: ["PermisionAccess", function(PermisionAccess) {
              return PermisionAccess.allowAccess('purchasing', 'write');
          }],
      }*/
    }).

    /* RETURN PENJUALAN */
    when('/data-return-penjualan/', {
      templateUrl: 'partial/data-return-penjualan.html',
      controller: 'ReturnPenjualanController',
      /*resolve: {
          access: ["PermisionAccess", function(PermisionAccess) {
              return PermisionAccess.allowAccess('purchasing', 'read');
          }],
      }*/
    }).
    when('/return-penjualan/new/', {
      templateUrl: 'partial/return-penjualan-form.html',
      controller: 'ReturnPenjualanNewController',
      /*resolve: {
          access: ["PermisionAccess", function(PermisionAccess) {
              return PermisionAccess.allowAccess('purchasing', 'write');
          }],
      }*/
    }).
    when('/return-penjualan/detail/:returnPenjualanId*', {
      templateUrl: 'partial/detail-return-penjualan.html',
      controller: 'ReturnPenjualanDetailController',
      /*resolve: {
          access: ["PermisionAccess", function(PermisionAccess) {
              return PermisionAccess.allowAccess('purchasing', 'read');
          }],
      }*/
    }).
    when('/return-penjualan/edit/:poId', {
      templateUrl: 'partial/edit-return-penjualan.html',
      controller: 'ReturnPenjualanEditController',
      /*resolve: {
          access: ["PermisionAccess", function(PermisionAccess) {
              return PermisionAccess.allowAccess('purchasing', 'write');
          }],
      }*/
    }).

    when('/data-pembelian/', {
      templateUrl: 'partial/data-pembelian.html',
      controller: 'PembelianController',
      /*resolve: {
          access: ["PermisionAccess", function(PermisionAccess) {
              return PermisionAccess.allowAccess('purchasing', 'read');
          }],
      }*/
    }).
    when('/pembelian/new/', {
      templateUrl: 'partial/pembelian-form.html',
      controller: 'PembelianNewController',
      /*resolve: {
          access: ["PermisionAccess", function(PermisionAccess) {
              return PermisionAccess.allowAccess('purchasing', 'write');
          }],
      }*/
    }).
    when('/pembelian/detail/:pembelianId', {
      templateUrl: 'partial/detail-pembelian.html',
      controller: 'PembelianDetailController',
      /*resolve: {
          access: ["PermisionAccess", function(PermisionAccess) {
              return PermisionAccess.allowAccess('purchasing', 'read');
          }],
      }*/
    }).
    when('/data-pengiriman-barang/', {
      templateUrl: 'partial/data-pengiriman-barang.html',
      controller: 'PengirimanBarangController',
      /*resolve: {
          access: ["PermisionAccess", function(PermisionAccess) {
              return PermisionAccess.allowAccess('purchasing', 'read');
          }],
      }*/
    }).
    when('/data-pengiriman-barang/:idProyek', {
      templateUrl: 'partial/data-pengiriman-barang.html',
      controller: 'PengirimanBarangController',
      /*resolve: {
          access: ["PermisionAccess", function(PermisionAccess) {
              return PermisionAccess.allowAccess('purchasing', 'read');
          }],
      }*/
    }).
    when('/pengiriman-barang/new/', {
      templateUrl: 'partial/pengiriman-barang-form.html',
      controller: 'PengirimanBarangNewController',
      /*resolve: {
          access: ["PermisionAccess", function(PermisionAccess) {
              return PermisionAccess.allowAccess('purchasing', 'write');
          }],
      }*/
    }).
    when('/pengiriman-barang/detail/:pbarangId', {
      templateUrl: 'partial/detail-pengiriman-barang.html',
      controller: 'PengirimanBarangDetailController',
      /*resolve: {
          access: ["PermisionAccess", function(PermisionAccess) {
              return PermisionAccess.allowAccess('purchasing', 'read');
          }],
      }*/
    }).

    //Daily Report
    when('/daily-report-day', {
      templateUrl: 'partial/report-daily.html',
      controller: 'ReportDailyController'
    }).
    when('/daily-report-user', {
      templateUrl: 'partial/report-karyawan.html',
      controller: 'ReportUserController'
    }).

    when('/laporan-stok', {
      templateUrl: 'partial/laporan-stok.html',
      controller: 'LaporanStok',
    }).
    when('/laporan-stok-keluar-masuk', {
      templateUrl: 'partial/laporan-stok-keluar-masuk.html',
      controller: 'LaporanStokKeluarMasuk',
    }).
    when('/laporan-outstanding-spb', {
      templateUrl: 'partial/laporan-outstanding-spb.html',
      controller: 'LaporanOutstandingSPB',
    }).
    when('/laporan-outstanding-po', {
      templateUrl: 'partial/laporan-outstanding-po.html',
      controller: 'LaporanOutstandingPO',
    }).
    when('/laporan-outstanding-invoice', {
      templateUrl: 'partial/laporan-outstanding-invoice.html',
      controller: 'LaporanOutstandingInvoice',
    }).
    when('/laporan-penjualan', {
      templateUrl: 'partial/laporan-penjualan-periode.html',
      controller: 'LaporanPenjualan',
    }).
    when('/laporan-penjualan-sbp', {
      templateUrl: 'partial/laporan-penjualan-spb-periode.html',
      controller: 'LaporanPenjualanSPB',
    }).
    when('/laporan-penjualan-perkategori', {
      templateUrl: 'partial/laporan-penjualan-perkategori.html',
      controller: 'LaporanPenjualanPerkategori',
    }).
    when('/laporan-penjualan-sj', {
      templateUrl: 'partial/laporan-penjualan-sj-periode.html',
      controller: 'LaporanPenjualanSJ',
    }).
    when('/laporan-hpp-periode', {
      templateUrl: 'partial/laporan-hpp-periode.html',
      controller: 'LaporanHPPPeriode',
    }).
    when('/laporan-laba-rugi', {
      templateUrl: 'partial/laporan-laba-rugi.html',
      controller: 'LaporanLabaRugi',
    }).
    when('/laporan-invoice-jatuh-tempo', {
      templateUrl: 'partial/laporan-invoice-jatuh-tempo.html',
      controller: 'LaporanInvoiceJatuhTempo',
    }).

    when('/laporan-order-barang', {
      templateUrl: 'partial/laporan-order-barang.html',
      controller: 'LaporanOrderBarangController',
    }).
    when('/laporan-pembelian', {
      templateUrl: 'partial/laporan-pembelian.html',
      controller: 'LaporanPembelianController',
    }).
    when('/laporan-pengiriman', {
      templateUrl: 'partial/laporan-pengiriman.html',
      controller: 'LaporanPengirimanController',
    }).
    when('/laporan-rekap-spb', {
      templateUrl: 'partial/laporan-rekap-spb.html',
      controller: 'LaporanRekapSPBController',
    }).
    when('/data-contact-category', {
      templateUrl: 'partial/data-contact-category.html',
      controller: 'ContactCategoryController',
    }).
    when('/contactcategory/new', {
      templateUrl: 'partial/contact-category-form.html',
      controller: 'ContactCategoryNewController',
    }).
    when('/contactcategory/edit/:contactcategoryId', {
      templateUrl: 'partial/contact-category-form.html',
      controller: 'ContactCategoryEditController',
    }).
    when('/manage-contact/:contactcategoryId', {
      templateUrl: 'partial/manage-contact.html',
      controller: 'ManageContactController',
    }).
    when('/data-mail-blasting', {
      templateUrl: 'partial/data-mail-blasting.html',
      controller: 'ManageBMController',
    }).
    when('/mail-blasting/new/', {
      templateUrl: 'partial/blasting-form.html',
      controller: 'ManageBMNewController',
    }).
    when('/resend-mail-blasting/:id', {
      templateUrl: 'partial/blasting-form.html',
      controller: 'ManageBMResendController',
    }).

    when('/data-stok-gudang/', {
      templateUrl: 'partial/data-stok-gudang.html',
      controller: 'StokGudangController',
    }).
    when('/kartu-stok-gudang/:id/:idGudang', {
      templateUrl: 'partial/kartu-stok-gudang.html',
      controller: 'KartuStokGudangController',
    }).
    when('/detail-stok-gudang/:idBarang', {
      templateUrl: 'partial/detail-data-stok-gudang.html',
      controller: 'StokGudangDetailController',
    }).
    when('/detail-stok-gudang/edit/:idStok', {
      templateUrl: 'partial/edit-data-stok-gudang.html',
      controller: 'StokGudangEditController',
    }).

    when('/data-stok-purchasing/', {
      templateUrl: 'partial/data-stok-purchasing.html',
      controller: 'StokPurchasingController',
    }).
    when('/kartu-stok-purchasing/:id/:idGudang/:idPenjualan', {
      templateUrl: 'partial/kartu-stok-purchasing.html',
      controller: 'KartuStokPurchasingController',
    }).
    when('/detail-stok-purchasing/:idBarang', {
      templateUrl: 'partial/detail-data-stok-purchasing.html',
      controller: 'StokPurchasingDetailController',
    }).
    when('/detail-stok-purchasing/edit/:idStok', {
      templateUrl: 'partial/edit-data-stok-purchasing.html',
      controller: 'StokPurchasingEditController',
    }).

    when('/audit-stok/', {
      templateUrl: 'partial/data-stok-opname.html',
      controller: 'StokOpnameController',
    }).
    when('/stok-barang/', {
      templateUrl: 'partial/data-stok-barang.html',
      controller: 'StokBarangController',
    }).
    when('/data-penerimaan-barang/', {
      templateUrl: 'partial/data-penerimaan-barang.html',
      controller: 'PenerimaanBarangController',
    }).
    when('/data-penerimaan-barang-ppn/', {
      templateUrl: 'partial/data-penerimaan-barang-ppn.html',
      controller: 'PenerimaanBarangPPNController',
    }).
    when('/penerimaan-barang/new/', {
      templateUrl: 'partial/penerimaan-barang-form.html',
      controller: 'PenerimaanBarangNewController',
    }).
    when('/penerimaan-barang/new/:ppn', {
      templateUrl: 'partial/penerimaan-barang-form.html',
      controller: 'PenerimaanBarangNewController',
    }).
    when('/penerimaan-barang/detail/:id', {
      templateUrl: 'partial/detail-penerimaan-barang.html',
      controller: 'PenerimaanBarangDetailController',
    }).
    when('/data-penjualan/', {
      templateUrl: 'partial/data-penjualan.html',
      controller: 'PenjualanController',
    }).
    when('/penjualan/new/', {
      templateUrl: 'partial/penjualan-form.html',
      controller: 'PenjualanNewController',
    }).
    when('/penjualan/edit/:penjualanId', {
      templateUrl: 'partial/edit-penjualan.html',
      controller: 'PenjualanEditController',
    }).
    when('/penjualan/detail/:penjualanId', {
      templateUrl: 'partial/detail-penjualan.html',
      controller: 'PenjualanDetailController',
    }).
    when('/data-penjualan-grosir/', {
      templateUrl: 'partial/data-penjualan-grosir.html',
      controller: 'PenjualanGrosirController',
    }).
    when('/penjualan-grosir/new/', {
      templateUrl: 'partial/penjualan-grosir-form.html',
      controller: 'PenjualanGrosirNewController',
    }).
    when('/penjualan-grosir/detail/:penjualanId', {
      templateUrl: 'partial/detail-penjualan-grosir.html',
      controller: 'PenjualanGrosirDetailController',
    }).
    when('/data-sph/', {
      templateUrl: 'partial/data-sph.html',
      controller: 'SPHController',
    }).
    when('/sph/new/', {
      templateUrl: 'partial/sph-form.html',
      controller: 'SPHNewController',
    }).
    when('/sph/edit/:sphId', {
      templateUrl: 'partial/edit-sph.html',
      controller: 'SPHEditController',
    }).
    when('/sph/detail/:sphId', {
      templateUrl: 'partial/detail-sph.html',
      controller: 'SPHDetailController',
    }).
    when('/data-spk/', {
      templateUrl: 'partial/data-spk.html',
      controller: 'SPKController',
    }).
    when('/spk/new/', {
      templateUrl: 'partial/spk-form.html',
      controller: 'SPKNewController',
    }).
    when('/spk/detail/:pengirimanMMSId', {
      templateUrl: 'partial/detail-spk.html',
      controller: 'SPKDetailController',
    }).


    when('/karyawan/edit/:karyawanId', {
      templateUrl: 'partial/karyawan-form.html',
      controller: 'KaryawanEditController'
    }).
    when('/karyawan/profile/:karyawanId', {
      templateUrl: 'partial/karyawan-profile.html',
      controller: 'KaryawanEditController'
    }).

    when('/laba-rugi-penjualan/:id', {
      templateUrl: 'partial/laba-rugi-penjualan.html',
      controller: 'LabaRugiSPBController',
    }).

    when('/data-pajak-po/', {
      templateUrl: 'partial/data-pajak-po.html',
      controller: 'PajakPOController',
      /*resolve: {
          access: ["PermisionAccess", function(PermisionAccess) {
              return PermisionAccess.allowAccess('purchasing', 'write');
          }],
      }*/
    }).

    when('/data-file-category', {
      templateUrl: 'partial/data-file-category.html',
      controller: 'FileCategoryCtrl'
    }).
    when('/file-category/new', {
      templateUrl: 'partial/file-category-form.html',
      controller: 'FileCategoryNewCtrl'
    }).
    when('/file-category/edit/:fileCategoryId', {
      templateUrl: 'partial/file-category-form.html',
      controller: 'FileCategoryEditCtrl'
    }).

    when('/data-file-pelanggan/:idFileCategory/:idPelanggan', {
      templateUrl: 'partial/data-file-pelanggan.html',
      controller: 'FilePelangganCtrl'
    }).
    when('/file-pelanggan/new/:idFileCategory/:idPelanggan', {
      templateUrl: 'partial/file-pelanggan-form.html',
      controller: 'FilePelangganNewCtrl'
    }).
    when('/file-pelanggan/edit/:idFileCategory/:idPelanggan/:id', {
      templateUrl: 'partial/file-pelanggan-form.html',
      controller: 'FilePelangganEditCtrl'
    }).

    when('/laporan-po-supplier', {
      templateUrl: 'partial/laporan-po-supplier.html',
      controller: 'LaporanPOSupplierController',
    }).

    when('/data-vo-spb/:idPenjualan', {
      templateUrl: 'partial/data-vo-spb.html',
      controller: 'VOSPBController',
    }).
    when('/data-variant-order', {
      templateUrl: 'partial/data-vo.html',
      controller: 'VOController',
    }).
    when('/vo-spb/new/:idPenjualan', {
      templateUrl: 'partial/vo-spb-form.html',
      controller: 'VOSPBNewController',
    }).
    when('/vo-spb/detail/:voId', {
      templateUrl: 'partial/detail-vo-spb.html',
      controller: 'VOSPBDetailController',
    }).
    when('/vo-spb/edit/:voId', {
      templateUrl: 'partial/vo-spb-form.html',
      controller: 'VOSPBEditController',
    }).

    when('/forbidden', {
      templateUrl: 'partial/forbidden.html',
      resolve: {
        access: ["PermisionAccess", function (PermisionAccess) {
          return PermisionAccess.allowAccess('general', 'read');
        }],
      }
    }).
    otherwise({
      redirectTo: '/'
    });
}]);

tripApp.factory('PermisionAccess', function ($rootScope) {
  var userAccess;
  return {
    setPermision: function (pAccess) {
      userAccess = pAccess;
    },
    allowAccess: function (a, b) {
      if (a === 'general')
        return true;
      else {
        console.log(a, b);
        if (userAccess[a] !== undefined) {
          $rootScope.ReadData = userAccess[a].read;
          $rootScope.WriteData = userAccess[a].write;
          console.log(userAccess[a][b]);
          return userAccess[a][b];
        } else
          return false;
        return true;
      }
    }
  }
})

tripApp.run(function ($rootScope, $http, $location, PermisionAccess, $timeout, Idle, Keepalive) {
  if (typeof $rootScope.authenticated == "undefined") {
    //$location.path("/");
    $http.get('api/config/session-login.php').then(function (results) {
      var results = results.data;
      if (results.uid !== null) {
        $rootScope.authenticated = true;
        $rootScope.userLoginID = results.uid;
        $rootScope.userLoginName = results.name;
        $rootScope.userLoginLevel = results.level;
        $rootScope.departement = results.departement;
        $rootScope.jabatan = results.jabatan;
        $rootScope.jabatan2 = results.jabatan2;
        PermisionAccess.setPermision(results.permision);
        $rootScope.userPrimaryPhoto = results.profile;
        $rootScope.karyawanMMS = results.karyawanMMS;
        $rootScope.listMenu = results.permision;
        $rootScope.totalNotifikasiMaintenance = results.totalNotifikasiMaintenance;
        //console.log($rootScope.departement);
      } else {
        $rootScope.authenticated = false;
        const currentUrl = window.location.href;
        const encodedCurrentUrl = encodeURIComponent(currentUrl);
        const newUrl = base_url + "login.html?redirect=" + encodedCurrentUrl;
        window.location.href = newUrl;
      }
    });
  } else {
    $timeout(function () {
      if (next.locals.access === false)
        $location.path("/forbidden");
    }, 100);
  }

  Idle.watch();
  $rootScope.$on('IdleStart', function () {

  });
  $rootScope.$on('IdleEnd', function () {

  });
  $rootScope.$on('IdleTimeout', function () {
    window.location.replace(base_url + "login.html");
  });

  $rootScope.$on("$routeChangeStart", function (event, next, current) {
    var d = new Date();

    $rootScope.month = {
      '0': {
        'id': '1',
        'value': 'Januari'
      },
      '1': {
        'id': '2',
        'value': 'Februari'
      },
      '2': {
        'id': '3',
        'value': 'Maret'
      },
      '3': {
        'id': '4',
        'value': 'April'
      },
      '4': {
        'id': '5',
        'value': 'Mei'
      },
      '5': {
        'id': '6',
        'value': 'Juni'
      },
      '6': {
        'id': '7',
        'value': 'Juli'
      },
      '7': {
        'id': '8',
        'value': 'Agustus'
      },
      '8': {
        'id': '9',
        'value': 'September'
      },
      '9': {
        'id': '10',
        'value': 'Oktober'
      },
      '10': {
        'id': '11',
        'value': 'November'
      },
      '11': {
        'id': '12',
        'value': 'Desember'
      },
    };

    $rootScope.year = [];

    for (var i = 2015; i <= d.getFullYear(); i++) {
      $rootScope.year.push(i);
    }

    $rootScope.baseURL = base_url;

    var dd = d.getDate();
    var mm = d.getMonth() + 1;
    var yyyy = d.getFullYear();

    if (parseInt(dd) < 10) dd = '0' + dd;
    if (parseInt(mm) < 10) mm = '0' + mm;

    $rootScope.currentDateID = dd + "/" + mm + "/" + yyyy;
    $rootScope.currentDate = yyyy + "-" + mm + "-" + dd;
    $rootScope.currentYear = yyyy;
    $rootScope.currentMonth = mm;
  });
});

tripApp.controller('AppGlobalController', function ($scope, $http) {
  $scope.app = {
    name: 'SOPAN Smart Office',
    description: 'Lintas Daya',
    author: 'Pesona Creative'
  }

  $scope.doSearch = function () {
    window.document.location = '#/find/' + $scope.searchkey;
  }

  $scope.doLogout = function () {
    $http.get('api/config/logout.php').success(function (data, status) {
      window.location.href = base_url + "login.html";
    });
  }

  $scope.datenow = function () {
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //January is 0!
    var yyyy = today.getFullYear();

    if (dd < 10) {
      dd = '0' + dd
    }

    if (mm < 10) {
      mm = '0' + mm
    }

    today = dd + '/' + mm + '/' + yyyy;
    return today;
  }

  $scope.getmonth = function () {
    var today = new Date();
    var mm = today.getMonth() + 1; //January is 0!
    return mm;
  }

  $scope.getyear = function () {
    var today = new Date();
    var yyyy = today.getFullYear();
    return yyyy;
  }

  $scope.datajabatan = function () {
    $http.get('api/jabatan/data-jabatan.php').success(function (data, status) {
      $scope.data_jabatan = data;
    });
  };
  //$scope.datajabatan();
});

function numberWithCommas(x) {
  var x = x.toString().replace(/,/g, "");
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

tripApp.directive("jqTable", function () {
  return function (scope, element, attrs) {
    'use strict';
    if (scope.$last) {
      var initBasicTable = function () {
        var table = $('#basicTable');
        var settings = {
          "dom": '<"top"f>t<"bottom"p><"clear">',
          "destroy": true,
          "paging": true,
          "searching": true,
          "pageLength": 10,
          "scrollCollapse": true,
          "aoColumnDefs": [{
            'bSortable': true
          }],
          "order": [
            [0, "asc"]
          ],
          "language": {
            "emptyTable": "No data available in table"
          }
        };
        table.dataTable(settings).fnDraw();
      }
      setTimeout(function () {
        initBasicTable();
      }, 10);
    }
  };
});

tripApp.directive("select2", function () {
  return function (scope, element, attrs) {
    'use strict';
    if (scope.$last) {
      $('.select2').select2({
        dropdownCssClass: 'bigdrop'
      });
    }
  };
});

tripApp.directive("select2return", function () {
  return function (scope, element, attrs, http) {
    'use strict';
    if (scope.$last) {
      $('.select2').select2({
        dropdownCssClass: 'bigdrop'
      });
    }
  };
});

tripApp.directive("selectMultiple", function () {
  return function (scope, element, attrs) {
    'use strict';
    $('.s2multiple').select2();
  };
});

tripApp.filter('titleCase', function () {
  return function (input) {
    input = input || '';
    return input.replace(/\w\S*/g, function (txt) {
      return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
    });
  };
})

tripApp.controller('PenjualanSearchController', function ($scope, $route, $routeParams, $http, ngToast) {

  $scope.searchkey = $routeParams.searchKey;

  $scope.getdata = function () {
    $scope.data_penjualan = [];
    $http.get('api/pos/data-list.php?key=' + $scope.searchkey).success(function (data, status) {
      $scope.data_penjualan = data;
    });
  };

  $scope.getdata();
});
