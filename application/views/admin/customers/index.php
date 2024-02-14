<div class="card shadow mb-4">
  <div class="card-header d-flex align-items-center justify-content-between py-3">
    <h6 class="m-0 font-weight-bold text-primary">Clientes</h6>
    <div>
      <?php
      if (isset($users)) :?>
        <select class="custom-select-sm btn-outline-secondary" id="user_id">
          <option value="all" selected>TODOS</option>
          <?php foreach ($users as $user) : ?>
            <option value="<?=$user->id?>" <?=($this->session->userdata('user_id') == $user->id)?'selected':''?>>
            <?="$user->academic_degree $user->first_name $user->last_name"?>
            </option>
          <?php endforeach ?>
        </select>
      <?php endif ?>
      <?php if ($CUSTOMER_CREATE || $AUTHOR_CUSTOMER_CREATE) : ?>
        <a class="d-sm-inline-block btn btn-sm btn-primary shadow-sm" href="<?php echo site_url('admin/customers/edit'); ?>"><i class="fas fa-plus-circle fa-sm"></i> Nuevo cliente</a>
      <?php endif ?>
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
      <table class="table table-bordered" id="customersTable" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th class="col-0">DNI</th>
            <th class="col-0">Nombre completo</th>
            <th class="col-0">Celular</th>
            <th class="col-0">Empresa</th>
            <th class="col-0">Estado</th>
            <th class="col-0">Acciones</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
<script >
  const CUSTOMER_UPDATE = <?=$CUSTOMER_UPDATE?'true':'false';?>;
  const CUSTOMER_DELETE = <?=$CUSTOMER_DELETE?'true':'false';?>;
  const AUTHOR_CUSTOMER_UPDATE = <?=$AUTHOR_CUSTOMER_UPDATE?'true':'false';?>;
  const AUTHOR_CUSTOMER_DELETE = <?=$AUTHOR_CUSTOMER_DELETE?'true':'false';?>;
  const SESSION_USER_ID = <?=$this->session->userdata('user_id')?>;
</script>
<script src="<?= site_url('assets/js/customers/index.js')?>"></script>