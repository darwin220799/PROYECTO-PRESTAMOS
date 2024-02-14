<div class="row">
  <!-- Earnings (Monthly) Card Example -->
  <div class="col-xl-4 mb-4">
    <div class="card border-left-primary shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
            Número clientes</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo isset($qCts->cantidad)?$qCts->cantidad:0 ?></div>
          </div>
          <div class="col-auto">
            <i class="fas fa-user fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Earnings (Monthly) Card Example -->
  <div class="col-xl-4 mb-4">
    <div class="card border-left-success shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
            Número préstamos</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo isset($qLoans->cantidad)?$qLoans->cantidad:0?></div>
          </div>
          <div class="col-auto">
            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Earnings (Monthly) Card Example -->
  <div class="col-xl-4 mb-4">
    <div class="card border-left-info shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Número cobros
            </div>
            <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?php echo isset($qPaids->cantidad)?$qPaids->cantidad:0 ?></div>
          </div>
          <div class="col-auto">
            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="card shadow mb-4">
    <div class="card-header d-flex py-3 justify-content-between">
      <h6 class="m-0 font-weight-bold text-primary"><?php echo $this->session->userdata('first_name'). ' '.$this->session->userdata('last_name'); ?></h6>
      <div>
        <?php if(isset($users)) : if(sizeof($users) > 0):
          echo "<select class='custom-select-sm btn-outline-secondary' onchange='location = this.value;'>";
          $url = site_url("admin/dashboard");
          $selected = ($selected_user_id == 0)?'selected':'';
          echo "<option value='$url' $selected>TODO</option>";
            foreach($users as $user) :
              $url = site_url("admin/dashboard/index/$user->id");
              $selected = ($selected_user_id == $user->id)?'selected':'';
              $user_name = "$user->academic_degree $user->first_name $user->last_name";
              echo "<option value='$url' $selected>$user_name</option>";
            endforeach;
          echo "</select>";
        endif; endif;?>
      </div>
    </div>
    <div class="card-body">
      <p class="text-center h5 mb-4">Total préstamos por tipo de moneda</p>
      <canvas id="grafica"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@latest/dist/Chart.min.js"></script>
<script>
  //When receiving data from a web server, the data is always a string.
  //Parse the data with JSON.parse(), and the data becomes a JavaScript object.
  var cData = JSON.parse('<?php echo $countLC; ?>');

  // Obtener una referencia al elemento canvas del DOM
  const $grafica = document.querySelector("#grafica");
  // Las etiquetas son las porciones de la gráfica
  const etiquetas = cData.label
  // Podemos tener varios conjuntos de datos. Comencemos con uno
  const datosIngresos = {
    data: cData.data, // La data es un arreglo que debe tener la misma cantidad de valores que la cantidad de etiquetas
    // Ahora debería haber tantos background colors como datos, es decir, para este ejemplo, 4
    backgroundColor: [
        'rgba(163,221,203,0.2)',
        'rgba(232,233,161,0.2)',
        'rgba(230,181,102,0.2)',
        'rgba(229,112,126,0.2)',
    ],// Color de fondo
    borderColor: [
        'rgba(163,221,203,1)',
        'rgba(232,233,161,1)',
        'rgba(230,181,102,1)',
        'rgba(229,112,126,1)',
    ],// Color del borde
    borderWidth: 1,// Ancho del borde
  };

  new Chart($grafica, {
    type: 'pie',// Tipo de gráfica. Puede ser dougnhut o pie
    data: {
      labels: etiquetas,
      datasets: [
          datosIngresos,
          // Aquí más datos...
      ]
    },
  });
</script>