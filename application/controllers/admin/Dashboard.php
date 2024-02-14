<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include(APPPATH . "/tools/UserPermission.php");

class Dashboard extends CI_Controller {

  private $user_id;
  private $permission;

  public function __construct()
  {
    parent::__construct();
    $this->load->model('config_m');
    $this->load->model('permission_m');
    $this->load->library('session');
    $this->session->userdata('loggedin') == TRUE || redirect('user/login');
    $this->user_id = $this->session->userdata('user_id');  
    $this->permission = new Permission($this->permission_m, $this->user_id);
  }

  public function index($user_id = 0)
  {
    $data['qCts'] = [];
    $data['qLoans'] = [];
    $data['qPaids'] = [];
    $count_lc = [];
    if($this->permission->getPermission([LOAN_READ], FALSE)){
      $data['users'] = $this->db->get('users')->result();
      $data['selected_user_id'] = $user_id;
      if($user_id == 0 || !is_numeric($user_id)){
        $data['qCts'] = $this->config_m->get_countCtsAll();
        $data['qLoans'] = $this->config_m->get_countLoansAll();
        $data['qPaids'] = $this->config_m->get_countPaidsAll();
        $count_lc = $this->config_m->get_countLCAll();
      }else{
        $data['qCts'] = $this->config_m->get_countCts($user_id);
        $data['qLoans'] = $this->config_m->get_countLoans($user_id);
        $data['qPaids'] = $this->config_m->get_countPaids($user_id);
        $count_lc = $this->config_m->get_countLC($user_id);
      }
    }elseif($this->permission->getPermission([AUTHOR_LOAN_READ], FALSE)){
      $data['qCts'] = $this->config_m->get_countCts($this->user_id);
      $data['qLoans'] = $this->config_m->get_countLoans($this->user_id);
      $data['qPaids'] = $this->config_m->get_countPaids($this->user_id);
      $count_lc = $this->config_m->get_countLC($this->user_id);
    }

    $data_lc = [];
 
    foreach($count_lc as $row) {
      $data_lc['label'][] = $row->name;
      $data_lc['data'][] = (int) $row->total;
    }

    $data['countLC'] = json_encode($data_lc);

    $data['subview'] = 'admin/index';
    $this->load->view('admin/_main_layout', $data);
  }

}

/* End of file Dashboard.php */
/* Location: ./application/controllers/admin/Dashboard.php */