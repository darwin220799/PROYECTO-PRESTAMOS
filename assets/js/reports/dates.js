function reportPDF() {
    var start_d = $("#start_d").val();
    var end_d = $("#end_d").val();
    var coin_t = $("#coin_type2").val();
    var user_selected_id = $("#user_selected").val() ? '/' + $("#user_selected").val() : !(typeof USER_ID === 'undefined') ? '/' + USER_ID : '';
  
    if (start_d == '' || end_d == '') {
      alert('Ingrese las fechas')
    } else {
      window.open(base_url + 'admin/reports/dates_pdf/' + coin_t + '/' + start_d + '/' + end_d + user_selected_id);
    }
  }