<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div><?= empty($role->id) ? 'Nuevo rol' : 'Editar rol'; ?></div>
    </div>
    <div class="card-body">
        <?php if (validation_errors()) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo validation_errors('<li>', '</li>'); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="false">&times;</span>
                </button>
            </div>
        <?php } ?>
        <?= form_open($post??'') ?>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label class="small mb-1" for="name">Nombre del rol</label>
                    <input class="form-control" type="text" id="name" name="name" value="<?= $role->name?? '' ?>" minlength="1" maxlength="30">
                </div>
                <div class="form-group col-md-6">
                    <label class="small mb-1" for="description">Descripción</label>
                    <input class="form-control" type="text" id="description" name="description" value="<?= $role->description?? '' ?>" maxlength="250">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-12 col-md-12 table-responsive">
                    <table class="table table-bordered table-striped table-sm table-hover" id="permissions" width="100%" cellspacing="0">
                        <?php $colums = 4; ?>
                        <thead>
                            <tr>
                                <th colspan="<?= $colums ?>">Permisos</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($permissions) :
                                for ($i = 0; $i < sizeof($permissions); $i++) :
                            ?>
                                    <?php
                                    for ($j = 0; $j < $colums; $j++) :
                                        if ($i + $j < sizeof($permissions)) :
                                    ?>
                                            <td class="col-<? $colums / 12 ?> col-lg-<? $colums / 12 ?>">
                                                <div class="form-check">
                                                    <?php $isSelected = isset($permissions[$i+$j]->is_selected)?(($permissions[$i+$j]->is_selected)?'checked':''):''?>
                                                    <input class="form-check-input" type="checkbox" id="id<?=$permissions[$i+$j]->id?>" value="<?= $permissions[$i+$j]->id ?>" name="permission_ids[]"<?=$isSelected?>>
                                                    <label class="form-check-label small" for="id<?=$permissions[$i+$j]->id?>" title="<?= $permissions[$i+$j]->name ?>">
                                                      <?= $permissions[$i+$j]->name ?>
                                                    </label>
                                                    <div><small><?=$permissions[$i+$j]->description?></small></div>
                                                </div>
                                            </td>
                                    <?php
                                        endif;
                                    endfor;
                                    $i+=$colums-1;
                                    ?>
                                    </tr>
                            <?php
                                endfor;
                            endif
                            ?>
                        </tbody>

                    </table>
                </div>
            </div>
            <?php $var = (isset($path))?$path:''?>
            <div class="float-right">
                <a class="btn btn-secondary px-3" type="submit" href="<?= site_url('admin/roles' . $var) ?>">Cancelar</a>
                <button class="btn btn-primary px-3" type="submit">Guardar</button>
            </div>
        <?= form_close() ?>
    </div>
</div>