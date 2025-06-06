tripApp.controller('GudangController', function($rootScope, $scope, $route, $http, ngToast){
    $scope.kategori = "0";

    $scope.getdata = function(){
        $http.get('api/gudang/data-gudang.php').success(function(data, status){
            $scope.data_gudang = data;
        });
    };
    $scope.getdata();

    $scope.removeRow = function (val){
        if(confirm("Anda yakin ingin menghapus data ini?")){
            $http({
                method:"POST",
                url: 'api/gudang/delete.php',
                data: $.param({
                    'idr':val
                }),
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function(data, status){
                if(data=="1"){
                    ngToast.create({
                      className: 'success',
                      content: 'Data gudang berhasil dihapus <i class="fa fa-remove"></i>'
                    });
                    $route.reload();
                } else if(data=="2"){
                    ngToast.create({
                      className: 'danger',
                      content: 'Data gudang tidak bisa dihapus karena sudah terhubung dengan data barang. Menghapus paksa dapat merusak sistem <i class="fa fa-remove"></i>'
                    });
                } else {
                    ngToast.create({
                      className: 'danger',
                      content: 'Data gudang gagal dihapus. Silahkan coba kembali lagi <i class="fa fa-remove"></i>'
                    });
                }
            });
        }
    }
});

tripApp.controller('GudangNewController', function($scope, $route, $http, ngToast){
    $scope.processing = false;
    $scope.disablecode = false;
    $scope.statusUser = "0";

    $scope.submitForm = function(isValid) {
        if (isValid) {
            $scope.processing = true;
            $http({
                method:"POST",
                url: 'api/gudang/new.php',
                data: $.param({
                    'nama':$scope.nama,
                    'is_default':$scope.is_default,
                }),
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function(data, status){
                if(data=="1"){
                    ngToast.create({
                      className: 'success',
                      content: 'Data gudang berhasil ditambahkan <i class="fa fa-remove"></i>'
                    });
                    window.document.location = '#/data-gudang';
                } else {
                    $scope.processing = false;
                    ngToast.create({
                      className: 'danger',
                      content: 'Data gudang gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
                    });
                }
            });
        }
    };
});

tripApp.controller('GudangEditController', function($rootScope, $scope, $route, $routeParams, $http, ngToast){
    $scope.processing = false;
    $scope.disablecode = true;

    $http.get('api/gudang/detail.php?id='+$routeParams.gudangId).success(function(data, status){
        $scope.nama = data.nama;
        $scope.is_default = data.is_default;
    });

    $scope.submitForm = function(isValid) {
        if (isValid) {
            $scope.processing = true;
            $http({
                method:"POST",
                url: 'api/gudang/edit.php',
                data: $.param({
                    'nama':$scope.nama,
                    'is_default':$scope.is_default,
                    'id':$routeParams.gudangId
                }),
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function(data, status){
                if(data=="1"){
                    ngToast.create({
                      className: 'success',
                      content: 'Data gudang berhasil diperbaharui <i class="fa fa-remove"></i>'
                    });
                    window.document.location = '#/data-gudang';
                } else {
                    $scope.processing = false;
                    ngToast.create({
                      className: 'danger',
                      content: 'Data gudang gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
                    });
                }
            });
        }
    };
});
