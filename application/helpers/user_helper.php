<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Orm\Tahun;
use Orm\Users_orm;
use Orm\Membership_orm;
use Orm\Membership_history_orm;
use Carbon\Carbon;

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

function get_selected_gel(){
	return 1;
}

function get_selected_smt(){
	return 1;
}

function get_selected_tahun(){
	return Tahun::get_tahun_aktif();
}


function get_membership_text(int $membership_id): string{
	$membership = Membership_orm::findOrFail($membership_id);
	return $membership->name;
}



function  get_membership_star(int $membership_id, string $size = 'medium'): string{
	
	// $return = '';
	// switch($membership_id){
	// 	case 2:
	// 		$stars = '';
	// 		foreach(){
	// 			$stars .= '<i class="fa fa-star font-'. $size .'-2"></i>';
	// 		}
	// 		$return = $stars;
	// 		break;
	// 	case 3:
	// 		$return = '<i class="fa fa-star font-'. $size .'-2"></i><i class="fa fa-star font-'. $size .'-2"></i>';
	// 		break;
	// 	default:
	// 		$return = '';
	// 		break;
	// }

	$stars = '';
	for($i = 2; $i <= $membership_id; $i++ ){
		$stars .= '<i class="fa fa-star font-'. $size .'-2"></i>';
	}

	return $stars;
}

function  get_membership_color(int $membership_id): string{
	
	$membership = Membership_orm::findOrFail($membership_id);
	return $membership->text_color;
}


function get_user_aktif_membership(int $user_id){
	$ci =& get_instance();
	$user = Users_orm::findOrFail($user_id);

	$membership_history = $user->membership_history()->where('stts', MEMBERSHIP_STTS_AKTIF)->firstOrFail(); 
	return $membership_history ;

}

function is_valid_order_membership($membership_id, $user_id){
	$is_valid_order_membership = false ;
	$user_aktif_membership = get_user_aktif_membership($user_id);

	if($membership_id != MEMBERSHIP_ID_DEFAULT){
		if($user_aktif_membership->membership_id < $membership_id){
			// JIKA PEMBELIAN DIATAS MEMBERSHIP TIDAK MELIHAT TGL EXPIRED
			$is_valid_order_membership = true;
		}else{
			// JIKA PEMBELIAN SAMA / DIBAWAH MEMBERSHIP TIDAK MELIHAT TGL EXPIRED
			$expired_at = new Carbon($user_aktif_membership->expired_at);
			$today = new Carbon();

			if($today->greaterThan($expired_at)){
				$is_valid_order_membership = true;
			}
		}
	}

	return $is_valid_order_membership;
}


function is_user_membership_expired(int $user_id = null): bool{
	$ci =& get_instance();

	$user = $ci->ion_auth->user()->row();

	if($ci->ion_auth->in_group('mahasiswa'))
		$user_orm = Users_orm::findOrFail($user->id);
	else
		$user_orm = Users_orm::findOrFail($user_id);

	$user_membership = get_user_aktif_membership($user_orm->id);
	$expired_at = new Carbon($user_membership->expired_at);
	$today = new Carbon();

	$expired = false ;
	if($today->greaterThan($expired_at)){
		$expired = true;
	}

	return $expired;
}
