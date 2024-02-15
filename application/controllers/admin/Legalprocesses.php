<?php
defined('BASEPATH') or exit('No direct script access allowed');
include(APPPATH . "/tools/UserPermission.php");


class Legalprocesses extends CI_Controller
{

  private $user_id;
  private $permission;

  public function __construct()
  {
    parent::__construct();
    $this->load->model('legalprocess_m');
    $this->load->model('permission_m');
    $this->load->library('session');
    $this->load->library('form_validation');
    $this->session->userdata('loggedin') == TRUE || redirect('user/login');
    $this->user_id = $this->session->userdata('user_id');
    $this->permission = new Permission($this->permission_m, $this->user_id);
  }

  public function index()
  {
    $LEGAL_PROCESS_READ = $this->permission->getPermission([LEGAL_PROCESS_READ], FALSE);
    if (!$LEGAL_PROCESS_READ) show_error("You don't have access to this site", 403, 'DENIED ACCESS');
    $data['LEGAL_PROCESS_CREATE'] = $this->permission->getPermission([LEGAL_PROCESS_CREATE], FALSE);
    $data['LEGAL_PROCESS_READ'] = $LEGAL_PROCESS_READ;
    $data['LEGAL_PROCESS_DELETE'] =     $data['LEGAL_PROCESS_READ'] = $this->permission->getPermission([LEGAL_PROCESS_DELETE], FALSE);
    $data['subview'] = 'admin/legalprocesses/index';
    $this->load->view('admin/_main_layout', $data);
  }

  public function ajax_legal_processes()
  {
    $LEGAL_PROCESS_READ = $this->permission->getPermission([LEGAL_PROCESS_READ], FALSE);
    if (!$LEGAL_PROCESS_READ) {
      $json_data = array(
        "draw"            => intval($this->input->post('draw')),
        "recordsTotal"    => intval(0),
        "recordsFiltered" => intval(0),
        "data"            => [],
      );
      echo json_encode($this->json_data);
      return;
    }
    $start = $this->input->post('start');
    $length = $this->input->post('length');
    $search = $this->input->post('search')['value'] ?? '';
    $columns = ['id', 'customer', 'start_date', ''];
    $columIndex = $this->input->post('order')['0']['column'] ?? 3;
    $order['column'] = $columns[$columIndex] ?? '';
    $order['dir'] = $this->input->post('order')['0']['dir'] ?? '';
    $query = $this->legalprocess_m->findAll($start, $length, $search, $order);
    if (sizeof($query['data']) == 0 && $start > 0) $query = $this->legalprocess_m->findAll(0, $length, $search, $order);
    $json_data = array(
      "draw"            => intval($this->input->post('draw')),
      "recordsTotal"    => intval(sizeof($query['data'])),
      "recordsFiltered" => intval($query['recordsFiltered']),
      "data"            => $query['data']
    );
    echo json_encode($json_data);
  }

  public function create()
  {
    $LEGAL_PROCESS_CREATE = $this->permission->getPermission([LEGAL_PROCESS_CREATE], FALSE);
    if (!$LEGAL_PROCESS_CREATE) show_error("You don't have access to this site", 403, 'DENIED ACCESS');
    $this->form_validation->set_rules($this->legalprocess_m->rules);
    if ($this->form_validation->run()) {
      $data = [
        'customer_id' => $this->input->post('customer_id'),
        'observations' => $this->input->post('observations'),
        'start_date' => $this->input->post('start_date'),

      ];

      $legal_process_id = $this->legalprocess_m->add($data);

      if ($legal_process_id) {
        // Guardar archivos
        $files = [];
        for ($i = 1; $i <= 5; $i++) {
          $obj = $this->saveFile("img$i");
          if ($obj)
            array_push($files, ['legal_process_id' => $legal_process_id, 'name' => $obj['file_name']]);
        }
        // Registrar archivos en la base de datos
        $this->legalprocess_m->addFiles($files);
        $this->session->set_flashdata('msg', 'Se agregó correctamente el cliente al listado');
        redirect('/admin/legalprocesses');
      } else $this->session->set_flashdata('msg_error', 'Algo salió mal...');
    } else {
      $form = new stdClass();
      $form->customer_id = $this->input->post('customer_id') ?? '';
      $form->observations = $this->input->post('observations') ?? '';
      $form->start_date = $this->input->post('start_date') ?? null;
      $data['form_state'] = $form;
    };
    $data['customers'] = $this->legalprocess_m->findLateDebtorCustomers();
    $data['subview'] = 'admin/legalprocesses/create';
    return $this->load->view('admin/_main_layout', $data);
  }

  public function create_file($legal_process_id)
  {
    if (!$this->permission->getPermission([LEGAL_PROCESS_UPDATE], FALSE))
      show_error("You don't have access to this site", 403, 'DENIED ACCESS');
    
    $data['legal_process_id'] = $legal_process_id;
    $data['subview'] = 'admin/legalprocesses/file_create';
    return $this->load->view('admin/_main_layout', $data);
  }

  public function add_file($legal_process_id)
  {
    if (!$this->permission->getPermission([LEGAL_PROCESS_UPDATE], FALSE))
      show_error("You don't have access to this site", 403, 'DENIED ACCESS');
    $obj = $this->saveFile("image");
    if ($obj) {
      if ($this->legalprocess_m->addFiles(
        [
          ['legal_process_id' => $legal_process_id, 'name' => $obj['file_name']]
        ]
      ))
        $this->session->set_flashdata('msg', 'Se agregó la imagen');
      else
        $this->session->set_flashdata('msg_error', 'Ocurrió un error al realizar el proceso...');
    }else{
      $this->session->set_flashdata('msg_error', 'Algo salió mal, no se encontraon datos sobre el arhivo');
      redirect("admin/legalprocesses/create_file/$legal_process_id");
    }
    redirect("admin/legalprocesses/view/$legal_process_id");
  }

  private function saveFile($inputName)
  {
    $fileName = str_replace(' ', '', str_replace('-', '', str_replace(':', '', Date('Y-m-d H:i:s')))) . uniqid();
    $config['upload_path'] = '././uploads';
    $config['file_name'] = $fileName;
    $config['allowed_types'] = 'jpg|png';
    $config['max_size'] = '5000';
    $config['max_width'] = '2000';
    $config['max_height'] = '2000';

    $this->load->library('upload', $config);

    if (!$this->upload->do_upload($inputName)) {
      echo $this->upload->display_errors();
      return;
    }
    return $this->upload->data();
  }

  public function edit($id)
  {
    $LEGAL_PROCESS_UPDATE = $this->permission->getPermission([LEGAL_PROCESS_UPDATE], FALSE);
    if (!$LEGAL_PROCESS_UPDATE) show_error("You don't have access to this site", 403, 'DENIED ACCESS');
    $this->form_validation->set_rules($this->legalprocess_m->rules_edit);
    if ($this->form_validation->run()) {
      $data = [
        'observations' => $this->input->post('observations'),
        'start_date' => $this->input->post('start_date')
      ];
      if ($this->legalprocess_m->update($id, $data))
        $this->session->set_flashdata('msg', 'Se realizó el proceso correctamente');
      else
        $this->session->set_flashdata('msg_error', 'Ocurrió un error al realizar el proceso...');
      redirect("admin/legalprocesses/view/$id");
    } else {
      $form = new stdClass();
      if ($this->input->post('customer_id')) {
        $form->customer_id = $this->input->post('customer_id') ?? '';
        $form->observations = $this->input->post('observations') ?? '';
        $form->start_date = $this->input->post('start_date') ?? null;
        $data['legal_process'] = $form;
      } else {
        $data['legal_process'] = $this->legalprocess_m->findById($id);
      }
    }

    $data['subview'] = 'admin/legalprocesses/edit';
    return $this->load->view('admin/_main_layout', $data);
  }

  public function view($id)
  {
    $LEGAL_PROCESS_READ = $this->permission->getPermission([LEGAL_PROCESS_READ], FALSE);
    if (!$LEGAL_PROCESS_READ) show_error("You don't have access to this site", 403, 'DENIED ACCESS');
    $data['legal_process'] = $this->legalprocess_m->findById($id);
    if($data['legal_process'])
      $data['loans'] = $this->legalprocess_m->findLoansWithExpiredLoanItems($data['legal_process']->customer_id);
    $data['subview'] = 'admin/legalprocesses/view';
    return $this->load->view('admin/_main_layout', $data);
  }

  /**
   * Elimina el documento legal y sus archivos
   */
  public function delete($id)
  {
    if (!$this->permission->getPermission([LEGAL_PROCESS_DELETE], FALSE))
      show_error("You don't have access to this site", 403, 'DENIED ACCESS');
    // Eliminar archivos
    $legal_process = $this->legalprocess_m->findById($id);
    foreach ($legal_process->files as $file) {
      try {
        unlink("././uploads/$file->name");
      } catch (\Throwable $th) {
        echo $th->getMessage();
      }
    }
    // Eliminar registro
    if ($this->legalprocess_m->deleteById($id))
      $this->session->set_flashdata('msg', 'Se eliminó de la lista');
    else
      $this->session->set_flashdata('msg_error', 'Ocurrió un error al realizar el proceso...');
    redirect("admin/legalprocesses");
  }

  /***
   * Elimina el archivo con $file_id y redirige a la vista con $id
   */
  public function file_remover($id, $file_id)
  {
    if (!$this->permission->getPermission([LEGAL_PROCESS_UPDATE], FALSE))
      show_error("You don't have access to this site", 403, 'DENIED ACCESS');
    $file = $this->legalprocess_m->findFileById($file_id);
    if ($file != null) {
      try {
        unlink("././uploads/$file->name");
      } catch (Exception $e) {
        echo $e->getMessage();
      }
      if ($this->legalprocess_m->deleteFileById($file_id)) {
        $this->session->set_flashdata('msg', 'Se eliminó el archivo');
      } else
        $this->session->set_flashdata('msg_error', 'Ocurrió un error al realizar el proceso...');
      redirect("admin/legalprocesses/view/$id");
      echo "El id es: " . $id . "  El file_id es: " . $file_id;
    }
  }
}

/* End of file Coins.php */
/* Location: ./application/controllers/admin/Coins.php */