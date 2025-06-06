tripApp.controller('StokOpnameController', function($rootScope, $scope, $route, $http, ngToast){
    $scope.jenis = "";
    
    $scope.getdata = function(){
        $http.get('api/stok-opname/data-stok-opname.php?jenis='+$scope.jenis).success(function(data, status){
            $scope.data_stok_opname = data;
        });
    };
    $scope.getdata();
    
    $scope.getdatamaterial = function(){
        $http.get('api/material/data-material.php').success(function(data, status){
            $scope.data_material = data;
        });
    };
    $scope.getdatamaterial();
    
    $scope.refreshData = function(){
        $('#basicTable').dataTable().fnDestroy();
        $scope.getdata();
    }
    
    $scope.editgudang = function(a,b,c){
        $scope.id_barang = a;
        $scope.nama_barang = b;
        $scope.stok = c;
        $('#StokGudang').modal('show');
    }
    
    $scope.editpurchasing = function(a,b,c){
        $scope.id_barang = a;
        $scope.nama_barang = b;
        $scope.stok = c;
        $('#StokPurchasing').modal('show');
    }
    
    $scope.updateStokGudang = function(){
        alert("IDBarang:"+$scope.id_barang+", Stok saat ini:"+$scope.stok_now);
    }
    
    $scope.updateStokPurchasing = function(){
        alert("IDBarang:"+$scope.id_barang+", Stok saat ini:"+$scope.stok_now);
    }
});