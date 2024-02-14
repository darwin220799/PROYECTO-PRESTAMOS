<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>Ticket No <?= $document_payment->id ?></title>
  <link href="<?php echo site_url(); ?>assets/css/sb-admin-2.min.css" rel="stylesheet">
  <style type="text/css" media="all">
    body {
      color: #000;
    }

    #wrapper {
      max-width: 520px;
      margin: 0 auto;
      padding-top: 20px;
    }

    .btn {
      margin-bottom: 5px;
    }

    .table {
      border-radius: 3px;
    }

    .table th {
      background: #f5f5f5;
    }

    .table th,
    .table td {
      vertical-align: middle !important;
    }

    tfoot tr th:first-child {
      text-align: right;
    }

    @media print {
      .no-print {
        display: none;
      }

      #wrapper {
        max-width: 480px;
        width: 100%;
        min-width: 250px;
        margin: 0 auto;
      }
    }
  </style>
</head>

<body>

  <div id="wrapper">
    <div id="receiptData" style="width: auto; max-width: 580px; min-width: 250px; margin: 0 auto;">
      <div id="receipt-data">
        <p style="text-align:center;"><strong>Pago Realizado</strong>
        <p>
          Código de Documento: <?= isset($document_payment->id) ? $document_payment->id : '-' ?> <br>
          Fecha/hora: <?php echo isset($document_payment->pay_date) ? $document_payment->pay_date : '-';
                      $total = 0; ?> <br>
          Código Préstamo: <?php echo isset($loan->id) ? $loan->id : '-'; ?> <br>
          Cliente: <?php echo isset($customer->name) ? $customer->name : '-'; ?> <br>
          Asesor: <?php echo isset($adviser->name) ? $adviser->name : '-'; ?> <br>
          Usuario: <?php echo isset($document_payment->user_name) ? $document_payment->user_name : '-'; ?> <br>
          Tipo moneda: <?= isset($loan->coin_name) ? $loan->coin_name : '-'; ?><br>
        </p>
        <div style="clear:both;"></div>
        <table class="table table-condensed">
          <thead>
            <tr>
              <th class="text-center" style="width: 50%; border-bottom: 2px solid #ddd;">Descripción</th>
              <th class="text-center" style="width: 24%; border-bottom: 2px solid #ddd;">Cuota</th>
              <th class="text-center" style="width: 26%; border-bottom: 2px solid #ddd;">Recargo</th>
              <th class="text-center" style="width: 26%; border-bottom: 2px solid #ddd;">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            <?php if (isset($quotas_payments)) : foreach ($quotas_payments as $qp) : ?>
                <?php
                $subtotal =  $qp->amount + $qp->surcharge;
                $total += $subtotal;
                ?>
                <tr>
                  <td>Cuota N° <?php echo $qp->num_quota ?></td>
                  <td class="text-right"><?php echo $qp->amount;  ?></td>
                  <td class="text-right"><?php echo $qp->surcharge ?></td>
                  <td class="text-right"><?php echo number_format($subtotal, 2) ?></td>
                </tr>
            <?php endforeach;
            endif; ?>
          </tbody>
          <tfoot>
            <tr>
              <th colspan="3">Total</th>
              <th class="text-right"><?php echo number_format($total, 2) ?></th>
            </tr>
          </tfoot>
        </table>
        <table class="table table-striped table-condensed" style="margin-top:10px;">
          <tbody>
            <tr>
              <td class="text-center">Pagado en: Efectivo</td>
            </tr>

          </tbody>
        </table>
      </div>
      <div id="buttons" style="text-transform:uppercase;" class="no-print">
        <hr>
        <span class="col-xs-12">
          <button onclick="window.print();" class="btn btn-block btn-primary">Imprimir</button></span>

        <!-- <span class="col-xs-12">
          <a class="btn btn-block btn-success" href="<?php echo site_url('admin/payments/'); ?>">Listar Pagos</a>
        </span> -->
        <div style="clear:both;"></div>
      </div>
    </div>
  </div>
</body>
</html>