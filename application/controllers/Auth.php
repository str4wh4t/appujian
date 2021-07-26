<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Orm\Data_daerah_orm;
use Orm\Matkul_orm;
use Orm\Mhs_orm;
use Orm\Users_orm;
use Orm\Membership_orm;
use Orm\Membership_history_orm;
use Orm\Paket_history_orm;
use Orm\Mhs_matkul_orm;
use Orm\Paket_orm;
use Orm\Tahun;
use Orm\Mhs_ujian_orm;
use Orm\Users_temp_orm;
use Carbon\Carbon;
use GuzzleHttp\Client;

class Auth extends MY_Controller
{
	public $data = [];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library('form_validation');
		$this->load->helper(['url', 'language']);
		$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
		$this->lang->load('auth');
	}

	private function _output_json($data)
	{
		$this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	public function index()
	{
		if ($this->ion_auth->logged_in()){
//			$user_id = $this->ion_auth->user()->row()->id; // Get User ID
//			$group = $this->ion_auth->get_users_groups($user_id)->row()->name; // Get user group
			redirect('dashboard');
		}
		$this->data['identity'] = [
			'name' => 'identity',
			'id' => 'identity',
			'type' => 'text',
			'placeholder' => 'Username',
			'autofocus'	=> 'autofocus',
			'class' => 'form-control',
			'autocomplete'=>'off'
		];
		$this->data['password'] = [
			'name' => 'password',
			'id' => 'password',
			'type' => 'password',
			'placeholder' => 'Password',
			'class' => 'form-control',
		];
		$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

//		$this->load->view('_templates/auth/_header.php');
//		$this->load->view('auth/login', $this->data);
//		$this->load->view('_templates/auth/_footer.php');
		
		view('auth/login',$this->data);
	}

	// public function cek_login_orginal()
	// {
	// 	$this->form_validation->set_rules('identity', str_replace(':', '', $this->lang->line('login_identity_label')), 'required|trim');
	// 	$this->form_validation->set_rules('password', str_replace(':', '', $this->lang->line('login_password_label')), 'required|trim');

	// 	if ($this->form_validation->run() === true)	{
	// 		$remember = (bool)$this->input->post('remember');
	// 		if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember)){
	// 			$this->cek_akses();
	// 		}else {
	// 			$data = [
	// 				'status' => false,
	// 				'failed' => 'Incorrect Login',
	// 			];
	// 			$this->_output_json($data);
	// 		}
	// 	}else{
	// 		$invalid = [
	// 			'identity' => form_error('identity'),
	// 			'password' => form_error('password')
	// 		];
	// 		$data = [
	// 			'status' 	=> false,
	// 			'invalid' 	=> $invalid
	// 		];
	// 		$this->_output_json($data);
	// 	}
	// }
	
	public function cek_login()
	{
		if(!$this->input->post()){
			show_404();
		}
		$this->form_validation->set_rules('identity', str_replace(':', '', $this->lang->line('login_identity_label')), 'required|trim');
		$this->form_validation->set_rules('password', str_replace(':', '', $this->lang->line('login_password_label')), 'required|trim');

		if ($this->form_validation->run() === true)	{
			$remember = (bool)$this->input->post('remember');
			$identity = $this->input->post('identity', true);
			$password = $this->input->post('password', true);
			if ($this->ion_auth->login($identity, $password, $remember)){
				$user = $this->ion_auth->user()->row();
				$login_as = $this->ion_auth->get_users_groups($user->id)->result()[0];

				if(APP_UDID){
					if ($login_as->name == 'mahasiswa'){
						// JIKA LOGIN SBG MHS atau APP_UDID = true 
						redirect('/logout', 'refresh');
					}
				}

//				if(!$user->is_online){
					$session_data = [
	                        'username'          => $user->username,
	                        'nama_lengkap'      => $user->full_name,
	                        'user'              => $user,
	                        'login_at'          => date('Y-m-d H:i:s'),
	                        'login_as'          => $login_as,
	                    ];
					
					$this->session->set_userdata('session_data',$session_data);
					// $message_rootpage = [
					// 	'header' => 'Welcome',
					// 	'content' => 'Login berhasil.',
					// 	'type' => 'success'
					// ];
					// $this->session->set_flashdata('message_rootpage', $message_rootpage);
					redirect('/dashboard', 'refresh');
//				}else{
//					redirect('not_valid_login', 'refresh');
//				}
			}else {

				if ($this->ion_auth->is_max_login_attempts_exceeded($identity)){
					$this->session->set_flashdata('error_login_msg', 'Login anda terkunci, silahkan hub pengawas.');
				}else{
					$this->session->set_flashdata('error_login_msg', 'Login salah / login akun anda tidak aktif.');
				}

				redirect('/', 'refresh');
			}
		}else{
			$this->session->set_flashdata('error_login_msg', 'Oops, isian anda salah.');
			redirect('/', 'refresh');
		}


	}

	public function cek_login_udid()
	{
		$token = null;
		$this->load->library('verification_jwt');
		$this->load->library('sso_udid_adapter', [
			'property' => [
				'client_id'         => APP_UDID_ID,
				'client_secret'     => APP_UDID_SECRET,
				'redirect_uri'      => url('auth/cek_login_udid'),
				'scope'             => 'User.Read User.Payment.Read User.Payment.Write',
				'authorization_url' => 'https://login.undip.id/oauth2/authorize/login',
				'access_token_url'  => 'https://login.undip.id/oauth2/authorize/access_token',
			],
			'settings' => [
		//		'verify' => false, /*default*/
			],
			'debug' => [
				'exception' => true, /*debug all exception*/
				'token'     => false, /*debug token request*/
				'response'  => false, /*debug all response non token*/
			],
		]);

		try{
			$token = $this->sso_udid_adapter->auth();
			$this->session->set_userdata('token_udid', $token);

		}catch (Exception $e) {
			$this->session->set_flashdata('error_login_msg', '1. '. $e->getMessage());
			redirect('/', 'refresh');
		}

		try {
			$result = $this->sso_udid_adapter->request([
				'method'      => 'POST',
				'endpoint'    => APP_UDID_API . '/api-user/api/get_akun',
				'body_params' => [
		//					'body' => json_encode([
		//						'token' => $token,
		//					]),
		//					'debug' => true,
				],
				'header_params' => [
		//			'Content-Type' => 'application/json',
					'Accept'       => 'application/json',
				]
			],$token);
			

			$notif = json_decode($result);

		}catch (Exception $e) {
			$this->session->set_flashdata('error_login_msg', '2. '. $e->getMessage());
			redirect('/', 'refresh');
		}

		$data = $notif->payload;

		// vdebug($data);

		// $sso_udid_id = '123455' ; // $this->input->post('sso_udid_id');
		$user = Users_orm::where('sso_udid_id', $data->cuid)->first();

		if (empty($user)) {
			// DIDAFTARKAN USERNYA 
			$users_temp = new Users_temp_orm();
			try {
				begin_db_trx();
				$users_temp->full_name = $data->nama;
				// $users_temp->nik = $this->input->post('nik');
				$users_temp->email = $data->email;
				$users_temp->phone = $data->mobile_phone;
				// $users_temp->jenis_kelamin = $this->input->post('jenis_kelamin');
				$users_temp->kota_asal = 'Kota Semarang';
				$users_temp->tmp_lahir = 'Kota Semarang';
				$users_temp->tgl_lahir = $data->tgl_lhr;
				$users_temp->password = '123456';
				$users_temp->sso_udid_id = $data->cuid;
				$users_temp->save();
				commit_db_trx();

				// $this->session->set_flashdata('success_registrasi_msg', 'Pendaftaran berhasil, silahkan cek email untuk aktivasi');
				// redirect('auth/registrasi', 'refresh');
				
				
			} catch (Exception $e) {
				rollback_db_trx();
				$this->session->set_flashdata('error_login_msg', '3. '. $e->getMessage());
				redirect('/', 'refresh');
				
			}
			$this->cron_auto_registrasi($users_temp);
			$user = Users_orm::where('sso_udid_id', $data->cuid)->first();

		}

		$this->_setup_user_login($user);
		
	}

	public function login_as($id_user){

		$this->_akses_admin_dan_koord_pengawas();

		$user = Users_orm::findOrFail($id_user);
		
		$this->_setup_user_login($user);
		
	}

	private function _setup_user_login($user){

		$this->ion_auth->set_session($user);
		$login_as = $this->ion_auth->get_users_groups($user->id)->result()[0];

		$session_data = [
				'username'          => $user->username,
				'nama_lengkap'      => $user->full_name,
				'user'              => $user,
				'login_at'          => date('Y-m-d H:i:s'),
				'login_as'          => $login_as,
			];
		
		$this->session->set_userdata('session_data',$session_data);
		// $message_rootpage = [
		// 	'header' => 'Welcome',
		// 	'content' => 'Login berhasil.',
		// 	'type' => 'success'
		// ];
		// $this->session->set_flashdata('message_rootpage', $message_rootpage);
		redirect('/dashboard', 'refresh');

	}


	public function logout()
	{
		$this->ion_auth->logout();
		session_destroy();
		redirect('login','refresh');
	}
	
	public function not_valid_login(){
		$this->ion_auth->logout();
		session_destroy();
		redirect('auth/logout_out_from_not_valid_login', 'refresh');
	}
	
	public function logout_out_from_not_valid_login(){
		$this->load->library('user_agent');
		$this->session->set_flashdata('error_login_msg', 'Username tsb sedang login di tempat lain.');
		redirect('/', 'refresh');
	}


	/**
	 * Activate the user
	 *
	 * @param int         $id   The user ID
	 * @param string|bool $code The activation code
	 */
	public function activate($id, $code = false)
	{
		$activation = false;

		if ($code !== false)
		{
			$activation = $this->ion_auth->activate($id, $code);
		}
		else if ($this->ion_auth->is_admin())
		{
			$activation = $this->ion_auth->activate($id);
		}

		if ($activation)
		{
			// redirect them to the auth page
			// $this->session->set_flashdata('message', $this->ion_auth->messages());

			$this->session->set_flashdata('success_activation_msg', 'Aktifasi berhasil, silahkan login');
			redirect("auth", 'refresh');
		}
		else
		{
			// redirect them to the forgot password page
			// $this->session->set_flashdata('message', $this->ion_auth->errors());
			// redirect("auth/forgot_password", 'refresh');

			$this->session->set_flashdata('error_activation_msg', 'Aktifasi gagal, silahkan hubungi admin');
			redirect("auth", 'refresh');
		}
	}
	
	public function expired_page(){
		// INI DIPANGGIL OLEH core/MY_Security.php dan config/routes.php
		show_error('Maaf, terjadi kesalahan silahkan kembali',500,'Perhatian');
	}
	
	public function notfound_page(){
		// INI DIPANGGIL OLEH config/routes.php
		show_error('Halaman yang anda cari tidak ditemukan',404,'Perhatian');
	}
	
	public function get_server_time(){

		$info = getdate();
		$date = $info['mday'];
		$month = $info['mon'];
		$year = $info['year'];
		$hour = $info['hours'];
		$min = $info['minutes'];
		$sec = $info['seconds'];

		// $current_date = "$date/$month/$year == $hour:$min:$sec";
		// $current_date = "$year-$month-$date $hour:$min:$sec";
		$current_date = "$year-$month-$date $hour:$min:$sec";

		// echo date('Y-m-d h:m:s') ;
		echo $current_date ;
		// return $current_date ;
	}

	public function registrasi(){

		if ($this->ion_auth->logged_in()){
			redirect('dashboard');
		}
					
		$data = [];
		$data['error_register_user'] = null;

		if($this->input->post('action')){
			$token = $this->input->post('token');
			$action = $this->input->post('action');
			$recaptcha = new \ReCaptcha\ReCaptcha(RECAPTCHA_V3_SECRET_KEY);
			$resp = $recaptcha->setExpectedAction($action)
                  ->setScoreThreshold(0.5)
                  ->verify($token, $_SERVER['REMOTE_ADDR']);

			// vdebug($resp->isSuccess());

			if ($resp->isSuccess()) {
				// Verified!
				$this->form_validation->set_rules('full_name', 'Email', 'required|trim|min_length[3]|max_length[250]');
				// $this->form_validation->set_rules('nik', 'Nik', 'exact_length[' . NIK_LENGTH . ']|is_unique[mahasiswa.nik]');
				$this->form_validation->set_rules('email', 'Email', 'required|trim|max_length[250]|valid_email|is_unique[users.email]');
				$this->form_validation->set_rules('telp', 'Telp', 'required|max_length[20]');
				// $this->form_validation->set_rules('jenis_kelamin', 'Jenis Kelamin', 'required|in_list[L,P]');
				$this->form_validation->set_rules('kota_asal', 'Kota Asal', 'required|trim|min_length[3]|max_length[250]');
				$this->form_validation->set_rules('tmp_lahir', 'Tmp Lahir', 'required|trim|min_length[3]|max_length[250]');
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
				$this->form_validation->set_rules('password', 'password', 'required|min_length['. PASSWORD_MIN_LENGTH .']|max_length['. PASSWORD_MAX_LENGTH .']');
				$this->form_validation->set_rules('password_confirm', 'password_confirm', 'required|matches[password]|min_length['. PASSWORD_MIN_LENGTH .']|max_length['. PASSWORD_MAX_LENGTH .']');

				$this->form_validation->set_error_delimiters('<li>', '</li>');
				$this->form_validation->set_message('required', 'Kolom {field} wajib diisi');
				$this->form_validation->set_message('is_unique', '{field} tsb sudah terdaftar');

				if ($this->form_validation->run() === false)
				{
					// $this->session->set_flashdata('error_registrasi_msg', $this->form_validation->error_string());
					// redirect('auth/registrasi', 'refresh');
				}else{

					try {
						begin_db_trx();
						$users_temp = new Users_temp_orm();
						$users_temp->full_name = $this->input->post('full_name');
						// $users_temp->nik = $this->input->post('nik');
						$users_temp->email = $this->input->post('email');
						$users_temp->phone = $this->input->post('telp');
						// $users_temp->jenis_kelamin = $this->input->post('jenis_kelamin');
						$users_temp->kota_asal = $this->input->post('kota_asal');
						$users_temp->tmp_lahir = $this->input->post('tmp_lahir');
						$users_temp->tgl_lahir = $this->input->post('tgl_lahir');
						$users_temp->password = $this->input->post('password');
						$users_temp->save();
						commit_db_trx();

						$this->session->set_flashdata('success_registrasi_msg', 'Pendaftaran berhasil, silahkan cek email untuk aktivasi');
						redirect('auth/registrasi', 'refresh');

					} catch (Exception $e) {
						rollback_db_trx();
						$data['error_register_user'] = $e->getMessage(); 
					}
				
				}

			}

		}

		$data['kota_kab_list'] = Data_daerah_orm::all();

		view('auth/registrasi', $data);

	}

	public function cron_auto_registrasi($users_temp_sso = null){

		/** JIKA $users_temp_list BUKAN null BERATI USER DI DAFTAR KAN LANGSUNG OLEH SSO */

		if(empty($users_temp_sso)){
			if(!is_cli()) show_404();
	
			$users_temp_list = Users_temp_orm::where('is_processed', 0)->orderBy('created_at')->get();

		}else{
			$users_temp_list = collect();
			$users_temp_list->push($users_temp_sso);
		}
		
		if($users_temp_list->isNotEmpty()) {
			$cron_end = date("Y-m-d H:i:s", strtotime("+1 minutes"));

			foreach ($users_temp_list as $users_temp) {

				$today = date('Y-m-d H:i:s');

				if(empty($users_temp_sso)){
					if($today > $cron_end){
						die('Waktu cron habis');
					}
				}

				if(empty($users_temp_sso)){
					echo date('Y-m-d H:i:s') ." => Nama : ". strtoupper($users_temp->full_name) ." => ";
				}

				// MENDAFTARKAN SBG USER
				$nama       = explode(' ', $users_temp->full_name, 2);
				$first_name = $nama[0];
				$last_name  = end($nama);
				$full_name  = $users_temp->full_name;
		
				$username        = date('ymdHis'); // USERNAME DIGENERATE OTOMATIS
				$password        = $users_temp->password;
				$email           = $users_temp->email;
				$email = strtolower($email); // KARAKTER DIKECILKAN
		
				$additional_data = [
					'first_name' => $first_name,
					'last_name'  => $last_name,
					'full_name'  => $full_name,
					'phone'		 => $users_temp->phone,
					'no_billkey' => $username, // BILLKEY DISAMAKAN DENGN USERNAME
					'sso_udid_id' => isset($users_temp->sso_udid_id) ? $users_temp->sso_udid_id : null,
				];
				$group           = [MHS_GROUP_ID]; // Sets user to mhs.
		
				$return_id_user = null ;
		
				try {
		
					$return_id_user = $this->ion_auth->register($username, $password, $email, $additional_data, $group);
		
					if($return_id_user == false){
						throw new Exception('Pendaftaran gagal, silahkan ulangi.');
					}
		
					begin_db_trx();
		
					$mhs = new Mhs_orm;
					$id_mahasiswa = $return_id_user;
		
					while(strlen($id_mahasiswa) < JML_DIGIT_ID_MHS){
						if(strlen($id_mahasiswa) == 9){
							$id_mahasiswa = PREFIX_ID_MHS . $id_mahasiswa ;
						}else{
							$id_mahasiswa = '0' . $id_mahasiswa ;
						}
					}
		
					$mhs->id_mahasiswa = $id_mahasiswa;
					$mhs->nim = $username;
					$mhs->nama = $users_temp->full_name;
					// $mhs->nik = $users_temp->nik;
					$mhs->tmp_lahir = $users_temp->tmp_lahir;
					$mhs->tgl_lahir = $users_temp->tgl_lahir;
					$mhs->email = $email;
					$mhs->prodi = PRODI_TXT_DEFAULT;
					$mhs->kodeps = PRODI_KODE_DEFAULT;
					$mhs->no_billkey = $additional_data['no_billkey'];
					// $mhs->jenis_kelamin = $users_temp->jenis_kelamin;
					$mhs->kota_asal = $users_temp->kota_asal;
					$mhs->tahun = Tahun::get_tahun_aktif();
					$mhs->save();
		
					$membership = Membership_orm::findOrFail(MEMBERSHIP_ID_DEFAULT);
					
					$membership_expiration_date = date('Y-m-d', strtotime("+". $membership->durasi ." months", strtotime(date('Y-m-d'))));
		
					$membership_history = new Membership_history_orm();
					$membership_history->mahasiswa_id = $id_mahasiswa;
					$membership_history->membership_id = $membership->id ;
					$membership_history->upgrade_ke = 0 ;
					// $membership_history->sisa_kuota_latihan_soal = $membership_sisa_kuota_latihan_soal ;
					$membership_history->expired_at = $membership_expiration_date;
					$membership_history->stts =  MEMBERSHIP_STTS_AKTIF ;
					$membership_history->save();

					$paket_bonus_membership = get_paket_bonus_membership($membership);

					$now = Carbon::now()->toDateTimeString();
		
					if (!empty($paket_bonus_membership)) {
						$matkul_ids_exist = [];
						$ujian_ids_exist = [];
						foreach ($paket_bonus_membership as $paket) {
		
							$paket_history = new Paket_history_orm();
							$paket_history->mahasiswa_id = $id_mahasiswa;
							$paket_history->paket_id = $paket->id ;
							$paket_history->upgrade_ke = 0 ;
							$paket_history->stts =  PAKET_STTS_AKTIF ;
							$paket_history->save();
							
							/** 
							 * SEBELUMNYA PAKET MENGACU KE MATERI UJIAN
							 * SEKARANG PAKET MENGACU KE UJIAN
							 */
							// foreach($paket->matkul as $matkul){
							// 	$mhs_matkul_orm = new Mhs_matkul_orm();
							// 	$mhs_matkul_orm->mahasiswa_id = $id_mahasiswa;
							// 	$mhs_matkul_orm->matkul_id = $matkul->id_matkul;
							// 	$mhs_matkul_orm->sisa_kuota_latihan_soal = $paket->kuota_latihan_soal ;
							// 	$mhs_matkul_orm->save();
		
							// 	// [START] JIKA UJIAN SOURCE DARI MATERI
							// 	if($matkul->m_ujian->isNotEmpty()){
							// 		$ujian_ids = $matkul->m_ujian()->pluck('id_ujian')->toArray();
							// 		$insert = [];
							// 		foreach($ujian_ids as $ujian_id){
							// 			$insert[] = [
							// 				'mahasiswa_id' => $id_mahasiswa,
							// 				'ujian_id' => $ujian_id,
							// 				'created_at' => $now,
							// 			];
							// 		}
							// 		Mhs_ujian_orm::insert($insert);
							// 	}
							// 	// [END] JIKA UJIAN SOURCE DARI MATERI

							// 	// [START] JIKA UJIAN SOURCE DARI BUNDLE
							// 	if($matkul->m_ujian_enable->isNotEmpty()){
							// 		$mhs_ujian_ids = $matkul->m_ujian_enable()->pluck('ujian_id')->toArray();
							// 		if(!empty($mhs_ujian_ids)){
							// 			$insert = [];
							// 			foreach($mhs_ujian_ids as $m_ujian_id){
							// 				$insert[] = [
							// 					'mahasiswa_id' => $id_mahasiswa,
							// 					'ujian_id'	=> $m_ujian_id,
							// 					'created_at' => $now,
							// 				];
							// 			}
							// 			Mhs_ujian_orm::insert($insert);
							// 		}
							// 	}
							// 	// [END] JIKA UJIAN SOURCE DARI BUNDLE
							// }

							if($paket->m_ujian->isNotEmpty()){
								$ujian_ids = $paket->m_ujian()->pluck('id_ujian')->toArray();
								$insert = [];
								foreach($ujian_ids as $ujian_id){
									if(!in_array($ujian_id, $ujian_ids_exist)){
										$ujian_ids_exist[] = $ujian_id;
										$insert[] = [
											'mahasiswa_id' => $id_mahasiswa,
											'ujian_id' => $ujian_id,
											'sisa_kuota_latihan_soal' => $paket->kuota_latihan_soal,
											'created_at' => $now,
										];
									}else{
										// JIKA SUDAH ADA MAKA SISA KUOTA LATIHAN SOAL DITAMBAHKAN
										$mhs_ujian_exist = Mhs_ujian_orm::where([
											'mahasiswa_id' => $id_mahasiswa,
											'ujian_id' => $ujian_id,
										])->first();
										$kuota_latihan_soal_exist = $mhs_ujian_exist->sisa_kuota_latihan_soal;
										$mhs_ujian_exist->sisa_kuota_latihan_soal = $kuota_latihan_soal_exist + $paket->kuota_latihan_soal;
										$mhs_ujian_exist->save();
									}
								}
								if(!empty($insert))
									Mhs_ujian_orm::insert($insert);
								/** MHS_MATKUL SUDAH TIDAK DIPAKAI, 4/6/2021 */
								/*
								foreach($paket->m_ujian as $m_ujian){
									if(!empty($m_ujian->matkul)){
										// [START] JIKA UJIAN SOURCE DARI MATERI
										if(!in_array($m_ujian->matkul->id_matkul, $matkul_ids_exist)){
											$matkul_ids_exist[] = $m_ujian->matkul->id_matkul;
											$mhs_matkul_orm = new Mhs_matkul_orm();
											$mhs_matkul_orm->mahasiswa_id = $id_mahasiswa;
											$mhs_matkul_orm->matkul_id = $m_ujian->matkul->id_matkul;
											$mhs_matkul_orm->sisa_kuota_latihan_soal = 0 ;
											$mhs_matkul_orm->save();
										}
										// [END] JIKA UJIAN SOURCE DARI MATERI
									}
									if($m_ujian->matkul_enable->isNotEmpty()){
										// [START] JIKA UJIAN SOURCE DARI BUNDLE
										$insert = [];
										foreach($m_ujian->matkul_enable as $matkul){
											if(!in_array($matkul->id_matkul, $matkul_ids_exist)){
												$matkul_ids_exist[] = $matkul->id_matkul;
												$insert[] = [
													'mahasiswa_id' => $id_mahasiswa,
													'matkul_id' => $matkul->id_matkul,
													'sisa_kuota_latihan_soal' => 0,
													'created_at' => $now,
												];
											}
										}
										if(!empty($insert))
											Mhs_matkul_orm::insert($insert);
										// [END] JIKA UJIAN SOURCE DARI BUNDLE
									}
								}
								*/
							}

						}
					}


					$users_temp->is_processed = 1 ; //  FLAG SUDAH DIPROSES
					$users_temp->save();

					if(empty($users_temp_sso)){
						echo 'DONE' . "\n";
					}
					
					commit_db_trx();
		
				} catch (Exception $e) {
					rollback_db_trx();

					if(!empty($return_id_user)){
						$users_temp->is_processed = 2 ; //  FLAG GAGAL DIPROSES
						$this->ion_auth->delete_user($return_id_user); // UNREGISTER USER
						echo 'Terjadi kesalahan memproses user : ' . $e->getMessage() . "\n";
					}else{
						$users_temp->is_processed = 3 ; //  FLAG GAGAL DIPROSES
						echo 'Terjadi kesalahan menyimpan user : ' . $e->getMessage() . "\n";
					}

					$users_temp->save();

					continue;
				}
			}

		}

	}

	public function resend_password()
	{
		if($this->input->post('identity')){

			$this->form_validation->set_rules('identity', 'Email', 'required|valid_email');

			if ($this->form_validation->run() === false)
			{
				
				$this->session->set_flashdata('error_resend_password_msg', 'Oops, isian anda salah.');
				redirect('auth/resend_password', 'refresh');
			}

			$identity = $this->ion_auth->where('email', $this->input->post('identity'))->users()->row();

			if (empty($identity))
			{
				$this->session->set_flashdata('error_resend_password_msg', 'User dengan email tsb tidak ditemukan.');
				redirect('auth/resend_password', 'refresh');
			}

			// run the forgotten password method to email an activation code to the user
			$forgotten = $this->ion_auth->forgotten_password($identity->{$this->config->item('identity', 'ion_auth')});

			if ($forgotten)
			{
				// if there were no errors
				$this->session->set_flashdata('success_resend_password_msg', 'Password baru telah dikirim ke email anda');
				redirect('auth/resend_password', 'refresh');
			}
			else
			{
				$this->session->set_flashdata('error_resend_password_msg', 'Terjadi kesalahan saat memproses.');
				redirect('auth/resend_password', 'refresh');
			}
			
		}

		view('auth/resend_password');
	}

	public function set_password($code = NULL)
	{
		if (!$code)
		{
			show_404();
		}

		$user = $this->ion_auth->forgotten_password_check($code);

		if (!$user)
		{
			$this->session->set_flashdata('error_resend_password_msg', 'Reset password anda telah expired, silahkan ulangi');
			redirect("auth/resend_password", 'refresh');
		}

		if($this->input->post('new')){
			$this->form_validation->set_rules('new', 'Password', 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[new_confirm]');
			$this->form_validation->set_rules('new_confirm', 'Password Confirm', 'required');
	
			if ($this->form_validation->run() === false)
			{
				$this->session->set_flashdata('error_set_password_msg', 'Isian password anda salah');
				redirect("auth/set_password/" . $code, 'refresh');
			}

			$identity = $user->{$this->config->item('identity', 'ion_auth')};

			if ($user->id != $this->input->post('user_id'))
			{

				// something fishy might be up
				$this->session->set_flashdata('error_resend_password_msg', 'Kesalahan token, silahkan ulangi');
				redirect("auth/resend_password", 'refresh');
			}
		
			// finally change the password
			$change = $this->ion_auth->reset_password($identity, $this->input->post('new'));

			if ($change)
			{
				// if the password was successfully changed
				$this->session->set_flashdata('success_resend_password_msg', 'Password berhasil direset, silahkan login kembali');
				redirect("login", 'refresh');
			}
			else
			{
				$this->session->set_flashdata('error_resend_password_msg', 'Terjadi kesalahan saat reset password');
				redirect('auth/resend_password/', 'refresh');
			}
		}
		
		$data['code'] = $code;
		$data['user'] = $user;
		view('auth/set_password', $data);

	}
}
