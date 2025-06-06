tripApp.controller('ProyekController', function ($rootScope, $scope, $route, $http, ngToast) {
  var d = new Date();
  $scope.year_selected = d.getFullYear().toString();
  $scope.status_proyek = 'all';
  $scope.nama_proyek = "";

  $scope.getdata = function () {
    $http.get('api/proyek/data-proyek.php?tahun=' + $scope.year_selected + '&status=' + $scope.status_proyek + '&nama_proyek=' + $scope.nama_proyek).success(function (data, status) {
      $scope.data_proyek = data;
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
        url: 'api/proyek/delete.php',
        data: $.param({
          'idr': val
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data proyek berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else if (data == "2") {
          ngToast.create({
            className: 'danger',
            content: 'Data proyek gagal dihapus karena telah terintegrasi dengan data Invoice/Jurnal/PO <i class="fa fa-remove"></i>'
          });
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data proyek gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }

  /* PROYEK GAJI */
  $scope.selectedProyek = {};
  $scope.showModal = function (index) {
    $scope.selectedProyek = $scope.data_proyek[index];
    if (typeof $scope.selectedProyek.LemburPerJamTipe === 'undefined' || $scope.selectedProyek.LemburPerJamTipe === null) {
      $scope.selectedProyek.LemburPerJamTipe = "1";
    }
    $('#myModal').modal('show');
    $('#myModal').children('.modal-dialog').removeClass('modal-lg');
    $('#myModal').on('shown.bs.modal', function () {
      $('#LemburPerJam').focus();
    });
  }

  $scope.updateGajiProyek = function () {
    $http({
      method: "POST",
      url: 'api/proyek/edit-gaji.php',
      data: $.param({
        'IDProyek': $scope.selectedProyek.IDProyek,
        'LemburPerJamTipe': $scope.selectedProyek.LemburPerJamTipe,
        'LemburPerJam': $scope.selectedProyek.LemburPerJam
      }),
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    }).success(function (data, status) {
      if (data == "1") {
        ngToast.create({
          className: 'success',
          content: 'Data gaji proyek berhasil disimpan <i class="fa fa-remove"></i>'
        });
        $('#myModal').modal('hide');
      } else {
        $scope.processing = false;
        ngToast.create({
          className: 'danger',
          content: 'Data proyek gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
        });
      }
    });
  }
});

tripApp.controller('ProyekNewController', function ($scope, $route, $q, $http, ngToast) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.getdatadepartement = function () {
    $http.get('api/departement/data-departement.php').success(function (data, status) {
      $scope.data_departement = data;
    });
  };

  //$scope.getdatadepartement();

  $scope.getjabatanpm = function () {
    $http.get('api/karyawan/data-karyawan.php?idj=5').success(function (data, status) {
      $scope.data_pm = data;
    });
  };

  //$scope.getjabatanpm();

  $scope.getjabatansm = function () {
    $http.get('api/karyawan/data-karyawan.php?idj=6').success(function (data, status) {
      $scope.data_sm = data;
    });
  };

  //$scope.getjabatansm();

  $scope.getjabatansv = function () {
    $http.get('api/karyawan/data-karyawan.php?idj=7').success(function (data, status) {
      $scope.data_sv = data;
    });
  };

  //$scope.getjabatansv();

  $scope.getkaryawan = function () {
    $http.get('api/karyawan/data-karyawan.php').success(function (data, status) {
      $scope.data_sa = data;
    });
  };

  //$scope.getkaryawan();

  $scope.getdata = function () {
    $http.get('api/pelanggan/data-pelanggan.php').success(function (data, status) {
      $scope.data_pelanggan = data;
    });
  };

  //$scope.getdata();

  $q.all({
    apiv1: $http.get('api/proyek/load-all-requirement.php')
  }).then(function (results) {
    $scope.data_departement = results.apiv1.data.departement;
    $scope.data_departement_pemilik = results.apiv1.data.departement_pemilik;
    $scope.data_pm = results.apiv1.data.kProyekManager;
    $scope.data_sm = results.apiv1.data.kSiteManager;
    $scope.data_sv = results.apiv1.data.kSupervisor;
    $scope.data_sa = results.apiv1.data.kSiteAdmin;
    $scope.data_pelanggan = results.apiv1.data.pelanggan;
  });

  $scope.keyupnominal = function () {
    if ($scope.nominal == null || $scope.nominal == '0') {
      $scope.limit_peng_persen = '0';
      $scope.limit_pengeluaran = '0';
    }
  }

  $scope.changePpnPersen = function () {
    if ($scope.ppn_persen == null)
      $scope.ppn = '0';
    else
      $scope.ppn = ($scope.ppn_persen / 100) * $scope.nominal.toString().replace(/,/g, "");

    $scope.grand_total = numberWithCommas(parseFloat($scope.ppn) + parseFloat($scope.nominal.toString().replace(/,/g, "")));
    $scope.ppn = numberWithCommas($scope.ppn);
    $scope.limit_persen();
  }

  $scope.limit_persen = function () {
    if ($scope.limit_peng_persen == null)
      $scope.limit_pengeluaran = '0';
    else
      $scope.limit_pengeluaran = numberWithCommas(($scope.limit_peng_persen / 100) * $scope.grand_total.toString().replace(/,/g, ""));
  }

  $scope.cektotal = function () {
    var limit_material = $('#limit_material').val().toString().replace(/,/g, "");
    var limit_tenaga = $('#limit_tenaga').val().toString().replace(/,/g, "");
    var limit_overhead = $('#limit_overhead').val().toString().replace(/,/g, "");
    if (limit_material == "") limit_material = 0;
    if (limit_tenaga == "") limit_tenaga = 0;
    if (limit_overhead == "") limit_overhead = 0;
    $scope.total = parseFloat(limit_material) + parseFloat(limit_tenaga) + parseFloat(limit_overhead);
    //alert($scope.total);
    if ($scope.total > $scope.limit_pengeluaran.toString().replace(/,/g, "")) {
      //alert("Jumlah telah melebihi limit pengeluaran");
      ngToast.create({
        className: 'danger',
        content: 'Jumlah telah melebihi limit pengeluaran <i class="fa fa-remove"></i>'
      });
    }
  }

  $scope.limitmaterial = function () {
    $scope.cektotal();
  }

  $scope.limittenaga = function () {
    $scope.cektotal();
  }

  $scope.limitoverhead = function () {
    $scope.cektotal();
  }

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/proyek/new.php',
        data: $.param({
          'kode_proyek': $scope.kode_proyek,
          'no_kontrak': $scope.no_kontrak,
          'nama_proyek': $scope.nama_proyek,
          'tahun': $scope.tahun,
          'client': $scope.client,
          'kategori': $scope.kategori,
          'kategori2': $scope.kategori2,
          'status': $scope.statusProyek,
          'nominal': $scope.nominal,
          'ppn_persen': $scope.ppn_persen,
          'ppn': $scope.ppn,
          'grand_total': $scope.grand_total,
          'limit_peng_persen': $scope.limit_peng_persen,
          'limit_pengeluaran': $scope.limit_pengeluaran,
          'limit_material': $scope.limit_material,
          'limit_tenaga': $scope.limit_tenaga,
          'limit_overhead': $scope.limit_overhead,
          'project_manager': $scope.project_manager,
          'site_manager': $scope.site_manager,
          'supervisor': $scope.supervisor,
          'site_admin': $scope.site_admin,
          'site_admin2': $scope.site_admin2,
          'tanggalmulai': $scope.tanggalmulai,
          'tanggalselesai': $scope.tanggalselesai,
          'locked': $scope.locked
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data proyek berhasil disimpan <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-proyek';
        } else if (data == "2") {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data proyek gagal disimpan karena telah melebihi limit pengeluaran <i class="fa fa-remove"></i>'
          });
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data proyek gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('ProyekEditController', function ($rootScope, $scope, $route, $q, $routeParams, $http, ngToast, Upload) {
  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.getdatadepartement = function () {
    $http.get('api/departement/data-departement.php').success(function (data, status) {
      $scope.data_departement = data;
    });
  };

  //$scope.getdatadepartement();

  $scope.getjabatanpm = function () {
    $http.get('api/karyawan/data-karyawan.php?idj=5').success(function (data, status) {
      $scope.data_pm = data;
    });
  };

  //$scope.getjabatanpm();

  $scope.getjabatansm = function () {
    $http.get('api/karyawan/data-karyawan.php?idj=6').success(function (data, status) {
      $scope.data_sm = data;
    });
  };

  //$scope.getjabatansm();

  $scope.getjabatansv = function () {
    $http.get('api/karyawan/data-karyawan.php?idj=7').success(function (data, status) {
      $scope.data_sv = data;
    });
  };

  //$scope.getjabatansv();

  $scope.getdata = function () {
    $http.get('api/pelanggan/data-pelanggan.php').success(function (data, status) {
      $scope.data_pelanggan = data;
    });
  };

  //$scope.getdata();

  $scope.getdataFiles = function () {
    $http.get('api/proyek/data-upload.php?id=' + $routeParams.proyekId).success(function (data, status) {
      $scope.data_upload = data;
    });
  };

  //$scope.getdataFiles();

  $q.all({
    apiv1: $http.get('api/proyek/load-all-requirement.php?id=' + $routeParams.proyekId)
  }).then(function (results) {
    $scope.data_departement = results.apiv1.data.departement;
    $scope.data_departement_pemilik = results.apiv1.data.departement_pemilik;
    $scope.data_pm = results.apiv1.data.kProyekManager;
    $scope.data_sm = results.apiv1.data.kSiteManager;
    $scope.data_sv = results.apiv1.data.kSupervisor;
    $scope.data_sa = results.apiv1.data.kSiteAdmin;
    $scope.data_pelanggan = results.apiv1.data.pelanggan;
    $scope.data_upload = results.apiv1.data.upload;
    $scope.data_vo = results.apiv1.data.variantOrder;

    $scope.kode_proyek = results.apiv1.data.detail.kode_proyek;
    $scope.no_kontrak = results.apiv1.data.detail.no_kontrak;
    $scope.nama_proyek = results.apiv1.data.detail.nama_proyek;
    $scope.tahun = results.apiv1.data.detail.tahun;
    $scope.client = results.apiv1.data.detail.client;
    $scope.statusProyek = results.apiv1.data.detail.statusProyek;
    $scope.nominal = results.apiv1.data.detail.nominal;
    $scope.ppn_persen = results.apiv1.data.detail.ppn_persen;
    $scope.ppn = results.apiv1.data.detail.ppn;
    $scope.grand_total = results.apiv1.data.detail.grand_total;
    $scope.grand_total_vo = results.apiv1.data.detail.grand_total_vo;
    $scope.limit_peng_persen = results.apiv1.data.detail.limit_peng_persen;
    $scope.limit_pengeluaran = results.apiv1.data.detail.limit_pengeluaran;
    $scope.limit_material = results.apiv1.data.detail.limit_material;
    $scope.limit_tenaga = results.apiv1.data.detail.limit_tenaga;
    $scope.limit_overhead = results.apiv1.data.detail.limit_overhead;
    $scope.project_manager = results.apiv1.data.detail.project_manager;
    $scope.site_manager = results.apiv1.data.detail.site_manager;
    $scope.supervisor = results.apiv1.data.detail.supervisor;
    $scope.site_admin = results.apiv1.data.detail.site_admin;
    $scope.site_admin2 = results.apiv1.data.detail.site_admin2;
    $scope.kategori = results.apiv1.data.detail.kategori;
    $scope.kategori2 = results.apiv1.data.detail.kategori2;
    $scope.locked = results.apiv1.data.detail.locked;
    $scope.totalVO = results.apiv1.data.totalVO;

    $scope.tanggalmulai = results.apiv1.data.detail.tanggalmulai;
    $scope.tanggalselesai = results.apiv1.data.detail.tanggalselesai;

    $scope.last_sync = results.apiv1.data.detail.last_sync;

    $scope.keyupnominal = function () {
      if ($scope.nominal == null || $scope.nominal == '0') {
        $scope.limit_peng_persen = '0';
        $scope.limit_pengeluaran = '0';
      }
    }

    $scope.changePpnPersen = function () {
      if ($scope.ppn_persen == null)
        $scope.ppn = '0';
      else
        $scope.ppn = ($scope.ppn_persen / 100) * $scope.nominal.toString().replace(/,/g, "");

      $scope.grand_total = numberWithCommas(parseFloat($scope.grand_total_vo) + parseFloat($scope.ppn) + parseFloat($scope.nominal.toString().replace(/,/g, "")));
      $scope.ppn = numberWithCommas($scope.ppn);
      $scope.limit_persen();
    }

    $scope.limit_persen = function () {
      if ($scope.limit_peng_persen == null)
        $scope.limit_pengeluaran = '0';
      else
        $scope.limit_pengeluaran = numberWithCommas(($scope.limit_peng_persen / 100) * $scope.grand_total.toString().replace(/,/g, ""));
    }
  });

  $scope.cektotal = function () {
    var limit_material = $('#limit_material').val().toString().replace(/,/g, "");
    var limit_tenaga = $('#limit_tenaga').val().toString().replace(/,/g, "");
    var limit_overhead = $('#limit_overhead').val().toString().replace(/,/g, "");
    if (limit_material == "") limit_material = 0;
    if (limit_tenaga == "") limit_tenaga = 0;
    if (limit_overhead == "") limit_overhead = 0;
    $scope.total = parseFloat(limit_material) + parseFloat(limit_tenaga) + parseFloat(limit_overhead);
    //alert($scope.total);
    if ($scope.total > $scope.limit_pengeluaran.toString().replace(/,/g, "")) {
      //alert("Jumlah telah melebihi limit pengeluaran");
      ngToast.create({
        className: 'danger',
        content: 'Jumlah telah melebihi limit pengeluaran <i class="fa fa-remove"></i>'
      });
    }
  }

  $scope.limitmaterial = function () {
    $scope.cektotal();
  }

  $scope.limittenaga = function () {
    $scope.cektotal();
  }

  $scope.limitoverhead = function () {
    $scope.cektotal();
  }

  $scope.processing = false;

  /*
  $http.get('api/proyek/detail.php?id=' + $routeParams.proyekId).success(function(data, status) {
      $scope.kode_proyek = data.kode_proyek;
      $scope.nama_proyek = data.nama_proyek;
      $scope.tahun = data.tahun;
      $scope.client = data.client;
      $scope.statusProyek = data.statusProyek;
      $scope.nominal = numberWithCommas(data.nominal);
      $scope.ppn_persen = data.ppn_persen;
      $scope.ppn = numberWithCommas(data.ppn);
      $scope.grand_total = numberWithCommas(data.grand_total);
      $scope.limit_peng_persen = data.limit_peng_persen;
      $scope.limit_pengeluaran = numberWithCommas(data.limit_pengeluaran);
      $scope.limit_material = numberWithCommas(data.limit_material);
      $scope.limit_tenaga = numberWithCommas(data.limit_tenaga);
      $scope.limit_overhead = numberWithCommas(data.limit_overhead);
      $scope.project_manager = data.project_manager;
      $scope.site_manager = data.site_manager;
      $scope.supervisor = data.supervisor;
      $scope.kategori = data.kategori;

      $scope.keyupnominal = function() {
          if ($scope.nominal == null || $scope.nominal == '0') {
              $scope.limit_peng_persen = '0';
              $scope.limit_pengeluaran = '0';
          }
      }

      $scope.limit_persen = function() {
          if ($scope.limit_peng_persen == null)
              $scope.limit_pengeluaran = '0';
          else
              $scope.limit_pengeluaran = numberWithCommas(($scope.limit_peng_persen / 100) * $scope.nominal.toString().replace(/,/g, ""));
      }

      $scope.changePpnPersen = function() {
          if ($scope.ppn_persen == null)
              $scope.ppn = '0';
          else
              $scope.ppn = ($scope.ppn_persen / 100) * $scope.nominal.toString().replace(/,/g, "");
          $scope.grand_total = numberWithCommas(parseFloat($scope.ppn) + parseFloat($scope.nominal.toString().replace(/,/g, "")));
          $scope.ppn = numberWithCommas($scope.ppn);
      }
  });
  */

  $scope.activateSmartProject = function () {
    $http.post('api/proyek/activate-smart-project.php?id=' + $routeParams.proyekId).success(function (data, status) {
      $scope.last_sync = data;
    });
  }

  $scope.proyekId = $routeParams.proyekId;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/proyek/edit.php',
        data: $.param({
          'kode_proyek': $scope.kode_proyek,
          'no_kontrak': $scope.no_kontrak,
          'nama_proyek': $scope.nama_proyek,
          'tahun': $scope.tahun,
          'client': $scope.client,
          'kategori': $scope.kategori,
          'kategori2': $scope.kategori2,
          'status': $scope.statusProyek,
          'nominal': $scope.nominal,
          'ppn_persen': $scope.ppn_persen,
          'ppn': $scope.ppn,
          'grand_total': $scope.grand_total,
          'limit_peng_persen': $scope.limit_peng_persen,
          'limit_pengeluaran': $scope.limit_pengeluaran,
          'limit_material': $scope.limit_material,
          'limit_tenaga': $scope.limit_tenaga,
          'limit_overhead': $scope.limit_overhead,
          'project_manager': $scope.project_manager,
          'site_manager': $scope.site_manager,
          'supervisor': $scope.supervisor,
          'site_admin': $scope.site_admin,
          'site_admin2': $scope.site_admin2,
          'locked': $scope.locked,
          'tanggalmulai': $scope.tanggalmulai,
          'tanggalselesai': $scope.tanggalselesai,
          'id': $routeParams.proyekId
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data proyek berhasil diperbaharui <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-proyek';
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data proyek gagal diperbaharui. Silahkan coba kembali <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };

  $scope.filetype = "Penawaran - Estimate";

  $scope.uploadForm = function () {
    if ($scope.file && $scope.fname) {
      Upload.upload({
        url: 'api/proyek/upload.php',
        data: {
          'file': $scope.file,
          'filetype': $scope.filetype,
          'fname': $scope.fname,
          'id_proyek': $routeParams.proyekId
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data == "1") {
          $scope.getdataFiles();
          $scope.file = "";
          $scope.fname = "";
          ngToast.create({
            className: 'success',
            content: 'Dokumen berhasil diupload <i class="fa fa-remove"></i>'
          });
        } else if (data == "0") {
          ngToast.create({
            className: 'danger',
            content: 'Dokument gagal diupload. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    } else {
      ngToast.create({
        className: 'danger',
        content: 'Silahkan pilih dokumen anda dan lengkapi nama dokumen terlebih dahulu. <i class="fa fa-remove"></i>'
      });
    }
  };

  $scope.doDownload = function (a) {
    window.open('https://lintasdaya.s3-ap-southeast-1.amazonaws.com/proyek_file_sopan/' + a, 'Download File', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }

  $scope.removeRow = function (val) {
    if (confirm("Anda yakin ingin menghapus file ini?")) {
      $http({
        method: "POST",
        url: 'api/proyek/delete-file.php',
        data: $.param({
          'idr': val
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Dokumen Proyek berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $scope.getdataFiles();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Dokumen Proyek gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }
});

tripApp.controller('LabaRugiProyekController', function ($scope, $route, $routeParams, $http, ngToast, CommonServices) {
  $scope.proyekID = $routeParams.id;
  $scope.urut = "1";

  $scope.tanggal = CommonServices.lastDateMonth();

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.doFilter = function () {
    $http.get('api/laporan/laporan-proyek.php?act=LaporanLabaRugi&id=' + $routeParams.id + '&urut=' + $scope.urut + '&tanggal=' + $scope.tanggal).success(function (data, status) {

      $scope.proyek = data.proyek;

      $scope.lMaterial = data.lMaterial;
      $scope.lTenaga = data.lTenaga;
      $scope.lOverhead = data.lOverhead;
      $scope.lPendapatan = data.lPendapatan;
      $scope.lPengiriman = data.lPengiriman;
      $scope.lReturn = data.lReturn;
      $scope.lAccidental = data.lAccidental;

      $scope.Pendapatan1 = data.Pendapatan1;
      $scope.Pendapatan2 = data.Pendapatan2;
      $scope.Pendapatan3 = data.Pendapatan3;
      $scope.Material1 = data.Material1;
      $scope.Material2 = data.Material2;
      $scope.Material3 = data.Material3;
      $scope.Pengiriman1 = data.Pengiriman1;
      $scope.Pengiriman2 = data.Pengiriman2;
      $scope.Pengiriman3 = data.Pengiriman3;
      $scope.Tenaga1 = data.Tenaga1;
      $scope.Tenaga2 = data.Tenaga2;
      $scope.Tenaga3 = data.Tenaga3;
      $scope.Overhead1 = data.Overhead1;
      $scope.Overhead2 = data.Overhead2;
      $scope.Overhead3 = data.Overhead3;
      $scope.Return1 = data.Return1;
      $scope.Return2 = data.Return2;
      $scope.Return3 = data.Return3;
      $scope.Accidental1 = data.Accidental1;
      $scope.Accidental2 = data.Accidental2;
      $scope.Accidental3 = data.Accidental3;

      $scope.Pengeluaran1 = data.Pengeluaran1;
      $scope.Pengeluaran2 = data.Pengeluaran2;
      $scope.Pengeluaran3 = data.Pengeluaran3;

      $scope.Profit = data.Profit;


      $scope.PPN10 = data.PPN10;
      $scope.PPH2 = data.PPH2;
      $scope.DPP = data.DPP;
      $scope.TotalPajak = data.TotalPajak;

      $scope.ppnLists = data.ppnLists;
    });
  }

  $scope.doFilter();
});

tripApp.controller('CashFlowController', function ($scope, $route, $routeParams, $http, ngToast) {
  $scope.proyekID = $routeParams.id;
  $http.get('api/laporan/laporan-proyek.php?act=LaporanCashFlow&id=' + $routeParams.id).success(function (data, status) {
    $scope.dataCashFlow = data.dataCashFlow;
    $scope.proyek = data.proyek;
  });
});

tripApp.controller('PajakProyekController', function ($scope, $route, $routeParams, $http, ngToast) {
  $scope.proyekID = $routeParams.id;
  $http.get('api/laporan/laporan-proyek.php?act=LaporanPajak&id=' + $routeParams.id).success(function (data, status) {
    $scope.dataCashFlow = data.dataCashFlow;
    $scope.proyek = data.proyek;
  });
});

tripApp.controller('AbsentProyekController', function ($scope, $rootScope, $routeParams, $route, $http, ngToast, CommonServices) {

  $scope.datestart = CommonServices.firstDateMonth();
  $scope.dateend = CommonServices.lastDateMonth();

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.getdata = function () {
    $http.get('api/proyek/data-absent.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&kode_proyek=' + $scope.kode_proyek + '&karyawan=' + $scope.karyawan).success(function (data, status) {
      $scope.data_absent = data.data;
      $scope.data_karyawan = data.karyawan;
      $scope.proyek = data.proyek;
    });
  };

  $scope.karyawan = "";
  $scope.kode_proyek = "";

  $scope.refreshData = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }

  $scope.getdata();

  $scope.doPrint = function (a) {
    window.open($rootScope.baseURL + 'api/print/print-invoice.php?id=' + a, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('ProyekAreaController', function ($rootScope, $scope, $route, $http, ngToast, $routeParams) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-area.php?act=DisplayData&IDProyek=' + $routeParams.proyekId).success(function (data, status) {
      $scope.data = data.payload.data;
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;
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
        url: 'api/proyek/proyek-area.php?act=Delete',
        data: $.param({
          'IDArea': val,
          'IDProyek': $routeParams.proyekId
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data area proyek berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data area proyek gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }
});

tripApp.controller('ProyekAreaNewController', function ($scope, $route, $q, $http, ngToast, $routeParams) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-area.php?act=LoadAllRequirement&IDProyek=' + $routeParams.proyekId).success(function (data, status) {
      $scope.data = data.payload.data;
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;

      $scope.Nama = "";
      $scope.Keterangan = "";

      $scope.data_sv = data.payload.supervisor;
    });
  };
  $scope.getdata();

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/proyek/proyek-area.php?act=InsertNew',
        data: $.param({
          'IDProyek': $routeParams.proyekId,
          'Nama': $scope.Nama,
          'Keterangan': $scope.Keterangan,
          'Supervisor': $scope.supervisor
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data area proyek berhasil disimpan <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-proyek-area/' + $routeParams.proyekId;
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data area proyek gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('ProyekAreaEditController', function ($scope, $route, $q, $http, ngToast, $routeParams) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-area.php?act=Detail&IDProyek=' + $routeParams.proyekId + '&IDArea=' + $routeParams.areaId).success(function (data, status) {
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;

      $scope.Nama = data.payload.data.Nama;
      $scope.Keterangan = data.payload.data.Keterangan;
      $scope.supervisor = data.payload.data.Supervisor;

      $scope.data_sv = data.payload.supervisor;
    });
  };
  $scope.getdata();

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      $http({
        method: "POST",
        url: 'api/proyek/proyek-area.php?act=Update',
        data: $.param({
          'IDProyek': $routeParams.proyekId,
          'IDArea': $routeParams.areaId,
          'Nama': $scope.Nama,
          'Keterangan': $scope.Keterangan,
          'Supervisor': $scope.supervisor
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data area proyek berhasil diupdate <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-proyek-area/' + $routeParams.proyekId;
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data area proyek gagal diupdate. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('ProyekGambarController', function ($rootScope, $scope, $route, $http, ngToast, $routeParams) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-gambar.php?act=DisplayData&IDProyek=' + $routeParams.proyekId).success(function (data, status) {
      $scope.data = data.payload.data;
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;
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
        url: 'api/proyek/proyek-gambar.php?act=Delete',
        data: $.param({
          'IDProyekGambar': val,
          'IDProyek': $routeParams.proyekId
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data gambar proyek berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data gambar proyek gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }
});

tripApp.controller('ProyekGambarNewController', function ($scope, $route, $q, $http, ngToast, $routeParams, Upload) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-gambar.php?act=LoadAllRequirement&IDProyek=' + $routeParams.proyekId).success(function (data, status) {
      $scope.data = data.payload.data;
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;

      $scope.Kategori = "Kontrak";
      $scope.Keterangan = "";
    });
  };
  $scope.getdata();

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/proyek/proyek-gambar.php?act=InsertNew',
        data: {
          'IDProyek': $routeParams.proyekId,
          'Kategori': $scope.Kategori,
          'Keterangan': $scope.Keterangan,
          'file': $scope.file
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data gambar proyek berhasil disimpan <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-proyek-gambar/' + $routeParams.proyekId;
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data gambar proyek gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('ProyekGambarEditController', function ($scope, $route, $q, $http, ngToast, $routeParams, Upload) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-gambar.php?act=Detail&IDProyek=' + $routeParams.proyekId + '&IDProyekGambar=' + $routeParams.gambarId).success(function (data, status) {
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;

      $scope.Kategori = data.payload.data.Kategori;
      $scope.Keterangan = data.payload.data.Keterangan;
      $scope.file = data.payload.data.FileGambar;
      $scope.fileview = data.payload.data.FileGambar;
    });
  };
  $scope.getdata();

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/proyek/proyek-gambar.php?act=Update',
        data: {
          'IDProyek': $routeParams.proyekId,
          'IDProyekGambar': $routeParams.gambarId,
          'Kategori': $scope.Kategori,
          'Keterangan': $scope.Keterangan,
          'file': $scope.file
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data gambar proyek berhasil diperbaharui <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-proyek-gambar/' + $routeParams.proyekId;
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data gambar proyek gagal diperbaharui. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('DokumenProyekController', function ($rootScope, $scope, $route, $http, ngToast, $routeParams) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-dokumen.php?act=DisplayData&IDProyek=' + $routeParams.proyekId).success(function (data, status) {
      $scope.data = data.payload.data;
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;
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
        url: 'api/proyek/proyek-dokumen.php?act=Delete',
        data: $.param({
          'IDProyekFile': val,
          'IDProyek': $routeParams.proyekId
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data Dokumen Proyek berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data Dokumen Proyek gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }
});

tripApp.controller('DokumenProyekNewController', function ($scope, $route, $q, $http, ngToast, $routeParams, Upload) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-dokumen.php?act=LoadAllRequirement&IDProyek=' + $routeParams.proyekId).success(function (data, status) {
      $scope.data = data.payload.data;
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;
    });
  };
  $scope.getdata();

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/proyek/proyek-dokumen.php?act=InsertNew',
        data: {
          'IDProyek': $routeParams.proyekId,
          'Name': $scope.Name,
          'FileType': $scope.FileType,
          'file': $scope.file
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data Dokumen Proyek berhasil disimpan <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-dokumen-proyek/' + $routeParams.proyekId;
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data Dokumen Proyek gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('DokumenProyekEditController', function ($scope, $route, $q, $http, ngToast, $routeParams, Upload) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-dokumen.php?act=Detail&IDProyek=' + $routeParams.proyekId + '&IDProyekFile=' + $routeParams.dokumenId).success(function (data, status) {
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;

      $scope.Name = data.payload.data.Name;
      $scope.FileType = data.payload.data.FileType;
      $scope.file = data.payload.data.FileName;
      $scope.fileview = data.payload.data.FileName;
    });
  };
  $scope.getdata();

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/proyek/proyek-dokumen.php?act=Update',
        data: {
          'IDProyek': $routeParams.proyekId,
          'IDProyekFile': $routeParams.dokumenId,
          'Name': $scope.Name,
          'FileType': $scope.FileType,
          'file': $scope.file
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data Dokumen Proyek berhasil diperbaharui <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-dokumen-proyek/' + $routeParams.proyekId;
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data Dokumen Proyek gagal diperbaharui. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});


tripApp.controller('ProyekRetensiController', function ($rootScope, $scope, $route, $http, ngToast, $routeParams) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-retensi.php?act=DisplayData&IDProyek=' + $routeParams.proyekId).success(function (data, status) {
      $scope.data = data.payload.data;
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;
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
        url: 'api/proyek/proyek-retensi.php?act=Delete',
        data: $.param({
          'IDProyekRetensi': val,
          'IDProyek': $routeParams.proyekId
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data proyek retensi berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data proyek retensi gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }
});

tripApp.controller('ProyekRetensiNewController', function ($scope, $rootScope, $route, $q, $http, ngToast, $routeParams, Upload) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-retensi.php?act=LoadAllRequirement&IDProyek=' + $routeParams.proyekId).success(function (data, status) {
      $scope.data = data.payload.data;
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;

      $scope.JatuhTempo = $rootScope.currentDateID;
      $scope.Keterangan = "";
    });
  };
  $scope.getdata();

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/proyek/proyek-retensi.php?act=InsertNew',
        data: {
          'IDProyek': $routeParams.proyekId,
          'JatuhTempo': $scope.JatuhTempo,
          'Keterangan': $scope.Keterangan,
          'file': $scope.file
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data proyek retensi berhasil disimpan <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-proyek-retensi/' + $routeParams.proyekId;
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data proyek retensi gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('ProyekRetensiEditController', function ($scope, $route, $q, $http, ngToast, $routeParams, Upload) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-retensi.php?act=Detail&IDProyek=' + $routeParams.proyekId + '&IDProyekRetensi=' + $routeParams.retensiId).success(function (data, status) {
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;

      $scope.JatuhTempo = data.payload.data.JatuhTempoID;
      $scope.Keterangan = data.payload.data.Keterangan;
      $scope.file = data.payload.data.FileRetensi;
      $scope.fileview = data.payload.data.FileRetensi;
    });
  };
  $scope.getdata();

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/proyek/proyek-retensi.php?act=Update',
        data: {
          'IDProyek': $routeParams.proyekId,
          'IDProyekRetensi': $routeParams.retensiId,
          'JatuhTempo': $scope.JatuhTempo,
          'Keterangan': $scope.Keterangan,
          'file': $scope.file
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data proyek retensi berhasil diperbaharui <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-proyek-retensi/' + $routeParams.proyekId;
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data proyek retensi gagal diperbaharui. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('ProyekSIController', function ($rootScope, $scope, $route, $http, ngToast, $routeParams) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-si.php?act=DisplayData&IDProyek=' + $routeParams.proyekId).success(function (data, status) {
      $scope.data = data.payload.data;
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;
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
        url: 'api/proyek/proyek-si.php?act=Delete',
        data: $.param({
          'IDProyekSiteInstruction': val,
          'IDProyek': $routeParams.proyekId
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data proyek site instruction berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data proyek site instruction gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }
});

tripApp.controller('ProyekSINewController', function ($scope, $rootScope, $route, $q, $http, ngToast, $routeParams, Upload) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-si.php?act=LoadAllRequirement&IDProyek=' + $routeParams.proyekId).success(function (data, status) {
      $scope.data = data.payload.data;
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;

      $scope.Nama = "";
      $scope.Keterangan = "";
    });
  };
  $scope.getdata();

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/proyek/proyek-si.php?act=InsertNew',
        data: {
          'IDProyek': $routeParams.proyekId,
          'Nama': $scope.Nama,
          'Keterangan': $scope.Keterangan,
          'file': $scope.file
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data proyek site instruction berhasil disimpan <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-proyek-si/' + $routeParams.proyekId;
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data proyek site instruction gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('ProyekSIEditController', function ($scope, $route, $q, $http, ngToast, $routeParams, Upload) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-si.php?act=Detail&IDProyek=' + $routeParams.proyekId + '&IDProyekSiteInstruction=' + $routeParams.siId).success(function (data, status) {
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;

      $scope.Nama = data.payload.data.Nama;
      $scope.Keterangan = data.payload.data.Keterangan;
      $scope.file = data.payload.data.FileSiteInstruction;
      $scope.fileview = data.payload.data.FileSiteInstruction;
    });
  };
  $scope.getdata();

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/proyek/proyek-si.php?act=Update',
        data: {
          'IDProyek': $routeParams.proyekId,
          'IDProyekSiteInstruction': $routeParams.siId,
          'Nama': $scope.Nama,
          'Keterangan': $scope.Keterangan,
          'file': $scope.file
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data proyek site instruction berhasil diperbaharui <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-proyek-si/' + $routeParams.proyekId;
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data proyek site instruction gagal diperbaharui. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('ProyekRFIController', function ($rootScope, $scope, $route, $http, ngToast, $routeParams) {
  $scope.filterstatus = "";
  $scope.activeMenu = '';

  $scope.all = '';
  $scope.new = '';
  $scope.approved = '';

  $scope.getdata = function () {
    $http.get('api/proyek/proyek-rfi.php?act=DisplayData&IDProyek=' + $routeParams.proyekId + '&Tipe=' + $scope.filterstatus).success(function (data, status) {
      $scope.data = data.payload.data;
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;
      $scope.all = data.payload.all;
      $scope.new = data.payload.new;
      $scope.approved = data.payload.approved;
    });
  };
  $scope.getdata();

  $scope.doFilter = function (a) {
    $scope.filterstatus = a;
    $scope.activeMenu = a;
    $scope.refreshData();
  }

  $scope.refreshData = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getdata();
  }

  $scope.removeRow = function (val) {
    if (confirm("Anda yakin ingin menghapus data ini?")) {
      $http({
        method: "POST",
        url: 'api/proyek/proyek-rfi.php?act=Delete',
        data: $.param({
          'IDProyekRFI': val,
          'IDProyek': $routeParams.proyekId
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data proyek request for information berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data proyek request for information gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }
});

tripApp.controller('ProyekRFINewController', function ($scope, $rootScope, $route, $q, $http, ngToast, $routeParams, Upload) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-rfi.php?act=LoadAllRequirement&IDProyek=' + $routeParams.proyekId).success(function (data, status) {
      $scope.data = data.payload.data;
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;

      $scope.Nama = "";
      $scope.Keterangan = "";
    });
  };
  $scope.getdata();

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/proyek/proyek-rfi.php?act=InsertNew',
        data: {
          'IDProyek': $routeParams.proyekId,
          'Nama': $scope.Nama,
          'TipeRFI': $scope.TipeRFI,
          'Keterangan': $scope.Keterangan,
          'file': $scope.file
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data proyek request for information berhasil disimpan <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-proyek-rfi/' + $routeParams.proyekId;
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data proyek request for information gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('ProyekRFIEditController', function ($scope, $route, $q, $http, ngToast, $routeParams, Upload) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-rfi.php?act=Detail&IDProyek=' + $routeParams.proyekId + '&IDProyekRFI=' + $routeParams.rfiId).success(function (data, status) {
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;

      $scope.Nama = data.payload.data.Nama;
      $scope.TipeRFI = data.payload.data.TipeRFI;
      $scope.Keterangan = data.payload.data.Keterangan;
      $scope.file = data.payload.data.FileRFI;
      $scope.fileview = data.payload.data.FileRFI;
    });
  };
  $scope.getdata();

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/proyek/proyek-rfi.php?act=Update',
        data: {
          'IDProyek': $routeParams.proyekId,
          'IDProyekRFI': $routeParams.rfiId,
          'Nama': $scope.Nama,
          'TipeRFI': $scope.TipeRFI,
          'Keterangan': $scope.Keterangan,
          'file': $scope.file
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data proyek request for information berhasil diperbaharui <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-proyek-rfi/' + $routeParams.proyekId;
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data proyek request for information gagal diperbaharui. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('ProyekEOTController', function ($rootScope, $scope, $route, $http, ngToast, $routeParams) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-eot.php?act=DisplayData&IDProyek=' + $routeParams.proyekId).success(function (data, status) {
      $scope.data = data.payload.data;
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;
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
        url: 'api/proyek/proyek-eot.php?act=Delete',
        data: $.param({
          'IDProyekEOT': val,
          'IDProyek': $routeParams.proyekId
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data Extension Of Time (EOT) berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data Extension Of Time (EOT) gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }
});

tripApp.controller('ProyekEOTNewController', function ($scope, $rootScope, $route, $q, $http, ngToast, $routeParams, Upload) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-eot.php?act=LoadAllRequirement&IDProyek=' + $routeParams.proyekId).success(function (data, status) {
      $scope.data = data.payload.data;
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;

      $scope.Nama = "";
      $scope.Keterangan = "";
    });
  };
  $scope.getdata();

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/proyek/proyek-eot.php?act=InsertNew',
        data: {
          'IDProyek': $routeParams.proyekId,
          'Nama': $scope.Nama,
          'Keterangan': $scope.Keterangan,
          'file': $scope.file
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data Extension Of Time (EOT) berhasil disimpan <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-proyek-eot/' + $routeParams.proyekId;
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data Extension Of Time (EOT) gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('ProyekEOTEditController', function ($scope, $route, $q, $http, ngToast, $routeParams, Upload) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-eot.php?act=Detail&IDProyek=' + $routeParams.proyekId + '&IDProyekEOT=' + $routeParams.eotId).success(function (data, status) {
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;

      $scope.Nama = data.payload.data.Nama;
      $scope.Keterangan = data.payload.data.Keterangan;
      $scope.file = data.payload.data.FileEOT;
      $scope.fileview = data.payload.data.FileEOT;
    });
  };
  $scope.getdata();

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/proyek/proyek-eot.php?act=Update',
        data: {
          'IDProyek': $routeParams.proyekId,
          'IDProyekEOT': $routeParams.eotId,
          'Nama': $scope.Nama,
          'Keterangan': $scope.Keterangan,
          'file': $scope.file
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data Extension Of Time (EOT) berhasil diperbaharui <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-proyek-eot/' + $routeParams.proyekId;
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data Extension Of Time (EOT) gagal diperbaharui. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('ProyekApprovalMaterialController', function ($rootScope, $scope, $route, $http, ngToast, $routeParams) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-approval-material.php?act=DisplayData&IDProyek=' + $routeParams.proyekId).success(function (data, status) {
      $scope.data = data.payload.data;
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;
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
        url: 'api/proyek/proyek-approval-material.php?act=Delete',
        data: $.param({
          'IDProyekApprovalMaterial': val,
          'IDProyek': $routeParams.proyekId
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data Approval Material berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data Approval Material gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }
});

tripApp.controller('ProyekApprovalMaterialNewController', function ($scope, $rootScope, $route, $q, $http, ngToast, $routeParams, Upload) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-approval-material.php?act=LoadAllRequirement&IDProyek=' + $routeParams.proyekId).success(function (data, status) {
      $scope.data = data.payload.data;
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;

      $scope.Nama = "";
      $scope.Keterangan = "";
    });
  };
  $scope.getdata();

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/proyek/proyek-approval-material.php?act=InsertNew',
        data: {
          'IDProyek': $routeParams.proyekId,
          'Nama': $scope.Nama,
          'Keterangan': $scope.Keterangan,
          'file': $scope.file
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data Approval Material berhasil disimpan <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-proyek-approval-material/' + $routeParams.proyekId;
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data Approval Material gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('ProyekApprovalMaterialEditController', function ($scope, $route, $q, $http, ngToast, $routeParams, Upload) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-approval-material.php?act=Detail&IDProyek=' + $routeParams.proyekId + '&IDProyekApprovalMaterial=' + $routeParams.approvMaterialId).success(function (data, status) {
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;

      $scope.Nama = data.payload.data.Nama;
      $scope.Keterangan = data.payload.data.Keterangan;
      $scope.file = data.payload.data.FileApprovalMaterial;
      $scope.fileview = data.payload.data.FileApprovalMaterial;
    });
  };
  $scope.getdata();

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/proyek/proyek-approval-material.php?act=Update',
        data: {
          'IDProyek': $routeParams.proyekId,
          'IDProyekApprovalMaterial': $routeParams.approvMaterialId,
          'Nama': $scope.Nama,
          'Keterangan': $scope.Keterangan,
          'file': $scope.file
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data Approval Material berhasil diperbaharui <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-proyek-approval-material/' + $routeParams.proyekId;
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data Approval Material gagal diperbaharui. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('ProyekSPController', function ($rootScope, $scope, $route, $http, ngToast, $routeParams) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-sertifikat-pembayaran.php?act=DisplayData&IDProyek=' + $routeParams.proyekId).success(function (data, status) {
      $scope.data = data.payload.data;
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;
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
        url: 'api/proyek/proyek-sertifikat-pembayaran.php?act=Delete',
        data: $.param({
          'IDProyekSertifikatPembayaran': val,
          'IDProyek': $routeParams.proyekId
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data sertifikat pembayaran proyek berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data sertifikat pembayaran proyek gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }
});

tripApp.controller('ProyekSPNewController', function ($scope, $rootScope, $route, $q, $http, ngToast, $routeParams, Upload) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-sertifikat-pembayaran.php?act=LoadAllRequirement&IDProyek=' + $routeParams.proyekId).success(function (data, status) {
      $scope.data = data.payload.data;
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;

      $scope.Nama = "";
      $scope.Keterangan = "";
    });
  };
  $scope.getdata();

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/proyek/proyek-sertifikat-pembayaran.php?act=InsertNew',
        data: {
          'IDProyek': $routeParams.proyekId,
          'Nama': $scope.Nama,
          'Keterangan': $scope.Keterangan,
          'file': $scope.file
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data sertifikat pembayaran proyek berhasil disimpan <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-proyek-sertifikat-pembayaran/' + $routeParams.proyekId;
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data sertifikat pembayaran proyek gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('ProyekSPEditController', function ($scope, $route, $q, $http, ngToast, $routeParams, Upload) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-sertifikat-pembayaran.php?act=Detail&IDProyek=' + $routeParams.proyekId + '&IDProyekSertifikatPembayaran=' + $routeParams.spId).success(function (data, status) {
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;

      $scope.Nama = data.payload.data.Nama;
      $scope.Keterangan = data.payload.data.Keterangan;
      $scope.file = data.payload.data.FileSertifikatPembayaran;
      $scope.fileview = data.payload.data.FileSertifikatPembayaran;
    });
  };
  $scope.getdata();

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/proyek/proyek-sertifikat-pembayaran.php?act=Update',
        data: {
          'IDProyek': $routeParams.proyekId,
          'IDProyekSertifikatPembayaran': $routeParams.spId,
          'Nama': $scope.Nama,
          'Keterangan': $scope.Keterangan,
          'file': $scope.file
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data sertifikat pembayaran proyek berhasil diperbaharui <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-proyek-sertifikat-pembayaran/' + $routeParams.proyekId;
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data sertifikat pembayaran proyek gagal diperbaharui. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('ProyekFileHandoverController', function ($rootScope, $scope, $route, $http, ngToast, $routeParams) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-file-handover.php?act=DisplayData&IDProyek=' + $routeParams.proyekId).success(function (data, status) {
      $scope.data = data.payload.data;
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;
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
        url: 'api/proyek/proyek-file-handover.php?act=Delete',
        data: $.param({
          'IDProyekFileHandover': val,
          'IDProyek': $routeParams.proyekId
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data file handover proyek berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data file handover proyek gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }
});

tripApp.controller('ProyekFileHandoverNewController', function ($scope, $route, $q, $http, ngToast, $routeParams, Upload) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-file-handover.php?act=LoadAllRequirement&IDProyek=' + $routeParams.proyekId).success(function (data, status) {
      $scope.data = data.payload.data;
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;

      $scope.Kategori = "Berita Acara";
      $scope.Keterangan = "";
    });
  };
  $scope.getdata();

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/proyek/proyek-file-handover.php?act=InsertNew',
        data: {
          'IDProyek': $routeParams.proyekId,
          'Kategori': $scope.Kategori,
          'Keterangan': $scope.Keterangan,
          'file': $scope.file
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data file handover proyek berhasil disimpan <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-proyek-file-handover/' + $routeParams.proyekId;
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data file handover proyek gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('ProyekFileHandoverEditController', function ($scope, $route, $q, $http, ngToast, $routeParams, Upload) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-file-handover.php?act=Detail&IDProyek=' + $routeParams.proyekId + '&IDProyekFileHandover=' + $routeParams.fhId).success(function (data, status) {
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;

      $scope.Kategori = data.payload.data.Kategori;
      $scope.Keterangan = data.payload.data.Keterangan;
      $scope.file = data.payload.data.FileHandover;
      $scope.fileview = data.payload.data.FileHandover;
    });
  };
  $scope.getdata();

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/proyek/proyek-file-handover.php?act=Update',
        data: {
          'IDProyek': $routeParams.proyekId,
          'IDProyekFileHandover': $routeParams.fhId,
          'Kategori': $scope.Kategori,
          'Keterangan': $scope.Keterangan,
          'file': $scope.file
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data file handover proyek berhasil diperbaharui <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-proyek-file-handover/' + $routeParams.proyekId;
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data file handover proyek gagal diperbaharui. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('ProyekPettyCashController', function ($rootScope, $scope, $route, $http, ngToast, $routeParams) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-petty-cash.php?act=DisplayData&IDProyek=' + $routeParams.proyekId).success(function (data, status) {
      $scope.data = data.payload.data;
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;
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
        url: 'api/proyek/proyek-petty-cash.php?act=Delete',
        data: $.param({
          'IDProyekPCCashFlow': val,
          'IDProyek': $routeParams.proyekId
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data pengeluaran petty cash proyek berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data pengeluaran petty cash proyek gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }
});

tripApp.controller('ProyekPettyCashNewController', function ($scope, $rootScope, $route, $q, $http, ngToast, $routeParams, Upload) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-petty-cash.php?act=LoadAllRequirement&IDProyek=' + $routeParams.proyekId).success(function (data, status) {
      $scope.data = data.payload.data;
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;

      $scope.Tanggal = $rootScope.currentDateID;
      $scope.NoNota = "";
      $scope.Jumlah = "0";
      $scope.Keterangan = "";
    });
  };
  $scope.getdata();

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/proyek/proyek-petty-cash.php?act=InsertNew',
        data: {
          'IDProyek': $routeParams.proyekId,
          'Tanggal': $scope.Tanggal,
          'NoNota': $scope.NoNota,
          'Keterangan': $scope.Keterangan,
          'Jumlah': $scope.Jumlah
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data pengeluaran petty cash proyek berhasil disimpan <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-proyek-petty-cash/' + $routeParams.proyekId;
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data pengeluaran petty cash proyek gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('ProyekPettyCashEditController', function ($scope, $route, $q, $http, ngToast, $routeParams, Upload) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-petty-cash.php?act=Detail&IDProyek=' + $routeParams.proyekId + '&IDProyekPCCashFlow=' + $routeParams.pcId).success(function (data, status) {
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;

      $scope.Tanggal = data.payload.data.TanggalID;
      $scope.NoNota = data.payload.data.NoNota;
      $scope.Keterangan = data.payload.data.Keterangan;
      $scope.Jumlah = data.payload.data.Jumlah;
    });
  };
  $scope.getdata();

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/proyek/proyek-petty-cash.php?act=Update',
        data: {
          'IDProyek': $routeParams.proyekId,
          'IDProyekPCCashFlow': $routeParams.pcId,
          'Tanggal': $scope.Tanggal,
          'NoNota': $scope.NoNota,
          'Keterangan': $scope.Keterangan,
          'Jumlah': $scope.Jumlah
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data pengeluaran petty cash proyek berhasil diperbaharui <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-proyek-petty-cash/' + $routeParams.proyekId;
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data pengeluaran petty cash proyek gagal diperbaharui. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('ProyekFileInvoiceController', function ($rootScope, $scope, $route, $http, ngToast, $routeParams) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-file-invoice.php?act=DisplayData&IDProyek=' + $routeParams.proyekId).success(function (data, status) {
      $scope.data = data.payload.data;
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;
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
        url: 'api/proyek/proyek-file-invoice.php?act=Delete',
        data: $.param({
          'IDProyekFileInvoice': val,
          'IDProyek': $routeParams.proyekId
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data file invoice proyek berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data file invoice proyek gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }
});

tripApp.controller('ProyekFileInvoiceNewController', function ($scope, $route, $q, $http, ngToast, $routeParams, Upload) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-file-invoice.php?act=LoadAllRequirement&IDProyek=' + $routeParams.proyekId).success(function (data, status) {
      $scope.data = data.payload.data;
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;

      $scope.Nama = "";
      $scope.Keterangan = "";
    });
  };
  $scope.getdata();

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/proyek/proyek-file-invoice.php?act=InsertNew',
        data: {
          'IDProyek': $routeParams.proyekId,
          'Nama': $scope.Nama,
          'Keterangan': $scope.Keterangan,
          'file': $scope.file
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data file invoice proyek berhasil disimpan <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-proyek-file-invoice/' + $routeParams.proyekId;
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data file invoice proyek gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('ProyekFileInvoiceEditController', function ($scope, $route, $q, $http, ngToast, $routeParams, Upload) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-file-invoice.php?act=Detail&IDProyek=' + $routeParams.proyekId + '&IDProyekFileInvoice=' + $routeParams.fhId).success(function (data, status) {
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;

      $scope.Nama = data.payload.data.Nama;
      $scope.Keterangan = data.payload.data.Keterangan;
      $scope.file = data.payload.data.File;
      $scope.fileview = data.payload.data.File;
    });
  };
  $scope.getdata();

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/proyek/proyek-file-invoice.php?act=Update',
        data: {
          'IDProyek': $routeParams.proyekId,
          'IDProyekFileInvoice': $routeParams.fhId,
          'Nama': $scope.Nama,
          'Keterangan': $scope.Keterangan,
          'file': $scope.file
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data file invoice proyek berhasil diperbaharui <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-proyek-file-invoice/' + $routeParams.proyekId;
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data file invoice proyek gagal diperbaharui. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('ProyekFilePPHController', function ($rootScope, $scope, $route, $http, ngToast, $routeParams) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-file-pph.php?act=DisplayData&IDProyek=' + $routeParams.proyekId).success(function (data, status) {
      $scope.data = data.payload.data;
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;
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
        url: 'api/proyek/proyek-file-pph.php?act=Delete',
        data: $.param({
          'IDProyekFilePPH': val,
          'IDProyek': $routeParams.proyekId
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data file pph proyek berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data file pph proyek gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }
});

tripApp.controller('ProyekFilePPHNewController', function ($scope, $route, $q, $http, ngToast, $routeParams, Upload) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-file-pph.php?act=LoadAllRequirement&IDProyek=' + $routeParams.proyekId).success(function (data, status) {
      $scope.data = data.payload.data;
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;

      $scope.Nama = "";
      $scope.Keterangan = "";
    });
  };
  $scope.getdata();

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/proyek/proyek-file-pph.php?act=InsertNew',
        data: {
          'IDProyek': $routeParams.proyekId,
          'Nama': $scope.Nama,
          'Keterangan': $scope.Keterangan,
          'file': $scope.file
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data file pph proyek berhasil disimpan <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-proyek-file-pph/' + $routeParams.proyekId;
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data file pph proyek gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('ProyekFilePPHEditController', function ($scope, $route, $q, $http, ngToast, $routeParams, Upload) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-file-pph.php?act=Detail&IDProyek=' + $routeParams.proyekId + '&IDProyekFilePPH=' + $routeParams.fhId).success(function (data, status) {
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;

      $scope.Nama = data.payload.data.Nama;
      $scope.Keterangan = data.payload.data.Keterangan;
      $scope.file = data.payload.data.File;
      $scope.fileview = data.payload.data.File;
    });
  };
  $scope.getdata();

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/proyek/proyek-file-pph.php?act=Update',
        data: {
          'IDProyek': $routeParams.proyekId,
          'IDProyekFilePPH': $routeParams.fhId,
          'Nama': $scope.Nama,
          'Keterangan': $scope.Keterangan,
          'file': $scope.file
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data file pph proyek berhasil diperbaharui <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-proyek-file-pph/' + $routeParams.proyekId;
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data file pph proyek gagal diperbaharui. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('ProyekFileFPController', function ($rootScope, $scope, $route, $http, ngToast, $routeParams) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-file-faktur-pajak.php?act=DisplayData&IDProyek=' + $routeParams.proyekId).success(function (data, status) {
      $scope.data = data.payload.data;
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;
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
        url: 'api/proyek/proyek-file-faktur-pajak.php?act=Delete',
        data: $.param({
          'IDProyekFileFP': val,
          'IDProyek': $routeParams.proyekId
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data file faktur pajak proyek berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data file faktur pajak proyek gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }
});

tripApp.controller('ProyekFileFPNewController', function ($scope, $route, $q, $http, ngToast, $routeParams, Upload) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-file-faktur-pajak.php?act=LoadAllRequirement&IDProyek=' + $routeParams.proyekId).success(function (data, status) {
      $scope.data = data.payload.data;
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;

      $scope.Nama = "";
      $scope.Keterangan = "";
    });
  };
  $scope.getdata();

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/proyek/proyek-file-faktur-pajak.php?act=InsertNew',
        data: {
          'IDProyek': $routeParams.proyekId,
          'Nama': $scope.Nama,
          'Keterangan': $scope.Keterangan,
          'file': $scope.file
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data file faktur pajak proyek berhasil disimpan <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-proyek-file-faktur-pajak/' + $routeParams.proyekId;
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data file faktur pajak proyek gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('ProyekFileFPEditController', function ($scope, $route, $q, $http, ngToast, $routeParams, Upload) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-file-faktur-pajak.php?act=Detail&IDProyek=' + $routeParams.proyekId + '&IDProyekFileFP=' + $routeParams.fhId).success(function (data, status) {
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;

      $scope.Nama = data.payload.data.Nama;
      $scope.Keterangan = data.payload.data.Keterangan;
      $scope.file = data.payload.data.File;
      $scope.fileview = data.payload.data.File;
    });
  };
  $scope.getdata();

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/proyek/proyek-file-faktur-pajak.php?act=Update',
        data: {
          'IDProyek': $routeParams.proyekId,
          'IDProyekFileFP': $routeParams.fhId,
          'Nama': $scope.Nama,
          'Keterangan': $scope.Keterangan,
          'file': $scope.file
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data file faktur pajak proyek berhasil diperbaharui <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-proyek-file-faktur-pajak/' + $routeParams.proyekId;
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data file faktur pajak proyek gagal diperbaharui. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('ProyekFileLPController', function ($rootScope, $scope, $route, $http, ngToast, $routeParams) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-file-laporan-proyek.php?act=DisplayData&IDProyek=' + $routeParams.proyekId).success(function (data, status) {
      $scope.data = data.payload.data;
      console.log($scope.data);
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;
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
        url: 'api/proyek/proyek-file-laporan-proyek.php?act=Delete',
        data: $.param({
          'IDProyekFileLP': val,
          'IDProyek': $routeParams.proyekId
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data file laporan proyek berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data file laporan proyek gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }
});

tripApp.controller('ProyekFileLPNewController', function ($scope, $route, $q, $http, ngToast, $routeParams, Upload) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-file-laporan-proyek.php?act=LoadAllRequirement&IDProyek=' + $routeParams.proyekId).success(function (data, status) {
      $scope.data = data.payload.data;
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;

      $scope.Nama = "";
      $scope.Keterangan = "";
    });
  };
  $scope.getdata();

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/proyek/proyek-file-laporan-proyek.php?act=InsertNew',
        data: {
          'IDProyek': $routeParams.proyekId,
          'Nama': $scope.Nama,
          'Keterangan': $scope.Keterangan,
          'file': $scope.file
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data file laporan proyek berhasil disimpan <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-proyek-file-laporan-proyek/' + $routeParams.proyekId;
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data file laporan proyek gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});

tripApp.controller('ProyekFileLPEditController', function ($scope, $route, $q, $http, ngToast, $routeParams, Upload) {
  $scope.getdata = function () {
    $http.get('api/proyek/proyek-file-laporan-proyek.php?act=Detail&IDProyek=' + $routeParams.proyekId + '&IDProyekFileLP=' + $routeParams.fhId).success(function (data, status) {
      $scope.IDProyek = $routeParams.proyekId;
      $scope.KodeProyek = data.payload.proyek.KodeProyek;
      $scope.Tahun = data.payload.proyek.Tahun;
      $scope.NamaProyek = data.payload.proyek.NamaProyek;

      $scope.Nama = data.payload.data.Nama;
      $scope.Keterangan = data.payload.data.Keterangan;
      $scope.file = data.payload.data.File;
      $scope.fileview = data.payload.data.File;
    });
  };
  $scope.getdata();

  $scope.processing = false;
  $scope.submitForm = function (isValid) {
    if (isValid) {
      $scope.processing = true;
      Upload.upload({
        url: 'api/proyek/proyek-file-laporan-proyek.php?act=Update',
        data: {
          'IDProyek': $routeParams.proyekId,
          'IDProyekFileLP': $routeParams.fhId,
          'Nama': $scope.Nama,
          'Keterangan': $scope.Keterangan,
          'file': $scope.file
        }
      }).then(function (resp) {
        var data = resp.data;
        if (data.payload.response == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data file laporan proyek berhasil diperbaharui <i class="fa fa-remove"></i>'
          });
          window.document.location = '#/data-proyek-file-laporan-proyek/' + $routeParams.proyekId;
        } else {
          $scope.processing = false;
          ngToast.create({
            className: 'danger',
            content: 'Data file laporan proyek gagal diperbaharui. Silahkan coba kembali. <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  };
});