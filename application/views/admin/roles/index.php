<div class="card shadow mb-4">
  <div class="card-header d-flex align-items-center justify-content-between py-3">
    <h6 class="m-0 font-weight-bold text-primary">Roles</h6>
    <div>
      <?php if($ROLE_CREATE) : ?>
      <a class="d-sm-inline-block btn btn-sm btn-primary shadow-sm" href="<?= site_url('admin/roles/create'); ?>"><i class="fas fa-plus-circle fa-sm"></i> Crear rol</a>
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
      <table class="table table-bordered" id="roles" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th>ID</th>
            <th class="col-10">Rol</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
  const ROLE_READ = <?=($ROLE_READ)?'true':'false'?>;
  const ROLE_UPDATE = <?=($ROLE_UPDATE)?'true':'false'?>;
  const ROLE_DELETE = <?=($ROLE_DELETE)?'true':'false'?>;
</script>
<script src="<?= site_url() . 'assets/js/roles/index.js' ?>"></script>