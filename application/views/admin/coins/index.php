<div class="card shadow mb-4">
  <div class="card-header d-flex align-items-center justify-content-between py-3">
    <h6 class="m-0 font-weight-bold text-primary">Monedas</h6>
    <a class="d-sm-inline-block btn btn-sm btn-primary shadow-sm" href="<?php echo site_url('admin/coins/edit'); ?>"><i class="fas fa-plus-circle fa-sm"></i> Nueva moneda</a>
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
      <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th>Nom. moneda</th>
            <th>Abreviatura</th>
            <th>Simbolo</th>
            <th>Descripción</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($coins)) : foreach ($coins as $coin) : ?>
              <tr>
                <td class="col-4"><?php echo $coin->name ?></td>
                <td class="col-1"><?php echo $coin->short_name ?></td>
                <td class="col-1"><?php echo $coin->symbol ?></td>
                <td class="col-4"><?php echo $coin->description ?></td>
                <td class="col-1">
                  <div class="container-fluid">
                    <div class="row col-sm-12">
                      <?php if ($COIN_UPDATE) : ?>
                        <a href="<?php echo site_url('admin/coins/edit/' . $coin->id); ?>" class="btn btn-warning btn-circle btn-sm"><i class="fas fa-edit fa-sm" title="Editar"></i></a>
                      <?php endif ?>
                    </div>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else : ?>
            <tr>
              <td colspan="5" class="text-center">No existen monedas para mostrar</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>