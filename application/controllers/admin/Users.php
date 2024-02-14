<?php
defined('BASEPATH') or exit('No direct script access allowed');
include(APPPATH . "/tools/UserPermission.php");

class Users extends CI_Controller
{

    private $user_id;
    private $permission;
    private $dir;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_m');
        $this->load->model('permission_m');
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->session->userdata('loggedin') == TRUE || redirect('user/login');
        $this->user_id = $this->session->userdata('user_id');
        $this->permission = new Permission($this->permission_m, $this->user_id);
        $this->load->helper('directory');
        $this->dir = "././assets/img/avatars";
    }

    public function index()
    {
        if(!$this->permission->getPermission([USER_READ], FALSE))
            show_error("You don't have access to this site", 403, 'DENIED ACCESS');
        $data[USER_CREATE] = $this->permission->getPermission([USER_CREATE], FALSE);
        $data[USER_READ] = $this->permission->getPermission([USER_READ], FALSE);
        $data[USER_UPDATE] = $this->permission->getPermission([USER_UPDATE], FALSE);
        $data[USER_DELETE] = $this->permission->getPermission([USER_DELETE], FALSE);
        $data['subview'] = 'admin/users/index';
        $this->load->view('admin/_main_layout', $data);
    }

    public function ajax_users()
    {
        $USER_READ = $this->permission->getPermission([USER_READ], FALSE);
        if (!$USER_READ) {
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
        $search = $this->input->post('search')['value'] ?? '';
        $columns = ['id', 'full_name', 'roles', 'email', null];
        $columIndex = $this->input->post('order')['0']['column'] ?? 0;
        $order['column'] = $columns[$columIndex] ?? '';
        $order['dir'] = $this->input->post('order')['0']['dir'] ?? '';
        $query = $this->user_m->findAll($start, $length, $search, $order);
        if (sizeof($query['data']) == 0 && $start > 0) $query = $this->user_m->findAll(0, $length, $search, $order);
        $json_data = array(
            "draw"            => intval($this->input->post('draw')),
            "recordsTotal"    => intval(sizeof($query['data'])), // total registros para mostrar
            "recordsFiltered" => intval($query['recordsFiltered']), // total registro en base de datos
            "data"            => $query['data'], // Registros 
        );
        echo json_encode($json_data);
    }

    public function create()
    {
        if(!$this->permission->getPermission([USER_CREATE], FALSE))
            show_error("You don't have access to this site", 403, 'DENIED ACCESS');
        $this->form_validation->set_rules($this->user_m->newUserRules);
        if ($this->form_validation->run()) {
            // Guardar
            $data['user'] = $this->user_m->array_from_post(['first_name', 'last_name', 'academic_degree', 'email', 'avatar', 'password']);
            $data['roles'] = $this->input->post('role_ids')??[];
            $data['user']['first_name'] = strtoupper($data['user']['first_name']);
            $data['user']['last_name'] = strtoupper($data['user']['last_name']);
            $data['user']['avatar'] = $data['user']['avatar'] ?? AVATARS[0];
            $data['user']['password'] = $this->user_m->hash($this->input->post('password'));
            if ($this->user_m->addUser($data))
                $this->session->set_flashdata('msg', 'Usuario agregado correctamente');
            else
                $this->session->set_flashdata('msg_error', 'Ocurrió un error durante el proceso');
            redirect('admin/users');
        } else {
            $data['user'] = $this->user_m->modelState($this->input);
            $data['roles'] = $this->db->get('roles')->result()??[];
        }
        $data['avatars'] = directory_map($this->dir);
        $data['degrees'] = $this->user_m->getDegrees();
        $data['subview'] = 'admin/users/edit';
        $this->load->view('admin/_main_layout', $data);
    }

    public function edit($id = null)
    {
        $origin = $this->input->get('origin');
        $path = $origin ? "/$origin/$id" : '';

        if(!$this->permission->getPermission([USER_CREATE], FALSE))
            show_error("You don't have access to this site", 403, 'DENIED ACCESS');
        unset($this->user_m->newUserRules['password']);
        $rules = $this->user_m->newUserRules;
        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() && $id != null) {
            $data['user'] = $this->user_m->array_from_post(['first_name', 'last_name', 'academic_degree', 'email', 'avatar']);
            $data['roles'] = $this->input->post('role_ids');
            $data['user']['first_name'] = strtoupper($data['user']['first_name']);
            $data['user']['last_name'] = strtoupper($data['user']['last_name']);
            $data['user']['avatar'] = $data['user']['avatar'] ?? AVATARS[0];
            if ($this->input->post('password')) {
                $data['user']['password'] = $this->user_m->hash($this->input->post('password'));
            }
            if ($this->user_m->update($data, $id))
                $this->session->set_flashdata('msg', 'Usuario editado correctamente');
            else
                $this->session->set_flashdata('msg_error', 'Ocurrió un error al intentar guardar el usuario');
            redirect('admin/users'.$path);
        } elseif ($id != null) {
            $data['user'] = $this->user_m->findById($id);
            $data['roles'] = $this->user_m->getRolesState($id);
        } else {
            $this->session->set_flashdata('msg_error', 'No se especificó ningún usuario');
            redirect('admin/users' .$path);
        }
        $data['post'] = $origin ? site_url('admin/users/edit/') . $id . "?origin=$origin" : '';
        $data['path'] = $path;
        $data['avatars'] = directory_map($this->dir);
        $data['degrees'] = $this->user_m->getDegrees();
        if(($data['user'] == null)){
            show_404();
            return;
        }else{
            $data['subview'] = 'admin/users/edit';
        }
        $this->load->view('admin/_main_layout', $data);
    }

    public function delete($id)
    {
        if ($this->permission->getPermission([USER_DELETE], FALSE)) {
            if ($this->user_m->deleteById($id) == TRUE) $this->session->set_flashdata('msg', 'Se eliminó correctamente');
            else $this->session->set_flashdata('msg_error', '!Ops, algo salió mal¡');
        } else show_error("You don't have access to this site", 403, 'DENIED ACCESS');
        redirect('admin/users');
    }

    public function view($id)
    {
        if ($id) {
            if(!$this->permission->getPermission([USER_READ], FALSE))
                show_error("You don't have access to this site", 403, 'DENIED ACCESS');
            $this->user_m->findUserRolesAndPermissions($id);
            $data[USER_READ] = $this->permission->getPermission([USER_READ], FALSE);
            $data[USER_UPDATE] = $this->permission->getPermission([USER_UPDATE], FALSE);
            $data['user'] = $this->user_m->findById($id);
            if(($data['user'] == null)) show_404();
            $data['rolesPermissions'] = $this->user_m->findUserRolesAndPermissions($id);;
            $data['subview'] = 'admin/users/view';
            $this->load->view('admin/_main_layout', $data);
        } else {
            echo "<script>window.history.back();<script>";
        }
    }
}

/* End of file Coins.php */
/* Location: ./application/controllers/admin/Coins.php */