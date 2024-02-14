<div class="card shadow mb-4">
  <div class="card-header py-3"><?php echo empty($customer->first_name) ? 'Nuevo Cliente' : 'Editar Cliente'; ?></div>
  <div class="card-body">
    <?php if(validation_errors()) { ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo validation_errors('<li>', '</li>'); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    <?php } ?>
    
    <?= form_open(); ?>
    
    <div class="form-row">
      <div class="form-group col-md-3">
        <label class="small mb-1" for="ci">Ingresar CI</label>
        <input class="form-control" id="ci" type="text" name="ci" value="<?php echo set_value('ci', $this->input->post('ci') ? $this->input->post('ci') : $customer->ci); ?>">
      </div>
      <div class="form-group col-md-3">
        <label class="small mb-1" for="first_name">Ingresar Nombre</label>
        <input class="form-control" id="first_name" style="text-transform:uppercase" type="text" name="first_name" value="<?php echo set_value('first_name', $this->input->post('first_name') ? $this->input->post('first_name') : $customer->first_name); ?>">
      </div>
      <div class="form-group col-md-3">
        <label class="small mb-1" for="last_name">Ingresar Apellidos</label>
        <input class="form-control" id="last_name" style="text-transform:uppercase" type="text" name="last_name" value="<?php echo set_value('last_name', $this->input->post('last_name') ? $this->input->post('last_name') : $customer->last_name); ?>">
      </div>
      <div class="form-group col-md-3">
        <label class="small mb-1" for="gender">Seleccionar Género</label>
        <select class="form-control" id="gender" name="gender">

          <?php if ($customer->gender == 'none'): ?>
            <option value = "" selected>Seleccionar género</option>
          <?php endif ?>

          <option value="masculino" <?php if ($customer->gender == 'masculino') echo "selected" ?>>
            masculino
          </option>
          <option value="femenino" <?php if ($customer->gender == 'femenino') echo "selected" ?>>
            femenino
          </option>
        </select>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group col-md-4">
        <label class="small mb-1" for="address">Ingresar dirección</label>
        <input class="form-control" id="address" type="text" name="address" value="<?php echo set_value('address', $this->input->post('address') ? $this->input->post('address') : $customer->address); ?>">
      </div>
      <div class="form-group col-md-4">
        <label class="small mb-1" for="mobile">Ingresar celular</label>
        <input class="form-control" id="mobile" type="text" name="mobile" value="<?php echo set_value('mobile', $this->input->post('mobile') ? $this->input->post('mobile') : $customer->mobile); ?>">
      </div>
      <div class="form-group col-md-4">
        <label class="small mb-1" for="phone">Ingresar Teléfono</label>
        <input class="form-control" id="phone" type="text" name="phone" value="<?php echo set_value('phone', $this->input->post('phone') ? $this->input->post('phone') : $customer->phone); ?>">
      </div>
    </div>

    <div class="form-row">
      <div class="form-group col-md-4">
        <label class="small mb-1" for="business_name">Ingresar razon social</label>
        <input class="form-control" id="business_name" type="text" name="business_name" value="<?php echo set_value('business_name', $this->input->post('business_name') ? $this->input->post('business_name') : $customer->business_name); ?>">
      </div>
      <div class="form-group col-md-4">
        <label class="small mb-1" for="nit">Ingresar NIT</label>
        <input class="form-control" id="nit" type="text" name="nit" value="<?php echo set_value('nit', $this->input->post('nit') ? $this->input->post('nit') : $customer->nit); ?>">
      </div>
      <div class="form-group col-md-4">
        <label class="small mb-1" for="company">Ingresar empresa</label>
        <input class="form-control" id="company" type="text" name="company" value="<?php echo set_value('company', $this->input->post('company') ? $this->input->post('company') : $customer->company); ?>">
      </div>
      <div class="form-group col-md-4" hidden >
        <input class="form-control"  hidden readonly=true  id="user_id" type="number" name="user_id" value="<?php echo set_value('user_id', $this->input->post('user_id') ? $this->input->post('user_id') : $customer->user_id); ?>">
      </div>
    </div>
    <div class="float-right">
      <a href="<?php echo site_url('admin/customers/'); ?>" class="btn btn-dark">Cancelar</a>
      <button class="btn btn-primary" type="submit">Guardar</button>
    </div>
    <?= form_close() ?>
  </div>
</div>