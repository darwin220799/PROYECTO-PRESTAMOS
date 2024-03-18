<?php

use LDAP\Result;
include(APPPATH."/models/IAuthor.php");

defined('BASEPATH') OR exit('No direct script access allowed');

class Customers_m extends MY_Model implements IAuthor {

  protected $_table_name = 'customers';
  protected $id = 'id';

  public $customer_rules = array(
    array(
      'field' => 'ci',
      'label' => 'CI',
      'rules' => 'trim|required|is_unique[customers.ci]|max_length[20]',
    ),
    array(
      'field' => 'first_name',
      'label' => 'nombre(s)',
      'rules' => 'trim|required|max_length[150]'
    ),
    array(
      'field' => 'last_name',
      'label' => 'apellido(s)',
      'rules' => 'trim|required|max_length[150]'
    )
  );

  public $customer_rules_x = array(
    array(
      'field' => 'ci',
      'label' => 'CI',
      'rules' => 'trim|required|max_length[20]'
    ),
    array(
      'field' => 'first_name',
      'label' => 'nombre(s)',
      'rules' => 'trim|required|max_length[150]'
    ),
    array(
      'field' => 'last_name',
      'label' => 'apellido(s)',
      'rules' => 'trim|required|max_length[150]'
    )
  );

  public function get_new()
  {
    $customer = new stdClass(); //clase vacia
    $customer->ci = '';
    $customer->first_name = '';
    $customer->last_name = '';
    $customer->gender = 'none';
    $customer->address = '';
    $customer->mobile = '';
    $customer->phone = '';
    $customer->business_name = '';
    $customer->nit = '';
    $customer->company = '';
    $customer->user_id = '';
    $customer->tipo_cliente = 'normal';
    return $customer;
  }

  public function getCustomers($start, $length, $search, $order, $user_id)
  {
    $this->db->select("COUNT(c.id) recordsFiltered");
    $this->db->from('customers c');
    $this->db->where("(c.ci LIKE '%$search%' OR CONCAT_WS('', c.first_name, c.last_name) LIKE '%$search%' OR
    c.company LIKE '%$search%' OR c.mobile LIKE '%$search%' OR c.loan_status LIKE '%$search%')");
    if($user_id != 'all' && $user_id != null)
      $this->db->where('c.user_id', $user_id);
    $data['recordsFiltered'] = $this->db->get()->row()->recordsFiltered??0;

    $this->db->select("c.id, c.ci, c.first_name, c.last_name, c.mobile, c.company, c.tipo_cliente, c.loan_status, c.user_id");
    $this->db->from('customers c');
    $this->db->where("(c.ci LIKE '%$search%' OR CONCAT_WS(' ', c.first_name, c.last_name) LIKE '%$search%' OR
    c.company LIKE '%$search%' OR c.mobile LIKE '%$search%' OR c.loan_status LIKE '%$search%')");
    if($user_id != 'all' && $user_id != null)
      $this->db->where('c.user_id', $user_id);
    if($order['column'] == 'name')
    {
      $this->db->order_by('c.first_name', $order['dir']);
      $this->db->order_by('c.last_name', $order['dir']);
    } else
      $this->db->order_by($order['column'], $order['dir']);
    $this->db->limit($length, $start);
    $data['data'] = $this->db->get()->result()??[];
    return $data;
  }

  public function getCustomerByIdInAll($customer_id){
    $this->db->select('*');
    $this->db->from('customers');
    $this->db->where("id" ,$customer_id);
    $query = $this->db->get();
    if ($query->num_rows() > 0)
      return $query->row();
    else
      return null;
  }

  public function getCustomerById($user_id, $customer_id){
    $this->db->select('*');
    $this->db->from('customers');
    $this->db->where("(user_id = $user_id AND id = $customer_id)");
    $query = $this->db->get();
    if ($query->num_rows() > 0)
      return $query->row();
    else
      return null;
  }

  public function delete($customer_id){
    return $this->db->delete('customers', array('id'=>$customer_id));
  }

  public function getAuthorId($model_id){
    $this->db->select('c.user_id');
    $this->db->from('customers c');
    $this->db->where('id', $model_id);
    return $this->db->get()->row();
  }

}

/* End of file Customers_m.php */
/* Location: ./application/models/Customers_m.php */