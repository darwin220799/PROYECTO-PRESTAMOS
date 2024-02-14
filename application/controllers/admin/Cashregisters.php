<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include(APPPATH . "/tools/UserPermission.php");

class Cashregisters extends CI_Controller {

  private $user_id;
  private $permission;

  public function __construct()
  {
    parent::__construct();
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
    $CASH_REGISTER_READ = $this->permission->getPermission([CASH_REGISTER_READ], FALSE);
    $AUTHOR_CASH_REGISTER_READ = $this->permission->getPermission([AUTHOR_CASH_REGISTER_READ], FALSE);
    if(!$CASH_REGISTER_READ && !$AUTHOR_CASH_REGISTER_READ) show_error("You don't have access to this site", 403, 'DENIED ACCESS');
    if($CASH_REGISTER_READ)
      $data['users'] = $this->db->order_by('id')->get('users')->result();
    $data['subview'] = 'admin/cashregisters/index';
    $data[CASH_REGISTER_CREATE] = $this->permission->getPermission([CASH_REGISTER_CREATE], FALSE);
    $data[AUTHOR_CASH_REGISTER_CREATE] = $this->permission->getPermission([AUTHOR_CASH_REGISTER_CREATE], FALSE);
    $this->load->view('admin/_main_layout', $data);
  }

  public function ajax_cash_registers($user_id = null)
  {
    $CASH_REGISTER_READ = $this->permission->getPermission([CASH_REGISTER_READ], FALSE);
    $AUTHOR_CASH_REGISTER_READ = $this->permission->getPermission([AUTHOR_CASH_REGISTER_READ], FALSE);
    if(!$CASH_REGISTER_READ) {
      if($AUTHOR_CASH_REGISTER_READ)
        $user_id = $this->user_id;
      else{
        $json_data = array(
          "draw"            => intval($this->input->post('draw')),
          "recordsTotal"    => intval(0),
          "recordsFiltered" => intval(0),
          "data"            => [],
        );
        echo json_encode($json_data);
        return;
      }
    }
    $start = $this->input->post('start');
		$length = $this->input->post('length');
		$search = $this->input->post('search')['value']??'';
    $columns = ['name', 'user_name', 'total_amount', 'opening_date', 'closing_date', 'status', ''];
    $columIndex = $this->input->post('order')['0']['column']??6;
    $order['column'] = $columns[$columIndex]??'';
    $order['dir'] = $this->input->post('order')['0']['dir']??'';
    $query = $this->cashregister_m->getCashRegisters($start, $length, $search, $order, $user_id);
    if(sizeof($query['data'])==0 && $start>0) $query = $this->cashregister_m->getCashRegisters(0, $length, $search, $order, $user_id);
    $json_data = array(
      "draw"            => intval($this->input->post('draw')),
      "recordsTotal"    => intval(sizeof($query['data'])), // total registros para mostrar
      "recordsFiltered" => intval($query['recordsFiltered']), // total registro en base de datos
      "data"            => $query['data'], // Registros 
    );
    echo json_encode($json_data);
  }

  /**
   * Muestra el formulario
   */
  public function create()
  {
    if(!$this->permission->getPermission([CASH_REGISTER_CREATE, AUTHOR_CASH_REGISTER_CREATE], FALSE)) show_error("You don't have access to this site", 403, 'DENIED ACCESS');
    $this->form_validation->set_rules($this->cashregister_m->rules);
    if ($this->form_validation->run() == TRUE) {
      $data['name'] = $this->input->post('name');
      $data['user_id'] = $this->user_id;
      $data['coin_id'] = $this->input->post('coin_id');
      $data['status'] = TRUE;
      $object = new DateTime();
      $date = $object->format("Y-m-d h:i:s");
      $data['opening_date'] = $date;
      try{
        $this->db->trans_begin(); 
        $cash_register_id = $this->cashregister_m->cashRegisterInsert($data);
        $datax['cash_register_id'] = $cash_register_id;
        $datax['amount'] = $this->input->post('amount');
        $datax['description'] = $this->input->post('description');
        $datax['date'] = $date;
        $this->cashregister_m->manualInputInsert($datax);
        $this->db->trans_commit();
        $this->session->set_flashdata('msg', "Se abrió la caja " . $data['name']);
        redirect("admin/cashregisters");
      }catch(Exception $e){
        $this->db->trans_rollback();
        $this->session->set_flashdata('msg_error', "¡Ocurrió un error durante el proceso! " . $e->getMessage());
        redirect("admin/cashregisters");
      }
    }else{
      $data['coins'] = $this->db->get('coins')->result()??[];
      $data['name'] = 'Caja ' . ($this->cashregister_m->getLastId()->id + 1);
      $data['subview'] = 'admin/cashregisters/create';
      $this->load->view('admin/_main_layout', $data);
    }
  }

  public function view($cash_register_id){
    $CASH_REGISTER_READ = $this->permission->getPermission([CASH_REGISTER_READ], FALSE);
    $AUTHOR_CASH_REGISTER_READ = $this->permission->getPermission([AUTHOR_CASH_REGISTER_READ], FALSE);
    if(!$CASH_REGISTER_READ && !$AUTHOR_CASH_REGISTER_READ) show_error("You don't have access to this site", 403, 'DENIED ACCESS');
    $IS_OPEN = $this->cashregister_m->cashRegisterIsOpen($cash_register_id);
    if(!$CASH_REGISTER_READ) {
      if(!($AUTHOR_CASH_REGISTER_READ && $this->cashregister_m->isAuthor($cash_register_id, $this->user_id)))
        show_error("You don't have access to this site", 403, 'DENIED ACCESS');
    }
    $data[CASH_REGISTER_UPDATE] = $this->permission->getPermission([CASH_REGISTER_UPDATE], FALSE);
    $data[AUTHOR_CASH_REGISTER_UPDATE] = $this->permission->getPermission([AUTHOR_CASH_REGISTER_UPDATE], FALSE) && $this->cashregister_m->isAuthor($cash_register_id, $this->user_id);
    $data['IS_OPEN'] = $IS_OPEN;
    $data['cash_register'] = $this->cashregister_m->getCashRegister($cash_register_id);
    if(($data['cash_register'] == null)) show_404();
    $data['subview'] = 'admin/cashregisters/view';
    $this->load->view('admin/_main_layout', $data);
  }

  public function ajax_manual_inputs($cash_register_id = 0){
    $CASH_REGISTER_READ = $this->permission->getPermission([CASH_REGISTER_READ], FALSE);
    $AUTHOR_CASH_REGISTER_READ = $this->permission->getPermission([AUTHOR_CASH_REGISTER_READ], FALSE);
    if(!$CASH_REGISTER_READ) {
      if(!($AUTHOR_CASH_REGISTER_READ && $this->cashregister_m->isAuthor($cash_register_id, $this->user_id)))
      { $json_data = array(
          "draw"            => intval($this->input->post('draw')),
          "recordsTotal"    => intval(0),
          "recordsFiltered" => intval(0),
          "data"            => [], 
        );
        echo json_encode($this->json_data);
        return;
      }
    }
    $start = $this->input->post('start');
		$length = $this->input->post('length');
		$search = $this->input->post('search')['value']??'';
    $columns = ['id', 'amount', 'description', 'date'];
    $columIndex = $this->input->post('order')['0']['column'];
    $order['column'] = $columns[$columIndex]??'';
    $order['dir'] = $this->input->post('order')['0']['dir']??'';
    $query = $this->cashregister_m->getManualInputItems($start, $length, $search, $order, $cash_register_id);
    if(sizeof($query['data'])==0 && $start>0) $query = $this->cashregister_m->getManualInputItems(0, $length, $search, $order, $cash_register_id);
    $json_data = array(
      "draw"            => intval($this->input->post('draw')),
      "recordsTotal"    => intval(sizeof($query['data'])),
      "recordsFiltered" => intval($query['recordsFiltered']),
      "data"            => $query['data']
    );
    echo json_encode($json_data);
  }

  public function ajax_manual_outputs($cash_register_id = 0){
    $CASH_REGISTER_READ = $this->permission->getPermission([CASH_REGISTER_READ], FALSE);
    $AUTHOR_CASH_REGISTER_READ = $this->permission->getPermission([AUTHOR_CASH_REGISTER_READ], FALSE);
    if(!$CASH_REGISTER_READ) {
      if(!($AUTHOR_CASH_REGISTER_READ && $this->cashregister_m->isAuthor($cash_register_id, $this->user_id)))
      { $json_data = array(
          "draw"            => intval($this->input->post('draw')),
          "recordsTotal"    => intval(0),
          "recordsFiltered" => intval(0),
          "data"            => [],
        );
        echo json_encode($json_data);
        return;
      }
    }
    $start = $this->input->post('start');
		$length = $this->input->post('length');
		$search = $this->input->post('search')['value']??'';
    $columns = ['id', 'amount', 'description', 'date'];
    $columIndex = $this->input->post('order')['0']['column'];
    $order['column'] = $columns[$columIndex]??'';
    $order['dir'] = $this->input->post('order')['0']['dir']??'';
    $query = $this->cashregister_m->getManualOutputItems($start, $length, $search, $order, $cash_register_id);
    if(sizeof($query['data'])==0 && $start>0) $query = $this->cashregister_m->getManualOutputItems(0, $length, $search, $order, $cash_register_id);
    $json_data = array(
      "draw"            => intval($this->input->post('draw')),
      "recordsTotal"    => intval(sizeof($query['data'])),
      "recordsFiltered" => intval($query['recordsFiltered']),
      "data"            => $query['data']
    );
    echo json_encode($json_data);
  }

  public function ajax_document_payments($cash_register_id = 0){
    $CASH_REGISTER_READ = $this->permission->getPermission([CASH_REGISTER_READ], FALSE);
    $AUTHOR_CASH_REGISTER_READ = $this->permission->getPermission([AUTHOR_CASH_REGISTER_READ], FALSE);
    if(!$CASH_REGISTER_READ) {
      if(!($AUTHOR_CASH_REGISTER_READ && $this->cashregister_m->isAuthor($cash_register_id, $this->user_id)))
      { $json_data = array(
          "draw"            => intval($this->input->post('draw')),
          "recordsTotal"    => intval(0),
          "recordsFiltered" => intval(0),
          "data"            => [],
        );
        echo json_encode($json_data);
        return;
      }
    }
    $start = $this->input->post('start');
		$length = $this->input->post('length');
		$search = $this->input->post('search')['value']??'';
    $columns = ['id', 'customer_name', 'amount', 'pay_date'];
    $columIndex = $this->input->post('order')['0']['column']??1;
    $order['column'] = $columns[$columIndex]??'';
    $order['dir'] = $this->input->post('order')['0']['dir']??'';
    $query = $this->cashregister_m->getDocumentPaymentInputItems($start, $length, $search, $order, $cash_register_id);
    if(sizeof($query['data'])==0 && $start>0) $query = $this->cashregister_m->getDocumentPaymentInputItems(0, $length, $search, $order, $cash_register_id);
    $json_data = array(
      "draw"            => intval($this->input->post('draw')),
      "recordsTotal"    => intval(sizeof($query['data'])),
      "recordsFiltered" => intval($query['recordsFiltered']),
      "data"            => $query['data']
    );
    echo json_encode($json_data);
  }

  public function ajax_loans($cash_register_id = 0){
    $CASH_REGISTER_READ = $this->permission->getPermission([CASH_REGISTER_READ], FALSE);
    $AUTHOR_CASH_REGISTER_READ = $this->permission->getPermission([AUTHOR_CASH_REGISTER_READ], FALSE);
    if(!$CASH_REGISTER_READ) {
      if(!($AUTHOR_CASH_REGISTER_READ && $this->cashregister_m->isAuthor($cash_register_id, $this->user_id)))
      { $json_data = array(
          "draw"            => intval($this->input->post('draw')),
          "recordsTotal"    => intval(0),
          "recordsFiltered" => intval(0),
          "data"            => [],
        );
        echo json_encode($json_data);
        return;
      }
    }
    $start = $this->input->post('start');
		$length = $this->input->post('length');
		$search = $this->input->post('search')['value']??'';
    $columns = ['id', 'customer_name', 'credit_amount', 'date'];
    $columIndex = $this->input->post('order')['0']['column']??1;
    $order['column'] = $columns[$columIndex]??'';
    $order['dir'] = $this->input->post('order')['0']['dir']??'';
    $query = $this->cashregister_m->getLoanOutputItems($start, $length, $search, $order, $cash_register_id);
    if(sizeof($query['data'])==0 && $start>0) $query = $this->cashregister_m->getLoanOutputItems(0, $length, $search, $order, $cash_register_id);
    $json_data = array(
      "draw"            => intval($this->input->post('draw')),
      "recordsTotal"    => intval(sizeof($query['data'])),
      "recordsFiltered" => intval($query['recordsFiltered']),
      "data"            => $query['data']
    );
    echo json_encode($json_data);
  }

  /**
   * Crear una entrana manual
   */
  public function manual_input_create($cash_register_id) {
    $CASH_REGISTER_UPDATE = $this->permission->getPermission([CASH_REGISTER_UPDATE], FALSE);
    $AUTHOR_CASH_REGISTER_UPDATE = $this->permission->getPermission([AUTHOR_CASH_REGISTER_UPDATE], FALSE);
    $IS_OPEN = $this->cashregister_m->cashRegisterIsOpen($cash_register_id);
    if(($CASH_REGISTER_UPDATE || ($AUTHOR_CASH_REGISTER_UPDATE && $this->cashregister_m->isAuthor($cash_register_id, $this->user_id))) && $IS_OPEN){
      $data['amount'] = $this->input->post('amount');
      $data['description'] = $this->input->post('description');
      $data['cash_register_id'] = $cash_register_id; // $this->input->post('cash_register_id');
      $object = new DateTime();
      $date = $object->format("Y-m-d h:i:s");
      $data['date'] = $date;
      // $this->permission->getPermission([CASH_REGISTER_UPDATE, AUTHOR_CASH_REGISTER_UPDATE], TRUE);
      $this->form_validation->set_rules($this->cashregister_m->manualInputRule);
      if ($this->form_validation->run()){
        $this->cashregister_m->manualInputInsert($data);
        $this->session->set_flashdata('msg', 'Se agregó el monto manual');
        redirect("admin/cashregisters/view/$cash_register_id");
      }else{
        $query = $this->cashregister_m->getCashRegisterBasicData($cash_register_id);
        $data['cash_register_id'] = $cash_register_id;
        if(isset($query)){
          $data['cash_register_name'] = $query->name;
          $data['coin_short_name'] = $query->coin_short_name;
        }
        $data['subview'] = 'admin/cashregisters/manual_input_create';
        $this->load->view('admin/_main_layout', $data);
      }
    }else{
      show_error("You don't have access to this site", 403, 'DENIED ACCESS');
    }
      
  }

  /**
   * Crear una salida manual
   */
  public function manual_output_create($cash_register_id = 0) {
    $CASH_REGISTER_UPDATE = $this->permission->getPermission([CASH_REGISTER_UPDATE], FALSE);
    $AUTHOR_CASH_REGISTER_UPDATE = $this->permission->getPermission([AUTHOR_CASH_REGISTER_UPDATE], FALSE);
    $IS_OPEN = $this->cashregister_m->cashRegisterIsOpen($cash_register_id);
    if(($CASH_REGISTER_UPDATE || ($AUTHOR_CASH_REGISTER_UPDATE && $this->cashregister_m->isAuthor($cash_register_id, $this->user_id))) && $IS_OPEN){

      $data['amount'] = $this->input->post('amount');
      $data['description'] = $this->input->post('description');
      $data['cash_register_id'] = $cash_register_id;
      $object = new DateTime();
      $date = $object->format("Y-m-d h:i:s");
      $data['date'] = $date;
      $existAmount = isset($data['amount'])?($this->cashregister_m->getTotal($cash_register_id) >= $data['amount']):TRUE;
      $this->form_validation->set_rules($this->cashregister_m->manualOutputRule);
      if ($this->form_validation->run() && $existAmount){
        $this->cashregister_m->manualOutputInsert($data);
        $this->session->set_flashdata('msg', 'Se extrajo el monto de caja');
        redirect("admin/cashregisters/view/$cash_register_id");
      }else{
        if(!$existAmount){
          $this->session->set_flashdata('msg_error', 'La caja no contiene el monto suficiente');
        } 
        $query = $this->cashregister_m->getCashRegisterBasicData($cash_register_id);
        $data['cash_register_id'] = $cash_register_id;
        if(isset($query)){
          $data['cash_register_name'] = $query->name;
          $data['coin_short_name'] = $query->coin_short_name;
        }
        $data['subview'] = 'admin/cashregisters/manual_output_create';
        $this->load->view('admin/_main_layout', $data);
      }

    }else{
      show_error("You don't have access to this site", 403, 'DENIED ACCESS');
    }
  }

  public function close_cash_register($cash_register_id){
    $CASH_REGISTER_UPDATE = $this->permission->getPermission([CASH_REGISTER_UPDATE], FALSE);
    $AUTHOR_CASH_REGISTER_UPDATE = $this->permission->getPermission([AUTHOR_CASH_REGISTER_UPDATE], FALSE);
    if($CASH_REGISTER_UPDATE || ($AUTHOR_CASH_REGISTER_UPDATE && $this->cashregister_m->isAuthor($cash_register_id, $this->user_id))){
      $data['status'] = 0;
      $object = new DateTime();
      $data['closing_date'] = $object->format("Y-m-d h:i:s");
      $this->cashregister_m->closeCashRegister($cash_register_id, $data);
      redirect("admin/cashregisters/view/$cash_register_id");
    }else{
      show_error("You don't have access to this site", 403, 'DENIED ACCESS');
    }
  }
}

/* End of file Coins.php */
/* Location: ./application/controllers/admin/Coins.php */