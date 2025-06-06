tripApp.controller('KaryawanController', function ($rootScope, $scope, $route, $http, ngToast, $location) {
  $scope.jabatan = "";
  $scope.departement = "";
  $scope.status_karyawan = "";
  $scope.proyek = "";
  $scope.agama = "";
  $scope.mark = "";
  $scope.status_akun = "";
  $scope.rfidcodefocus = true;
  $scope.filterstatus = "1";
  $scope.activeMenu = '1';

  $scope.newUrl = '#/karyawan/new/';
  $scope.editUrl = '#/karyawan/edit/';
  $scope.profileUrl = '#/karyawan/profile/';

  var path = $location.path();
  $scope.isHarian = false;
  if (path.indexOf('karyawan-harian') > -1) {
    $scope.isHarian = true;
    $scope.status_karyawan = 'Harian';
    $scope.newUrl = '#/karyawan-harian/new/';
    $scope.editUrl = '#/karyawan-harian/edit/';
    $scope.profileUrl = '#/karyawan-harian/profile/';
  }

  $scope.showModal = function (a, b, c) {
    $scope.person_name = a;
    $scope.idk = b;
    $scope.rfidcode = c;
    //$scope.rfidcodefocus = true;

    $('#myModal').modal('show');
    $('#myModal').children('.modal-dialog').removeClass('modal-lg');
    $('#myModal').on('shown.bs.modal', function () {
      $('#rfidcode').focus();
    });
  };

  $scope.doFilter = function (a) {
    $scope.filterstatus = a;
    $scope.activeMenu = a;
    $scope.refreshData();
  }

  $scope.updateRFID = function () {
    $http.get('api/karyawan/update-rfid.php?key=' + $scope.rfidcode + '&idk=' + $scope.idk).success(function (data, status) {
      if (data == "1") {
        ngToast.create({
          className: 'success',
          content: 'RFID Code milik ' + $scope.person_name + ' berhasil diubah <i class="fa fa-remove"></i>'
        });
      } else if (data == "2") {
        ngToast.create({
          className: 'danger',
          content: 'RFID telah digunakan oleh Karyawan Lain. Silahkan gunakan RFID lain.. <i class="fa fa-remove"></i>'
        });
      } else {
        ngToast.create({
          className: 'danger',
          content: 'RFID gagal diubah. Silahkan coba kembali.. <i class="fa fa-remove"></i>'
        });
      }
    });
  }

  $scope.getAllData = function () {
    $http.get('api/karyawan/karyawan.php?act=DataKaryawan&jabatan=' + $scope.jabatan + '&departement=' + $scope.departement + '&status_karyawan=' + $scope.status_karyawan + '&mark=' + $scope.mark + '&status_akun=' + $scope.filterstatus + '&proyek=' + $scope.proyek + '&agama=' + $scope.agama).success(function (data, status) {
      $scope.data_karyawan = data.karyawan;
      $scope.data_jabatan = data.jabatan;
      $scope.data_departement = data.departement;
      $scope.data_proyek = data.proyek;
      $scope.all = data.all;
      $scope.active = data.active;
      $scope.resign = data.resign;
    });
  };

  $scope.change = function () {
    $scope.counter++;
  };

  $scope.getAllData();

  $scope.refreshData = function () {
    $('#basicTable').dataTable().fnDestroy();
    $scope.getAllData();
  }

  $scope.removeRow = function (val) {
    if (confirm("Anda yakin ingin menghapus data ini?")) {
      /*
      Explicit untuk menghilangkan record yang ada. Tidak cocok ketika menggunakan datatables.
      var index = -1;     
      var comArr = eval( $scope.data_user );
      for( var i = 0; i < comArr.length; i++ ) {
          if( comArr[i].IDUser === val ) {
              index = i;
              break;
          }
      }
      if( index === -1 ) {
          alert( "Something gone wrong" );
      }
      $scope.data_user.splice( index, 1 );
      */
      $http({
        method: "POST",
        url: 'api/karyawan/delete.php',
        data: $.param({
          'idk': val
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data karyawan berhasil dihapus <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data karyawan gagal dihapus. Silahkan coba kembali lagi <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }

  $scope.base64 = function (str) {
    return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g,
      function toSolidBytes(match, p1) {
        return String.fromCharCode('0x' + p1);
      }));
  }

  $scope.base64URL = function (url) {
    return $scope.base64(url);
  }

  $scope.setFingerPrintURL = function (IDKaryawan) {
    var base64url = $scope.base64URL('http://10.1.1.141/smartoffice/register.php?IDKaryawan=' + IDKaryawan);
    return 'finspot:FingerspotReg;' + base64url;
  }
  /* Finger Print */
  $scope.registerFingerPrint = function (IDKaryawan, NamaKaryawan, CurrentData, index) {
    $('body').ajaxMask();

    regStats = 0;
    regCt = -1;
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
      console.log("'" + NamaKaryawan + "' registration checking...");

      $http.get('api/karyawan/get-fp.php?id=' + IDKaryawan + '&current=' + CurrentData).success(function (data, status) {
        if (data === '1') {
          timer_register.stop();
          console.log("'" + NamaKaryawan + "' registration checking end");

          alert("'" + NamaKaryawan + "' registration success!");
          $('body').ajaxMask({ stop: true });

          $scope.data_karyawan[index].CountFinger = parseInt($scope.data_karyawan[index].CountFinger) + 1;
        }
      });

      if (ct >= limit) {
        timer_register.stop();
        console.log("'" + NamaKaryawan + "' registration checking end");

        alert("'" + NamaKaryawan + "' registration fail!");
        $('body').ajaxMask({ stop: true });
      }

      ct++;
    });
  }

  $scope.resetFP = function (val) {
    if (confirm("Anda yakin ingin mereset Finger Print Staff ini?")) {
      $http({
        method: "POST",
        url: 'api/karyawan/clear-fp.php',
        data: $.param({
          'idk': val
        }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
      }).success(function (data, status) {
        if (data == "1") {
          ngToast.create({
            className: 'success',
            content: 'Data Finger Print berhasil direset <i class="fa fa-remove"></i>'
          });
          $route.reload();
        } else {
          ngToast.create({
            className: 'danger',
            content: 'Data Finger Print gagal direset. Silahkan coba kembali lagi <i class="fa fa-remove"></i>'
          });
        }
      });
    }
  }

  $scope.doPrint = function (a) {
    window.open($rootScope.baseURL + 'api/print/print-karyawan.php?id=' + a, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }

  $scope.doPrint2 = function (a) {
    window.open($rootScope.baseURL + 'api/print/print-karyawan-per-departement.php', 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }

  $scope.printData = function () {
    //window.open($rootScope.baseURL + 'api/print/print-data-karyawan.php?jabatan=' + $scope.jabatan + '&departement=' + $scope.departement + '&status_karyawan=' + $scope.status_karyawan, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
    window.open($rootScope.baseURL + 'api/print/print-rekap-karyawan.php?status_karyawan=' + $scope.status_karyawan + '&status_akun=' + $scope.filterstatus + '&proyek=' + $scope.proyek + '&agama=' + $scope.agama, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
  }
});

tripApp.controller('KaryawanNewController', function ($scope, $route, $http, ngToast, Upload, $location) {
  $scope.processing = false;
  $scope.statusUser = "0";
  $scope.departement = "";

  $scope.backUrl = '#/data-karyawan';

  var path = $location.path();
  $scope.isHarian = false;
  if (path.indexOf('karyawan-harian') > -1) {
    $scope.isHarian = true;
    $scope.stts_karyawan = 'Harian';
    $scope.statusUser = 1;
    $scope.backUrl = '#/data-karyawan-harian';
  }

  $('.datepick').datepicker({
    format: 'dd/mm/yyyy',
    showOtherMonths: true,
    selectOtherMonths: true,
    autoclose: true
  });

  $scope.getAllData = function () {
    $http.get('api/karyawan/karyawan.php?act=DetailKaryawan').success(function (data, status) {
      $scope.data_jabatan = data.jabatan;
      $scope.history_jabatan = data.historyJabatan;
      $scope.data_departement = data.departement;
      $scope.data_proyek = data.proyek;
    });
  };

  $scope.getAllData();

  /*
  $scope.$watch(function () {
      return $scope.statusUser;
  }, function () {
      $scope.statusUser = Number($scope.statusUser);
      console.log($scope.statusUser, typeof $scope.statusUser);
  }, true);
  */
  $scope.submitForm = function (isValid) {
    if (isValid) {
      if ($scope.file || $scope.file_surat_lamaran || $scope.file_ktp || $scope.file_kartu_keluarga || $scope.file_ijasah || $scope.file_sertifikat_asuransi) {
        $scope.processing = true;
        Upload.upload({
          url: 'api/karyawan/new.php',
          data: {
            'file': $scope.file,
            'file_surat_lamaran': $scope.file_surat_lamaran,
            'file_ktp': $scope.file_ktp,
            'file_kartu_keluarga': $scope.file_kartu_keluarga,
            'file_ijasah': $scope.file_ijasah,
            'file_sertifikat_asuransi': $scope.file_sertifikat_asuransi,
            'file_bpjs_kesehatan': $scope.file_bpjs_kesehatan,
            'file_bpjs_ketenagakerjaan': $scope.file_bpjs_ketenagakerjaan,
            'file_sertifikat_keterangan_kerja': $scope.file_sertifikat_keterangan_kerja,
            'nik': $scope.nik,
            'nik2': $scope.nik2,
            'jabatan': $scope.jabatan,
            'departement': $scope.departement,
            'id_proyek': $scope.id_proyek,
            'jabatan2': $scope.jabatan2,
            'no_ktp': $scope.no_ktp,
            'thn_masuk': $scope.thn_masuk,
            'bln_masuk': $scope.bln_masuk,
            'tgl_masuk': $scope.tgl_masuk,
            'nama': $scope.nama,
            'jenis_kelamin': $scope.jenis_kelamin,
            'alamat_sementara': $scope.alamat_sementara,
            'alamat_ktp': $scope.alamat_ktp,
            'no_telp': $scope.no_telp,
            'email': $scope.email,
            'stts_karyawan': $scope.stts_karyawan,
            'agama': $scope.agama,
            'stts_lainnya': $scope.stts_lainnya,
            'usrname': $scope.usrname,
            'pass': $scope.pass,
            'absen_id': $scope.absen_id,
            'nama_ayah': $scope.nama_ayah,
            'no_telp_ayah': $scope.no_telp_ayah,
            'alamat_ayah': $scope.alamat_ayah,
            'nama_ibu': $scope.nama_ibu,
            'no_telp_ibu': $scope.no_telp_ibu,
            'alamat_ibu': $scope.alamat_ibu,
            'nama_suami': $scope.nama_suami,
            'no_telp_suami': $scope.no_telp_suami,
            'alamat_suami': $scope.alamat_suami,
            'nama_wali': $scope.nama_wali,
            'no_telp_wali': $scope.no_telp_wali,
            'alamat_wali': $scope.alamat_wali,
            'martial_stts': $scope.martial_stts,
            'status': $scope.statusUser,
            'namabank': $scope.namabank,
            'norekening': $scope.norekening,
            'tempat_lahir': $scope.tempat_lahir,
            'tanggal_lahir': $scope.tanggal_lahir,
            'pendidikan_terakhir': $scope.pendidikan_terakhir,
            'jumlah_anak': $scope.jumlah_anak,
            'tanggal_resign': $scope.tanggal_resign
          }
        }).then(function (resp) {
          var data = resp.data;
          if (data == "1") {
            ngToast.create({
              className: 'success',
              content: 'Data karyawan berhasil ditambahkan <i class="fa fa-remove"></i>'
            });
            window.document.location = $scope.backUrl;
          } else if (data == "2") {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Username yang anda masukan sudah digunakan oleh karyawan lain. Silahkan gunakan alternatif username lainnya! <i class="fa fa-remove"></i>'
            });
          } else {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Data karyawan gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
            });
          }
        });
      } else {
        $scope.processing = true;
        $http({
          method: "POST",
          url: 'api/karyawan/new.php',
          data: $.param({
            'nik': $scope.nik,
            'nik2': $scope.nik2,
            'jabatan': $scope.jabatan,
            'id_proyek': $scope.id_proyek,
            'departement': $scope.departement,
            'jabatan2': $scope.jabatan2,
            'no_ktp': $scope.no_ktp,
            'thn_masuk': $scope.thn_masuk,
            'bln_masuk': $scope.bln_masuk,
            'tgl_masuk': $scope.tgl_masuk,
            'nama': $scope.nama,
            'jenis_kelamin': $scope.jenis_kelamin,
            'alamat_sementara': $scope.alamat_sementara,
            'alamat_ktp': $scope.alamat_ktp,
            'no_telp': $scope.no_telp,
            'email': $scope.email,
            'stts_karyawan': $scope.stts_karyawan,
            'agama': $scope.agama,
            'stts_lainnya': $scope.stts_lainnya,
            'usrname': $scope.usrname,
            'pass': $scope.pass,
            'absen_id': $scope.absen_id,
            'nama_ayah': $scope.nama_ayah,
            'no_telp_ayah': $scope.no_telp_ayah,
            'alamat_ayah': $scope.alamat_ayah,
            'nama_ibu': $scope.nama_ibu,
            'no_telp_ibu': $scope.no_telp_ibu,
            'alamat_ibu': $scope.alamat_ibu,
            'nama_suami': $scope.nama_suami,
            'no_telp_suami': $scope.no_telp_suami,
            'alamat_suami': $scope.alamat_suami,
            'nama_wali': $scope.nama_wali,
            'no_telp_wali': $scope.no_telp_wali,
            'alamat_wali': $scope.alamat_wali,
            'martial_stts': $scope.martial_stts,
            'status': $scope.statusUser,
            'namabank': $scope.namabank,
            'norekening': $scope.norekening,
            'tempat_lahir': $scope.tempat_lahir,
            'tanggal_lahir': $scope.tanggal_lahir,
            'pendidikan_terakhir': $scope.pendidikan_terakhir,
            'jumlah_anak': $scope.jumlah_anak,
            'tanggal_resign': $scope.tanggal_resign
          }),
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).success(function (data, status) {
          if (data == "1") {
            ngToast.create({
              className: 'success',
              content: 'Data karyawan berhasil ditambahkan <i class="fa fa-remove"></i>'
            });
            window.document.location = $scope.backUrl;
          } else if (data == "2") {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Username yang anda masukan sudah digunakan oleh karyawan lain. Silahkan gunakan alternatif username lainnya! <i class="fa fa-remove"></i>'
            });
          } else {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Data karyawan gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
            });
          }
        });
      }
    }
  };
});

tripApp.controller('KaryawanEditController', function ($scope, $route, $rootScope, $routeParams, $http, ngToast, Upload, $location) {
  $scope.processing = false;
  $scope.jabatan = "";
  $scope.departement = "";

  $scope.backUrl = '#/data-karyawan';

  var path = $location.path();
  $scope.isHarian = false;
  if (path.indexOf('karyawan-harian') > -1) {
    $scope.isHarian = true;
    $scope.stts_karyawan = 'Harian';
    $scope.backUrl = '#/data-karyawan-harian';
  }

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
    $http.get('api/karyawan/karyawan.php?act=DetailKaryawan&id=' + $routeParams.karyawanId).success(function (data, status) {
      $scope.data_jabatan = data.jabatan;
      $scope.history_jabatan = data.historyJabatan;
      $scope.data_departement = data.departement;
      $scope.history_training = data.historyTraining;
      $scope.data_proyek = data.proyek;

      $scope.nik = data.detailKaryawan.nik;
      $scope.nik2 = data.detailKaryawan.nik2;
      $scope.nama_jabatan = data.detailKaryawan.nama_jabatan;
      $scope.nama_jabatan2 = data.detailKaryawan.nama_jabatan2;
      $scope.jabatan = data.detailKaryawan.jabatan;
      $scope.departement = data.detailKaryawan.departement;
      $scope.departement2 = data.detailKaryawan.departement2;
      $scope.jabatan2 = data.detailKaryawan.jabatan2;
      $scope.no_ktp = data.detailKaryawan.no_ktp;
      $scope.thn_masuk = data.detailKaryawan.thn_masuk;
      $scope.bln_masuk = data.detailKaryawan.bln_masuk;
      $scope.tgl_masuk = data.detailKaryawan.tgl_masuk;
      $scope.nama = data.detailKaryawan.nama;
      $scope.jenis_kelamin = data.detailKaryawan.jenis_kelamin;
      $scope.alamat_sementara = data.detailKaryawan.alamat_sementara;
      $scope.alamat_ktp = data.detailKaryawan.alamat_ktp;
      $scope.no_telp = data.detailKaryawan.no_telp;
      $scope.email = data.detailKaryawan.email;
      $scope.stts_karyawan = data.detailKaryawan.stts_karyawan;
      $scope.agama = data.detailKaryawan.agama;
      $scope.stts_lainnya = data.detailKaryawan.stts_lainnya;
      $scope.nama_ayah = data.detailKaryawan.nama_ayah;
      $scope.no_telp_ayah = data.detailKaryawan.no_telp_ayah;
      $scope.alamat_ayah = data.detailKaryawan.alamat_ayah;
      $scope.nama_ibu = data.detailKaryawan.nama_ibu;
      $scope.no_telp_ibu = data.detailKaryawan.no_telp_ibu;
      $scope.alamat_ibu = data.detailKaryawan.alamat_ibu;
      $scope.nama_suami = data.detailKaryawan.nama_suami;
      $scope.no_telp_suami = data.detailKaryawan.no_telp_suami;
      $scope.alamat_suami = data.detailKaryawan.alamat_suami;
      $scope.nama_wali = data.detailKaryawan.nama_wali;
      $scope.no_telp_wali = data.detailKaryawan.no_telp_wali;
      $scope.alamat_wali = data.detailKaryawan.alamat_wali;
      $scope.usrname = data.detailKaryawan.usrname;
      $scope.martial_stts = data.detailKaryawan.martial_stts;
      $scope.statusUser = parseInt(data.detailKaryawan.statusUser);
      $scope.nama_statusUser = data.detailKaryawan.namaStatusUser;
      $scope.foto = data.detailKaryawan.foto;
      $scope.namabank = data.detailKaryawan.namabank;
      $scope.norekening = data.detailKaryawan.norekening;
      $scope.id_karyawan = $routeParams.karyawanId;
      $scope.tempat_lahir = data.detailKaryawan.tempat_lahir;
      $scope.tanggal_lahir = data.detailKaryawan.tanggal_lahir;
      if ($scope.tanggal_lahir === "00/00/0000") $scope.tanggal_lahir = "";
      $scope.pendidikan_terakhir = data.detailKaryawan.pendidikan_terakhir;
      $scope.jumlah_anak = data.detailKaryawan.jumlah_anak;
      $scope.bln_masuk = data.detailKaryawan.bln_masuk;
      $scope.absen_id = data.detailKaryawan.absen_id;
      $scope.id_proyek = data.detailKaryawan.id_proyek;
      $scope.file_ijasah = data.detailKaryawan.file_ijasah;
      $scope.file_kartu_keluarga = data.detailKaryawan.file_kartu_keluarga;
      $scope.file_ktp = data.detailKaryawan.file_ktp;
      $scope.file_sertifikat_asuransi = data.detailKaryawan.file_sertifikat_asuransi;
      $scope.file_surat_lamaran = data.detailKaryawan.file_surat_lamaran;
      $scope.file_bpjs_kesehatan = data.detailKaryawan.file_bpjs_kesehatan;
      $scope.file_bpjs_ketenagakerjaan = data.detailKaryawan.file_bpjs_ketenagakerjaan;
      $scope.file_sertifikat_keterangan_kerja = data.detailKaryawan.file_sertifikat_keterangan_kerja;
      $scope.tanggal_resign = data.detailKaryawan.tanggal_resign;

      setTimeout(() => {
        $("#id_proyek").select2("val", $scope.id_proyek);
      }, 1000);
    });
  };

  $scope.getAllData();

  $scope.changeJabatan = function () {
    angular.forEach($scope.data_jabatan, function (value, key) {
      if (value.IDJabatan === $scope.jabatan) {
        $scope.jabatanValue = value.Jabatan;
      }
    });
  }

  $scope.removePhoto = function (a) {
    $http.get('api/karyawan/remove-photo.php?id_karyawan=' + a).success(function (data, status) {
      if (data == "1") {
        $scope.foto = "";
      } else {
        ngToast.create({
          className: 'danger',
          content: 'Foto gagal dihapus. Silahkan coba kembali nanti. <i class="fa fa-remove"></i>'
        });
      }
    });
  };

  $scope.chageUsername = function () {
    $('#usrname').addClass('loaded');
    $http.get('api/karyawan/find-username.php?usr=' + $scope.usrname + '&id=' + $routeParams.karyawanId).success(function (data, status) {
      if (data != "") {
        $scope.userValid = "2";
      } else {
        $scope.userValid = "1";
      }
    });
  };

  $scope.submitForm = function (isValid) {
    if (isValid) {
      if ($scope.file || $scope.file_surat_lamaran || $scope.file_ktp || $scope.file_kartu_keluarga || $scope.file_ijasah || $scope.file_sertifikat_asuransi) {
        $scope.processing = true;
        Upload.upload({
          url: 'api/karyawan/edit.php',
          data: {
            'file': $scope.file,
            'file_surat_lamaran': $scope.file_surat_lamaran,
            'file_ktp': $scope.file_ktp,
            'file_kartu_keluarga': $scope.file_kartu_keluarga,
            'file_ijasah': $scope.file_ijasah,
            'file_sertifikat_asuransi': $scope.file_sertifikat_asuransi,
            'file_bpjs_kesehatan': $scope.file_bpjs_kesehatan,
            'file_bpjs_ketenagakerjaan': $scope.file_bpjs_ketenagakerjaan,
            'file_sertifikat_keterangan_kerja': $scope.file_sertifikat_keterangan_kerja,
            'nik': $scope.nik,
            'nik2': $scope.nik2,
            'jabatan': $scope.jabatan,
            'id_proyek': $scope.id_proyek,
            'departement': $scope.departement,
            'jabatan2': $scope.jabatan2,
            'no_ktp': $scope.no_ktp,
            'thn_masuk': $scope.thn_masuk,
            'bln_masuk': $scope.bln_masuk,
            'tgl_masuk': $scope.tgl_masuk,
            'nama': $scope.nama,
            'jenis_kelamin': $scope.jenis_kelamin,
            'alamat_sementara': $scope.alamat_sementara,
            'alamat_ktp': $scope.alamat_ktp,
            'no_telp': $scope.no_telp,
            'email': $scope.email,
            'stts_karyawan': $scope.stts_karyawan,
            'agama': $scope.agama,
            'stts_lainnya': $scope.stts_lainnya,
            'usrname': $scope.usrname,
            'pass': $scope.pass,
            'absen_id': $scope.absen_id,
            'nama_ayah': $scope.nama_ayah,
            'no_telp_ayah': $scope.no_telp_ayah,
            'alamat_ayah': $scope.alamat_ayah,
            'nama_ibu': $scope.nama_ibu,
            'no_telp_ibu': $scope.no_telp_ibu,
            'alamat_ibu': $scope.alamat_ibu,
            'nama_suami': $scope.nama_suami,
            'no_telp_suami': $scope.no_telp_suami,
            'alamat_suami': $scope.alamat_suami,
            'nama_wali': $scope.nama_wali,
            'no_telp_wali': $scope.no_telp_wali,
            'alamat_wali': $scope.alamat_wali,
            'martial_stts': $scope.martial_stts,
            'status': $scope.statusUser,
            'foto': $scope.foto,
            'id': $routeParams.karyawanId,
            'namabank': $scope.namabank,
            'norekening': $scope.norekening,
            'tempat_lahir': $scope.tempat_lahir,
            'tanggal_lahir': $scope.tanggal_lahir,
            'pendidikan_terakhir': $scope.pendidikan_terakhir,
            'jumlah_anak': $scope.jumlah_anak,
            'tanggal_resign': $scope.tanggal_resign
          }
        }).then(function (resp) {
          var data = resp.data;
          if (data == "1") {
            ngToast.create({
              className: 'success',
              content: 'Data karyawan berhasil diperbaharui <i class="fa fa-remove"></i>'
            });
          } else if (data == "2") {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Username yang anda masukan sudah digunakan oleh karyawan lain. Silahkan gunakan alternatif username lainnya! <i class="fa fa-remove"></i>'
            });
          } else {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Data karyawan gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
            });
          }
        });
      } else {
        $scope.processing = true;
        $http({
          method: "POST",
          url: 'api/karyawan/edit.php',
          data: $.param({
            'nik': $scope.nik,
            'nik2': $scope.nik2,
            'jabatan': $scope.jabatan,
            'id_proyek': $scope.id_proyek,
            'departement': $scope.departement,
            'jabatan2': $scope.jabatan2,
            'no_ktp': $scope.no_ktp,
            'thn_masuk': $scope.thn_masuk,
            'bln_masuk': $scope.bln_masuk,
            'tgl_masuk': $scope.tgl_masuk,
            'nama': $scope.nama,
            'jenis_kelamin': $scope.jenis_kelamin,
            'alamat_sementara': $scope.alamat_sementara,
            'alamat_ktp': $scope.alamat_ktp,
            'no_telp': $scope.no_telp,
            'email': $scope.email,
            'stts_karyawan': $scope.stts_karyawan,
            'agama': $scope.agama,
            'stts_lainnya': $scope.stts_lainnya,
            'usrname': $scope.usrname,
            'pass': $scope.pass,
            'absen_id': $scope.absen_id,
            'nama_ayah': $scope.nama_ayah,
            'no_telp_ayah': $scope.no_telp_ayah,
            'alamat_ayah': $scope.alamat_ayah,
            'nama_ibu': $scope.nama_ibu,
            'no_telp_ibu': $scope.no_telp_ibu,
            'alamat_ibu': $scope.alamat_ibu,
            'nama_suami': $scope.nama_suami,
            'no_telp_suami': $scope.no_telp_suami,
            'alamat_suami': $scope.alamat_suami,
            'nama_wali': $scope.nama_wali,
            'no_telp_wali': $scope.no_telp_wali,
            'alamat_wali': $scope.alamat_wali,
            'martial_stts': $scope.martial_stts,
            'status': $scope.statusUser,
            'foto': $scope.foto,
            'id': $routeParams.karyawanId,
            'namabank': $scope.namabank,
            'norekening': $scope.norekening,
            'tempat_lahir': $scope.tempat_lahir,
            'tanggal_lahir': $scope.tanggal_lahir,
            'pendidikan_terakhir': $scope.pendidikan_terakhir,
            'jumlah_anak': $scope.jumlah_anak,
            'tanggal_resign': $scope.tanggal_resign
          }),
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).success(function (data, status) {
          if (data == "1") {
            ngToast.create({
              className: 'success',
              content: 'Data karyawan berhasil diperbaharui <i class="fa fa-remove"></i>'
            });
          } else if (data == "2") {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Username yang anda masukan sudah digunakan oleh karyawan lain. Silahkan gunakan alternatif username lainnya! <i class="fa fa-remove"></i>'
            });
          } else {
            $scope.processing = false;
            ngToast.create({
              className: 'danger',
              content: 'Data karyawan gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
            });
          }
        });
      }
    }
  };
});
