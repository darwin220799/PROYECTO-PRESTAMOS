<?php
$ci = &get_instance();
$ci->load->model("permission_m");
$COIN_READ = $ci->permission_m->getAuthorization($this->session->userdata('user_id'), COIN_READ);
$USER_READ = $ci->permission_m->getAuthorization($this->session->userdata('user_id'), USER_READ);
$ROLE_READ = $ci->permission_m->getAuthorization($this->session->userdata('user_id'), ROLE_READ);
$LEGAL_PROCESS_READ = $ci->permission_m->getAuthorization($this->session->userdata('user_id'), LEGAL_PROCESS_READ);
?>
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
  <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">
    <!-- <img class="page-logo" src="<?php echo base_url(); ?>assets/img/logo.png" alt="logo" /> -->
    <div class="sidebar-brand-text mx-2 text-style">
      <div class="text-style2">
        Sistema de Prestamos
      </div>
      CREDIMOTOS
    </div>
  </a>

  <!-- Divider -->
  <hr class="sidebar-divider my-0">

  <!-- Nav Item - Dashboard -->
  <li class="nav-item active">
    <a class="nav-link" href="<?php echo site_url('admin/dashboard'); ?>">
      <i class="fas fa-fw fa-tachometer-alt"></i>
      <span>Inicio</span></a>
  </li>

  <!-- Divider -->
  <hr class="sidebar-divider">

  <li class="nav-item">
    <a class="nav-link" href="<?php echo site_url('admin/customers'); ?>">
      <i class="fas fa-fw fa-user"></i>
      <span>Clientes</span></a>
  </li>

  <?php
  $ci = &get_instance();
  if ($COIN_READ) :
  ?>
    <li class="nav-item">
      <a class="nav-link" href="<?php echo site_url('admin/coins'); ?>">
        <i class="fas fa-fw fa-money-bill"></i>
        <span>Monedas</span></a>
    </li>
  <?php endif ?>


  <li class="nav-item">
    <a class="nav-link" href="<?php echo site_url('admin/cashregisters'); ?>">
      <i class="fas fa-fw fa-cash-register"></i>
      <span>Cajas</span></a>
  </li>


  <li class="nav-item">
    <a class="nav-link" href="<?php echo site_url('admin/loans'); ?>">
      <i class="fas fa-fw fa-money-bill"></i>
      <span>Préstamos</span></a>
  </li>

  <li class="nav-item">
    <a class="nav-link" href="<?php echo site_url('admin/payments'); ?>">
      <i class="fas fa-fw fa-money-bill"></i>
      <span>Cobros</span></a>
  </li>
  <?php if ($LEGAL_PROCESS_READ) : ?>
    <li class="nav-item">
      <a class="nav-link" href="<?php echo site_url('admin/legalprocesses'); ?>">
        <i class="fas fa-fw fa-file"></i>
        <span>Procesos legales</span></a>
    </li>
  <?php endif ?>
  <li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseReports" aria-expanded="false" aria-controls="collapseReports">
      <i class="fas fa-fw fa-info-circle"></i>
      <span>Reportes</span>
    </a>
    <div id="collapseReports" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <a class="collapse-item" href="<?php echo site_url('admin/reports'); ?>">Resumen General</a>
        <a class="collapse-item" href="<?php echo site_url('admin/reports/dates'); ?>">Entre Fechas</a>
        <a class="collapse-item" href="<?php echo site_url('admin/reports/customers'); ?>">General x cliente</a>
        <a class="collapse-item" href="<?php echo site_url('admin/reports/document_payments'); ?>">Pagos</a>
      </div>
    </div>
  </li>

  <li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseConfiguracion" aria-expanded="false" aria-controls="collapseConfiguracion">
      <i class="fas fa-fw fa-user"></i>
      <span>Configuración</span>
    </a>
    <div id="collapseConfiguracion" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <a class="collapse-item" href="<?php echo site_url('admin/config'); ?>"> Editar datos</a>
        <a class="collapse-item" href="<?php echo site_url('admin/config/change_password'); ?>"> Cambiar Contraseña</a>
      </div>
    </div>
  </li>


  <?php if ($USER_READ || $ROLE_READ) : ?>
    <li class="nav-item">
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAdmin" aria-expanded="false" aria-controls="collapseAdmin">
        <i class="fas fa-fw fa-toolbox"></i>
        <span>Administración</span>
      </a>
      <div id="collapseAdmin" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
          <?php if ($USER_READ) : ?>
            <a class="collapse-item" href="<?php echo site_url('admin/users'); ?>">Gertor de usuarios</a>
          <?php endif ?>
          <?php if ($ROLE_READ) : ?>
            <a class="collapse-item" href="<?php echo site_url('admin/roles'); ?>">Gestor de roles</a>
          <?php endif ?>
        </div>
      </div>
    </li>
  <?php endif  ?>


  <!-- Divider -->
  <hr class="sidebar-divider d-none d-md-block">

  <!-- Sidebar Toggler (Sidebar) -->
  <div class="text-center d-none d-md-inline">
    <button class="rounded-circle border-0" id="sidebarToggle"></button>
  </div>

</ul>