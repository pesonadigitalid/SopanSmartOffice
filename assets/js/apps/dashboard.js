tripApp.controller('DashboardController', function ($rootScope, $scope, $route, $http, ngToast) {  //

  $scope.summaryAllSPB = "Rp. 0";
  $scope.totalAllSPB = 0;
  $scope.summaryActiveSPB = "Rp. 0";
  $scope.totalActiveSPB = 0;
  $scope.summaryCompletedSPB = "Rp. 0";
  $scope.totalCompletedSPB = 0;
  $scope.summaryNewSPB = "Rp. 0";
  $scope.totalNewSPB = 0;
  $scope.summaryInvoices = "Rp. 0";
  $scope.totalInvoices = 0;
  $scope.summaryPo = "Rp. 0";
  $scope.totalPo = 0;

  var arrayMonth = [];
  var arrayValueInvoices = [];
  var arrayValuePo = [];

  $scope.refreshChart = true;

  $scope.getDataSummary = function () {
    $http.get('api/dashboard/dashboard.php?type=LoadSummary').success(function (data, status) {
      $scope.summaryAllSPB = data.summaryAllSPB;
      $scope.totalAllSPB = data.totalAllSPB;
      $scope.summaryActiveSPB = data.summaryActiveSPB;
      $scope.totalActiveSPB = data.totalActiveSPB;
      $scope.summaryCompletedSPB = data.summaryCompletedSPB;
      $scope.totalCompletedSPB = data.totalCompletedSPB;
      $scope.summaryNewSPB = data.summaryNewSPB;
      $scope.totalNewSPB = data.totalNewSPB;
      $scope.summaryInvoices = data.summaryInvoices;
      $scope.totalInvoices = data.totalInvoices;
      $scope.summaryPo = data.summaryPo;
      $scope.totalPo = data.totalPo;
    });
  };

  $scope.getDataSummary();

  $scope.getDataChart = function () {
    $http.get('api/dashboard/dashboard.php?type=LoadChart').success(function (data, status) {
      data.months.forEach((item) => {
        arrayMonth.push(item.substring(0, 3));
      })

      arrayValueInvoices = data.valueInvoice;
      arrayValuePo = data.valuePo;

      $scope.refreshChart = false;
      $scope.loadChart();
    });
  };

  $scope.getDataChart();

  $scope.title = 'Chart Gross Revenue + Spending YTD ' + new Date().getFullYear();

  $scope.loadChart = function () {
    const data = {
      labels: arrayMonth,
      datasets: [
        {
          label: 'Invoice',
          backgroundColor: 'rgba(18, 223, 204, 0.3)',
          borderColor: '#10cfbd',
          fill: true,
          data: arrayValueInvoices,
          lineTension: 0.3
        },
        {
          label: 'Purchase Order',
          backgroundColor: 'rgba(143, 122, 225, 0.3)',
          borderColor: '#6d5cae',
          fill: true,
          data: arrayValuePo,
          lineTension: 0.3
        }
      ]
    };

    const config = {
      type: 'line',
      data: data,
      options: {
        plugins: {
          legend: {
            position: "bottom",
          }
        },
        responsive: true,
        interaction: {
          intersect: false,
        },
        scales: {
          x: {
            display: true
          },
          y: {
            display: true,
            suggestedMin: 0,
            suggestedMax: 200
          }
        }
      },
    };


    const ctx = document.getElementById('myChart');
    ctx.height = 100;

    new Chart(ctx, config);
  }
});
