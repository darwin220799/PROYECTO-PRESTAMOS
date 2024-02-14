<div class="card shadow mb-4">
  <div class="card-header d-flex align-items-center justify-content-between py-3">
    <h6 class="m-0 font-weight-bold text-primary">Resumen general de préstamos</h6>
    <div>
      <?php
      if (isset($users)) : if (sizeof($users) > 0) :
          echo "<select class='custom-select-sm btn-outline-secondary col-md-12' id='user_id'>";
          echo "<option value='all' $selected>TODOS</option>";
          foreach ($users as $user) :
            $user_name = "$user->academic_degree $user->first_name $user->last_name";
            echo "<option value='$user->id' $selected>$user_name</option>";
          endforeach;
          echo "</select>";
        endif;
      endif;
      ?>
    </div>

  </div>

  <div class="card-body">

    <div class="form-row col-md-12">

      <div class="form-group col-md-3">
        <label class="small mb-1" for="exampleFormControlSelect2">Fecha inicio</label>
        <input type="date" id="start_d" class="form-control">
      </div>

      <div class="form-group col-md-3">
        <label class="small mb-1" for="exampleFormControlSelect2">Fecha final</label>
        <input type="date" id="end_d" class="form-control">
      </div>

      <div class="form-group col-md-3">
        <label class="small mb-1" for="exampleFormControlSelect2">Tipo de moneda</label>
        <select class="form-control" id="coin_type" name="coin_type">
          <?php foreach ($coins as $c) : ?>
            <option <?php if (strtolower($c->name) == 'bolivianos') echo 'selected' ?> value="<?php echo $c->id ?>" data-symbol="<?php echo $c->short_name ?>"><?php echo $c->name . ' (' . strtoupper($c->short_name) . ')' ?></option>
          <?php endforeach ?>
        </select>
      </div>
      <div class="form-group d-flex justify-content-end align-items-end col-md-3">
        <a class="btn btn-primary shadow-sm form-control" onclick="loadGeneralReport()">Consultar</a>
      </div>
    </div>

    <hr>
    
    <div class="alert alert-secondary small" id="alert_message" style="display:none;">
        <h6 id="message" class="alert-heading bold"></h6>
        <p id=range_date></p>
        <hr>
        <p class="mb-0 small">Datos cargados en tabla</p>
    </div>

    <div class="table-responsive" id="imp1">
      <table class="table" width="100%" cellspacing="0">
        <thead class="thead-dark">
          <tr>
            <th>Descripción</th>
            <th class="text-right">Cantidad</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Total crédito</td>
            <td class="text-right" id="cr">0</td>
          </tr>
          <tr>
            <td>Total crédito con interés</td>
            <td class="text-right" id="cr_interest">0</td>
          </tr>
          <tr hidden>
            <td>Total crédito cancelado con interés</td>
            <td class="text-right" id="cr_interestPaid">0</td>
          </tr>
          <tr hidden>
            <td>Total crédito por cobrar con interés</td>
            <td class="text-right" id="cr_interestPay">0</td>
          </tr>
          <tr hidden>
            <td>Total en cuotas programadas</td>
            <td class="text-right" id="payable">0</td>
          </tr>
          <tr>
            <td>Total por cobros realizados</td>
            <td class="text-right" id="total_payed">0</td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="form-row col-md-12">
      <div class="col-md-12">
        <a class="btn btn-primary shadow-sm col-md-3 float-right" href="#" onclick="printElementById('imp1', report_title, date_range);"><i class="fas fa-print fa-sm"></i> Imprimir tabla</a>
      </div>
    </div>
  </div>
</div>

<script src="<?= site_url() ?>assets/js/reports/index.js"></script>