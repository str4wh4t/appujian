<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Orm\Soal_orm;
use Orm\Matkul_orm;
use Orm\Bobot_soal_orm;
use Orm\Topik_orm;
use Orm\Users_orm;
use Orm\Bundle_orm;
use Orm\Bundle_soal_orm;
use Orm\Section_orm;
use Illuminate\Database\Eloquent\Builder;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\MySQL;
use Carbon\Carbon;

class Soal extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		if (!$this->ion_auth->logged_in()) {
			redirect('auth');
		} else if (!$this->ion_auth->is_admin() && 
					!$this->ion_auth->in_group('dosen') && 
					!$this->ion_auth->in_group('penyusun_soal')) {
			show_error('Hanya Administrator dan dosen yang diberi hak untuk mengakses halaman ini', 403, 'Akses Terlarang');
		}
		$this->load->library(['datatables', 'form_validation']); // Load Library Ignited-Datatables
		$this->load->helper('my'); // Load Library Ignited-Datatables
		$this->load->model('Master_model', 'master');
		$this->load->model('Soal_model', 'soal');
		$this->form_validation->set_error_delimiters('', '');
	}

	public function index()
	{
		$user = $this->ion_auth->user()->row();
		$data = [
			'judul'	=> 'Soal',
			'subjudul' => 'List Soal'
		];

		if ($this->ion_auth->is_admin() || $this->ion_auth->in_group('penyusun_soal')) {
			//Jika admin maka tampilkan semua matkul
			$data['matkul'] = Matkul_orm::all();
			$data['topik'] = Topik_orm::all();
		} else {
			//Jika bukan maka matkul dipilih otomatis sesuai matkul dosen
			$dosen = Orm\Dosen_orm::where('nip', $user->username)->first();
			$matkul = [] ;
			if($dosen->matkul->isNotEmpty()){
				$matkul = $dosen->matkul;
			}

			$matkul_ids = $matkul->pluck('id_matkul')->toArray();

			$data['topik'] = Topik_orm::whereIn('matkul_id', $matkul_ids)->get();
			$data['matkul'] = $matkul;
		}

		$data['bobot_soal'] = Bobot_soal_orm::All();
		$data['gel'] = Soal_orm::distinct()->pluck('gel')->toArray();
		$data['smt'] = Soal_orm::distinct()->pluck('smt')->toArray();
		$data['tahun'] = Soal_orm::distinct()->pluck('tahun')->toArray();

		$data['bundle_avail'] = Bundle_orm::all();
		$data['bundle_selected'] = $data['bundle_selected'] ?? [];

		//		$this->load->view('_templates/dashboard/_header.php', $data);
		//		$this->load->view('soal/data');
		//		$this->load->view('_templates/dashboard/_footer.php');
		view('soal/index', $data);
	}

	public function detail($id)
	{
		//        $user = $this->ion_auth->user()->row();

		$soal_orm = Soal_orm::findOrFail($id);
		$user = $this->ion_auth->user()->row();

		$where_add = [];
		if (!$this->ion_auth->is_admin()) {
			if ($soal_orm->created_by != $user->username) {
				$message_rootpage = [
					'header' => 'Perhatian',
					'content' => 'Anda bukan pembuat soal.',
					'type' => 'warning'
				];
				$this->session->set_flashdata('message_rootpage', $message_rootpage);
				redirect('soal/index', 'refresh');
			}
			$where_add['created_by'] = $soal_orm->created_by;
		}

		$maker = Users_orm::where('username', $soal_orm->created_by)->firstOrFail();

		// $prev = Soal_orm::where('id_soal', '<', $soal_orm->id_soal)
		// 						->where('topik_id', $soal_orm->topik_id)
		// 						->where($where_add)
		// 						->max('id_soal');

		$prev = Soal_orm::where('no_urut', '<', $soal_orm->no_urut)
								->where('topik_id', $soal_orm->topik_id)
								->where($where_add)
								->max('id_soal');

		// get next user id
		// $next = Soal_orm::where('id_soal', '>', $soal_orm->id_soal)
		// 					->where('topik_id', $soal_orm->topik_id)
		// 					->where($where_add)
		// 					->min('id_soal');

		$next = Soal_orm::where('no_urut', '>' , $soal_orm->no_urut)
								->where('topik_id', $soal_orm->topik_id)
								->where($where_add)
								->min('id_soal');

		$data = [
			//			'user'      => $user,
			'judul'	    => 'Soal',
			'subjudul'  => 'Detail Soal',
			'soal'      => $soal_orm,
			'prev'	=> $prev,
			'next'	=> $next,
			'user' => $maker,
		];

		// $this->load->view('_templates/dashboard/_header.php', $data);
		// $this->load->view('soal/detail');
		// $this->load->view('_templates/dashboard/_footer.php');

		view('soal/detail', $data);
	}

	public function add(array $data = [])
	{
		$user = $this->ion_auth->user()->row();

		$data['user']      = $user;
		$data['judul']	    = 'Soal';
		$data['subjudul']  = 'Add Soal';

		if ($this->ion_auth->in_group('dosen')) {
			//Jika dosen otomatis sesuai matkul dosen
			$data['matkul'] =  Orm\Dosen_orm::where('nip', $user->username)->firstOrFail()->matkul;
		} else {
			//Jika admin / penyusun_soal maka tampilkan semua matkul
			$data['matkul'] = $this->master->getAllMatkul();
			// $data['topik'] = $this->master->getAllTopik();
		}

		$data['bobot_soal'] = Bobot_soal_orm::All();

		$tahun_avail = [];
		$tahun_avail = Soal_orm::distinct()->pluck('tahun')->toArray();
		$tahun_avail[] = get_selected_tahun();
		$tahun_avail = array_unique($tahun_avail);
		$data['tahun_avail'] = $tahun_avail;

		$data['bundle_avail'] = Bundle_orm::all();
		$data['bundle_selected'] = $data['bundle_selected'] ?? [];

		$data['section_avail'] = Section_orm::all();

		//		$this->load->view('_templates/dashboard/_header.php', $data);
		//		$this->load->view('soal/add');
		//		$this->load->view('_templates/dashboard/_footer.php');
		view('soal/add', $data);
	}

	public function edit($id, array $data = [])
	{
		//		$user = $this->ion_auth->user()->row();
		$soal = Soal_orm::findOrFail($id);
		$user = $this->ion_auth->user()->row();

		if (!$this->ion_auth->is_admin()) {
			if ($soal->created_by != $user->username) {
				$message_rootpage = [
					'header' => 'Perhatian',
					'content' => 'Anda bukan pembuat soal.',
					'type' => 'warning'
				];
				$this->session->set_flashdata('message_rootpage', $message_rootpage);
				redirect('soal/index', 'refresh');
			}
		}

		$data['judul']	    = 'Soal';
		$data['subjudul']   = 'Edit Soal';
		$data['soal']       = $this->soal->getSoalById($id);

		if ($this->ion_auth->in_group('dosen')) {
			//Jika dosen maka matkul dipilih otomatis sesuai matkul dosen
			$data['matkul'] =  Orm\Dosen_orm::where('nip', $user->username)->firstOrFail()->matkul;
			$data['soal'] = $soal;
		} else {
			//Jika admin / penyusun_soal maka tampilkan semua matkul
			$data['matkul'] = $this->master->getAllMatkul();
			// $data['topik'] = $this->master->getAllTopik();
			$data['soal'] = $soal;
		}

		$data['bobot_soal'] = Bobot_soal_orm::All();
		
		$tahun_avail = [];
		$tahun_avail = Soal_orm::distinct()->pluck('tahun')->toArray();
		$tahun_avail[] = get_selected_tahun();
		$tahun_avail = array_unique($tahun_avail);
		$data['tahun_avail'] = $tahun_avail;

		$data['bundle_avail'] = Bundle_orm::all();
		$data['bundle_selected'] = $data['bundle_selected'] ?? $soal->bundle()->pluck('bundle.id')->toArray();

		$data['section_avail'] = Section_orm::all();

		//		$this->load->view('_templates/dashboard/_header.php', $data);
		//		$this->load->view('soal/edit');
		//		$this->load->view('_templates/dashboard/_footer.php');

		view('soal/edit', $data);
	}

	protected function _data()
	{
		$data['matkul_id'] = $this->input->get('matkul_id') == 'null' ? null : $this->input->get('matkul_id');
		$data['topik_id'] = $this->input->get('topik_id') == 'null' ? null : $this->input->get('topik_id');
		$data['gel'] = $this->input->get('gel') == 'null' ? null : $this->input->get('gel');
		$data['smt'] = $this->input->get('smt') == 'null' ? null : $this->input->get('smt');
		$data['tahun'] = $this->input->get('tahun') == 'null' ? null : $this->input->get('tahun');
		$data['bundle'] = $this->input->get('bundle') == 'null' ? null : $this->input->get('bundle');

		$username = null;
		if ($this->ion_auth->in_group('dosen')) {
			$username = $this->ion_auth->user()->row()->username;
		}
		$this->_json($this->soal->getDataSoal($data, $username), false);
	}

	private function _validasi()
	{
		$this->form_validation->set_rules('matkul_id', 'Matkul', 'required');
		$this->form_validation->set_rules('topik_id', 'Topik', 'required');
		$this->form_validation->set_rules('soal', 'Soal', 'required|trim');
		$this->form_validation->set_rules('jawaban_a', 'Jawaban A', 'required|trim');
		$this->form_validation->set_rules('jawaban_b', 'Jawaban B', 'required|trim');
		$this->form_validation->set_rules('jawaban_c', 'Jawaban C', 'required|trim');
		$this->form_validation->set_rules('jawaban_d', 'Jawaban D', 'required|trim');
		$this->form_validation->set_rules('jawaban_e', 'Jawaban E', 'required|trim');
		$this->form_validation->set_rules('jawaban', 'Kunci Jawaban', 'required');
		$this->form_validation->set_rules('bobot_soal_id', 'Bobot Soal', 'required|is_natural_no_zero');
		$this->form_validation->set_rules('penjelasan', 'Penjelasan', 'trim');
		//        $this->form_validation->set_rules('bobot', 'Bobot Soal', 'required|is_natural_no_zero|max_length[2]');
		$this->form_validation->set_rules(
			'gel',
			'Gel',
			[
				'required',
				[
					'is_valid_gel',
					function ($gel) {
						return in_array($gel, GEL_AVAIL);
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
						return in_array($smt, SMT_AVAIL);
					}
				]
			],
			[
				'is_valid_smt' => 'Smt yg dimasukan salah',
			]
		);

		$tahun_avail = [];
		$tahun_avail = Soal_orm::distinct()->pluck('tahun')->toArray();
		$tahun_avail[] = get_selected_tahun();
		$tahun_avail = array_unique($tahun_avail);
		
		$this->form_validation->set_rules(
			'tahun',
			'Tahun',
			[
				'required',
				[
					'is_valid_tahun',
					function ($tahun) use($tahun_avail) {
						return in_array($tahun, $tahun_avail);
					}
				]
			],
			[
				'is_valid_tahun' => 'Tahun yg dimasukan salah',
			]
		);

		$bundle_avail = Bundle_orm::pluck('id')->toArray();
		$bundle_list = $this->input->post('bundle[]');
		
		$this->form_validation->set_rules(
			'bundle[]',
			'Bundle',
			[
				[
					'is_valid_bundle',
					function () use($bundle_list, $bundle_avail) {
						if(!empty($bundle_list)){
							$bundle_valid = array_intersect($bundle_list, $bundle_avail);
							return count($bundle_list) == count($bundle_valid);
						}else{
							return true;
						}
					}
				]
			],
			[
				'is_valid_bundle' => 'Bundle yg dimasukan salah',
			]
		);

	}

	private function _file_config()
	{
		$allowed_type 	= [
			"image/jpeg", "image/jpg", "image/png", "image/gif",
			"audio/mpeg", "audio/mpg", "audio/mpeg3", "audio/mp3", "audio/x-wav", "audio/wave", "audio/wav",
			"video/mp4", "application/octet-stream"
		];
		$config['upload_path']      = FCPATH . 'uploads/bank_soal/';
		$config['allowed_types']    = 'jpeg|jpg|png|gif|mpeg|mpg|mpeg3|mp3|wav|wave|mp4';
		$config['encrypt_name']     = true;

		return $this->load->library('upload', $config);
	}

	/*
     * UNTUK METHOD SAVE PADA CLASS INI TIDAK DI DECLARE PROTECTED TETAPI TETAP PUBLIC KRN TIDAK DIPANGGIL VIA AJAX
     */
	public function save()
	{

		if (!$this->input->post())
			redirect('soal');

		//    	vdebug($this->input->post());

		$aksi = $this->input->post('aksi', true);
		$id_soal = $this->input->post('id_soal', true);
		$this->_validasi();
		// $this->_file_config();

		if ($this->form_validation->run() === false) {
			// VALIDASI SALAH
			$bundle_selected = empty($this->input->post('bundle[]')) ? [] : $this->input->post('bundle[]');
			$stts = 'ko' ;
			$data = [
				'bundle_selected' => $bundle_selected,
				'stts' => $stts,
			];
			$aksi === 'add' ? $this->add($data) : $this->edit($id_soal, $data);
		} else {
			// VALIDASI BENAR
			$data = [
				'soal'      => $this->input->post('soal'),
				'jawaban'   => $this->input->post('jawaban', true),
				// 'bobot'     => $this->input->post('bobot', true),
				'bobot_soal_id'     => $this->input->post('bobot_soal_id', true),
				'gel'     => $this->input->post('gel', true),
				'smt'     => $this->input->post('smt', true),
				'tahun'     => $this->input->post('tahun', true),
				'penjelasan'     => $this->input->post('penjelasan'),
				'bundle'     => $this->input->post('bundle[]'),
				'section_id'     => $this->input->post('section_id', true),
			];

			// vdebug($data['penjelasan']);

			// $abjad = OPSI_SOAL;

			// Inputan Opsi
			// foreach (OPSI_SOAL as $abj) {
			// 	$data['opsi_' . $abj]    = $this->input->post('jawaban_' . $abj);
			// }

			//            $i = 0;
			//            foreach ($_FILES as $key => $val) {
			//                $img_src = FCPATH.'uploads/bank_soal/';
			//	            $getsoal = $this->soal->getSoalById($id_soal);
			//
			//                $error = '';
			//                if($key === 'file_soal'){
			//                    if(!empty($_FILES['file_soal']['name'])){
			//                        if (!$this->upload->do_upload('file_soal')){
			//                            $error = $this->upload->display_errors();
			//                            show_error($error, 500, 'File Soal Error');
			//                            exit();
			//                        }else{
			//                            if($aksi === 'edit'){
			//                                if(!unlink($img_src.$getsoal->file)){
			//                                    show_error('Error saat delete gambar <br/>'.var_dump($getsoal), 500, 'Error Edit Gambar');
			//                                    exit();
			//                                }
			//                            }
			//                            $data['file'] = $this->upload->data('file_name');
			//                            $data['tipe_file'] = $this->upload->data('file_type');
			//                        }
			//                    }
			//                }else{
			//                    $file_abj = 'file_'.$abjad[$i];
			//                    if(!empty($_FILES[$file_abj]['name'])){
			//                        if (!$this->upload->do_upload($key)){
			//                            $error = $this->upload->display_errors();
			//                            show_error($error, 500, 'File Opsi '.strtoupper($abjad[$i]).' Error');
			//                            exit();
			//                        }else{
			//                            if($aksi === 'edit'){
			//                                if(!unlink($img_src.$getsoal->$file_abj)){
			//                                    show_error('Error saat delete gambar', 500, 'Error Edit Gambar');
			//                                    exit();
			//                                }
			//                            }
			//                            $data[$file_abj] = $this->upload->data('file_name');
			//                        }
			//                    }
			//                    $i++;
			//                }
			//            }

			//            if($this->ion_auth->is_admin()){
			//                $pecah = $this->input->post('dosen_id', true);
			//                $pecah = explode(':', $pecah);
			//                $data['dosen_id'] = $pecah[0];
			//                $data['matkul_id'] = end($pecah);
			//            }else{
			//                $data['dosen_id'] = $this->input->post('dosen_id', true);
			//                $data['matkul_id'] = $this->input->post('matkul_id', true);
			//            }

			$data['topik_id'] = $this->input->post('topik_id', true);
			//            $data['matkul_id'] = $this->input->post('matkul_id', true);
			
			$ok = true ;
			$error = null ;

			// vdebug($data);
			if ($aksi === 'add') {
				// push array
				// $data['created_at'] = date('Y-m-d H:i:s');
				// $data['created_by']   = $this->ion_auth->user()->row()->username;
				// insert data
				// $this->master->create('tb_soal', $data);
				try{
					begin_db_trx();

					$no_urut = 0 ;
					$soal_before = Soal_orm::where('topik_id', $data['topik_id'])
										->orderBy('created_at', 'desc')
										->first();

					if(empty($soal_before))
						$no_urut = 1;
					else{
						if(empty($soal_before->no_urut))
							$no_urut = 1;
						else
							$no_urut = ($soal_before->no_urut) + 1;
					}

					$soal = new Soal_orm();
					// $soal->soal = $data['soal'];
					$soal->jawaban = $data['jawaban'];
					$soal->bobot_soal_id = $data['bobot_soal_id'];
					$soal->gel = $data['gel'];
					$soal->smt = $data['smt'];
					$soal->tahun = $data['tahun'];
					// foreach (OPSI_SOAL as $abj) {
						// $data['opsi_' . $abj]    = $this->input->post('jawaban_' . $abj);
						// $opsi = 'opsi_' . $abj ;
						// $soal->$opsi = $data[$opsi];
					// }
					$soal->topik_id = $data['topik_id'];
					// $soal->penjelasan = $data['penjelasan'];
					$soal->created_by = $this->ion_auth->user()->row()->username;
					$soal->no_urut = $no_urut ;
					$soal->section_id = empty($data['section_id']) ? null : $data['section_id'];
					$soal->save();

					$soal_temp = Soal_orm::findOrFail($soal->id_soal);

					$html = $data['soal'];
					// $doc = new DOMDocument('1.0', 'UTF-8');
					// $doc->loadHTML($html);
					$doc = new DOMDocument();
					$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
					$i = 0 ;
					foreach ($doc->getElementsByTagName('img') as $img_node) {
						$src = $img_node->getAttribute('src') ;
						if(strpos($src, 'data:image/png;base64,') !== false){
							$img = str_replace('data:image/png;base64,', '', $src);
							$img = str_replace(' ', '+', $img);
							$img_64 = base64_decode($img);
							$file_name = $soal_temp->id_soal .'_soal_'. mt_rand()  .'.png';
							$file = UPLOAD_DIR . 'img_soal/' . $file_name;
							$success = file_put_contents($file, $img_64);
							if($success){
								$img_node->setAttribute('src', asset('uploads/img_soal/' . $file_name)) ;
								$doc->saveHTML($img_node);
							}
							$i++;
						}
					}
					
					$xpath = new DOMXPath($doc);

					$body = '';
					foreach ($xpath->evaluate('//body/node()') as $node) {
						$body .= $doc->saveHtml($node);
					}

					$soal_temp->soal = $body;

					foreach (OPSI_SOAL as $abj) {
						$opsi = 'opsi_' . $abj ;
						// $html = $soal_temp->$opsi;
						$html = $this->input->post('jawaban_' . $abj);
						// $doc = new DOMDocument('1.0', 'UTF-8');
						// $doc->loadHTML($html);
						$doc = new DOMDocument();
						$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
						$i = 0 ;
						foreach ($doc->getElementsByTagName('img') as $img_node) {
							$src = $img_node->getAttribute('src') ;
							if(strpos($src, 'data:image/png;base64,') !== false){
								$img = str_replace('data:image/png;base64,', '', $src);
								$img = str_replace(' ', '+', $img);
								$img_64 = base64_decode($img);
								$file_name = $soal_temp->id_soal .'_jawaban_'. $opsi .'_'. mt_rand()  .'.png';
								$file = UPLOAD_DIR . 'img_soal/' . $file_name;
								$success = file_put_contents($file, $img_64);
								if($success){
									$img_node->setAttribute('src', asset('uploads/img_soal/' . $file_name)) ;
									$doc->saveHTML($img_node);
								}
								$i++;
							}
						}
						
						$xpath = new DOMXPath($doc);

						$body = '';
						foreach ($xpath->evaluate('//body/node()') as $node) {
							$body .= $doc->saveHtml($node);
						}

						$soal_temp->$opsi = $body;

					}

					/////////////

					$html = $data['penjelasan'];
					if(!empty($html)){
						// $doc = new DOMDocument('1.0', 'UTF-8');
						// $doc->loadHTML($html);
						$doc = new DOMDocument();
						$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
						$i = 0 ;
						foreach ($doc->getElementsByTagName('img') as $img_node) {
							$src = $img_node->getAttribute('src') ;
							if(strpos($src, 'data:image/png;base64,') !== false){
								$img = str_replace('data:image/png;base64,', '', $src);
								$img = str_replace(' ', '+', $img);
								$img_64 = base64_decode($img);
								$file_name = $soal_temp->id_soal .'_penjelasan_'. mt_rand()  .'.png';
								$file = UPLOAD_DIR . 'img_soal/' . $file_name;
								$success = file_put_contents($file, $img_64);
								if($success){
									$img_node->setAttribute('src', asset('uploads/img_soal/' . $file_name)) ;
									$doc->saveHTML($img_node);
								}
								$i++;
							}
						}
						
						$xpath = new DOMXPath($doc);

						$body = '';
						foreach ($xpath->evaluate('//body/node()') as $node) {
							$body .= $doc->saveHtml($node);
						}

						$soal_temp->penjelasan = $body;
					}

					/////////////
					
					$soal_temp->save();

					$id_soal = $soal->id_soal ;

					//[START] SAVE BUNDLE SOAL

					if(!empty($data['bundle'])){
						foreach($data['bundle'] as $bundle_id){
							$bundle = new Bundle_soal_orm();
							$bundle->id_soal = $id_soal;
							$bundle->bundle_id = $bundle_id;
							$bundle->save();
						}
					}

					//[STOP] SAVE BUNDLE SOAL

					commit_db_trx();

				}catch(Exception $e){
					rollback_db_trx();

					$error = $e->getMessage();
					$ok = false ;
				}

			} else if ($aksi === 'edit') {
				//push array
				// $data['updated_at'] = date('Y-m-d H:i:s');
				/// $data['updated_by']   = $this->ion_auth->user()->row()->username;
				//update data
				//                $id_soal = $this->input->post('id_soal', true);
				try{
					begin_db_trx();

					// $this->master->update('tb_soal', $data, 'id_soal', $id_soal);

					$soal = Soal_orm::findOrFail($id_soal);
					// $soal->soal = $data['soal'];
					$soal->jawaban = $data['jawaban'];
					$soal->bobot_soal_id = $data['bobot_soal_id'];
					$soal->gel = $data['gel'];
					$soal->smt = $data['smt'];
					$soal->tahun = $data['tahun'];
					// foreach ($abjad as $abj) {
						// $data['opsi_' . $abj]    = $this->input->post('jawaban_' . $abj);
						// $opsi = 'opsi_' . $abj ;
						// $soal->$opsi = $data[$opsi];
					// }
					$soal->topik_id = $data['topik_id'];
					// $soal->penjelasan = $data['penjelasan'];
					$soal->created_by = $this->ion_auth->user()->row()->username;
					$soal->section_id = empty($data['section_id']) ? null : $data['section_id'];
					// $soal->save();

					// $soal_temp = Soal_orm::findOrFail($id_soal);

					// $html = $soal_temp->soal;
					$html = $data['soal'];
					// $doc = new DOMDocument('1.0', 'UTF-8');
					// $doc->loadHTML($html);
					$doc = new DOMDocument();
					$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
					$i = 0 ;
					foreach ($doc->getElementsByTagName('img') as $img_node) {
						$src = $img_node->getAttribute('src') ;
						if(strpos($src, 'data:image/png;base64,') !== false){
							$img = str_replace('data:image/png;base64,', '', $src);
							$img = str_replace(' ', '+', $img);
							$img_64 = base64_decode($img);
							// $file_name = $soal_temp->id_soal . '_soal.png';
							$file_name = $id_soal .'_soal_'. mt_rand()  .'.png';
							$file = UPLOAD_DIR . 'img_soal/' . $file_name;
							$success = file_put_contents($file, $img_64);
							if($success){
								$img_node->setAttribute('src', asset('uploads/img_soal/' . $file_name)) ;
								$doc->saveHTML($img_node);
							}
							$i++;
						}
					}
					
					$xpath = new DOMXPath($doc);

					$body = '';
					foreach ($xpath->evaluate('//body/node()') as $node) {
						$body .= $doc->saveHtml($node);
					}

					// $soal_temp->soal = $body;
					$soal->soal = $body;

					foreach (OPSI_SOAL as $abj) {
						$opsi = 'opsi_' . $abj ;
						// $html = $soal_temp->$opsi;
						$html = $this->input->post('jawaban_' . $abj);
						// $doc = new DOMDocument('1.0', 'UTF-8');
						// $doc->loadHTML($html);
						$doc = new DOMDocument();
						$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
						$i = 0 ;
						foreach ($doc->getElementsByTagName('img') as $img_node) {
							$src = $img_node->getAttribute('src') ;
							if(strpos($src, 'data:image/png;base64,') !== false){
								$img = str_replace('data:image/png;base64,', '', $src);
								$img = str_replace(' ', '+', $img);
								$img_64 = base64_decode($img);
								// $file_name = $soal_temp->id_soal . '_jawaban_'. $opsi .'.png';
								$file_name = $id_soal .'_jawaban_'. $opsi .'_'. mt_rand()  .'.png';
								$file = UPLOAD_DIR . 'img_soal/' . $file_name;
								$success = file_put_contents($file, $img_64);
								if($success){
									$img_node->setAttribute('src', asset('uploads/img_soal/' . $file_name)) ;
									$doc->saveHTML($img_node);
								}
								$i++;
							}
						}
						
						$xpath = new DOMXPath($doc);

						$body = '';
						foreach ($xpath->evaluate('//body/node()') as $node) {
							$body .= $doc->saveHtml($node);
						}

						// $soal_temp->$opsi = $body;
						$soal->$opsi = $body;

					}

					/////////

					$html = $data['penjelasan'];
					if(!empty($html)){
						// $doc = new DOMDocument('1.0', 'UTF-8');
						// $doc->loadHTML($html);
						$doc = new DOMDocument();
						$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
						$i = 0 ;
						foreach ($doc->getElementsByTagName('img') as $img_node) {
							$src = $img_node->getAttribute('src') ;
							if(strpos($src, 'data:image/png;base64,') !== false){
								$img = str_replace('data:image/png;base64,', '', $src);
								$img = str_replace(' ', '+', $img);
								$img_64 = base64_decode($img);
								// $file_name = $soal_temp->id_soal . '_soal.png';
								$file_name = $id_soal .'_penjelasan_'. mt_rand()  .'.png';
								$file = UPLOAD_DIR . 'img_soal/' . $file_name;
								$success = file_put_contents($file, $img_64);
								if($success){
									$img_node->setAttribute('src', asset('uploads/img_soal/' . $file_name)) ;
									$doc->saveHTML($img_node);
								}
								$i++;
							}
						}
						
						$xpath = new DOMXPath($doc);

						$body = '';
						foreach ($xpath->evaluate('//body/node()') as $node) {
							$body .= $doc->saveHtml($node);
						}

						// $soal_temp->soal = $body;
						$soal->penjelasan = $body;
					}

					////////

					// $soal_temp->save();
					$soal->save();

					//[START] SAVE BUNDLE SOAL

					$bundle_soal_before = $soal->bundle()->pluck('bundle.id')->toArray();
					
					if(!empty($data['bundle'])){
						$bundle_soal_ids_insert = array_diff($data['bundle'], $bundle_soal_before);
						$bundle_soal_ids_delete = array_diff($bundle_soal_before, $data['bundle']);
						
						if(!empty($bundle_soal_ids_insert)){
							foreach($bundle_soal_ids_insert as $bundle_id){
								$bundle = new Bundle_soal_orm();
								$bundle->id_soal = $id_soal;
								$bundle->bundle_id = $bundle_id;
								$bundle->save();
							}
						}
						if(!empty($bundle_soal_ids_delete)){
							// $bundle_soal = Bundle_soal_orm::where([
							// 	'id_soal' => $id_soal,
							// 	'bundle_id'    => $bundle_soal_ids_delete
							// ])->firstOrFail();

							// $bundle_soal->delete();

							Bundle_soal_orm::where('id_soal', $id_soal)
											->whereIn('bundle_id', $bundle_soal_ids_delete)
											->delete();

						}
					}else{
						if(!empty($bundle_soal_before)){
							Bundle_soal_orm::where('id_soal', $id_soal)
											->whereIn('bundle_id', $bundle_soal_before)
											->delete();
						}
					}

					//[STOP] SAVE BUNDLE SOAL

					commit_db_trx();

				}catch(Exception $e){
					rollback_db_trx();

					$error = $e->getMessage();
					$ok = false ;
				}
			} else {
				show_error('Method tidak diketahui', 404);
			}

			if($ok){
				$message_rootpage = [
					'header' => 'Perhatian',
					'content' => 'Data berhasil disimpan.',
					'type' => 'success'
				];
			}else{
				$message_rootpage = [
					'header' => 'Perhatian',
					'content' => 'Data gagal disimpan, error : ' . $error,
					'type' => 'error'
				];
			}

			$this->session->set_flashdata('message_rootpage', $message_rootpage);
			if($ok)
				$aksi === 'add' ? redirect('soal/detail/' . $id_soal ) : redirect('soal/detail/' . $id_soal);
			else
				$aksi === 'add' ? redirect('soal/add' ) : redirect('soal/edit/' . $id_soal);
		}
	}

	protected function _delete()
	{
		$chk = $this->input->post('checked', true);

		// Delete File
		// foreach ($chk as $id) {
		// 	$abjad = ['a', 'b', 'c', 'd', 'e'];
		// 	$path = FCPATH . 'uploads/bank_soal/';
		// 	$soal = $this->soal->getSoalById($id);
		// 	// Hapus File Soal
		// 	if (!empty($soal->file)) {
		// 		if (file_exists($path . $soal->file)) {
		// 			unlink($path . $soal->file);
		// 		}
		// 	}
		// 	//Hapus File Opsi
		// 	$i = 0; //index
		// 	foreach ($abjad as $abj) {
		// 		$file_opsi = 'file_' . $abj;
		// 		if (!empty($soal->$file_opsi)) {
		// 			if (file_exists($path . $soal->$file_opsi)) {
		// 				unlink($path . $soal->$file_opsi);
		// 			}
		// 		}
		// 	}
		// }

		if (!$chk) {
			$this->_json(['status' => false]);
		} else {

			$user = $this->ion_auth->user()->row();
			if (!$this->ion_auth->is_admin()) {
				$allow = true;
				foreach ($chk  as $c) {
					$soal = Soal_orm::findOrFail($c);
					if ($soal->created_by != $user->username) {
						$allow = false;
						break;
					}
				}
				if (!$allow) {
					$data['status'] = false;
					$this->_json($data);
					return;
				}
			}

			if ($this->master->delete('tb_soal', $chk, 'id_soal')) {
				$this->_json(['status' => true, 'total' => count($chk)]);
			}
		}
	}

	protected function _get_topic_by_matkul()
	{

		$result = [];
		$id = $this->input->get('id');
		$empty = $this->input->get('empty');
		if($id != 'null'){
			$matkul = Matkul_orm::find($id);
			if(!empty($matkul)){
				if ($matkul->topik->isNotEmpty()) {
						foreach ($matkul->topik as $topik) {
							// $result[$topik->id] = '<small><b>'. $topik->matkul->nama_matkul . '</b></small><br/><span class="text-danger">' . $topik->nama_topik . '</span>';
							$result[$topik->id] = $topik->nama_topik ;
						}
					
				}
			}
		}else{
			$topik_list = [];
			if(in_group(DOSEN_GROUP_ID)){
				$user = $this->ion_auth->user()->row();
				$dosen = Orm\Dosen_orm::where('nip', $user->username)->first();
				$matkul = [] ;
				if($dosen->matkul->isNotEmpty()){
					$matkul = $dosen->matkul;
				}

				$matkul_ids = $matkul->pluck('id_matkul')->toArray();

				$topik_list = Topik_orm::whereIn('matkul_id', $matkul_ids)->get();
			}else{
				if(!$empty)
					$topik_list = Topik_orm::all();
			}
			if(!empty($topik_list)){
				foreach ($topik_list as $topik) {
					// $result[$topik->id] = '<small><b>'. $topik->matkul->nama_matkul . '</b></small><br/><span class="text-danger">' . $topik->nama_topik . '</span>';
					$result[$topik->id] = $topik->nama_topik ;
				}
			}
		}
		$this->_json($result);
	}

	public function bobot_soal()
	{
		$data = [
			'judul'	    => 'Bobot Soal',
			'subjudul'  => 'List Bobot Soal'
		];

		view('soal/bobot_soal', $data);
	}

	public function bundle_soal()
	{
		$data = [
			'judul'	    => 'Bundle Soal',
			'subjudul'  => 'List Bundle Soal'
		];

		view('soal/bundle_soal', $data);
	}

	protected function _data_bobot_soal()
	{

		$config = [
			'host'     => $this->db->hostname,
			'port'     => $this->db->port,
			'username' => $this->db->username,
			'password' => $this->db->password,
			'database' => $this->db->database,
		];

		$dt = new Datatables(new MySQL($config));

		$this->db->select('a.id, a.bobot, a.nilai, CONCAT(COUNT(b.bobot_soal_id), " soal") AS jml_soal');
		$this->db->from('bobot_soal a');
		$this->db->join('tb_soal AS b', 'b.bobot_soal_id = a.id', 'left');
		$this->db->group_by('a.id');

		$query = $this->db->get_compiled_select(); // GET QUERY PRODUCED BY ACTIVE RECORD WITHOUT RUNNING I

		$dt->query($query);

		$return = $dt->generate();

		$this->_json($return, false);
	}

	protected function _data_bundle_soal()
	{

		$config = [
			'host'     => $this->db->hostname,
			'port'     => $this->db->port,
			'username' => $this->db->username,
			'password' => $this->db->password,
			'database' => $this->db->database,
		];

		$dt = new Datatables(new MySQL($config));

		$this->db->select('a.id, a.nama_bundle, CONCAT(COUNT(b.id_soal), " soal") AS jml_soal');
		$this->db->from('bundle a');
		$this->db->join('bundle_soal AS b', 'b.bundle_id = a.id', 'left');
		$this->db->group_by('a.id');

		$query = $this->db->get_compiled_select(); // GET QUERY PRODUCED BY ACTIVE RECORD WITHOUT RUNNING I

		$dt->query($query);

		$return = $dt->generate();

		$this->_json($return, false);
	}

	protected function _save_bobot_soal()
	{
		$this->_akses_admin();
		if ($this->input->post()) {
			$this->form_validation->set_rules('aksi', 'Aksi', 'required');
			$this->form_validation->set_rules('bobot', 'Bobot', 'required');
			$this->form_validation->set_rules('nilai', 'Nilai', 'required|decimal|greater_than[0]');
			if ($this->form_validation->run() === false) {
				// VALIDASI SALAH
				$data = [
					'status'	=> false,
					'errors'	=> [
						'bobot' => form_error('bobot'),
						'nilai' => form_error('nilai'),
					]
				];

				$this->_json($data);
			} else {
				$id             = $this->input->post('id');
				$bobot             = $this->input->post('bobot');
				$nilai             = $this->input->post('nilai');

				if (null == $id) {
					$bobot_soal        = new Bobot_soal_orm();
					$bobot_soal->bobot = $bobot;
					$bobot_soal->nilai = $nilai;
					$action = $bobot_soal->save();
					$data['status'] = $action;
				} else {
					$bobot_soal        = Bobot_soal_orm::findOrFail($id);
					$bobot_soal->bobot = $bobot;
					$bobot_soal->nilai = $nilai;
					$action = $bobot_soal->save();
					$data['status'] = $action;
				}
				$this->_json($data);
			}
		}
	}

	protected function _save_bundle_soal()
	{
		$this->_akses_admin();
		if ($this->input->post()) {
			$this->form_validation->set_rules('aksi', 'Aksi', 'required');
			$this->form_validation->set_rules('id', 'ID', 'required');
			$this->form_validation->set_rules('nama_bundle', 'Nama Bundle', 'required');
			if ($this->form_validation->run() === false) {
				// VALIDASI SALAH
				$data = [
					'status'	=> false,
					'errors'	=> [
						'nama_bundle' => form_error('nama_bundle'),
					]
				];

				$this->_json($data);
			} else {
				$id             = $this->input->post('id');
				$nama_bundle             = $this->input->post('nama_bundle');

				// if (null == $id) {
					// $bundle        = new Bundle_orm();
					// $bundle->nama_bundle = $nama_bundle;
					// $action = $bundle->save();
					// $data['status'] = $action;
				// } else {
				// 	$bundle        = Bundle_orm::findOrFail($id);
				// 	$bundle->nama_bundle = $nama_bundle;
				// 	$action = $bundle->save();
				// 	$data['status'] = $action;
				// }

				$bundle        = Bundle_orm::findOrFail($id);
				$bundle->nama_bundle = $nama_bundle;
				$action = $bundle->save();
				$data['status'] = $action;

				$this->_json($data);
			}
		}
	}

	public function add_bobot_soal()
	{

		$this->_akses_admin();

		$data = [
			'judul'	    => 'Bobot Soal',
			'subjudul'  => 'Add Bobot Soal'
		];

		view('soal/add_bobot_soal', $data);
	}

	public function edit_bobot_soal($id)
	{
		$this->_akses_admin();
		$bobot_soal = Bobot_soal_orm::findOrFail($id);

		$data = [
			'judul'	    => 'Bobot Soal',
			'subjudul'  => 'Edit Bobot Soal',
			'bobot_soal' => $bobot_soal,
		];

		view('soal/edit_bobot_soal', $data);
	}

	public function edit_bundle_soal($id)
	{
		$this->_akses_admin();
		$bundle = Bundle_orm::findOrFail($id);

		$data = [
			'judul'	    => 'Bundle Soal',
			'subjudul'  => 'Edit Bundle Soal',
			'bundle' => $bundle,
		];

		view('soal/edit_bundle_soal', $data);
	}

	public function delete_bobot_soal($id)
	{
		$this->_akses_admin();
		
		$bobot_soal = Bobot_soal_orm::findOrFail($id);

		if (empty(count($bobot_soal->soal))) {
			$bobot_soal->delete();
			$message_rootpage = [
				'header' => 'Perhatian',
				'content' => 'Data berhasil dihapus.',
				'type' => 'success'
			];
		} else {
			$message_rootpage = [
				'header' => 'Perhatian',
				'content' => 'Data masih digunakan.',
				'type' => 'error'
			];
		}
		$this->session->set_flashdata('message_rootpage', $message_rootpage);
		redirect('soal/bobot_soal', 'refresh');
	}

	protected function _delete_bundle_soal()
	{
		$this->_akses_admin();

		$id = $this->input->post('id');
		$bundle = Bundle_orm::findOrFail($id);

		$stts = null;
		$msg = null;
		try{
			$bundle->delete();
			$stts = 'ok';
			
		}catch(Exception $e){
			$stts = 'ko';
			$msg = $e->getMessage();
		}

		$this->_json(['stts' => $stts, 'msg' => $msg]);
	}

	protected function _get_jml_soal_per_topik()
	{

		$topik_ids = $this->input->post('topik_ids');
		$topik_ids = json_decode($topik_ids);

		$filter_data	= $this->input->post('filter');
		$filter			= [];
		if (!empty($filter_data)) {
			foreach ($filter_data as $key => $v) {
				if($v != 'null'){
					$filter[$key] = $v;
				}
			}
		}

		$bundle_ids = $this->input->post('bundle_ids');
		$bundle_ids = json_decode($bundle_ids);

		$jml_soal = [];
		
		if (!empty($topik_ids)) {
			//            $topik = Topik_orm::whereIn('id',$topik_ids)->get();
			//            $bobot_soal = Bobot_soal_orm::whereIn('id',$topik_ids)->get();
			$soal = Soal_orm::whereIn('topik_id', $topik_ids)->where('is_reported', NO_REPORTED_SOAL);
			if(!empty($filter)){
				$soal->where($filter);
			}

			if(!empty($bundle_ids)){
				$soal->whereHas('bundle_soal', function (Builder $query) use($bundle_ids) {
					$query->whereIn('bundle_id', $bundle_ids);
				});
			}

			$soal = $soal->get();
			if ($soal->isNotEmpty()) {
				foreach ($soal as $d) {
					if (!isset($jml_soal[$d->topik_id][$d->bobot_soal_id]))
						$jml_soal[$d->topik_id][$d->bobot_soal_id] = 0;
					$jml_soal[$d->topik_id][$d->bobot_soal_id] = ++$jml_soal[$d->topik_id][$d->bobot_soal_id];
				}
			}
		}
		$this->_json($jml_soal);
	}

	protected function _get_matkul_from_selected_bundle(){

		$bundle_ids = $this->input->post('bundle_ids');
		$bundle_ids = json_decode($bundle_ids);

		$topik_id_list = [];
		$topik_id_ref_bundle_list = [];

		$bundle_list = Bundle_orm::whereIn('id', $bundle_ids)->get();
		if($bundle_list->isNotEmpty()){
			foreach($bundle_list as $bundle){
				if($bundle->soal->isNotEmpty()){
					$soal_list = $bundle->soal()->groupBy('topik_id')->get(['topik_id']);
					foreach($soal_list as $soal){
						if(!isset($topik_id_ref_bundle_list[$bundle->id]))
							$topik_id_ref_bundle_list[$bundle->id] = [];
						if(!in_array($soal->topik_id, $topik_id_ref_bundle_list[$bundle->id]))
							$topik_id_ref_bundle_list[$bundle->id][] = $soal->topik_id ;
						if(!in_array($soal->topik_id, $topik_id_list)){
							$topik_id_list[] = $soal->topik_id;
						}

					}
				}
			}
		}

		$matkul_ids = Topik_orm::whereIn('id',$topik_id_list)
									// ->get(['matkul_id'])
									->pluck('matkul_id')
									->toArray();
		$matkul_ids = array_unique($matkul_ids);
		$matkul_list = Matkul_orm::whereIn('id_matkul', $matkul_ids)->get();

		$this->_json(['matkul_list' => $matkul_list]);

	}

	protected function _get_topik_from_selected_bundle(){

		$bundle_ids = $this->input->post('bundle_ids');
		$bundle_ids = json_decode($bundle_ids);

		$topik_id_list = [];
		$topik_id_ref_bundle_list = [];
		$topik_list = [];

		$bundle_list = Bundle_orm::whereIn('id', $bundle_ids)->get();
		if($bundle_list->isNotEmpty()){
			foreach($bundle_list as $bundle){
				if($bundle->soal->isNotEmpty()){
					$soal_list = $bundle->soal()->groupBy('topik_id')->get(['topik_id']);
					foreach($soal_list as $soal){
						if(!isset($topik_id_ref_bundle_list[$bundle->id]))
							$topik_id_ref_bundle_list[$bundle->id] = [];
						if(!in_array($soal->topik_id, $topik_id_ref_bundle_list[$bundle->id]))
							$topik_id_ref_bundle_list[$bundle->id][] = $soal->topik_id ;
						if(!in_array($soal->topik_id, $topik_id_list)){
							$topik_id_list[] = $soal->topik_id;
							$topik_list[$soal->topik_id] = '<small><b>'. $soal->topik->matkul->nama_matkul . '</b></small><br/><span class="text-danger">' . $soal->topik->nama_topik . '</span>';
						}

					}
				}
			}
		}

		$this->_json(['ids' => $topik_id_list, 'topik_id_ref_bundle' => $topik_id_ref_bundle_list, 'topik' => $topik_list]);

	}

	public function import($import_data = null)
	{
		$data = [
			'judul'	=> 'Soal',
			'subjudul' => 'Import Soal',
			'topik_list' => Topik_orm::All(),
			'bobot_list' => Bobot_soal_orm::All()
		];
		if ($import_data != null) $data['import'] = $import_data;

		//		$this->load->view('_templates/dashboard/_header', $data);
		//		$this->load->view('master/mahasiswa/import');
		//		$this->load->view('_templates/dashboard/_footer');
		view('soal/import', $data);
	}

	public function preview()
	{
		$config['upload_path']		= UPLOAD_DIR .'import/';
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

				if (count($sheetData[$i]) < JML_KOLOM_EXCEL_IMPOR_SOAL) {
					unlink($file);
					show_error('Isian file tidak sesuai', 500, 'Perhatian');
				}

				$topik_id = $sheetData[$i][0];
				$topik = Topik_orm::find($topik_id);
				if ($topik == null) {
					$topik_id = '!! ERROR !!';
				}

				$soal = $sheetData[$i][1];
				if (empty($soal)) {
					$soal = '!! ERROR !!';
				}

				$opsi_a = $sheetData[$i][2];
				if (empty($opsi_a)) {
					$opsi_a = '!! ERROR !!';
				}

				$opsi_b = $sheetData[$i][3];
				if (empty($opsi_b)) {
					$opsi_b = '!! ERROR !!';
				}

				$opsi_c = $sheetData[$i][4];
				if (empty($opsi_c)) {
					$opsi_c = '!! ERROR !!';
				}

				$opsi_d = $sheetData[$i][5];
				if (empty($opsi_d)) {
					$opsi_d = '!! ERROR !!';
				}

				$opsi_e = $sheetData[$i][6];
				if (empty($opsi_e)) {
					$opsi_e = '!! ERROR !!';
				}

				$jawaban = $sheetData[$i][7];
				if (!in_array($jawaban, ['A', 'B', 'C', 'D', 'E'])) {
					$jawaban = '!! ERROR !!';
				}

				$penjelasan = $sheetData[$i][8];
				// if (empty($penjelasan)) {
				// 	$penjelasan = '!! ERROR !!';
				// }

				$bobot_soal_id = $sheetData[$i][9];
				$bobot_soal = Bobot_soal_orm::find($bobot_soal_id);
				if ($bobot_soal == null) {
					$bobot_soal_id = '!! ERROR !!';
				}


				$gel = strval($sheetData[$i][10]);
				if (!ctype_digit($gel)) {
					$gel = '!! ERROR !!';
				}

				$smt = strval($sheetData[$i][11]);
				if (!ctype_digit($smt)) {
					$smt = '!! ERROR !!';
				}

				$tahun = $sheetData[$i][12];
				if (strlen($tahun) != 4 || !ctype_digit($tahun)) {
					$tahun = '!! ERROR !!';
				}

				$data[] = [
					'topik_id' => $topik_id,
					'topik' => $topik,
					'soal' => $soal,
					'opsi_a' => $opsi_a,
					'opsi_b' => $opsi_b,
					'opsi_c' => $opsi_c,
					'opsi_d' => $opsi_d,
					'opsi_e' => $opsi_e,
					'jawaban' => $jawaban,
					'penjelasan' => $penjelasan,
					'bobot_soal_id' => $bobot_soal_id,
					'bobot_soal' => $bobot_soal,
					'gel' => $gel,
					'smt' => $smt,
					'tahun' => $tahun,
				];
			}

			unlink($file);

			$this->import($data);
		}
	}

	public function do_import()
	{

		$this->_akses_admin_dosen_dan_penyusun_soal();

		$input = json_decode($this->input->post('data', true));
		//		$data = [];
		try {
			begin_db_trx();
			$allow = true;
			$msg   = null;
			//		vdebug($input);
			foreach ($input as $d) {
				$topik_id = $d->topik_id;
				if (Topik_orm::find($topik_id) == null) {
					$allow = false;
					$msg   = 'Topik ID bermasalah, topik_id : ' . $topik_id;
					break;
				}

				$isi_soal = $d->soal;
				if (empty($isi_soal)) {
					$allow = false;
					$msg   = 'Soal salah, soal : ' . $isi_soal;
					break;
				}

				$opsi_a = $d->opsi_a;
				if (empty($opsi_a)) {
					$allow = false;
					$msg   = 'Opsi A salah, opsi_a : ' . $opsi_a;
					break;
				}

				$opsi_b = $d->opsi_b;
				if (empty($opsi_b)) {
					$allow = false;
					$msg   = 'Opsi B salah, opsi_b : ' . $opsi_b;
					break;
				}

				$opsi_c = $d->opsi_c;
				if (empty($opsi_c)) {
					$allow = false;
					$msg   = 'Opsi C salah, opsi_c : ' . $opsi_c;
					break;
				}

				$opsi_d = $d->opsi_d;
				if (empty($opsi_d)) {
					$allow = false;
					$msg   = 'Opsi D salah, opsi_d : ' . $opsi_d;
					break;
				}

				$opsi_e = $d->opsi_e;
				if (empty($opsi_e)) {
					$allow = false;
					$msg   = 'Opsi E salah, opsi_e : ' . $opsi_e;
					break;
				}

				$jawaban = $d->jawaban;
				if (!in_array($jawaban, [
					'A',
					'B',
					'C',
					'D',
					'E'
				])) {
					$allow = false;
					$msg   = 'Jawaban bermasalah, jawaban : ' . $jawaban;
					break;
				}

				$penjelasan = empty($d->penjelasan) ? null : $d->penjelasan;
				// if (empty($penjelasan)) {
				// 	$allow = false;
				// 	$msg   = 'Penjelasan salah, penjelasan : ' . $penjelasan;
				// 	break;
				// }

				$bobot_soal_id = $d->bobot_soal_id;
				if (Bobot_soal_orm::find($bobot_soal_id) == null) {
					$allow = false;
					$msg   = 'Bobot soal ID bermasalah, bobot_soal_id : ' . $bobot_soal_id;
					break;
				}

				$gel = strval($d->gel);
				if (!ctype_digit($gel)) {
					$allow = false;
					$msg   = 'Gel salah, gel : ' . $gel;
					break;
				}

				$smt = strval($d->smt);
				if (!ctype_digit($smt)) {
					$allow = false;
					$msg   = 'Smt salah, smt : ' . $smt;
					break;
				}

				$tahun = $d->tahun;
				if (strlen($tahun) != 4 || !ctype_digit($tahun)) {
					$allow = false;
					$msg   = 'Tahun salah, tahun : ' . $tahun;
					break;
				}

				$no_urut = 0 ;
				$soal_before = Soal_orm::where('topik_id', $topik_id)
									->orderBy('created_at', 'desc')
									->first();

				if(empty($soal_before))
					$no_urut = 1;
				else{
					if(empty($soal_before->no_urut))
						$no_urut = 1;
					else
						$no_urut = ($soal_before->no_urut) + 1;
				}

				$soal                = new Soal_orm();
				$soal->topik_id      = $topik_id;
				$soal->no_urut       = $no_urut;
				$soal->soal          = '<p>' . $isi_soal . '</p>';
				$soal->opsi_a        = '<p>' . $opsi_a . '</p>';
				$soal->opsi_b        = '<p>' . $opsi_b . '</p>';
				$soal->opsi_c        = '<p>' . $opsi_c . '</p>';
				$soal->opsi_d        = '<p>' . $opsi_d . '</p>';
				$soal->opsi_e        = '<p>' . $opsi_e . '</p>';
				$soal->jawaban       = $jawaban;
				$soal->penjelasan       = $penjelasan;
				$soal->bobot_soal_id = $bobot_soal_id;
				$soal->gel = $gel;
				$soal->smt = $smt;
				$soal->tahun = $tahun;
				$soal->created_by    = $this->ion_auth->user()->row()->username;
				$soal->save();
			}

			if (!$allow) {
				throw new Exception($msg);
			} else {
				commit_db_trx();
				$message_rootpage = [
					'header'  => 'Perhatian',
					'content' => 'Data berhasil di impor.',
					'type'    => 'success'
				];
				$this->session->set_flashdata('message_rootpage', $message_rootpage);
				redirect('soal/import');
			}

		} catch (Exception $e) {
			rollback_db_trx();
			show_error($e->getMessage(), 500, 'Perhatian');
		}
	}

	protected function _save_bundle(){ // SAVE NEW BUNDLE FROM LIST SOAL INTERFACE
		$this->_akses_admin();
		$nama_bundle = $this->input->post('nama_bundle');
		try{
			$bundle = new Bundle_orm();
			$bundle->nama_bundle = $nama_bundle;
			$bundle->created_by = $this->ion_auth->user()->row()->username;
			$bundle->save();

			$this->_json(['stts' => 'ok', 'bundle' => ['id' => $bundle->id, 'nama_bundle' => $nama_bundle]]);

		}catch(Exception $e){

			$this->_json(['stts' => 'ko', 'msg' => $e->getMessage()]);
		}

	}

	protected function _asign_soal_bundle(){

		$this->_akses_admin();

		$selected_bundle = $this->input->post('selected_bundle');
		$selected_soal = $this->input->post('selected_soal');
		$is_ignore_bundle = $this->input->post('is_ignore_bundle') == 'true' ? true : false;
		try{
			begin_db_trx();
			$selected_bundle = json_decode($selected_bundle);
			$selected_soal = json_decode($selected_soal);
			$now = Carbon::now()->toDateTimeString();
			if(!empty($selected_bundle)){
				if(!$is_ignore_bundle){
					$bundle_ids_before = Bundle_soal_orm::whereIn('id_soal', $selected_soal)
											// ->get(['bundle_id'])
											->pluck('bundle_id')
											->toArray();
					$bundle_ids_delete = array_diff($bundle_ids_before, $selected_bundle);
					if(!empty($bundle_ids_delete)){
						Bundle_soal_orm::whereIn('bundle_id', $bundle_ids_delete)->whereIn('id_soal', $selected_soal)->delete();
					}
				}
				// $bundle_ids_delete = array_diff($soal_ids_before, $selected_soal);
				foreach($selected_bundle as $bundle_id){
					$soal_ids_before = Bundle_soal_orm::where('bundle_id', $bundle_id)
											->whereIn('id_soal', $selected_soal)
											// ->get(['id_soal'])
											->pluck('id_soal')
											->toArray();
					$soal_ids_insert = array_diff($selected_soal, $soal_ids_before);
					// $soal_ids_delete = array_diff($soal_ids_before, $selected_soal);

					$insert = [];
					if(!empty($soal_ids_insert)){
						foreach($soal_ids_insert as $soal_id){
							$insert[] = [
								'bundle_id' => $bundle_id,
								'id_soal' => $soal_id,
								'created_at' => $now,
							];
						}
						Bundle_soal_orm::insert($insert);
					}

					// if(!empty($soal_ids_delete)){
					// 	foreach($soal_ids_delete as $soal_id){
					// 		$delete = [
					// 			'bundle_id' => $bundle_id,
					// 			'id_soal' => $soal_id,
					// 		];
					// 		Bundle_soal_orm::where($delete)->delete();
					// 	}
					// 	Bundle_soal_orm::where('bundle_id', $bundle_id)->whereIn('id_soal', $soal_ids_delete)->delete();
					// }
				}
			}else{
				if(!$is_ignore_bundle){
					Bundle_soal_orm::whereIn('id_soal', $selected_soal)->delete();
				}
			}

			commit_db_trx();
			$this->_json(['stts' => 'ok']);

		}catch(Exception $e){
			rollback_db_trx();
			$this->_json(['stts' => 'ko', 'msg' => $e->getMessage()]);
		}

	}

	protected function _save_section(){

		$this->_akses_admin_dosen_dan_penyusun_soal();

		$keterangan = $this->input->post('keterangan');
		$konten = $this->input->post('konten');

		try{
			begin_db_trx();
			$section = new Section_orm();
			$section->keterangan = $keterangan;
			$section->konten = $konten;

			$section->save();

			$section_temp = Section_orm::findOrFail($section->id);
			
			$html = $konten;
			
			// $doc = new DOMDocument('1.0', 'UTF-8');
			// $doc->loadHTML($html);
			$doc = new DOMDocument();
			$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
			$i = 0 ;
			foreach ($doc->getElementsByTagName('img') as $img_node) {
				$src = $img_node->getAttribute('src') ;
				if(strpos($src, 'data:image/png;base64,') !== false){
					$img = str_replace('data:image/png;base64,', '', $src);
					$img = str_replace(' ', '+', $img);
					$img_64 = base64_decode($img);
					// $file_name = $soal_temp->id_soal . '_soal.png';
					$file_name = $section_temp->id .'_section_'. mt_rand()  .'.png';
					$file = UPLOAD_DIR . 'img_soal/' . $file_name;
					$success = file_put_contents($file, $img_64);
					if($success){
						$img_node->setAttribute('src', asset('uploads/img_soal/' . $file_name)) ;
						$doc->saveHTML($img_node);
					}
					$i++;
				}
			}
			
			$xpath = new DOMXPath($doc);

			$body = '';
			foreach ($xpath->evaluate('//body/node()') as $node) {
				$body .= $doc->saveHtml($node);
			}

			$section_temp->konten = $body;

			$section_temp->save();

			commit_db_trx();
			$this->_json(['stts' => 'ok', 'section' => ['id' => $section->id, 'keterangan' => $keterangan]]);

		}catch(Exception $e){
			rollback_db_trx();
			$this->_json(['stts' => 'ko', 'msg' => $e->getMessage()]);
		}

	}

	protected function _select_section(){

		$this->_akses_admin_dosen_dan_penyusun_soal();

		$id = $this->input->post('id');
		try{
			$section = Section_orm::findOrFail($id);

			$this->_json(['stts' => 'ok', 'section' => $section]);

		}catch(Exception $e){

			$this->_json(['stts' => 'ko', 'msg' => $e->getMessage()]);
		}

	}

	protected function _report_soal(){

		$this->_akses_admin();

		$id = $this->input->post('id_soal');
		try{
			$soal = Soal_orm::findOrFail($id);
			if($soal->is_reported)
				$soal->is_reported = 0 ;
			else
				$soal->is_reported = 1 ;

			$soal->save();

			$this->_json(['stts' => 'ok', 'id_soal' => $id, 'is_reported' => $soal->is_reported]);

		}catch(Exception $e){

			$this->_json(['stts' => 'ko', 'msg' => $e->getMessage()]);
		}
	}
}
