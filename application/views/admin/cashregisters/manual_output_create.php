<div class="card">

    <div class="card-header">
        <div>Nueva salida manual de <?= $cash_register_name ?? '' ?></div>
    </div>
    <div class="card-body">
        <?php if ($this->session->flashdata('msg_error')) : ?>
            <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                <?= $this->session->flashdata('msg_error') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif ?>
        <?php if (validation_errors()) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo validation_errors('<li>', '</li>'); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="false">&times;</span>
                </button>
            </div>
        <?php } ?>

        <?= form_open() ?>
        <div class="container py-3">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label class="small mb-1" for="amount">Monto en <?= $coin_short_name ?? '' ?></label>
                    <input class="form-control" type="id" id="amount" name="amount" value="<?= $amount ?>">
                </div>
                <div class="form-group col-md-6">
                    <label class="small mb-1" for="description">Descripción</label>
                    <input class="form-control" type="text" id="description" name="description" maxlength="200" value="<?= $description ?>">
                </div>
            </div>
            <div class="float-right">
                <a class="btn btn-secondary pull-xs-right" type="submit" href="<?= site_url('admin/cashregisters/view/' . $cash_register_id) ?>">Cancelar</a>
                <button class="btn btn-primary pull-xs-right" type="submit">Agregar</button>
            </div>
        </div>
        <?= form_close() ?>
    </div>
</div>