<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Orm\Mhs_orm;
use Orm\Mhs_matkul_orm;
use Orm\Users_orm;
use Orm\Matkul_orm;
use Illuminate\Database\Capsule\Manager as DB;

class Mahasiswa extends MY_Controller
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
			'judul'	=> 'Peserta Ujian',
			'subjudul' => 'List Peserta Ujian'
		];
//		$this->load->view('_templates/dashboard/_header.php', $data);
//		$this->load->view('master/mahasiswa/data');
//		$this->load->view('_templates/dashboard/_footer.php');
		view('mahasiswa/index',$data);
	}

	protected function _data()
	{
		$this->_json($this->master->getDataMahasiswa(), false);
	}

	public function add()
	{
		$data = [
			'user' => $this->ion_auth->user()->row(),
			'judul'	=> 'Peserta Ujian',
			'subjudul' => 'Tambah Data Peserta Ujian',
			'matkul'	=> $this->master->getAllMatkul()
		];
//		$this->load->view('_templates/dashboard/_header.php', $data);
//		$this->load->view('master/mahasiswa/add');
//		$this->load->view('_templates/dashboard/_footer.php');
		view('mahasiswa/add',$data);
	}

	public function edit($id)
	{
		$mhs_orm = Mhs_orm::findOrFail($id);
		$mhs = $this->master->getMahasiswaById($id);
		$data = [
			'user' 		=> $this->ion_auth->user()->row(),
			'judul'		=> 'Peserta Ujian',
			'subjudul'	=> 'Edit Data Peserta Ujian',
//			'jurusan'	=> $this->master->getJurusan(),
//			'kelas'		=> $this->master->getKelasByJurusan($mhs->jurusan_id),
			'mahasiswa' => $mhs,
			'matkul'	=> $this->master->getAllMatkul(),
			'mhs'	=> $mhs_orm,
			'user_is_exist'	=> isset(Users_orm::where('username',$mhs_orm->nim)->first()->username) ? true : false,
		];
//		$this->load->view('_templates/dashboard/_header.php', $data);
//		$this->load->view('master/mahasiswa/edit');
//		$this->load->view('_templates/dashboard/_footer.php');
		view('mahasiswa/edit',$data);
	}

	private function _validasi_mahasiswa($method)
	{
		$id_mahasiswa 	= $this->input->post('id_mahasiswa', true);
		$nim 			= $this->input->post('nim', true);
		$nik 			= $this->input->post('nik', true);
		$email 			= $this->input->post('email', true);
		$tgl_lahir			= $this->input->post('tgl_lahir', true);
		$foto			= $this->input->post('foto', true);
		
		if ($method == 'add') {
			$u_nim = '|is_unique[mahasiswa.nim]';
			$u_email = '|is_unique[mahasiswa.email]';
			$u_nik = '|is_unique[mahasiswa.nik]';

		} else {
			$dbdata 	= $this->master->getMahasiswaById($id_mahasiswa);
			$u_nim		= $dbdata->nim === $nim ? "" : "|is_unique[mahasiswa.nim]";
			$u_nik		= $dbdata->nik === $nik ? "" : "|is_unique[mahasiswa.nik]";
			$u_email	= $dbdata->email === $email ? "" : "|is_unique[mahasiswa.email]|is_unique[users.email]";
		}
		$this->form_validation->set_rules('nim', 'No Peserta', 'required|is_natural_no_zero|trim|exact_length['.MHS_ID_LENGTH.']' . $u_nim);
		$this->form_validation->set_rules('nama', 'Nama', 'required|trim|min_length[3]|max_length[250]');
		// UNTUK SELANJUTNY USER DGN NIK DAN EMAIL YG SAMA DAPAT MENDAFTAR UJIAN KEMBALI
		// $this->form_validation->set_rules('nik', 'NIK', 'required|is_natural_no_zero|trim|exact_length['.NIK_LENGTH.']' . $u_nik);
		// $this->form_validation->set_rules('email', 'Email', 'required|trim|max_length[250]|valid_email' . $u_email);
		$this->form_validation->set_rules('nik', 'NIK', 'required|is_natural_no_zero|trim|exact_length['.NIK_LENGTH.']');
		$this->form_validation->set_rules('email', 'Email', 'required|trim|max_length[250]|valid_email');
		$this->form_validation->set_rules('tmp_lahir', 'Tmp Lahir', 'required|trim|min_length[3]|max_length[250]');
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
		
		$this->form_validation->set_rules('foto', 'Foto', ['required','trim', ['check_valid_img_url', function($foto){
			if(!empty($foto)) {
				if ($size = @getimagesize($foto)){
					if (strtolower(substr($size['mime'], 0, 5)) == 'image') {
						return TRUE;
					} else {
						$this->form_validation->set_message('check_valid_img_url', 'Kolom foto salah');
						return FALSE;
					}
				}else{
					$this->form_validation->set_message('check_valid_img_url', 'Kolom foto salah');
					return FALSE;
				}
			}
		}] ]);
  
		$this->form_validation->set_rules('jenis_kelamin', 'Jenis Kelamin', 'required|in_list[L,P]');
		$this->form_validation->set_rules('matkul[]', 'Mata Kuliah', 'required');
//		$this->form_validation->set_rules('jurusan', 'Jurusan', 'required');
//		$this->form_validation->set_rules('kelas', 'Kelas', 'required');

		$this->form_validation->set_message('required', 'Kolom {field} wajib diisi');
	}

	protected function _save()
	{
		$method = $this->input->post('method', true);
		$this->_validasi_mahasiswa($method);

		if ($this->form_validation->run() == FALSE) {
			$data = [
				'status'	=> false,
				'errors'	=> [
					'nim' => form_error('nim'),
					'nama' => form_error('nama'),
					'nik' => form_error('nik'),
					'tmp_lahir' => form_error('tmp_lahir'),
					'tgl_lahir' => form_error('tgl_lahir'),
					'email' => form_error('email'),
					'foto' => form_error('foto'),
					'jenis_kelamin' => form_error('jenis_kelamin'),
					'matkul[]' => form_error('matkul[]'),
//					'jurusan' => form_error('jurusan'),
//					'kelas' => form_error('kelas'),
				]
			];
			$this->_json($data);
		} else {
			$input = [
				'nim' 			=> $this->input->post('nim', true),
				'email' 		=> $this->input->post('email', true),
				'nama' 			=> $this->input->post('nama', true),
				'nik' 			=> $this->input->post('nik', true),
				'tmp_lahir' 	=> $this->input->post('tmp_lahir', true),
				'tgl_lahir' 	=> $this->input->post('tgl_lahir', true),
				'nama' 			=> $this->input->post('nama', true),
				'foto' 			=> $this->input->post('foto', true),
				'jenis_kelamin' => $this->input->post('jenis_kelamin', true),
				'matkul'        => $this->input->post('matkul[]', true),
//				'kelas_id' 		=> $this->input->post('kelas', true),
			];
			if ($method === 'add') {
			
//				$action = $this->master->create('mahasiswa', $input);
				
				try {
					begin_db_trx();
					$mhs = new Mhs_orm;
					$mhs->nim = $input['nim'];
					$mhs->nama = $input['nama'];
					$mhs->nik = $input['nik'];
					$mhs->tmp_lahir = $input['tmp_lahir'];
					$mhs->tgl_lahir = $input['tgl_lahir'];
					$mhs->email = $input['email'];
					$mhs->foto = $input['foto'];
					$mhs->jenis_kelamin = $input['jenis_kelamin'];
					$mhs->save();
					
					foreach($input['matkul'] as $matkul_id){
						$mhs_matkul = new Mhs_matkul_orm();
						$mhs_matkul->mahasiswa_id = $mhs->id_mahasiswa;
						$mhs_matkul->matkul_id = $matkul_id;
						$mhs_matkul->save();
					}
					
					// MENDAFTARKAN SBG USER
					$nama       = explode(' ', $mhs->nama, 2);
					$first_name = $nama[0];
					$last_name  = end($nama);
					$full_name  = $mhs->nama;
					
					$username        = $mhs->nim;
					$password        = date("dmY", strtotime($mhs->tgl_lahir));
					$email           = $mhs->email;
					$tgl_lahir        = date("dmY", strtotime($mhs->tgl_lahir));
					$additional_data = [
						'first_name' => $first_name,
						'last_name'  => $last_name,
						'full_name'  => $full_name,
						'tgl_lahir'  => $tgl_lahir,
					];
					$group           = [ MHS_GROUP_ID ]; // Sets user to mhs.
					$this->ion_auth->register($username, $password, $email, $additional_data, $group);
					commit_db_trx();
					$action = true;
				
				} catch(\Illuminate\Database\QueryException $e){
					rollback_db_trx();
					$action = false;
			    }
				
			} else if ($method === 'edit') {
				
				$id_mahasiswa = $this->input->post('id_mahasiswa', true);
				$matkul = $this->input->post('matkul[]', true);
				
//				$action = $this->master->update('mahasiswa', $input, 'id_mahasiswa', $id_mahasiswa);
				
				$mhs = Mhs_orm::findOrFail($id_mahasiswa);
				
				try {
					begin_db_trx();
				
					$mhs->nama = $input['nama'];
					$mhs->nik = $input['nik'];
					$mhs->tmp_lahir = $input['tmp_lahir'];
					$mhs->tgl_lahir = $input['tgl_lahir'];
					$mhs->email = $input['email'];
					$mhs->foto = $input['foto'];
					$mhs->jenis_kelamin = $input['jenis_kelamin'];
					$mhs->save();
					
					Mhs_matkul_orm::where('mahasiswa_id',$id_mahasiswa)->delete(); // LOGIKA NYA DI DELETE DULU BARU DI INSERT
					foreach($matkul as $matkul_id){
						$mhs_matkul = new Mhs_matkul_orm();
						$mhs_matkul->mahasiswa_id = $mhs->id_mahasiswa;
						$mhs_matkul->matkul_id = $matkul_id;
						$mhs_matkul->save();
					}
					
	                $user = Users_orm::where('username',$mhs->nim)->first();
					
					if(null != $user) {
						$nama       = explode(' ', $mhs->nama, 2);
						$first_name = $nama[0];
						$last_name  = end($nama);
						
						$user->first_name = $first_name;
						$user->tgl_lahir  = date("dmY", strtotime($mhs->tgl_lahir));
						$user->last_name  = $last_name;
						$user->full_name  = $mhs->nama;
						$user->email      = $mhs->email;
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
//				$mhs = Mhs_orm::findOrFail($c);
//				$user = Users_orm::where('username',$mhs->nim)->first();
//				if($user != null){
//					show_error('User masih digunakan',500,'Perhatian');
//				}
//			}
//			if($allow) {
//				if ($this->master->delete('mahasiswa', $chk, 'id_mahasiswa')) {
//					$this->_json([
//						'status' => TRUE,
//						'total'  => count($chk)
//					]);
//				}
//			}

			try {
			    DB::beginTransaction();
			    foreach($chk as $c) {
				    $mhs = Mhs_orm::findOrFail($c);
				    Users_orm::where('username', $mhs->nim)->delete();
				    $mhs->delete();
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
		$data = $this->master->getMahasiswaById($id);
		$nama = explode(' ',$data->nama,2);
		$first_name = $nama[0];
		$last_name = end($nama);
		$full_name = $data->nama;

		$username = $data->nim;
		$password = $data->nim;
		$email = $data->email;
		$additional_data = [
			'first_name'	=> $first_name,
			'last_name'		=> $last_name,
			'full_name'     => $full_name,
		];
		$group = [ MHS_GROUP_ID ]; // Sets user to mhs.

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
				'msg'	 => 'User berhasil dibuat. NIM digunakan sebagai password pada saat login.'
			];
		}
		$this->_json($data);
	}

	public function import($import_data = null)
	{
		$data = [
			'user' => $this->ion_auth->user()->row(),
			'judul'	=> 'Peserta Ujian',
			'subjudul' => 'Import Peserta Ujian',
			'matkul_list' => Matkul_orm::All()
		];
		if ($import_data != null) $data['import'] = $import_data;

//		$this->load->view('_templates/dashboard/_header', $data);
//		$this->load->view('master/mahasiswa/import');
//		$this->load->view('_templates/dashboard/_footer');
		view('mahasiswa/import',$data);
	}
	
	public function preview()
	{
		$config['upload_path']		= './uploads/import/';
		$config['allowed_types']	= 'xls|xlsx|csv';
		$config['max_size']			= 5120;
		$config['encrypt_name']		= true;

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('upload_file')) {
			$error = $this->upload->display_errors();
			show_error('Upload error : ' . $error, 500, 'Perhatian');
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
					show_error('Upload error : file tidak didukung', 500, 'Perhatian');
			}

			$spreadsheet = $reader->load($file);
			$sheetData = $spreadsheet->getActiveSheet()->toArray();
			$data = [];
			for ($i = 1; $i < count($sheetData); $i++) {
				
				if(count($sheetData[$i]) < 9){
					unlink($file);
					show_error('Isian file tidak sesuai',500,'Perhatian');
				}
				
				$nim = strval($sheetData[$i][0]);
				if(strlen($nim) != MHS_ID_LENGTH || !ctype_digit($nim)) {
					$nim = '!! ERROR !!';
				}
				if(Mhs_orm::where('nim',$nim)->first() != null){
					$nim = '!! ERROR !!';
				}
				
				$nama = $sheetData[$i][1];
				if(strlen($nama) > 250 || strlen($nama) < 3){
					$nama = '!! ERROR !!';
				}
				
				$nik = strval($sheetData[$i][2]);
				$nik = str_replace("'" ,"", $nik);
				if(strlen($nik) != NIK_LENGTH || !ctype_digit($nik)) {
					$nik = '!! ERROR !!';
				}
				if(Mhs_orm::where('nik',$nik)->first() != null){
					$nik = '!! ERROR !!';
				}
				
				$tmp_lahir = $sheetData[$i][3];
				if(strlen($tmp_lahir) > 250 || strlen($tmp_lahir) < 3){
					$tmp_lahir = '!! ERROR !!';
				}
				
				$tgl_lahir = $sheetData[$i][4];
				if(strlen($tgl_lahir) != 10){
					$tgl_lahir = '!! ERROR !!';
				}else{
					$day = (int) substr($tgl_lahir, 8, 2);
				    $month = (int) substr($tgl_lahir, 5, 2);
				    $year = (int) substr($tgl_lahir, 0, 4);
				   if(!checkdate($month, $day, $year)){
				   	    $tgl_lahir = '!! ERROR !!';
				   }
				}
				
				$email = $sheetData[$i][5] ;
				if(filter_var($email, FILTER_VALIDATE_EMAIL)){
					if(Mhs_orm::where('email',$email)->first() != null){
						$email = '!! ERROR !!';
					}
					if(strlen($email) > 250){
						$email = '!! ERROR !!';
					}
				}else{
					$email = '!! ERROR !!';
				}
				
				$foto = $sheetData[$i][6];
				if(!empty($foto)) {
					if ($size = @getimagesize($foto)){
						if (strtolower(substr($size['mime'], 0, 5)) != 'image') {
							$foto = '!! ERROR !!';
						}
					}else{
						$foto = '!! ERROR !!';
					}
				}
				
				$jk = $sheetData[$i][7];
				if(!in_array($jk,['L','P'])){
					$jk = '!! ERROR !!';
				}
				
				$matkul = [];
				$sd = explode(',',$sheetData[$i][8]);
				if(!empty($sd)){
					foreach($sd as $s){
						$m = Matkul_orm::find($s);
						if($m == null){
							$m = '!! ERROR !!';
						}
						$matkul[] = $m;
					}
				}else{
					$matkul[] = '!! ERROR !!';
				}
				
				$data[] = [
					'nim' => $nim,
					'nama' => $nama,
					'nik' => $nik,
					'tmp_lahir' => $tmp_lahir,
					'tgl_lahir' => $tgl_lahir,
					'email' => $email,
					'foto' => $foto,
					'jenis_kelamin' => $jk,
					'matkul' => $matkul
				];
			}

			unlink($file);

			$this->import($data);
		}
	}

	public function do_import()
	{
		$input = json_decode($this->input->post('data', true));
//		$data = [];
		begin_db_trx();
		$allow = true;
		$msg = null;
		foreach ($input as $d) {
			$nim = strval($d->nim);
			if(strlen($nim) != MHS_ID_LENGTH || !ctype_digit($nim)) {
				$allow = false;
				$msg = 'NIM salah, nim : '. $nim ;
				break;
			}
			if(Mhs_orm::where('nim',$nim)->first() != null){
				$allow = false;
				$msg = 'NIM sudah terdaftar, nim : '. $nim ;
				break;
			}
			
			$nama = $d->nama;
			if(strlen($nama) > 250 || strlen($nama) < 3){
				$allow = false;
				$msg = 'Nama salah, nama : '. $nama ;
				break;
			}
			
			$nik = strval($d->nik);
			if(strlen($nik) != NIK_LENGTH || !ctype_digit($nik)) {
				$allow = false;
				$msg = 'NIK salah, nik : '. $nik ;
				break;
			}
			if(Mhs_orm::where('nik',$nik)->first() != null){
				$allow = false;
				$msg = 'NIK sudah terdaftar, nik : '. $nik ;
				break;
			}
			
			$tmp_lahir = $d->tmp_lahir;
			if(strlen($tmp_lahir) > 250 || strlen($tmp_lahir) < 3){
				$allow = false;
				$msg = 'Tmp lahir salah, tmp_lahir : '. $tmp_lahir ;
				break;
			}
			
			$tgl_lahir = $d->tgl_lahir;
			if(strlen($tgl_lahir) != 10){
				$allow = false;
				$msg = 'Tgl lahir salah, tgl_lahir : '. $tgl_lahir ;
				break;
			}else{
				$day = (int) substr($tgl_lahir, 8, 2);
			    $month = (int) substr($tgl_lahir, 5, 2);
			    $year = (int) substr($tgl_lahir, 0, 4);
			   if(!checkdate($month, $day, $year)){
			        $allow = false;
					$msg = 'Tgl lahir salah, tgl_lahir : '. $tgl_lahir ;
					break;
			   }
			}
			
			$email = $d->email;
			if(filter_var($email, FILTER_VALIDATE_EMAIL)){
				if(Mhs_orm::where('email',$email)->first() != null){
					$allow = false;
					$msg = 'Email sudah terdaftar, email : '. $email ;
					break;
				}
				if(strlen($email) > 250){
					$allow = false;
					$msg = 'Email bermasalah, email : '. $email ;
					break;
				}
			}else{
				$allow = false;
				$msg = 'Email bermasalah, email : '. $email ;
				break;
			}
			
			$foto = $d->foto;
			if(!empty($foto)) {
				if ($size = @getimagesize($foto)){
					if (strtolower(substr($size['mime'], 0, 5)) != 'image') {
						$allow = false;
						$msg = 'Foto bermasalah, foto : '. $foto ;
						break;
					}
				}else{
					$allow = false;
					$msg = 'Foto bermasalah, foto : '. $foto ;
					break;
				}
			}
			
			$jk = $d->jenis_kelamin;
			if(!in_array($jk,['L','P'])){
				$allow = false;
				$msg = 'Jenis kelamin bermasalah, jenis kelamin : '. $jk ;
				break;
			}
			
			$matkul_list = [];
			$sd = $d->matkul;
			if(!empty($sd)){
				foreach($sd as $s){
					$m = Matkul_orm::find($s->id_matkul);
					if($m == null){
						$allow = false;
						$msg = 'Materi ujian bermasalah, ID : '. $s->id_matkul ;
						break;
					}
					$matkul_list[] = $m;
				}
				if(!$allow){
					break;
				}
			}else{
				$allow = false;
				$msg = 'Materi ujian bermasalah, ID : TIDAK BOLEH KOSONG' ;
				break;
			}
			
			$mhs                = new Mhs_orm();
			$mhs->nim           = $nim;
			$mhs->nama          = $nama;
			$mhs->nik           = $nik;
			$mhs->tmp_lahir          = $tmp_lahir;
			$mhs->tgl_lahir          = $tgl_lahir;
			$mhs->email         = $email;
			$mhs->foto         = $foto;
			$mhs->jenis_kelamin = $jk;
			$mhs->save();
			
			foreach ($matkul_list as $matkul) {
				$mhs_matkul               = new Mhs_matkul_orm();
				$mhs_matkul->mahasiswa_id = $mhs->id_mahasiswa;
				$mhs_matkul->matkul_id    = $matkul->id_matkul;
				$mhs_matkul->save();
			}
			
			// MENDAFTARKAN SBG USER
			$nama       = explode(' ', $mhs->nama, 2);
			$first_name = $nama[0];
			$last_name  = end($nama);
			$full_name  = $mhs->nama;
			
			$username        = $mhs->nim;
			$password        = date("dmY", strtotime($mhs->tgl_lahir));
			$email           = $mhs->email;
			$tgl_lahir        = date("dmY", strtotime($mhs->tgl_lahir));
			$additional_data = [
				'first_name' => $first_name,
				'last_name'  => $last_name,
				'full_name'  => $full_name,
				'tgl_lahir'  => $tgl_lahir,
			];
			$group           = [ MHS_GROUP_ID ]; // Sets user to mhs.
			$this->ion_auth->register($username, $password, $email, $additional_data, $group);
		}
		
		if (!$allow){
			rollback_db_trx();
			show_error($msg,500,'Perhatian');
		}
		else{
			commit_db_trx();
			$message_rootpage = [
				'header' => 'Perhatian',
				'content' => 'Data berhasil di impor.',
				'type' => 'success'
			];
			$this->session->set_flashdata('message_rootpage', $message_rootpage);
			redirect('mahasiswa/import');
		}
		
	}
	
	public function edit_on_table(){
		$data = [
			'judul'	=> 'Peserta Ujian',
			'subjudul' => 'Impor Peserta Ujian',
		];
		view('mahasiswa/edit_on_table',$data);
	}
	
	protected function _table_import(){
		$input = json_decode($this->input->post('data', true));
		
//		$data = [];
		begin_db_trx();
		$allow = true;
		$msg = null;
		$i = 1;
		foreach ($input as $d) {
			$nim = strval($d[0]);
			if(strlen($nim) != MHS_ID_LENGTH || !ctype_digit($nim)) {
				$allow = false;
				$msg = 'Row : '. $i .', NIM salah, nim : ' . $nim  ;
				break;
			}
			if(Mhs_orm::where('nim',$nim)->first() != null){
				$allow = false;
				$msg = 'Row : '. $i .', NIM sudah terdaftar, nim : '. $nim ;
				break;
			}
			
			$nama = $d[1];
			if(strlen($nama) > 50 || strlen($nama) < 3){
				$allow = false;
				$msg = 'Row : '. $i .', Nama salah, nama : '. $nama ;
				break;
			}
			
			$nik = strval($d[2]);
			if(strlen($nik) != NIK_LENGTH || !ctype_digit($nik)) {
				$allow = false;
				$msg = 'Row : '. $i .', NIK salah, nik : ' . $nik  ;
				break;
			}
			if(Mhs_orm::where('nik',$nik)->first() != null){
				$allow = false;
				$msg = 'Row : '. $i .', NIK sudah terdaftar, nik : '. $nik ;
				break;
			}
			
			$tmp_lahir = $d[3];
			if(strlen($tmp_lahir) > 50 || strlen($tmp_lahir) < 3){
				$allow = false;
				$msg = 'Row : '. $i .', Tmp lahir salah, tmp lahir : '. $tmp_lahir ;
				break;
			}
			
			$tgl_lahir = $d[4];
			if(strlen($tgl_lahir) != 10){
				$allow = false;
				$msg = 'Row : '. $i .', Tgl lahir salah, tgl lahir : '. $tmp_lahir ;
				break;
			}else{
				$day = (int) substr($tgl_lahir, 8, 2);
			    $month = (int) substr($tgl_lahir, 5, 2);
			    $year = (int) substr($tgl_lahir, 0, 4);
			   if(!checkdate($month, $day, $year)){
			        $allow = false;
					$msg = 'Row : '. $i .', Tgl lahir salah, tgl lahir : '. $tmp_lahir ;
					break;
			   }
			}
			
			$email = $d[5];
			if(filter_var($email, FILTER_VALIDATE_EMAIL)){
				if(Mhs_orm::where('email',$email)->first() != null){
					$allow = false;
					$msg = 'Row : '. $i .', Email sudah terdaftar, email : '. $email ;
					break;
				}
				if(strlen($email) > 250){
					$allow = false;
					$msg = 'Row : '. $i .', Email bermasalah, email : '. $email ;
					break;
				}
			}else{
				$allow = false;
				$msg = 'Row : '. $i .', Email bermasalah, email : '. $email ;
				break;
			}
			
			$foto = $d[6];
			if(!empty($foto)) {
				if ($size = @getimagesize($foto)){
					if (strtolower(substr($size['mime'], 0, 5)) != 'image') {
						$allow = false;
						$msg = 'Row : '. $i .', Foto bermasalah, foto : '. $foto ;
						break;
					}
				}else{
					$allow = false;
					$msg = 'Row : '. $i .', Foto bermasalah, foto : '. $foto ;
					break;
				}
			}
			
			$jk = $d[7];
			if(!in_array($jk,['L','P'])){
				$allow = false;
				$msg = 'Row : '. $i .', Jenis kelamin bermasalah, jenis kelamin : '. $jk ;
				break;
			}
			
			$matkul_list = [];
			$sd = explode(',',$d[8]);
			if(!empty($sd)){
				foreach($sd as $s){
					$m = Matkul_orm::find($s);
					if($m == null){
						$allow = false;
						$msg = 'Row : '. $i .', Materi ujian bermasalah, ID : '. $s ;
						break;
					}
					$matkul_list[] = $m;
				}
				if(!$allow){
					break;
				}
			}else{
				$allow = false;
				$msg = 'Row : '. $i .', Materi ujian bermasalah, ID : TIDAK BOLEH KOSONG' ;
				break;
			}
			
			$mhs                = new Mhs_orm();
			$mhs->nim           = $nim;
			$mhs->nama          = $nama;
			$mhs->nik          = $nik;
			$mhs->tmp_lahir     = $tmp_lahir;
			$mhs->tgl_lahir     = $tgl_lahir;
			$mhs->email         = $email;
			$mhs->foto         = $foto;
			$mhs->jenis_kelamin = $jk;
			$mhs->save();
			
			foreach ($matkul_list as $matkul) {
				$mhs_matkul               = new Mhs_matkul_orm();
				$mhs_matkul->mahasiswa_id = $mhs->id_mahasiswa;
				$mhs_matkul->matkul_id    = $matkul->id_matkul;
				$mhs_matkul->save();
			}
			
			// MENDAFTARKAN SBG USER
			$nama       = explode(' ', $mhs->nama, 2);
			$first_name = $nama[0];
			$last_name  = end($nama);
			$full_name  = $mhs->nama;
			
			$username        = $mhs->nim;
			$password        = date("dmY", strtotime($mhs->tgl_lahir));
			$email           = $mhs->email;
			$tgl_lahir        = date("dmY", strtotime($mhs->tgl_lahir));
			$additional_data = [
				'first_name' => $first_name,
				'last_name'  => $last_name,
				'full_name'  => $full_name,
				'tgl_lahir'  => $tgl_lahir,
			];
			$group           = [ MHS_GROUP_ID ]; // Sets user to mhs.
			$this->ion_auth->register($username, $password, $email, $additional_data, $group);
			$i++;
		}
		
		if (!$allow){
			rollback_db_trx();
	        $this->_json(['status' => false, 'msg' => $msg]);
		}
		else{
			commit_db_trx();
	        $this->_json(['status' => true]);
		}
		
	}
}
