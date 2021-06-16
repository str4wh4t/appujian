<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if (!$this->ion_auth->logged_in()){
			redirect('auth');
		}
		$this->load->model('Dashboard_model', 'dashboard');
		$this->user = $this->ion_auth->user()->row();
	}

	public function index()
	{

		// vdebug($_SESSION);

		$user = $this->user;
		$user_groups = $this->ion_auth->get_users_groups($user->id)->result();
		$data = [
			'user' 			=> $user,
			'user_groups'	=> $user_groups,
			'judul'			=> 'Dashboard',
			'subjudul'		=> 'Data Aplikasi',
		];

		if ( is_admin() ) {

			$box = [
				[
					'box' 		=> 'olive',
					'total' 	=> $this->dashboard->total('matkul'),
					'title'		=> 'Materi Ujian',
					'link'		=> 'matkul',
					'icon'		=> 'graduation-cap'
				],
				[
					'box' 		=> 'olive',
					'total' 	=> $this->dashboard->total('dosen'),
					'title'		=> 'Dosen',
					'link'		=> 'dosen',
					'icon'		=> 'user-secret'
				],
				[
					'box' 		=> 'olive',
					'total' 	=> $this->dashboard->total('topik'),
					'title'		=> 'Topik',
					'link'		=> 'topik',
					'icon'		=> 'building-o'
				],
				[
					'box' 		=> 'olive',
					'total' 	=> $this->dashboard->total('mahasiswa'),
					'title'		=> 'Peserta Ujian',
					'link'		=> 'mahasiswa',
					'icon'		=> 'user'
				],
				[
					'box' 		=> 'olive',
					'total' 	=> $this->dashboard->total('tb_soal'),
					'title'		=> 'Soal',
					'link'		=> 'soal',
					'icon'		=> 'bookmark'
				],
				[
					'box' 		=> 'olive',
					'total' 	=> $this->dashboard->total('m_ujian'),
					'title'		=> 'Ujian',
					'link'		=> 'ujian/master',
					'icon'		=> 'book'
				],
			];
			$info_box = json_decode(json_encode($box), FALSE);

			$data['info_box'] = $info_box;
		} elseif ( in_group(KOORD_PENGAWAS_GROUP_ID) || in_group(PENGAWAS_GROUP_ID) || in_group(PENYUSUN_SOAL_GROUP_ID) ) {
			$data['user'] = $user;
		} elseif ( in_group(DOSEN_GROUP_ID) ) {
//			$matkul = ['matkul' => 'dosen.matkul_id=matkul.id_matkul'];
//			$data['dosen'] = $this->dashboard->get_where('dosen', 'nip', $user->username, $matkul)->row();
			$data['dosen'] = Orm\Dosen_orm::where('nip',$user->username)->firstOrFail();
//			$kelas = ['kelas' => 'kelas_dosen.kelas_id=kelas.id_kelas'];
//			$data['kelas'] = $this->dashboard->get_where('kelas_dosen', 'dosen_id' , $data['dosen']->id_dosen, $kelas, ['nama_kelas'=>'ASC'])->result();
		}else{
//			$join = [
//				'kelas b' 	=> 'a.kelas_id = b.id_kelas',
//				'jurusan c'	=> 'b.jurusan_id = c.id_jurusan'
//			];
			$data['mahasiswa'] = Orm\Mhs_orm::where('nim',$user->username)->firstOrFail(); // $this->dashboard->get_where('mahasiswa a', 'nim', $user->username, $join)->row();
		}

//		$this->load->view('_templates/dashboard/_header.php', $data);
//		$this->load->view('dashboard');
//		$this->load->view('_templates/dashboard/_footer.php');

		if(APP_TYPE == 'tryout'){
			if(in_group(MHS_GROUP_ID)){
				view('dashboard/tryout/mhs/index',$data);
			}else{
				view('dashboard/index',$data);
			}
		}else{
			view('dashboard/index',$data);
		}

	}
}
