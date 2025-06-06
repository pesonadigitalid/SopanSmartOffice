tripApp.controller('SatuanController', function($rootScope, $scope, $route, $http, ngToast){
    $scope.kategori = "0";
    
    $scope.getdata = function(){
        $http.get('api/satuan/data-satuan.php').success(function(data, status){
            $scope.data_satuan = data;
        });
    };
    $scope.getdata();
    
    $scope.removeRow = function (val){ 
        if(confirm("Anda yakin ingin menghapus data ini?")){    
            $http({
                method:"POST",
                url: 'api/satuan/delete.php', 
                data: $.param({
                    'idr':val
                }),
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function(data, status){
                if(data=="1"){
                    ngToast.create({
                      className: 'success',
                      content: 'Data satuan berhasil dihapus <i class="fa fa-remove"></i>'
                    });       
                    $route.reload();
                } else if(data=="2"){
                    ngToast.create({
                      className: 'danger',
                      content: 'Data satuan tidak bisa dihapus karena sudah terhubung dengan data barang. Menghapus paksa dapat merusak sistem <i class="fa fa-remove"></i>'
                    });
                } else {
                    ngToast.create({
                      className: 'danger',
                      content: 'Data satuan gagal dihapus. Silahkan coba kembali lagi <i class="fa fa-remove"></i>'
                    });
                }
            });
        }
    }
});

tripApp.controller('SatuanNewController', function($scope, $route, $http, ngToast){    
    $scope.processing = false;
    $scope.disablecode = false;
    $scope.statusUser = "0";
    
    $scope.submitForm = function(isValid) {
        if (isValid) {
            $scope.processing = true;
            $http({
                method:"POST",
                url: 'api/satuan/new.php', 
                data: $.param({
                    'nama':$scope.nama
                }),
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function(data, status){
                if(data=="1"){
                    ngToast.create({
                      className: 'success',
                      content: 'Data satuan berhasil ditambahkan <i class="fa fa-remove"></i>'
                    });        
                    window.document.location = '#/data-satuan';
                } else {
                    $scope.processing = false;
                    ngToast.create({
                      className: 'danger',
                      content: 'Data satuan gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
                    });
                }
            });
        }
    };
});

tripApp.controller('SatuanEditController', function($rootScope, $scope, $route, $routeParams, $http, ngToast){    
    $scope.processing = false;
    $scope.disablecode = true;
    
    $http.get('api/satuan/detail.php?id='+$routeParams.satuanId).success(function(data, status){
        $scope.nama = data.nama;
    });
    
    $scope.submitForm = function(isValid) {
        if (isValid) {
            $scope.processing = true;
            $http({
                method:"POST",
                url: 'api/satuan/edit.php', 
                data: $.param({
                    'nama':$scope.nama,
                    'id':$routeParams.satuanId
                }),
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function(data, status){
                if(data=="1"){
                    ngToast.create({
                      className: 'success',
                      content: 'Data satuan berhasil diperbaharui <i class="fa fa-remove"></i>'
                    });        
                    window.document.location = '#/data-satuan';
                } else {
                    $scope.processing = false;
                    ngToast.create({
                      className: 'danger',
                      content: 'Data satuan gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
                    });
                }
            });
        }
    };
});