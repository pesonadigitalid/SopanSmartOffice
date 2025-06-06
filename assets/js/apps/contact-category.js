tripApp.controller('ContactCategoryController', function ($scope, $route, $http, ngToast) {

    $scope.getdata = function () {
        $http.get('api/contactcategory/data-contact-category.php').success(function (data, status) {
            $scope.data_contactcategory = data;
        });
    };

    $scope.getdata();

    $scope.removeRow = function (val) {
        if (confirm("Anda yakin ingin menghapus data ini?")) {
            $http({
                method: "POST",
                url: 'api/contactcategory/delete.php',
                data: $.param({
                    'idr': val
                }),
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function (data, status) {
                if (data == "1") {
                    ngToast.create({
                        className: 'success',
                        content: 'Data asset category berhasil dihapus <i class="fa fa-remove"></i>'
                    });
                    $route.reload();
                } else {
                    ngToast.create({
                        className: 'success',
                        content: 'Data asset category gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
                    });
                }
            });
        }
    }
});

tripApp.controller('ManageContactController', function ($scope, $route, $http, ngToast, $routeParams) {
    $scope.kategori = "0";

    $scope.getdata = function () {
        $http.get('api/contactcategory/data-contact.php?kategori=' + $scope.kategori + '&id=' + $routeParams.contactcategoryId).success(function (data, status) {
            $scope.datacontact = data;
        });
    };

    $scope.getdata();

    $scope.filtersupplier = function () {
        $('#basicTable').dataTable().fnDestroy();
        $scope.getdata();
    }

    $scope.hello = function (a, b) {
        $http({
            method: "POST",
            url: 'api/contactcategory/manage.php',
            data: $.param({
                'id': a,
                'jenis': b,
                'idC': $routeParams.contactcategoryId
            }),
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).success(function (data, status) {
            if (data == "1") {
                ngToast.create({
                    className: 'success',
                    content: 'Kontak berhasil ditambahakan ke Category <i class="fa fa-remove"></i>'
                });
            } else if (data == "0") {
                $scope.processing = false;
                ngToast.create({
                    className: 'danger',
                    content: 'Kontak gagal ditambahkan ke Category. Silahkan coba kembali. <i class="fa fa-remove"></i>'
                });
            } else if (data == "2") {
                ngToast.create({
                    className: 'success',
                    content: 'Kontak berhasil dihapus dari Category <i class="fa fa-remove"></i>'
                });
            } else {
                $scope.processing = false;
                ngToast.create({
                    className: 'danger',
                    content: 'Kontak gagal dihapus dari Category. Silahkan coba kembali. <i class="fa fa-remove"></i>'
                });
            }
        });
    }
});



tripApp.controller('ContactCategoryNewController', function ($scope, $route, $http, ngToast) {
    $scope.processing = false;

    $scope.submitForm = function (isValid) {
        if (isValid) {
            $scope.processing = true;
            $http({
                method: "POST",
                url: 'api/contactcategory/new.php',
                data: $.param({
                    'nama': $scope.nama
                }),
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function (data, status) {
                if (data == "1") {
                    ngToast.create({
                        className: 'success',
                        content: 'Data contact category berhasil ditambahkan <i class="fa fa-remove"></i>'
                    });
                    window.document.location = '#/data-contact-category';
                } else {
                    $scope.processing = false;
                    ngToast.create({
                        className: 'danger',
                        content: 'Data contact category gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
                    });
                }
            });
        }
    };
});

tripApp.controller('ContactCategoryEditController', function ($scope, $route, $routeParams, $http, ngToast) {
    $scope.processing = false;
    $http.get('api/contactcategory/detail.php?id=' + $routeParams.contactcategoryId).success(function (data, status) {
        $scope.nama = data.Nama;
    });

    $scope.submitForm = function (isValid) {
        if (isValid) {
            $scope.processing = true;
            $http({
                method: "POST",
                url: 'api/contactcategory/edit.php',
                data: $.param({
                    'nama': $scope.nama,
                    'id': $routeParams.contactcategoryId
                }),
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function (data, status) {
                if (data == "1") {
                    ngToast.create({
                        className: 'success',
                        content: 'Data asset category berhasil diperbaharui <i class="fa fa-remove"></i>'
                    });
                    window.document.location = '#/data-contact-category';
                } else {
                    $scope.processing = false;
                    ngToast.create({
                        className: 'danger',
                        content: 'Data asset category gagal diperbaharui. Silahkan coba kembali <i class="fa fa-remove"></i>'
                    });
                }
            });
        }
    };
});