// Cargar
let report_title = '';
let date_range = '';
function loadGeneralReport() {
  const coin_id = $("#coin_type").val()
  const start_d = $("#start_d").val()
  const end_d = $("#end_d").val()
  const user_id = $("#user_id").val()
  var symbol = $('#coin_type option:selected').data("symbol").toUpperCase();
  if (start_d == '' || end_d == '') {
    alert('Ingrese las fechas')
    return;
  } else if (coin_id == '') {
    alert('Seleccione una moneda');
    return;
  }

  $.get(base_url + "admin/reports/ajaxGetCredits/" + coin_id + "/" + start_d + "/" + end_d + ((user_id != '') ? "/" + user_id : ''), function (data) {

    data = JSON.parse(data);

    if (data.credits[0].sum_credit == null) {
      var sum_credit = '0.00 ' + symbol
    } else {
      var sum_credit = data.credits[0].sum_credit + ' ' + (data.credits[0].short_name).toUpperCase()
    }
    $("#cr").html(sum_credit)

    if (data.credits[1].cr_interest == null) {
      var cr_interest = '0.00 ' + symbol
    } else {
      var cr_interest = data.credits[1].cr_interest + ' ' + (data.credits[1].short_name).toUpperCase()
    }
    $("#cr_interest").html(cr_interest)

    if (data.credits[2].cr_interestPaid == null) {
      var cr_interestPaid = '0.00 ' + symbol
    } else {
      var cr_interestPaid = data.credits[2].cr_interestPaid + ' ' + data.credits[2].short_name.toUpperCase()
    }
    $("#cr_interestPaid").html(cr_interestPaid)

    if (data.credits[3].cr_interestPay == null) {
      var cr_interestPay = '0.00 ' + symbol
    } else {
      var cr_interestPay = data.credits[3].cr_interestPay + ' ' + (data.credits[3].short_name).toUpperCase()
    }
    $("#cr_interestPay").html(cr_interestPay)
    if (data.credits[4].amount_payed == null) {
      var total_payed = '0.00 ' + symbol
    } else {
      var total_payed = (Number(data.credits[4].amount_payed) + Number(data.credits[4].amount_surcharge)).toFixed(2) + ' ' + (data.credits[4].short_name).toUpperCase()
    }
    $("#total_payed").html(total_payed)
    if (data.credits[5].payable == null) {
      var payable = '0.00 ' + symbol
    } else {
      var payable = data.credits[5].payable + ' ' + (data.credits[5].short_name).toUpperCase()
    }
    $("#payable").html(payable)
    user_name = '';
    if (typeof data.selected_user === 'undefined') {
      report_title = '';
    } else if (data.selected_user.user_name.toLowerCase() == 'all') {
      report_title = 'RESUMEN GENERAL DE PRÉSTAMOS';
      user_name = 'TODOS';
    } else {
      report_title = 'RESUMEN DE PRÉSTAMOS - ' + data.selected_user.user_name;
      user_name = data.selected_user.user_name;
    }
    date_range = `${start_d} - ${end_d}`;
    $("#range_date").html('RANGO DE FECHAS: ' + date_range);
    toastr["success"](date_range, user_name);
    $("#message").html('USUARIO: ' + user_name);
    if (report_title != '')
      document.getElementById('alert_message').style.display = "block";
    else
      document.getElementById('alert_message').style.display = "none";
  });
}