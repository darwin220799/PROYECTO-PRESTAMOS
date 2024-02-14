<div class="card shadow mb-4">
    <div class="card-header py-3"><?= empty($user->id) ? 'Nuevo proceso' : 'Editar proceso'; ?></div>
    <div class="card-body">
        <?php if (validation_errors()) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= validation_errors('<li>', '</li>') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif ?>
        <?echo $error;?>
        <?= form_open_multipart();?>
            <div class="form-row">
                <div class="form-group col-12">
                    <label class="small mb-1" for="customer_id">Cliente</label>
                    <div class="input-group">
                        <select class="form-control form-select" id="customer_id" name="customer_id">
                            <option value="0">...</option>
                            <?php foreach ($customers as $customer) : ?>
                                <option value="<?= $customer->id ?>">
                                    <?= $customer->ci . " | $customer->first_name $customer->last_name | $customer->num_fee cuotas vencidas" ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <span class="small mb-1"><small id="customerRectriction">Sólo aparecen en la lista los clientes con cuatas vencidas.</small></span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-12">
                    <label class="small mb-1" for="start_date">Fecha de inicio</label>
                    <input type="date" class="form-control" rows="10" name="start_date" value="<?= $form_state->start_date ?? Date('Y-m-d') ?>" />
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-12">
                    <label class="small mb-1" for="observations">Observaciones</label>
                    <textarea class="form-control" rows="10" name="observations"><?= $form_state->observations ?? '' ?></textarea>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-12">
                    <label class="small mb-1">Seleccionar imágenes (Máx. 5 Mb)</label>

                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="img1" id="img1" lang="es">
                        <label class="custom-file-label" for="img1">Seleccionar archivo</label>
                    </div>

                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="img2" id="img2" lang="es">
                        <label class="custom-file-label" for="img2">Seleccionar archivo</label>
                    </div>

                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="img3" id="img3" lang="es">
                        <label class="custom-file-label" for="img3">Seleccionar archivo</label>
                    </div>

                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="img4" id="img4" lang="es">
                        <label class="custom-file-label" for="img4">Seleccionar archivo</label>
                    </div>

                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="img5" id="img5" lang="es">
                        <label class="custom-file-label" for="img5">Seleccionar archivo</label>
                    </div>
                </div>
            </div>
            <div class="float-right">
                <?php $var = (isset($path)) ? $path : '' ?>
                <a href="<?= site_url('admin/legalprocesses' . $var) ?>" class="btn btn-dark">Cancelar</a>
                <button class="btn btn-primary" type="submit">Guardar</button>
            </div>
        <?= form_close() ?>
    </div>
</div>

<script src="<?= site_url('assets/js/legal-processes/edit.js') ?>"></script>