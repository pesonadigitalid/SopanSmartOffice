tripApp.controller('SPPDController', function($scope, $route, $http, ngToast, CommonServices, $rootScope){
    $scope.karyawan = "";
    
    $scope.datestart = CommonServices.firstDateMonth();
    $scope.dateend = CommonServices.lastDateMonth();

    $('.datepick').datepicker({
        format: 'dd/mm/yyyy',
        showOtherMonths: true,
        selectOtherMonths: true,
        autoclose: true
    });

    $scope.getdata = function(){
        $http.get('api/surat-perintah-perjalanan-dinas/data-sppd.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&karyawan=' + $scope.karyawan).success(function(data, status){
            $scope.data_sppd = data.SPPD;
            $scope.data_karyawan = data.Karyawan;
        });
    };
    $scope.getdata();
    
    $scope.refreshData = function () {
        $('#basicTable').dataTable().fnDestroy();
        $scope.getdata();
    }
    
    $scope.removeRow = function (val){ 
        if(confirm("Anda yakin ingin menghapus data ini?")){    
            $http({
                method:"POST",
                url: 'api/surat-perintah-perjalanan-dinas/delete.php', 
                data: $.param({
                    'idr':val
                }),
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function(data, status){
                if(data=="1"){
                    ngToast.create({
                      className: 'success',
                      content: 'Data perintah perjalanan dinas berhasil dihapus <i class="fa fa-remove"></i>'
                    });       
                    $route.reload();
                } else {
                    ngToast.create({
                      className: 'success',
                      content: 'Data perintah perjalanan dinas gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
                    });
                }
            });
        }
    }

    $scope.doPrint = function () {
      window.open($rootScope.baseURL + 'api/print/print-data-sppd.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&karyawan=' + $scope.karyawan, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
    }
});

tripApp.controller('SPPDNewController', function($rootScope, $scope, $route, $http, ngToast){    
    $scope.processing = false;
    $scope.disablecode = false;
    $scope.statusspk = "0";
    $scope.IDKaryawan = "";
    
    $('.datepick').datepicker({
        format: 'dd/mm/yyyy',
        showOtherMonths: true,
        selectOtherMonths: true,
        autoclose: true
    });
        
    $scope.getdatakaryawan = function(){
        $http.get('api/karyawan/data-karyawan.php').success(function(data, status){
            $scope.data_karyawan = data;
        });
    };
    
    $scope.getdatakaryawan();
    
    $scope.changeKaryawan = function(){
        $scope.IDKaryawan = $('#karyawan').val();
        $http.get('api/surat-perintah-kerja/change-karyawan.php?id='+$scope.IDKaryawan).success(function(data, status){
            $scope.change_karyawan = data;
            $('#nik').val(data[0]["NIK"]);
            $('#bagian').val(data[0]["Jabatan"]);
        });
    }
    
    $scope.submitForm = function(isValid) {
        if (isValid) {
            $scope.processing = true;
            $http({
                method:"POST",
                url: 'api/surat-perintah-perjalanan-dinas/new.php', 
                data: $.param({
                    'tanggal':$scope.tanggal,
                    'karyawan':$scope.karyawan,
                    'nama_perusahaan':$scope.nama_perusahaan,
                    'alamat':$scope.alamat,
                    'no_telp':$scope.no_telp,
                    'rencana_tugas':$scope.rencana_tugas,
                    'tgl_mulai':$scope.tgl_mulai,
                    'tgl_akhir':$scope.tgl_akhir,
                    'jam_mulai':$scope.jam_mulai,
                    'jam_akhir':$scope.jam_akhir,
                    'catatan':$scope.catatan,
                    'statusspk':$scope.statusspk,
                    'iduser':$rootScope.userLoginID
                }),
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function(data, status){
                if(data=="1"){
                    ngToast.create({
                      className: 'success',
                      content: 'Data perintah perjalanan dinas berhasil ditambahkan <i class="fa fa-remove"></i>'
                    });        
                    window.document.location = '#/data-surat-perintah-perjalanan-dinas';
                } else {
                    $scope.processing = false;
                    ngToast.create({
                      className: 'danger',
                      content: 'Data perintah perjalanan dinas gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
                    });
                }
            });
        }
    };
});

tripApp.controller('SPPDEditController', function($rootScope, $scope, $route, $routeParams, $http, ngToast){    
    $scope.processing = false;
    $scope.disablecode = true;
    $scope.IDKaryawan = "";
    
    $('.datepick').datepicker({
        format: 'dd/mm/yyyy',
        showOtherMonths: true,
        selectOtherMonths: true,
        autoclose: true
    });
        
    $scope.getdatakaryawan = function(){
        $http.get('api/karyawan/data-karyawan.php').success(function(data, status){
            $scope.data_karyawan = data;
        });
    };
    
    $scope.getdatakaryawan();
    
    $scope.changeKaryawan = function(){
        $scope.IDKaryawan = $('#karyawan').val();
        $http.get('api/surat-perintah-perjalanan-dinas/change-karyawan.php?id='+$scope.IDKaryawan).success(function(data, status){
            $scope.change_karyawan = data;
            $('#nik').val(data[0]["NIK"]);
            $('#bagian').val(data[0]["Jabatan"]);
        });
    }
    
    $http.get('api/surat-perintah-perjalanan-dinas/detail.php?id='+$routeParams.sppdId).success(function(data, status){
        $scope.no_sppd = data.no_sppd;
        $scope.tanggal = data.tanggal;
        $scope.karyawan = data.karyawan;
        $scope.nik = data.nik;
        $scope.bagian = data.bagian;
        $scope.nama_perusahaan = data.nama_perusahaan;
        $scope.alamat = data.alamat;
        $scope.no_telp = data.no_telp;
        $scope.rencana_tugas = data.rencana_tugas;
        $scope.tgl_mulai = data.tgl_mulai;
        $scope.tgl_akhir = data.tgl_akhir;
        $scope.jam_mulai = data.jam_mulai;
        $scope.jam_akhir = data.jam_akhir;
        $scope.catatan = data.catatan;
        $scope.statusspk = data.statusspk;
    });
    
    $scope.submitForm = function(isValid) {
        if (isValid) {
            $scope.processing = true;
            $http({
                method:"POST",
                url: 'api/surat-perintah-perjalanan-dinas/edit.php', 
                data: $.param({
                    'tanggal':$scope.tanggal,
                    'karyawan':$scope.karyawan,
                    'nama_perusahaan':$scope.nama_perusahaan,
                    'alamat':$scope.alamat,
                    'no_telp':$scope.no_telp,
                    'rencana_tugas':$scope.rencana_tugas,
                    'tgl_mulai':$scope.tgl_mulai,
                    'tgl_akhir':$scope.tgl_akhir,
                    'jam_mulai':$scope.jam_mulai,
                    'jam_akhir':$scope.jam_akhir,
                    'catatan':$scope.catatan,
                    'statusspk':$scope.statusspk,
                    'iduser':$rootScope.userLoginID,
                    'id':$routeParams.sppdId
                }),
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function(data, status){
                if(data=="1"){
                    ngToast.create({
                      className: 'success',
                      content: 'Data perintah perjalanan dinas berhasil diperbaharui <i class="fa fa-remove"></i>'
                    });        
                    window.document.location = '#/data-surat-perintah-perjalanan-dinas';
                } else {
                    $scope.processing = false;
                    ngToast.create({
                      className: 'danger',
                      content: 'Data perintah perjalanan dinas gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
                    });
                }
            });
        }
    };
});