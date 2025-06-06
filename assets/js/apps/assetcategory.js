tripApp.controller('AssetCategoryController', function ($scope, $route, $http, ngToast) {

    $scope.getdata = function () {
        $http.get('api/assetcategory/data-asset-category.php').success(function (data, status) {
            $scope.data_assetcategory = data;
        });
    };

    $scope.getdata();

    $scope.removeRow = function (val) {
        if (confirm("Anda yakin ingin menghapus data ini?")) {
            $http({
                method: "POST",
                url: 'api/assetcategory/delete.php',
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

tripApp.controller('AssetCategoryNewController', function ($scope, $route, $http, ngToast) {
    $scope.processing = false;
    $scope.jenis = 'Asset'

    $scope.submitForm = function (isValid) {
        if (isValid) {
            $scope.processing = true;
            $http({
                method: "POST",
                url: 'api/assetcategory/new.php',
                data: $.param({
                    'nama': $scope.nama,
                    'jenis': $scope.jenis
                }),
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function (data, status) {
                if (data == "1") {
                    ngToast.create({
                        className: 'success',
                        content: 'Data asset category berhasil ditambahkan <i class="fa fa-remove"></i>'
                    });
                    window.document.location = '#/data-asset-category';
                } else {
                    $scope.processing = false;
                    ngToast.create({
                        className: 'danger',
                        content: 'Data asset category gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
                    });
                }
            });
        }
    };
});

tripApp.controller('AssetCategoryEditController', function ($scope, $route, $routeParams, $http, ngToast) {
    $scope.processing = false;
    $http.get('api/assetcategory/detail.php?id=' + $routeParams.assetcategoryId).success(function (data, status) {
        //alert(data);
        $scope.nama = data.nama;
        $scope.jenis = data.jenis;
    });

    $scope.submitForm = function (isValid) {
        if (isValid) {
            $scope.processing = true;
            $http({
                method: "POST",
                url: 'api/assetcategory/edit.php',
                data: $.param({
                    'nama': $scope.nama,
                    'jenis': $scope.jenis,
                    'id': $routeParams.assetcategoryId
                }),
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function (data, status) {
                if (data == "1") {
                    ngToast.create({
                        className: 'success',
                        content: 'Data asset category berhasil diperbaharui <i class="fa fa-remove"></i>'
                    });
                    window.document.location = '#/data-asset-category';
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