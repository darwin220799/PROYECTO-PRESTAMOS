<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include(APPPATH . "/tools/UserPermission.php");

class Coins extends CI_Controller {

  private $user_id;
  private $permission;

  public function __construct()
  {
    parent::__construct();
    $this->load->model('coins_m');
    $this->load->model('permission_m');
    $this->load->library('session');
    $this->load->library('form_validation');
    $this->session->userdata('loggedin') == TRUE || redirect('user/login');
    $this->user_id = $this->session->userdata('user_id');
    $this->permission = new Permission($this->permission_m, $this->user_id);
  }

  public function index()
  {
    if(!$this->permission->getPermission([COIN_READ], FALSE)) show_error("You don't have access to this site", 403, 'DENIED ACCESS');
    $data[COIN_UPDATE] = $this->permission->getPermission([COIN_UPDATE], FALSE);
    $data['coins'] = $this->coins_m->get();
    $data['subview'] = 'admin/coins/index';

    $this->load->view('admin/_main_layout', $data);
  }

  public function edit($id = NULL)
  {
    if(!$this->permission->getPermission([COIN_READ, COIN_CREATE], TRUE))
      show_error("You don't have access to this site", 403, 'DENIED ACCESS');
    if ($id) {
      $data['coin'] = $this->coins_m->get($id);
    } else {
      $data['coin'] = $this->coins_m->get_new();
    }

    $rules = $this->coins_m->coin_rules;
   
    $this->form_validation->set_rules($rules);

    if ($this->form_validation->run() == TRUE) {

      $coin_data = $this->coins_m->array_from_post(['name','short_name', 'symbol', 'description']);

      if ($id) {
        if($this->permission->getPermission([COIN_UPDATE], FALSE)){
          if($this->coins_m->save($coin_data, $id))
            $this->session->set_flashdata('msg', 'Moneda editada correctamente');
          else $this->session->set_flashdata('msg', 'Ecurrió un error durante el proceso');
        }else{
          $this->session->set_flashdata('msg_error', '¡No tiene permiso para modificar monedas!');
        }  
      } elseif($this->permission->getPermission([COIN_CREATE], FALSE)) {
        if($this->coins_m->save($coin_data, $id))
          $this->session->set_flashdata('msg', 'Moneda agregado correctamente');
        else $this->session->set_flashdata('msg', 'Ecurrió un error durante el proceso');
      }else{
        $this->session->set_flashdata('msg_error', '¡No tiene permiso para crear monedas!');
      }
      redirect('admin/coins');
    }
    $data['subview'] = 'admin/coins/edit';
    $this->load->view('admin/_main_layout', $data);
  }

}

/* End of file Coins.php */
/* Location: ./application/controllers/admin/Coins.php */