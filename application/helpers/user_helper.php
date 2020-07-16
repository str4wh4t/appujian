<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function get_nama_lengkap_user($user = null){
	if(null == $user){
		$nama_lengkap = null ;
		if(null !== session('session_data')){
			$session_data = session('session_data');
			// if($session_data['login_as'] == 'ADMIN') {
				$nama_lengkap = $session_data['nama_lengkap'];
			// }
		}
	}else{
        $nama_lengkap = $user->full_name;
	}
	return $nama_lengkap;
}

function get_selected_role(){
	$role = null ;
	if(null !== session('session_data')){
		$session_data = session('session_data');
        $role = $session_data['login_as'];
	}
	return $role;
}

function is_admin(){
	$ci =& get_instance();
	return $ci->ion_auth->is_admin();
}

function in_group($group){
	$ci =& get_instance();
	return $ci->ion_auth->in_group($group);
}

function get_logged_user(){
	$user = null;
	if(null !== session('session_data')){
		$session_data = session('session_data');
		$user = $session_data['user'];
	}
	return $user;
}
