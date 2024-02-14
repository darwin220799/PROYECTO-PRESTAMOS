<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

defined('BASEPATH') or exit('No direct script access allowed');
include(APPPATH . "/tools/UserPermission.php");

class Payments extends CI_Controller
{

  private $user_id;
  private $permission;

  public function __construct()
  {
    parent::__construct();
    $this->load->model('payments_m');
    $this->load->model('cashregister_m');
    $this->load->model('permission_m');
    $this->load->library('form_validation');
    $this->load->library('session');
    $this->session->userdata('loggedin') == TRUE || redirect('user/login');
    $this->user_id = $this->session->userdata('user_id');
    $this->permission = new Permission($this->permission_m, $this->user_id);
  }

  public function index()
  {
    $data[PAYMENT_CREATE] = $this->permission->getPermission([PAYMENT_CREATE], FALSE);
    $data[AUTHOR_PAYMENT_CREATE] =  $this->permission->getPermission([AUTHOR_PAYMENT_CREATE], FALSE);
    $data[LOAN_READ] = $this->permission->getPermission([LOAN_READ], FALSE);
    $data[AUTHOR_LOAN_READ] = $this->permission->getPermission([AUTHOR_LOAN_READ], FALSE);
    $data[LOAN_ITEM_READ] = $this->permission->getPermission([LOAN_ITEM_READ], FALSE);
    $data[AUTHOR_LOAN_ITEM_READ] = $this->permission->getPermission([AUTHOR_LOAN_ITEM_READ], FALSE);
    $data['payments'] = array();
    if ($this->permission->getPermission([LOAN_READ, LOAN_ITEM_READ], FALSE)) {
      $data['users'] = $this->db->get('users')->result();
      $data['payments'] = $this->payments_m->getPaymentsAll();
    } elseif ($this->permission->getPermission([AUTHOR_LOAN_READ, AUTHOR_LOAN_ITEM_READ], FALSE)) {
      $data['payments'] = $this->payments_m->getPayments($this->user_id);
    }
    $data['subview'] = 'admin/payments/index';
    $this->load->view('admin/_main_layout', $data);
  }

  public function ajax_payed_loan_items($user_id = null)
  {
    $LOAN_READ = $this->permission->getPermission([CUSTOMER_READ], FALSE);
    $AUTHOR_LOAN_READ = $this->permission->getPermission([AUTHOR_CUSTOMER_READ], FALSE);
    if(!$LOAN_READ) {
      if(!$AUTHOR_LOAN_READ)
      { $json_data = array(
          "draw"            => intval($this->input->post('draw')),
          "recordsTotal"    => intval(0),
          "recordsFiltered" => intval(0),
          "data"            => [], 
        );
        echo json_encode($this->json_data);
        return;
      }else{
        $user_id = $this->user_id;
      }
    }
    $start = $this->input->post('start');
		$length = $this->input->post('length');
		$search = $this->input->post('search')['value']??'';
    $columns = ['ci', 'name_cst', 'loan_id', 'num_quota', 'fee_amount', 'pay_date', ''];
    $columIndex = $this->input->post('order')['0']['column']??6;
    $order['column'] = $columns[$columIndex]??'';
    $order['dir'] = $this->input->post('order')['0']['dir']??'';
    $query = $this->payments_m->findPayedLoanItems($start, $length, $search, $order, $user_id);
    if(sizeof($query['data'])==0 && $start>0) $query = $this->payments_m->findPayedLoanItems(0, $length, $search, $order, $user_id);
    $json_data = array(
      "draw"            => intval($this->input->post('draw')),
      "recordsTotal"    => intval(sizeof($query['data'])),
      "recordsFiltered" => intval($query['recordsFiltered']),
      "data"            => $query['data']
    );
    echo json_encode($json_data);
  }

  public function edit()
  {
    $customer_id = $this->input->get('customer_id') ?? NULL;
    $data['default_selected_customer_id'] = $customer_id;
    $data['customers'] = array();
    if ($this->permission->getPermission([PAYMENT_CREATE], FALSE))
      $data['customers'] = $this->payments_m->getCustomersAll();
    elseif ($this->permission->getPermission([AUTHOR_PAYMENT_CREATE], FALSE)){
      if($customer_id){
        if(!$this->payments_m->isAdviser($customer_id, $this->user_id)){
          show_error("You don't have access to this site", 403, 'DENIED ACCESS');
        }
      }
      $data['customers'] = $this->payments_m->get_customers($this->user_id);
    }
    else
      $this->permission->redirectIfFalse(FALSE, TRUE);
    $data['subview'] = 'admin/payments/edit';
    $this->load->view('admin/_main_layout', $data);
  }

  public function ajax_get_loan($customer_id)
  {
    if ($this->permission->getPermissionX([PAYMENT_CREATE], FALSE))
      $quota_data = $this->payments_m->getLoanAll($customer_id);
    elseif ($this->permission->getPermissionX([AUTHOR_PAYMENT_CREATE], FALSE))
      $quota_data = $this->payments_m->getLoan($this->user_id, $customer_id);
    $search_data = ['loan' => $quota_data];
    echo json_encode($search_data); // datos leidos por javascript Ajax
  }

  public function ajax_get_loan_items($loan_id)
  {
    $quota_data = array();
    if ($this->permission->getPermission([PAYMENT_CREATE], FALSE))
      $quota_data = $this->payments_m->getLoanItemsAll($loan_id);
    elseif ($this->permission->getPermission([AUTHOR_PAYMENT_CREATE], FALSE))
      $quota_data = $this->payments_m->getLoanItems($this->user_id, $loan_id);
    $search_data = ['quotas' => $quota_data];
    echo json_encode($search_data); // datos leidos por javascript Ajax
  }

  public function ajax_get_guarantors($loan_id)
  {
    $guarantors = array();
    if ($this->permission->getPermissionX([PAYMENT_CREATE], FALSE))
      $guarantors = $this->payments_m->getGuarantorsAll($loan_id);
    elseif ($this->permission->getPermissionX([AUTHOR_PAYMENT_CREATE], FALSE))
      $guarantors = $this->payments_m->get_guarantors($this->user_id, $loan_id);
    $search_datax = ['guarantors' => $guarantors];
    echo json_encode($search_datax); // datos leidos por javascript Ajax
  }

  /**
   * Pagar reemplaza funcionalidad de guardar de ticket
   */
  public function save_payment()
  {
    // $LOAN_UPDATE = $this->permission->getPermission([LOAN_UPDATE], FALSE);
    // $AUTHOR_LOAN_UPDATE = $this->permission->getPermission([AUTHOR_LOAN_UPDATE], FALSE);
    $PAYMENT_CREATE = $this->permission->getPermission([PAYMENT_CREATE], FALSE);
    $AUTHOR_PAYMENT_CREATE = $this->permission->getPermission([AUTHOR_PAYMENT_CREATE], FALSE);
    // Guardar
    $customer_id = $this->input->post('customer_id');
    if ($customer_id != null) { // valida que no se acceda desde la url sin datos de entrada
      if ($AUTHOR_PAYMENT_CREATE)
        $data['customerName'] = $this->payments_m->getCustomerByIdAll($customer_id);
      elseif ($AUTHOR_PAYMENT_CREATE)
        $data['customerName'] = $this->payments_m->get_customer_by_id($this->user_id, $customer_id);
      $data['coin'] = $this->input->post('coin');
      $data['loan_id'] = $this->input->post('loan_id');
      $loan_id = $this->input->post('loan_id');
      $quota_id = $this->input->post('quota_id'); // array
      $cash_register_id =  $this->input->post('cash_register_id');
      // cargar pagos
      $payments = [];
      if (isset($quota_id)) : if (sizeof($quota_id) > 0) :
          foreach ($quota_id as $id) {
            array_push($payments, [
              'loan_item_id' => $id,
              'amount' => $this->input->post("amount_quota_$id"),
              'surcharge' => $this->input->post("surcharge_$id")
            ]);
          }
        endif;
      endif;
      if ($PAYMENT_CREATE) {
        $this->addPayment($loan_id, $cash_register_id, $quota_id, $payments, $customer_id, $data);
      } elseif ($AUTHOR_PAYMENT_CREATE) {
        $probable_user_id = $this->payments_m->get_loan_adviser_user_id($loan_id)->id;
        if (AuthUserData::isAuthor($probable_user_id)) {
          $this->addPayment($loan_id, $cash_register_id, $quota_id, $payments, $customer_id, $data);
        } else {
          show_error("You don't have access to this site", 403, 'DENIED ACCESS');
        }
      } else {
        show_error("You don't have access to this site", 403, 'DENIED ACCESS');
      }
    } else {
      echo loadErrorMessage('¡Imposible acceder a está página, faltan datos de entrada para la transacción!');
    }
  }

  /**
   * Guarda los pagos
   */
  private function addPayment($loan_id, $cash_register_id, $quota_id, $payments, $customer_id)
  {
    $validate = $this->payments_m->paymentsOk($payments);
    $savePaymentsIsSuccess = FALSE;
    try {
      if ($validate->valid) {
        $this->cashRegisterValidation($loan_id, $this->user_id, $cash_register_id);
        $Object = new DateTime();
        $pay_date = $Object->format("Y-m-d h:i:s");
        $this->db->trans_begin();
        $id = $this->payments_m->addDocumentPayment(['user_id' => $this->user_id, 'cash_register_id' => $cash_register_id, 'pay_date' => $pay_date]);
        if ($id > 0) {
          for ($i = 0; $i < sizeof($payments); $i++) {
            $payments[$i]['document_payment_id'] = $id;
          }
          $savePaymentsIsSuccess = $this->payments_m->addPayments($payments);
        }
      } else {
        foreach ($validate->errors as $error)
          echo "ERROR: " . $error;
        return;
      }
      if ($savePaymentsIsSuccess) { // Cambiar estados
        if (isset($quota_id)) {
          foreach ($quota_id as $q) {
            if ($this->payments_m->paymentsIsEqualToQuote($q))
              $this->payments_m->update_quota(['status' => 0], $q);
          }
          if (!$this->payments_m->check_cstLoan($loan_id)) {
            $this->payments_m->update_cstLoan($loan_id, $customer_id);
          }
          $this->db->trans_commit();
          $this->session->set_flashdata('msg', 'El pago se procesó con éxito');
          if ($this->permission->getPermission([DOCUMENT_PAYMENT_READ, AUTHOR_DOCUMENT_PAYMENT_READ], FALSE)) {
            $this->session->set_flashdata('document_payment_id', $id);
          }
          redirect("admin/payments");
        } else {
          $this->db->trans_rollback();
          $this->session->set_flashdata('msg_error', 'No existen cuotas para registrar');
        }
      } else {
        $this->db->trans_rollback();
        $this->session->set_flashdata('msg_error', '¡Ocurrió un error durante la transacción!');
      }
      redirect("admin/payments/edit");
    } catch (Exception $ex) {
      $this->db->trans_rollback();
      echo loadErrorMessage($ex->getMessage());
    }
  }

  private function cashRegisterValidation($loan_id, $user_id, $cash_register_id)
  {
    $errors = [];
    if ($cash_register_id == null || $cash_register_id == '')
      array_push($errors, 'No se identifico una caja de origen para el préstamo');
    else
    if (
      ($this->permission->getPermission([AUTHOR_CASH_REGISTER_UPDATE], FALSE) && $this->cashregister_m->isAuthor($cash_register_id, $user_id))
      || $this->permission->getPermission([CASH_REGISTER_UPDATE], FALSE)
    ) {
      $loanRequest = $this->db->get_where('loans', ['id' => $loan_id])->row();
      $coin_id = $loanRequest->coin_id ?? 0;
      if (!$this->cashregister_m->isCoinType($cash_register_id, $coin_id))
        array_push($errors, 'El tipo de moneda del préstamo, no coincide con el tipo de moneda de la caja');
      if (!$this->cashregister_m->isOpen($cash_register_id))
        array_push($errors, 'La caja está cerrada');
    } else {
      array_push($errors, 'El usuario no tiene el permiso y no es autor de la caja o la caja no existe');
    }
    if (sizeof($errors) > 0) {
      $messages = '';
      foreach ($errors as $error)
        $messages .= '<li>' . $error . '</li>';;
      $this->session->set_flashdata('msg_error', $messages);
      redirect("admin/payments/edit");
    }
  }

  public function document_payment($id)
  {
    $document = $this->payments_m->getDocumentPayment($id);
    if ($this->permission->getPermission([DOCUMENT_PAYMENT_READ], FALSE)) {
      $this->load->view('admin/payments/ticket', $document);
      return;
    } elseif ($this->permission->getPermission([AUTHOR_DOCUMENT_PAYMENT_READ], FALSE)) {
      if ($document['customer']->user_id == $this->user_id) {
        $this->load->view('admin/payments/ticket', $document);
        return;
      }
    }
    echo loadErrorMessage('No tiene el permiso para leer el documento de impresión...');
  }

  private function get_week_data($user_id)
  {
    $start_date = date("Y-m-d", time());
    $end_date = date("Y-m-d", strtotime($start_date . ' + 7 days'));
    if ($this->permission->getPermission([LOAN_READ, LOAN_ITEM_READ], FALSE)) {
      if ($user_id == 'all') {
        $data['user_name'] = "TODOS";
        $request = $this->payments_m->quotesWeekAll($start_date, $end_date);
      } else {
        $data['user_name'] = $this->payments_m->getUser($user_id)->user_name;
        $request = $this->payments_m->quotesWeek($user_id, $start_date, $end_date);
      }
      $data['items'] = $request['items'];
      $data['payables'] = $request['payables'];
      $data['payable_expired'] = $request['payable_expired'];
      $data['payable_now'] = $request['payable_now'];
      $data['payable_next'] = $request['payable_next'];
    } elseif ($this->permission->getPermission([AUTHOR_LOAN_READ, AUTHOR_LOAN_ITEM_READ], FALSE)) {
      $request = $this->payments_m->quotesWeek($this->user_id, $start_date, $end_date);
      $data['user_name'] = $this->payments_m->getUser($this->user_id)->user_name;
      $data['items'] = $request['items'];
      $data['payables'] = $request['payables'];
      $data['payable_expired'] = $request['payable_expired'];
      $data['payable_now'] = $request['payable_now'];
      $data['payable_next'] = $request['payable_next'];
    } else {
      $data['user_name'] = '';
      $data['items'] = [];
      $data['payables'] = null;
      $data['payable_expired'] = null;
      $data['payable_now'] = null;
      $data['payable_next'] = null;
    }
    return $data;
  }

  /**
   * Muestra las cuotas proximas y las que ya están con mora
   * https://www.delftstack.com/es/howto/php/how-to-get-the-current-date-and-time-in-php/
   * https://www.php.net/manual/en/timezones.america.php
   */
  function quotes_week($user_id)
  {
    $data = $this->get_week_data($user_id);
    $data['user_id'] = $user_id;
    $this->load->view('admin/payments/quotes_week', $data);
  }

  /**
   * Sirven para actualizar caja mediante cobros
   */
  public function ajax_get_cash_registers($coin_id)
  {
    if (!$this->permission->getPermission([CASH_REGISTER_UPDATE], FALSE)) {
      if ($this->permission->getPermission([AUTHOR_CASH_REGISTER_UPDATE], FALSE))
        echo json_encode($this->cashregister_m->getCashRegistersX($this->user_id, $coin_id));
      else
        echo json_encode([]);
      return;
    }
    echo json_encode($this->cashregister_m->getCashRegistersX('all', $coin_id));
  }

  /**
   * Crea el reporte en excel
   */
  public function week_excel($user_id)
  {
    $data = $this->get_week_data($user_id);
    if($data == null) return;
    $phpExcel = new Spreadsheet();
    $phpExcel->getProperties()->setCreator('ecomsoft')->setTitle('Reporte');
    $sheet = $phpExcel->getActiveSheet();
    $sheet->setTitle('REPORTE');

    $drawing = new Drawing();
    $drawing->setName('logo');
    $drawing->setPath('assets/img/excel_report_logo.png');
    $drawing->setHeight(100);
    $drawing->setCoordinates('A1');
    $drawing->setWorksheet($sheet);

    $sheet->mergeCells('A1:N1');
    $sheet->setCellValue('A1', 'USUARIO ' . $this->session->userdata('first_name') . ' ' . $this->session->userdata('last_name'));
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

    // encabezado y pie de página
    $datatime = date('Y-m-d h:i:s');
    $sheet->getHeaderFooter()
    ->setOddHeader('&L' . $datatime .'&C&HECOMSOFT&R'.$data['user_name']);
    $sheet->getHeaderFooter()
    ->setOddFooter('&RPage &P of &N');

    // Area o escala a mostrar en hoja 73 %
    $sheet->getPageSetup()->setScale(73); 

    $sheet->mergeCells('A3:N3');
    $sheet->setCellValue('A3', "COBROS EN LOS PRÓXIMOS 7 DÍAS");
    $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A3')->getFont()->setSize(14);
    $sheet->getStyle('A3')->getFont()->setBold(TRUE);

    // Estilo para el encabezado de las tablas
    $primaryHeaderColor = 'e25006';
    $sheet->getStyle("A3")->getFont()->getColor()->setARGB($primaryHeaderColor);
    // Estilo para el curpo de las tablas
    $tableBodyStyle = [
      'borders' => [
        'allBorders' => [
          'borderStyle' => Border::BORDER_MEDIUM,
          'color' => ['rgb' => $primaryHeaderColor]
        ]
      ],
    ];
    // tabla totales
    $row = 5;
    $startRow = $row;
    $coinItemsRow = [];
    // Total contiene todos los tipos de moneda que tienen préstamo
    // Table head
    $sheet->setCellvalue("J$row", 'MONEDA');
    $sheet->setCellvalue("K$row", 'MORA');
    $sheet->setCellvalue("L$row", 'HOY');
    $sheet->setCellvalue("M$row", 'CERCA');
    $sheet->setCellvalue("N$row", 'TOTAL');
    // Table body
    $row++;
    if (isset($data['payables'])) {
      foreach ($data['payables'] as $item) { // pintar columna total ()
        $coinItemsRow[$item->name] = $row;
        $sheet->setCellvalue("J$row", $item->name);
        $sheet->setCellValue("N$row", $item->total);
        $row++;
      }
      if (isset($data['payable_expired']))
        foreach ($data['payable_expired'] as $item) {
          $sheet->setCellvalue('K' . $coinItemsRow[$item->name], $item->total);
        }
      if (isset($data['payable_now']))
        foreach ($data['payable_now'] as $item) {
          $sheet->setCellvalue('L' . $coinItemsRow[$item->name], $item->total);
        }
      if (isset($data['payable_next']))
        foreach ($data['payable_next'] as $item) {
          $sheet->setCellvalue('M' . $coinItemsRow[$item->name], $item->total);
        }
    }
    // Estilos a la tabla
    $sheet->getStyle("J$startRow:N$startRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("J$startRow:N$startRow")->getFont()->setBold(TRUE);
    $sheet->getStyle("J$startRow:N$startRow")->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
    $sheet->getStyle("J$startRow:N$startRow")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($primaryHeaderColor);
    $sheet->getStyle("J$startRow:N" . ($row - 1))->applyFromArray($tableBodyStyle);

    // Table head content
    $row++;
    $startRow = $row;
    $sheet->setCellvalue("A$row", 'CI');
    $sheet->mergeCells("B$row:E$row");
    $sheet->setCellvalue("B$row", 'CLIENTE');
    $sheet->mergeCells("F$row:I$row");
    $sheet->setCellvalue("F$row", 'ASESOR');
    $sheet->setCellvalue("J$row", 'MONEDA');
    $sheet->setCellvalue("K$row", 'MONTO');
    $sheet->mergeCells("L$row:M$row");
    $sheet->setCellvalue("L$row", 'FECHA');
    $sheet->setCellvalue("N$row", 'ESTADO');
    // Table body content
    if (isset($data['items'])) {
      $row++;
      foreach ($data['items'] as $item) {
        $sheet->setCellvalue("A$row", $item->ci);
        $sheet->mergeCells("B$row:E$row");
        $sheet->setCellvalue("B$row", $item->customer_name);
        $sheet->mergeCells("F$row:I$row");
        $sheet->setCellvalue("F$row", $item->user_name);
        $sheet->setCellvalue("J$row", $item->coin_name);
        $sheet->setCellvalue("K$row", $item->fee_amount - $item->payed);
        $sheet->mergeCells("L$row:M$row");
        $sheet->setCellvalue("L$row", $item->date);
        $sheet->getStyle("L$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        if ($item->date == date("Y-m-d")) {
          $sheet->setCellvalue("N$row", 'HOY');
          $sheet->getStyle("N$row")->getFont()->getColor()->setARGB(Color::COLOR_DARKYELLOW);
        } elseif ($item->date < date("Y-m-d")) {
          $sheet->setCellvalue("N$row", 'MORA');
          $sheet->getStyle("N$row")->getFont()->getColor()->setARGB(Color::COLOR_DARKRED);
        } else {
          $sheet->setCellvalue("N$row", 'CERCA');
          $sheet->getStyle("N$row")->getFont()->getColor()->setARGB(Color::COLOR_DARKGREEN);
        }
        $sheet->getStyle("N$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row++;
      }
    }
    // Estilos a la tabla
    $sheet->getStyle("A$startRow:N$startRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("A$startRow:N$startRow")->getFont()->setBold(TRUE);
    $sheet->getStyle("A$startRow:N$startRow")->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
    $sheet->getStyle("A$startRow:N$startRow")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($primaryHeaderColor);
    $sheet->getStyle("A$startRow:N" . ($row - 1))->applyFromArray($tableBodyStyle);
    $sheet->getStyle("A$startRow:N$startRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $row++;
    $sheet->setCellvalue("A$row", "FILTRO: ");
    $sheet->mergeCells("B$row:N$row");
    $sheet->setCellvalue("B$row", $data['user_name']);

    $fileName = 'week_data.xlsx';
    // Guardar excel
    try {
      $writer = new Xlsx($phpExcel);
      $writer->save('public/' . $fileName);
    } catch (Exception $e) {
      print_r($e->getMessage());
      return;
    }

    // download file
    $this->load->helper('download');
    force_download('public/' . $fileName, null);
  }
}

/* End of file Payments.php */
/* Location: ./application/controllers/admin/Payments.php */