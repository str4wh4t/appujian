<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Orm\Hujian_orm;
use Orm\Hujian_history_orm;
use Orm\Mujian_orm;
use Orm\Topik_orm;
use Orm\Hujian_deleted_orm;
use Orm\Jawaban_ujian_deleted_orm;
use Orm\Jawaban_ujian_orm;
use Orm\Mhs_orm;
use Orm\Mhs_ujian_orm;
use Illuminate\Database\Capsule\Manager as DB;

class HasilUjian extends MY_Controller {
	
	private $user ;

	public function __construct(){
		parent::__construct();
		if (!$this->ion_auth->logged_in()){
			redirect('auth');
		}
		
		$this->load->library(['datatables']);// Load Library Ignited-Datatables
		$this->load->model('Master_model', 'master');
		$this->load->model('Ujian_model', 'ujian');
		
		$this->user = $this->ion_auth->user()->row();
	}

	protected function _data()
	{
		$nip = null;
		
		if( $this->ion_auth->in_group('dosen') ) {
			$nip = $this->user->username;
		}

		$this->_json($this->ujian->getHasilUjian($nip), false);
	}

//	public function NilaiMhs($id)
//	{
//		$this->_json($this->ujian->HslUjianById($id, true), false);
//	}
	
	protected function _nilai()
	{
		$id = $this->input->post('id');
		
		if(in_group('mahasiswa')){
			$id = integer_read_from_uuid($id);
		}
		
		$m_ujian = Mujian_orm::findOrFail($id);
		
		if(in_group('mahasiswa')){
			if(!$m_ujian->tampilkan_hasil){
				show_404();
			}
		}
		
		$this->_json($this->ujian->HslUjianById($id, true), false);
	}
	
	protected function _reset_hasil_ujian(){
		
		$this->_akses_admin();
		
		$id = $this->input->post('id');
		$h_ujian = Hujian_orm::findOrFail($id);
		$mujian_id = $h_ujian->ujian_id;
		$action = true;
		try {
			begin_db_trx();
//			$h_ujian_deleted = new Hujian_deleted_orm();
//			$h_ujian_deleted->ujian_id = $h_ujian->ujian_id;
//			$h_ujian_deleted->mahasiswa_id = $h_ujian->mahasiswa_id;
//			$h_ujian_deleted->mahasiswa_ujian_id = $h_ujian->mahasiswa_ujian_id;
//			$h_ujian_deleted->jml_soal = $h_ujian->jml_soal;
//			$h_ujian_deleted->jml_benar = $h_ujian->jml_benar;
//			$h_ujian_deleted->jml_salah = $h_ujian->jml_salah;
//			$h_ujian_deleted->nilai = $h_ujian->nilai;
//			$h_ujian_deleted->nilai_bobot_benar = $h_ujian->nilai_bobot_benar;
//			$h_ujian_deleted->total_bobot = $h_ujian->total_bobot;
//			$h_ujian_deleted->nilai_bobot = $h_ujian->nilai_bobot;
//			$h_ujian_deleted->detail_bobot_benar = $h_ujian->detail_bobot_benar;
//			$h_ujian_deleted->tgl_mulai = $h_ujian->tgl_mulai;
//			$h_ujian_deleted->tgl_selesai = $h_ujian->tgl_selesai;
//			$h_ujian_deleted->ujian_selesai = $h_ujian->ujian_selesai;
//			$h_ujian_deleted->save();
//
//			foreach($h_ujian->jawaban_ujian as $jawaban_ujian) {
////				vdebug($jawaban_ujian);
//				$jawaban_ujian_deleted_orm           = new Jawaban_ujian_deleted_orm();
//				$jawaban_ujian_deleted_orm->ujian_id = $h_ujian_deleted->id;
//				$jawaban_ujian_deleted_orm->soal_id  = $jawaban_ujian->soal_id;
//				$jawaban_ujian_deleted_orm->jawaban  = $jawaban_ujian->jawaban;
//				$jawaban_ujian_deleted_orm->status_jawaban  = $jawaban_ujian->status_jawaban;
//				$jawaban_ujian_deleted_orm->save();
//			}

			$mahasiswa_ujian_id = $h_ujian->mahasiswa_ujian_id;
			$h_ujian->delete();

			Hujian_history_orm::where('mahasiswa_ujian_id', $mahasiswa_ujian_id)->delete();

			commit_db_trx();
		} catch(\Illuminate\Database\QueryException $e){
			rollback_db_trx();
			show_error($e->getMessage(), 500, 'Perhatian');
			$action = false;
	    }
		
		$this->_json(['status' => $action, 'mujian_id' => $mujian_id]);
		
	}

	public function index()
	{
		$data = [
			'user' => $this->user,
			'judul'	=> 'Hasil Ujian',
			'subjudul'=> 'List Hasil Ujian',
		];
//		$this->load->view('_templates/dashboard/_header.php', $data);
//		$this->load->view('ujian/hasil');
//		$this->load->view('_templates/dashboard/_footer.php');
		view('hasilujian/index',$data);
	}
	
	public function detail($id)
	{
//		vdebug($this->ion_auth->in_group('mahasiswa'));
		$mhs = null ;
		if(in_group('mahasiswa')){
			$id = integer_read_from_uuid($id);
			$user = $this->ion_auth->user()->row();
			$mhs = Mhs_orm::where('nim', $user->username)->firstOrFail();
		}

		// vdebug($mhs);
		
		$ujian = Mujian_orm::findOrFail($id);
		
		if(in_group('mahasiswa')){
			if(!$ujian->tampilkan_hasil){
				show_404();
			}
		}
		
		$nilai = $this->ujian->bandingNilai($id);
		
//		vdebug($nilai);

		$data = [
			'user' => $this->user,
			'judul'	=> 'Ujian',
			'subjudul'=> 'Detail Hasil Ujian',
			'ujian'	=> $ujian,
			'nilai'	=> $nilai,
			'mhs'	=> $mhs, //  ADA NILAI NYA JIKA YG LOGIN MHS
		];

//		$this->load->view('_templates/dashboard/_header.php', $data);
//		$this->load->view('ujian/detail_hasil');
//		$this->load->view('_templates/dashboard/_footer.php');
		view('hasilujian/detail',$data);
	}

	protected function _get_stat_nilai(){
		$id = $this->input->post('id');
		
		if(in_group('mahasiswa')){
			$id = integer_read_from_uuid($id);
		}
		
		$m_ujian = Mujian_orm::findOrFail($id);
		
		if(in_group('mahasiswa')){
			if(!$m_ujian->tampilkan_hasil){
				show_404();
			}
		}
		
		$nilai = $this->ujian->bandingNilai($m_ujian->id_ujian);

		$data['nilai_terendah'] = number_format($nilai->min_nilai,2,'.', '');
		$data['nilai_tertinggi'] = number_format($nilai->max_nilai,2,'.', '');
		$data['nilai_rata_rata'] = number_format($nilai->avg_nilai,2,'.', '');
		
		$this->_json($data);
	}

	public function cetak_detail($id)
	{
		ini_set('max_execution_time', 0);
		if(in_group('mahasiswa')){
			$id = integer_read_from_uuid($id);
		}
		
		$m_ujian = Mujian_orm::findOrFail($id);
		
		if(in_group('mahasiswa')){
			if(!$m_ujian->tampilkan_hasil){
				show_404();
			}
		}
		
		$ujian = $m_ujian;
		$nilai = $this->ujian->bandingNilai($id);
		$hasil = $this->ujian->HslUjianById($id)->result();
		

		$data = [
			'ujian'	=> $ujian,
			'nilai'	=> $nilai,
			'hasil'	=> $hasil
		];

		$tpk = Topik_orm::pluck('nama_topik','id')->toArray();
		
		$new_hasil = [];
		foreach ($data['hasil'] as $hasil){
			$hasil_ujian_per_topik = json_decode($hasil->detail_bobot_benar);
            $return = '<table>';
            if(!empty($hasil_ujian_per_topik)) {
	            foreach ($hasil_ujian_per_topik as $t => $v) {
		            $return .= '<tr>';
		            // $tpk    = Topik_orm::findOrFail($t);
		            $return .= '<td width="80%">' . $tpk[$t] . '</td>';
		            if(is_show_detail_hasil())
                        $return .= '<td width="20%">' . $v . '</td>';
		            $return .= '</tr>';
	            }
            }
            $return .= '</table>';
            $new_hasil[] = [
				'nim' => $hasil->nim,
				'nama' => $hasil->nama,
				'nilai' => $hasil->nilai,
				'nilai_bobot_benar' => $hasil->nilai_bobot_benar,
				'detail_bobot_benar' => $return,
				// 'absensi' => empty($hasil->absen_by) ? 'BELUM' : 'SUDAH',
				// 'is_terlihat_pada_layar' => empty($hasil->is_terlihat_pada_layar) ? '-' : ($hasil->is_terlihat_pada_layar ? 'YA' : '-'),
				// 'is_perjokian' => empty($hasil->is_perjokian) ? '-' : ($hasil->is_perjokian ? 'YA' : '-'),
				// 'is_sering_buka_page_lain' => empty($hasil->is_sering_buka_page_lain) ? '-' : ($hasil->is_sering_buka_page_lain ? 'YA' : '-'),
			];
		}
		
		$data['hasil'] = $new_hasil;

		$this->load->view('hasilujian/cetak_detail', $data);
	}

	public function cetak_detail_xls($id)
	{
		ini_set('max_execution_time', 0);
		if(in_group('mahasiswa')){
			$id = integer_read_from_uuid($id);
		}
		
		$m_ujian = Mujian_orm::findOrFail($id);
		
		if(in_group('mahasiswa')){
			if(!$m_ujian->tampilkan_hasil){
				show_404();
			}
		}

		$ujian = $m_ujian;
		// dd($m_ujian->topik);

		$nilai = $this->ujian->bandingNilai($id);
		$hasil = $this->ujian->HslUjianById($id)->result();

		$data = [
			'ujian'	=> $ujian,
			'nilai'	=> $nilai,
			'hasil'	=> $hasil,
			'materi_ujian_list'	=> [],
		];

		// $tpk = Topik_orm::pluck('nama_topik','id')->toArray();
		
		$new_hasil = [];
		foreach ($data['hasil'] as $hasil){
			$hasil_ujian_per_topik = json_decode($hasil->detail_bobot_benar);
			$detail_bobot_benar_array = [];
            if(!empty($hasil_ujian_per_topik)) {
	            foreach ($hasil_ujian_per_topik as $t => $v) {
					$detail_bobot_benar_array[$t] = $v;
	            }
            }

			$hasil_ujian_per_topik_nilai = json_decode($hasil->detail_nilai);
			$detail_nilai_array = [];
            if(!empty($hasil_ujian_per_topik_nilai)) {
	            foreach ($hasil_ujian_per_topik_nilai as $t => $v) {
					$detail_nilai_array[$t] = $v;
	            }
            }

            $new_hasil[] = [
				'nim' => $hasil->nim,
				'nama' => $hasil->nama,
				'nilai' => $hasil->nilai,
				'nilai_bobot_benar' => $hasil->nilai_bobot_benar,
				'detail_bobot_benar' => $detail_bobot_benar_array,
				'detail_nilai' => $detail_nilai_array,
				'absensi' => empty($hasil->absen_by_username) ? 'BELUM' : 'SUDAH',
				'absensi_oleh' => empty($hasil->absen_by_username) ? '-' : "'". $hasil->absen_by_username,
				'is_terlihat_pada_layar' => empty($hasil->is_terlihat_pada_layar) ? '-' : ($hasil->is_terlihat_pada_layar ? 'YA' : '-'),
				'is_perjokian' => empty($hasil->is_perjokian) ? '-' : ($hasil->is_perjokian ? 'YA' : '-'),
				'is_sering_buka_page_lain' => empty($hasil->is_sering_buka_page_lain) ? '-' : ($hasil->is_sering_buka_page_lain ? 'YA' : '-'),
				'catatan_pengawas' => $hasil->catatan_pengawas,
			];
		}
		
		$data['hasil'] = $new_hasil;
		$data['nama_file'] = 'download_hasil_' . str_replace('.', '_', APP_ID) . '_' . url_title($m_ujian->nama_ujian, '_', true) . '_' . date('Ymd');

		$this->load->view('hasilujian/cetak_detail_xls', $data);
	}

	public function jawaban($id)
	{
		if(in_group('mahasiswa')){
			$id = integer_read_from_uuid($id);
		}

		$h_ujian = Hujian_orm::find($id);

		$is_data_history = false ;
		if(empty($h_ujian)){
			$h_ujian = Hujian_history_orm::findorFail($id);
			$is_data_history = true ;
			$mhs = $h_ujian->mhs;
			// $h_ujian_all = Hujian_history_orm::select('*', DB::raw('TIMESTAMPDIFF(SECOND, tgl_mulai, tgl_selesai) AS lama_pengerjaan'))
			// 				->where(['ujian_id' => $h_ujian->ujian_id])
			// 				->orderBy('nilai_bobot_benar', 'desc')
			// 				->orderBy('lama_pengerjaan', 'asc')
			// 				->get();
			$peringkat = $h_ujian->peringkat;
			$jml_peserta = $h_ujian->jml_peserta ;

		}else{
			$mhs = $h_ujian->mhs;
			$h_ujian_all = Hujian_orm::select('*', DB::raw('TIMESTAMPDIFF(SECOND, tgl_mulai, tgl_selesai) AS lama_pengerjaan'))
							->where(['ujian_id' => $h_ujian->ujian_id])
							->orderBy('nilai_bobot_benar', 'desc')
							->orderBy('lama_pengerjaan', 'asc')
							->get();
			$jml_peserta = $h_ujian_all->count();

			$peringkat = 1;
			foreach($h_ujian_all as $ujian){
				if($ujian->mahasiswa_id == $mhs->id_mahasiswa){
					break;
				}
				$peringkat++;
			}
		}

		if(in_group('mahasiswa')){
			if(!$h_ujian->m_ujian->tampilkan_jawaban){
				show_404();
			}
		}

		$data = [
			'judul'	=> 'Hasil Ujian',
			'subjudul' => 'Jawaban'
		];

		$data['is_data_history'] = $is_data_history;

		$data['h_ujian'] = $h_ujian;

		$date1 = new DateTime($h_ujian->tgl_mulai);
		$date2 = new DateTime($h_ujian->tgl_selesai);
		$interval = $date1->diff($date2);

		$waktu_mengerjakan = $interval->h  . ' jam ' . $interval->i . ' mnt ' . $interval->s . ' dtk' ;
		$data['waktu_mengerjakan'] = $waktu_mengerjakan;

		$data['mhs'] = $mhs;
		$data['peringkat'] = $peringkat;
		$data['jml_peserta'] = $jml_peserta;

		$topik_ujian_list = [];
		if(!empty($h_ujian->m_ujian->urutan_topik)){
			$urutan_topik = $h_ujian->m_ujian->urutan_topik;
			$urutan_topik = json_decode($urutan_topik, true);
			uasort($urutan_topik, function($a, $b){
				return $a['urutan'] <=> $b['urutan'];
			});
			
			foreach($urutan_topik as $topik_id => $v){
				$topik_ujian_list[] = Topik_orm::findOrFail($topik_id);
			}
		}else{
			// LOGIK INI SEPERTINYA SUDAH TIDAK DIPAKAI
			$topik_ujian_list = $h_ujian->m_ujian->topik;
		}

		$data['topik_ujian_list'] = $topik_ujian_list;

		/**[START] PREPARE DATA FOR CHART*/
		$label_and_data = collect();

		if($h_ujian->jawaban_ujian->isNotEmpty()){
			// $label_and_data = $h_ujian->jawaban_ujian->pluck('waktu_jawab_soal', 'soal_id');
			$i = 1 ;

			$jawaban_ujian_urut = collect();
			$tes = [];
			foreach($topik_ujian_list as $topik_ujian){
				foreach($h_ujian->jawaban_ujian->sortBy('id') as $jawaban_ujian){
					if($topik_ujian->id == $jawaban_ujian->soal->topik_id){
						$jawaban_ujian_urut->add($jawaban_ujian);
						$tes[] = $jawaban_ujian->id;
					}
				}
			}

			foreach($jawaban_ujian_urut as $jawaban_ujian){
				$date1 = new DateTime($jawaban_ujian->waktu_buka_soal);
				$date2 = new DateTime($jawaban_ujian->waktu_jawab_soal);
				$interval = $date1->diff($date2);

				// $waktu_menjawab = $interval->i . ' mnt ' . $interval->s . ' dtk' ;
				$label_and_data->put($i, ($interval->i * 60) + $interval->s);
				$i++;
			}
		}

		// vdebug($label_and_data);

		$chart_label_and_data_array = [];
		$label_and_data->each(function ($item, $key) use(&$chart_label_and_data_array){
			$data = [
				'soal_ke' => $key,
				'waktu_menjawab' => $item,
			];
			$chart_label_and_data_array[] = $data;
		});
		
		$chart_label_and_data = collect($chart_label_and_data_array);
		$data['chart_label_and_data'] = $chart_label_and_data;
		/**[START] PREPARE DATA FOR CHART*/


		view('hasilujian/jawaban', $data);

	}

	public function history($mahasiswa_ujian_id){

		if(!is_admin() && !in_group('mahasiswa')){
			show_404();
		}

		$data = [
			'judul'	=> 'Ujian',
			'subjudul' => 'History Ujian'
		];
		
		if(in_group('mahasiswa'))
			$mahasiswa_ujian_id = integer_read_from_uuid($mahasiswa_ujian_id);

		
		$mhs_ujian = Mhs_ujian_orm::findOrFail($mahasiswa_ujian_id);

		// if(in_group('mahasiswa')){
		// 	if(!($mhs_ujian->m_ujian->tampilkan_hasil && $mhs_ujian->m_ujian->tampilkan_jawaban)){ // CHECK JIKA BOLEH MENAMPILKAN HASIL DAN JAWABAN
		// 		show_404();
		// 	}
		// }

		if(in_group('mahasiswa')){
			if(!$mhs_ujian->m_ujian->tampilkan_hasil){ // CHECK JIKA BOLEH MENAMPILKAN HASIL
				show_404();
			}
		}
		
		$h_ujian = $mhs_ujian->h_ujian()->where('ujian_selesai', 'Y')->first(); // RELASI 1 - 1
		$h_ujian_history = $mhs_ujian->h_ujian_history; // RELASI 1 - N
		
		if(empty($h_ujian) && $h_ujian_history->isEmpty()){ // CHECK JIKA MEMANG SUDAH MEMILIKI HASIL UJIAN
			show_404();
		}

		$data['mhs'] = $mhs_ujian->mhs;
		$data['m_ujian'] = $mhs_ujian->m_ujian;
		$data['h_ujian'] = $h_ujian;
		$data['h_ujian_history'] = $h_ujian_history;

		/**[START] PREPARE DATA FOR CHART*/
		$label_and_data = collect();

		if($h_ujian_history->isNotEmpty()){
			$label_and_data = $h_ujian_history->pluck('nilai_bobot_benar', 'ujian_ke');
		}

		
		if(!empty($h_ujian)){
			$label_and_data->put($label_and_data->count() + 1, $h_ujian->nilai_bobot_benar);
		}
		
		$chart_label_and_data_array = [];
		$label_and_data->each(function ($item, $key) use(&$chart_label_and_data_array){
			$data = [
				'ujian_ke' => $key,
				'nilai_bobot_benar' => $item,
			];
			$chart_label_and_data_array[] = $data;
		});
		
		$chart_label_and_data = collect($chart_label_and_data_array);
		$data['chart_label_and_data'] = $chart_label_and_data;
		/**[START] PREPARE DATA FOR CHART*/

		view('hasilujian/history', $data);

	}

	protected function _submit_nilai_essay(){
		$this->_akses_admin_dan_dosen();

		$id = $this->input->post('id');
		$nilai = $this->input->post('nilai');

		$user = $this->ion_auth->user()->row();

		$jawaban_ujian = Jawaban_ujian_orm::findOrFail($id);
		if($jawaban_ujian->soal->tipe_soal != TIPE_SOAL_ESSAY){
			show_error('Jenis soal tidak valid', 500, 'Perhatian');
		}
		try{
			begin_db_trx();
			$jawaban_ujian->nilai_essay = $nilai;
			$jawaban_ujian->penilai_essay = $user->username;
			$jawaban_ujian->waktu_menilai_essay = date('Y-m-d H:i:s');
			$jawaban_ujian->save();

			$h_ujian = $jawaban_ujian->h_ujian;

			if ($h_ujian->ujian_selesai != 'Y') {
				throw new Exception('Ujian belum diakhiri');
			}
	
			$jumlah_benar = 0;
			$jumlah_salah = 0;
			$total_bobot  = 0;
			$total_bobot_benar  = 0;
			$jumlah_soal  = $h_ujian->jawaban_ujian->count();
	
			$topik_ujian_nilai_bobot = [];
	
			foreach ($h_ujian->jawaban_ujian as $jwb) {
				if (!isset($topik_ujian_nilai_bobot[$jwb->soal->topik_id])) {
					$topik_ujian_nilai_bobot[$jwb->soal->topik_id] = 0;
				}
	
				if($jwb->soal->tipe_soal == TIPE_SOAL_MCSA){
					// INI HANYA UNTUK JENIS MCSA
					if(!$jwb->soal->is_bobot_per_jawaban){
						$total_bobot = $total_bobot + ($jwb->soal->bobot_soal->nilai * $jwb->soal->topik->poin_topik);
						if ($jwb->jawaban == $jwb->soal->jawaban) {
							$jumlah_benar++;
							$bobot_poin = ($jwb->soal->bobot_soal->nilai * $jwb->soal->topik->poin_topik);
							$total_bobot_benar = $total_bobot_benar + $bobot_poin;
							$topik_ujian_nilai_bobot[$jwb->soal->topik_id] = $topik_ujian_nilai_bobot[$jwb->soal->topik_id] + $bobot_poin;
						} else {
							$jumlah_salah++;
						}
					}else{
						if(!empty($jwb->jawaban)){
							$jumlah_benar++;
	
							$abj = strtolower($jwb->jawaban);
							$opsi_bobot = 'opsi_'. $abj .'_bobot' ;
	
							$nilai_opsi_bobot = $jwb->soal->$opsi_bobot;
							$bobot_poin = ($nilai_opsi_bobot * $jwb->soal->topik->poin_topik);
							$total_bobot_benar = $total_bobot_benar + $bobot_poin;
							$topik_ujian_nilai_bobot[$jwb->soal->topik_id] = $topik_ujian_nilai_bobot[$jwb->soal->topik_id] + $bobot_poin;
						}
						else
							$jumlah_salah++;
					}
				}elseif($jwb->soal->tipe_soal == TIPE_SOAL_MCMA){
					// INI HANYA UNTUK JENIS MCMA
					// $total_bobot = $total_bobot + ($jwb->soal->bobot_soal->nilai * $jwb->soal->topik->poin_topik);
					$jawaban_mcma = [];
					if(!empty($jwb->jawaban_mcma))
						$jawaban_mcma = json_decode($jwb->jawaban_mcma);
	
					if(!$jwb->soal->is_bobot_per_jawaban){
						$total_bobot = $total_bobot + ($jwb->soal->bobot_soal->nilai * $jwb->soal->topik->poin_topik);
						$jawaban_mcma_kunci = json_decode($jwb->soal->jawaban);
						if(empty(array_diff($jawaban_mcma, $jawaban_mcma_kunci)) && empty(array_diff($jawaban_mcma_kunci, $jawaban_mcma))){
							$jumlah_benar++;
							$bobot_poin = ($jwb->soal->bobot_soal->nilai * $jwb->soal->topik->poin_topik);
							$total_bobot_benar = $total_bobot_benar + $bobot_poin;
							$topik_ujian_nilai_bobot[$jwb->soal->topik_id] = $topik_ujian_nilai_bobot[$jwb->soal->topik_id] + $bobot_poin;
						} else {
							$jumlah_salah++;
						}
					}else{
						if(!empty($jawaban_mcma)){
							$jumlah_benar++;
							foreach($jawaban_mcma as $ABJ){
								$abj = strtolower($ABJ);
								$opsi_bobot = 'opsi_'. $abj .'_bobot' ;
	
								$nilai_opsi_bobot = $jwb->soal->$opsi_bobot;
								$bobot_poin = ($nilai_opsi_bobot * $jwb->soal->topik->poin_topik);
								$total_bobot_benar = $total_bobot_benar + $bobot_poin;
								$topik_ujian_nilai_bobot[$jwb->soal->topik_id] = $topik_ujian_nilai_bobot[$jwb->soal->topik_id] + $bobot_poin;
							}
						}
						else
							$jumlah_salah++;
						
					}
				}elseif($jwb->soal->tipe_soal == TIPE_SOAL_ESSAY){
					if(!empty($jwb->jawaban_essay)){
						$jumlah_benar++;
						$bobot_poin = ($jwb->nilai_essay * ($jwb->soal->bobot_soal->nilai * $jwb->soal->topik->poin_topik)); // BELUM TAHU BAIKNYA DI KALI KAN DENGAN BOBOT SOAL dan TOPIK ATAU TIDAK
						// $bobot_poin = $jwb->nilai_essay ;
						$total_bobot_benar = $total_bobot_benar + $bobot_poin;
						$topik_ujian_nilai_bobot[$jwb->soal->topik_id] = $topik_ujian_nilai_bobot[$jwb->soal->topik_id] + $bobot_poin;
					}else
						$jumlah_salah++;
				}
			}
			$nilai       = ($jumlah_benar / $jumlah_soal) * 100;
			$nilai_bobot_benar = $total_bobot_benar;

			$h_ujian->jml_benar =  $jumlah_benar;
			$h_ujian->jml_salah =  $jumlah_salah;
			$h_ujian->jml_soal =  $jumlah_soal;
			$h_ujian->nilai     =  round($nilai, 2);
			$h_ujian->nilai_bobot = 0;
			$h_ujian->nilai_bobot_benar     =  round($nilai_bobot_benar, 2);
			$h_ujian->total_bobot     =  round($total_bobot, 2);
			$h_ujian->detail_bobot_benar     =  json_encode($topik_ujian_nilai_bobot);
			$h_ujian->save();

			commit_db_trx();

			$this->_json(['status' => 'ok']);

		}catch(Exception $e){
			rollback_db_trx();
			show_error($e->getMessage(), 500, 'Perhatian');
		}

	}
	
}
