const selectCustomer = document.getElementById('customer_id');
const selectCashRegisters = document.getElementById('cash_register_id');
const selectCoin = document.getElementById('coin_id');
const cashRegisterId = document.getElementById('cash_register_id');
const creditAmount = document.getElementById('credit_amount');
const cashRegisterUpdate = document.getElementById('cash_register_update');
const coinId = document.getElementById('coin_id');
cashRegisters = [];

$(document).ready(function () {
  // Realiza la suma de las cuotas seleccionadas al registrar un nuevo prestamo
  $('#calcular').on('click', function () {
    // var define una variable global o local en una función sin importar el ámbito del bloque
    var contador = 0
    errors = '';
    const focusables = new Array();
    if ($("#customer_id").val() == "0" || $("#customer_id").val() == "") {
      contador++;
      error = `${errors == '' ? '' : '\n'}- Selecciona un cliente.`;
      errors += error;
      focusables.push($("#customer_id"));
    }

    if ($("#credit_amount").val() == "") {
      contador++;
      error = `${errors == '' ? '' : '\n'}- Ingresar monto.`;
      errors += error;
      focusables.push($("#credit_amount"));
    }
    if ($("#time").val() == "") {
      contador++;
      error = `${errors == '' ? '' : '\n'}- Ingresar tiempo.`;
      errors += error;
      focusables.push($("#time"));
    }

    if ($("#in_amount").val() == "") {
      contador++;
      error = `${errors == '' ? '' : '\n'}- Ingresar interés.`;
      errors += error;
      focusables.push($("#in_amount"));
    }

    if ($("#cash_register_id").val() == "" || $("#cash_register_id").val() == null) {
      contador++;
      error = `${errors == '' ? '' : '\n'}- Selecciona una caja.`;
      errors += error;
      focusables.push($("#cash_register_id"));
    }
    cashRegister = cashRegisters.find(element => element.id == cashRegisterId.value);
    if (cashRegister != null) {
      if (Number.parseFloat(creditAmount.value) > Number.parseFloat(cashRegister.total_amount)) {
        error = `${errors == '' ? '' : '\n'}- La caja no contiene el monto requerido.`;
        errors += error;
        focusables.push($("#cash_register_id"));
      }
    }

    if ($("#date").val() == "") {
      contador++;
      error = `${errors == '' ? '' : '\n'}- Ingresar fecha emisión.`;
      errors += error;
      focusables.push($("#date"));
    }


    if (focusables.length > 0) focusables[0].focus();
    if (contador == 0) {
      $('#register_loan').attr('disabled', false);
    } else {
      $('#register_loan').attr('disabled', true);
    }

    if (errors != '') {
      toastr["warning"]("Verifica el formulario", 'ERROR');
      alert('ERRORES:\n\n' + errors);
      return;
    }

    let time = parseFloat($('#time').val()); // n meses
    let payment = $('#payment_m').val(); // mensual, quincenal, semanal, diario
    let monto = parseFloat($('#credit_amount').val());
    
    let i = ($('#in_amount').val() / 100);
    if (payment.toLowerCase() == 'mensual') {
      $('#num_fee').val(time * 1);
      let num_cuotas = $('#num_fee').val();
      let I = Math.pow(1 + i,1/12) - 1;
      
      let cuota = monto * (I / (1 - Math.pow(1 + I, -num_cuotas))*1);
      let monto_total =  cuota*num_cuotas;
      $('#fee_amount').val(cuota.toFixed(2));
      $('#valor_interes').val(monto_total.toFixed(2)-monto);
      $('#monto_total').val(monto_total.toFixed(2));
      loadCuotasTimelime();
      toastr["success"]("Procesado", 'CALCULAR');
    } else if (payment.toLowerCase() == 'quincenal') {
      $('#num_fee').val(time * 2);
      let num_cuotas = $('#num_fee').val();
      let I = Math.pow(1 + i,1/24) - 1;
      let cuota = monto * (I / (1 - Math.pow(1 + I, -num_cuotas))*1);
      let monto_total =  cuota*num_cuotas;
      
      $('#fee_amount').val(cuota.toFixed(2));
      $('#valor_interes').val(monto_total.toFixed(2)-monto);
      $('#monto_total').val(monto_total.toFixed(2));
      loadCuotasTimelime();
      toastr["success"]("Procesado", 'CALCULAR');
    } else if (payment.toLowerCase() == 'semanal') {
      $('#num_fee').val(time * 4);
      let num_cuotas = $('#num_fee').val();
      let I = Math.pow(1 + i,1/52) - 1;
      
      let cuota = monto * (I / (1 - Math.pow(1 + I, -num_cuotas))*1);
      let monto_total =  cuota*num_cuotas;
      $('#fee_amount').val(cuota.toFixed(2));
      $('#valor_interes').val(monto_total.toFixed(2)-monto);
      $('#monto_total').val(monto_total.toFixed(2));
      loadCuotasTimelime();
      toastr["success"]("Procesado", 'CALCULAR');
    } else if (payment.toLowerCase() == 'diario') {
      let tipo_p= $('#type_of_loan').val();
      
      if(tipo_p=='prendario'){
        $('#num_fee').val(time * 24);
      }else{
        $('#num_fee').val(time * 30);
      }
      
      
      let num_cuotas = $('#num_fee').val();
      let I = Math.pow(1 + i,1/360) - 1;
      let cuota = monto * (I / (1 - Math.pow(1 + I, -num_cuotas))*1);
      let monto_total =  cuota*num_cuotas;
      $('#fee_amount').val(cuota.toFixed(2));
      $('#valor_interes').val(monto_total.toFixed(2)-monto);
      $('#monto_total').val(monto_total.toFixed(2));
      loadCuotasTimelime();
      toastr["success"]("Procesado", 'CALCULAR');
    } else if (payment.toLowerCase() == 'semestral') {
      $('#num_fee').val(time * 0.5);
      let num_cuotas = $('#num_fee').val();
      let I = Math.pow(1 + i,1/2) - 1;
     
      let cuota = monto * (I / (1 - Math.pow(1 + I, -num_cuotas))*1);
      let monto_total =  cuota*num_cuotas;
      $('#fee_amount').val(cuota.toFixed(2));
      $('#valor_interes').val(monto_total.toFixed(2)-monto);
      $('#monto_total').val(monto_total.toFixed(2));
      loadCuotasTimelime();
      toastr["success"]("Procesado", 'CALCULAR');
    } else {
      $('#num_fee').val(0);
    }
  });

  $("#loan_form").submit(function () {
    if ($("#customer").val() == "") {
      alert("Buscar un cliente");
      return false;
    }
  });
})

function loadGuarantorsOptions() {
  guarantorsItems = document.getElementById('guarantors');
  while (guarantorsItems.firstChild) {
    guarantorsItems.removeChild(guarantorsItems.firstChild);
  };
  user_name = document.getElementById('user_name');
  user_name.value = '';
  id = document.getElementById('customer_id').value;
  if (id != 0) {
    x = customerList.find(x => x.id == id);
    user_name.value = x.user_name;
    customerList.forEach(element => {
      if (x.user_id == element.user_id && element.id != id) {
        let option = "<option value='" + element.id + "'>" + element.ci + " | " + element.fullname + "</option>";
        guarantorsItems.insertAdjacentHTML("beforeend", option);
      }
    });
  }
}

function getCashRegisters(coin_id) {
  if (selectCoin.value != '') {
    fetch(`${base_url}admin/loans/ajax_get_cash_registers/${coin_id}`)
      .then(response => response.json()
        .then(json => {
          cashRegisters = json;
          while (cashRegisterId.firstChild) {
            cashRegisterId.removeChild(cashRegisterId.firstChild);
          };
          json.forEach(element => {
            option = `<option value="${element.id}">${element.name + " | Saldo: " + element.total_amount + " " + element.short_name + ""}</option>`;
            cashRegisterId.insertAdjacentHTML("beforeend", option);
          });
        }));
  } else {
    console.log('Seleccionar un usuario y una moneda');
  }
}


selectCoin.addEventListener('change', (event) => {
  getCashRegisters(selectCoin.value);
});

cashRegisterUpdate.addEventListener('click', event => {
  getCashRegisters(selectCoin.value);
});



function loanConfirmation() {
  cashRegister = cashRegisters.find(element => element.id == cashRegisterId.value);
  if (cashRegister != null) {
    if (Number.parseFloat(creditAmount.value) > Number.parseFloat(cashRegister.total_amount)) {
      alert(`La caja '${cashRegister.name} | ${cashRegister.total_amount + ' ' + cashRegister.short_name}' no contiene el monto suficiente para realizar está acción`);
    } else {
      return confirm(`Se procesará el préstamo de ${creditAmount.value + " " + coinId.options[coinId.selectedIndex].text}\n¿Quieres continuar?`);
    }
  } else {
    alert(`Selecciona una caja`);
    return false;
  }
}


function autoLoad() {
  if (selectCoin.value != '')
    getCashRegisters(selectCoin.value);
}
autoLoad();


async function loadCuotasTimelime() {
  num_fee = document.getElementById('num_fee').value;
  payment_m = document.getElementById('payment_m').value;
  fee_amount = document.getElementById('fee_amount').value;
  tipo_c= document.getElementById('type_of_loan').value;
  tbody = document.getElementById('tbody');
  date = document.getElementById('date').value;
  if (num_fee != null && payment_m != null && fee_amount != null) {
    resQuery = await fetch(`${base_url}admin/loans/get_timeline/${num_fee}/${payment_m}/${fee_amount}/${date}/${tipo_c}`);
    const list = await resQuery.json();
    content = '';
    list.forEach(item => {
      content += `<tr>
        <th>${item.num_quota}</th>
        <th>${item.fee_amount}</th>
        <th>${item.date}</th>
      </tr>`;
    });
    tbody.innerHTML = content;
  }
}