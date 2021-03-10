<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Orm\Dosen_orm;
use Orm\Dosen_matkul_orm;
use Orm\Users_orm;
use Orm\Membership_orm;
use Illuminate\Database\Capsule\Manager as DB;

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
        $data['user'] = $user;

        $membership_list = Membership_orm::where('show', 1)->get();
        $data['membership_list'] = $membership_list;
        
        view('paket/list', $data);
    }

}