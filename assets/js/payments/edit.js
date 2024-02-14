cashRegisterId = document.getElementById('cash_register_id');
cashRegisterUpdate = document.getElementById('cash_register_update');
coinId = null;

// Si existe un cliente seleccionado por defecto, carga su préstamo
async function autoSearch() {
  select = document.getElementById('search');
  if (select.value != 0) {
    await loadLoan();
    await getCashRegisters(coinId);
  }
}
// Se ejecuta después de que termina de cargar la página
window.onload = async function () {
  autoSearch();
}

//  Funcion para cargar las cuotas de credito de un cliente al cobrar credito
async function loadLoan() {
  customer_id = document.getElementById("search").value
  if (customer_id > 0) {
    await fetch(base_url + "admin/payments/ajax_get_loan/" + customer_id)
      .then(response => response.json())
      .then(data => {
        if (data.loan != null) {
          $("#customer_id").val(data.loan.customer_id);
          $("#loan_id").val(data.loan.id);
          $("#credit_amount").val(data.loan.credit_amount);
          $("#payment_m").val(data.loan.payment_m);
          $("#coin").val(data.loan.coin_name);
          $("#adviser").val(data.loan.user_name);
          $("#total_amount").val('');
          loadLoanItems(data.loan.id);
          loadGuarantors(data.loan.id);
          coinId = data.loan.coin_id;
        }
        else {
          alert('No se encontró un préstamo asociado al cliente seleccionado');
          window.location.reload();
        }
      })
  } else {
    document.getElementById("guarantors_container").style.display = "none"
    $('#register_loan').attr('disabled', true);
    clearform();
  }
}

async function autoLoad() {
  await loadLoan();
  await getCashRegisters(coinId);
}

function clearform() {
  coinId = null;
  $("#credit_amount").val('');
  $("#payment_m").val('');
  $("#coin").val('');
  $("#total_amount").val('');
  $("#quotas").dataTable().fnDestroy();
  $('#quotas').dataTable({
    "bPaginate": false, //Ocultar paginación
    "scrollY": '50vh',
    "scrollCollapse": true,
    "aaData": []
  });
}

function loadLoanItems(loan_id) {
  // Consultar cuotas del préstamo
  fetch(base_url + "admin/payments/ajax_get_loan_items/" + loan_id)
    .then(responsex => responsex.json())
    .then(datax => {
      if (datax.quotas != null) { // Cargar tabla
        // cargar tabla de cuotas
        var x = new Array(datax.quotas.length);
        if (datax.quotas.length > 0) {
          const idParam = '$idParam';
          const nameParam = '$nameParam';
          const statusParam = "$statusParam"; // disbled and checked or empty
          const payableParam = "$payableParam";
          const numQuotaParam = "$numQuotaParam";
          const valueParam = "$valueParam";
          const checkbox = `<input type="checkbox" name="quota_id[]" ${(statusParam)} data_fee='${payableParam}' num_quota='${numQuotaParam}' value='${valueParam}'>`;
          const maxParam = "$maxParam";
          const input = `<input type='number' step=".01" min="0.01" max="${maxParam}" id='${idParam}' name='${nameParam}' class='form-control col-md-12 text-center' onchange="calculateTotal();" disabled>`;

          for (i = 0; i < datax.quotas.length; i++) {
            const status = datax.quotas[i].status == 1 ? true : false;
            const fee_amount = datax.quotas[i].fee_amount;
            const id = datax.quotas[i].id;
            const num_quota = datax.quotas[i].num_quota;
            const date = datax.quotas[i].date;
            // const noDetailsPaid = (!status && datax.quotas[i].payed == null) ? true : false;
            const payed = (!status) ?
              fee_amount : (datax.quotas[i].payed != null) ? datax.quotas[i].payed : 0;
            const surcharge = (datax.quotas[i].surcharge != null) ? datax.quotas[i].surcharge : 0;
            const stateStatus = status ? "" : 'disabled checked';
            const payable = (fee_amount - payed).toFixed(2);
            const payedChecbox = checkbox
              .replace(statusParam, stateStatus)
              .replace(payableParam, payable)
              .replace(numQuotaParam, num_quota)
              .replace(valueParam, id);
            const surchargeInput = status ? input
              .replace(idParam, `surcharge_${id}`)
              .replace(nameParam, `surcharge_${id}`)
              .replace(maxParam, "")
              .replace("0.01", 0) : `<input class='form-control col-md-12 text-center' value='${surcharge}' disabled />`; // recargo
            const paymentInput = status ? input
              .replace(idParam, `amount_quota_${id}`)
              .replace(nameParam, `amount_quota_${id}`)
              .replace(maxParam, payable)
              : '<input class="btn btn-outline-success col-md-12" value="Completo" disabled/>';
            x[i] = [
              payedChecbox,
              num_quota,
              date,
              fee_amount,
              payed,
              payable,
              surchargeInput,
              paymentInput
            ]
          }
        }
        // clear the table before populating it with more data
        $("#quotas").dataTable().fnDestroy();
        $('#quotas').dataTable({
          "bPaginate": false, //Ocultar paginación
          "scrollY": '50vh',
          "scrollCollapse": true,
          "aaData": x
        })
        $('input:checkbox').on('change', function () {
          calculateTotal();
        });
      } else {
        $("#quotas").dataTable().fnDestroy();
        $('#quotas').dataTable({
          "bPaginate": false, //Ocultar paginación
          "scrollY": '50vh',
          "scrollCollapse": true,
          "aaData": null
        })
      }
    });
}

function calculateTotal() {
  $("#register_loan").attr("disabled", true);
  var total = 0;
  $('input:checkbox:enabled').each(function () {
    surcharge_input_id = '#surcharge_' + $(this).val(); // rechargeInput
    payment_input_id = '#amount_quota_' + $(this).val() // Payment
    if ($(this).prop('checked')) {
      value = $(this).attr('data_fee');
      if ($(payment_input_id).val() == '') $(payment_input_id).val(value);
      if ($(surcharge_input_id).val() == '') $(surcharge_input_id).val(0);
      $(payment_input_id).attr("disabled", false);
      $(surcharge_input_id).attr("disabled", false);
    } else {
      value = '';
      $(payment_input_id).val(value);
      $(surcharge_input_id).val(value);
      $(payment_input_id).attr("disabled", true);
      $(surcharge_input_id).attr("disabled", true);
    }
    total += isNaN(parseFloat($(payment_input_id).val())) || isNaN(parseFloat($(surcharge_input_id).val())) ? 0 : parseFloat($(payment_input_id).val()) + parseFloat($(surcharge_input_id).val());
  });
  $("#total_amount").val(total.toFixed(2));
  setTimeout(() => { // Ayuda a que no se envie el formulario con la tecla enter
    if (total > 0 && $("#cash_register_id").val() != '')
      $("#register_loan").attr("disabled", false);
  }, 500);
}


function loadGuarantors(loan_id) {
  fetch(base_url + "admin/payments/ajax_get_guarantors/" + loan_id)
    .then(response => response.json())
    .then(x => {
      if (x.guarantors != null) {
        var options = "";
        x.guarantors.forEach(element => {
          var option = '<button type="button" class="btn btn-secondary margin-right" >' + element.ci + " | " + element.guarantor_name + '</button>';
          options += option;
        });
        $("#guarantors_contend").html("");
        document.getElementById("guarantors_container").style.display = (x.guarantors.length > 0) ? "" : "none"
        $("#guarantors_contend").html(options);
      }
    })
}

// valida y pide confirmación antes de enviar el formulario para pagar
function payConfirmation() {
  try {
    total_amount = $("#total_amount").val()
    separator = "\n";
    errors = "";
    quotas = 0;
    formTotal = 0;
    quotasArray = new Array();
    if(cashRegisterId.value == '' || cashRegisterId.value == null) errors += ((errors == '') ? '' : separator) + "- Selecciona una caja.";
    if (isNaN(total_amount)) {
      errors += ((errors == '') ? '' : separator) + "- Ingrese un monto válido.";
    } else { // si total_amount es un número
      if (total_amount <= 0) errors += ((errors == '') ? '' : separator) + "- Debe ingregar un monto mayor a 0.";
      // Validar
      $('input:checkbox:enabled:checked').each(function () {
        formTotal += parseFloat($('#amount_quota_' + $(this).val()).val()) + parseFloat($('#surcharge_' + $(this).val()).val());
        const object = {
          quota_id: $(this).val(),
          num_quota: $(this).attr('num_quota'),
          quota_amount: $('#amount_quota_' + $(this).val()).val(),
          surcharge_amount: $('#surcharge_' + $(this).val()).val()
        };
        if (parseFloat(object.quota_amount) <= 0)
          errors += ((errors == '') ? '' : separator) + `- La cuota ${object.num_quota} debe ser mayor a 0.`;
        if (parseFloat(object.quota_amount) > parseFloat($(this).attr('data_fee')))
          errors += ((errors == '') ? '' : separator) + `- La cuota ${object.num_quota} no puede ser mayor a ${$(this).attr('data_fee')}.`;
        if (parseFloat(object.surcharge_amount) < 0)
          errors += ((errors == '') ? '' : separator) + `- El recargo en la cuota ${object.num_quota}, no puede ser negativo.`;
        quotasArray[quotas] = object;
        quotas++;
      });
      formTotal = formTotal.toFixed(2)
      if (quotas <= 0) errors += ((errors == '') ? '' : separator) + "- Debe existir al menos una cuota seleccionada.";
      if (formTotal != total_amount) errors += ((errors == '') ? '' : separator) + "- El total no coincide con en el número de cuotas seleccionadas.";
    }
    if (errors == '') {
      strQuotas = "";
      quotasArray.forEach(
        (quotaItem) => { strQuotas += `(${quotaItem.quota_id}) Cuota: ${quotaItem.num_quota}: ${quotaItem.quota_amount} Recargo: ${quotaItem.surcharge_amount}\n`; }
      );
      return confirm(
        `Número de cuotas: ${quotas}\n\n${strQuotas}\nTotal: ${total_amount}\n\n¿Continuar?`
      );
    } else {
      alert("ERRORES:\n\n" + errors);
      return false;
    }
  } catch (e) {
    console.log(e);
    toastr["error"](e, 'ERROR');
    return false;
  }
}

/**
 * Optiene las cajas con el tipo de moneda
 */
async function getCashRegisters(coinId) {
  try {
    const resQuery = await fetch(`${base_url}admin/payments/ajax_get_cash_registers/${coinId}`);
    const list = await resQuery.json();
    while (cashRegisterId.firstChild) {
      cashRegisterId.removeChild(cashRegisterId.firstChild);
    };
    list.forEach(element => {
      option = `<option value="${element.id}">${element.name + " | Saldo: " + element.total_amount + " " + element.short_name + ""}</option>`;
      cashRegisterId.insertAdjacentHTML("beforeend", option);
    });
  } catch (error) {
    console.log(error);
  }
}

cashRegisterUpdate.addEventListener('click', event =>{
  getCashRegisters(coinId);
});