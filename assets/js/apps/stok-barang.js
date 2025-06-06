tripApp.controller('StokBarangController', function($rootScope, $scope, $route, $http, ngToast){
    $scope.jenis = "";
    
    $scope.getdata = function(){
        $http.get('api/stok-barang/data-stok-barang.php?jenis='+$scope.jenis).success(function(data, status){
            $scope.data_stok_barang = data.dataStok;
            $scope.data_material = data.dataMaterial;
        });
    };
    $scope.getdata();
    
    $scope.refreshData = function(){
        $('#basicTable').dataTable().fnDestroy();
        $scope.getdata();
    }
});