    <div class="card shadow mb-4">
        <div class="card-header d-flex align-items-center justify-content-between py-3">
            <h6 class="m-0 font-weight-bold text-primary" id="cash_register_name"><?= $cash_register->name ?? 'undefined' ?></h6>
            <div class="btn-group" role="group" aria-label="Basic example">
                <?php if (($CASH_REGISTER_UPDATE || $AUTHOR_CASH_REGISTER_UPDATE) && $IS_OPEN) : if (isset($cash_register)) : ?>
                        <a type="button" class="btn btn-secondary btn-sm" href="<?= site_url('admin/cashregisters/manual_input_create/' . $cash_register->id) ?>">Entrada manual</a>
                        <a type="button" class="btn btn-secondary btn-sm" href="<?= site_url('admin/cashregisters/manual_output_create/' . $cash_register->id) ?>">Salida manual</a>
                <?php endif;
                endif ?>
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
                    </button>
                    <?php $this->session->unset_userdata('msg'); ?>
                </div>
            <?php endif ?>
            <div class="card">
                <div class="card-header">
                    <h6 class="h6">Datos técnicos</h6>
                </div>

                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label class="small mb-1">Usuario</label>
                            <p class="form-control"><?= $cash_register->user_name ?></p>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="small mb-1">Fecha de apertura</label>
                            <p class="form-control"><?= $cash_register->opening_date ?></p>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="small mb-1">Fecha de cierre</label>
                            <p class="form-control" style="text-transform:uppercase"><?= $cash_register->closing_date ?></p>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="small mb-1">Identificador</label>
                            <input class="form-control" id='id' value="<?= $cash_register->id ?>" disabled>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="small mb-1">Tipo de moneda</label>
                            <p class="form-control"><?= $cash_register->coin_name ?></p>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="small mb-1">Estado</label>
                            <?php
                            $url = $cash_register->status ? site_url("admin/cashregisters/close_cash_register/$cash_register->id") : '#';
                            if ($cash_register->status) :
                            ?>
                                <a href="<?= site_url('admin/cashregisters/close_cash_register/' . $cash_register->id) ?>" class="form-control btn btn-primary" onclick="return confirm('Se cerrará esta caja, esta acción es irreversible\n¿Quieres continuar?')">
                                    ABIERTO
                                </a>
                            <?php else : ?>
                                <a href="#" class="form-control btn btn-secondary">
                                    CERRADO
                                </a>
                            <?php endif ?>
                        </div>
                    </div>

                </div>
            </div>
            <br>
            <div class="card">
                <div class="card-header">
                    <h6 class="h6">Entradas manuales</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="manual-inputs" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th class="col-1">ID</th>
                                    <th class="col-2">Monto</th>
                                    <th class="col-6">Descripción</th>
                                    <th class="col-3">Fecha</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <br>
            <div class="card">
                <div class="card-header">
                    <h6 class="h6">Entradas por pagos</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="document-payment-inputs" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th class="col-1">ID</th>
                                    <th class="col-6">Cliente</th>
                                    <th class="col-2">Monto</th>
                                    <th class="col-3">Fecha</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <br>
            <div class="card">
                <div class="card-header">
                    <h6 class="h6">Salidas manuales</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="manual-outputs" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th class="col-1">ID</th>
                                    <th class="col-2">Monto</th>
                                    <th class="col-6">Descripción</th>
                                    <th class="col-3">Fecha</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <br>
            <div class="card">
                <div class="card-header">
                    <h6 class="h6">Salidas por préstamos</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="loan-outputs" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th class="col-1">ID</th>
                                    <th class="col-6">Cliente</th>
                                    <th class="col-2">Monto</th>
                                    <th class="col-3">Fecha</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <br>
            <div class="card">
                <div class="card-header">
                    <h6 class="h6">Resumen general</h6>
                </div>
                <div class="card-body ">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col"><b>Descripción</b></th>
                                    <th scope="col">
                                        <d>Manual</b>
                                    </th>
                                    <th scope="col">Operación</th>
                                    <th scope="col">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row">Entradas</th>
                                    <td><?= number_format($cash_register->manual_inputs_amount, 2) ?></td>
                                    <td><?= number_format($cash_register->document_payment_inputs_amount, 2) ?></td>
                                    <td><?= number_format($cash_register->manual_inputs_amount + $cash_register->document_payment_inputs_amount, 2) ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Salidas</th>
                                    <td><?= number_format($cash_register->manual_outputs_amount, 2) ?></td>
                                    <td><?= number_format($cash_register->loan_outputs_amount, 2) ?></td>
                                    <td><?= number_format($cash_register->manual_outputs_amount + $cash_register->loan_outputs_amount, 2) ?></td>
                                </tr>
                                <tr>
                                    <?php
                                    $manualTotal = number_format($cash_register->manual_inputs_amount - $cash_register->manual_outputs_amount, 2);
                                    $operationTotal = number_format($cash_register->document_payment_inputs_amount - $cash_register->loan_outputs_amount, 2);
                                    $total = number_format($cash_register->manual_inputs_amount - $cash_register->manual_outputs_amount +
                                        $cash_register->document_payment_inputs_amount - $cash_register->loan_outputs_amount, 2);
                                    ?>
                                    <th scope="row">Total</th>
                                    <td><b><?= $manualTotal ?></b></td>
                                    <td><b><?= $operationTotal ?></b></td>
                                    <td><b><?= $total ?></b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <script src="<?= site_url() . 'assets/js/cash-registers/view.js' ?>"></script>
    </div>