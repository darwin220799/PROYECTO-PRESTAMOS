<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Legalprocess_m extends MY_Model
{
    protected $_table_name = 'legal_processes';

    public $rules = array(
        array(
            'field' => 'customer_id',
            'label' => 'cliente',
            'rules' => 'required|numeric'
        ),
        array(
            'field' => 'observations',
            'label' => 'observaciones',
            'rules' => 'required|max_length[5000]'
        ),
        array(
            'field' => 'start_date',
            'label' => 'fecha de inicio',
            'rules' => 'required'
        )
    );

    public $rules_edit = array(
        array(
            'field' => 'observations',
            'label' => 'observaciones',
            'rules' => 'required|max_length[5000]'
        ),
        array(
            'field' => 'start_date',
            'label' => 'fecha de inicio',
            'rules' => 'required'
        )
    );

    /**
     * Lista paginable
     */
    public function findAll($start, $length, $search, $order)
    {
        $this->db->select('COUNT(lp.id) recordsFiltered');
        $this->db->from('legal_processes lp');
        $this->db->join('customers c', 'c.id = lp.customer_id');
        $this->db->where("lp.id LIKE '%$search%' OR (CONCAT(c.first_name, ' ', c.last_name) LIKE '%$search%' OR lp.start_date LIKE '%$search%')");
        $data['recordsFiltered'] = $this->db->get()->row()->recordsFiltered ?? 0;

        $this->db->select("lp.id, CONCAT(c.first_name, ' ', c.last_name) customer, lp.start_date");
        $this->db->from('legal_processes lp');
        $this->db->join('customers c', 'c.id = lp.customer_id');
        $this->db->where("lp.id LIKE '%$search%' OR (CONCAT(c.first_name, ' ', c.last_name) LIKE '%$search%' OR lp.start_date LIKE '%$search%')");
        $this->db->limit($length, $start);
        $this->db->order_by($order['column'], $order['dir']);
        $data['data'] = $this->db->get()->result() ?? [];

        return $data;
    }

    public function findLateDebtorCustomers()
    {
        $this->db->select("c.id, c.ci, c.first_name, c.last_name, COUNT(li.id) num_fee");
        $this->db->from('customers c');
        $this->db->join('loans l', 'l.customer_id = c.id');
        $this->db->join('loan_items li', 'li.loan_id = l.id');
        $this->db->where('li.status', 1);
        $now = Date('Y-m-d');
        $this->db->where("li.date < '{$now}'");
        $this->db->group_by('c.id');
        $this->db->order_by('li.date');
        return $this->db->get()->result() ?? [];
    }

    /**
     * id del proceso legal
     */
    public function findById($id)
    {
        $this->db->select("lp.id, lp.customer_id,  CONCAT(c.first_name, ' ', c.last_name) customer, lp.observations, lp.start_date");
        $this->db->from('legal_processes lp');
        $this->db->join('customers c', 'c.id = lp.customer_id');
        $this->db->where('lp.id', $id);
        $lp = $this->db->get()->row()??null;
        if($lp != null){
            $lp->files = $this->db->get_where('files', ['legal_process_id' => $id])->result()??[];
        }
        return $lp;
    }

    public function add($legalProcess)
    {
        $this->save($legalProcess);
        return $this->db->insert_id();
    }

    public function addFiles($images){
        foreach ($images as $img) {
            $this->db->insert('files', $img);
        }
        return true;
    }

    /**
     * Elimina el proceso legal y los egistros  de sus archivos
     */
    public function deleteById($id){
        return $this->db->delete('legal_processes', ['id' => $id]);
    }

    /**
     * id del proceso legal
     */
    public function update($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('legal_processes', $data);
    }

    public function findFileById($imageId)
    {
        return $this->db->get_where('files', ['id' => $imageId])->row()??null;
    }

    public function deleteFileById($id){
        return $this->db->delete('files', array('id' => $id));
    }

    public function findLoansWithExpiredLoanItems($customer_id)
    {
        $this->db->select("l.id, l.credit_amount, l.date, c.short_name");
        $this->db->from("loans l");
        $this->db->join("loan_items li", "li.loan_id = l.id");
        $this->db->join("coins c", "c.id = l.coin_id");
        $now = Date('Y-m-d');
        $this->db->where("li.date < '{$now}'");
        $this->db->where("li.status = 1");
        $this->db->where("l.customer_id = $customer_id");
        $this->db->group_by("l.id");
        return $this->db->get()->result()??[];
    }
        
}
