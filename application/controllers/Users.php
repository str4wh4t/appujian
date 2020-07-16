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
	

    public function data($id = null)
    {
		$this->_akses_admin();
        $this->_json($this->users->getDataUsers($id), false);
    }

    public function index()
	{
		$this->_akses_admin();
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
	
	public function edit($id = "")
	{
		if(is_admin()){
			Users_orm::findOrFail($id);
		}else{
			$id = $this->ion_auth->user()->row()->id;
		}
		
		$level = $this->ion_auth->get_users_groups($id)->result();
		$data = [
			'user' 		=> $this->ion_auth->user()->row(),
			'judul'		=> 'User Management',
			'subjudul'	=> 'Edit Data User',
			'users' 	=> $this->ion_auth->user($id)->row(),
			'groups'	=> $this->ion_auth->groups()->result(),
			'level'		=> $level[0]
		];
//		$this->load->view('_templates/dashboard/_header.php', $data);
//		$this->load->view('users/edit');
//		$this->load->view('_templates/dashboard/_footer.php');
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
				
				if ($is_mhs->count()) {
					// IS MHS
					$mhs        = Mhs_orm::where('nim', $user->username)
					                     ->firstOrFail();
					$mhs->nama  = $user->full_name;
					$mhs->email = $user->email;
					$mhs->save();
				}
				
				$is_dosen = $user->groups()->where('name','dosen')->get();
				
				if ($is_dosen->count()) {
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
		$this->_akses_admin();
		$this->form_validation->set_rules('id', 'Id User', 'required');
		
		if($this->form_validation->run()===FALSE){
			$data['status'] = false;
			$data['errors'] = [
				'id' => form_error('id'),
			];
		}else{
			$id = $this->input->post('id', true);
			$users = Users_orm::find($id);
			if($users->count() > 0){
			    $data = [
	                'password' => $users->tgl_lahir,
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
		$this->_akses_admin();
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
	
	protected function _save_pengawas(){
		
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
				$record = $return->record;
				
				$nama       = explode(' ', $record->nama, 2);
				$first_name = $nama[0];
				$last_name  = end($nama);
				$full_name  = $record->nama;
				
				$username        = $record->nip;
				$password        = date("dmY", strtotime($record->tgl_lahir));
				$email           = $record->email;
				$tgl_lahir        = date("dmY", strtotime($record->tgl_lahir));
				$additional_data = [
					'first_name' => $first_name,
					'last_name'  => $last_name,
					'full_name'  => $full_name,
					'tgl_lahir'  => $tgl_lahir,
				];
				$group           = [ PENGAWAS_GROUP_ID ]; // Sets user to pengawas
				$status = $this->ion_auth->register($username, $password, $email, $additional_data, $group);
				$this->_json(['status' => $status]);
			}else{
				show_error('Terjadi kesalahan, status : ' . $response->getStatusCode(), 500, 'Perhatian');
			}
		}catch(ClientException $e){
			show_error('Terjadi kesalahan, ' . $e->getMessage(), 500, 'Perhatian');
		}
		
		
	}
}
