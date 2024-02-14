<div class="card shadow mb-4">
  <div class="card-header d-flex align-items-center justify-content-between py-3">
    <h6 class="m-0 font-weight-bold text-primary">Usuarios</h6>
    <div>
      <?php if($USER_CREATE) : ?>
      <a class="d-sm-inline-block btn btn-sm btn-primary shadow-sm" href="<?= site_url('admin/users/create'); ?>"><i class="fas fa-plus-circle fa-sm"></i> Crear usuario</a>
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
      <table class="table table-bordered" id="users" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th class="col-0">ID</th>
            <th class="col-3">Nombre completo</th>
            <th class="col-3">Roles</th>
            <th class="col-2">Email</th>
            <th class="col-0">Acciones</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
  const USER_READ = <?=($USER_READ)?'true':'false'?>;
  const USER_UPDATE = <?=($USER_UPDATE)?'true':'false'?>;
  const USER_DELETE = <?=($USER_DELETE)?'true':'false'?>;
</script>
<script src="<?= site_url() . 'assets/js/users/index.js' ?>"></script>