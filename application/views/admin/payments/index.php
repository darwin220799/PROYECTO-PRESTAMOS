<div class="card shadow mb-4">
  <div class="card-header d-flex align-items-center justify-content-between py-3">
    <h6 class="m-0 font-weight-bold text-primary">Cuotas pagadas</h6>
    <div>
      <?php if (isset($users)) : ?>
        <select class="custom-select-sm btn-outline-secondary" id="user_id">
          <option value="all" selected>TODOS</option>
          <?php foreach ($users as $user) : ?>
            <option value="<?= $user->id ?>" <?= ($this->session->userdata('user_id') == $user->id) ? 'selected' : '' ?>>
              <?= "$user->academic_degree $user->first_name $user->last_name" ?>
            </option>
          <?php endforeach ?>
        </select>
      <?php endif ?>
      <?php if ($LOAN_READ || $AUTHOR_LOAN_READ || $LOAN_ITEM_READ || $AUTHOR_LOAN_ITEM_READ) : ?>
       <a id="week" href="#" class="btn btn-sm btn-primary shadow-sm" data-toggle="ajax-modal"><i class="fas fa-eye fa-sm"></i> Cobrables & Morosos</a>
      <?php endif ?>
      <?php if ($PAYMENT_CREATE || $AUTHOR_PAYMENT_CREATE) : ?>
        <a class="d-sm-inline-block btn btn-sm btn-primary shadow-sm" href="<?php echo site_url('admin/payments/edit'); ?>"><i class="fas fa-plus-circle fa-sm"></i> Realizar Pago</a>
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
      <table class="table table-bordered" id="loanItemsPayedTable" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th>DNI</th>
            <th class="col-5">Cliente</th>
            <!-- <th>Prest. ID</th> -->
            <th>N° Cuota</th>
            <th>M. Cancelado</th>
            <th class="col-2">Fecha Pago</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
<?php if ($this->session->flashdata('document_payment_id')) :
  $url = site_url('admin/payments/document_payment/' . $this->session->flashdata('document_payment_id'));
  echo "<script>
  setTimeout(function(){
    window.open('$url', '_blank');
    window.focus();
  }, 4000);
  </script>";
endif ?>

<div class="modal fade" id="myModal" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true"></div>

<script src="<?= site_url('assets/js/payments/index.js') ?>"></script>