<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Orm\Dosen_orm;
use Orm\Dosen_matkul_orm;
use Orm\Users_orm;
use Illuminate\Database\Capsule\Manager as DB;

class Dosen extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		if (!$this->ion_auth->logged_in()) {
			redirect('auth');
		} else if (!$this->ion_auth->is_admin()) {
			show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
		}
		$this->load->library(['datatables', 'form_validation']); // Load Library Ignited-Datatables
		$this->load->model('Master_model', 'master');
		$this->form_validation->set_error_delimiters('', '');
	}
	
	public function index()
	{
		$data = [
			'user' => $this->ion_auth->user()->row(),
			'judul'	=> 'Dosen',
			'subjudul' => 'List Dosen'
		];
//		$this->load->view('_templates/dashboard/_header.php', $data);
//		$this->load->view('master/dosen/data');
//		$this->load->view('_templates/dashboard/_footer.php');
		view('dosen/index',$data);
	}

	protected function _data()
	{
		$this->_json($this->master->getDataDosen(), false);
	}

	public function add()
	{
		$data = [
			'user' => $this->ion_auth->user()->row(),
			'judul'	=> 'Dosen',
			'subjudul' => 'Add Dosen',
			'matkul'	=> $this->master->getAllMatkul()
		];
//		$this->load->view('_templates/dashboard/_header.php', $data);
//		$this->load->view('master/dosen/add');
//		$this->load->view('_templates/dashboard/_footer.php');
		view('dosen/add',$data);
	}

	public function edit($id)
	{
		$dosen_orm = Dosen_orm::findOrFail($id);
		$data = [
			'user' 		=> $this->ion_auth->user()->row(),
			'judul'		=> 'Dosen',
			'subjudul'	=> 'Edit Dosen',
			'matkul'	=> $this->master->getAllMatkul(),
			'dosen'	=> $dosen_orm,
			'user_is_exist'	=> isset(Users_orm::where('username',$dosen_orm->nip)->first()->username) ? true : false,
			'data' 		=> $this->master->getDosenById($id)
		];
//		vdebug($data['dosen']->matkul[0]->id_matkul);
//		$this->load->view('_templates/dashboard/_header.php', $data);
//		$this->load->view('master/dosen/edit');
//		$this->load->view('_templates/dashboard/_footer.php');
		view('dosen/edit',$data);
	}

	protected function _save()
	{
		$method 	= $this->input->post('method', true);
		$id_dosen 	= $this->input->post('id_dosen', true);
		$nama_dosen = $this->input->post('nama_dosen', true);
		$nip        = $this->input->post('nip', true);
		$email 		= $this->input->post('email', true);
		$tgl_lahir 		= $this->input->post('tgl_lahir', true);
		$matkul 	= $this->input->post('matkul[]', true);
//		vdebug($matkul);
		if ($method == 'add') {
			$u_nip = '|is_unique[dosen.nip]';
			$u_email = '|is_unique[dosen.email]';
		} else {
			$dbdata 	= $this->master->getDosenById($id_dosen);
			$u_nip		= $dbdata->nip === $nip ? "" : "|is_unique[dosen.nip]";
			$u_email	= $dbdata->email === $email ? "" : "|is_unique[dosen.email]|is_unique[users.email]";
		}
		$this->form_validation->set_rules('nip', 'NIP', 'required|trim|min_length[10]|max_length[25]' . $u_nip);
		$this->form_validation->set_rules('nama_dosen', 'Nama Dosen', 'required|trim|min_length[3]|max_length[50]');
		$this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email' . $u_email);
		$this->form_validation->set_rules('matkul[]', 'Mata Kuliah', 'required');
		$this->form_validation->set_rules('tgl_lahir', 'Tgl Lahir', ['required','trim', ['check_valid_date', function ($tgl_lahir) {
			if (!empty($tgl_lahir)) {
				$day   = (int)substr($tgl_lahir, 8, 2);
				$month = (int)substr($tgl_lahir, 5, 2);
				$year  = (int)substr($tgl_lahir, 0, 4);
				if (checkdate($month, $day, $year)) {
					return TRUE;
				} else {
					$this->form_validation->set_message('check_valid_date', 'Kolom tanggal salah');
					return FALSE;
				}
			}
		}] ]);

		if ($this->form_validation->run() == FALSE) {
			$data = [
				'status'	=> false,
				'errors'	=> [
					'nip' => form_error('nip'),
					'nama_dosen' => form_error('nama_dosen'),
					'email' => form_error('email'),
					'matkul[]' => form_error('matkul[]'),
					'tgl_lahir' => form_error('tgl_lahir'),
				]
			];
			$this->_json($data);
		} else {
			$input = [
				'nip'			=> $nip,
				'nama_dosen' 	=> $nama_dosen,
				'email' 		=> $email,
				'tgl_lahir' 		=> $tgl_lahir,
				// 'matkul_id' 	=> $matkul
			];
			if ($method === 'add') {
			
//				$action = $this->master->create('dosen', $input);
				try {
					begin_db_trx();
					$dosen = new Dosen_orm;
					$dosen->nip = $input['nip'];
					$dosen->nama_dosen = $input['nama_dosen'];
					$dosen->email = $input['email'];
					$dosen->tgl_lahir = $input['tgl_lahir'];
					$dosen->save();
					
					foreach($matkul as $matkul_id){
						$dosen_matkul = new Dosen_matkul_orm();
						$dosen_matkul->dosen_id = $dosen->id_dosen;
						$dosen_matkul->matkul_id = $matkul_id;
						$dosen_matkul->save();
					}
					
					// MENDAFTARKAN SBG USER
					$nama       = explode(' ', $dosen->nama_dosen, 2);
					$first_name = $nama[0];
					$last_name  = end($nama);
					$full_name  = $dosen->nama_dosen;
					
					$username        = $dosen->nip;
					$password        = date("dmY", strtotime($dosen->tgl_lahir));
					$email           = $dosen->email;
					$tgl_lahir        = date("dmY", strtotime($dosen->tgl_lahir));
					$additional_data = [
						'first_name' => $first_name,
						'last_name'  => $last_name,
						'full_name'  => $full_name,
						'tgl_lahir'  => $tgl_lahir,
					];
					$group           = [ DOSEN_GROUP_ID ]; // Sets user to mhs.
					$this->ion_auth->register($username, $password, $email, $additional_data, $group);
				
					commit_db_trx();
					$action = true;
				
				} catch(\Illuminate\Database\QueryException $e){
					rollback_db_trx();
					$action = false;
			    }
			} else if ($method === 'edit') {
			
//				$action = $this->master->update('dosen', $input, 'id_dosen', $id_dosen);
				
				$dosen = Dosen_orm::findOrFail($id_dosen);
				
				try {
					begin_db_trx();
				
					$dosen->nama_dosen = $input['nama_dosen'];
					$dosen->email = $input['email'];
					$dosen->tgl_lahir = $input['tgl_lahir'];
					$dosen->save();
					
					Dosen_matkul_orm::where('dosen_id',$id_dosen)->delete(); // LOGIKA NYA DI DELETE DULU BARU DI INSERT
					foreach($matkul as $matkul_id){
						$dosen_matkul = new Dosen_matkul_orm();
						$dosen_matkul->dosen_id = $dosen->id_dosen;
						$dosen_matkul->matkul_id = $matkul_id;
						$dosen_matkul->save();
					}
					
					$user = Users_orm::where('username',$dosen->nip)->first();
					
					if(null != $user) {
						$nama       = explode(' ', $dosen->nama_dosen, 2);
						$first_name = $nama[0];
						$last_name  = end($nama);
						
						$user->first_name = $first_name;
						$user->last_name  = $last_name;
						$user->full_name  = $dosen->nama_dosen;
						$user->email      = $dosen->email;
						$user->tgl_lahir  = date("dmY", strtotime($dosen->tgl_lahir));
						$user->save();
					}
					commit_db_trx();
					$action = true;
				
				} catch(\Illuminate\Database\QueryException $e){
					rollback_db_trx();
					$action = false;
			    }
			}

			if ($action) {
				$this->_json(['status' => true]);
			} else {
				$this->_json(['status' => false]);
			}
		}
	}

	protected function _delete()
	{
		$chk = $this->input->post('checked', true);
		if (!$chk) {
			$this->_json(['status' => false]);
		} else {
		
//			$allow = true ;
//			foreach($chk as $c){
//				$dosen = Dosen_orm::findOrFail($c);
//				$user = Users_orm::where('username',$dosen->nip)->first();
//				if($user != null){
//					$allow = false;
//					break;
//				}
//			}
//			if($allow){
//				if ($this->master->delete('dosen', $chk, 'id_dosen')) {
//					$this->_json(['status' => true, 'total' => count($chk)]);
//				}
//			}
			
			try {
			    DB::beginTransaction();
			    foreach($chk as $c) {
				    $dosen = Dosen_orm::findOrFail($c);
				    Users_orm::where('username', $dosen->nip)->delete();
				    $dosen->delete();
			    }
			    DB::commit();
				$this->_json([
					'status' => TRUE,
					'total'  => count($chk)
				]);
			} catch (Exception $e) {
			    DB::rollBack();
			    show_error('Terjadi masalah',500,'Perhatian');
			}
		
		}
	}

	protected function _create_user()
	{
		$id = $this->input->get('id', true);
		$data = $this->master->getDosenById($id);
		$nama = explode(' ',$data->nama_dosen,2);
		$first_name = $nama[0];
		$last_name = end($nama);
		$full_name = $data->nama_dosen;

		$username = $data->nip;
		$password = $data->nip;
		$email = $data->email;
		$additional_data = [
			'first_name'	=> $first_name,
			'last_name'		=> $last_name,
			'full_name'     => $full_name,
		];
		$group = [ DOSEN_GROUP_ID ]; // Sets user to dosen.

		if ($this->ion_auth->username_check($username)) {
			$data = [
				'status' => false,
				'msg'	 => 'Username tidak tersedia (sudah digunakan).'
			];
		} else if ($this->ion_auth->email_check($email)) {
			$data = [
				'status' => false,
				'msg'	 => 'Email tidak tersedia (sudah digunakan).'
			];
		} else {
			$this->ion_auth->register($username, $password, $email, $additional_data, $group);
			$data = [
				'status'	=> true,
				'msg'	 => 'User berhasil dibuat. NIP digunakan sebagai password pada saat login.'
			];
		}
		$this->_json($data);
	}

	public function import($import_data = null)
	{
		$data = [
			'user' => $this->ion_auth->user()->row(),
			'judul'	=> 'Dosen',
			'subjudul' => 'Import Dosen',
			'matkul' => $this->master->getAllMatkul()
		];
		if ($import_data != null) $data['import'] = $import_data;

//		$this->load->view('_templates/dashboard/_header', $data);
//		$this->load->view('master/dosen/import');
//		$this->load->view('_templates/dashboard/_footer');
		view('dosen/import',$data);
	}
	
	public function preview()
	{
		$config['upload_path']		= './uploads/import/';
		$config['allowed_types']	= 'xls|xlsx|csv';
		$config['max_size']			= 2048;
		$config['encrypt_name']		= true;

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('upload_file')) {
			$error = $this->upload->display_errors();
			echo $error;
			die;
		} else {
			$file = $this->upload->data('full_path');
			$ext = $this->upload->data('file_ext');

			switch ($ext) {
				case '.xlsx':
					$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
					break;
				case '.xls':
					$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
					break;
				case '.csv':
					$reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
					break;
				default:
					echo "unknown file ext";
					die;
			}

			$spreadsheet = $reader->load($file);
			$sheetData = $spreadsheet->getActiveSheet()->toArray();
			$data = [];
			for ($i = 1; $i < count($sheetData); $i++) {
				$data[] = [
					'nip' => $sheetData[$i][0],
					'nama_dosen' => $sheetData[$i][1],
					'email' => $sheetData[$i][2],
					'matkul_id' => $sheetData[$i][3]
				];
			}

			unlink($file);

			$this->import($data);
		}
	}

	public function do_import()
	{
		$input = json_decode($this->input->post('data', true));
		$data = [];
		foreach ($input as $d) {
			$data[] = [
				'nip' => $d->nip,
				'nama_dosen' => $d->nama_dosen,
				'email' => $d->email,
				'matkul_id' => $d->matkul_id
			];
		}

		$save = $this->master->create('dosen', $data, true);
		if ($save) {
			redirect('dosen');
		} else {
			redirect('dosen/import');
		}
	}
}
