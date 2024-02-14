<?php

defined('BASEPATH') or exit('No direct script access allowed');
include(APPPATH . "/tools/UserPermission.php");

class Roles extends CI_Controller {

    private $user_id;
    private $permission;

    public function __construct(){
        parent::__construct();
        $this->load->model('role_m');
        $this->load->model('permission_m');
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->session->userdata('loggedin') == TRUE || redirect('user/login');
        $this->user_id = $this->session->userdata('user_id');
        $this->permission = new Permission($this->permission_m, $this->user_id);
    }

    public function index(){
        if(!$this->permission->getPermission([ROLE_READ], FALSE))
            show_error("You don't have access to this site", 403, 'DENIED ACCESS');
        $data[ROLE_CREATE] = $this->permission->getPermission([ROLE_CREATE], FALSE);
        $data[ROLE_READ] = $this->permission->getPermission([ROLE_READ], FALSE);
        $data[ROLE_UPDATE] = $this->permission->getPermission([ROLE_UPDATE], FALSE);
        $data[ROLE_DELETE] = $this->permission->getPermission([ROLE_DELETE], FALSE);
        $data['subview'] = 'admin/roles/index';
        $this->load->view('admin/_main_layout', $data);
    }

    /**
     * Mostrar los roles con paginación
     */
    public function ajax_roles(){
        $ROLE_READ = $this->permission->getPermission([ROLE_READ], FALSE);
        if (!$ROLE_READ) {
            $json_data = array(
                "draw"            => intval($this->input->post('draw')),
                "recordsTotal"    => intval(0),
                "recordsFiltered" => intval(0),
                "data"            => [],
            );
            echo json_encode($json_data);
            return;
        }
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $search = $this->input->post('search')['value']?? '';
        $columns = ['id', 'name', 'description', null];
        $columIndex = $this->input->post('order')['0']['column'] ?? 0;
        $order['column'] = $columns[$columIndex] ?? '';
        $order['dir'] = $this->input->post('order')['0']['dir'] ?? '';
        $query = $this->role_m->findAll($start, $length, $search, $order);
        if (sizeof($query['data']) == 0 && $start > 0) $query = $this->role_m->findAll(0, $length, $search, $order);
        $json_data = array(
            "draw"            => intval($this->input->post('draw')),
            "recordsTotal"    => intval(sizeof($query['data'])),
            "recordsFiltered" => intval($query['recordsFiltered']),
            "data"            => $query['data'],
        );
        echo json_encode($json_data);
    }

    public function create()
    {
        if(!$this->permission->getPermission([ROLE_CREATE], FALSE))
            show_error("You don't have access to this site", 403, 'DENIED ACCESS');
        $this->form_validation->set_rules($this->role_m->rules);
        if ($this->form_validation->run()) {
            // Cargar rol y permisos
            $data['role'] = [
                'name' => $this->input->post('name'), 
                'description' => $this->input->post('description')
            ]; 
            $data['permissions'] = array_unique($this->input->post('permission_ids')??[]);
            // Guardar rol y permisos
            if($this->role_m->addRole($data))
                $this->session->set_flashdata('msg', 'Rol agregado correctamente');
            else
                $this->session->set_flashdata('msg_error', 'Ocurrió un error durante el proceso');
            redirect('admin/roles');
        }
        $permissions = $this->db->get('permissions')->result()??[];
        $data = $this->role_m->modelState($this->input, $permissions);
        $data['subview'] = 'admin/roles/edit';
        
        $this->load->view('admin/_main_layout', $data);
    }

    public function view($id){
        $this->permission->getPermission([ROLE_READ], TRUE);
        $data = $this->role_m->findById($id);
        if(($data['role'] == null)) show_404();
        $data['subview'] = 'admin/roles/view';
        $this->load->view('admin/_main_layout', $data);
    }

    public function edit($id)
    {
        if(!$this->permission->getPermission([ROLE_UPDATE], FALSE)) 
            show_error("You don't have access to this site", 403, 'DENIED ACCESS');
        $origin = $this->input->get('origin');
        $path = $origin?"/$origin/$id":'';
        $this->form_validation->set_rules($this->role_m->rules);
        if ($this->form_validation->run()) {
            // Cargar rol y permisos
            $data['role'] = [
                'name' => $this->input->post('name'), 
                'description' => $this->input->post('description')
            ]; 
            $data['permissions'] = array_unique($this->input->post('permission_ids')??[]);
            // Guardar rol y permisos
            if($this->role_m->update($data, $id)) // actualizar datos
                $this->session->set_flashdata('msg', 'Rol actualizado correctamente');
            else
                $this->session->set_flashdata('msg_error', 'Ocurrió un error durante el proceso');
            redirect('admin/roles'.$path);
        }
        if($this->input->post('name') || $this->input->post('description') || $this->input->post('permission_ids')){
            $permissions = $this->db->get('permissions')->result()??[];
            $data = $this->role_m->modelState($this->input, $permissions);
        }else{
            $data['role'] = $this->db->get_where('roles', ['id'=>$id])->row()??null;
            $data['permissions'] = $this->role_m->getPermissionsState($id);
            if(($data['role'] == null)) show_404();
        }
        
        $data['post'] = $origin?site_url('admin/roles/edit/') . $id."?origin=$origin":'';
        $data['path'] = $path;
        $data['subview'] = 'admin/roles/edit';
        $this->load->view('admin/_main_layout', $data);
    }

    public function delete($id){
        if(!$this->permission->getPermission([ROLE_DELETE], FALSE))
            show_error("You don't have access to this site", 403, 'DENIED ACCESS');
        if($this->role_m->deleteById($id))
            $this->session->set_flashdata('msg', 'El rol se eliminó correctamente');
        else
            $this->session->set_flashdata('msg_error', 'Ocurrió un error durante el proceso');
        redirect('admin/roles');
    }

}