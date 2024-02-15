<link href="<?php echo site_url() ?>assets/css/image-style.css" rel="stylesheet">

<!-- The Modal -->
<div id="modal" class="modalx">
    <span class="closex" id="closex">&times;</span>
    <img class="modal-contentx" id="img">
    <div id="caption"></div>
</div>

<div class="card shadow mb-4">
    <div class="card-header d-flex align-items-center justify-content-between py-3">
        <h6 class="m-0 font-weight-bold text-primary">Lista Negra</h6>
        <div class="btn-group">
            <div class="btn-group">
                <a class="btn btn-secondary btn-sm shadow-sm" href="<?= site_url("admin/legalprocesses/edit/$legal_process->id")?>">Editar</a>
                <?php $deleteUrl = site_url("admin/legalprocesses/delete/$legal_process->id") ?>
                <button class="btn btn-danger btn-sm shadow-sm" onclick="deleteConfirmation('CONFIRMACIÓN', '¿Realmente desea eliminar de la lista?', '<?= $deleteUrl ?>')">Eliminar</a>
            </div>
        </div>
    </div>

    <div class="card-body">

        <?php if (validation_errors()) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= validation_errors('<li>', '</li>') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif ?>
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
        <div class="form-row">
            <div class="form-group col-12">
                <label class="small mb-1" for="customer_id">Cliente</label>
                <p class="form-control"><?= $legal_process->customer ?></p>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-12">
                <label class="small mb-1" for="start_date">Fecha de inicio</label>
                <p class="form-control"><?= $legal_process->start_date ?></p>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-12">
                <label class="small mb-1" for="observations">Observaciones</label>
                <textarea class="form-control" rows="5" readonly><?= $legal_process->observations ?></textarea>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-12">
                <label class="small mb-1" for="observations">Préstamos con cuotas vencidas</label>
                <div>
                    <?php
                    if ($loans) :
                        foreach ($loans as $loan) : ?>
                            <a href="<?=site_url("admin/loans/view/$loan->id")?>" class="btn btn-secondary btn-sm" data-toggle="ajax-modal" title="Ver detalles"><i class="fas fa-info-circle">  <?= "Préstamo de: $loan->credit_amount $loan->short_name" ?></i></a>
                    <?php endforeach;
                    endif ?>
                </div>

            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-12">
                <div class="justify-content-between py-3">
                    <a href="<?= site_url("admin/legalprocesses/create_file/$legal_process->id") ?>" class="btn btn-primary btn-icon-split btn-sm">
                        <span class="icon text-white-50">
                            <i class="fas fa-flag"></i>
                        </span>
                        <span class="text">Imagen</span>
                    </a>
                </div>
                <div class="form-row text-center row">
                    <?php $i = 1;
                    foreach ($legal_process->files as $file) : ?>
                    <div class="col-sm-12 col-md-4 col-lg-3 alert">
                        <div class=" card">
                            <img id="img<?= $i ?>" src="<?= site_url('uploads/' . $file->name) ?>" class="card-img-top img-select" alt="<?= $file->name ?>">
                            <div class="card-body">
                                <button onClick="return deleteConfirmation('CONFIRMACIÓN', 'Se eliminará esta imagen', '<?= site_url("admin/legalprocesses/file_remover/$legal_process->id/$file->id") ?>')" class="btn btn-danger btn-sm">
                                    Eliminar
                                </button>
                            </div>
                        </div>
                        </div>
                    <?php $i++;
                    endforeach ?>
                </div>
                <script>
                    i = <?= $i - 1 ?>;
                </script>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true"></div>

<script src="<?= site_url('assets/js/legal-processes/view.js') ?>"></script>