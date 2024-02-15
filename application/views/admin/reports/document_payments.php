<div class="card shadow mb-4">
  <div class="card-header d-flex align-items-center justify-content-between py-3">
    <h6 class="m-0 font-weight-bold text-primary">Pagos Realizados</h6>
    <div>
      <?php
      if (isset($users)) : if (sizeof($users) > 0) :
          echo "<select class='custom-select-sm btn-outline-secondary' id='userSelector'>";
          echo "<option value='all'>TODOS</option>";
          foreach ($users as $user) :
            $user_name = "$user->academic_degree $user->first_name $user->last_name";
            echo "<option value='$user->id'>$user_name</option>";
          endforeach;
          echo "</select>";
        endif;
      endif;
      ?>
    </div>
  </div>
  <div class="card-body">
    <?php if ($this->session->flashdata('msg')) : ?>
      <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
        <?= $this->session->flashdata('msg') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    <?php endif ?>

    <?php if ($this->session->flashdata('msg_error')) : ?>
      <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
        <?= $this->session->flashdata('msg_error') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    <?php endif ?>

    <div class="table-responsive">
      <table class="table table-bordered" id="document-registers" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th class="col-1">ID</th>
            <th class="col-3">Cliente</th>
            <th class="col-3">Usuario</th>
            <th class="col-1">Moneda</th>
            <th class="col-2">Monto</th>
            <th class="col-2">Fecha</th>
            <th class="col-1">Acciones</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="<?= site_url() . 'assets/js/reports/document-payments.js' ?>"></script>