tripApp.service('CommonServices', function ($q, $rootScope) {
  this.currentDateID = function () {
    d = new Date();

    var dd = d.getDate();
    var mm = d.getMonth() + 1;
    var yyyy = d.getFullYear();

    if (parseInt(dd) < 10) dd = '0' + dd;
    if (parseInt(mm) < 10) mm = '0' + mm;

    return dd + "/" + mm + "/" + yyyy;
  };

  this.currentMonth = function () {
    d = new Date();
    var mm = d.getMonth() + 1;
    if (parseInt(mm) < 10) mm = '0' + mm;
    return mm;
  };

  this.currentYear = function () {
    d = new Date();
    var yyyy = d.getFullYear();
    return yyyy;
  };

  this.currentDate = function () {
    d = new Date();

    var dd = d.getDate();
    var mm = d.getMonth() + 1;
    var yyyy = d.getFullYear();

    if (parseInt(dd) < 10) dd = '0' + dd;
    if (parseInt(mm) < 10) mm = '0' + mm;

    return yyyy + "-" + mm + "-" + dd;
  };

  this.firstDateMonth = function () {
    d = new Date();

    var dd = d.getDate();
    var mm = d.getMonth() + 1;
    var yyyy = d.getFullYear();
    var firstDay = new Date(d.getFullYear(), d.getMonth(), 1).getDate();
    if (parseInt(firstDay) < 10) firstDay = '0' + firstDay;
    if (parseInt(mm) < 10) mm = '0' + mm;

    return firstDay + "/" + mm + "/" + yyyy;
  };

  this.lastDateMonth = function () {
    d = new Date();

    var dd = d.getDate();
    var mm = d.getMonth() + 1;
    var yyyy = d.getFullYear();
    var lastDay = new Date(d.getFullYear(), d.getMonth() + 1, 0);
    if (parseInt(mm) < 10) mm = '0' + mm;

    return lastDay.getDate() + "/" + mm + "/" + yyyy;
  };

  this.currentDate = function () {
    d = new Date();

    var dd = d.getDate();
    var mm = d.getMonth() + 1;
    var yyyy = d.getFullYear();

    if (parseInt(dd) < 10) dd = '0' + dd;
    if (parseInt(mm) < 10) mm = '0' + mm;

    return yyyy + "-" + mm + "-" + dd;
  };

  this.monthList = function () {
    var month = {
      '0': {
        'id': '01',
        'value': 'Januari'
      },
      '1': {
        'id': '02',
        'value': 'Februari'
      },
      '2': {
        'id': '03',
        'value': 'Maret'
      },
      '3': {
        'id': '04',
        'value': 'April'
      },
      '4': {
        'id': '05',
        'value': 'Mei'
      },
      '5': {
        'id': '06',
        'value': 'Juni'
      },
      '6': {
        'id': '07',
        'value': 'Juli'
      },
      '7': {
        'id': '08',
        'value': 'Agustus'
      },
      '8': {
        'id': '09',
        'value': 'September'
      },
      '9': {
        'id': '10',
        'value': 'Oktober'
      },
      '10': {
        'id': '11',
        'value': 'November'
      },
      '11': {
        'id': '12',
        'value': 'Desember'
      },
    };
    return month;
  };

  this.yearList = function () {
    var year = [];
    var d = new Date();
    for (var i = 2015; i <= d.getFullYear(); i++) {
      year.push(i);
    }
    return year;
  };

  this.setDatePickerJQuery = function () {
    d = new Date();

    var dd = d.getDate();
    var mm = d.getMonth() + 1;
    var yyyy = d.getFullYear();

    if (parseInt(dd) < 10) dd = '0' + dd;
    if (parseInt(mm) < 10) mm = '0' + mm;
    return $('.datepick').datepicker({ format: 'dd/mm/yyyy', showOtherMonths: true, selectOtherMonths: true, autoclose: true, setDate: new Date(2008, 9, 0o3) });
  }

  this.getDiscountValue = function (discount, price) {
    if (!discount) return 0;
    if (discount.indexOf("%") > -1) {
      var discountPercentageSplit = discount.split("+");
      var totalDiscount = 0;
      price = this.parseFloat(price)
      for (var discountPersentage of discountPercentageSplit) {
        if (discountPersentage) {
          var discountPersentageNumber = this.parseFloat(discountPersentage.replace("%", "").trim());
          var diskon = (price * discountPersentageNumber) / 100;

          totalDiscount += diskon;
          price -= diskon;
        }
      }
      return Math.round(totalDiscount);
    }
    return discount;
  }

  this.parseNumber = function (value) {
    if (!value) return 0;
    return value.toString().replace(/,/g, "");
  }

  this.getInputValueAsNumber = function (id) {
    if (!id) return 0;
    return $(id).val().toString().replace(/,/g, "");
  }

  this.getInputValueAsFloat = function (id) {
    if (!id) return 0;
    return parseFloat(this.getInputValueAsNumber(id));
  }

  this.parseFloat = function (value) {
    if (!value) return 0;
    return parseFloat(this.parseNumber(value));
  }

  this.getInputValueAsInt = function (id) {
    if (!id) return 0;
    return parseInt(this.getInputValueAsNumber(id));
  }

  this.setValueWithNumberFormat = function (id, value) {
    return $(id).val(numberWithCommas(value));
  }
});
