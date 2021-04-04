<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Orm\Users_orm;
use Orm\Membership_orm;
use Orm\Membership_history_orm;
use Orm\Mhs_orm;
use Illuminate\Database\Capsule\Manager as DB;

use Carbon\Carbon;

class Membership extends MY_Controller
{

	public function __construct(){
        parent::__construct();
		if (!$this->ion_auth->logged_in()) {
			redirect('auth');
		}

        if (!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('mahasiswa'))
            show_404();


    }

    public function index(){

    }

    public function list(){

        $data = [];

        $user = $this->ion_auth->user()->row();
        $data['user'] = $user;

        $membership_list = Membership_orm::where('show', 1)->get();

        $data['membership_list'] = $membership_list;

        $user_aktif_membership = get_user_aktif_membership($user->id);

        $data['user_aktif_membership']  = $user_aktif_membership ;

        $is_valid_membership = true ;

        // vdebug($user_aktif_membership->expired_at);

        $expired_at = new Carbon($user_aktif_membership->expired_at);
        $today = new Carbon();

        if($user_aktif_membership->membership_id != MEMBERSHIP_ID_DEFAULT){
            if($today->greaterThan($expired_at)){
                $is_valid_membership = false;
            }
        }

        $data['is_valid_membership'] = $is_valid_membership;
        
        view('membership/list', $data);
    }

    public function beli($membership_id, $user_id = null){

        $user = $this->ion_auth->user()->row();
        // if($membership_id < $user->membership_id){
        //     show_error('Terjadi kesalahan pembelian', 500, 'Perhatian');
        // }

        $membership_id = integer_read_from_uuid($membership_id);
        $membership = Membership_orm::findOrFail($membership_id);

        $is_valid_order_membership = is_valid_order_membership($membership->id, $user->id) ;

        if(!$is_valid_order_membership){
            show_error('Terjadi kesalahan pembelian', 500, 'Perhatian');
        }

        if($this->ion_auth->in_group('mahasiswa')){
            $user_beli = Users_orm::findOrFail($user->id);
        }else{
            $user_beli = Users_orm::findOrFail($user_id);
        }

        $data['info'] = 'M' . $membership->id ;
        $data['item'] = $membership;
        $data['user']   = $user_beli;

        view('payment/beli', $data);


    }

    public function history($user_id = null){
        $user = $this->ion_auth->user()->row();

        if($this->ion_auth->in_group('mahasiswa'))
            $user = Users_orm::findOrFail($user->id);
        else
            $user = Users_orm::findOrFail($user_id);

        
        $membership_history_list = Membership_history_orm::where('users_id', $user->id)->orderBy('id', 'DESC')->get();

        $data['membership_history_list']   = $membership_history_list;

        $user_membership = get_user_aktif_membership($user->id);

        $today = Carbon::now();
        $count_expire_days = 'UNLIMITED' ;

        if($user_membership->membership_id != MEMBERSHIP_ID_DEFAULT){
            $expired_at = new Carbon($user_membership->expired_at);
            if($expired_at->greaterThan($today))
                $count_expire_days = $expired_at->diffInDays($today) . ' Hari Lagi';
            else
                $count_expire_days = '0 Hari Lagi';
        }

        $data['count_expire_days'] = $count_expire_days ;

        view('membership/history', $data);

    }

}