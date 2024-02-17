<div class="card shadow mb-4">
    <div class="card-header py-3"><?= empty($user->id) ? 'Nuevo usuario' : 'Editar usuario'; ?></div>
    <div class="card-body">
        <?php if (validation_errors()) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo validation_errors('<li>', '</li>'); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php } ?>

        <?= form_open($post ?? ''); ?>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label class="small mb-1" for="first_name">Ingresar Nombre</label>
                <input class="form-control" id="first_name" style="text-transform:uppercase" type="text" name="first_name" value="<?= $user->first_name ?>">
            </div>
            <div class="form-group col-md-4">
                <label class="small mb-1" for="last_name">Ingresar Apellidos</label>
                <input class="form-control" id="last_name" style="text-transform:uppercase" type="text" name="last_name" value="<?= $user->last_name ?>">
            </div>
            <div class="form-group col-md-4">
                <label class="small mb-1" for="academic_degree">Grado académico</label>
                <select class="form-control" id="academic_degree" name="academic_degree" value="<?= $user->academic_degree ?>">
                    <option value="" selected>Selecciona el grado académico</option>
                    <?php foreach ($degrees as $degree) : ?>
                        <option value="<?= $degree[0] ?>" <?= $degree[0] == $user->academic_degree ? 'selected' : '' ?>><?= $degree[1] ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label class="small mb-1" for="email">DNI</label>
                <input class="form-control" id="email" type="text" name="email" value="<?= $user->email ?>" autocomplete="nope">
            </div>
            <div class="form-group col-md-4">
                <label class="small mb-1" for="avatar">Avatar</label>
                <select class="form-control" id="avatar" name="avatar" value="<?= $user->avatar ?>">
                    <?php foreach ($avatars as $avatar) : ?>
                        <option data-img_src="<?= site_url('assets/img/avatars/') . $avatar ?>" value="<?= $avatar ?>" <?= ($avatar == $user->avatar) ? 'selected' : '' ?>><?= str_replace('.png', '', $avatar) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label class="small mb-1" for="password">Contraseña</label>
                <input class="form-control" id="password" type="password" name="password" autocomplete="new-password" value="">
            </div>
        </div>
        <div class="form-group">
            <label class="small mb-1">Roles</label>
            <div class="row container">
                <?php if ($roles) : foreach ($roles as $role) : ?>
                    <?php $isSelected = isset($role->is_selected) ? (($role->is_selected) ? 'checked' : '') : '' ?>
                        <div class="form-group col-md-3">
                            <input class="form-check-input" type="checkbox" id="id<?=$role->id?>" type="checkbox" value="<?= $role->id ?>" name="role_ids[]" <?= $isSelected ?>>
                            <label class="form-check-label" for="id<?=$role->id?>" title="<?= $role->name ?>">
                                <?= $role->name ?>
                            </label>
                        </div>
                <?php endforeach;
                endif ?>
            </div>
        </div>
        <div class="float-right">
            <?php $var = (isset($path)) ? $path : '' ?>
            <a href="<?= site_url('admin/users' . $var) ?>" class="btn btn-dark">Cancelar</a>
            <button class="btn btn-primary" type="submit">Guardar</button>
        </div>

        <?= form_close() ?>
    </div>
</div>
<script type="text/javascript">
    function custom_template(obj) {
        var data = $(obj.element).data();
        var text = $(obj.element).text();
        if (data && data['img_src']) {
            img_src = data['img_src'];
            template = $("<img src=\"" + img_src + "\" style=\"width:30px;height:30px;\"/><label style=\"font-weight: 50;font-size:14pt;text-align:center;\">" + "  " + text + "</label>");
            return template;
        }
    }
    var options = {
        'templateSelection': custom_template,
        'templateResult': custom_template,
    }
    $('#avatar').select2(options);
    $('.select2-container--default .select2-selection--single').css({
        'height': '37px'
    });
</script>