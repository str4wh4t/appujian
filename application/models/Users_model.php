<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\MySQL;

class Users_model extends CI_Model {
    
    public function getDatausers($id = null, $role)
    {

        $config = [
        	'host'     => $this->db->hostname,
            'port'     => $this->db->port,
            'username' => $this->db->username,
            'password' => $this->db->password,
            'database' => $this->db->database,
        ];
    	
	    $dt = new Datatables( new MySQL($config) );

        $this->db->select('users.id, username, full_name, email, FROM_UNIXTIME(created_on) as created_on, last_login, active, groups.name as level');
        $this->db->from('users_groups');
        $this->db->join('users', 'users_groups.user_id=users.id');
        $this->db->join('groups', 'users_groups.group_id=groups.id'); 
        if(!empty($id)){
            $this->db->where('users.id !=', $id);
        }
        if($role->name == 'koord_pengawas'){
            $this->db->where_in('groups.id', [PENGAWAS_GROUP_ID, MHS_GROUP_ID]);
        }

        
        $query = $this->db->get_compiled_select() ; // GET QUERY PRODUCED BY ACTIVE RECORD WITHOUT RUNNING I
        
        $dt->query($query);

        $identity = $this->config->item('identity', 'ion_auth');
        
        $dt->edit('active', function ($data) use($identity){
            $active = $data['active'];
            if($this->ion_auth->is_max_login_attempts_exceeded($data[$identity])){
                $active = LOCKED_USER_ID;
            }
            return $active ;
        });

        // $dt->edit('oleh', function ($data) use ($user_orm) {
        //     $user = $user_orm->where('username',$data['oleh'])->first();
        //     return $user != null ? $user->full_name : '';
        // });

        return $dt->generate();

    }
}
