<div class="card shadow mb-4">

  <div class="card-header d-flex align-items-center justify-content-between py-3">
    <h6 class="m-0 font-weight-bold text-primary">Préstamos</h6>
    <div>
      <?php
      if (isset($users)) : ?>
        <select class="custom-select-sm btn-outline-secondary" id="user_id">
          <option value="all" selected>TODOS</option>
          <?php foreach ($users as $user) : ?>
            <option value="<?= $user->id ?>" <?=($this->session->userdata('user_id') == $user->id)?'selected':''?>>
              <?= "$user->academic_degree $user->first_name $user->last_name" ?>
            </option>
          <?php endforeach ?>
        </select>
      <?php endif ?>
      <?php if ($LOAN_CREATE || $AUTHOR_LOAN_CREATE) : ?>
        <a class="d-sm-inline-block btn btn-sm btn-primary shadow-sm" href="<?php echo site_url('admin/loans/edit'); ?>"><i class="fas fa-plus-circle fa-sm"></i> Nuevo préstamo</a>
      <?php endif ?>
    </div>
  </div>
  <div class="card-body">
    <?php if ($this->session->flashdata('msg')) : ?>
      <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
        <?= $this->session->flashdata('msg') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
          <?php $this->session->unset_userdata('msg'); ?>
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
      <table class="table table-bordered" id="loansTable" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th>N° Prest.</th>
            <th class="col-5">Cliente</th>
            <th>Monto credito</th>
            <th>Monto interes</th>
            <th>Monto total</th>
            <th>T. moneda</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>

<div class="modal fade" id="myModal" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true"></div>

<script src="<?= site_url('assets/js/loans/index.js') ?>"></script>