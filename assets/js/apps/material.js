tripApp.controller('MaterialController', function ($scope, $route, $http, ngToast) {

    $scope.getdata = function () {
        $http.get('api/material/data-material.php').success(function (data, status) {
            $scope.data_material = data;
        });
    };

    $scope.getdata();

    $scope.removeRow = function (val) {
        if (confirm("Anda yakin ingin menghapus data ini?")) {
            $http({
                method: "POST",
                url: 'api/material/delete.php',
                data: $.param({
                    'idr': val
                }),
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function (data, status) {
                if (data == "1") {
                    ngToast.create({
                        className: 'success',
                        content: 'Data jenis barang berhasil dihapus <i class="fa fa-remove"></i>'
                    });
                    $route.reload();
                } else {
                    ngToast.create({
                        className: 'danger',
                        content: 'Data jenis barang gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
                    });
                }
            });
        }
    }
});

tripApp.controller('MaterialNewController', function ($scope, $route, $http, ngToast) {
    $scope.processing = false;
    $scope.parent = "0";
    $scope.isparent = "0";

    $scope.$watch(function () {
        return $scope.isparent;
    }, function () {
        $scope.isparent = Number($scope.isparent);
        console.log($scope.isparent, typeof $scope.isparent);
    }, true);

    $scope.getdata = function () {
        $http.get('api/material/data-material.php?param=getmaterial').success(function (data, status) {
            $scope.data_material = data;
        });
    };

    $scope.getdata();

    $scope.submitForm = function (isValid) {
        if (isValid) {
            $scope.processing = true;
            $http({
                method: "POST",
                url: 'api/material/new.php',
                data: $.param({
                    'parent': $scope.parent,
                    'nama': $scope.nama,
                    'isparent': $scope.isparent
                }),
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function (data, status) {
                if (data == "1") {
                    ngToast.create({
                        className: 'success',
                        content: 'Data jenis barang berhasil ditambahkan <i class="fa fa-remove"></i>'
                    });
                    window.document.location = '#/data-jenis-barang';
                } else if (data == "2") {
                    $scope.processing = false;
                    ngToast.create({
                        className: 'danger',
                        content: 'Data jenis barang sudah ada! Anda tidak dapat menginput data yang sama. <i class="fa fa-remove"></i>'
                    });
                } else {
                    $scope.processing = false;
                    ngToast.create({
                        className: 'danger',
                        content: 'Data jenis barang gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
                    });
                }
            });
        }
    };
});

tripApp.controller('MaterialEditController', function ($scope, $route, $routeParams, $http, ngToast) {
    $scope.processing = false;
    $http.get('api/material/detail.php?id=' + $routeParams.materialId).success(function (data, status) {
        $scope.parent = data.parent;
        $scope.nama = data.nama;
        $scope.isparent = data.isparent;
    });

    $scope.getdata = function () {
        $http.get('api/material/data-material.php?param=getmaterial').success(function (data, status) {
            $scope.data_material = data;
        });
    };

    $scope.getdata();

    $scope.$watch(function () {
        return $scope.isparent;
    }, function () {
        $scope.isparent = Number($scope.isparent);
        console.log($scope.isparent, typeof $scope.isparent);
    }, true);

    $scope.submitForm = function (isValid) {
        if (isValid) {
            $scope.processing = true;
            $http({
                method: "POST",
                url: 'api/material/edit.php',
                data: $.param({
                    'parent': $scope.parent,
                    'nama': $scope.nama,
                    'isparent': $scope.isparent,
                    'id': $routeParams.materialId
                }),
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function (data, status) {
                if (data == "1") {
                    $scope.processing = false;
                    ngToast.create({
                        className: 'success',
                        content: 'Data jenis barang berhasil diperbaharui <i class="fa fa-remove"></i>'
                    });
                } else {
                    $scope.processing = false;
                    ngToast.create({
                        className: 'danger',
                        content: 'Data jenis barang gagal diperbaharui. Silahkan coba kembali <i class="fa fa-remove"></i>'
                    });
                }
            });
        }
    };
});