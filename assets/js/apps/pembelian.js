tripApp.controller('PembelianController', function($rootScope, $scope, $q, $routeParams, $route, $http, ngToast) {
    $('.datepick').datepicker({
        format: 'dd/mm/yyyy',
        showOtherMonths: true,
        selectOtherMonths: true,
        autoclose: true
    });
    $scope.datestart = "";
    $scope.dateend = "";
    $scope.kode_proyek = "";

    $scope.getdata = function() {
        $http.get('api/pembelian/data-pembelian.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&kode_proyek=' + $scope.kode_proyek).success(function(data, status) {
            $scope.data_pembelian = data;
        });
    };

    //$scope.getdata();

    $scope.getdataproyek = function() {
        $http.get('api/proyek/data-proyek.php').success(function(data, status) {
            $scope.data_proyek = data;
        });
    };

    //$scope.getdataproyek();

    $q.all({
        data_pembelian: $http.get('api/pembelian/data-pembelian.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&kode_proyek=' + $scope.kode_proyek),
        data_proyek: $http.get('api/proyek/data-proyek.php')
    }).then(function(results) {
        $scope.data_pembelian = results.data_pembelian.data;
        $scope.data_proyek = results.data_proyek.data;
    });

    $scope.refreshData = function() {
        $('#basicTable').dataTable().fnDestroy();
        $scope.getdata();
    }

    $scope.removeRow = function(val) {
        if (confirm("Anda yakin ingin menghapus data ini?")) {
            $http({
                method: "POST",
                url: 'api/pembelian/delete.php',
                data: $.param({
                    'idr': val
                }),
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function(data, status) {
                if (data == "1") {
                    ngToast.create({
                        className: 'success',
                        content: 'Data pembelian berhasil dihapus <i class="fa fa-remove"></i>'
                    });
                    $route.reload();
                } else if (data == "2") {
                    ngToast.create({
                        className: 'danger',
                        content: 'Data pembelian tidak dapat dihapus karena stok telah terdistribusi... <i class="fa fa-remove"></i>'
                    });
                    $route.reload();
                } else {
                    ngToast.create({
                        className: 'danger',
                        content: 'Data pembelian gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
                    });
                }
            });
        }
    }

    $scope.doPrint = function(a) {
        window.open($rootScope.baseURL + 'api/print/print-pembelian.php?id=' + a, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
    }
});

tripApp.controller('PembelianNewController', function($rootScope, $scope, $q, $routeParams, $rootScope, $route, $http, ngToast) {
    var cartArray = [];
    var noUrut = 1;
    $scope.displayCartArray = [];
    $scope.total = 0;
    $scope.ppn = 0;
    $scope.ppn_persen = 0;
    $scope.diskon = 0;
    $scope.diskon_persen = 0;
    $scope.total2 = 0;
    $scope.grand_total = 0;
    $scope.sisa = 0;
    $scope.kembali = 0;
    $scope.pembayarandp = 0;
    $scope.metode_pembayaran = "Tunai";
    $scope.metode_pembayaran2 = "Kas Kecil";
    $scope.showBG = false;
    $scope.showBG2 = false;
    $scope.nobg = "";
    $scope.jatuhtempobg = "";

    $scope.changeMetodePayment2 = function(){
        $scope.nobg = "";
        $scope.jatuhtempobg = "";
        if($scope.metode_pembayaran=="Rekening BG"){
            $scope.showBG = true;
            $scope.showBG2 = true;
        } else {
            $scope.showBG = false;
            $scope.showBG2 = false;
        }
    };

    $('.datepick').datepicker({
        format: 'dd/mm/yyyy',
        showOtherMonths: true,
        selectOtherMonths: true,
        autoclose: true
    });

    $scope.getdata = function() {
        $http.get('api/purchase-order/data-purchase-order.php?param=pembelian').success(function(data, status) {
            $scope.data_purchase = data;
        });
    };

    //$scope.getdata();

    $scope.getdatabarang = function() {
        $http.get('api/barang/data-barang.php').success(function(data, status) {
            $scope.data_barang = data;
        });
    };

    //$scope.getdatabarang();

    $q.all({
        apiv1: $http.get('api/pembelian/load-all-requirement.php')
    }).then(function(results) {
        $scope.data_barang = results.apiv1.data.barang;
        $scope.data_purchase = results.apiv1.data.po;
        $scope.data_proyek = results.apiv1.data.proyek;
        $scope.data_supplier = results.apiv1.data.supplier;
    });

    $scope.usrlogin = $rootScope.userLoginName;

    $("#kode").on("change", function(e) {
        $scope.kode = this.value;
        $scope.changeKode();
    });

    $scope.changeKode = function() {
        if ($scope.kode != "") {
            $scope.anama = $scope.data_barang[$scope.kode].Nama;
            $scope.aharga = $scope.data_barang[$scope.kode].Harga;
            $scope.aidbarang = $scope.data_barang[$scope.kode].IDBarang;

            //FORCE DISPLAY TO ELEMENT THOUGHT JQUERY - BUG SELECT2 AJAX MODEL CANNOT UPDATE DOM
            $('#nama_barang').val($scope.anama);
            $('#harga').val($scope.aharga);
            $scope.qty = 1;
        } else {
            $('#nama_barang').val("");
            $('#harga').val("");
            $scope.qty = "";
        }
    }

    $scope.addtocart = function() {
        if ($scope.aidbarang > 0) {
            var IDBarang = $scope.aidbarang;
            var NamaBarang = $scope.anama;
            var Harga = $scope.aharga.toString().replace(/,/g, "");
            var Qty = $scope.qty;
            var QtyVal = $('#QtyBarang' + IDBarang).val();

            if (typeof cartArray[IDBarang] != 'undefined') {
                cartArray[IDBarang]["Harga"] = parseFloat(Harga);
                cartArray[IDBarang]["QtyBarang"] = parseInt(QtyVal) + parseInt(Qty);
                cartArray[IDBarang]["SubTotal"] = cartArray[IDBarang]["QtyBarang"] * cartArray[IDBarang]["Harga"];
                $('#QtyBarang' + IDBarang).val(cartArray[IDBarang]["QtyBarang"]);
            } else {
                SubTotal = Harga * Qty;
                cartArray[IDBarang] = { NoUrut: noUrut, IDBarang: IDBarang, NamaBarang: NamaBarang, Harga: Harga, QtyBarang: Qty, SubTotal: SubTotal };
                noUrut += 1;
            }

            $('#nama_barang').val('');
            $scope.qty = 1;
            $scope.aharga = 0;
            $scope.anama = "";
            $scope.aidbarang = 0;
            $scope.kode = "";

            $scope.displayCart();
        } else {
            alert('Ada sesuatu yang salah. Silahkan coba pilih barang anda kembali.');
        }
    }

    $scope.displayCart = function() {
        function sortFunction(a, b) {
            if (a['NoUrut'] == b['NoUrut']) {
                return 0;
            } else {
                return (a['NoUrut'] < b['NoUrut']) ? -1 : 1;
            }
        }
        $scope.total = 0;
        $scope.displayCartArray = cartArray.filter(function() {
            return true;
        });
        $scope.displayCartArray = $scope.displayCartArray.sort(sortFunction);
        $scope.displayCartArray.forEach(function(entry) {
            $scope.total = parseFloat($scope.total.toString().replace(/,/g, "")) + parseFloat(entry["SubTotal"]);
        });
        $scope.countingGrandTotal();
    }

    $scope.countingGrandTotal = function() {
        $scope.changeDiskon();
        $scope.changePPN();
        $scope.changePembayaran();
    }

    $scope.changeQty = function(a) {
        var QtyVal = $('#QtyBarang' + a).val();
        cartArray[a]['QtyBarang'] = QtyVal;
        cartArray[a]["SubTotal"] = cartArray[a]["Harga"].toString().replace(/,/g, "") * QtyVal;
        $scope.displayCart();
    }

    $scope.removeRow = function(a) {
        delete cartArray[a];
        $scope.displayCart();
        return false;
    };

    $scope.changeDiskon = function() {
        var DiskonPersen = $('#diskon_persen').val();
        $scope.diskon = (DiskonPersen / 100) * parseFloat($scope.total);
        $scope.total2 = parseFloat($scope.total) - $scope.diskon;
    }

    $scope.changePPN = function() {
        var PPNPersen = parseFloat($scope.ppn_persen);
        $scope.ppn = (PPNPersen / 100) * parseFloat($scope.total2);
        $scope.grand_total = parseFloat($scope.total2) + $scope.ppn;
    }

    $scope.changePembayaran = function() {
        var GrandTotal = parseFloat($scope.grand_total);
        var Pembayaran = parseFloat($scope.pembayarandp);

        $scope.kembali = Pembayaran - GrandTotal;
        $scope.sisa = GrandTotal - Pembayaran;

        if ($scope.kembali < 0)
            $scope.kembali = 0;

        if ($scope.sisa < 0)
            $scope.sisa = 0;
    }

    $scope.loadpurchaseorder = function(a) {
        cartArray = [];
        $http.get('api/pembelian/load-po.php?id=' + a).success(function(data, status) {
            $scope.loadpending = data;
            $scope.total = data["total"];
            $scope.diskon_persen = data["diskon_persen"];
            $scope.total2 = data["total2"];
            $scope.ppn_persen = data["ppn_persen"];
            $scope.grand_total = data["grand_total"];
            $scope.pembayarandp = data["pembayarandp"];
            $scope.sisa = data["sisa"];
            $scope.keterangan = data["keterangan"];

            for (i in data["Cart"]) {
                cartArray[data["Cart"][i].IDBarang] = { IDBarang: data["Cart"][i].IDBarang, NoUrut: data["Cart"][i].NoUrut, NamaBarang: data["Cart"][i].NamaBarang, Harga: data["Cart"][i].Harga, QtyBarang: data["Cart"][i].Qty, SubTotal: data["Cart"][i].SubTotal };
            }

            $scope.displayCart();
        });
    };

    $scope.changePO = function() {
        var NoPO = $('#no_po').val();
        $scope.loadpurchaseorder(NoPO);
    }

    $scope.tanggal = $rootScope.currentDateID;

    $scope.processing = false;
    $scope.submitForm = function(isValid) {
        if (isValid) {
            $scope.processing = true;
            $http({
                method: "POST",
                url: 'api/pembelian/new.php',
                data: $.param({
                    'no_po': $scope.no_po,
                    'tanggal': $scope.tanggal,
                    'supplier': $scope.supplier,
                    'proyek': $scope.proyek,
                    'usrlogin': $scope.usrlogin,
                    'total': $scope.total,
                    'diskon_persen': $scope.diskon_persen,
                    'diskon': $scope.diskon,
                    'total2': $scope.total2,
                    'ppn_persen': $scope.ppn_persen,
                    'ppn': $scope.ppn,
                    'grand_total': $scope.grand_total,
                    'pembayarandp': $scope.pembayarandp,
                    'sisa': $scope.sisa,
                    'keterangan': $scope.keterangan,
                    'uploaded': $scope.userLoginID,
                    'metode_pembayaran': $scope.metode_pembayaran,
                    'metode_pembayaran2': $scope.metode_pembayaran2,
                    'nobg': $scope.nobg,
                    'jatuhtempobg': $scope.jatuhtempobg,
                    'kembali': $scope.kembali,
                    'cart': JSON.stringify(cartArray)
                }),
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function(data, status) {
                if (data == "1") {
                    ngToast.create({
                        className: 'success',
                        content: 'Data pembelian berhasil disimpan <i class="fa fa-remove"></i>'
                    });
                    window.document.location = '#/data-pembelian/';
                } else {
                    $scope.processing = false;
                    ngToast.create({
                        className: 'danger',
                        content: 'Data pembelian gagal disimpan. Silahkan coba kembali. <i class="fa fa-remove"></i>'
                    });
                }
            });
        }
    };
});

tripApp.controller('PembelianDetailController', function($rootScope, $scope, $route, $routeParams, $http, ngToast) {
    $('.datepick').datepicker({
        format: 'dd/mm/yyyy',
        showOtherMonths: true,
        selectOtherMonths: true
    });

    $scope.processing = false;
    $http.get('api/pembelian/detail.php?id=' + $routeParams.pembelianId).success(function(data, status) {
        $scope.no_pembelian = data.no_pembelian;
        $scope.no_po = data.no_po;
        $scope.proyek = data.proyek;
        $scope.tanggal = data.tanggal;
        $scope.usrlogin = data.usrlogin;
        $scope.total = data.total;
        $scope.diskon_persen = data.diskon_persen;
        $scope.total2 = data.total2;
        $scope.ppn_persen = data.ppn_persen;
        $scope.grand_total = data.grand_total;
        $scope.pembayarandp = data.pembayarandp;
        $scope.sisa = data.sisa;
        $scope.keterangan = data.keterangan;
        $scope.supplier = data.supplier;
        $scope.metode_pembayaran = data.metode_pembayaran;
        $scope.metode_pembayaran2 = data.metode_pembayaran2;
        $scope.nobg = data.nobg;
        $scope.jatuhtempobg = data.jatuhtempobg;
        $scope.kembali = data.kembali;
        $scope.keterangan = data.keterangan;
        $scope.supplier = data.supplier;
        $scope.metode_pembayaran = data.metode_pembayaran;
        $scope.metode_pembayaran2 = data.metode_pembayaran2;
        $scope.nobg = data.nobg;
        $scope.jatuhtempobg = data.jatuhtempobg;
        $scope.kembali = data.kembali;

        if($scope.metode_pembayaran!="")
            $scope.showMethodPembayaran = true;
        else
            $scope.showMethodPembayaran = false;

        if($scope.metode_pembayaran2!="")
            $scope.showMethodPembayaran2 = true;
        else
            $scope.showMethodPembayaran2 = false;

        if($scope.metode_pembayaran=="Rekening BG"){
            $scope.showBG = true;
            $scope.showBG2 = true;
        } else {
            $scope.showBG = false;
            $scope.showBG2 = false;
        }
    });

    $scope.getdatadetail = function() {
        $http.get('api/pembelian/detail-cart.php?id=' + $routeParams.pembelianId).success(function(data, status) {
            $scope.data_detail = data;
        });
    };

    $scope.getdatadetail();

    $scope.doPrint = function() {
        window.open($rootScope.baseURL + 'api/print/print-pembelian.php?id=' + $scope.no_pembelian, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
    }
});
