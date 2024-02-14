<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payments_m extends CI_Model {

  public function isAdviser($customer_id, $user_id)
  {
    $this->db->select("IF( EXISTS(
      SELECT *
      FROM users u
      JOIN customers c ON c.user_id = u.id
      WHERE u.id = $user_id AND c.id = $customer_id), 1, 0) exist");
    return $this->db->get()->row()->exist==1?TRUE:FALSE;
  }

  public function getPaymentsAll()
  {
    $this->db->select("li.id, c.ci, concat(c.first_name,' ',c.last_name) AS name_cst, l.id AS loan_id, li.pay_date, li.num_quota, li.fee_amount");
    $this->db->from('loan_items li');
    $this->db->join('loans l', 'l.id = li.loan_id', 'left');
    $this->db->join('customers c', 'c.id = l.customer_id', 'left');
    $this->db->join('users u', 'u.id = c.user_id', 'left');
    $this->db->where('li.status', 0);
    $this->db->order_by('li.pay_date', 'desc');
    return $this->db->get()->result();
  }

  public function getPayments($user_id)
  {
    $this->db->select("li.id, c.ci, concat(c.first_name,' ',c.last_name) AS name_cst, l.id AS loan_id, li.pay_date, li.num_quota, li.fee_amount");
    $this->db->from('loan_items li');
    $this->db->join('loans l', 'l.id = li.loan_id', 'left');
    $this->db->join('customers c', 'c.id = l.customer_id', 'left');
    $this->db->join('users u', 'u.id = c.user_id', 'left');
    $this->db->where('li.status', 0);
    $this->db->where('u.id', $user_id);
    $this->db->order_by('li.pay_date', 'desc');
    return $this->db->get()->result();
  }

  public function findPayedLoanItems($start, $length, $search, $order, $user_id)
  {
    $this->db->select("COUNT(li.id) recordsFiltered");
    $this->db->from('loan_items li');
    $this->db->join('loans l', 'l.id = li.loan_id', 'left');
    $this->db->join('customers c', 'c.id = l.customer_id', 'left');
    $this->db->join('users u', 'u.id = c.user_id', 'left');
    $this->db->where("(c.ci LIKE '%$search%' OR CONCAT(c.first_name,' ',  c.last_name) LIKE '%$search%' OR
    l.id LIKE '%$search%' OR li.num_quota LIKE '%$search%' OR li.fee_amount LIKE '%$search%' OR li.pay_date LIKE '%$search%')");
    $this->db->where('li.status', 0);
    if($user_id != 'all' && $user_id != null)
      $this->db->where('c.user_id', $user_id);
    $data['recordsFiltered'] = $this->db->get()->row()->recordsFiltered??0;

    $this->db->select("li.id, c.ci, CONCAT(c.first_name,' ',c.last_name) AS name_cst, l.id AS loan_id, li.pay_date, li.num_quota, li.fee_amount");
    $this->db->from('loan_items li');
    $this->db->join('loans l', 'l.id = li.loan_id', 'left');
    $this->db->join('customers c', 'c.id = l.customer_id', 'left');
    $this->db->join('users u', 'u.id = c.user_id', 'left');
    $this->db->where("(c.ci LIKE '%$search%' OR CONCAT(c.first_name,' ',  c.last_name) LIKE '%$search%' OR
    l.id LIKE '%$search%' OR li.num_quota LIKE '%$search%' OR li.fee_amount LIKE '%$search%' OR li.pay_date LIKE '%$search%')");
    $this->db->where('li.status', 0);
    if($user_id != 'all' && $user_id != null)
      $this->db->where('c.user_id', $user_id);
    $this->db->order_by($order['column'], $order['dir']);
    $this->db->limit($length, $start);
    $data['data'] = $this->db->get()->result()??[];
    return $data;
  }

  public function getCustomersAll(){
    $this->db->select("c.id, c.ci, CONCAT(c.first_name, ' ', c.last_name) as fullname");
    $this->db->from('customers c');
    $this->db->where("c.loan_status = TRUE");
    return $this->db->get()->result();
  }

  public function get_customers($user_id){
    $this->db->select("c.id, c.ci, CONCAT(c.first_name, ' ', c.last_name) as fullname");
    $this->db->from('customers c');
    $this->db->where("c.user_id = $user_id");
    $this->db->where("c.loan_status = TRUE");
    return $this->db->get()->result();
  }

  public function getLoanAll($customer_id)
  {
    $this->db->select("l.id, l.customer_id, l.credit_amount, l.payment_m, co.name coin_name, l.coin_id, CONCAT(u.academic_degree, ' ', u.first_name, ' ', u.last_name) as user_name");
    $this->db->from('loans l');
    $this->db->join('customers c', 'c.id = l.customer_id', 'left');
    $this->db->join('users u', 'u.id = c.user_id', 'left');
    $this->db->join('coins co', 'co.id = l.coin_id', 'left');
    $this->db->where(['c.loan_status' => 1, 'l.status' => 1, 'c.id' => $customer_id]);
    return $this->db->get()->row();
  }

  public function getLoan($user_id, $customer_id)
  {
    $this->db->select("l.id, l.customer_id, l.credit_amount, l.payment_m, co.name as coin_name, l.coin_id, CONCAT(u.academic_degree, ' ', u.first_name, ' ', u.last_name) as user_name");
    $this->db->from('loans l');
    $this->db->join('customers c', 'c.id = l.customer_id', 'left');
    $this->db->join('users u', 'u.id = c.user_id', 'left');
    $this->db->join('coins co', 'co.id = l.coin_id', 'left');
    $this->db->where(['c.loan_status' => 1, 'l.status' => 1, 'c.id' => $customer_id]);
    $this->db->where("u.id", $user_id);
    return $this->db->get()->row();
  }

  public function getLoanItemsAll($loan_id)
  {
    $this->db->select("li.*, SUM(IFNULL(p.amount, 0)) payed, SUM(IFNULL(p.surcharge, 0)) surcharge");
    $this->db->from('loan_items li');
    $this->db->join('loans l', 'l.id = li.loan_id');
    $this->db->join('customers c', 'c.id = l.customer_id');
    $this->db->join('users u', 'u.id = c.user_id');
    $this->db->join('coins co', 'co.id = l.coin_id');
    $this->db->join('payments p', 'p.loan_item_id = li.id', 'left');
    $this->db->where("l.id", $loan_id);
    $this->db->group_by('li.id');
    $this->db->order_by('li.num_quota');
    return $this->db->get()->result();
  }

  public function getLoanItems($user_id, $loan_id)
  {
    $this->db->select("li.*, SUM(IFNULL(p.amount, 0)) payed, SUM(IFNULL(p.surcharge, 0)) surcharge");
    $this->db->from('loan_items li');
    $this->db->join('loans l', 'l.id = li.loan_id');
    $this->db->join('customers c', 'c.id = l.customer_id');
    $this->db->join('users u', 'u.id = c.user_id');
    $this->db->join('coins co', 'co.id = l.coin_id');
    $this->db->join('payments p', 'p.loan_item_id = li.id', 'left');
    $this->db->where("l.id", $loan_id);
    $this->db->where("u.id", $user_id);
    $this->db->group_by('li.id');
    $this->db->order_by('li.num_quota');
    return $this->db->get()->result();
  }

  public function update_quota($data, $id)
  {
    $this->db->where('id', $id);
    $this->db->update('loan_items', $data); 
  }

  public function check_cstLoan($loan_id)
  {
    $request = $this->db->select('COUNT(*) count')
      ->from('loan_items')
      ->where('loan_id', $loan_id)
      ->where('status', TRUE)
      ->get()->row();
    return ($request->count > 0)?TRUE: FALSE;
  }

  /**
   * Verifica si ya se completó de pagar una cuata con los pagos
   */
  public function paymentsIsEqualToQuote($loan_item_id){
    $paidRequest = $this->db
      ->select('SUM(p.amount) total_quota')
      ->where('loan_item_id', $loan_item_id)
      ->get('payments p')
      ->row();
    $paid = ($paidRequest->total_quota)?$paidRequest->total_quota:0;
    $quotaRequest = $this->db
      ->select('li.fee_amount')
      ->where('id', $loan_item_id)
      ->get('loan_items li')
      ->row();
    $quota = ($quotaRequest->fee_amount)?$quotaRequest->fee_amount:0;
    return ($paid >= $quota)?TRUE:FALSE;
  }

  public function addPayments($payments){
    try{
      foreach($payments as $payment){
        $this->db->insert('payments', $payment);
      }
      return true;
    }catch(Exception $e){
      echo ($e->getMessage());
      return false;
    }
  }

  public function addDocumentPayment($data){
    $this->db->insert('document_payments', $data);
    return $this->db->insert_id();
  }

  public function update_cstLoan($loan_id, $customer_id)
  {
    $this->db->where('id', $loan_id);
    $this->db->update('loans', ['status' => 0]);

    $this->db->where('id', $customer_id);
    $this->db->update('customers', ['loan_status' => 0]); 
  }

  public function get_quotasPaid($data)
  {
    $this->db->where_in('id', $data);
    return $this->db->get('loan_items')->result();
  }

  // Funciones para validación
  // Retorna el id del usuario consejero del prestamo
  public function get_loan_adviser_user_id($loan_id){
    $this->db->select('u.id');
    $this->db->from('users u');
    $this->db->join('customers c', 'c.user_id = u.id');
    $this->db->join('loans l', 'l.customer_id = c.id');
    $this->db->where('l.id', $loan_id);
    return $this->db->get()->row();
  }

  public function getGuarantorsAll($loan_id){
    $this->db->select("g.id, c.ci ci, CONCAT(c.first_name, ' ', c.last_name) guarantor_name");
    $this->db->from('customers c');
    $this->db->join('guarantors g', 'g.customer_id = c.id');
    $this->db->where(['g.loan_id'=>$loan_id]);
    return $this->db->get()->result();
  }

  public function get_guarantors($user_id, $loan_id){
    $this->db->select("g.id, c.ci ci, CONCAT(c.first_name, ' ', c.last_name) guarantor_name");
    $this->db->from('customers c');
    $this->db->join('guarantors g', 'g.customer_id = c.id');
    $this->db->where(['g.loan_id'=>$loan_id, 'c.user_id'=>$user_id]);
    return $this->db->get()->result();
  }

  public function getCustomerByIdAll($customer_id){
    $this->db->select("CONCAT(c.first_name, ' ', c.last_name) customer_name")
              ->from('customers c')
              ->where('c.id', $customer_id);
    return $this->db->get()->row();
  }

  public function get_customer_by_id($user_id, $customer_id){
    $this->db->select("CONCAT(c.first_name, ' ', c.last_name) customer_name")
              ->from('customers c')
              ->join('users u', 'u.id = c.user_id')
              ->where('u.id', $user_id)
              ->where('c.id', $customer_id);
    return $this->db->get()->row();
  }

  public function getCustomerAdvisorName($customer_id){
    $this->db->select("CONCAT(u.academic_degree, ' ', u.first_name, ' ', u.last_name) user_name");
    $this->db->from('users u');
    $this->db->join('customers c', 'c.user_id = u.id');
    $this->db->where('c.id', $customer_id);
    return $this->db->get()->row();
  }

  /**
   * Valida si permite guardar o no el registro de pagos
   * Si payment es > a 0, continuar (ok)
   * Si el estado de loan_item está true, continuar (ok)
   * Si payment <= a la deuda, continuar;
   */
  public function paymentsOk($payments){
    $success = new stdClass();
    $success->errors = [];
    $success->valid = TRUE;
    try{
      foreach($payments as $item){
        // Si payment es > a 0, continuar (ok)
        if($item['amount'] <= 0)
          array_push($success->errors, 'payments.amount <= 0');
        // Si payment es mayor a 0, continuar (ok)
        $loan_item_status = $this->db->select('status')->where('id', $item["loan_item_id"])->get('loan_items')->row();
        if($loan_item_status->status == FALSE)
          array_push($success->errors, 'loan_items.status is FALSE');
        // Si payment <= a la deuda, continuar;
        $paid_request = $this->db->select("SUM(p.amount) AS debt")->where('loan_item_id', $item['loan_item_id'])->get('payments p')->row(); // deuda
        $amount_request = $this->db->select('li.fee_amount')->where('id',$item['loan_item_id'])->get('loan_items li')->row();
        $paid = isset($paid_request->debt)?$paid_request->debt:0;
        $amount = isset($amount_request->fee_amount)?$amount_request->fee_amount:0;
        $debt = round($amount - $paid, 2);
        if($item['amount'] > $debt)
          array_push($success->errors, "payments.amount > debt ($amount > $debt)");
        if(sizeof($success->errors) > 0)
          $success->valid = FALSE;
        return $success;
      }
    }catch(Exception $e){
      $success->valid = FALSE;
      array_push($success->errors, $e->getMessage());
      return $success;
    }
  }

  /**
   * Obtiene la información para el docuemnto de impresión
   * $id es el id del documento ("documentt_payment_id")
   */
  public function getDocumentPayment($id){
    // Obtener el documento
    $data['document_payment'] = $this->db
    ->select("dp.*, CONCAT(u.academic_degree, ' ', u.first_name, ' ', u.last_name) user_name")
    ->join('users u', 'u.id = dp.user_id')
    ->get_where('document_payments dp', ['dp.id'=>$id])->row();
    // Obtener pagos del docuemnto
    $data['quotas_payments'] =  $this->db
    ->select('li.num_quota, li.loan_id, p.loan_item_id, p.amount, p.surcharge, p.document_payment_id')
    ->from('payments p')
    ->join('loan_items li', 'li.id = p.loan_item_id')
    ->where('p.document_payment_id', $id)
    ->get()
    ->result();
    // Obtener el préstamo
    $data['loan'] = null;
    if(isset($data['quotas_payments'])): if(sizeof($data['quotas_payments']) > 0) :
      $loan_id = $data['quotas_payments'][0]->loan_id;
      $data['loan'] = $this->db->select("l.id, l.customer_id, c.name coin_name")
      ->join('coins c', 'c.id = l.coin_id')
      ->get_where('loans l', ['l.id' => $loan_id])->row();
    endif; endif;
    // Obtener cliente
    $data['customer'] = null;
    if(isset($data['loan']->customer_id)){
      $customer_id = $data['loan']->customer_id;
      $data['customer'] = $this->db->select("c.id, CONCAT(c.first_name, ' ', c.last_name) name, c.user_id")
        ->from('customers c')
        ->where('id', $customer_id)
        ->get()->row();
    }
    // Obtener asesor
    $data['adviser'] = null;
    if(isset($data['customer']->user_id)){
      $user_id = $data['customer']->user_id;
      $data['adviser'] = $this->db->select("CONCAT(u.academic_degree, u.first_name, ' ', u.last_name) as name")
        ->from('users u')
        ->where('id', $user_id)
        ->get()->row();
    }
    return $data;
  }

    /**
   * Obtiene las cuotas de la última semana
   */
  public function quotesWeekAll($start_date, $end_date)
  {
    $this->db->select('c.id, c.ci, CONCAT(c.first_name, " " , c.last_name) customer_name, 
    CONCAT(u.academic_degree, " ", u.first_name, " " , u.last_name) user_name, 
    li.date, co.name as coin_name, co.short_name as coin_short_name, li.fee_amount,
    (SELECT SUM(p.amount) FROM payments p WHERE p.loan_item_id = li.id) payed');
    $this->db->from('customers c');
    $this->db->join('users u', 'u.id = c.user_id');
    $this->db->join('loans l', 'c.id = l.customer_id');
    $this->db->join('coins co', 'co.id = l.coin_id');
    $this->db->join('loan_items li', 'l.id = li.loan_id');
    $this->db->where("(li.date BETWEEN '{$start_date}' AND '{$end_date}' AND li.status = TRUE) OR (li.date < '{$start_date}' AND li.status = TRUE)");
    $this->db->order_by('li.date');
    $data['items'] = $this->db->get()->result();
    // print_r(json_encode($data) );

    // Montos totalespor monedas
    $query = "SELECT c.name, SUM(IFNULL(li.fee_amount - (SELECT IFNULL(SUM(p.amount), 0) FROM payments p WHERE p.loan_item_id = li.id), 0)) total
    FROM loans l
    JOIN coins c ON c.id = l.coin_id
    JOIN loan_items li ON l.id = li.loan_id
    WHERE (li.date BETWEEN '$start_date' AND '$end_date' AND li.status = TRUE) OR (li.date < '$start_date' AND li.status = TRUE)
    GROUP BY c.name;";
    $data['payables'] = $this->db->query($query)->result();
    // print_r($data['payables']);

    // Buscar monto total de moras por monedas
    $queryA = "SELECT c.name, SUM(IFNULL(li.fee_amount - (SELECT IFNULL(SUM(p.amount), 0) FROM payments p WHERE p.loan_item_id = li.id), 0)) total
    FROM loans l
    JOIN coins c ON c.id = l.coin_id
    JOIN loan_items li ON l.id = li.loan_id
    WHERE li.date < '$start_date' AND li.status = TRUE
    GROUP BY c.name;";
    $data['payable_expired'] = $this->db->query($queryA)->result();

    // Buscar monto total de cobros hoy por monedas
    $queryB = "SELECT c.name, SUM(IFNULL(li.fee_amount - (SELECT IFNULL(SUM(p.amount), 0) FROM payments p WHERE p.loan_item_id = li.id), 0)) total
    FROM loans l
    JOIN coins c ON c.id = l.coin_id
    JOIN loan_items li ON l.id = li.loan_id
    WHERE li.date = '$start_date' AND li.status = TRUE
    GROUP BY c.name;";
    $data['payable_now'] = $this->db->query($queryB)->result();

    // Buscar monto total de cobros próximos por monedas
    $queryC = "SELECT c.name, SUM(IFNULL(li.fee_amount - (SELECT IFNULL(SUM(p.amount), 0) FROM payments p WHERE p.loan_item_id = li.id), 0)) total
    FROM loans l
    JOIN coins c ON c.id = l.coin_id
    JOIN loan_items li ON l.id = li.loan_id
    WHERE li.date > '$start_date' AND li.date <= '$end_date' AND li.status = TRUE
    GROUP BY c.name;";
    $data['payable_next'] = $this->db->query($queryC)->result();

    return $data;
  }

  /**
   * Obtiene las cuotas de la última semana del usuario
   */
  public function quotesWeek($user_id, $start_date, $end_date)
  {
    $this->db->select('c.id, c.ci, CONCAT(c.first_name, " " , c.last_name) customer_name, 
    CONCAT(u.academic_degree, " ", u.first_name, " " , u.last_name) user_name, 
    li.date, , co.name as coin_name, co.short_name as coin_short_name, li.fee_amount,
    (SELECT SUM(p.amount) FROM payments p WHERE p.loan_item_id = li.id) payed');
    $this->db->from('customers c');
    $this->db->join('users u', 'u.id = c.user_id');
    $this->db->join('loans l', 'c.id = l.customer_id');
    $this->db->join('loan_items li', 'l.id = li.loan_id');
    $this->db->join('coins co', 'co.id = l.coin_id');
    $this->db->where("((li.date BETWEEN '{$start_date}' AND '{$end_date}' AND li.status = TRUE) OR (li.date < '{$start_date}' AND li.status = TRUE)) AND u.id = $user_id");
    $this->db->order_by('li.date');
    $data['items'] = $this->db->get()->result();
    
    // Buscar totales por cobrar
    $query = "SELECT co.name, SUM(IFNULL(li.fee_amount - (SELECT IFNULL(SUM(p.amount), 0) FROM payments p WHERE p.loan_item_id = li.id), 0)) total
    FROM loans l
    JOIN customers c ON c.id = .l.customer_id
    JOIN coins co ON co.id = l.coin_id
    JOIN loan_items li ON l.id = li.loan_id
    WHERE ((li.date BETWEEN '$start_date' AND '$end_date' AND li.status = TRUE) OR (li.date < '$start_date' AND li.status = TRUE)) and c.user_id = $user_id
    GROUP BY co.name;";
    $data['payables'] = $this->db->query($query)->result();

    // Buscar monto total de moras por monedas
    $queryA = "SELECT co.name, SUM(IFNULL(li.fee_amount - (SELECT IFNULL(SUM(p.amount), 0) FROM payments p WHERE p.loan_item_id = li.id), 0)) total
    FROM loans l
    JOIN customers c ON c.id = .l.customer_id
    JOIN coins co ON co.id = l.coin_id
    JOIN loan_items li ON l.id = li.loan_id
    WHERE  (li.date < '$start_date' AND li.status = TRUE) and c.user_id = $user_id
    GROUP BY co.name;";
    $data['payable_expired'] = $this->db->query($queryA)->result();

    // Buscar monto total de cobros hoy por monedas
    $queryB = "SELECT co.name, SUM(IFNULL(li.fee_amount - (SELECT IFNULL(SUM(p.amount), 0) FROM payments p WHERE p.loan_item_id = li.id), 0)) total
    FROM loans l
    JOIN customers c ON c.id = .l.customer_id
    JOIN coins co ON co.id = l.coin_id
    JOIN loan_items li ON l.id = li.loan_id
    WHERE (li.date = '$start_date' AND li.status = TRUE) AND c.user_id = $user_id
    GROUP BY co.name;";
    $data['payable_now'] = $this->db->query($queryB)->result();

    // Buscar monto total de cobros próximos por monedas
    $queryC = "SELECT co.name, SUM(IFNULL(li.fee_amount - (SELECT IFNULL(SUM(p.amount), 0) FROM payments p WHERE p.loan_item_id = li.id), 0)) total
    FROM loans l
    JOIN customers c ON c.id = .l.customer_id
    JOIN coins co ON co.id = l.coin_id
    JOIN loan_items li ON l.id = li.loan_id
    WHERE (li.date > '$start_date' AND li.date <= '$end_date' AND li.status = TRUE) AND c.user_id = $user_id
    GROUP BY co.name;";
    $data['payable_next'] = $this->db->query($queryC)->result();
    return $data;
  }

  public function getUser($user_id){
    $this->db->select("CONCAT_WS(' ', u.academic_degree, u.first_name, u.last_name) user_name");
    $this->db->from('users u');
    if($user_id != "all" && $user_id != null)
      $this->db->where('u.id', $user_id);
    return $this->db->get()->row()??null;
  }

}

/* End of file Payments_m.php */
/* Location: ./application/models/Payments_m.php */