<div class="card shadow mb-4">
  <div class="card-header d-flex align-items-center justify-content-between py-3">
    <h6 class="m-0 font-weight-bold text-primary">Reporte de préstamos por rango de fechas</h6>
    <div>
      <?php
      if (isset($users)) : if (sizeof($users) > 0) :
          echo '<select class="custom-select-sm btn-outline-secondary" id="user_selected">';
          echo "<option value=''>TODOS</option>";
          foreach ($users as $user) :
            echo "<option value='$user->id'>$user->academic_degree $user->first_name $user->last_name</option>";
          endforeach;
          echo "</select>";
        endif;
      elseif (isset($user_id)) :
        echo "<script>const USER_ID = $user_id;</script>";
      endif;
      ?>
    </div>
  </div>
  <div class="card-body">

    <div class="form-row">

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
        <select class="form-control" id="coin_type2" name="coin_type2">
          <?php foreach ($coins as $c) : ?>
            <option <?php if (strtolower($c->name) == 'bolivianos') echo 'selected' ?> value="<?php echo $c->id ?>"><?php echo $c->name . ' (' . strtoupper($c->short_name) . ')' ?></option>
          <?php endforeach ?>
        </select>
      </div>

      <div class="form-group col-md-3 d-flex justify-content-end align-items-end">
        <a class="btn btn-primary shadow-sm" href="javascript:reportPDF()"><i class="fas fa-print fa-sm"></i> Imprimir</a>
      </div>

    </div>

  </div>
</div>

<script src="<?php echo site_url() ?>assets/js/reports/dates.js"></script>