<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Orm\Users_orm;
use Orm\Mhs_orm;
use Orm\Dosen_orm;
use Illuminate\Database\Eloquent\Builder;

class Users extends CI_Controller {

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
	
	public function is_admin()
	{
		if (!$this->ion_auth->is_admin()){
			show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="'.base_url('dashboard').'">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
		}
	}

	public function output_json($data, $encode = true)
	{
        if($encode) $data = json_encode($data);
        $this->output->set_content_type('application/json')->set_output($data);
	}
	

    public function data($id = null)
    {
		$this->is_admin();
        $this->output_json($this->users->getDataUsers($id), false);
    }

    public function index()
	{
		$this->is_admin();
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
	
//		$this->is_admin();
		$this->form_validation->set_rules('id', 'Id User', 'required');
//		$this->form_validation->set_rules('username', 'Username', 'required');
//		$this->form_validation->set_rules('first_name', 'First Name', 'required');
//		$this->form_validation->set_rules('last_name', 'Last Name', 'required');
		$this->form_validation->set_rules('full_name', 'Nama', 'required');
		
		if($user->email == $this->input->post('email', true))
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		else
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]');
		
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
		$this->output_json($data);
	}

	public function edit_status()
	{
		$this->is_admin();
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
		$this->output_json($data);
	}
	
	public function edit_level()
	{
		$this->is_admin();
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
		$this->output_json($data);
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
		$this->output_json($data);
	}
	
	public function reset_password_by_admin(){
		$this->is_admin();
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
		$this->output_json($data);
	}

	public function delete($id)
	{
		$this->is_admin();
		$data['status'] = $this->ion_auth->delete_user($id) ? true : false;
		$this->output_json($data);
	}
}
