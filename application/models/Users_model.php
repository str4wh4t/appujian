<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users_model extends CI_Model {
    
    public function getDatausers($id = null, $role)
    {
        $this->datatables->select('users.id, username, full_name, email, FROM_UNIXTIME(created_on) as created_on, last_login, active, groups.name as level');
        $this->datatables->from('users_groups');
        $this->datatables->join('users', 'users_groups.user_id=users.id');
        $this->datatables->join('groups', 'users_groups.group_id=groups.id');
        if(!empty($id)){
            $this->datatables->where('users.id !=', $id);
        }
        if($role->name == 'koord_pengawas'){
            $this->datatables->where_in('groups.id', [PENGAWAS_GROUP_ID, MHS_GROUP_ID]);
        }
        return $this->datatables->generate();
    }
}
