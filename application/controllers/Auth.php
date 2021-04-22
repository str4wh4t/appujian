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
use Orm\Mhs_ujian_orm;
use Orm\Users_temp_orm;

class Auth extends CI_Controller
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

	// 	if ($this->form_validation->run() === TRUE)	{
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

		if ($this->form_validation->run() === TRUE)	{
			$remember = (bool)$this->input->post('remember');
			if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember)){
				$user = $this->ion_auth->user()->row();
//				if(!$user->is_online){
					$session_data = [
	                        'username'          => $user->username,
	                        'nama_lengkap'      => $user->full_name,
	                        'user'              => $user,
	                        'login_at'          => date('Y-m-d H:i:s'),
	                        'login_as'          => $this->ion_auth->get_users_groups($user->id)->result()[0],
	                    ];
					$this->session->set_userdata('session_data',$session_data);
					$message_rootpage = [
						'header' => 'Welcome',
						'content' => 'Login berhasil.',
						'type' => 'success'
					];
					$this->session->set_flashdata('message_rootpage', $message_rootpage);
					redirect('/dashboard', 'refresh');
//				}else{
//					redirect('not_valid_login', 'refresh');
//				}
			}else {
				$this->session->set_flashdata('error_login_msg', 'Login salah / login akun anda tidak aktif.');
				redirect('/', 'refresh');
			}
		}else{
			$this->session->set_flashdata('error_login_msg', 'Oops, isian anda salah.');
			redirect('/', 'refresh');
		}


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
	public function activate($id, $code = FALSE)
	{
		$activation = FALSE;

		if ($code !== FALSE)
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
				$this->form_validation->set_rules('nik', 'Nik', 'exact_length[' . NIK_LENGTH . ']|is_unique[mahasiswa.nik]');
				$this->form_validation->set_rules('email', 'Email', 'required|trim|max_length[250]|valid_email|is_unique[users.email]');
				$this->form_validation->set_rules('telp', 'Telp', 'required|max_length[20]');
				$this->form_validation->set_rules('jenis_kelamin', 'Jenis Kelamin', 'required|in_list[L,P]');
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

				if ($this->form_validation->run() === FALSE)
				{
					// $this->session->set_flashdata('error_registrasi_msg', $this->form_validation->error_string());
					// redirect('auth/registrasi', 'refresh');
				}else{

					try {

						begin_db_trx();

						$users_temp = new Users_temp_orm();
						$users_temp->full_name = $this->input->post('full_name');
						$users_temp->nik = $this->input->post('nik');
						$users_temp->email = $this->input->post('email');
						$users_temp->phone = $this->input->post('telp');
						$users_temp->jenis_kelamin = $this->input->post('jenis_kelamin');
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

	public function cron_auto_registrasi(){

		if(!is_cli()) show_404();

		$users_temp_list = Users_temp_orm::where('is_processed', 0)->orderBy('created_at')->get();
		
		if($users_temp_list->isNotEmpty()) {
			$cron_end = date("Y-m-d H:i:s", strtotime("+1 minutes"));

			foreach ($users_temp_list as $users_temp) {
				$today = date('Y-m-d H:i:s');
				if($today > $cron_end){
					die('Waktu cron habis');
				}

				echo 'Nama : '. strtoupper($users_temp->full_name) ." ===> ";

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
					$mhs->nik = $users_temp->nik;
					$mhs->tmp_lahir = $users_temp->tmp_lahir;
					$mhs->tgl_lahir = $users_temp->tgl_lahir;
					$mhs->email = $email;
					$mhs->no_billkey = $additional_data['no_billkey'];
					$mhs->jenis_kelamin = $users_temp->jenis_kelamin;
					$mhs->kota_asal = $users_temp->kota_asal;
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
		
					if (!empty($paket_bonus_membership)) {
						foreach ($paket_bonus_membership as $paket) {
		
							$paket_history = new Paket_history_orm();
							$paket_history->mahasiswa_id = $id_mahasiswa;
							$paket_history->paket_id = $paket->id ;
							$paket_history->upgrade_ke = 0 ;
							$paket_history->stts =  PAKET_STTS_AKTIF ;
							$paket_history->save();
		
							foreach($paket->matkul as $matkul){
								$mhs_matkul_orm = new Mhs_matkul_orm();
								$mhs_matkul_orm->mahasiswa_id = $id_mahasiswa;
								$mhs_matkul_orm->matkul_id = $matkul->id_matkul;
								$mhs_matkul_orm->sisa_kuota_latihan_soal = $paket->kuota_latihan_soal ;
								$mhs_matkul_orm->save();
		
								if($matkul->m_ujian->isNotEmpty()){
									foreach($matkul->m_ujian as $m_ujian){
										$mhs_ujian = new Mhs_ujian_orm();
										$mhs_ujian->mahasiswa_id = $id_mahasiswa;
										$mhs_ujian->ujian_id = $m_ujian->id_ujian;
										$mhs_ujian->save();
									}
								}
							}
						}
					}


					$users_temp->is_processed = 1 ; //  FLAG SUDAH DIPROSES
					$users_temp->save();

					echo 'DONE' . "\n";
					
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

			if ($this->form_validation->run() === FALSE)
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
	
			if ($this->form_validation->run() === FALSE)
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
