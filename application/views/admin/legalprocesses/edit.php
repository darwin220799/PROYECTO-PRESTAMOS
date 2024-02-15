<div class="card shadow mb-4">
    <div class="card-header py-3">Editar cliente en lista</div>
    <div class="card-body">
        <?php if (validation_errors()) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= validation_errors('<li>', '</li>') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif ?>
        <? echo $error; ?>
        <?= form_open_multipart() ?>
        <div class="form-row">
            <div class="form-group col-12">
                <label class="small mb-1">Cliente</label>
                <div class="input-group">
                    <input class="form-control" type="text" name="customer_id" value="<?= $legal_process->id??'' ?>" hidden>
                    <input class="form-control" type="text" value="<?= $legal_process->customer??'' ?>" readonly>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-12">
                <label class="small mb-1" for="start_date">Inicio de Registro</label>
                <input type="date" class="form-control" rows="10" name="start_date" value="<?= $legal_process->start_date ?? Date('Y-m-d') ?>" />
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-12">
                <label class="small mb-1" for="observations">Observaciones</label>
                <textarea class="form-control" rows="10" name="observations"><?= $legal_process->observations ?? '' ?></textarea>
            </div>
        </div>

        <div class="float-right">
            <a href="<?= site_url("admin/legalprocesses/view/$legal_process->id") ?>" class="btn btn-dark">Cancelar</a>
            <button class="btn btn-primary" type="submit">Actualizar</button>
        </div>
        <?= form_close() ?>
    </div>
</div>

<script src="<?= site_url('assets/js/legal-processes/edit.js') ?>"></script>