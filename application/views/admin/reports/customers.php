<div class="card shadow mb-4">
  <div class="card-header d-flex align-items-center justify-content-between py-3">
    <h6 class="m-0 font-weight-bold text-primary">Reporte general por clientes</h6>
    <div class="row">
    <?php if (isset($users)) :?>
        <select class="custom-select-sm btn-outline-secondary" id="user_id">
          <option value="all" selected>TODOS</option>
          <?php foreach ($users as $user) : ?>
            <option value="<?=$user->id?>" <?=($this->session->userdata('user_id') == $user->id)?'selected':''?>>
            <?="$user->academic_degree $user->first_name $user->last_name"?>
            </option>
          <?php endforeach ?>
        </select>
      <?php endif ?>
    </div>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered" id="customerGeneralReportTable" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th>ID</th>
            <th>DNI</th>
            <th class="col-7">Cliente</th>
            <th>Acciones</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>

<script src="<?= site_url() . 'assets/js/reports/customer-general-report.js' ?>"></script>