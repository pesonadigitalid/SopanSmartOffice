tripApp.controller('PenjualanGrosirController', function($scope, $routeParams, $rootScope, $route, $http, ngToast) {
    $('.datepick').datepicker({
        format: 'dd/mm/yyyy',
        showOtherMonths: true,
        selectOtherMonths: true,
        autoclose: true
    });

    $scope.datestart = "";
    $scope.dateend = "";
    $scope.pelanggan = "";
    $scope.filterstatus = "";
    $scope.activeMenu = '';

    $scope.getAllData = function() {
        $http.get('api/penjualan-grosir/penjualan.php?act=DataList&datestart=' + $scope.datestart + '&dateend=' + $scope.dateend + '&pelanggan=' + $scope.pelanggan + '&filterstatus=' + $scope.filterstatus).success(function(data, status) {
            $scope.data_penjualan = data.penjualan;
            $scope.data_pelanggan = data.pelanggan;

            $scope.all = data.all;
        });
    };

    $scope.getAllData();

    $scope.doFilter = function(a) {
        $scope.filterstatus = a;
        $scope.activeMenu = a;
        $scope.refreshData();
    }

    $scope.refreshData = function() {
        $('#basicTable').dataTable().fnDestroy();
        $scope.getAllData();
    }

    $scope.removeRow = function(val) {
        if (confirm("Anda yakin ingin menghapus data ini?")) {
            $http({
                method: "POST",
                url: 'api/penjualan-grosir/penjualan.php?act=Delete',
                data: $.param({
                    'idr': val
                }),
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function(data, status) {
                if (data == "1") {
                    ngToast.create({
                        className: 'success',
                        content: 'Data penjualan berhasil dihapus <i class="fa fa-remove"></i>'
                    });
                    $scope.refreshData();
                } else {
                    ngToast.create({
                        className: 'danger',
                        content: 'Data penjualan gagal dihapus. Silahkan coba kembali lagi... <i class="fa fa-remove"></i>'
                    });
                }
            });
        }
    }

    $scope.doPrint2 = function() {
        window.open($rootScope.baseURL + 'api/print/print-data-penjualan-grosir.php?datestart=' + $scope.datestart + '&dateend=' + $scope.dateend, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
    }
});

tripApp.controller('PenjualanGrosirNewController', function($scope, $routeParams, $rootScope, $route, $http, ngToast) {
    var cartArray = [];
    var noUrut = 1;
    $scope.displayCartArray = [];
    $scope.totalItem = 0;

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

    $scope.pelanggan = "";

    $('.datepick').datepicker({
        format: 'dd/mm/yyyy',
        showOtherMonths: true,
        selectOtherMonths: true,
        autoclose: true
    });

    $scope.getdata = function() {
        $http.get('api/penjualan-grosir/penjualan.php?act=LoadAllRequirement').success(function(data, status) {
            $scope.data_pelanggan = data.pelanggan;
            $scope.data_barang = data.barang;
        });
    };

    $scope.getdata();

    $scope.usrlogin = $rootScope.userLoginName;
    $scope.tanggal = $rootScope.currentDateID;

    $("#kode").on("change", function(e) {
        $scope.kode = this.value;
        $scope.changeKode();
    });

    $scope.changeKode = function() {
        if ($scope.kode != "") {
            $scope.anama = $scope.data_barang[$scope.kode].Nama;
            $scope.aidbarang = $scope.data_barang[$scope.kode].IDBarang;
            $scope.isserialize = $scope.data_barang[$scope.kode].IsSerialize;

            $scope.limit = $scope.data_barang[$scope.kode].Limit;
            $scope.harga = $scope.data_barang[$scope.kode].HargaJualGrosir;

            $('#nama_barang').val($scope.anama);
            $scope.qty = 1;
            $scope.serialnumber = "";

            if ($scope.data_barang[$scope.kode].IsSerialize == "1") {
                $scope.disabledQty = true;
                $scope.disabledSN = false;
            } else {
                $scope.disabledQty = false;
                $scope.disabledSN = true;
            }
        } else {
            $('#nama_barang').val("");
            $scope.qty = "";
            $scope.serialnumber = "";
        }
    }

    $scope.noMoreThanLimit = function() {
        if ($scope.qty > $scope.limit)
            $scope.qty = $scope.limit;
    }

    $scope.addtocart = function() {
        if ($scope.isserialize === "1" && $scope.serialnumber === "") {
            ngToast.create({
                className: 'danger',
                content: 'Silahkan tambahkan No Seri Barang untuk barang yang terserialisasi. <i class="fa fa-remove"></i>'
            });
        } else {
            if ($scope.aidbarang > 0) {
                var IDBarang = $scope.aidbarang;
                var NamaBarang = $scope.anama;
                var Qty = parseFloat($scope.qty);
                var Limit = $scope.limit;
                var SN = $scope.serialnumber;
                var Harga = parseFloat($scope.harga);
                var IsSerialize = $scope.isserialize;
                var updated = false;

                var SubTotal = Harga * Qty;

                if (IsSerialize == 0) {
                    cartArray.forEach(function(entry) {
                        if (IDBarang == entry["IDBarang"]) {
                            updated = true;
                            entry["QtyBarang"] += parseFloat(Qty);
                        }
                    });
                }

                if (!updated) {
                    cartArray[noUrut] = { NoUrut: noUrut, IDBarang: IDBarang, NamaBarang: NamaBarang, QtyBarang: Qty, SNBarang: SN, IsSerialize: IsSerialize, Limit: Limit, Harga: Harga, SubTotal: SubTotal };
                    noUrut += 1;
                }

                $('#nama_barang').val('');
                $scope.qty = "";
                $scope.serialnumber = "";
                $scope.isserialize = "";
                $scope.anama = "";
                $scope.aidbarang = 0;
                $scope.kode = "";
                $scope.limit = "";

                $scope.displayCart();
            } else {
                alert('Ada sesuatu yang salah. Silahkan ulang pilih data barang!');
            }
        }
    }

    $scope.displayCart = function() {
        console.log(cartArray);
        var lastIDBarang = "";

        function sortFunction(a, b) {
            if (a['NoUrut'] == b['NoUrut']) {
                return 0;
            } else {
                return (a['NoUrut'] < b['NoUrut']) ? -1 : 1;
            }
        }
        $scope.total = 0;
        $scope.totalItem = 0;
        $scope.displayCartArray = cartArray.filter(function() {
            return true
        });
        $scope.displayCartArray = $scope.displayCartArray.sort(sortFunction);
        $scope.displayCartArray.forEach(function(entry) {
            $scope.totalItem += parseFloat(entry["QtyBarang"]);
            $scope.total = parseFloat($scope.total.toString().replace(/,/g, "")) + parseFloat(entry["SubTotal"].toString().replace(/,/g, ""));
        });
        $scope.countingGrandTotal();
    }

    $scope.changeQty = function(a) {
        var QtyVal = parseFloat($('#QtyBarang' + a).val());
        if (QtyVal > cartArray[a]['Limit']) QtyVal = cartArray[a]['Limit'];
        cartArray[a]['QtyBarang'] = QtyVal;
        cartArray[a]["SubTotal"] = cartArray[a]["Harga"].toString().replace(/,/g, "") * QtyVal;
        $scope.displayCart();
    }

    $scope.changeSN = function(a) {
        var SNVal = $('#SNBarang' + a).val();
        cartArray[a]['SNBarang'] = SNVal;
        $scope.displayCart();
    }

    $scope.countingGrandTotal = function() {
        $scope.changeDiskon();
        $scope.changePPN();
        $scope.changePembayaran();
    }

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

    $scope.removeRow = function(a) {
        delete cartArray[a];
        $scope.displayCart();

        return false;
    };

    $scope.processing = false;
    $scope.submitForm = function(isValid) {
        if (isValid) {
            $scope.processing = true;
            $http({
                method: "POST",
                url: 'api/penjualan-grosir/penjualan.php?act=InsertNew',
                data: $.param({
                    'tanggal': $scope.tanggal,
                    'usrlogin': $rootScope.userLoginName,
                    'pelanggan': $scope.pelanggan,
                    'total_item': $scope.totalItem,
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
                    'kembali': $scope.kembali,
                    'cart': JSON.stringify(cartArray)
                }),
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).success(function(data, status) {
                if (data.res == "1") {
                    ngToast.create({
                        className: 'success',
                        content: data.mes + ' <i class="fa fa-remove"></i>'
                    });
                    window.document.location = '#/data-penjualan-grosir/';
                } else {
                    $scope.processing = false;
                    ngToast.create({
                        className: 'danger',
                        content: data.mes + ' <i class="fa fa-remove"></i>'
                    });
                }
            });
        }
    };
});

tripApp.controller('PenjualanGrosirDetailController', function($scope, $route, $routeParams, $http, ngToast) {
    $http.get('api/penjualan-grosir/penjualan.php?act=Detail&id=' + $routeParams.penjualanId).success(function(data, status) {

        $scope.no_penjualan = data.master.NoPenjualan;
        $scope.pelanggan = data.master.Pelanggan;
        $scope.tanggal = data.master.Tanggal;
        $scope.totalItem = data.master.TotalItem;
        $scope.total = data.master.Total;
        $scope.ppn = data.master.PPN;
        $scope.ppn_persen = data.master.PPNPersen;
        $scope.diskon = data.master.Diskon;
        $scope.diskon_persen = data.master.DiskonPersen;
        $scope.total2 = data.master.Total2;
        $scope.grand_total = data.master.GrandTotal;
        $scope.sisa = data.master.Sisa;
        $scope.kembali = data.master.Kembali;
        $scope.pembayarandp = data.master.TotalPembayaran;
        $scope.metode_pembayaran = data.master.MetodePembayaran1;
        $scope.metode_pembayaran2 = data.master.MetodePembayaran2;

        $scope.data_detail = data.detail;
    });

    $scope.doPrint = function() {
        window.open($rootScope.baseURL + 'api/print/print-penjualan-grosir.php?id=' + $routeParams.penjualanId, 'Print', 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=1000,height=450');
    }
});
