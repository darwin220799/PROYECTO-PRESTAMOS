<?php if ($user != null) : ?>
    <div class="card shadow mb-4">
        <div class="card-header d-flex align-items-center justify-content-between py-3">
            <h6 class="m-0 font-weight-bold text-primary" id="user_name">Información sobre el usuario</h6>
            <div class="btn-group">
                <div class="btn-group">
                    <a class="btn btn-secondary btn-sm shadow-sm" href="<?= site_url('admin/users/edit/') . $user->id ?>?origin=view">Editar</a>
                    <?php $deleteUrl = site_url('admin/users/delete/') . $user->id; ?>
                    <button class="btn btn-danger btn-sm shadow-sm" onclick="deleteConfirmation('CONFIRMACIÓN', '¿Realmente desea eliminar este usuario?', '<?= $deleteUrl ?>')">Eliminar</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class=" col-12 text-center">
                <h5 class="h5">DETALLES</h5>
            </div>
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
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between py-3">
                    <h6 class="h6">Datos del usuario</h6>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label class="small mb-1">Identificador</label>
                            <p class="form-control"><?= $user->id ?></p>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="small mb-1">Nombre</label>
                            <p class="form-control"><?= $user->first_name ?></p>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="small mb-1">Apellidos</label>
                            <p class="form-control"><?= $user->last_name ?></p>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="small mb-1">Email</label>
                            <p class="form-control"><?= $user->email ?></p>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="small mb-1">Grado</label>
                            <p class="form-control"><?= $user->academic_degree ?></p>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="small mb-1">Avatar</label>
                            <div class="form-control py-0 text-justify">
                                <img src="<?= site_url('assets/img/avatars/') . $user->avatar ?>" alt="Avatar" width="35" height="35">
                                <span><?= $user->avatar ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="roles" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ROLES</th>
                            <th>PERMISOS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rolesPermissions as  $item) :
                            $rowSpan = sizeof($item->permissions) == 0 ? 1 : sizeof($item->permissions);
                        ?>
                            <tr class="text-center">
                                <td class="align-middle"><input class="btn btn-outline-secondary btn-sm" value="<?= $item->name ?>" title="<?= $item->description ?>" readonly></td>
                                <td class="align-middle">
                                    <?php foreach ($item->permissions as $subItem) : ?>
                                        <input class="btn btn-outline-secondary btn-sm" value="<?= $subItem->name ?>" title="<?= $subItem->description ?>" readonly>
                                    <?php endforeach ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
            <br>
        </div>
    <?php else : ?>
        <script>
            alert('No existen datos');
        </script>
    <?php endif ?>