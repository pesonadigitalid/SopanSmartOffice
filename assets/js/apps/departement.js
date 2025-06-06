tripApp.controller('DepartementController', function($scope, $route, $http, ngToast){
    
    $scope.getdata = function(){
        $http.get('api/departement/data-departement.php').success(function(data, status){
            $scope.data_departement = data;
        });
    };
    
    $scope.getdata();
    
    $scope.removeRow = function (val){ 
        if(confirm("Anda yakin ingin menghapus data ini?")){    
            $http({
                method:"POST",
                url: 'api/departement/delete.php', 
                data: $.param({
                    'idr':val
                }),
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function(data, status){
                if(data=="1"){
                    ngToast.create({
                      className: 'success',
                      content: 'Data departement berhasil dihapus <i class="fa fa-remove"></i>'
                    });       
                    $route.reload();
                } else {
                    ngToast.create({
                      className: 'success',
                      content: 'Data departement gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
                    });  
                }
            });
        }
    }
});

tripApp.controller('DepartementNewController', function($scope, $route, $http, ngToast){      
    $scope.processing = false;    
    $scope.submitForm = function(isValid) {
        if (isValid) {
            $scope.processing = true;
            $http({
                method:"POST",
                url: 'api/departement/new.php', 
                data: $.param({
                    'nama':$scope.nama
                }),
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function(data, status){
                if(data=="1"){
                    ngToast.create({
                      className: 'success',
                      content: 'Data departement berhasil ditambahkan <i class="fa fa-remove"></i>'
                    });        
                    window.document.location = '#/data-departement';
                } else {
                    $scope.processing = false;
                    ngToast.create({
                      className: 'danger',
                      content: 'Data departement gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
                    });
                }
            });
        }
    };
});

tripApp.controller('DepartementEditController', function($scope, $route, $routeParams, $http, ngToast){     
    $scope.processing = false;
    $http.get('api/departement/detail.php?id='+$routeParams.departementId).success(function(data, status){
        $scope.nama = data.nama;
    });
    
    $scope.submitForm = function(isValid) {
        if (isValid) {
            $scope.processing = true;
            $http({
                method:"POST",
                url: 'api/departement/edit.php', 
                data: $.param({
                    'nama':$scope.nama,
                    'id':$routeParams.departementId
                }),
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function(data, status){
                if(data=="1"){
                    ngToast.create({
                      className: 'success',
                      content: 'Data departement berhasil diperbaharui <i class="fa fa-remove"></i>'
                    });        
                    window.document.location = '#/data-departement';
                } else {
                    $scope.processing = false;
                    ngToast.create({
                      className: 'danger',
                      content: 'Data departement gagal diperbaharui. Silahkan coba kembali <i class="fa fa-remove"></i>'
                    });
                }
            });
        }
    };
});