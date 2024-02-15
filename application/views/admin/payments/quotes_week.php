
<div class="modal-dialog modal-xl">

  <div class="modal-content">
    <div class="d-flex flex-row-reverse col-md-12" style="padding-top:10px;">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="padding-left:10px;"><i class="fas fa-times fa-sm"></i></button>
      <button type="button" class="close" onclick="printElementById('printable', 'COBROS PRÓXIMOS')" style="padding-left:10px;"><i class="fas fa-print fa-sm"></i></button>
      <a type="button" class="close" style="padding-left:10px;" href="<?=site_url('admin/payments/week_excel/'.$user_id)?>"><i class="fas fa-file-excel fa-sm"></i></a>
    </div>
    <div id="printable">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">
          <?php
          $quotes = ($items != null) ? sizeof($items) : "0";
          echo "Cuotas: " . $quotes . "<br>";
          echo "Asesor: $user_name";
          ?>
        </h5>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <div class="table-responsive" id="table">
              <div style="margin-bottom: 8px">
                <?php if (isset($payables)) : if (sizeof($payables) > 0) : ?>
                  <h6 class="h6">Montos totales por tipo de monenda y estado:</h6>
                <?php endif; endif;?>
                <?php if (isset($payable_expired)) : if (sizeof($payable_expired) > 0) : ?>
                    <?php foreach ($payable_expired as $pay_exp) : ?>
                      <input class="btn btn-outline-danger" value="<?= $pay_exp->total . ' ' . $pay_exp->name ?>" style="margin-bottom: 3px" readonly />
                    <?php endforeach ?>
                <?php endif;
                endif ?>
                
                <?php if (isset($payable_now)) : if (sizeof($payable_now) > 0) : ?>
                    <?php foreach ($payable_now as $pay_now) : ?>
                      <input class="btn btn-outline-warning" value="<?= $pay_now->total . ' ' . $pay_now->name ?>" style="margin-bottom: 3px" readonly />
                    <?php endforeach ?>
                <?php endif;
                endif ?>

                <?php if (isset($payable_next)) : if (sizeof($payable_next) > 0) : ?>
                    <?php foreach ($payable_next as $pay_next) : ?>
                      <input class="btn btn-outline-success" value="<?= $pay_next->total . ' ' . $pay_next->name ?>" style="margin-bottom: 3px" readonly />
                    <?php endforeach ?>
                <?php endif;
                endif ?>
              </div>

              <?php if (isset($payables)) : if (sizeof($payables) > 0) : ?>
                  <h6 class="h6">Montos totales por tipo de moneda:</h6>
                  <?php foreach ($payables as $payable) : ?>
                    <input class="btn btn-outline-secondary" value="<?= $payable->total . ' ' . $payable->name ?>" style="margin-bottom: 3px" readonly />
                  <?php endforeach ?>
                  <br>
              <?php endif;
              endif ?>
              <?php if ($items != null) : echo '<small>En la lista se muestran las cuotas con moras y las cuotas cobrables en los próximos 7 días</small>'; ?>
                <div class="table-responsive">
                  <table id="table_content" border="1" class="table table-bordered">
                    <thead>
                      <tr class="active">
                        <th>DNI</th>
                        <th>Cliente</th>
                        <th>$</th>
                        <th>Monto</th>
                        <th class="text-center">Fecha</th>
                        <th class="">Estado</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if ($items) {
                        $i = 0;
                        foreach ($items as $item) {
                          $payed = ($item->payed != null) ? $item->payed : 0;
                          $amount = $item->fee_amount - $payed;
                          echo '<tr title="Asesor: ' . $item->user_name . ' ">';
                          echo "<td>$item->ci</td>";
                          echo "<td>$item->customer_name</td>";
                          echo "<td>$item->coin_short_name</td>";
                          echo "<td>$amount</td>";
                          echo "<td>$item->date</td>";
                          $pay_url = site_url("admin/payments/edit?customer_id=$item->id");
                          if ($item->date == date("Y-m-d")) {
                            echo '<td><center><a class="btn btn-sm btn-warning" href="' . $pay_url . '">' . 'HOY' . '</a></center></td>';
                          } elseif ($item->date < date("Y-m-d")) {
                            echo '<td><center><a class="btn btn-sm btn-danger" href="' . $pay_url . '">' . 'MORA' . '</a></center></td>';
                          } else {
                            echo '<td><center><a class="btn btn-sm btn-success" href="' . $pay_url . '">' . 'CERCA' . '</a></center></td>';
                          }
                          echo '</tr>';
                        }
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
                <?php endif ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="<?= site_url() . 'assets/js/payments/excel-export.js' ?>"></script>
