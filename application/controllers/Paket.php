<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Orm\Users_orm;
use Orm\Paket_orm;
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
        $data['user'] = Users_orm::findOrFail($user->id);

        $paket_list = Paket_orm::where('show', 1)->get();

        $data['paket_list'] = $paket_list;
        
        view('paket/list', $data);
    }

}