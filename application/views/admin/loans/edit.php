<div class="card shadow mb-4">
  <div class="card-header py-3">Crear préstamo </div>
  <div class="card-body">

    <?php if ($this->session->flashdata('msg_error')) : ?>
      <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
        <?= $this->session->flashdata('msg_error') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    <?php endif ?>

    <?php if (validation_errors()) { ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo validation_errors('<li>', '</li>'); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    <?php } ?>
    <?php echo form_open('admin/loans/edit', 'id="loan_form"'); ?>
    <div class="form-row">
      <div class="form-group col-12 col-md-12">
        <label class="small mb-1" for="customer_id">Cliente</label>
        <div class="input-group">
          <select class="form-control form-select" id="customer_id" name="customer_id" onchange="loadGuarantorsOptions()">
            <option value="0" selected="selected">...</option>
            <?php foreach ($customers as $customer) : ?>
              <?php if ($customer->loan_status == FALSE) : ?>
                <option value="<?php echo $customer->id ?>">
                  <?php
                  echo  $customer->ci . " | " . $customer->fullname ?>
                </option>
              <?php endif ?>
            <?php endforeach ?>
          </select>
        </div col-12 col-md-12>
        <span class="small mb-1"><small id="customerRectriction">Solo aparecen en la lista los clientes que no tienen cuentas pendientes.</small></span>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group col-12 col-md-12">
        <label class="small mb-1" for="guarantors">Garantes</label>
        <div class="input-group">
          <select id="guarantors" class="form-control form-select" name="guarantors[]" multiple="multiple">
          </select>
        </div>
        <span class="small mb-1"><small id="guarantorsRestriction">Máximo 9 garantes.</small></span>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group col-12 col-md-12">
        <label class="small mb-1">Asesor</label>
        <input class="form-control" id="user_name" type="text" >
      </div>
    </div>

<!-- AÑADIDO POR GUSTAVO 👇 -->
<div class="form-row">
  <div class="form-group col-6"> <!-- Cambio: col-6 -->
    <label class="small mb-1" for="type_of_loan">Tipo de préstamo</label>
    <select class="form-control" id="type_of_loan" name="type_of_loan">
      <option value="vehicular" selected="selected">Crédito vehicular</option>
      <option value="prendario">Crédito prendario</option>
    </select>
  </div>
  <div class="form-group col-6"> <!-- Cambio: col-6 -->
    <label class="small mb-1" for="object_description">Descripción del objeto</label>
    <input class="form-control" type="text" id="object_description" name="object_description">
  </div>
</div>

<!-- AÑADIDO POR GUSTAVO ☝ -->




    <div class="form-row">
      <div class="form-group col-12 col-md-4">
        <label class="small mb-1" for="credit_amount">Monto préstamo (capital)</label>
        <input class="form-control" id="credit_amount" type="number" name="credit_amount" step="none" min="1" value="<?= $credit_amount ?? '' ?>">
      </div>
      <div class="form-group col-12 col-md-4">
        <label class="small mb-1" for="in_amount">Tasa de interés %</label>
        <input class="form-control" type="number" id="in_amount" name="interest_amount">
        
      </div>
      <div class="form-group col-12 col-md-4">
        <label class="small mb-1" for="payment_m">Modalidad de pago</label>
        <select class="form-control" id="payment_m" name="payment_m">
          <option value="diario">Diario</option>
          <option value="semanal">Semanal</option>
          <option value="quincenal" selected="selected">Quincenal</option>
          <option value="mensual">Mensual</option>
          <option value="mensual">Semestral</option>
        </select>
      </div>
    </div>
    <div class="form-row">

      <div class="form-group col-12 col-md-4">
        <label class="small mb-1" for="time">Tiempo (meses)</label>
        <input class="form-control" min="1" id="time" type="number" name="time">
      </div>

      <div class="form-group col-12 col-md-4">
        <label class="small mb-1" for="num_fee">Nro cuotas</label>
        <input class="form-control" id="num_fee" type="number" name="num_fee" readonly="readonly">
      </div>

      <div class="form-group col-12 col-md-4">
        <label class="small mb-1" for="coin_id">Tipo moneda</label>
        <select class="form-control" id="coin_id" name="coin_id">
          <?php foreach ($coins as $coin) : ?>
            <option <?php if (strtolower($coin->name) == 'bolivianos') echo 'selected' ?> value="<?php echo $coin->id ?>"><?php echo $coin->short_name ?></option>
          <?php endforeach ?>
        </select>
      </div>
      <div class="form-group col-12 col-md-4">
        <label class="small mb-1" id="cash_register_update" for="cash_register_id">Caja de extracción</label>
        <select class="form-control" id="cash_register_id" name="cash_register_id">
        </select>
      </div>
      <div class="form-group col-12 col-md-4">
        <label class="small mb-1" for="date">Fecha emisión</label>
        <input class="form-control" id="date" type="date" name="date" value="<?= $date ?? '' ?>">
      </div>
      <div class="form-group col-12 col-md-4">
        <label class="small mb-1">Acción</label>
        <button class="btn btn-primary form-control" type="button" id="calcular">Calcular</button>
      </div>
    </div>
    
    <div class="form-row">
      <div class="form-group col-12 col-md-4">
        <label class="small mb-1">Importe de la cuenta</label>
        <input class="form-control" id="fee_amount" type="text" name="fee_amount" readonly>
      </div>
      <div class="form-group col-12 col-md-4">
        <label class="small mb-1">Interés</label>
        <input class="form-control" id="valor_interes" type="text" disabled>
      </div>
      <div class="form-group col-12 col-md-4">
        <label class="small mb-1">Monto total</label>
        <input class="form-control" id="monto_total" type="text" name="" disabled>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group col-12 col-md-12">
        <table class="table table-bordered" id="quotas" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th class="col-md-1 text-center">Nro </th>
              <th class="col-md-5 text-center">Monto</th>
              <th class="col-md-6 text-center">Fecha de pago</th>
            </tr>
          </thead>
          <tbody id="tbody">
          </tbody>
        </table>
      </div>
    </div>
    <div class="float-right">
      <a href="<?php echo site_url('admin/loans/'); ?>" class="btn btn-dark">Cancelar</a>
      <button class="btn btn-primary" id="register_loan" type="submit" disabled onclick="return loanConfirmation()">Registrar Préstamo</button>
    </div>
    <?php echo form_close() ?>

  </div>
</div>
<script>
  customerList = <?php echo json_encode($customers); ?>
</script>
<script>
  $("#customer_id").select2({
    tags: false
  });
</script>

<script>
  $('#guarantors').select2({
    tags: false,
    tokenSeparators: [' | '],
    maximumSelectionLength: 9
  })
</script>

<script src="<?= site_url() . 'assets/js/loans/edit.js' ?>"></script>