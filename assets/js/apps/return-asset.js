tripApp.controller('ReturnAssetController', function ($rootScope, $scope, $route, $http, ngToast, CommonServices) {
    $scope.year_selected = CommonServices.currentYear().toString();
    $scope.karyawan = "";

    $scope.tahunList = CommonServices.yearList();

    $scope.getdata = function () {
        $http.get('api/return-asset/data-return.php?act=DataReturn&tahun=' + $scope.year_selected + '&karyawan=' + $scope.karyawan).success(function (data, status) {
            $scope.data_return = data.DataReturn;
            $scope.data_karyawan = data.DataKaryawan;
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
                url: 'api/return-asset/delete.php',
                data: $.param({
                    'idr': val
                }),
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function (data, status) {
                if (data == "1") {
                    ngToast.create({
                        className: 'success',
                        content: 'Data return asset berhasil dihapus <i class="fa fa-remove"></i>'
                    });
                    $route.reload();
                } else if (data === "3") {
                    ngToast.create({
                        className: 'danger',
                        content: 'Data return asset tidak dapat dihapus karena asset telah di-assign... <i class="fa fa-remove"></i>'
                    });
                } else {
                    ngToast.create({
                        className: 'danger',
                        content: 'Data return asset gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
                    });
                }
            });
        }
    }
});

tripApp.controller('ReturnAssetNewController', function ($routeParams, $scope, $route, $http, ngToast, CommonServices) {
    $scope.processing = false;
    $scope.disablecode = false;
    var cartArray = [];
    var noUrut = 1;
    var TotalItem = 1;
    $scope.displayCartArray = [];
    $scope.total_item = 0;
    $scope.karyawan = "";
    $scope.kode = "";

    CommonServices.setDatePickerJQuery();
    $scope.tanggal = CommonServices.currentDateID();

    /*$scope.getdatakaryawan = function() {
        $http.get('api/karyawan/data-karyawan.php').success(function(data, status) {
            $scope.data_karyawan = data;
        });
    };

    $scope.getdatakaryawan();
*/
    $scope.chooseKaryawan = function () {
        $scope.getdata();
    }

    $scope.getdata = function () {
        $http.get('api/asset/data-asset.php?param=return&id=' + $scope.karyawan).success(function (data, status) {
            $scope.data_asset = data.assetArray;
            $scope.data_karyawan = data.karyawanArray;
        });
    };

    $scope.getdata();

    $("#kode").on("change", function (e) {
        $scope.kode = this.value;
        //alert($scope.kode);
        $scope.changeKode();
    });

    $scope.changeKode = function () {
        if ($scope.kode != "") {
            $scope.anama = $scope.data_asset[$scope.kode].Nama;
            $scope.akode = $scope.data_asset[$scope.kode].KodeAsset;
            $scope.idasset = $scope.data_asset[$scope.kode].IDAsset;

            //FORCE DISPLAY TO ELEMENT THOUGHT JQUERY - BUG SELECT2 AJAX MODEL CANNOT UPDATE DOM
            $('#nama').val($scope.anama);
        } else {
            $('#nama').val("");
        }
    }

    $scope.addtocart = function () {
        var IDAsset = $scope.idasset;
        var KodeAsset = $scope.akode;
        var NamaAsset = $scope.anama;

        if (typeof cartArray[IDAsset] != 'undefined') {
            ngToast.create({
                className: 'danger',
                content: 'Asset tersebut sudah dipilih. <i class="fa fa-remove"></i>'
            });
        } else {
            cartArray[IDAsset] = { NoUrut: noUrut, IDAsset: IDAsset, KodeAsset: KodeAsset, NamaAsset: NamaAsset };
            noUrut += 1;
        }
        $scope.displayCart();
        //alert(cartArray);
    }

    $scope.displayCart = function () {
        function sortFunction(a, b) {
            if (a['NoUrut'] == b['NoUrut']) {
                return 0;
            } else {
                return (a['NoUrut'] < b['NoUrut']) ? -1 : 1;
            }
        }
        $scope.displayCartArray = cartArray.filter(function () {
            return true
        });
        $scope.displayCartArray = $scope.displayCartArray.sort(sortFunction);
        $scope.total_item = $scope.displayCartArray.length;
    }

    $scope.deleteCart = function (a) {
        delete cartArray[a];
        $scope.displayCart();

        return false;
    };

    $scope.submitForm = function (isValid) {
        if ($scope.displayCartArray.length == "0") {
            ngToast.create({
                className: 'danger',
                content: 'Detail return asset anda masih kosong. <i class="fa fa-remove"></i>'
            });
        } else {
            if (isValid) {
                $scope.processing = true;

                $http({
                    method: "POST",
                    url: 'api/return-asset/new.php',
                    data: $.param({
                        'tanggal': $scope.tanggal,
                        'karyawan': $scope.karyawan,
                        'total_item': $scope.total_item,
                        'uploaded': $scope.userLoginID,
                        'cart': JSON.stringify(cartArray)
                    }),
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                }).success(function (data, status) {
                    if (data == "1") {
                        ngToast.create({
                            className: 'success',
                            content: 'Data return asset berhasil disimpan <i class="fa fa-remove"></i>'
                        });
                        window.document.location = '#/data-return-asset';
                    } else {
                        $scope.processing = false;
                        ngToast.create({
                            className: 'danger',
                            content: 'Data return asset gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
                        });
                    }
                });
            }
        }
    };
});

tripApp.controller('ReturnAssetDetailController', function ($scope, $route, $rootScope, $routeParams, $http, ngToast, Upload) {
    $scope.processing = false;
    $scope.jabatan = "";
    $scope.departement = "";

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
        $http.get('api/return-asset/data-return.php?act=DetailReturn&id=' + $routeParams.idReturn).success(function (data, status) {
            $scope.data_detail = data.DataDetail;

            $scope.no_return = data.DataMaster[0].NoReturn;
            $scope.tanggal = data.DataMaster[0].Tanggal;
            $scope.karyawan = data.DataMaster[0].Karyawan;
            $scope.total_item = data.DataMaster[0].TotalItems;
        });
    };

    $scope.getAllData();
});