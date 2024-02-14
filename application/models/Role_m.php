<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Role_m extends MY_Model
{

    protected $_table_name = 'roles';
    public $rules = [
        'name' => array(
            'field' => 'name',
            'label' => 'nombre',
            'rules' => 'trim|required|max_length[30]'
        ),
        'description' => array(
            'field' => 'description',
            'label' => 'descripción',
            'rules' => 'max_length[250]'
        )
    ];


    public function modelState($input, $permissions){
        $role = new stdClass();
        $role->name = $input->post('name')??'';
        $role->description = $input->post('description')??'';
        $permissionIds = $input->post('permission_ids')??[];
        foreach($permissions as  $permission){
            $permission->is_selected = in_array($permission->id, $permissionIds, TRUE)?1:0;
        }
        return ['role' =>$role, 'permissions' => $permissions];
    }

    /**
     * Muestra todos los roles con paginación
     */
    public function findAll($start, $length, $search, $order)
    {
        $this->db->select('COUNT(id) total');
        $this->db->from('roles');
        $this->db->where("id LIKE '%$search%' OR name LIKE '%$search%'");
        $data['recordsFiltered'] = $this->db->get()->row()->total ?? 0;

        $this->db->select('*');
        $this->db->from('roles');
        $this->db->where("id LIKE '%$search%' OR name LIKE '%$search%'");
        $this->db->limit($length, $start);
        $this->db->order_by($order['column'], $order['dir']);
        $data['data'] = $this->db->get()->result() ?? [];

        return $data;
    }

    /**
     * Obtiene el rol y sus permisos
     */
    public function findById($id)
    {
        $data['role'] = $this->db->get_where('roles r', ['r.id' => $id])->row()?? null;

        $this->db->select('p.*');
        $this->db->from('permissions p');
        $this->db->join('roles_permissions rp', 'rp.permission_id = p.id');
        $this->db->where('rp.role_id', $id);
        $this->db->order_by('p.name');
        $data['permissions'] = $this->db->get()->result() ?? [];

        return $data;
    }

    /**
     * Crea el rol y los permisos para el rol
     */
    public function addRole($data)
    {
        $this->db->trans_begin();
        try {
            // Guardar rol
            $this->save($data['role']);
            $role_id = $this->db->insert_id();
            // Guardar permisos
            foreach ($data['permissions'] as $permission_id) {
                $rolePermission = ['role_id' => $role_id, 'permission_id' => $permission_id];
                $this->db->insert('roles_permissions', $rolePermission);
            }
            // Confirmar transacción
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return FALSE;
            } else {
                $this->db->trans_commit();
                return TRUE;
            }
        } catch (Exception $e) {
            $this->db->trans_rollback();
            return FALSE;
        }
    }

    /**
     * Actualiza el rol y los permisos para el rol
     */
    public function update($data, $role_id)
    {
        $this->db->trans_begin();
        try {
            // Guardar rol
            $this->save($data['role'], $role_id);
            // Cargar permisos actuales
            $permissionsNow = $this->db->get_where('roles_permissions rp', ['rp.role_id' => $role_id])->result()??[];
            // Quitar permisos que estan en bd pero no están en nueva lista
            foreach($permissionsNow as  $item){
                if(!in_array($item->permission_id, $data['permissions'], TRUE)){
                    $this->db->delete('roles_permissions', ['id' =>  $item->id]);
                }
            }
            // Agregar permisos que no están en BD pero si en la nueva lista
            foreach ($data['permissions'] as $permission_id) {
                $exist = $this->db->select('IF(COUNT(rp.id) > 0, 1, 0) exist')
                ->from('roles_permissions rp')
                ->where("rp.role_id = $role_id AND rp.permission_id = $permission_id")
                ->get()->row()->exist??0;
                if(!$exist){
                    $rolePermission = ['role_id' => $role_id, 'permission_id' => $permission_id];
                    $this->db->insert('roles_permissions', $rolePermission);
                }
            }
            // Confirmar transacción
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                return FALSE;
            } else {
                $this->db->trans_commit();
                return TRUE;
            }
        } catch (Exception $e) {
            $this->db->trans_rollback();
            return FALSE;
        }
    }

    /**
     * Cargar los permisos con sus estadados para el rol
     * $id: role
     */
    public function getPermissionsState($role_id){
        $this->db->select("p.*, EXISTS(
            SELECT rp.id FROM roles_permissions rp WHERE rp.role_id = $role_id AND rp.permission_id = p.id
        ) is_selected");
        $this->db->from('permissions p');
        $this->db->order_by('p.name');
        return $this->db->get()->result()??[];
    }

    /**
     * Elimina permiso de un rol
     */
    public function deleteById($id)
    {
        return $this->db->delete('roles', array('id' => $id));
    }
}
