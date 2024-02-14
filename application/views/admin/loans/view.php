<div class="modal-dialog">

  <div class="modal-content">

    <div class="col-md-12" style="padding-top:10px;">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="padding-left:10px;"><i class="fas fa-times fa-sm"></i></button>
      <button type="button"  class="close" onclick="printElementById('printable', 'PAGOS')" ><i class="fas fa-print fa-sm"></i></button>
    </div>
    <div id="printable">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">
          ID préstamo # <?php if ($loan) echo $loan->id ?>
          <br>
          Cliente: <?php if ($loan) echo $loan->customer_name; ?>
        </h5>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <div class="table-responsive" id="table">
              <?php if ($loan != null) : ?>
                <div class="clearfix mb-2">

                  <div class="float-left">

                    Monto Crédito: <?= $loan->credit_amount; ?>
                    <br>
                    Interes Crédito: <?= $loan->interest_amount . '%'; ?>
                    <br>
                    Nro cuotas: <?= $loan->num_fee; ?>
                    <br>
                    Monto cuota: <?= $loan->fee_amount; ?>
                    <br>
                  </div>
                  <div class="float-right">
                    Fecha Crédito: <?= $loan->date; ?>
                    <br>
                    Forma Pago: <?= $loan->payment_m; ?>
                    <br>
                    Estado Crédito: <?= $loan->status ? 'Pendiente' : 'Pagado'; ?>
                    <br>
                    Tipo de moneda: <?= strtoupper($loan->short_name); ?>
                  </div>

                </div>

                <div class="table-responsive">
                  <table class="table table-striped table-condensed">
                    <thead>
                      <tr class="active">
                        <th>Nro Cuota</th>
                        <th class="col-xs-2">Fecha Pago</th>
                        <th class="col-xs-2 text-right">Total pagar</th>
                        <th class="col-xs-2 text-center">Estado</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if ($items) {
                        $i = 0;
                        foreach ($items as $item) {
                          echo '<tr>';
                          echo '<td>' . ++$i . '</td>';
                          echo '<td>' . $item->date . '</td>';
                          echo '<td class="text-right">' . $item->fee_amount . '</td>';
                          $status = ($item->status) ? 'Pendiente' : 'Cancelado';
                          echo '<td class="text-center">' . $status . '</td>';
                          echo '</tr>';
                        }
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
            </div>
          <?php endif ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>