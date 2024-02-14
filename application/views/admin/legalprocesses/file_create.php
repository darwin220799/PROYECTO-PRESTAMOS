<div class="card shadow mb-4">
    <div class="card-header py-3">Agregar imagen</div>
    <div class="card-body">
        <?php if ($this->session->flashdata('msg_error')) : ?>
            <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                <?= $this->session->flashdata('msg_error') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif ?>
        <div class="form-group col-12">
            <?= form_open_multipart("admin/legalprocesses/add_file/$legal_process_id") ?>
            <div class="form-row">
                <div class="form-group col-12">
                    <div>
                        <input type="file" class="custom-file-input" name="image" id="image" lang="es" class="col-10">
                        <label class="custom-file-label" class="col-8">Elige una imgen (max 5 mb)</label>
                    </div>
                </div>
            </div>
            <div class="float-right">
                <a href="<?= site_url("admin/legalprocesses/view/$legal_process_id") ?>" class="btn btn-dark">Cancelar</a>
                <button class="btn btn-primary" type="submit">Agregar</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<script src="<?= site_url('assets/js/legal-processes/create-file.js') ?>"></script>