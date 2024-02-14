<?php
defined('BASEPATH') or exit('No direct script access allowed');
include(APPPATH . "/tools/UserPermission.php");

class Loans extends CI_Controller
{
  private $permission;
  private $user_id;

  public function __construct()
  {
    parent::__construct();
    $this->load->model('loans_m');
    $this->load->model('customers_m');
    $this->load->model('cashregister_m');
    $this->load->model('permission_m');
    $this->load->library('session');
    $this->load->library('form_validation');
    $this->session->userdata('loggedin') == TRUE || redirect('user/login');
    $this->user_id = $this->session->userdata('user_id');
    $this->permission = new Permission($this->permission_m, $this->user_id);
  }

  public function index()
  {
    $data[LOAN_CREATE] = $this->permission->getPermission([LOAN_CREATE], FALSE);
    $data[AUTHOR_LOAN_CREATE] = $this->permission->getPermission([AUTHOR_LOAN_CREATE], FALSE);
    if ($this->permission->getPermission([LOAN_READ], FALSE))
      $data['users'] = $this->db->order_by('id')->get('users')->result();
    if(!$data[LOAN_CREATE] && !$data[AUTHOR_LOAN_CREATE])
      show_error("You don't have access to this site", 403, 'DENIED ACCESS');
    $data['subview'] = 'admin/loans/index';
    $this->load->view('admin/_main_layout', $data);
  }

  public function ajax_loans($user_id = null){
    $LOAN_READ = $this->permission->getPermission([LOAN_READ], FALSE);
    $AUTHOR_LOAN_READ = $this->permission->getPermission([AUTHOR_LOAN_READ], FALSE);
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
    $columns = ['id', 'customer', 'credit_amount', 'interest', 'total', 'coin_short_name', 'status', ''];
    $columIndex = $this->input->post('order')['0']['column']??7;
    $order['column'] = $columns[$columIndex]??'';
    $order['dir'] = $this->input->post('order')['0']['dir']??'';
    $query = $this->loans_m->findAll($start, $length, $search, $order, $user_id);
    if(sizeof($query['data'])==0 && $start>0) $query = $this->loans_m->findAll(0, $length, $search, $order, $user_id);
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
    if (
      $this->permission->getPermission([AUTHOR_LOAN_CREATE], FALSE) ||
      $this->permission->getPermission([LOAN_CREATE], FALSE)
    ) {
      $data['coins'] = $this->loans_m->getCoins();
      if ($this->permission->getPermissionX([LOAN_CREATE], FALSE)) {
        $data['customers'] = $this->loans_m->getCustomersAll();
      } else {
        $data['customers'] = $this->loans_m->getCustomers($this->user_id);
      }

      $rules = $this->loans_m->loan_rules;

      $this->form_validation->set_rules($rules);

      if ($this->form_validation->run() == TRUE) {
        // inicio fechas
        $items = $this->getTimelime($this->input->post('num_fee'), $this->input->post('payment_m'), $this->input->post('fee_amount'), $this->input->post('date'));
        // fin fechas
        $loan_data = $this->loans_m->array_from_post(['customer_id', 'credit_amount', 'interest_amount', 'num_fee', 'fee_amount', 'payment_m', 'coin_id', 'cash_register_id', 'date']);
        $guarantors_list = $this->input->post('guarantors');
        $guarantors = [];
        if ($guarantors_list != null) // validar que el cliente seleccionado no exista en la lista de garantes
          for ($i = 0; $i < sizeof($guarantors_list); $i++) {
            if ($guarantors_list[$i] != $loan_data["customer_id"])
              $guarantors[$i] = $this->input->post('guarantors')[$i];
          }
        if ($loan_data['customer_id'] > 0) {
          if ($this->permission->getPermission([LOAN_CREATE], FALSE))
            $customer = $this->customers_m->getCustomerByIdInAll($loan_data['customer_id']);
          elseif ($this->permission->getPermissionX([AUTHOR_LOAN_CREATE], FALSE))
            $customer = $this->customers_m->getCustomerById($this->user_id, $loan_data['customer_id']);
          if ($customer != null) {
            if ($this->guarantorsValidation($customer->user_id, $guarantors)) {
              if ($this->formValidation($this->input)) {
                $this->cashRegisterValidation($this->input, $this->user_id);
                if ($this->loans_m->addLoan($loan_data, $items, $guarantors)) {
                  $this->session->set_flashdata('msg', 'Préstamo agregado correctamente');
                  redirect('admin/loans');
                } else {
                  $this->session->set_flashdata('msg_error', 'Ocurrió un error al guardar, intente nuevamente');
                }
              } else {
                $this->session->set_flashdata('msg_error', 'ERROR: ¡La información del formulario enviado no es consistente, intente nuevamente!');
              }
            } else {
              $this->session->set_flashdata('msg_error', '¡Uno o más garantes no existen en el equipo del asesor!');
            }
          } else {
            $this->session->set_flashdata('msg_error', '¡El cliente no existe!');
          }
        } else {
          $this->session->set_flashdata('msg_error', '¡No se seleccionó un cliente!');
        }
        redirect('admin/loans/edit');
      } else {
        $data['data'] = $this->loans_m->array_from_post(['customer_id', 'credit_amount', 'interest_amount', 'num_fee', 'fee_amount', 'payment_m', 'coin_id', 'cash_register_id', 'date']);
        $data['subview'] = 'admin/loans/edit';
        $this->load->view('admin/_main_layout', $data);
      }
    } else {
      show_error("You don't have access to this site", 403, 'DENIED ACCESS');
    }
  } // fin edit

  /**
   * Valida los datos de entrada, para constatar de que el cálculo es correcto
   * Criterios de validación (num_fee, fee_amount)
   */
  private function formValidation($input)
  {
    $credit_amount = $input->post('credit_amount');
    $payment = $input->post('payment_m');
    $time = $input->post('time');
    $interest_amount = $input->post('interest_amount');
    $num_fee = 0;
    if (strtolower($payment) == 'mensual') {
      $num_fee = $time * 1;
    } else if (strtolower($payment) == 'quincenal') {
      $num_fee = $time * 2;
    } else if (strtolower($payment) == 'semanal') {
      $num_fee = $time * 4;
    } else if (strtolower($payment) == 'diario') {
      $num_fee = $time * 30;
    } else {
      $num_fee = 0;
    }
    $i = ($interest_amount / 100);
    $I = $credit_amount * $i * $time;
    $monto_total = $I + $credit_amount;
    $cuota = round($monto_total / $num_fee, 2);
    if ($cuota == $input->post('fee_amount') && $num_fee == $input->post('num_fee')) {
      return true;
    } else {
      return false;
    }
  }

  public function get_timeline($num_fee, $payment_m, $fee_amount, $date)
  {
    echo json_encode($this->getTimelime($num_fee, $payment_m, $fee_amount, $date));
  }

  private function getTimelime($num_fee, $payment_m, $fee_amount, $date)
  {
    $items = [];
    if ($num_fee != null && $payment_m != null && $fee_amount != null) {
      // inicio fechas
      if ($payment_m == 'diario')
        $p = 'P1D';
      if ($payment_m == 'semanal')
        $p = 'P7D';
      if ($payment_m == 'quincenal')
        $p = 'P14D';
      if ($payment_m == 'mensual')
        $p = 'P1M';
      // definir periodo de fechas
      $period = new DatePeriod(
        new DateTime($date), // Donde empezamos a contar el periodo
        new DateInterval($p), // Definimos el periodo a 1 día, 1mes
        $num_fee, // Aplicamos el numero de repeticiones
        DatePeriod::EXCLUDE_START_DATE
      );
      $num_quota = 1;
      foreach ($period as $date) {
        $isSunday = $date->format('N') == 7 ? TRUE : FALSE;
        if ($isSunday)
          $date->add(new DateInterval('P1D'));
        $fomattedDate = $date->format('Y-m-d');

        $items[] = array(
          'date' => $fomattedDate,
          'num_quota' => $num_quota++,
          'fee_amount' => $fee_amount
        );
      }
    }
    return $items;
  }

  private function cashRegisterValidation($input, $user_id)
  {
    $cash_register_id = $input->post('cash_register_id');
    $coin_id = $input->post('coin_id');
    $credit_amount = $input->post('credit_amount');
    $errors = [];
    if (
      ($this->permission->getPermission([AUTHOR_CASH_REGISTER_UPDATE], FALSE) && $this->cashregister_m->isAuthor($cash_register_id, $user_id))
      || $this->permission->getPermission([CASH_REGISTER_UPDATE], FALSE)
    ) {
      if (!$this->cashregister_m->isCoinType($cash_register_id, $coin_id))
        array_push($errors, 'El tipo de moneda del préstamo, no coincide con el tipo de moneda de la caja');
      if (!$this->cashregister_m->isOpen($cash_register_id))
        array_push($errors, 'La caja está cerrada');
      if (sizeof($errors) == 0 && ($this->cashregister_m->getTotal($cash_register_id) < $credit_amount))
        array_push($errors, 'La caja no cuenta con el saldo sufuciente');
    } else {
      array_push($errors, 'El usuario no es autor de la caja o la caja no existe');
    }
    if (sizeof($errors) > 0) {
      $messages = '';
      foreach ($errors as $error)
        $messages .= '<li>' . $error . '</li>';
      $this->session->set_flashdata('msg_error', $messages);
      redirect("admin/loans/edit");
    }
  }

  // Valida que todos los garantes sean del mismo asesor que el cliente
  private function guarantorsValidation($user_id, $guarantors)
  {
    $valid = TRUE;
    if ($guarantors != null)
      foreach ($guarantors as $customer_id) {
        $guarantor = $this->customers_m->getCustomerByIdInAll($customer_id);
        if ($guarantor->user_id != $user_id) {
          $valid &= FALSE;
          break;
        }
      }
    return $valid;
  }

  public function view($id)
  {
    if ($this->permission->getPermissionX([LOAN_READ], FALSE)) {
      $data['loan'] = $this->loans_m->getLoanInAll($id);
      $data['items'] = $this->loans_m->getLoanItemsInAll($id);
    } elseif ($this->permission->getPermissionX([AUTHOR_LOAN_READ], FALSE)) {
      $data['loan'] = $this->loans_m->getLoan($this->session->userdata('user_id'), $id);
      $data['items'] = $this->loans_m->getLoanItems($this->session->userdata('user_id'), $id);
    }
    $this->load->view('admin/loans/view', $data);
  }

  /**
   * Sirven para actualizar caja mediante préstamos
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
}

/* End of file Loans.php */
/* Location: ./application/controllers/admin/Loans.php */