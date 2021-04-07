<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Orm\Users_orm;
use Orm\Paket_orm;
use Orm\Paket_history_orm;
use Orm\Membership_history_orm;
use Illuminate\Database\Capsule\Manager as DB;

use Carbon\Carbon;

class Paket extends MY_Controller
{

	public function __construct(){
        parent::__construct();
		if (!$this->ion_auth->logged_in()) {
			redirect('auth');
		}


    }

    public function index(){

    }

    public function list(){
        $data = [];

        $user = $this->ion_auth->user()->row();
        $data['user'] = Users_orm::findOrFail($user->id);

        $paket_list = Paket_orm::where('show', 1)->get();

        $data['paket_list'] = $paket_list;
        
        view('paket/list', $data);
    }

    public function history($user_id = null){
        $user = $this->ion_auth->user()->row();

        if($this->ion_auth->in_group('mahasiswa'))
            $user = Users_orm::findOrFail($user->id);
        else
            $user = Users_orm::findOrFail($user_id);

        
        $paket_history_list = $user->mhs->paket_history->sortByDesc('id');

        $data['paket_history_list']   = $paket_history_list;


        $data['mhs_matkul_list']   = $user->mhs->mhs_matkul;

        $mhs_membership = get_mhs_aktif_membership($user->mhs);

        $today = Carbon::now();
        $count_expire_days = 'UNLIMITED' ;

        if($mhs_membership->membership_id != MEMBERSHIP_ID_DEFAULT){
            $expired_at = new Carbon($mhs_membership->expired_at);
            if($expired_at->greaterThan($today))
                $count_expire_days = $expired_at->diffInDays($today) . ' Hari Lagi';
            else
                $count_expire_days = '0 Hari Lagi';
        }

        $data['mhs_membership'] = $mhs_membership ;
        $data['count_expire_days'] = $count_expire_days ;

        view('paket/history', $data);

    }

}