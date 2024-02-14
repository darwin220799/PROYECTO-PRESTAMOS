<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Loans_m extends MY_Model
{

  protected $_table_name = 'loans';

  public $loan_rules = array(
    array(
      'field' => 'customer_id',
      'rules' => 'trim|required',
      'errors' => array(
        'required' => 'buscar persona para realizar préstamo',
      )
    ),
    array(
      'field' => 'cash_register_id',
      'label' => 'caja',
      'rules' => 'trim|required',
    )
  );


  // public function getLoansAll()
  // {
  //   $this->db->select("l.id, CONCAT(c.first_name, ' ', c.last_name) AS customer, l.credit_amount, l.interest_amount, co.short_name, l.status, l.payment_m, l.num_fee, c.user_id");
  //   $this->db->from('loans l');
  //   $this->db->join('customers c', 'c.id = l.customer_id', 'left');
  //   $this->db->join('coins co', 'co.id = l.coin_id', 'left');
  //   $this->db->join('users u', 'u.id = c.user_id');
  //   $this->db->order_by('l.id', 'desc');
  //   return $this->db->get()->result();
  // }

  // public function getLoans($user_id)
  // {
  //   $this->db->select("l.id, CONCAT(c.first_name, ' ', c.last_name) AS customer, l.credit_amount, l.interest_amount, co.short_name, l.status, l.payment_m, l.num_fee, c.user_id");
  //   $this->db->from('loans l');
  //   $this->db->join('customers c', 'c.id = l.customer_id', 'left');
  //   $this->db->join('coins co', 'co.id = l.coin_id', 'left');
  //   $this->db->join('users u', 'u.id = c.user_id');
  //   $this->db->order_by('l.id', 'desc');
  //   $this->db->where("u.id = $user_id");
  //   return $this->db->get()->result();
  // }

  public function findAll($start, $length, $search, $order, $user_id)
  {
    $time = "
      IF(
        l.payment_m = 'mensual', 1, 
        IF(l.payment_m = 'quincenal', 2,
          IF(l.payment_m = 'semanal', 4, 
            IF(l.payment_m = 'diario', 30, 0)
          )
        )
      )
    ";
    $interest = "
    ROUND(l.credit_amount * (l.interest_amount/100) * (l.num_fee / $time))
    ";
    $total = "
      (l.credit_amount + $interest)
    ";

    $this->db->select("COUNT(l.id) recordsFiltered");
    $this->db->from('loans l');
    $this->db->join('customers c', 'c.id = l.customer_id', 'left');
    $this->db->join('coins co', 'co.id = l.coin_id', 'left');
    $this->db->join('users u', 'u.id = c.user_id');
    $this->db->where("(l.id LIKE '%$search%' OR CONCAT(c.first_name, ' ', c.last_name) LIKE '%$search%' OR
    l.credit_amount LIKE '%$search%' OR $interest LIKE '%$search%' OR $total LIKE '%$search%' OR
    co.short_name LIKE '%$search%' OR l.status LIKE '%$search%')");
    if ($user_id != 'all' && $user_id != null)
      $this->db->where('u.id', $user_id);
    $data['recordsFiltered'] = $this->db->get()->row()->recordsFiltered ?? 0;

    $this->db->select("l.id, CONCAT(c.first_name, ' ', c.last_name) AS customer, 
    ROUND(l.credit_amount, 2) credit_amount, $interest interest, $total total, co.short_name coin_short_name, 
    l.status");
    $this->db->from('loans l');
    $this->db->join('customers c', 'c.id = l.customer_id', 'left');
    $this->db->join('coins co', 'co.id = l.coin_id', 'left');
    $this->db->join('users u', 'u.id = c.user_id');
    $this->db->where("(l.id LIKE '%$search%' OR CONCAT(c.first_name, ' ', c.last_name) LIKE '%$search%' OR
    l.credit_amount LIKE '%$search%' OR $interest LIKE '%$search%' OR $total LIKE '%$search%' OR
    co.short_name LIKE '%$search%' OR l.status LIKE '%$search%')");
    if ($user_id != 'all' && $user_id != null)
      $this->db->where('u.id', $user_id);
    $this->db->order_by($order['column'], $order['dir']);
    $this->db->limit($length, $start);
    $data['data'] = $this->db->get()->result() ?? [];

    return $data;
  }

  public function getcoins()
  {
    return $this->db->get('coins')->result();
  }

  public function getSearchCst($user_id, $ci)
  {
    $this->db->where('ci', $ci);
    $this->db->where('user_id', $user_id);
    return $this->db->get('customers')->row();
  }

  public function addLoan($data, $items, $guarantors)
  {

    if ($this->db->insert('loans', $data)) {
      $loan_id = $this->db->insert_id();

      $this->db->where('id', $data['customer_id']);
      $this->db->update('customers', ['loan_status' => 1]);

      foreach ($items as $item) {
        $item['loan_id'] = $loan_id;
        $this->db->insert('loan_items', $item);
      }

      if ($guarantors != null) {
        foreach ($guarantors as $customer_id) {
          $datax['customer_id'] = $customer_id;
          $datax['loan_id'] = $loan_id;
          $this->db->insert('guarantors', $datax);
        }
      }
      return true;
    }

    return false;
  }

  public function getLoanInAll($loan_id)
  {
    $this->db->select("l.*, CONCAT(c.first_name, ' ', c.last_name) AS customer_name, co.short_name");
    $this->db->from('loans l');
    $this->db->join('customers c', 'c.id = l.customer_id', 'left');
    $this->db->join('coins co', 'co.id = l.coin_id', 'left');
    $this->db->join('users u', 'u.id = c.user_id', 'left');
    $this->db->where('l.id', $loan_id);
    return $this->db->get()->row();
  }

  public function getLoan($user_id, $loan_id)
  {
    $this->db->select("l.*, CONCAT(c.first_name, ' ', c.last_name) AS customer_name, co.short_name");
    $this->db->from('loans l');
    $this->db->join('customers c', 'c.id = l.customer_id', 'left');
    $this->db->join('coins co', 'co.id = l.coin_id', 'left');
    $this->db->join('users u', 'u.id = c.user_id', 'left');
    $this->db->where('l.id', $loan_id);
    $this->db->where('u.id', $user_id);
    return $this->db->get()->row();
  }

  public function getLoanItemsInAll($loan_id)
  {
    $this->db->select('li.id, li.loan_id, li.date, li.num_quota, li.fee_amount, li.pay_date, li.status');
    $this->db->from('loan_items li');
    $this->db->join('loans l', 'l.id = li.loan_id');
    $this->db->join('customers c', 'c.id = l.customer_id');
    $this->db->join('users u', 'u.id = c.user_id');
    $this->db->where('l.id', $loan_id);
    return $this->db->get()->result();
  }

  public function getLoanItems($user_id, $loan_id)
  {
    $this->db->select('li.id, li.loan_id, li.date, li.num_quota, li.fee_amount, li.pay_date, li.status');
    $this->db->from('loan_items li');
    $this->db->join('loans l', 'l.id = li.loan_id');
    $this->db->join('customers c', 'c.id = l.customer_id');
    $this->db->join('users u', 'u.id = c.user_id');
    $this->db->where('l.id', $loan_id);
    $this->db->where('u.id', $user_id);
    return $this->db->get()->result();
  }

  public function getCustomersAll()
  {
    $this->db->select("c.id, c.ci, CONCAT(c.first_name, ' ', c.last_name) as fullname, c.loan_status, c.user_id, CONCAT(u.academic_degree, ' ', u.first_name, ' ', u.last_name) as user_name");
    $this->db->from('customers c');
    $this->db->join('users u', 'u.id = c.user_id');
    return $this->db->get()->result();
  }

  public function getCustomers($user_id)
  {
    $this->db->select("c.id, c.ci, CONCAT(c.first_name, ' ', c.last_name) as fullname, c.loan_status, c.user_id, CONCAT(u.academic_degree, ' ', u.first_name, ' ', u.last_name) as user_name");
    $this->db->from('customers c');
    $this->db->join('users u', 'u.id = c.user_id');
    $this->db->where("c.user_id = $user_id");
    return $this->db->get()->result();
  }
}

/* End of file Loans_m.php */
/* Location: ./application/models/Loans_m.php */