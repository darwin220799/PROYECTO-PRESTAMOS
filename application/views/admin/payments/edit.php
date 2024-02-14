<div class="card shadow mb-4">
  <div class="card-header py-3">Pagar cuotas del préstamo </div>
  <div class="card-body">

    <?php if ($this->session->flashdata('msg_error')) : ?>
      <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
        <?= $this->session->flashdata('msg_error') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    <?php endif ?>

    <?php echo form_open('admin/payments/save_payment'); ?>
    <div class="form-row">
      <div class="form-group col-12 col-md-12">
        <label class="small mb-1" for="exampleFormControlSelect2">Cliente</label>
        <div class="input-group">
          <?php if (isset($customers)) : ?>
            <select id="search" class="col-12 col-md-12" name="customer_id" onChange="autoLoad()">
              <option value="0" selected="selected" class="input-group">...</option>
              <?php foreach ($customers as $customer) : ?>
                <?php $selected = ($customer->id == $default_selected_customer_id) ? 'selected' : ''; ?>
                <option value="<?php echo $customer->id ?>" <?= $selected ?>>
                  <?php echo  $customer->ci . " | " . $customer->fullname ?>
                </option>
              <?php endforeach ?>
            </select>
          <?php endif; ?>
        </div>
        <span class="small mb-1"><small>(Solo aparecen en la lista los clientes que tienen cuentas pendientes)</small></span>
      </div>
    </div>

    <div class="form-row" id="guarantors_container" style="display: none">
      <div class="form-group col-12 col-md-16">
        <label class="small mb-1" for="exampleFormControlSelect2">Garantes</label>
        <div class="input-group" id="guarantors_contend">

        </div>
        <span class="small mb-1"><small>Garantes del préstamo</small></span>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group col-12 col-md-16">
        <label class="small mb-1" for="exampleFormControlSelect2">Asesor</label>
        <input class="form-control" id="adviser" type="text" disabled>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group col-12 col-md-4">
        <input type="hidden" name="loan_id" id="loan_id">
        <label class="small mb-1" for="inputUsername">Monto préstado</label>
        <input class="form-control" id="credit_amount" type="text" readonly>
      </div>
      <div class="form-group col-12 col-md-4">
        <label class="small mb-1" for="inputUsername">Forma de pago</label>
        <input class="form-control" id="payment_m" type="text" disabled>
      </div>
      <div class="form-group col-12 col-md-4">
        <label class="small mb-1" for="exampleFormControlTextarea1">Tipo moneda</label>
        <input class="form-control" id="coin" name="coin" type="text" readonly>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group col-12 col-md-12">
        <table class="table " id="quotas" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th class="col-md-1">Sel</th>
              <th class="col-md-1 text-center" id='ncuota'>N° Cuota</th>
              <th class="col-md-3 text-center">Fecha</th>
              <th class="col-md-1 text-center">Cuota</th>
              <th class="col-md-1 text-center">Pagado</th>
              <th class="col-md-1 text-center">Pagable</th>
              <th class="col-md-2 text-center">Recargo</th>
              <th class="col-md-2 text-center">Pago</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
    <div class="row">
      <div class="form-group col-12 col-md-12 text-center">
        <div class="form-row">
          <div class="form-group col-6 col-md-6 col-sm-12">
            <label id="cash_register_update" class="small mb-1" id="cash_register_update">Caja de destino</label>
            <select class="form-control" id="cash_register_id" name="cash_register_id"></select>
          </div>
          <div class="form-group col-6 col-md-6 col-sm-12">
            <label class="small mb-1" for="exampleFormControlTextarea1">Monto total a pagar</label>
            <div class="input-group mb-3">
              <input type="number" step=".01" class="form-control text-center" style="font-weight: bold; font-size: 1.2rem;" id="total_amount" disabled>
              <button class="btn btn-outline-secondary" type="button" id="button-addon2" onclick="calculateTotal()"><i class="fas fa-calculator fa-sm"></i></button>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-6">
            <a href="<?php echo site_url('admin/payments/'); ?>" class="btn btn-dark btn-block">Cancelar</a>
          </div>
          <div class="col-6">
            <button class="btn btn-primary btn-block" id="register_loan" type="submit" onclick="return payConfirmation();" disabled>Registrar Pago</button>
          </div>
        </div>
      </div>
    </div>
    <?php echo form_close() ?>
  </div>
</div>


<script>
  $("#search").select2({
    tags: false
  });
</script>

<script src="<?= site_url() . 'assets/js/payments/edit.js' ?>"></script>