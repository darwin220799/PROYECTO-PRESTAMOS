<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Config_m extends MY_Model {

  protected $_table_name = 'users';

  public $config_rules = array(
    array(
      'field' => 'first_name',
      'label' => 'nombre(s)',
      'rules' => 'trim|required'
    ),
    array(
      'field' => 'last_name',
      'label' => 'apellido(s)',
      'rules' => 'trim|required'
    ),
    array(
      'field' => 'email',
      'label' => 'correo electronico',
      'rules' => 'trim|required'
    )
  );

  public $password_rules = array(
    array(
      'field' => 'old_password',
      'label' => 'contraseña anterior',
      'rules' => 'trim|required|callback__password_verify'
    ),
    array(
      'field' => 'new_password',
      'label' => 'nueva contraseña',
      'rules' => 'trim|matches[confirm_password]'
    ),
    array(
      'field' => 'confirm_password',
      'label' => 'confirmar contraseña',
      'rules' => 'trim|required'
    )
  );

  public function get_countCtsAll()
  {
    $this->db->select("count(*) as cantidad");
    $this->db->from('customers');
    return $this->db->get()->row(); 
  }

  public function get_countCts($user_id)
  {
    $this->db->select("count(*) as cantidad");
    $this->db->from('customers');
    $this->db->where("(user_id = $user_id)");
    return $this->db->get()->row(); 
  }

  public function get_countLoansAll()
  {
    return $this->get_count_loans_state_all(TRUE);
  }

  public function get_countLoans($user_id)
  {
    return $this->get_count_loans_state($user_id, TRUE);
  }

  public function get_countPaidsAll()
  {
    return $this->get_count_loans_state_all(FALSE);
  }

  public function get_countPaids($user_id)
  {
    return $this->get_count_loans_state($user_id, FALSE);
  }

  public function get_countLCAll()
  {
    $this->db->select("c.name, c.short_name, count(l.id) as total");
    $this->db->from('loans l');
    $this->db->join('coins c', 'c.id = l.coin_id');
    $this->db->join('customers cu', 'cu.id = l.customer_id');
    $this->db->group_by('l.coin_id');
    return $this->db->get()->result();
  }

  public function get_countLC($user_id)
  {
    $this->db->select("c.name, c.short_name, count(l.id) as total");
    $this->db->from('loans l');
    $this->db->join('coins c', 'c.id = l.coin_id');
    $this->db->join('customers cu', 'cu.id = l.customer_id');
    $this->db->join('users u', 'u.id = cu.user_id');
    $this->db->group_by('l.coin_id');
    $this->db->where("u.id = $user_id");
    return $this->db->get()->result();
  }

  private function get_count_loans_state_all($status){
    $status = $status?'TRUE':'FALSE';
    $this->db->select("count(*) as cantidad");
    $this->db->from("loans l");
    $this->db->where("l.status = $status");
    return $this->db->get()->row();
  }

  private function get_count_loans_state($user_id, $status){
    $status = $status?'TRUE':'FALSE';
    $this->db->select("count(*) as cantidad");
    $this->db->from("loans l");
    $this->db->join("customers c","c.id = l.customer_id");
    $this->db->join("users u", "u.id = c.user_id");
    $this->db->group_by("l.status");
    $this->db->where("u.id = $user_id");
    $this->db->where("l.status = $status");
    return $this->db->get()->row();
  }

}

/* End of file config_m.php */
/* Location: ./application/models/config_m.php */