<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\MySQL;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Orm\Mujian_orm;
use Orm\Hujian_orm;
use Orm\Hujian_history_orm;
use Orm\Jawaban_ujian_orm;
use Orm\Jawaban_ujian_history_orm;
use Orm\Dosen_orm;
use Orm\Mhs_orm;
use Orm\Matkul_orm;
use Orm\Topik_orm;
use Orm\Soal_orm;
use Orm\Topik_ujian_orm;
use Orm\Bobot_soal_orm;
use Orm\Mhs_ujian_orm;
use Orm\Mhs_matkul_orm;
use Orm\Users_groups_orm;
use Orm\Daftar_hadir_orm;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Capsule\Manager as DB;
use Orm\Users_orm;
use Orm\Membership_history_orm;
use Carbon\Carbon;

class Ujian extends MY_Controller
{

	public $mhs, $user;

	public function __construct()
	{
		parent::__construct();
		if (!$this->ion_auth->logged_in()) {
			redirect('auth');
		}
		$this->load->library(['datatables', 'form_validation']); // Load Library Ignited-Datatables
		$this->load->helper('my');
		$this->load->model('Master_model', 'master');
		$this->load->model('Soal_model', 'soal');
		$this->load->model('Ujian_model', 'ujian');
		$this->form_validation->set_error_delimiters('', '');

		$this->user = $this->ion_auth->user()->row();
		$this->mhs 	= $this->ujian->getIdMahasiswa($this->user->username);
	}

	protected function _data()
	{
		if (
			!$this->ion_auth->in_group('admin')
			&& !$this->ion_auth->in_group('dosen')
			&& !$this->ion_auth->in_group('pengawas')
		) {
			show_404();
		}
		//		if (empty($id)) {
		//			$dosen = Dosen_orm::where('nip', $this->ion_auth->user()->row()->username)->firstOrFail();
		//			$id    = $dosen->id_dosen;
		//		}
		$id = null;
		$username = null;

		if ($this->ion_auth->in_group('dosen')) {
			$username = $this->ion_auth->user()->row()->username;
		}

		$status_ujian = empty($this->input->post('status_ujian')) ? 'active' : $this->input->post('status_ujian');

		$this->_json($this->ujian->getDataUjian($id, $username, get_selected_role(), $status_ujian), false);
	}

	public function master()
	{
		if (
			!$this->ion_auth->in_group('admin') &&
			!$this->ion_auth->in_group('dosen') &&
			!$this->ion_auth->in_group('pengawas')
		) {
			show_404();
		}
		$user = $this->ion_auth->user()->row();
		$data = [
			'user' => $user,
			'judul'	=> 'Ujian',
			'subjudul' => 'List Ujian',
			//'dosen' => $this->ujian->getIdDosen($user->username),
		];

		if ($this->ion_auth->in_group('dosen')) {
		}
		//		$this->load->view('_templates/dashboard/_header.php', $data);
		//		$this->load->view('ujian/data');
		//		$this->load->view('_templates/dashboard/_footer.php');
		view('ujian/master', $data);
	}

	public function add()
	{
		if (!$this->ion_auth->in_group('admin') && !$this->ion_auth->in_group('dosen')) {
			show_404();
		}

		$data = [
			'judul'		=> 'Ujian',
			'subjudul'	=> 'Add Ujian',
			'matkul'	=> Matkul_orm::all(),
		];

		if ($this->ion_auth->in_group('dosen')) {
			$user = $this->ion_auth->user()->row();
			$data['matkul'] = Dosen_orm::where('nip', $user->username)->firstOrFail()->matkul;
		}

		$matkul_id = $this->input->get('m');
		$data['matkul_dipilih'] = $matkul_id;
		$topik = [];
		if (null != $matkul_id) {
			/**
			 * LOGIC INI SUDAH TIDAK DIGUNAKAN KRN TOPIK DIABIL VIA AJAX
			 */
			$matkul = Matkul_orm::findOrFail($matkul_id);
			$topik = $matkul->topik;
		}

		$data['topik'] = $topik;

		$data['bobot_soal'] = Bobot_soal_orm::all();
		$data['peserta_avail'] = [];

		$data['tahun_soal'] = Soal_orm::distinct()->pluck('tahun')->toArray();
		$data['tahun_mhs'] = Mhs_orm::distinct()->pluck('tahun')->filter()->toArray();

		view('ujian/add', $data);
	}

	public function edit($id = null)
	{
		if (!$this->ion_auth->in_group('admin') && !$this->ion_auth->in_group('dosen')) {
			show_404();
		}

		$ujian = Mujian_orm::findOrFail($id);
		$user = $this->ion_auth->user()->row();

		$data = [
			'judul'		=> 'Ujian',
			'subjudul'	=> 'Edit Ujian',
			'matkul'	=> Matkul_orm::all(),
			'ujian'		=> $ujian,
		];

		if ($this->ion_auth->in_group('dosen')) {
			$data['matkul'] = Dosen_orm::where('nip', $user->username)->firstOrFail()->matkul;
		}

		if (!$this->ion_auth->is_admin()) {
			if ($ujian->created_by != $user->username) {
				$message_rootpage = [
					'header' => 'Perhatian',
					'content' => 'Anda bukan pembuat ujian.',
					'type' => 'warning'
				];
				$this->session->set_flashdata('message_rootpage', $message_rootpage);
				redirect('ujian/master', 'refresh');
			}
		}

		$matkul_id = $this->input->get('m') == null ? $ujian->matkul_id : $this->input->get('m');
		$matkul_id = $matkul_id == $ujian->matkul_id ? $ujian->matkul_id : $this->input->get('m');
		$data['matkul_dipilih'] = $matkul_id;

		if (null != $matkul_id) {
			$matkul = Matkul_orm::findOrFail($matkul_id);
			$data['jumlah_soal_total'] = $matkul_id == $ujian->matkul_id ? $ujian->jumlah_soal : 0;
			$jumlah_soal = [];
			if ($matkul_id == $ujian->matkul_id) {
				$topik_ujian = $ujian->topik_ujian;
				$jumlah_soal = [];
				foreach ($topik_ujian as $t) {
					$jumlah_soal[$t->topik_id][$t->bobot_soal_id] = $t->jumlah_soal;
				}
			}

			$topik_dipilih = [];
			if(!empty($jumlah_soal)){
				foreach ($jumlah_soal as $topik_id => $bobot_soal) {
					$topik_dipilih[$topik_id] = Topik_orm::findOrFail($topik_id);
				}
			}
			$data['topik'] = $topik_dipilih;
			$data['jumlah_soal'] = $jumlah_soal;

			$urutan_topik = json_decode($ujian->urutan_topik, true); // AS ARRAY
			$data['urutan_topik'] = $urutan_topik;

		}

		$data['peserta_avail'] = []; // VALUE INI TIDAK BERGUNA KRN AKAN DI OVERIDE DI VIEW, DATA DIAMBIL SCR AJAX
		$data['bobot_soal'] = Bobot_soal_orm::all();

		$data['tahun_soal'] = Soal_orm::distinct()->pluck('tahun')->toArray();
		$data['tahun_mhs'] = Mhs_orm::distinct()->pluck('tahun')->filter()->toArray();

		view('ujian/edit', $data);
	}

	private function _validasi()
	{
		//		$this->_akses_dosen();

		//		vdebug($this->input->post('jumlah_soal'));
		//		$user 	= $this->ion_auth->user()->row();
		//		$dosen 	= $this->ujian->getIdDosen($user->username);
		//		$jml 	= $this->ujian->getJumlahSoal($dosen->id_dosen)->jml_soal;
		//		$jml_a 	= $jml + 1; // Jika tidak mengerti, silahkan baca user_guide codeigniter tentang form_validation pada bagian less_than

		// if ($this->input->post('matkul_id')) {
		$jumlah_soal_list = $this->input->post('jumlah_soal', TRUE);

		$gel			= $this->input->post('gel', true);
		$smt			= $this->input->post('smt', true);
		$tahun			= $this->input->post('tahun', true);

		$filter_data = [
			'gel' 		=> $gel == 'null' ? null : $gel,
			'smt' 		=> $smt == 'null' ? null : $smt,
			'tahun' 	=> $tahun == 'null' ? null : $tahun,
		];

		$filter = [];

		foreach ($filter_data as $key => $v) {
			if (!empty($v)) {
				$filter[$key] = $v;
			}
		}

		if (!empty($jumlah_soal_list)) {
			foreach ($jumlah_soal_list as $topik_id => $topik_id_list) {
				foreach ($topik_id_list as $bobot_soal_id => $jml_soal) {

					/**[START] TEST JUMLAH SOAL SESUAI ATAU TIDAK */

					$soal = Soal_orm::where(['topik_id' => $topik_id, 'bobot_soal_id' => $bobot_soal_id]);
					if (!empty($filter)) {
						$soal->where($filter);
					}
					$jumlah_soal_max = $soal->count();

					/**[STOP] TEST JUMLAH SOAL SESUAI ATAU TIDAK */

					$jml_min = $jumlah_soal_max  + 1;
					$this->form_validation->set_rules('jumlah_soal[' . $topik_id . '][' . $bobot_soal_id . ']', 'Jumlah Soal', "required|is_natural|less_than[{$jml_min}]", ['less_than' => "Soal tidak cukup, anda hanya memiliki {$jumlah_soal_max} soal"]);
				}
			}
		}
		$jumlah_soal_total = 0;
		$matkul            = Matkul_orm::findOrFail($this->input->post('matkul_id'));
		foreach ($matkul->topik as $matkul_topik) {
			$jumlah_soal_total = $jumlah_soal_total + $matkul_topik->soal->count();
		}
		$jumlah_soal_total_min = $jumlah_soal_total + 1;
		$this->form_validation->set_rules('jumlah_soal_total', 'Jumlah Soal Total', "required|is_natural_no_zero|less_than[{$jumlah_soal_total_min}]", ['less_than' => "Soal tidak cukup, matkul tsb hanya memiliki {$jumlah_soal_total} soal"]);
		// }


		$urutan_topik_list = $this->input->post('urutan_topik', TRUE);
		if (!empty($urutan_topik_list)) {
			foreach ($urutan_topik_list as $topik_id => $urutan) {
				$this->form_validation->set_rules('urutan_topik[' . $topik_id . ']', 'Urutan Topik', "required|is_natural");
			}
		}

		$is_sekuen_topik = $this->input->post('is_sekuen_topik', true) == 'on' ? 1 : 0;
		if($is_sekuen_topik){
			$waktu_topik_list = $this->input->post('waktu_topik', true);
			if (!empty($waktu_topik_list)) {
				foreach ($waktu_topik_list as $topik_id => $waktu) {
					$this->form_validation->set_rules('waktu_topik[' . $topik_id . ']', 'waktu Topik', "required|is_natural");
				}
			}


		}else{
			$this->form_validation->set_rules('waktu', 'Waktu', 'required|integer|max_length[4]|greater_than[0]');
		}

		$this->form_validation->set_rules('nama_ujian', 'Nama Ujian', 'required|alpha_numeric_spaces|max_length[50]');
		$this->form_validation->set_rules('matkul_id', 'Matkul', 'required');
		$this->form_validation->set_rules('topik_id[]', 'Topik', 'required');
		// $this->form_validation->set_rules('tgl_selesai', 'Tanggal Mulai', 'required');
		$tgl_selesai = $this->input->post('tgl_selesai', TRUE);
		$this->form_validation->set_rules(
			'tgl_mulai',
			'Tanggal Mulai',
			[
				'required',
				[
					'is_valid_tgl_mulai_value',
					function ($tgl_mulai) {
						$return = true;

						$d = DateTime::createFromFormat('Y-m-d H:i:s', $tgl_mulai);
						if (!($d && $d->format('Y-m-d H:i:s') == $tgl_mulai)) {
							$return = false;
						}

						return $return;
					}
				],
				[
					'is_valid_tgl_mulai',
					function ($tgl_mulai) use ($tgl_selesai) {
						$return = true;

						$startDate = strtotime($tgl_mulai);
						$endDate = strtotime($tgl_selesai);

						if ($endDate <= $startDate) {
							$return = false;
						}

						return $return;
					}
				],
			],
			[
				'is_valid_tgl_mulai_value' => 'Tgl mulai ujian tidak valid',
				'is_valid_tgl_mulai' => 'Tgl mulai tidak boleh melebihi / sama dengan tgl selesai ujian',
			]
		);

		$this->form_validation->set_rules(
			'tgl_selesai',
			'Tanggal Selesai',
			[
				'required',
				[
					'is_valid_tgl_selesai_value',
					function ($tgl_selesai) {
						$return = true;

						$d = DateTime::createFromFormat('Y-m-d H:i:s', $tgl_selesai);
						if (!($d && $d->format('Y-m-d H:i:s') == $tgl_selesai)) {
							$return = false;
						}

						return $return;
					}
				]
			],
			[
				'is_valid_tgl_selesai_value' => 'Tgl selesai ujian tidak valid',
			]
		);
	
		$this->form_validation->set_rules('masa_berlaku_sert', 'Masa Berlaku Sertifikat', 'required|is_natural');
		//		$this->form_validation->set_rules('pakai_token', 'Pakai Token', 'required|in_list[Y,N]');
		$this->form_validation->set_rules('jenis', 'Acak Soal', 'required|in_list[acak,urut]');
		$this->form_validation->set_rules('jenis_jawaban', 'Acak Jawaban', 'required|in_list[acak,urut]');
		//		$this->form_validation->set_rules('peserta_hidden', 'Peserta', 'required');
		$this->form_validation->set_rules(
			'gel',
			'Gel',
			[
				'required',
				[
					'is_valid_gel',
					function ($gel) {
						if ($gel != 'null') {
							return in_array($gel, GEL_AVAIL);
						} else {
							return true;
						}
					}
				]
			],
			[
				'is_valid_gel' => 'Gel yg dimasukan salah',
			]
		);
		$this->form_validation->set_rules(
			'smt',
			'Smt',
			[
				'required',
				[
					'is_valid_smt',
					function ($smt) {
						if ($smt != 'null') {
							return in_array($smt, SMT_AVAIL);
						} else {
							return true;
						}
					}
				]
			],
			[
				'is_valid_smt' => 'Smt yg dimasukan salah',
			]
		);

		$tahun_soal = Soal_orm::distinct()->pluck('tahun')->toArray();
		
		$this->form_validation->set_rules(
			'tahun',
			'Tahun',
			[
				'required',
				[
					'is_valid_tahun',
					function ($tahun) use ($tahun_soal) {
						if ($tahun != 'null') {
							return in_array($tahun, $tahun_soal);
						} else {
							return true;
						}
					}
				]
			],
			[
				'is_valid_tahun' => 'Tahun yg dimasukan salah',
			]
		);

		$this->form_validation->set_rules(
			'kelompok_ujian',
			'Kelompok Ujian',
			[
				'required',
				[
					'is_valid_kelompok_ujian',
					function ($kelompok_ujian) {
						return array_key_exists($kelompok_ujian, KELOMPOK_UJIAN_AVAIL);
					}
				]
			],
			[
				'is_valid_kelompok_ujian' => 'KelompoK Ujian yg dimasukan salah',
			]
		);

		$this->form_validation->set_rules(
			'tgl_ujian',
			'Tanggal Ujian',
			[
				[
					'is_valid_tgl_ujian_value',
					function ($tgl_ujian) {
						$return = true;
						if(!empty($tgl_ujian)){
							$d = DateTime::createFromFormat('Y-m-d', $tgl_ujian);
							if (!($d && $d->format('Y-m-d') == $tgl_ujian)) {
								$return = false;
							}
						}

						return $return;
					}
				]
			],
			[
				'is_valid_tgl_ujian_value' => 'Tgl ujian tidak valid',
			]
		);

		$tahun_mhs = Mhs_orm::distinct()->pluck('tahun')->toArray();

		$this->form_validation->set_rules(
			'tahun_mhs',
			'Tahun',
			[
				'required',
				[
					'is_valid_tahun_mhs',
					function ($tahun) use ($tahun_mhs) {
						if ($tahun != 'null') {
							return in_array($tahun, $tahun_mhs);
						} else {
							return true;
						}
					}
				]
			],
			[
				'is_valid_tahun_mhs' => 'Tahun yg dimasukan salah',
			]
		);
	}

	public function save()
	{
		if (!$this->ion_auth->in_group('admin') && !$this->ion_auth->in_group('dosen')) {
			show_404();
		}

		$this->_validasi();
		$this->load->helper('string');

		if ($this->form_validation->run() === FALSE) {
			$data['status'] = false;
			$data['errors'] = [
				'nama_ujian' 	=> form_error('nama_ujian'),
				'matkul_id' 	=> form_error('matkul_id'),
				'topik_id' 	=> form_error('topik_id[]'),
				'tgl_mulai' 	=> form_error('tgl_mulai'),
				'tgl_selesai' 	=> form_error('tgl_selesai'),
				'waktu' 		=> form_error('waktu'),
				'masa_berlaku_sert' 		=> form_error('masa_berlaku_sert'),
				//				'pakai_token' 		=> form_error('pakai_token'),
				'jenis' 		=> form_error('jenis'),
				'jenis_jawaban' 		=> form_error('jenis_jawaban'),
				'jumlah_soal_total' 	=> form_error('jumlah_soal_total'),
				//				'peserta_hidden' 	=> form_error('peserta_hidden'),
				'gel' 	=> form_error('gel'),
				'smt' 	=> form_error('smt'),
				'tahun' 	=> form_error('tahun'),
				'kelompok_ujian' 	=> form_error('kelompok_ujian'),
				'tgl_ujian' 	=> form_error('tgl_ujian'),
				'tahun_mhs' 	=> form_error('tahun_mhs'),
			];

			$jumlah_soal_list = $this->input->post('jumlah_soal', true);
			if (!empty($jumlah_soal_list)) {
				foreach ($jumlah_soal_list as $topik_id => $topik_id_list) {
					foreach ($topik_id_list as $bobot_soal_id => $jml_soal) {
						$data['errors']['jumlah_soal[' . $topik_id . '][' . $bobot_soal_id . ']'] = form_error('jumlah_soal[' . $topik_id . '][' . $bobot_soal_id . ']');
					}
				}
			}

			$urutan_topik_list = $this->input->post('urutan_topik', true);
			if (!empty($urutan_topik_list)) {
				foreach ($urutan_topik_list as $topik_id => $urutan) {
					$data['errors']['urutan_topik[' . $topik_id . ']'] = form_error('urutan_topik[' . $topik_id . ']');
				}
			}

			$is_sekuen_topik = $this->input->post('is_sekuen_topik', true) == 'on' ? 1 : 0;
			if($is_sekuen_topik){
				$waktu_topik_list = $this->input->post('waktu_topik', true);
				if (!empty($waktu_topik_list)) {
					foreach ($waktu_topik_list as $topik_id => $waktu) {
						$data['errors']['waktu_topik[' . $topik_id . ']'] = form_error('waktu_topik[' . $topik_id . ']');
					}
				}
			}

		} else {
			$user = $this->ion_auth->user()->row();

			$method 		= $this->input->post('method', true);
			$matkul_id 		= $this->input->post('matkul_id', true);
			$nama_ujian 	= $this->input->post('nama_ujian', true);
			$jumlah_soal_list = $this->input->post('jumlah_soal', true);
			$jumlah_soal = 0;
			$jumlah_soal_detail = [];
			if (!empty($jumlah_soal_list)) {
				foreach ($jumlah_soal_list as $bobot_soal_id => $topik_id_list) {
					foreach ($topik_id_list as $topik_id => $jml_soal) {
						$jumlah_soal = $jumlah_soal + $this->input->post('jumlah_soal[' . $bobot_soal_id . '][' . $topik_id . ']', TRUE);
						$jumlah_soal_detail[$bobot_soal_id][$topik_id] = $jml_soal;
					}
				}
			}

			$urutan_topik_list = $this->input->post('urutan_topik', true);
			$urutan_topik = [];
			if (!empty($urutan_topik_list)) {
				foreach ($urutan_topik_list as $topik_id => $urutan) {
					$urutan_topik[$topik_id] = [
						'urutan' => $urutan,
						'waktu'	=> 0,
					];
				}
			}

			$is_sekuen_topik = $this->input->post('is_sekuen_topik', true) == 'on' ? 1 : 0;
			
			$waktu			= $this->input->post('waktu', true);
			if($is_sekuen_topik){
				$waktu_topik_list = $this->input->post('waktu_topik', true);
				$jml_waktu_topik = 0;
				if (!empty($waktu_topik_list)) {
					foreach ($waktu_topik_list as $topik_id => $waktu) {
						$data['errors']['waktu_topik[' . $topik_id . ']'] = form_error('waktu_topik[' . $topik_id . ']');
						$jml_waktu_topik = $jml_waktu_topik + $waktu;
						$urutan_topik[$topik_id]['waktu'] = $waktu;
					}
				}
				$waktu = $jml_waktu_topik;
			}


			//			$jumlah_soal_detail = json_encode($jumlah_soal_detail);
			$tgl_mulai 		= $this->input->post('tgl_mulai', true);
			$tgl_selesai	= $this->input->post('tgl_selesai', true);
			// $waktu			= $this->input->post('waktu', true);
			$masa_berlaku_sert			= $this->input->post('masa_berlaku_sert', true);
			$pakai_token			= $this->input->post('pakai_token', true) == 'on' ? 1 : 0;
			$tampilkan_hasil			= $this->input->post('tampilkan_hasil', true) == 'on' ? 1 : 0;
			$tampilkan_jawaban			= $this->input->post('tampilkan_jawaban', true) == 'on' ? 1 : 0;
			$repeatable			= $this->input->post('repeatable', true) == 'on' ? 1 : 0;
			$jenis			= $this->input->post('jenis', true);
			$jenis_jawaban			= $this->input->post('jenis_jawaban', true);
			$peserta			= empty($this->input->post('peserta[]', true)) ? [] : $this->input->post('peserta[]', true);
			$gel			= $this->input->post('gel', true);
			$smt			= $this->input->post('smt', true);
			$tahun			= $this->input->post('tahun', true);
			$kelompok_ujian			= $this->input->post('kelompok_ujian', true);
			$tgl_ujian			= $this->input->post('tgl_ujian', true);
			$tahun_mhs			= $this->input->post('tahun_mhs', true);

			//[START] LOGIC TAMPILKAN HASIL & JAWABAN
			$tampilkan_hasil = $tampilkan_jawaban == 1 ? 1 : $tampilkan_hasil ;
			$tampilkan_jawaban = $tampilkan_hasil == 0 ? 0 : $tampilkan_jawaban ;
			//[STOP] LOGIC TAMPILKAN HASIL & JAWABAN

			$input = [
				'nama_ujian' 				=> $nama_ujian,
				'matkul_id' 				=> $matkul_id,
				'jumlah_soal' 				=> $jumlah_soal,
				// 'jumlah_soal_detail' => $jumlah_soal_detail,
				'tgl_mulai' 				=> $tgl_mulai,
				'terlambat' 				=> $tgl_selesai,
				'waktu' 					=> $waktu,
				'masa_berlaku_sert' 		=> $masa_berlaku_sert,
				'pakai_token' 				=> $pakai_token,
				'tampilkan_hasil' 			=> $tampilkan_hasil,
				'tampilkan_jawaban' 		=> $tampilkan_jawaban,
				'repeatable' 				=> $repeatable,
				'jenis' 					=> $jenis,
				'jenis_jawaban' 			=> $jenis_jawaban,
				'peserta' 					=> $peserta,
				'is_sekuen_topik'			=> $is_sekuen_topik,
				'urutan_topik'				=> json_encode($urutan_topik),
			];

			if ($method === 'add') {

				$input['status_ujian']		= 1; // STATUS SAAT DIBUAT UJIAN SCR DEFAULT AKTIF
				// $action = $this->master->create('m_ujian', $input);

				try {
					begin_db_trx();
					$m_ujian_orm = new Mujian_orm();
					$m_ujian_orm->nama_ujian = $input['nama_ujian'];
					$m_ujian_orm->matkul_id = $input['matkul_id'];
					$m_ujian_orm->jumlah_soal = $input['jumlah_soal'];
					// $m_ujian_orm->jumlah_soal_detail = $input['jumlah_soal_detail'];
					$m_ujian_orm->tgl_mulai = $input['tgl_mulai'];
					$m_ujian_orm->terlambat = $input['terlambat'];
					$m_ujian_orm->waktu = $input['waktu'];
					$m_ujian_orm->masa_berlaku_sert = $input['masa_berlaku_sert'];
					$m_ujian_orm->jenis = $input['jenis'];
					$m_ujian_orm->jenis_jawaban = $input['jenis_jawaban'];
					$m_ujian_orm->token		= strtoupper(random_string('alpha', 5));
					$m_ujian_orm->pakai_token = $input['pakai_token'];
					$m_ujian_orm->tampilkan_hasil = $input['tampilkan_hasil'];
					$m_ujian_orm->tampilkan_jawaban = $input['tampilkan_jawaban'];
					$m_ujian_orm->repeatable = $input['repeatable'];
					$m_ujian_orm->status_ujian = $input['status_ujian'];

					$m_ujian_orm->soal_gel = $gel == 'null' ? null : $gel;
					$m_ujian_orm->soal_smt = $smt == 'null' ? null : $smt;
					$m_ujian_orm->soal_tahun = $tahun == 'null' ? null : $tahun;

					$m_ujian_orm->mhs_kelompok_ujian = $kelompok_ujian == 'null' ? null : $kelompok_ujian;
					$m_ujian_orm->mhs_tgl_ujian = empty($tgl_ujian) ? null : $tgl_ujian;
					$m_ujian_orm->mhs_tahun = $tahun_mhs == 'null' ? null : $tahun_mhs;

					$m_ujian_orm->is_sekuen_topik = $input['is_sekuen_topik'];
					$m_ujian_orm->urutan_topik = $input['urutan_topik'];

					$m_ujian_orm->created_by = $user->username;
					$m_ujian_orm->save();

					foreach ($jumlah_soal_list as $topik_id => $topik_id_list) {
						foreach ($topik_id_list as $bobot_soal_id => $jml_soal) {
							$topik_ujian_orm = new Topik_ujian_orm();
							$topik_ujian_orm->ujian_id = $m_ujian_orm->id_ujian;
							$topik_ujian_orm->topik_id = $topik_id;
							$topik_ujian_orm->bobot_soal_id = $bobot_soal_id;
							$topik_ujian_orm->jumlah_soal = $jml_soal;
							$topik_ujian_orm->save();
						}
					}

					foreach ($peserta as $mhs_id) {
						$mhs_matkul = Mhs_matkul_orm::where(['mahasiswa_id' => $mhs_id, 'matkul_id' => $m_ujian_orm->matkul_id])->firstOrFail();
						$mhs_ujian_orm = new Mhs_ujian_orm();
						$mhs_ujian_orm->mahasiswa_matkul_id = $mhs_matkul->id;
						$mhs_ujian_orm->ujian_id = $m_ujian_orm->id_ujian;
						$mhs_ujian_orm->save();
					}

					commit_db_trx();
					$action = true;
				} catch (Exception $e) {
					rollback_db_trx();
					show_error($e->getMessage(), 500, 'Perhatian');
					$action = false;
				}
			} else if ($method === 'edit') {

				$id_ujian = $this->input->post('id_ujian', true);
				// $action = $this->master->update('m_ujian', $input, 'id_ujian', $id_ujian);

				try {
					begin_db_trx();
					$m_ujian_orm = Mujian_orm::findOrFail($id_ujian);
					$matkul_id_before = $m_ujian_orm->matkul_id;

					$m_ujian_orm->nama_ujian = $input['nama_ujian'];
					$m_ujian_orm->matkul_id = $input['matkul_id'];
					$m_ujian_orm->jumlah_soal = $input['jumlah_soal'];
					// $m_ujian_orm->jumlah_soal_detail = $input['jumlah_soal_detail'];
					$m_ujian_orm->tgl_mulai = $input['tgl_mulai'];
					$m_ujian_orm->terlambat = $input['terlambat'];
					$m_ujian_orm->waktu = $input['waktu'];
					$m_ujian_orm->masa_berlaku_sert = $input['masa_berlaku_sert'];
					$m_ujian_orm->jenis = $input['jenis'];
					$m_ujian_orm->jenis_jawaban = $input['jenis_jawaban'];
					// $m_ujian_orm->token = $input['token'];
					$m_ujian_orm->pakai_token = $input['pakai_token'];
					$m_ujian_orm->tampilkan_hasil = $input['tampilkan_hasil'];
					$m_ujian_orm->tampilkan_jawaban = $input['tampilkan_jawaban'];
					$m_ujian_orm->repeatable = $input['repeatable'];
					$m_ujian_orm->status_ujian = $this->input->post('status_ujian', true) == 'on' ? '1' : '0';

					$m_ujian_orm->soal_gel = $gel == 'null' ? null : $gel;
					$m_ujian_orm->soal_smt = $smt == 'null' ? null : $smt;
					$m_ujian_orm->soal_tahun = $tahun == 'null' ? null : $tahun;

					$m_ujian_orm->mhs_kelompok_ujian = $kelompok_ujian == 'null' ? null : $kelompok_ujian;
					$m_ujian_orm->mhs_tgl_ujian = empty($tgl_ujian) ? null : $tgl_ujian;
					$m_ujian_orm->mhs_tahun = $tahun_mhs == 'null' ? null : $tahun_mhs;

					$m_ujian_orm->is_sekuen_topik = $input['is_sekuen_topik'];
					$m_ujian_orm->urutan_topik = $input['urutan_topik'];

					$m_ujian_orm->updated_by = $user->username;
					$m_ujian_orm->save();

					Topik_ujian_orm::where('ujian_id', $id_ujian)->delete(); // LOGIKA NYA DI DELETE DULU BARU DI INSERT
					foreach ($jumlah_soal_list as $topik_id => $topik_id_list) {
						foreach ($topik_id_list as $bobot_soal_id => $jml_soal) {
							$topik_ujian_orm              = new Topik_ujian_orm();
							$topik_ujian_orm->ujian_id    = $m_ujian_orm->id_ujian;
							$topik_ujian_orm->topik_id    = $topik_id;
							$topik_ujian_orm->bobot_soal_id = $bobot_soal_id;
							$topik_ujian_orm->jumlah_soal = $jml_soal;
							$topik_ujian_orm->save();
						}
					}

					$mhs_ujian = Mhs_ujian_orm::where(['ujian_id' => $m_ujian_orm->id_ujian])
						->whereDoesntHave('h_ujian')
						->whereDoesntHave('h_ujian_history')
						->get();

					$peserta_ujian_before = [];
					if ($mhs_ujian->isNotEmpty()) {
						foreach ($mhs_ujian as $mu) {
							$peserta_ujian_before[] = $mu->mhs_matkul->mahasiswa_id;
						}
					}

					$mhs_ids_insert = array_diff($peserta, $peserta_ujian_before);
					$mhs_ids_delete = array_diff($peserta_ujian_before, $peserta);

					// vdebug($mhs_ids_delete);

					if (!empty($mhs_ids_delete)) {
						foreach ($mhs_ids_delete as $mhs_id) {

							if($matkul_id_before == $m_ujian_orm->matkul_id){
								$mhs_matkul = Mhs_matkul_orm::where([
									'mahasiswa_id' => $mhs_id,
									'matkul_id'    => $m_ujian_orm->matkul_id
								])->firstOrFail();
							}else{
								// JIKA MATKUL YANG DIPILIH BERBEDA DENGAN SEBELUMNYA
								$mhs_matkul = Mhs_matkul_orm::where([
									'mahasiswa_id' => $mhs_id,
									'matkul_id'    => $matkul_id_before
								])->firstOrFail();
							}

							// vdebug($mhs_matkul);

							$mhs_ujian_orm = Mhs_ujian_orm::where([
								'mahasiswa_matkul_id' => $mhs_matkul->id,
								'ujian_id'            => $m_ujian_orm->id_ujian
							])->firstOrFail();

							$mhs_ujian_orm->delete();
						}
					}

					if (!empty($mhs_ids_insert)) {
						foreach ($mhs_ids_insert as $mhs_id) {
							$mhs_matkul = Mhs_matkul_orm::where([
								'mahasiswa_id' => $mhs_id,
								'matkul_id'    => $m_ujian_orm->matkul_id
							])->firstOrFail();

							$mhs_ujian_orm                      = new Mhs_ujian_orm();
							$mhs_ujian_orm->mahasiswa_matkul_id = $mhs_matkul->id;
							$mhs_ujian_orm->ujian_id            = $m_ujian_orm->id_ujian;
							$mhs_ujian_orm->save();
						}
					}

					$h_ujian_list = Hujian_orm::where('ujian_id', $m_ujian_orm->id_ujian)->get();
					if($h_ujian_list->isNotEmpty()){
						$this->load->library('socket');
						foreach($h_ujian_list as $h_ujian){
							$h_ujian_start = $h_ujian->tgl_mulai;
							$minutes_to_add = $m_ujian_orm->waktu;

							$time = new DateTime($h_ujian_start);
							$time->add(new DateInterval('PT' . $minutes_to_add . 'M'));

							$h_ujian_stop_new = $time->format('Y-m-d H:i:s');

							$date_end = date('Y-m-d H:i:s', strtotime($m_ujian_orm->terlambat));
							$waktu_selesai 	= $h_ujian_stop_new > $date_end ? $date_end : $h_ujian_stop_new;

							$h_ujian->tgl_selesai = $waktu_selesai;
							if($h_ujian->ujian_selesai == 'N'){
								$cmd = '{"as":"'. get_selected_role()->name .'","cmd":"UPDATE_TIME","nim":"'. $h_ujian->mhs->nim .'","app_id":"'. APP_ID .'"}';
								$this->socket->notif_ws($cmd);
							}
							$h_ujian->save();
						}

						

					}

					commit_db_trx();
					$action = true;
				} catch (Exception $e) {
					rollback_db_trx();
					show_error($e->getMessage(), 500, 'Perhatian');
					$action = false;
				}
			}
			$data['status'] = $action;
		}
		$this->_json($data);
	}

	public function delete()
	{
		if (!$this->ion_auth->in_group('admin') && !$this->ion_auth->in_group('dosen')) {
			show_404();
		}
		$chk = $this->input->post('checked', true);
		if (!$chk) {
			$this->_json(['status' => false]);
		} else {

			$user = $this->ion_auth->user()->row();
			if (!$this->ion_auth->is_admin()) {
				$allow = true;
				foreach ($chk  as $c) {
					$ujian = Mujian_orm::findOrFail($c);
					if ($ujian->created_by != $user->username) {
						$allow = false;
						break;
					}
				}
				if (!$allow) {
					$data['status'] = FALSE;
					$this->_json($data);
					return;
				}
			}



			if ($this->master->delete('m_ujian', $chk, 'id_ujian')) {
				$this->_json(['status' => true, 'total' => count($chk)]);
			}
		}
	}

	protected function _refresh_token()
	{
		if (!$this->ion_auth->in_group('admin') && !$this->ion_auth->in_group('dosen')) {
			show_404();
		}


		$id = $this->input->post('id');
		$ujian = Mujian_orm::findOrFail($id);
		$user = $this->ion_auth->user()->row();

		if (!$this->ion_auth->is_admin()) {
			if ($ujian->created_by != $user->username) {
				$data['status'] = FALSE;
				$this->_json($data);
				return;
			}
		}

		$this->load->helper('string');
		$data['token'] = strtoupper(random_string('alpha', 5));
		$refresh = $this->master->update('m_ujian', $data, 'id_ujian', $ujian->id_ujian);
		$data['status'] = $refresh ? TRUE : FALSE;
		$this->_json($data);
	}

	/**
	 * BAGIAN MAHASISWA
	 */

	protected function _list_json()
	{
		$this->_akses_mahasiswa();

		$user = $this->ion_auth->user()->row();
		$mhs_orm = Mhs_orm::where('nim', $user->username)->firstOrFail();
		$list = $this->ujian->getListUjian($mhs_orm);
		$this->_json($list, false);
	}

	public function list()
	{
		if(APP_TYPE == 'tryout')
			redirect('ujian/latihan_soal');

		$this->_akses_mahasiswa();

		$user = $this->ion_auth->user()->row();

		$data = [
			'user' 		=> $user,
			'judul'		=> 'Ujian',
			'subjudul'	=> 'List Ujian',
			'mhs' 		=> $this->ujian->getIdMahasiswa($user->username),
		];
		//		$this->load->view('_templates/dashboard/_header.php', $data);
		//		$this->load->view('ujian/list');
		//		$this->load->view('_templates/dashboard/_footer.php');

		$user = $this->ion_auth->user()->row();
		$mhs_orm = Mhs_orm::where('nim', $user->username)->firstOrFail();
		// $mhs_matkul = Mhs_matkul_orm::where(['mahasiswa_id' => $mhs_orm->id_mahasiswa])->get();
		$mhs_ujian_aktif = Mhs_ujian_orm::whereHas(
			'mhs_matkul',
			function (Builder $query) use ($mhs_orm) {
				$query->where('mahasiswa_id', $mhs_orm->id_mahasiswa);
			}
		)->whereHas(
			'm_ujian',
			function (Builder $query){
				$query->where('status_ujian', 1);
			}
		)->whereDoesntHave(
			'h_ujian',
			function (Builder $query){
				$query->where('ujian_selesai', 'Y');
			}
		)->get();

		$mhs_ujian_riwayat = Mhs_ujian_orm::whereHas(
			'mhs_matkul',
			function (Builder $query) use ($mhs_orm) {
				$query->where('mahasiswa_id', $mhs_orm->id_mahasiswa);
			}
		)->whereHas(
			'm_ujian',
			function (Builder $query){
				$query->where('status_ujian', 1);
			}
		)->whereHas(
			'h_ujian',
			function (Builder $query){
				$query->where('ujian_selesai', 'Y');
			}
		)->get();

		// vdebug($m_ujian->count());
		// $data['ujian_aktif'] =  Mhs_matkul_orm::where(['mahasiswa_id' => $mhs_orm->id_mahasiswa])->mhs_ujian();
		// vdebug($data['ujian_aktif']);

		$data['mhs_ujian_aktif'] = $mhs_ujian_aktif;
		$data['mhs_ujian_riwayat'] = $mhs_ujian_riwayat;
		$data['is_show_tutorial'] = true;

		view('ujian/list', $data);
	}

	public function latihan_soal()
	{
		if(APP_TYPE == 'ujian')
			redirect('ujian/list');

		$this->_akses_mahasiswa();

		$user = $this->ion_auth->user()->row();

		$data = [
			'user' 		=> $user,
			'judul'		=> 'Ujian',
			'subjudul'	=> 'List Latihan Soal',
			'mhs' 		=> $this->ujian->getIdMahasiswa($user->username),
		];

		$user = $this->ion_auth->user()->row();
		$mhs_orm = Mhs_orm::where('nim', $user->username)->firstOrFail();

		$mhs_ujian_aktif = Mhs_ujian_orm::whereHas(
			'mhs_matkul',
			function (Builder $query) use ($mhs_orm) {
				$query->where('mahasiswa_id', $mhs_orm->id_mahasiswa);
			}
		)->whereHas(
			'm_ujian',
			function (Builder $query){
				$query->where('status_ujian', 1);
				$query->where('repeatable', 1); // LATIHAN SOAL JIKA UJIAN IS REPEATABLE
			}
		)->whereDoesntHave(
			'h_ujian',
			function (Builder $query){
				$query->where('ujian_selesai', 'Y');
			}
		)->get();

		$mhs_ujian_riwayat = Mhs_ujian_orm::whereHas(
			'mhs_matkul',
			function (Builder $query) use ($mhs_orm) {
				$query->where('mahasiswa_id', $mhs_orm->id_mahasiswa);
			}
		)->whereHas(
			'm_ujian',
			function (Builder $query){
				$query->where('status_ujian', 1);
				$query->where('repeatable', 1); // LATIHAN SOAL JIKA UJIAN IS REPEATABLE
			}
		)->whereHas(
			'h_ujian',
			function (Builder $query){
				$query->where('ujian_selesai', 'Y');
			}
		)->get();

		$data['mhs_ujian_aktif'] = $mhs_ujian_aktif;
		$data['mhs_ujian_riwayat'] = $mhs_ujian_riwayat;
		$data['is_show_tutorial'] = true;

		view('ujian/list', $data);
	}

	public function tryout()
	{
		if(APP_TYPE == 'ujian')
			redirect('ujian/list');

		$this->_akses_mahasiswa();

		$user = $this->ion_auth->user()->row();

		$data = [
			'user' 		=> $user,
			'judul'		=> 'Ujian',
			'subjudul'	=> 'List Latihan Soal',
			'mhs' 		=> $this->ujian->getIdMahasiswa($user->username),
		];

		$user = $this->ion_auth->user()->row();
		$mhs_orm = Mhs_orm::where('nim', $user->username)->firstOrFail();

		$mhs_ujian_aktif = Mhs_ujian_orm::whereHas(
			'mhs_matkul',
			function (Builder $query) use ($mhs_orm) {
				$query->where('mahasiswa_id', $mhs_orm->id_mahasiswa);
			}
		)->whereHas(
			'm_ujian',
			function (Builder $query){
				$query->where('status_ujian', 1);
				$query->where('repeatable', 0); // TRYOUT SOAL JIKA UJIAN IS NOT REPEATABLE
			}
		)->whereDoesntHave(
			'h_ujian',
			function (Builder $query){
				$query->where('ujian_selesai', 'Y');
			}
		)->get();

		$mhs_ujian_riwayat = Mhs_ujian_orm::whereHas(
			'mhs_matkul',
			function (Builder $query) use ($mhs_orm) {
				$query->where('mahasiswa_id', $mhs_orm->id_mahasiswa);
			}
		)->whereHas(
			'm_ujian',
			function (Builder $query){
				$query->where('status_ujian', 1);
				$query->where('repeatable', 0); // TRYOUT SOAL JIKA UJIAN IS NOT REPEATABLE
			}
		)->whereHas(
			'h_ujian',
			function (Builder $query){
				$query->where('ujian_selesai', 'Y');
			}
		)->get();

		$data['mhs_ujian_aktif'] = $mhs_ujian_aktif;
		$data['mhs_ujian_riwayat'] = $mhs_ujian_riwayat;
		$data['is_show_tutorial'] = false;

		view('ujian/list', $data);
	}

	public function token($id = null)
	{
		$id = integer_read_from_uuid($id);
		
		$this->_akses_mahasiswa();
		$user = $this->ion_auth->user()->row();

		$mhs_orm = Mhs_orm::where('nim', $user->username)->firstOrFail();
		
		$m_ujian = Mujian_orm::findOrFail($id);

		if(APP_TYPE == 'tryout'){
			$mhs_aktif_membership = get_mhs_aktif_membership($mhs_orm);
			if($m_ujian->repeatable){
				// JIKA PADA LATIHAN SOAL

				// if(empty($mhs_aktif_membership->sisa_kuota_latihan_soal)){
				// 	$message_rootpage = [
				// 		'header' => 'Perhatian',
				// 		'content' => 'Sisa kuota latihan soal anda sudah habis',
				// 		'type' => 'error'
				// 	];
				// 	$this->session->set_flashdata('message_rootpage', $message_rootpage);
				// 	redirect('ujian/latihan_soal');
				// }

				// $allow_1 = true ;
				// $allow_2 = true ;

				// $expired_at = new Carbon($mhs_aktif_membership->expired_at);
				// $today = Carbon::now();
				// if($today->greaterThanOrEqualTo($expired_at)){
				// 	$allow_1 = false ;
				// }

				// if($mhs_orm->mhs_matkul()
				// 	->where('matkul_id', $m_ujian->matkul_id)
				// 	->first()
				// 	->sisa_kuota_latihan_soal <= 0){
				// 		$allow_2 = false ;
				// }

				// if(!$allow_1 && !$allow_2){
					
				// }

				if(is_mhs_limit_by_kuota()){
					if($mhs_orm->mhs_matkul()
						->where('matkul_id', $m_ujian->matkul_id)
						->first()
						->sisa_kuota_latihan_soal <= 0){
							$message_rootpage = [
								'header' => 'Perhatian',
								'content' => 'Membership anda sudah expired / kuota latihan sudah habis',
								'type' => 'error'
							];
							$this->session->set_flashdata('message_rootpage', $message_rootpage);
							redirect('ujian/latihan_soal');
					}

				}

				
			}else{
				// JIKA PADA TRYOUT

				if($mhs_aktif_membership->membership_id == MEMBERSHIP_ID_DEFAULT){
					// JIKA USER GRATIS
					$message_rootpage = [
						'header' => 'Perhatian',
						'content' => 'Anda tidak di ijinkan mengakses halaman ini',
						'type' => 'error'
					];
					$this->session->set_flashdata('message_rootpage', $message_rootpage);
					redirect('ujian/tryout');
				}

				$expired_at = new Carbon($mhs_aktif_membership->expired_at);
				$today = Carbon::now();
				if($today->greaterThan($expired_at)){
					$message_rootpage = [
						'header' => 'Perhatian',
						'content' => 'Membership anda sudah expired',
						'type' => 'error'
					];
					$this->session->set_flashdata('message_rootpage', $message_rootpage);
					redirect('ujian/tryout');
				}
			}
			
		}

		$this->session->unset_userdata('one_time_token');
		$one_time_token = Uuid::uuid1()->toString();
		$this->session->set_userdata('one_time_token', $one_time_token);

		$mhs = $this->ujian->getIdMahasiswa($user->username);

		
		$data = [
			'user' 		=> $user,
			'judul'		=> 'Ujian',
			'subjudul'	=> 'Mulai Ujian',
			'mhs' 		=> $mhs,
			'ujian'		=> $m_ujian,
			'token'     => $m_ujian->token,
			'encrypted_id' => uuid_create_from_integer($id),
			'one_time_token' => $one_time_token,
		];

		//		$m_ujian = Mujian_orm::findOrFail($id);
		//		$data['m_ujian'] = $m_ujian;

		$h_ujian = Hujian_orm::where(['ujian_id' => $id, 'mahasiswa_id' => $mhs->id_mahasiswa])->first();
		if (!empty($h_ujian)) {
			if ($h_ujian->ujian_selesai == 'Y') {
				$message_rootpage = [
					'header' => 'Perhatian',
					'content' => 'Ujian telah diakhiri.',
					'type' => 'success'
				];
				$this->session->set_flashdata('message_rootpage', $message_rootpage);
				redirect('ujian/list');
			}

			$data['h_ujian'] = $h_ujian;
		}

		$urutan_topik = $m_ujian->urutan_topik;
		$urutan_topik = json_decode($urutan_topik, true);
		uasort($urutan_topik, function($a, $b){
			return $a['urutan'] <=> $b['urutan'];
		});

		$data['urutan_topik'] = $urutan_topik;
		$data['topik_orm'] = new Topik_orm();

		//		$this->load->view('_templates/topnav/_header.php', $data);
		////		$this->load->view('ujian/token');
		////		$this->load->view('_templates/topnav/_footer.php');
		view('ujian/token', $data);
	}

	protected function _cektoken()
	{
		$id = $this->input->post('id_ujian', true);
		$id = integer_read_from_uuid($id);
		$token = $this->input->post('token', true);
		$ujian = Mujian_orm::findOrFail($id);

		sleep(1);

		if ($ujian->pakai_token == 1) {
			//			$this->session->unset_userdata('status_token');
			//			$this->session->set_userdata('status_token', $token === $ujian->token);
			$data['status'] = $token === $ujian->token ? TRUE : FALSE;
			$data['token']  = $token === $ujian->token ? $ujian->token : 'XXX';
		} else {
			$data['status'] = TRUE;
			$data['token']  = $ujian->token;
		}
		$this->_json($data);
	}

	protected function _encrypt()
	{
		$id = $this->input->post('id', true);
		$key = urlencode($this->encryption->encrypt($id));
		// $decrypted = $this->encryption->decrypt(rawurldecode($key));
		$this->_json(['key' => $key]);
	}

	public function index()
	{
		if (!$this->input->get()) {
			show_404();
		}

		$this->_akses_mahasiswa();
		$key = $this->input->get('key', true);
		$uuid  = $this->input->get('id', true);
		$token = $this->input->get('token', true);

		$one_time_token = $this->session->userdata('one_time_token');

		if (empty($one_time_token)) {
			show_404();
		}

		if ($one_time_token != $key) {
			show_404();
		}

		$id = integer_read_from_uuid($uuid);

		$ujian      = Mujian_orm::findOrFail($id);

		if(!$ujian->status_ujian){ // CEK STATUS UJIAN IS CLOSE = 0
			show_404();
		}

		if ($ujian->token !== trim($token)) {
			show_404();
		}

		$user = $this->ion_auth->user()->row();		
		$mhs		= Mhs_orm::where('nim', $user->username)->firstOrFail();
		$h_ujian 	= Hujian_orm::where('ujian_id', $ujian->id_ujian)->where('mahasiswa_id', $mhs->id_mahasiswa)->first();

		$cek_sudah_ikut = $h_ujian == null ? false : true;

		if (!$cek_sudah_ikut) {

			// CEK UNTUK TRYOUT
			if(APP_TYPE == 'tryout'){
				// $mhs_aktif_membership = get_mhs_aktif_membership($mhs);
				if($ujian->repeatable){
					// JIKA UJIAN LATIHAN SOAL
					// if($mhs_aktif_membership->membership->is_limit_by_kuota){
						// $membership_history_user = Membership_history_orm::findOrFail($mhs_aktif_membership->id);
						// $membership_history_user->sisa_kuota_latihan_soal = ($membership_history_user->sisa_kuota_latihan_soal - 1);
						// $membership_history_user->save();
					// }
					if(is_mhs_limit_by_kuota()){
						$mhs_matkul = $mhs->mhs_matkul()->where('matkul_id', $ujian->matkul_id)->first();
						$mhs_matkul->sisa_kuota_latihan_soal = ($mhs_matkul->sisa_kuota_latihan_soal - 1);
						$mhs_matkul->save();
					}
				}
			}

			/*
			 * [START]CHECK VALID TIME M_UJIAN
			 */

			$today = date('Y-m-d H:i:s');
			//echo $paymentDate; // echos today!
			$date_start = date('Y-m-d H:i:s', strtotime($ujian->tgl_mulai));
			$date_end = date('Y-m-d H:i:s', strtotime($ujian->terlambat));

			if (!(($today >= $date_start) && ($today < $date_end))) {
				show_404();
			}

			/*
			 * [END]CHEK VALID TIME M_UJIAN
			 */

			$soal 		= [];
			$soal_topik = [];
			$i = 0;
			foreach ($ujian->topik_ujian as $topik_ujian) {
				$jumlah_soal_diset = $topik_ujian->jumlah_soal;
				$soal_avail = Soal_orm::where('topik_id', $topik_ujian->topik_id)
										->where('bobot_soal_id', $topik_ujian->bobot_soal_id);

				$filter_data = [
					'gel' 		=> $ujian->soal_gel,
					'smt' 		=> $ujian->soal_smt,
					'tahun' 	=> $ujian->soal_tahun,
				];
	
				$filter = [];

				foreach ($filter_data as $k => $v) {
					if (!empty($v)) {
						$filter[$k] = $v;
					}
				}
				
				if (!empty($filter)){
					$soal_avail->where($filter);
				}

				$soal_avail = $soal_avail->get()->sortBy('id_soal');

				if ($jumlah_soal_diset > $soal_avail->count()) {
					show_error('Jumlah soal tidak memenuhi untuk ujian', 500, 'Perhatian');
				}

				foreach ($soal_avail as $s) {
					if ($i < $jumlah_soal_diset) {
						$soal_topik[] = $s;
						$i++;
					} else {
						break;
					}
				}

				if ($ujian->jenis == 'acak') {
					shuffle($soal_topik);
				}

				$soal[$topik_ujian->topik_id][$topik_ujian->bobot_soal_id] = $soal_topik;
				$soal_topik = [];
				$i = 0;
			}

			$waktu_selesai 	= date('Y-m-d H:i:s', strtotime("+{$ujian->waktu} minute"));

			$waktu_selesai 	= $waktu_selesai > $date_end ? $date_end : $waktu_selesai;
			$time_mulai		= date('Y-m-d H:i:s');

			$mhs_matkul = Mhs_matkul_orm::where(['mahasiswa_id' => $mhs->id_mahasiswa, 'matkul_id' => $ujian->matkul_id])->firstOrFail();
			$mhs_ujian = Mhs_ujian_orm::where(['mahasiswa_matkul_id' => $mhs_matkul->id, 'ujian_id' => $ujian->id_ujian])->firstOrFail();

			$input = [
				'ujian_id' 		=> $ujian->id_ujian,
				'mahasiswa_id'	=> $mhs->id_mahasiswa,
				'mahasiswa_ujian_id'  => $mhs_ujian->id,
				'jml_soal'		=> 0,
				'jml_benar'		=> 0,
				'jml_salah'		=> 0,
				'nilai'			=> 0,
				'nilai_bobot_benar'			=> 0,
				'total_bobot'			=> 0,
				'nilai_bobot'	=> 0,
				'tgl_mulai'		=> $time_mulai,
				'tgl_selesai'	=> $waktu_selesai,
				'ujian_selesai'		=> 'N',
			];
			//			$this->master->create('h_ujian', $input);

			try {
				begin_db_trx();
				$h_ujian = new Hujian_orm();
				$h_ujian->ujian_id = $input['ujian_id'];
				$h_ujian->mahasiswa_id = $input['mahasiswa_id'];
				$h_ujian->mahasiswa_ujian_id = $input['mahasiswa_ujian_id'];
				$h_ujian->jml_soal = $input['jml_soal'];
				$h_ujian->jml_benar = $input['jml_benar'];
				$h_ujian->jml_salah = $input['jml_salah'];
				$h_ujian->nilai = $input['nilai'];
				$h_ujian->nilai_bobot_benar = $input['nilai_bobot_benar'];
				$h_ujian->total_bobot = $input['total_bobot'];
				$h_ujian->nilai_bobot = $input['nilai_bobot'];
				$h_ujian->tgl_mulai = $input['tgl_mulai'];
				$h_ujian->tgl_selesai = $input['tgl_selesai'];
				$h_ujian->ujian_selesai = $input['ujian_selesai'];
				$h_ujian->save();

				foreach ($soal as $topik_id => $t) {
					foreach ($t as $bobot_soal_id => $d) {
						foreach ($d as $s) {
							$jawaban_ujian_orm           = new Jawaban_ujian_orm();
							$jawaban_ujian_orm->ujian_id = $h_ujian->id;
							$jawaban_ujian_orm->soal_id  = $s->id_soal;
							$jawaban_ujian_orm->save();
						}
					}
				}

				commit_db_trx();
				$action = true;
			} catch (Exception $e) {
				rollback_db_trx();
				show_error($e->getMessage(), 500, 'Perhatian');
				$action = false;
			}

			$url_target = 'ujian/?key=' . urlencode($key) . '&id=' . $uuid . '&token=' . $token;

			if ($action)
				redirect($url_target, 'location', 301);
			// Setelah insert wajib refresh dulu
		}

		// $this->session->unset_userdata('one_time_token');

		/*
		 * [START]CHECK VALID TIME H_UJIAN
		 */

		$today = date('Y-m-d H:i:s');
		//echo $paymentDate; // echos today!
		$date_start = date('Y-m-d H:i:s', strtotime($h_ujian->tgl_mulai));
		$date_end = date('Y-m-d H:i:s', strtotime($h_ujian->tgl_selesai));

		if (!(($today >= $date_start) && ($today < $date_end))) {
			show_404();
		}

		/*
		 * [END]CHEK VALID TIME H_UJIAN
		 */

		/**
		 * [START] LOGIK URUTAN TOPIK
		 */
		$urutan_topik = $h_ujian->m_ujian->urutan_topik;
		if(!empty($urutan_topik)){
			$urutan_topik = json_decode($urutan_topik, true);
			// vdebug($urutan_topik);
			uasort($urutan_topik, function($a, $b){
				return $a['urutan'] <=> $b['urutan'];
			});
			// vdebug($urutan_topik);
			$jawaban_ujian = collect();
			foreach($urutan_topik as $topik_id => $v){
				$item_list = $h_ujian->jawaban_ujian()
								->whereHas('soal', function(Builder $query) use ($topik_id){
									$query->where('topik_id', $topik_id);
								})
								->get()
								->sortBy('id');				
				if($item_list->isNotEmpty()){
					foreach($item_list as $item){
						$jawaban_ujian->add($item);
					}
				}
			}
		}else{
			$urutan_topik = [];
			$jawaban_ujian = $h_ujian->jawaban_ujian->sortBy('id');
		}
		/**
		 * [STOP] LOGIK URUTAN TOPIK
		 */

		$list_jawaban = '';
		foreach ($jawaban_ujian as $jwb) {
			$list_jawaban .= $jwb->soal_id . ":" . $jwb->jawaban . ":" . $jwb->status_jawaban . ",";
		}
		$list_jawaban 	= substr($list_jawaban, 0, -1);

		$urut_soal 		= explode(",", $list_jawaban);
		$soal_urut_ok	= [];
		for ($i = 0; $i < sizeof($urut_soal); $i++) {
			$pc_urut_soal	= explode(":", $urut_soal[$i]);
			$pc_urut_soal1 	= empty($pc_urut_soal[1]) ? "''" : "'{$pc_urut_soal[1]}'";
			$ambil_soal 	= $this->ujian->ambilSoal($pc_urut_soal1, $pc_urut_soal[0]);
			$soal_urut_ok[] = $ambil_soal;
		}

		$pc_list_jawaban = explode(",", $list_jawaban);
		$arr_jawab = array();
		foreach ($pc_list_jawaban as $v) {
			$pc_v 	= explode(":", $v);
			$idx 	= $pc_v[0];
			$val 	= $pc_v[1];
			$rg 	= $pc_v[2];

			$arr_jawab[$idx] = array("j" => $val, "r" => $rg);
		}

		$arr_opsi = array("a", "b", "c", "d", "e");
		$html = '';
		$html_pertanyaan = '';
		$no = 1;
		if (!empty($soal_urut_ok)) {
			foreach ($soal_urut_ok as $s) {
				$path = 'uploads/bank_soal/';
				$vrg = $arr_jawab[$s->id_soal]["r"] == "" ? "N" : $arr_jawab[$s->id_soal]["r"];
				$html .= '<input type="hidden" name="id_soal_'. $no .'" value="'. $s->id_soal .'">';
				$html .= '<input type="hidden" name="rg_'. $no .'" id="rg_'. $no .'" value="'. $vrg .'">';
				$html .= '<input type="hidden" name="topik_id_'. $no .'" id="topik_id_'. $no .'" value="'. $s->topik_id .'">';

				$html_pertanyaan .= '<div class="step step_pertanyaan" id="widget_'. $no .'">
										<div class="pertanyaan">
											<div class="media-pertanyaan">'. tampil_media($path . $s->file) .'</div>
											<div>'. $s->soal .'</div>
										</div>
									</div>';

				$html .= '<div class="step" id="widget_jawaban_' . $no . '">';
				$html .= '<div class="funkyradio">';

				$urutan_jawaban = [0, 1, 2, 3, 4];
				$urutan_jawaban_huruf = ['a', 'b', 'c', 'd', 'e'];
				$ujian->jenis_jawaban == 'acak' ? shuffle($urutan_jawaban) : null;

				// for ($j = 0; $j < $this->config->item('jml_opsi'); $j++) {
				$i = 0;
				foreach ($urutan_jawaban as $j) {
					$opsi 			= "opsi_" . $arr_opsi[$j];
					$file 			= "file_" . $arr_opsi[$j];
					$checked 		= $arr_jawab[$s->id_soal]["j"] == strtoupper($arr_opsi[$j]) ? "checked" : "";
					$pilihan_opsi 	= !empty($s->$opsi) ? $s->$opsi : "";
					$tampil_media_opsi = (is_file(base_url() . $path . $s->$file) || $s->$file != "") ? tampil_media($path . $s->$file) : "";

					$html .= '<div class="funkyradio-success">
						<input type="radio" id="opsi_' . strtolower($arr_opsi[$j]) . '_' . $s->id_soal . '" name="opsi_' . $no . '" data-sid="' . $s->id_soal . '" value="' . strtoupper($arr_opsi[$j]) . '" rel="' . $no . '" ' . $checked . '>
						<label for="opsi_' . strtolower($arr_opsi[$j]) . '_' . $s->id_soal . '" class="label_pilihan">
							<div class="huruf_opsi">' . $urutan_jawaban_huruf[$i] . '</div> <div>' . $pilihan_opsi . '</div><div class="w-25">' . $tampil_media_opsi . '</div>
						</label></div>';
					$i++;
				}

				$html .= '</div></div>';
				$no++;
			}
		}

		// Enkripsi Id Tes
		// $id_tes = $this->encryption->encrypt($detail_tes->id);
		$data = [
			'user' 		=> $user,
			'mhs'		=> $mhs,
			'judul'		=> 'Ujian',
			'subjudul'	=> 'Lembar Ujian',
			//			'soal'		=> $detail_tes,
			'no' 		=> $no,
			'html' 		=> $html,
			'html_pertanyaan' => $html_pertanyaan,
			'one_time_token' => $one_time_token,
			'h_ujian' => $h_ujian,
			'id_tes' => uuid_create_from_integer($h_ujian->id),
			//			'id_tes'	=> $id_tes
			'urutan_topik'	=> $urutan_topik,
		];
		//		$this->load->view('_templates/topnav/_header.php', $data);
		//		$this->load->view('ujian/sheet');
		//		$this->load->view('_templates/topnav/_footer.php');

		view('ujian/index', $data);
	}

	//	public function index_()
	//	{
	//		$this->akses_mahasiswa();
	//		if(!$this->input->get()){
	//			show_404();
	//		}
	//		$key = $this->input->get('key', true);
	//		$uuid  = $this->input->get('id', true);
	//
	//		$one_time_token = $this->session->userdata('one_time_token');
	//
	//		if(empty($one_time_token)){
	//			show_404();
	//		}
	//
	//		if($one_time_token != $key){
	//			show_404();
	//		}
	//
	//
	//		$id = integer_read_from_uuid($uuid);
	////		echo $id . '<br>' ;
	////		die;
	//		$m_ujian_orm = Mujian_orm::findOrFail($id);
	//
	//		$ujian 		= $this->ujian->getUjianById($id);
	//		$soal 		= $this->ujian->getSoal($id);
	//
	//		$mhs		= $this->mhs;
	//		$h_ujian 	= $this->ujian->HslUjian($id, $mhs->id_mahasiswa);
	//
	//		$cek_sudah_ikut = $h_ujian->num_rows();
	//
	//		if ($cek_sudah_ikut < 1) {
	//
	//			/*
	//			 * [START]CHECK VALID TIME M_UJIAN
	//			 */
	//
	//			$today = date('Y-m-d H:i:s');
	//			//echo $paymentDate; // echos today!
	//			$date_start = date('Y-m-d H:i:s', strtotime($m_ujian_orm->tgl_mulai));
	//			$date_end = date('Y-m-d H:i:s', strtotime($m_ujian_orm->terlambat));
	//
	//			if (!(($today >= $date_start) && ($today < $date_end))){
	//			    show_404();
	//			}
	//
	//			/*
	//			 * [END]CHEK VALID TIME M_UJIAN
	//			 */
	//
	//			$soal_urut_ok 	= array();
	//			$i = 0;
	//			foreach ($soal as $s) {
	//				$soal_per = new stdClass();
	//				$soal_per->id_soal 		= $s->id_soal;
	//				$soal_per->soal 		= $s->soal;
	//				$soal_per->file 		= $s->file;
	//				$soal_per->tipe_file 	= $s->tipe_file;
	//				$soal_per->opsi_a 		= $s->opsi_a;
	//				$soal_per->opsi_b 		= $s->opsi_b;
	//				$soal_per->opsi_c 		= $s->opsi_c;
	//				$soal_per->opsi_d 		= $s->opsi_d;
	//				$soal_per->opsi_e 		= $s->opsi_e;
	//				$soal_per->jawaban 		= $s->jawaban;
	//				$soal_urut_ok[$i] 		= $soal_per;
	//				$i++;
	//			}
	//			$soal_urut_ok 	= $soal_urut_ok;
	//			$list_id_soal	= "";
	//			$list_jw_soal 	= "";
	//			if (!empty($soal)) {
	//				foreach ($soal as $d) {
	//					$list_id_soal .= $d->id_soal.",";
	//					$list_jw_soal .= $d->id_soal."::N,";
	//				}
	//			}
	//			$list_id_soal 	= substr($list_id_soal, 0, -1);
	//			$list_jw_soal 	= substr($list_jw_soal, 0, -1);
	//			$waktu_selesai 	= date('Y-m-d H:i:s', strtotime("+{$ujian->waktu} minute"));
	//			$time_mulai		= date('Y-m-d H:i:s');
	//
	//			$input = [
	//				'ujian_id' 		=> $id,
	//				'mahasiswa_id'	=> $mhs->id_mahasiswa,
	//				'list_soal'		=> $list_id_soal,
	//				'list_jawaban' 	=> $list_jw_soal,
	//				'jml_benar'		=> 0,
	//				'nilai'			=> 0,
	//				'nilai_bobot'	=> 0,
	//				'tgl_mulai'		=> $time_mulai,
	//				'tgl_selesai'	=> $waktu_selesai,
	//				'status'		=> 'Y'
	//			];
	//			$this->master->create('h_ujian', $input);
	//
	//			// Setelah insert wajib refresh dulu
	//			redirect('ujian/?key='. urlencode($key) .'&id=' . $uuid, 'location', 301);
	//		}
	//
	////		$this->session->unset_userdata('one_time_token');
	//
	//		$h_ujian_orm = Hujian_orm::where(['ujian_id' => $id,'mahasiswa_id' => $mhs->id_mahasiswa])->first();
	//		/*
	//		 * [START]CHECK VALID TIME H_UJIAN
	//		 */
	//
	//		$today = date('Y-m-d H:i:s');
	//		//echo $paymentDate; // echos today!
	//		$date_start = date('Y-m-d H:i:s', strtotime($h_ujian_orm->tgl_mulai));
	//		$date_end = date('Y-m-d H:i:s', strtotime($h_ujian_orm->tgl_selesai));
	//
	//		if (!(($today >= $date_start) && ($today < $date_end))){
	//		    show_404();
	//		}
	//
	//		/*
	//		 * [END]CHEK VALID TIME H_UJIAN
	//		 */
	//
	//		$q_soal = $h_ujian->row();
	//
	//		$urut_soal 		= explode(",", $q_soal->list_jawaban);
	//		$soal_urut_ok	= array();
	//		for ($i = 0; $i < sizeof($urut_soal); $i++) {
	//			$pc_urut_soal	= explode(":",$urut_soal[$i]);
	//			$pc_urut_soal1 	= empty($pc_urut_soal[1]) ? "''" : "'{$pc_urut_soal[1]}'";
	//			$ambil_soal 	= $this->ujian->ambilSoal($pc_urut_soal1, $pc_urut_soal[0]);
	//			$soal_urut_ok[] = $ambil_soal;
	//		}
	//
	//		$detail_tes = $q_soal;
	//		$soal_urut_ok = $soal_urut_ok;
	//
	//		$pc_list_jawaban = explode(",", $detail_tes->list_jawaban);
	//		$arr_jawab = array();
	//		foreach ($pc_list_jawaban as $v) {
	//			$pc_v 	= explode(":", $v);
	//			$idx 	= $pc_v[0];
	//			$val 	= $pc_v[1];
	//			$rg 	= $pc_v[2];
	//
	//			$arr_jawab[$idx] = array("j"=>$val,"r"=>$rg);
	//		}
	//
	//		$arr_opsi = array("a","b","c","d","e");
	//		$html = '';
	//		$no = 1;
	//		if (!empty($soal_urut_ok)) {
	//			foreach ($soal_urut_ok as $s) {
	//				$path = 'uploads/bank_soal/';
	//				$vrg = $arr_jawab[$s->id_soal]["r"] == "" ? "N" : $arr_jawab[$s->id_soal]["r"];
	//				$html .= '<input type="hidden" name="id_soal_'.$no.'" value="'.$s->id_soal.'">';
	//				$html .= '<input type="hidden" name="rg_'.$no.'" id="rg_'.$no.'" value="'.$vrg.'">';
	//				$html .= '<div class="step" id="widget_'.$no.'">';
	//
	//				$html .= '<div class="text-center"><div class="w-25">'.tampil_media($path.$s->file).'</div></div>'.$s->soal.'<div class="funkyradio">';
	//				for ($j = 0; $j < $this->config->item('jml_opsi'); $j++) {
	//					$opsi 			= "opsi_".$arr_opsi[$j];
	//					$file 			= "file_".$arr_opsi[$j];
	//					$checked 		= $arr_jawab[$s->id_soal]["j"] == strtoupper($arr_opsi[$j]) ? "checked" : "";
	//					$pilihan_opsi 	= !empty($s->$opsi) ? $s->$opsi : "";
	//					$tampil_media_opsi = (is_file(base_url().$path.$s->$file) || $s->$file != "") ? tampil_media($path.$s->$file) : "";
	//					$html .= '<div class="funkyradio-success"">
	//						<input type="radio" id="opsi_'.strtolower($arr_opsi[$j]).'_'.$s->id_soal.'" name="opsi_'.$no.'" value="'.strtoupper($arr_opsi[$j]).'" rel="'.$no.'" '.$checked.'>
	//						<label for="opsi_'.strtolower($arr_opsi[$j]).'_'.$s->id_soal.'" class="label_pilihan">
	//							<div class="huruf_opsi">'.$arr_opsi[$j].'</div> <p>'.$pilihan_opsi.'</p><div class="w-25">'.$tampil_media_opsi.'</div>
	//						</label></div>';
	//				}
	//				$html .= '</div></div>';
	//				$no++;
	//			}
	//		}
	//
	//		// Enkripsi Id Tes
	//		$id_tes = $this->encryption->encrypt($detail_tes->id);
	//
	//		$data = [
	//			'user' 		=> $this->user,
	//			'mhs'		=> $this->mhs,
	//			'judul'		=> 'Ujian',
	//			'subjudul'	=> 'Lembar Ujian',
	//			'soal'		=> $detail_tes,
	//			'no' 		=> $no,
	//			'html' 		=> $html,
	//			'id_tes'	=> $id_tes
	//		];
	//		$this->load->view('_templates/topnav/_header.php', $data);
	//		$this->load->view('ujian/sheet');
	//		$this->load->view('_templates/topnav/_footer.php');
	//
	////		view('ujian/index',$data);
	//
	//	}

	// protected function _simpan_jawaban_all()
	// {
	// 	$key = $this->input->post('key', true);
	// 	$one_time_token = $this->session->userdata('one_time_token');

	// 	if (empty($one_time_token)) {
	// 		show_404();
	// 	}

	// 	if ($one_time_token != $key) {
	// 		show_error('Token salah', 500, 'Perhatian');
	// 	}

	// 	// Decrypt Id
	// 	$id = $this->input->post('id', true);
	// 	$id = integer_read_from_uuid($id);

	// 	$h_ujian = Hujian_orm::findOrFail($id);
	// 	if ($h_ujian->ujian_selesai == 'Y') {
	// 		show_error('Ujian sudah diakhiri.', 500, 'Perhatian');
	// 	}

	// 	$input 	= $this->input->post(null, true);
	// 	try {
	// 		begin_db_trx();
	// 		for ($i = 1; $i < $input['jml_soal']; $i++) {
	// 			$_tjawab 	= "opsi_" . $i;
	// 			$_tidsoal 	= "id_soal_" . $i;
	// 			$_ragu 		= "rg_" . $i;
	// 			$jawaban_ujian = Jawaban_ujian_orm::where('ujian_id', $h_ujian->id)->where('soal_id', $input[$_tidsoal])->firstOrFail();

	// 			if (!empty($input[$_tjawab]))
	// 				$jawaban_ujian->jawaban = $input[$_tjawab];

	// 			$jawaban_ujian->status_jawaban = $input[$_ragu];

	// 			$jawaban_ujian->save();
	// 		}
	// 		commit_db_trx();
	// 		$action = true;
	// 		$this->_json(['status' => $action]);
	// 	} catch (Exception $e) {
	// 		rollback_db_trx();
	// 		show_error('Terjadi kesalahan saat menyimpan.', 500, 'Perhatian');
	// 	}
	// }

	protected function _simpan_jawaban_satu()
	{
		$key = $this->input->post('key', true);
		$one_time_token = $this->session->userdata('one_time_token');

		if (empty($one_time_token)) {
			show_404();
		}

		if ($one_time_token != $key) {
			show_error('Token salah', 500, 'Perhatian');
		}

		$sid = $this->input->post('sid', true);
		$answer = $this->input->post('answer', true);
		$ragu = $this->input->post('ragu', true);
		$waktu_buka_soal = $this->input->post('waktu_buka_soal', true);
		$waktu_jawab_soal = $this->input->post('waktu_jawab_soal', true);
		
		// Decrypt Id
		$id = $this->input->post('id', true);
		$id = integer_read_from_uuid($id);

		$h_ujian = Hujian_orm::findOrFail($id);
		if ($h_ujian->ujian_selesai == 'Y') {
			show_error('Ujian sudah diakhiri.', 500, 'Perhatian');
		}

		$jawaban_ujian = Jawaban_ujian_orm::where('ujian_id', $h_ujian->id)->where('soal_id', $sid)->firstOrFail();
		$jawaban_ujian->jawaban = $answer;
		$jawaban_ujian->status_jawaban = $ragu;
		$jawaban_ujian->waktu_buka_soal = $waktu_buka_soal == 'null' ? null : $waktu_buka_soal;
		$jawaban_ujian->waktu_jawab_soal = $waktu_jawab_soal;

		$action = $jawaban_ujian->save();
		$this->_json(['status' => $action]);
	}

	//	protected function _simpan_akhir()
	//	{
	//
	//		// Decrypt Id
	//		$id_tes = $this->input->post('id', true);
	//		$id_tes = $this->encryption->decrypt($id_tes);
	//
	//		// Get Jawaban
	//		$list_jawaban = $this->ujian->getJawaban($id_tes);
	//
	//		// Pecah Jawaban
	//		$pc_jawaban = explode(",", $list_jawaban);
	//
	//		$jumlah_benar 	= 0;
	//		$jumlah_salah 	= 0;
	//		$jumlah_ragu  	= 0;
	//		$nilai_bobot 	= 0;
	//		$total_bobot	= 0;
	//		$jumlah_soal	= sizeof($pc_jawaban);
	//
	//		foreach ($pc_jawaban as $jwb) {
	//			$pc_dt 		= explode(":", $jwb);
	//			$id_soal 	= $pc_dt[0];
	//			$jawaban 	= $pc_dt[1];
	//			$ragu 		= $pc_dt[2];
	//
	//			$cek_jwb 	= $this->soal->getSoalById($id_soal);
	//			$total_bobot = $total_bobot + $cek_jwb->bobot;
	//
	//			$jawaban == $cek_jwb->jawaban ? $jumlah_benar++ : $jumlah_salah++;
	//		}
	//
	//		$nilai = ($jumlah_benar / $jumlah_soal)  * 100;
	//		$nilai_bobot = ($total_bobot / $jumlah_soal)  * 100;
	//
	//		$d_update = [
	//			'jml_benar'		=> $jumlah_benar,
	//			'nilai'			=> number_format(floor($nilai), 0),
	//			'nilai_bobot'	=> number_format(floor($nilai_bobot), 0),
	//			'status'		=> 'N'
	//		];
	//
	//		$this->master->update('h_ujian', $d_update, 'id', $id_tes);
	//		$this->_json(['status'=>TRUE, 'data'=>$d_update, 'id'=>$id_tes]);
	//	}

	protected function _close_ujian()
	{

		$key = $this->input->post('key', true);
		$one_time_token = $this->session->userdata('one_time_token');

		$allow = true;
		if (empty($one_time_token)) {
			$allow = false;
		}

		if ($one_time_token != $key) {
			$allow = false;
		}

		$this->session->unset_userdata('one_time_token');

		if (!$allow) {
			show_error('Token akhiri salah.', 500, 'Perhatian');
		} else {
			$ended_by = $this->input->post('ended_by', true);
			// Decrypt Id
			$id = $this->input->post('id', true);
			$id_h_ujian = integer_read_from_uuid($id);

			$action = $this->_akhiri_ujian($id_h_ujian, $ended_by);

			$message_rootpage = [
				'header' => 'Perhatian',
				'content' => 'Ujian telah selesai.',
				'type' => 'success'
			];
			$this->session->set_flashdata('message_rootpage', $message_rootpage);
			$this->_json(['status' => $action]);
		}
	}

	protected function _force_close_ujian()
	{
		// LOGIC UNTUK KICK PESERTA UJIAN
		if (
			!$this->ion_auth->in_group('admin')
			&& !$this->ion_auth->in_group('pengawas')
		) {
			show_404();
		}

		$mhs_ujian_id = $this->input->post('id', true);
		$ended_by = $this->input->post('ended_by', true);
		$h_ujian = Hujian_orm::where('mahasiswa_ujian_id', $mhs_ujian_id)->firstOrFail();

		$action = $this->_akhiri_ujian($h_ujian->id, $ended_by);

		$data = [
			'status' => $action,
		];
		$this->_json($data);
	}

	private function _akhiri_ujian($id_h_ujian, $ended_by)
	{
		$h_ujian = Hujian_orm::findOrFail($id_h_ujian);
		if ($h_ujian->ujian_selesai == 'Y') {
			// JIKA UJIAN SUDAH PERNAH DIAKHIRI
			show_error('Ujian sudah diakhiri.', 500, 'Perhatian');
		}

		// Get Jawaban
		// $list_jawaban = $this->ujian->getJawaban($id_h_ujian);

		// Pecah Jawaban
		$pc_jawaban = $h_ujian->jawaban_ujian;

		$jumlah_benar = 0;
		$jumlah_salah = 0;
		//			$jumlah_ragu  = 0;
		//			$nilai_bobot  = 0;
		$total_bobot  = 0;
		$total_bobot_benar  = 0;
		$jumlah_soal  = count($pc_jawaban);

		$topik_ujian_nilai_bobot = [];

		foreach ($pc_jawaban as $jwb) {
			if (!isset($topik_ujian_nilai_bobot[$jwb->soal->topik_id])) {
				$topik_ujian_nilai_bobot[$jwb->soal->topik_id] = 0;
			}
			$total_bobot = $total_bobot + ($jwb->soal->bobot_soal->nilai * $jwb->soal->topik->poin_topik);
			if ($jwb->jawaban == $jwb->soal->jawaban) {
				$jumlah_benar++;
				$bobot_poin = ($jwb->soal->bobot_soal->nilai * $jwb->soal->topik->poin_topik);
				$total_bobot_benar = $total_bobot_benar + $bobot_poin;
				$topik_ujian_nilai_bobot[$jwb->soal->topik_id] = $topik_ujian_nilai_bobot[$jwb->soal->topik_id] + $bobot_poin;
			} else {
				$jumlah_salah++;
			}
		}

		$nilai       = ($jumlah_benar / $jumlah_soal) * 100;
		$nilai_bobot_benar = $total_bobot_benar;
		//			$total_bobot_benar = $total_bobot;
		$nilai_bobot = ($total_bobot / $jumlah_soal) * 100;

		$h_ujian->jml_benar =  $jumlah_benar;
		$h_ujian->jml_salah =  $jumlah_salah;
		$h_ujian->jml_soal =  $jumlah_soal;
		//			$h_ujian->nilai     =  number_format(floor($nilai), 0);
		$h_ujian->nilai     =  round($nilai, 2);
		//		$h_ujian->nilai_bobot = $nilai_bobot;
		$h_ujian->nilai_bobot = 0;
		$h_ujian->nilai_bobot_benar     =  round($nilai_bobot_benar, 2);
		$h_ujian->total_bobot     =  round($total_bobot, 2);
		$h_ujian->detail_bobot_benar     =  json_encode($topik_ujian_nilai_bobot);

		$stop_ujian = date('Y-m-d H:i:s');
		$stop_ujian_max = date('Y-m-d H:i:s', strtotime($h_ujian->tgl_mulai . '+' . $h_ujian->m_ujian->waktu . ' minutes')) ;
		$stop_ujian 	= $stop_ujian > $stop_ujian_max ? $stop_ujian_max : $stop_ujian;

		$date_end = date('Y-m-d H:i:s', strtotime($h_ujian->m_ujian->terlambat));
		$waktu_selesai 	= $stop_ujian > $date_end ? $date_end : $stop_ujian;

		$h_ujian->tgl_selesai =  $waktu_selesai;
		$h_ujian->ujian_selesai    =  'Y';
		$h_ujian->ended_by =  $ended_by;
		$action = $h_ujian->save();

		return $action;
	}

	public function monitor($id_ujian)
	{

		if (!(in_group('admin') || in_group('pengawas'))) show_404();

		$m_ujian = Mujian_orm::findOrFail($id_ujian);
		if (
			!$this->ion_auth->in_group('admin')
			&& !$this->ion_auth->in_group('dosen')
			&& !$this->ion_auth->in_group('pengawas')
		) {
			show_404();
		}

		$data = [
			'judul'		=> 'Ujian',
			'subjudul'	=> 'Monitor Ujian',
		];

		$data['m_ujian'] = $m_ujian;

		$jml_daftar_hadir = 0;
		$jml_daftar_hadir_by_pengawas = 0;
		if (in_group('pengawas')) {
			$users_groups = Users_groups_orm::where([
				'user_id'  => get_logged_user()->id,
				'group_id' => PENGAWAS_GROUP_ID
			])->firstOrFail();

			$jml_daftar_hadir_by_pengawas = Mhs_ujian_orm::where('ujian_id', $m_ujian->id_ujian)
				->whereHas('daftar_hadir', function (Builder $query) use ($users_groups) {
					$query->where('absen_by', $users_groups->id);
				})
				->get()
				->count();
		}

		$jml_daftar_hadir = Mhs_ujian_orm::where('ujian_id', $m_ujian->id_ujian)
			->whereHas('daftar_hadir')
			->get()
			->count();

		$data['jml_daftar_hadir'] = $jml_daftar_hadir;
		$data['jml_daftar_hadir_by_pengawas'] = $jml_daftar_hadir_by_pengawas;

		view('ujian/monitor', $data);
	}

	protected function _data_daftar_hadir()
	{

		if (!(in_group('admin') || in_group('pengawas'))) show_404();

		$id = $this->input->post('id');
		$as = $this->input->post('as');

		$pengawas_id = 'ALL';

		if ($as == 'pengawas') {
			$user_id = $this->input->post('user_id');
			if ($user_id != 'ALL') {
				$users_groups    = Users_groups_orm::where([
					'user_id'  => $user_id,
					'group_id' => PENGAWAS_GROUP_ID
				])->firstOrFail();

				$pengawas_id = $users_groups->id;
			}
		}


		$m_ujian = Mujian_orm::findOrFail($id);
		$config = [
			'host'     => $this->db->hostname,
			'port'     => $this->db->port,
			'username' => $this->db->username,
			'password' => $this->db->password,
			'database' => $this->db->database,
		];

		$this->db->select('a.id, c.nim, c.nama, c.nik, c.jenis_kelamin, c.tgl_lahir, c.prodi, d.absen_by, "OFFLINE" AS koneksi, "0ms" AS latency, e.ujian_selesai AS status, "AKSI" AS aksi');
		$this->db->from('mahasiswa_ujian AS a');
		$this->db->join('mahasiswa_matkul AS b', 'a.mahasiswa_matkul_id = b.id');
		$this->db->join('mahasiswa AS c', 'b.mahasiswa_id = c.id_mahasiswa');
		$this->db->join('daftar_hadir AS d', 'a.id = d.mahasiswa_ujian_id', 'left');
		$this->db->join('h_ujian AS e', 'a.id = e.mahasiswa_ujian_id', 'left');
		$this->db->where(['a.ujian_id' => $m_ujian->id_ujian]);
		if ($as == 'pengawas') {
			if ($pengawas_id != 'ALL')
				$this->db->where('d.absen_by', $pengawas_id);
			else
				$this->db->where('d.absen_by IS NOT NULL', NULL, FALSE);
		}
		$this->db->group_by('a.id');
		$this->db->order_by('c.nim');

		$dt = new Datatables(new MySQL($config));

		$query = $this->db->get_compiled_select(); // GET QUERY PRODUCED BY ACTIVE RECORD WITHOUT RUNNING I

		$dt->query($query);

		$dt->edit('absen_by', function ($data) {
			//	            return number_format($data['nilai_bobot_benar'] / 3,2,'.', '') ;
			$return = '<span class="badge border-danger danger round badge-border" id="badge_absensi_' . $data['nim'] . '">BELUM</span>';
			if (!empty($data['absen_by'])) {
				$return = '<span class="badge border-success success round badge-border" id="badge_absensi_' . $data['nim'] . '">SUDAH</span>';
			}
			return  $return;
		});

		$dt->edit('aksi', function ($data) {
			return '<div class="btn-group">
						<button type="button" title="hentikan ujian peserta" class="btn btn-sm btn-danger btn_kick" data-id="' . $data['id'] . '" data-nim="' . $data['nim'] . '"><i class="fa fa-user-times"></i></button>
						<button type="button" title="lihat foto peserta" class="btn btn-sm btn-info btn_foto" data-id="' . $data['id'] . '" data-nim="' . $data['nim'] . '"><i class="fa fa-camera"></i></button>
						</div>';
		});

		$dt->edit('koneksi', function ($data) {
			return '<span class="badge bg-danger" id="badge_koneksi_' . $data['nim'] . '">' . $data['koneksi'] . '</span>
					<span class="badge bg-info" id="badge_ip_' . $data['nim'] . '" style="display: none">-</span>';
		});

		$dt->edit('latency', function ($data) {
			return '<span class="badge bg-grey" id="badge_latency_' . $data['nim'] . '">' . $data['latency'] . '</span>';
		});

		$dt->edit('status', function ($data) {
			$status = '';
			$status_badge = '';

			if (empty($data['status'])) {
				$status = $data['status'] == null ? 'BELUM UJIAN' : 'SUDAH UJIAN';
				$status_badge = $data['status'] == null ? 'secondary' : 'success';
			} else {
				$status = $data['status'] == 'N' ? 'SEDANG UJIAN' : 'SUDAH UJIAN';
				$status_badge = $data['status'] == 'N' ? 'danger' : 'success';
				// $status = 'BELUM UJIAN' ;
				// $status_badge = 'secondary' ;
			}

			return '<span class="badge bg-' . $status_badge . '" id="badge_status_' . $data['nim'] . '">' . $status . '</span>
					<span class="badge bg-warning" id="badge_focus_' . $data['nim'] . '" style="display: none">BUKA PAGE LAIN</span>';
		});

		$dt->add('absensi', function ($data) {
			if (in_group('admin')) {
				return '<button type="button" class="btn btn-sm btn-success btn_open" data-id="' . $data['id'] . '" data-nim="' . $data['nim'] . '"><i class="fa fa-folder-open"></i> Lihat</button>';
			} else if (in_group('pengawas')) {
				return '<div class="btn-group">
							<button type="button" title="isi absen" class="btn btn-sm btn-info btn_absensi" data-id="' . $data['id'] . '" data-nim="' . $data['nim'] . '"><i class="fa fa-check"></i></button>
							<button type="button" title="batal absen" class="btn btn-sm btn-danger btn_absensi_batal" data-id="' . $data['id'] . '" data-nim="' . $data['nim'] . '"><i class="fa fa-times"></i></button>
							<button type="button" title="check absen" class="btn btn-sm btn-secondary btn_absensi_check" data-id="' . $data['id'] . '" data-nim="' . $data['nim'] . '"><i class="fa fa-question"></i></button>
							</div>';
			} else {
				return '-';
			}
		});

		$this->_json($dt->generate(), false);
	}

	protected function _check_pengabsen()
	{
		$mahasiswa_ujian_id = $this->input->post('mahasiswa_ujian_id');

		$daftar_hadir = Daftar_hadir_orm::where([
			'mahasiswa_ujian_id' => $mahasiswa_ujian_id,
		])->first();

		$data['nama_pengabsen'] = null;
		if (!empty($daftar_hadir)) {
			$data['nama_pengabsen'] = $daftar_hadir->pengawas->users->full_name;
		}

		$this->_json($data);
	}

	protected function _get_foto_url()
	{
		$nim = $this->input->post('nim');
		$mhs = Mhs_orm::where('nim', $nim)->firstOrFail();
		$this->_json(['src_img' => $mhs->foto]);
	}

	protected function _absen_pengawas()
	{
		$this->_akses_pengawas();

		$user_id = get_logged_user()->id;
		$mahasiswa_ujian_id = $this->input->post('mahasiswa_ujian_id');
		$aksi = $this->input->post('aksi');

		$users_groups = Users_groups_orm::where([
			'user_id'  => $user_id,
			'group_id' => PENGAWAS_GROUP_ID
		])->firstOrFail();

		$ok = false;
		if ($aksi == 'batal') {
			// JIKA MEMBATALKAN ABSEN
			$daftar_hadir = Daftar_hadir_orm::where([
				'mahasiswa_ujian_id' => $mahasiswa_ujian_id,
				'absen_by'           => $users_groups->id,
			])->first();


			if (!empty($daftar_hadir)) {
				$ok = $daftar_hadir->delete();
			}
		} else {
			// JIKA MENGISI ABSEN
			$daftar_hadir = Daftar_hadir_orm::where([
				'mahasiswa_ujian_id' => $mahasiswa_ujian_id,
			])->first();

			if (empty($daftar_hadir)) {
				$daftar_hadir                     = new Daftar_hadir_orm();
				$daftar_hadir->mahasiswa_ujian_id = $mahasiswa_ujian_id;
				$daftar_hadir->absen_by           = $users_groups->id;
				$ok                               = $daftar_hadir->save();
			}
		}

		$this->_json(['ok' => $ok]);
	}

	protected function _prepare_ujian_ulang(){

		$this->_akses_mahasiswa();

		$h_ujian_id_uuid = $this->input->post('id');
		$h_ujian_id = integer_read_from_uuid($h_ujian_id_uuid);
		$h_ujian = Hujian_orm::findOrFail($h_ujian_id);

		if($h_ujian->ujian_selesai != 'Y'){ // JIKA UJIAN BELUM SELESAI
			show_404();
		}

		if(APP_TYPE == 'tryout'){
			$user = $this->ion_auth->user()->row();
			$mhs = Mhs_orm::where('nim', $user->username)->firstOrFail();
			// $mhs_aktif_membership = get_mhs_aktif_membership($h_ujian->mhs);

			// if($mhs_aktif_membership->membership->is_limit_by_kuota){
			// 	if(empty($mhs_aktif_membership->sisa_kuota_latihan_soal)){
			// 		$this->_json(['status' => 'ko', 'msg' => 'Sisa kuota latihan soal anda sudah habis']);
			// 		return ;
			// 	}
			// }

			// if($mhs_aktif_membership->membership->is_limit_by_durasi){
			// 	$expired_at = new Carbon($mhs_aktif_membership->expired_at);
			// 	$today = Carbon::now();
			// 	if($today->greaterThan($expired_at)){
			// 		$this->_json(['status' => 'ko', 'msg' => 'Membership anda sudah expired']);
			// 		return ;
			// 	}
			// }

			if(is_mhs_limit_by_kuota()){
				if($mhs->mhs_matkul()
					->where('matkul_id', $h_ujian->m_ujian->matkul_id)
					->first()
					->sisa_kuota_latihan_soal <= 0){
						$this->_json(['status' => 'ko', 'msg' => 'Membership anda sudah expired / kuota latihan sudah habis']);
						return ;
				}
			}
		}

		$today = date('Y-m-d H:i:s');
		//echo $paymentDate; // echos today!
		$date_start = date('Y-m-d H:i:s', strtotime($h_ujian->m_ujian->tgl_mulai));
		$date_end = date('Y-m-d H:i:s', strtotime($h_ujian->m_ujian->terlambat));

		if (!(($today >= $date_start) && ($today < $date_end))) { // JIKA BUKAN MASA UJIAN
			show_404(); 
		}

		if(!$h_ujian->m_ujian->repeatable){ // JIKA TIDAK REPEATABLE
			show_404();
		}

		if(empty($h_ujian->jawaban_ujian()->count())){ // JIKA BELUM PERNAH UJIAN SEBELUMNYA
			show_404();
		}

		$ujian_ke = Hujian_history_orm::where('mahasiswa_ujian_id', $h_ujian->mahasiswa_ujian_id)->max('ujian_ke');

		try{
			begin_db_trx();
			/** INSERT JAWABAN UJIAN SEBELUMNYA KE HISTORY */
			$h_ujian_history = new Hujian_history_orm();
			$h_ujian_history->ujian_id = $h_ujian->ujian_id; 
			$h_ujian_history->mahasiswa_id = $h_ujian->mahasiswa_id; 
			$h_ujian_history->mahasiswa_ujian_id = $h_ujian->mahasiswa_ujian_id; 
			$h_ujian_history->list_soal = $h_ujian->list_soal; 
			$h_ujian_history->list_jawaban = $h_ujian->list_jawaban; 
			$h_ujian_history->jml_soal = $h_ujian->jml_soal;
			$h_ujian_history->jml_benar = $h_ujian->jml_benar;
			$h_ujian_history->jml_salah = $h_ujian->jml_salah;
			$h_ujian_history->nilai = 	 $h_ujian->nilai ;	
			$h_ujian_history->nilai_bobot = $h_ujian->nilai_bobot;
			$h_ujian_history->nilai_bobot_benar = $h_ujian->nilai_bobot_benar;
			$h_ujian_history->total_bobot = $h_ujian->total_bobot; 
			$h_ujian_history->detail_bobot_benar = $h_ujian->detail_bobot_benar; 
			$h_ujian_history->tgl_mulai = $h_ujian->tgl_mulai;
			$h_ujian_history->tgl_selesai = $h_ujian->tgl_selesai;
			$h_ujian_history->ended_by = $h_ujian->ended_by;
			$h_ujian_history->ujian_ke = empty($ujian_ke) ? 1 : ($ujian_ke + 1) ;

			$h_ujian_all = Hujian_orm::select('*', DB::raw('TIMESTAMPDIFF(SECOND, tgl_mulai, tgl_selesai) AS lama_pengerjaan'))
                                                        ->where(['ujian_id' =>  $h_ujian->ujian_id])
                                                        ->orderBy('nilai_bobot_benar', 'desc')
                                                        ->orderBy('lama_pengerjaan', 'asc')
                                                        ->get();
                                    
			$jml_peserta = $h_ujian_all->count();
			
			$peringkat = 1;
			foreach($h_ujian_all as $ujian){
				if($ujian->mahasiswa_id == $h_ujian->mahasiswa_id){
					break;
				}
				$peringkat++;
			}

			$h_ujian_history->peringkat = $peringkat;
			$h_ujian_history->jml_peserta = $jml_peserta;

			$h_ujian_history->save();     

			foreach($h_ujian->jawaban_ujian as $jawaban_ujian){


				$jawaban_ujian_history = new Jawaban_ujian_history_orm();
				$jawaban_ujian_history->ujian_id = $h_ujian_history->id;
				$jawaban_ujian_history->soal_id = $jawaban_ujian->soal_id;
				$jawaban_ujian_history->jawaban = $jawaban_ujian->jawaban;
				$jawaban_ujian_history->status_jawaban = $jawaban_ujian->status_jawaban;
				$jawaban_ujian_history->waktu_buka_soal = $jawaban_ujian->waktu_buka_soal;
				$jawaban_ujian_history->waktu_jawab_soal = $jawaban_ujian->waktu_jawab_soal;
				$jawaban_ujian_history->save();

			}

			/** HAPUS JAWABAN SEBELUMNYA */
			$h_ujian->delete();

			commit_db_trx();

			$this->_json(['status' => 'ok']);

		}catch(Exception $e){
			rollback_db_trx();
			show_error($e->getMessage(), 500, 'Perhatian');
		}

	}

	public function tutorial(){
		$user = $this->ion_auth->user()->row();
		$data['user'] = $user;

		view('ujian/tutorial', $data);
	}

	protected function _get_urutan_topik(){
		$id_ujian = $this->input->post('id');
		$h_ujian = Hujian_orm::findOrFail($id_ujian);
		$result = [];
		$topik_waktu = [];
		$fixed_waktu = $h_ujian->tgl_selesai;
		if($h_ujian->m_ujian->is_sekuen_topik){
			if(!empty($h_ujian->m_ujian->urutan_topik)){
				$urutan_topik = $h_ujian->m_ujian->urutan_topik;
				$urutan_topik = json_decode($urutan_topik, true);
				uasort($urutan_topik, function($a, $b){
					return $a['urutan'] <=> $b['urutan'];
				});
				
				$date_akhir_topik = '';
				foreach($urutan_topik as $topik_id => $v){
					if(empty($date_akhir_topik))
						$date_akhir_topik = date('Y-m-d H:i:s', strtotime($h_ujian->tgl_mulai . '+' . $v['waktu'] . ' minutes')) ;
					else
						$date_akhir_topik = date('Y-m-d H:i:s', strtotime($date_akhir_topik . '+' . $v['waktu'] . ' minutes')) ;
					$topik_waktu[$topik_id] = $date_akhir_topik;
				}
				$result = [
					'topik_waktu' 	=> $topik_waktu,
					'fixed_waktu'	=> $fixed_waktu,
				];
			}
		}else{
			$result = [
				'topik_waktu' => $topik_waktu,
				'fixed_waktu' => $fixed_waktu,
			];
		}

		$this->_json($result);
		
	}

	//	function c(){
	//		$mhs_ujian = Mhs_ujian_orm::where(['ujian_id' => 50])->first();
	////		    echo '1.' . count($daftar_hadir);
	////	    vdebug($mhs_ujian->isNotEmpty());
	//	    vdebug(empty($mhs_ujian));
	//        if(empty($daftar_hadir)) {
	////	            echo '2.' . count($daftar_hadir);
	//        }
	//	}


}