<div class="card shadow mb-4">
  <div class="card-header d-flex align-items-center justify-content-between py-3">
    <h6 class="m-0 font-weight-bold text-primary">Lista Negra</h6>
    <?php if($LEGAL_PROCESS_CREATE) : ?>
    <a class="d-sm-inline-block btn btn-sm btn-primary shadow-sm" href="<?= site_url('admin/legalprocesses/create'); ?>"><i class="fas fa-plus-circle fa-sm"></i> Añadir a Lista</a>
    <?php endif ?>
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
      <table class="table table-bordered" id="legal-precesses" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th class="col-1">ID</th>
            <th class="col-5">Cliente</th>
            <th class="col-3">Inicio de Registro</th>
            <th class="col-2">Acciones</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
<script src="<?=site_url('assets/js/legal-processes/index.js')?>"></script>
<script> 
  const LEGAL_PROCESS_READ = <?=($LEGAL_PROCESS_READ)?'true':'false'?>;
  const LEGAL_PROCESS_DELETE = <?=($LEGAL_PROCESS_DELETE)?'true':'false'?>;
</script>