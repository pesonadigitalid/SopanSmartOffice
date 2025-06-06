tripApp.controller('SuratKeluarController', function ($scope, $route, $http, ngToast, CommonServices) {
    $scope.datestart = CommonServices.firstDateMonth();
    $scope.dateend = CommonServices.lastDateMonth();
    $scope.keyword = "";
    $scope.kode_proyek = "";

    $('.datepick').datepicker({
        format: 'dd/mm/yyyy',
        showOtherMonths: true,
        selectOtherMonths: true,
        autoclose: true
    });

    $scope.getdata = function () {
        $http.get('api/surat-keluar/data-surat-keluar.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&keyword=' + $scope.keyword + '&kode_proyek=' + $scope.kode_proyek).success(function (data, status) {
            $scope.data_suratkeluar = data.data;
            $scope.data_proyek = data.proyek;
        });
    };

    $scope.getdata();

    $scope.filtersupplier = function () {
        $('#basicTable').dataTable().fnDestroy();
        $scope.getdata();
    }

    $scope.removeRow = function (val) {
        if (confirm("Anda yakin ingin menghapus data ini?")) {
            $http({
                method: "POST",
                url: 'api/surat-keluar/delete.php',
                data: $.param({
                    'idr': val
                }),
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function (data, status) {
                if (data == "1") {
                    ngToast.create({
                        className: 'success',
                        content: 'Data Surat Keluar berhasil dihapus <i class="fa fa-remove"></i>'
                    });
                    $route.reload();
                } else {
                    ngToast.create({
                        className: 'success',
                        content: 'Data Surat Keluar gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
                    });
                }
            });
        }
    }
});

tripApp.controller('SuratKeluarNewController', function ($scope, $route, $http, ngToast, Upload, $rootScope) {
    $scope.processing = false;

    var d = new Date();
    $scope.year_selected = d.getFullYear().toString();
    $scope.status_proyek = '2';
    $scope.nama_proyek = "";
    $scope.tanggal = $rootScope.currentDateID;
    $scope.id_proyek = "";

    $('.datepick').datepicker({
        format: 'dd/mm/yyyy',
        showOtherMonths: true,
        selectOtherMonths: true,
        autoclose: true
    });

    $scope.getdataProyek = function () {
        $http.get('api/proyek/data-proyek.php?status=' + $scope.status_proyek + '&nama_proyek=' + $scope.nama_proyek).success(function (data, status) {
            $scope.data_proyek = data;
        });
    };

    $scope.getdataProyek();

    $scope.getdataDepartment = function () {
        $http.get('api/departement/data-departement.php').success(function (data, status) {
            $scope.data_department = data;
        });
    };

    $scope.getdataDepartment();

    $scope.$watch('id_proyek', function () {
        if (parseInt($scope.id_proyek) > 0) {
            $scope.id_department = $scope.data_proyek.filter((x) => x.IDProyek === $scope.id_proyek)[0].IDDepartement;
        }
    });

    $scope.submitForm = function (isValid) {
        if (isValid) {
            $scope.processing = true;
            Upload.upload({
                url: 'api/surat-keluar/new.php',
                data: {
                    // 'nosurat': $scope.nosurat,
                    'file_surat': $scope.file_surat_keluar,
                    'id_proyek': $scope.id_proyek,
                    'id_department': $scope.id_department,
                    'jenis': $scope.jenis,
                    'prihal': $scope.prihal,
                    'tanggal': $scope.tanggal,
                    'deskripsi': $scope.deskripsi
                }
            }).then(function (resp) {
                if (resp.data == "1") {
                    ngToast.create({
                        className: 'success',
                        content: 'Surat Keluar berhasil ditambahkan <i class="fa fa-remove"></i>'
                    });
                    window.document.location = '#/surat-keluar';
                } else {
                    $scope.processing = false;
                    ngToast.create({
                        className: 'danger',
                        content: 'Surat Keluar gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
                    });
                }
            });
        }
    };
});

tripApp.controller('SuratKeluarEditController', function ($scope, $route, $routeParams, $http, ngToast, Upload) {
    $scope.processing = false;
    $scope.namapelanggan = "0";

    var d = new Date();
    $scope.year_selected = d.getFullYear().toString();
    $scope.status_proyek = '2';
    $scope.nama_proyek = "";
    $scope.id_proyek = "";

    $('.datepick').datepicker({
        format: 'dd/mm/yyyy',
        showOtherMonths: true,
        selectOtherMonths: true,
        autoclose: true
    });

    $http.get('api/surat-keluar/detail.php?id=' + $routeParams.suratkeluarId).success(function (data, status) {
        $scope.nosurat = data.nosurat;
        $scope.id_proyek = data.id_proyek;
        $scope.id_department = data.id_department;
        $scope.jenis = data.jenis;
        $scope.prihal = data.prihal;
        $scope.tanggal = data.tanggal;
        $scope.deskripsi = data.deskripsi;
        $scope.file_surat_keluar_name = data.file_surat_keluar;

        setTimeout(() => {
            $('#id_proyek').val($scope.id_proyek).trigger('change');
        }, 1000);
    });

    $scope.getdataProyek = function () {
        $http.get('api/proyek/data-proyek.php?status=' + $scope.status_proyek + '&nama_proyek=' + $scope.nama_proyek).success(function (data, status) {
            $scope.data_proyek = data;

            setTimeout(() => {
                $('#id_proyek').val($scope.id_proyek).trigger('change');
            }, 1000);
        });
    };

    $scope.getdataProyek();

    $scope.getdataDepartment = function () {
        $http.get('api/departement/data-departement.php').success(function (data, status) {
            $scope.data_department = data;
        });
    };

    $scope.getdataDepartment();

    $scope.$watch('id_proyek', function () {
        if (parseInt($scope.id_proyek) > 0) {
            $scope.id_department = $scope.data_proyek.filter((x) => x.IDProyek === $scope.id_proyek)[0].IDDepartement;
        }
    });

    $scope.submitForm = function (isValid) {
        if (isValid) {
            $scope.processing = true;
            Upload.upload({
                url: 'api/surat-keluar/edit.php',
                data: {
                    // 'nosurat': $scope.nosurat,
                    'file_surat': $scope.file_surat_keluar,
                    'id_proyek': $scope.id_proyek,
                    'id_department': $scope.id_department,
                    'jenis': $scope.jenis,
                    'prihal': $scope.prihal,
                    'tanggal': $scope.tanggal,
                    'deskripsi': $scope.deskripsi,
                    'id': $routeParams.suratkeluarId
                }
            }).then(function (resp) {
                if (resp.data == "1") {
                    ngToast.create({
                        className: 'success',
                        content: 'Surat Keluar berhasil diperbaharui <i class="fa fa-remove"></i>'
                    });
                    window.document.location = '#/surat-keluar';
                } else {
                    $scope.processing = false;
                    ngToast.create({
                        className: 'danger',
                        content: 'Surat Keluar gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
                    });
                }
            });
        }
    };
});