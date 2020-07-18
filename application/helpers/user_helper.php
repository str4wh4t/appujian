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

// Function to get the client IP address
function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}
