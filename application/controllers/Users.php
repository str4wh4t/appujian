<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Orm\Users_orm;
use Orm\Mhs_orm;
use Orm\Dosen_orm;
use GuzzleHttp\Exception\ClientException;

class Users extends MY_Controller {

	public function __construct(){
		parent::__construct();
		if (!$this->ion_auth->logged_in()){
			redirect('auth');
		}
		$this->load->library(['datatables', 'form_validation']);// Load Library Ignited-Datatables
		$this->load->model('Users_model', 'users');
		$this->load->model('Master_model', 'master');
//		$this->load->model('User_orm_model', 'user_orm');
		$this->form_validation->set_error_delimiters('','');
	}
	

    protected function _data()
    {
		$id = $this->input->post('id');

		$this->_akses_admin_dan_koord_pengawas();

        $this->_json($this->users->getDataUsers($id, get_selected_role()), false);
    }

    public function index()
	{
		$this->_akses_admin_dan_koord_pengawas();

		$data = [
			'user' => $this->ion_auth->user()->row(),
			'judul'	=> 'User',
			'subjudul'=> 'List User'
		];
//		$this->load->view('_templates/dashboard/_header.php', $data);
//		$this->load->view('users/data');
//		$this->load->view('_templates/dashboard/_footer.php');
		view('users/index',$data);
	}
	
	public function edit($id = null)
	{	
		if(empty($id)){
			$id = $this->ion_auth->user()->row()->id;
		}else{
			if(is_admin() || in_group(KOORD_PENGAWAS_GROUP_ID)){
				$user_orm = Users_orm::findOrFail($id);
			}else{
				$id = $this->ion_auth->user()->row()->id;
			}
		}
		
		$level = $this->ion_auth->get_users_groups($id)->result();
		$user_login = $this->ion_auth->user()->row();
		$user_cari = $this->ion_auth->user($id)->row();
		$data = [
			'user_login' 		=> $user_login,
			'judul'		=> 'User Management',
			'subjudul'	=> 'Edit Data User',
			'user_cari' 	=> $user_cari,
			'groups'	=> $this->ion_auth->groups()->result(),
			'level'		=> $level[0]
		];
		
//		vdebug($data);
//		$this->load->view('_templates/dashboard/_header.php', $data);
//		$this->load->view('users/edit');
//		$this->load->view('_templates/dashboard/_footer.php');
		if($user_login->id == $user_cari->id)
			view('users/edit_pribadi',$data);
		else
			view('users/edit',$data);
	}

	public function edit_info()
	{
		
		if(is_admin()){
			$id = $this->input->post('id', true);
		}else{
			$id = $this->ion_auth->user()->row()->id;
		}
		
		$user = Users_orm::findOrFail($id);
	
//		$this->_akses_admin();
		$this->form_validation->set_rules('id', 'Id User', 'required');
//		$this->form_validation->set_rules('username', 'Username', 'required');
//		$this->form_validation->set_rules('first_name', 'First Name', 'required');
//		$this->form_validation->set_rules('last_name', 'Last Name', 'required');
		$this->form_validation->set_rules('full_name', 'Nama', 'required');
		
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		
//		if($user->email == $this->input->post('email', true))
//			$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
//		else
//			$this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]');
		
		if($this->form_validation->run()===FALSE){
			$data['status'] = false;
			$data['errors'] = [
				'id' => form_error('id'),
//				'username' => form_error('username'),
//				'first_name' => form_error('first_name'),
//				'last_name' => form_error('last_name'),
				'full_name' => form_error('full_name'),
				'email' => form_error('email'),
			];
		}else{
			
			
			
			try {
				begin_db_trx();
				
				$nama = explode(' ', $this->input->post('full_name', true),2);
				$first_name = $nama[0];
				$last_name = end($nama);
			
				$user->first_name = $first_name ;
				$user->last_name = $last_name ;
				$user->full_name = $this->input->post('full_name', true) ;
				$user->email = $this->input->post('email', true) ;
				$user->save();
				
				$is_mhs = $user->groups()->where('name','mahasiswa')->get();
				
				if ($is_mhs->isNotEmpty()) {
					// IS MHS
					$mhs        = Mhs_orm::where('nim', $user->username)
					                     ->firstOrFail();
					$mhs->nama  = $user->full_name;
					$mhs->email = $user->email;
					$mhs->save();
				}
				
				$is_dosen = $user->groups()->where('name','dosen')->get();
				
				if ($is_dosen->isNotEmpty()) {
					// IS DOSEN
					$dosen        = Dosen_orm::where('nip', $user->username)
					                         ->firstOrFail();
					$dosen->nama_dosen  = $user->full_name;
					$dosen->email = $user->email;
					$dosen->save();
				}
				commit_db_trx();
				$action = true;
			
			} catch(\Illuminate\Database\QueryException $e){
				rollback_db_trx();
				$action = false;
		    }
			
			$data['status'] = $action ? true : false;
		}
		$this->_json($data);
	}

	public function edit_status()
	{
		$this->_akses_admin();
		$this->form_validation->set_rules('id', 'Id User', 'required');
		$this->form_validation->set_rules('status', 'Status', 'required');
		
		if($this->form_validation->run()===FALSE){
			$data['status'] = false;
			$data['errors'] = [
				'id' => form_error('id'),
				'status' => form_error('status'),
			];
		}else{
			$id = $this->input->post('id', true);
			$input = [
				'active' 		=> $this->input->post('status', true),
			];
			$update = $this->master->update('users', $input, 'id', $id);
			$data['status'] = $update ? true : false;
		}
		$this->_json($data);
	}
	
	public function edit_level()
	{
		$this->_akses_admin();
		$this->form_validation->set_rules('id', 'Id User', 'required');
		$this->form_validation->set_rules('level', 'Level', 'required');
		
		if($this->form_validation->run()===FALSE){
			$data['status'] = false;
			$data['errors'] = [
				'id' => form_error('id'),
				'level' => form_error('level'),
			];
		}else{
			$id = $this->input->post('id', true);
			$input = [
				'group_id' 		=> $this->input->post('level', true),
			];
			$update = $this->master->update('users_groups', $input, 'user_id', $id);
			$data['status'] = $update ? true : false;
		}
		$this->_json($data);
	}

	public function change_password()
	{
		$this->form_validation->set_rules('id', 'Id User', 'required');
		$this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
		$this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[new_confirm]');
		$this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');
		
		if ($this->form_validation->run() === FALSE){
			$data = [
				'status' => false,
				'errors' => [
					'id' => form_error('id'),
					'old' => form_error('old'),
					'new' => form_error('new'),
					'new_confirm' => form_error('new_confirm')
				]
			];
		}else{
			$identity = $this->session->userdata('identity');
			$change = $this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'));
			if($change){
				$data['status'] = true;
			}
			else{
				$data = [
					'status' 	=> false,
					'msg'		=> $this->ion_auth->errors()
				];
			}
		}
		$this->_json($data);
	}
	
	public function reset_password_by_admin(){

		$this->_akses_admin_dan_koord_pengawas();

		$this->form_validation->set_rules('id', 'Id User', 'required');
		
		if($this->form_validation->run()===FALSE){
			$data['status'] = false;
			$data['errors'] = [
				'id' => form_error('id'),
			];
		}else{
			$id = $this->input->post('id', true);
			$users = Users_orm::findOrFail($id);
			$level = $this->ion_auth->get_users_groups($id)->result();
			$pass_default = $users->tgl_lahir;
			if($level[0]->id == MHS_GROUP_ID)
				$pass_default = $users->no_billkey;
			if($users->count() > 0){
			    $data = [
	                'password' => $pass_default,
	            ];
			    $change = $this->ion_auth->update($id, $data);
				$data['status'] = $change ? true : false;
			}else{
				$data['status'] = false;
				$data['errors'] = [
					'id' => 'User tidak ditemukan',
				];
			}
		}
		$this->_json($data);
	}

	public function delete($id)
	{
		$this->_akses_admin_dan_koord_pengawas();

		$user = $this->ion_auth->user()->row();

		if($id == $user->id) show_404(); // JIKA YG DIDELETE ADALAH DIRI SENDIRI

		$data['status'] = $this->ion_auth->delete_user($id) ? true : false;
		$this->_json($data);
	}
	
	protected function _cari_pegawai(){
		
		$nip = $this->input->post('nip', true);
		$client = new \GuzzleHttp\Client();
		$data = [];
		try{
			$options = [
				    'form_params' => [
				        'nip' => $nip,
				        'auth' => '{"user":"sempak","pass":"teles"}'
				    ]
			   ];
			$response = $client->post('https://siap.undip.ac.id/apidata/get_data_pegawai', $options);

			// echo $response->getBody();

			if($response->getStatusCode() == 200){
				$json  = $response->getBody()->getContents();
				$return = json_decode($json);
				$data['record'] = $return->record;
				$this->_json($data);
			}else{
				show_error('Terjadi kesalahan, status : ' . $response->getStatusCode(), 500, 'Perhatian');
			}
		}catch(ClientException $e){
			show_error('Terjadi kesalahan, ' . $e->getMessage(), 500, 'Perhatian');
		}
	}
	
	protected function _save_pengawas()
	{

		$this->_akses_admin_dan_koord_pengawas();
		
		$nip = $this->input->post('nip', true);
		$is_koord_pengawas = $this->input->post('is_koord_pengawas', true);

		$client = new \GuzzleHttp\Client();
		$data = [];
		try{
			$options = [
				    'form_params' => [
				        'nip' => $nip,
				        'auth' => '{"user":"sempak","pass":"teles"}'
				    ]
			   ];
			$response = $client->post('https://siap.undip.ac.id/apidata/get_data_pegawai', $options);

			// echo $response->getBody();

			if($response->getStatusCode() == 200){
				$json  = $response->getBody()->getContents();
				$return = json_decode($json);
				$record = $return->record;
				
				$record_nama = trim($record->nama);
				$nama       = explode(' ', $record_nama, 2);
				$first_name = $nama[0];
				$last_name  = end($nama);
				$full_name  = $record_nama;
				
				$username        = $record->nip; // JADI TIDAK AKAN DOBEL INSERT
				$password        = date("dmY", strtotime($record->tgl_lahir));
				$email           = $record->email;
				$tgl_lahir        = date("dmY", strtotime($record->tgl_lahir));
				$additional_data = [
					'first_name' => $first_name,
					'last_name'  => $last_name,
					'full_name'  => $full_name,
					'tgl_lahir'  => $tgl_lahir,
				];
				
				if(!$is_koord_pengawas)
					$group           = [ PENGAWAS_GROUP_ID ]; // Sets user to pengawas
				else
					$group           = [ KOORD_PENGAWAS_GROUP_ID ]; // Sets user to pengawas

				$msg = null;

				try {
					begin_db_trx();
					$status = $this->ion_auth->register($username, $password, $email, $additional_data, $group);
					commit_db_trx();
				}catch(Exception $e){
					rollback_db_trx();
					$msg = $e->getMessage();
					$status = false ;
				}

				$this->_json(['status' => $status, 'msg' => $msg]);
			}else{
				show_error('Terjadi kesalahan, status : ' . $response->getStatusCode(), 500, 'Perhatian');
			}
		}catch(ClientException $e){
			show_error('Terjadi kesalahan, ' . $e->getMessage(), 500, 'Perhatian');
		}
		
		
	}

	protected function _save_penyusun_soal()
	{
		$this->_akses_admin();

		$nm_lengkap = $this->input->post('nm_lengkap', true);
		$tgl_lahir = $this->input->post('tgl_lahir', true);
		$email = $this->input->post('email', true);
		$email = strtolower($email);

		$this->form_validation->set_rules('nm_lengkap', 'Nama', 'required');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]');
		$this->form_validation->set_rules(
					'tgl_lahir', 
					'Tgl Lahir', 
					[
						'required', 
						'trim', 
						[
							'check_valid_date', 
							function ($tgl_lahir) {
								if (!empty($tgl_lahir)) {
									$d = DateTime::createFromFormat('Y-m-d', $tgl_lahir);
									return $d && $d->format('Y-m-d') == $tgl_lahir;
								}
							}
						]
						],
						[
							'check_valid_date' => 'Kolom tanggal salah',
						]
				);
		
		$status = true ;
		$msg = null;

		if ($this->form_validation->run() === FALSE)
		{
			$status = false;
			$msg = $this->form_validation->error_string();
		}else{
			
			$nama       = explode(' ', $nm_lengkap, 2);
			$first_name = $nama[0];
			$last_name  = end($nama);
			$full_name  = $nm_lengkap;
			
			$username        = date('ymdHis'); // USERNAME DIGENERATE OTOMATIS
			$password        = date("dmY", strtotime($tgl_lahir));
			$email           = $email;
			$tgl_lahir        = date("dmY", strtotime($tgl_lahir));
			$additional_data = [
				'first_name' => $first_name,
				'last_name'  => $last_name,
				'full_name'  => $full_name,
				'tgl_lahir'  => $tgl_lahir,
			];
			
			$group           = [ PENYUSUN_SOAL_GROUP_ID ]; // Sets user to penyusun soal
			
			try {
				begin_db_trx();
				$status = $this->ion_auth->register($username, $password, $email, $additional_data, $group);
				commit_db_trx();
			}catch(Exception $e){
				rollback_db_trx();
				$msg = $e->getMessage();
				$status = false ;
			}

		}

		$this->_json(['status' => $status, 'msg' => $msg]);
		
	}
}
