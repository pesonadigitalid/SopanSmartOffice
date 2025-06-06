tripApp.controller('GajiKaryawanController', function($rootScope, $scope, $route, $http, ngToast) {
    $scope.kategori = "0";

    $scope.getdata = function() {
        $http.get('api/karyawan/data-karyawan.php?status=1&status_harian=0').success(function(data, status) {
            $scope.data_karyawan = data;
        });
    };
    $scope.getdata();
    $scope.url = "";

    $scope.showModal = function(a) {
        $('#myModal').modal('show');
        $('#myModal').children('.modal-dialog').removeClass('modal-lg');
        $('#myModal').on('shown.bs.modal', function() {
            $('#password').focus();
            $scope.url = a;
        });
    }

    $scope.grantAccess = function() {
        $http.get('api/karyawan/karyawan.php?act=GrantAccessGaji&pass=' + $scope.password).success(function(data, status) {
            if (data == "1") {
                $('#myModal').modal('hide');
                setTimeout(function(){
                    window.document.location = '#/gaji-karyawan/' + $scope.url;
                },500);
            } else {
                ngToast.create({
                    className: 'danger',
                    content: 'Password salah. Akses ke data gaji ditolak! <i class="fa fa-remove"></i>'
                });
                $('#myModal').modal('hide');
            }
        });

    }
});

tripApp.controller('GajiKaryawanNewController', function($routeParams, $scope, $route, $http, ngToast) {
    $scope.processing = false;
    $scope.disablecode = false;
    $scope.statusUser = "0";

    $scope.idkaryawan = $routeParams.karyawanId;

    $scope.getdataKaryawan = function() {
        $http.get('api/karyawan/detail.php?id=' + $scope.idkaryawan).success(function(data, status) {
            $scope.nik = data.nik;
            $scope.jabatan = data.nama_jabatan;
            $scope.nama = data.nama;
            $scope.stts_karyawan = data.stts_karyawan;
            $scope.stts_lainnya = data.stts_lainnya;
            if (data.statusUser == "1") $scope.status_user = "Aktif";
            else $scope.status_user = "Tidak Aktif";
        });
    };
    $scope.getdataKaryawan();

    $scope.getdata = function() {
        $http.get('api/gaji-karyawan/data-gaji-karyawan.php?id_karyawan=' + $routeParams.karyawanId).success(function(data, status) {
            $scope.data_gaji = data;
        });
    };
    $scope.getdata();

    $scope.EditHistoryGaji = function(a) {
        //alert(a);
        $http.get('api/gaji-karyawan/detail.php?id=' + a).success(function(data, status) {
            $scope.idkaryawan = data.idkaryawan;
            $scope.efektif_bln = data.efektif_bln;
            $scope.efektif_thn = data.efektif_thn;
            $scope.gaji_pokok = data.gaji_pokok;
            $scope.uang_makan = data.uang_makan;
            $scope.uang_pulsa = data.uang_pulsa;
            $scope.uang_transport = data.uang_transport;
            $scope.uang_performance = data.uang_performance;
            $scope.uang_lain2 = data.uang_lain2;
            $scope.idgaji = data.idgaji;
        });
    }

    $scope.removeHistoryGaji = function(val) {
        if (confirm("Anda yakin ingin menghapus data ini?")) {
            $http({
                method: "POST",
                url: 'api/gaji-karyawan/delete.php',
                data: $.param({
                    'idr': val
                }),
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            }).success(function(data, status) {
                if (data == "1") {
                    ngToast.create({
                        className: 'success',
                        content: 'History gaji berhasil dihapus <i class="fa fa-remove"></i>'
                    });
                    $route.reload();
                } else {
                    ngToast.create({
                        className: 'danger',
                        content: 'History gaji tidak dapat dihapus. Silahkan coba lagi nanti... <i class="fa fa-remove"></i>'
                    });
                }
            });
        }
    }

    $scope.submitForm = function(isValid) {
        if (isValid) {
            $scope.processing = true;
            $http({
                method: "POST",
                url: 'api/gaji-karyawan/new.php',
                data: $.param({
                    'id_karyawan': $scope.idkaryawan,
                    'efektif_bln': $scope.efektif_bln,
                    'efektif_thn': $scope.efektif_thn,
                    'gaji_pokok': $scope.gaji_pokok,
                    'uang_makan': $scope.uang_makan,
                    'uang_pulsa': $scope.uang_pulsa,
                    'uang_transport': $scope.uang_transport,
                    'uang_performance': $scope.uang_performance,
                    'uang_lain2': $scope.uang_lain2,
                    'id': $scope.idgaji
                }),
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function(data, status) {
                if (data == "1") {
                    ngToast.create({
                        className: 'success',
                        content: 'Data gaji berhasil disimpan <i class="fa fa-remove"></i>'
                    });
                    $route.reload();
                } else {
                    $scope.processing = false;
                    ngToast.create({
                        className: 'danger',
                        content: 'Data gaji gagal disimpan <i class="fa fa-remove"></i>'
                    });
                }
            });
        }
    };
});