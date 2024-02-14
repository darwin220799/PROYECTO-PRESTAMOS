<?php
defined('BASEPATH') or exit('No direct script access allowed');

include(APPPATH . "/tools/UserPermission.php");

class Customers extends CI_Controller
{

  private $user_id;
  private $permission;

  public function __construct()
  {
    parent::__construct();
    $this->load->model('customers_m');
    $this->load->model('permission_m');
    $this->load->library('form_validation');
    $this->load->library('session');
    $this->session->userdata('loggedin') == TRUE || redirect('user/login');
    $this->user_id = $this->session->userdata('user_id');
    $this->permission = new Permission($this->permission_m, $this->user_id);
  }

  public function index()
  {
    if(!$this->permission->getPermission([CUSTOMER_READ, AUTHOR_CUSTOMER_READ], FALSE))
      show_error("You don't have access to this site", 403, 'DENIED ACCESS');
    // permisos del usuario [para la vista]
    $data[CUSTOMER_UPDATE] = $this->permission->getPermission([CUSTOMER_UPDATE], FALSE);
    $data[CUSTOMER_DELETE] = $this->permission->getPermission([CUSTOMER_DELETE], FALSE);
    $data[CUSTOMER_CREATE] = $this->permission->getPermission([CUSTOMER_CREATE], FALSE);
    $data[AUTHOR_CUSTOMER_UPDATE] = $this->permission->getPermission([AUTHOR_CUSTOMER_UPDATE], FALSE);
    $data[AUTHOR_CUSTOMER_DELETE] = $this->permission->getPermission([AUTHOR_CUSTOMER_DELETE], FALSE);
    $data[AUTHOR_CUSTOMER_CREATE] = $this->permission->getPermission([AUTHOR_CUSTOMER_CREATE], FALSE);
    // fin permisos del usuario [para la vista]
    if($this->permission->getPermission([CUSTOMER_READ], FALSE))
      $data['users'] = $this->db->order_by('id')->get('users')->result()??[];
    $data['subview'] = 'admin/customers/index';
    $this->load->view('admin/_main_layout', $data);
  }

  public function ajax_customers($user_id = null)
  {
    $CUSTOMER_READ = $this->permission->getPermission([CUSTOMER_READ], FALSE);
    $AUTHOR_CUSTOMER_READ = $this->permission->getPermission([AUTHOR_CUSTOMER_READ], FALSE);
    if(!$CUSTOMER_READ) {
      if(!$AUTHOR_CUSTOMER_READ)
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
    $columns = ['ci', 'name', 'mobile', 'company', 'loan_status', 'id'];
    $columIndex = $this->input->post('order')['0']['column']??5;
    $order['column'] = $columns[$columIndex]??'';
    $order['dir'] = $this->input->post('order')['0']['dir']??'';
    $query = $this->customers_m->getCustomers($start, $length, $search, $order, $user_id);
    if(sizeof($query['data'])==0 && $start>0) $query = $this->customers_m->getCustomers(0, $length, $search, $order, $user_id);
    $json_data = array(
      "draw"            => intval($this->input->post('draw')),
      "recordsTotal"    => intval(sizeof($query['data'])),
      "recordsFiltered" => intval($query['recordsFiltered']),
      "data"            => $query['data']
    );
    echo json_encode($json_data);
  }

  public function edit($id = NULL)
  {
    if ($id) {
      if($this->permission->getPermission([CUSTOMER_UPDATE], FALSE))
        $row = $this->customers_m->getCustomerByIdInAll($id);
      else
        $row = $this->customers_m->getCustomerById($this->user_id, $id);
      if ($row != null)
        $data['customer'] = $row;
      else
        $data['customer'] = $this->customers_m->get_new();
    } else {
      $data['customer'] = $this->customers_m->get_new();
    }
    $this->form_validation->set_rules($this->customers_m->customer_rules_x);
    if ($this->form_validation->run()) {
      $cst_data = $this->customers_m->array_from_post(['ci', 'first_name', 'last_name', 'gender', 'mobile', 'address', 'phone', 'business_name', 'nit', 'company', 'user_id']);
      $isSuccessfull = FALSE;
      $cst_data['first_name'] = strtoupper($cst_data['first_name']);
      $cst_data['last_name'] = strtoupper($cst_data['last_name']);
      if ($cst_data['user_id']) { // EDITAR REGISTRO
        if ($this->permission->getPermission([CUSTOMER_UPDATE], FALSE)) {
          $this->form_validation->set_rules($this->customers_m->customer_rules_x);
          if ($this->form_validation->run())
            $isSuccessfull = $this->customers_m->save($cst_data, $id);
        } elseif ($this->permission->getPermission([AUTHOR_CUSTOMER_UPDATE], FALSE)) {
          $this->form_validation->set_rules($this->customers_m->customer_rules_x);
          if ($this->form_validation->run())
            if (AuthUserData::isAuthor($cst_data["user_id"])) {
              $isSuccessfull = $this->customers_m->save($cst_data, $id);
            }
        } else {
          show_error("You don't have access to this site", 403, 'DENIED ACCESS');
        }
      } else { // NUEVO REGISTRO
        if ($this->permission->getPermission([AUTHOR_CUSTOMER_CREATE, CUSTOMER_CREATE], FALSE)) {
          if ($this->form_validation->run() == TRUE) {
            $this->form_validation->set_rules($this->customers_m->customer_rules);
            if ($this->form_validation->run() == TRUE)
              $cst_data['user_id'] = $this->user_id;
            $isSuccessfull = $this->customers_m->save($cst_data, $id);
          }
        } else {
          show_error("You don't have access to this site", 403, 'DENIED ACCESS');
        }
      }

      if ($isSuccessfull) {
        if ($id) {
          $this->session->set_flashdata('msg', 'Cliente editado correctamente');
        } else {
          $this->session->set_flashdata('msg', 'Cliente agregado correctamente');
        }
      } else {
        $this->session->set_flashdata('msg_error', 'Hubo un problema al procesar los datos, intente nuevamente...');
      }
      redirect('admin/customers');
    }
    $data['subview'] = 'admin/customers/edit';
    $this->load->view('admin/_main_layout', $data);
  }

  public function delete($id)
  {
    if ($this->permission->getPermission([CUSTOMER_DELETE], FALSE)) {
      if ($this->customers_m->delete($id)==TRUE) $this->session->set_flashdata('msg', 'Se eliminó correctamente');
      else $this->session->set_flashdata('msg_error', '!Ops, algo salió mal¡');
    } elseif ($this->permission->getPermission([AUTHOR_CUSTOMER_DELETE], FALSE)) {
      if (AuthUserData::isAuthorX($this->customers_m, $id)) {
        if ($this->customers_m->delete($id)==TRUE) $this->session->set_flashdata('msg', 'Se eliminó correctamente');
        else $this->session->set_flashdata('msg_error', '!Ops, algo salió mal¡');
      } else {
        show_error("You don't have access to this site", 403, 'DENIED ACCESS');
      }
    } else show_error("You don't have access to this site", 403, 'DENIED ACCESS');
    redirect('admin/customers');
  }
}


/* End of file Customers.php */
/* Location: ./application/controllers/admin/Customers.php */