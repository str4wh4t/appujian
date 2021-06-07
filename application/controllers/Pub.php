<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Orm\Mhs_orm;
use Orm\Mhs_source_orm;
use Orm\Mhs_ujian_orm;
use Orm\Mhs_matkul_orm;
use Orm\Hujian_orm;
use Orm\Mujian_orm;
use Orm\Topik_orm;
use Orm\Matkul_orm;
use Orm\Soal_orm;
use Orm\Jawaban_ujian_orm;
use Orm\Trx_midtrans_orm;
use Orm\Data_daerah_orm;
use GuzzleHttp\Client;
use Carbon\Carbon;

class Pub extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->library('socket');

	}
	
	public function index(){
		show_404();
	}
	
	// 12020070007-d7d657c0-5db1-3bd0-90b8-a6a929ba9dd0
	public function cetak_sertifikat($nim, $ujian_id_uuid)
	{
		$mhs = Mhs_orm::where('nim', $nim)->firstOrFail();
		$ujian_id = integer_read_from_uuid($ujian_id_uuid);
		$hasil = Hujian_orm::where(['mahasiswa_id' => $mhs->id_mahasiswa, 'ujian_id' => $ujian_id])->firstOrFail();
		$mhs 	= $hasil->mhs;
//		$hasil 	= $ujian;
		$ujian 	= $hasil->m_ujian;
		
		$detail_bobot_benar = json_decode($hasil->detail_bobot_benar);
		$detail_ujian = [];
		foreach($detail_bobot_benar as $topik_id => $nilai){
			$nm_topik = strtoupper(Topik_orm::findOrFail($topik_id)->nama_topik);
			$detail_ujian[$nm_topik] = $nilai ;
		}
//		vdebug($detail_ujian);

		$data = [
			'ujian' => $ujian,
			'hasil' => $hasil,
			'mhs'	=> $mhs,
			'detail_ujian' => $detail_ujian
		];

		$this->load->view('hasilujian/cetak_sertifikat', $data);
		
	}
	
	public function c_user(){
		if(!is_cli()) show_404();

		die('DISABLED');; /* <--- DISABLED FUNCTION */

		$data_mhs = Mhs_orm::all();
		foreach($data_mhs as $data) {
			$nama       = explode(' ', $data->nama, 2);
			$first_name = $nama[0];
			$last_name  = end($nama);
			$full_name  = $data->nama;
			
			$username        = $data->nim;
			$password        = date("dmY", strtotime($data->tgl_lahir));
			$email           = $data->email;
			$tgl_lahir       = date("dmY", strtotime($data->tgl_lahir));
			$additional_data = [
				'first_name' => $first_name,
				'last_name'  => $last_name,
				'full_name'  => $full_name,
				'tgl_lahir'  => $tgl_lahir,
			];
			
			$group = [ MHS_GROUP_ID ]; // Sets user to mhs.
			
			if ($this->ion_auth->username_check($username)) {
//				$data = [
//					'status' => FALSE,
//					'msg'    => 'Username tidak tersedia (sudah digunakan).'
//				];
				echo "[SKIPED]" . $data->nama ;
				echo "\n";
			}
//			elseif ($this->ion_auth->email_check($email)) {
//				$data = [
//					'status' => FALSE,
//					'msg'    => 'Email tidak tersedia (sudah digunakan).'
//				];
//			}
			else {
				$this->ion_auth->register($username, $password, $email, $additional_data, $group);
				echo "[DONE]" . $data->nama ;
				echo "\n";
//				$data = [
//					'status' => TRUE,
//					'msg'    => 'User berhasil dibuat. No Peserta digunakan sebagai password pada saat login.'
//				];
			}
		}
		die('DONE');
	}
	
	public function asign_prodi(){
		if(!is_cli()) show_404();

		die('DISABLED');; /* <--- DISABLED FUNCTION */

		$mhs_source = Mhs_source_orm::all();
		$i = 0;
		foreach($mhs_source as $m){
			$mhs = Mhs_orm::find($m->id_mahasiswa);
			if(!empty($mhs)){
				$mhs->kodeps = $m->kodeps;
				$mhs->prodi =  $m->prodi;
				$mhs->jalur = 'IUP';
				$mhs->gel = 2;
				$mhs->tahun = 2020;
				if($mhs->save())
					echo "[UPDATE]" . $mhs->nama ;
				else
					echo "[GAGAL]" . $mhs->nama ;
			}else{
				echo "[NOTFOUND]" . $m->id_mahasiswa ;
				die;
			}
			echo "\n";
			$i++;
		}
		die('DONE, j = ' . $i);
	}
	
	public function perbaiki(){
		if(!is_cli()) show_404();

		die('DISABLED');; /* <--- DISABLED FUNCTION */

		$mhs_list = Mhs_orm::whereNotNull('no_billkey')->get();
		$i = 0;
		foreach($mhs_list as $mhs){
				$mhs->jalur = 'IUP';
				$mhs->gel = 2;
				$mhs->tahun = 2020;
				if($mhs->save())
					echo "[UPDATE]" . $mhs->nama ;
				else
					echo "[GAGAL]" . $mhs->nama ;
				echo "\n";
				$i++;
		}
		die('DONE, jml diproses = ' . $i);
	}
	
	public function asign_ujian(){
		if(!is_cli()) show_404();

		die('DISABLED');; /* <--- DISABLED FUNCTION */

		$mhs_list = Mhs_orm::whereNotNull('no_billkey')->get();
		$i = 0;
		$matkul_id = 25;
		$ujian_id = 54;
		foreach($mhs_list as $m){
			$mhs_matkul = Mhs_matkul_orm::where(['mahasiswa_id' => $m->id_mahasiswa, 'matkul_id' => $matkul_id])->first();
			if(!empty($mhs_matkul)){
				$mhs_ujian = Mhs_ujian_orm::where(['mahasiswa_matkul_id' => $mhs_matkul->id, 'ujian_id' => $ujian_id])->first();
				if(empty($mhs_ujian)){
					$mhs_ujian = new Mhs_ujian_orm();
					$mhs_ujian->mahasiswa_matkul_id = $mhs_matkul->id;
					$mhs_ujian->ujian_id =  $ujian_id;
					if($mhs_ujian->save())
						echo "[UPDATE]" . $m->nama ;
					else {
						echo "[GAGAL]" . $m->nama;
						die;
					}
					echo "\n";
					$i++;
				}
			}
		}
		die('DONE, jml diproses = ' . $i);
	}
	
	
	public function socket($port){
		if(!is_cli()) show_404();

		$this->socket->run($port);
	}
	
	public function cron_auto_start_ujian_for_unstarted_participants(){
		if(!is_cli()) show_404();
		
		$mhs_ujian_orm = new Mhs_ujian_orm();
		$m_ujian_orm = new Mujian_orm();
		$soal_orm = new Soal_orm();

		// if($app_id == 'ujian'){
	    //     $mhs_ujian_orm->setConnection('ujian');
	    //     $m_ujian_orm->setConnection('ujian');
	    //     $soal_orm->setConnection('ujian');
		// }
        // else{
	    //     $mhs_ujian_orm->setConnection('cat');
	    //     $m_ujian_orm->setConnection('cat');
	    //     $soal_orm->setConnection('cat');
	    // }
		
		$mhs_ujian_list = $mhs_ujian_orm->whereDoesntHave('h_ujian')->orderBy('id')->get();
		
		if($mhs_ujian_list->isNotEmpty()) {
			$cron_end = date("Y-m-d H:i:s", strtotime("+1 minutes"));
			foreach ($mhs_ujian_list as $mu) {

				$today = date('Y-m-d H:i:s');
				if($today > $cron_end){
					die('Waktu cron habis');
				}

				
				$m_ujian = $m_ujian_orm->where(['id_ujian' => $mu->ujian_id, 'status_ujian' => 1])->first();
				
				if(empty($m_ujian)){
					echo 'break, status not active';
					break;
				}
				
				if(empty($m_ujian->terlambat)){
					continue;
				}
				
				$date_end = date('Y-m-d H:i:s', strtotime($m_ujian->terlambat));
				if ($today < $date_end){
					continue;
				}
				
				// echo 'ID : Mujian : ' . $mu->ujian_id . "\n";
				// echo 'today : ' . $today . "\n";
				// echo 'date_end : ' . $date_end . "\n";

				try {
					$soal 		= [];
					$soal_topik = [];
					$i = 0;
					foreach($m_ujian->topik_ujian as $topik_ujian){
						$jumlah_soal_diset = $topik_ujian->jumlah_soal;
						$soal_avail = $soal_orm->where('topik_id', $topik_ujian->topik_id)
												->where('bobot_soal_id', $topik_ujian->bobot_soal_id);

						$filter_data = [
							'gel' 		=> $m_ujian->soal_gel,
							'smt' 		=> $m_ujian->soal_smt,
							'tahun' 	=> $m_ujian->soal_tahun,
						];
			
						$filter = [];
		
						foreach ($filter_data as $key => $v) {
							if (!empty($v)) {
								$filter[$key] = $v;
							}
						}
						
						if (!empty($filter)){
							$soal_avail->where($filter);
						}

						$soal_avail = $soal_avail->get();

						if ($jumlah_soal_diset > $soal_avail->count()) {
							die('Jumlah soal tidak memenuhi untuk ujian');
						}
		
						foreach($soal_avail as $s){
							if($i < $jumlah_soal_diset){
								$soal_topik[] = $s;
								$i++;
							}else{
								break;
							}
						}
						
						if($m_ujian->jenis == 'acak'){
							shuffle($soal_topik);
						}
						
						$soal[$topik_ujian->topik_id][$topik_ujian->bobot_soal_id] = $soal_topik;
						$soal_topik = [];
						$i = 0;
					}
					
					begin_db_trx();
					$h_ujian_orm = new Hujian_orm();

					// if($app_id == 'ujian')
				    //     $h_ujian_orm->setConnection('ujian');
			        // else
				    //     $h_ujian_orm->setConnection('cat');
				  
					$h_ujian_orm->ujian_id = $m_ujian->id_ujian;
					// $h_ujian_orm->mahasiswa_id = $mu->mhs_matkul->mahasiswa_id;
					$h_ujian_orm->mahasiswa_id = $mu->mahasiswa_id;
					$h_ujian_orm->mahasiswa_ujian_id = $mu->id;
					$h_ujian_orm->jml_soal = $m_ujian->jumlah_soal;
					$h_ujian_orm->jml_benar = 0;
					$h_ujian_orm->jml_salah = 0;
					$h_ujian_orm->nilai = 0;
					$h_ujian_orm->nilai_bobot_benar = 0;
					$h_ujian_orm->total_bobot = 0;
					$h_ujian_orm->nilai_bobot = 0;
					$h_ujian_orm->tgl_mulai = $m_ujian->terlambat;
					$h_ujian_orm->tgl_selesai = $m_ujian->terlambat;
					$h_ujian_orm->ujian_selesai = 'N';
					$h_ujian_orm->save();

					echo 'MHS ID : ' . $mu->mahasiswa_id . " ==> STARTED " . "\n";
					// echo 'today : ' . $today . "\n";
					// echo 'date_end : ' . $date_end . "\n";
	
					foreach($soal as $topik_id => $t){
						foreach($t as $bobot_soal_id => $d) {
							foreach ($d as $s) {
								$jawaban_ujian_orm = new Jawaban_ujian_orm();

								// if($app_id == 'ujian')
							    //     $jawaban_ujian_orm->setConnection('ujian');
						        // else
							    //     $jawaban_ujian_orm->setConnection('cat');
							    
								$jawaban_ujian_orm->ujian_id = $h_ujian_orm->id;
								$jawaban_ujian_orm->soal_id  = $s->id_soal;
								$jawaban_ujian_orm->save();
							}
						}
					}
					
					commit_db_trx();
					$action = true;
				} catch(Exception $e){
					rollback_db_trx();
					$action = false;
					die($e);
			    }
			}
		}
	}
	
	public function cron_auto_close(){
		if(!is_cli()) show_404();
		
		$h_ujian_list = new Hujian_orm();

		// if($app_id == 'ujian')
	    //     $h_ujian_list->setConnection('ujian');
        // else
	    //     $h_ujian_list->setConnection('cat');

		// $cron_end = date("Y-m-d H:i:s", strtotime("+1 minutes"));

		$dt_1 = Carbon::now();
		$cron_end = $dt_1->addMinutes(1);

		$h_ujian_list = $h_ujian_list->where('ujian_selesai', 'N')->get();
		if($h_ujian_list->isNotEmpty()){
			foreach($h_ujian_list as $h_ujian){

				// $now = date('Y-m-d H:i:s');

				$now = Carbon::now();

				// if($now > $cron_end){
				// 	die('Waktu cron habis');
				// }

				if($now->greaterThan($cron_end)){
					die('Waktu cron habis');
				}

				// $date_end = date('Y-m-d H:i:s', strtotime($h_ujian->m_ujian->terlambat));
				
				// $date_end = date('Y-m-d H:i:s', strtotime($h_ujian->tgl_selesai));

				$date_end = Carbon::createFromFormat('Y-m-d H:i:s', $h_ujian->tgl_selesai);

				// if ($now >= $date_end){
				if ($now->greaterThan($date_end)){
					echo $h_ujian->id . "\n";
					echo $h_ujian->mhs->nama . "\n";
					if($this->submit_ujian($h_ujian)){
//						$ws = new Chat();
//						$ws->send_msg_stop_ujian($h_ujian->mhs->nim, $app_id . '.undip.ac.id');
//						$cmd = 'wscat -c '. ws_url() .' -x  {\"cmd\":\"MHS_STOP_UJIAN\",\"nim\":\"'. $h_ujian->mhs->nim .'\",\"app_id\":\"'. $app_id . '.undip.ac.id' .'\"}';
//						exec($cmd);
						$cmd = '{"cmd":"MHS_STOP_UJIAN","nim":"'. $h_ujian->mhs->nim .'","app_id":"'. APP_ID .'"}';
						$this->socket->notif_ws($cmd);
						echo "DONE" ;
					}else{
						echo "SKIP" ;
					}
				}
			}
		}
		return;
	}
	
	public function submit_ujian($h_ujian){
		if(!is_cli()) show_404();

		$this->load->model('Ujian_model', 'ujian');
		$this->load->model('Master_model', 'master');
		// $id_h_ujian = $h_ujian->id;
		
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
			if(!isset($topik_ujian_nilai_bobot[$jwb->soal->topik_id])){
				$topik_ujian_nilai_bobot[$jwb->soal->topik_id] = 0 ;
			}
			$total_bobot = $total_bobot + ($jwb->soal->bobot_soal->nilai * $jwb->soal->topik->poin_topik);
			if($jwb->jawaban == $jwb->soal->jawaban){
				$jumlah_benar++;
				$bobot_poin = ($jwb->soal->bobot_soal->nilai * $jwb->soal->topik->poin_topik);
				$total_bobot_benar = $total_bobot_benar + $bobot_poin;
				$topik_ujian_nilai_bobot[$jwb->soal->topik_id] = $topik_ujian_nilai_bobot[$jwb->soal->topik_id] + $bobot_poin;
			}else{
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
//		$h_ujian->nilai     =  number_format(floor($nilai), 0);
		$h_ujian->nilai     =  round($nilai, 2);
//		$h_ujian->nilai_bobot = $nilai_bobot;
		$h_ujian->nilai_bobot = 0;
		$h_ujian->nilai_bobot_benar     =  round($nilai_bobot_benar, 2);
		$h_ujian->total_bobot     =  round($total_bobot, 2);
		$h_ujian->detail_bobot_benar     =  json_encode($topik_ujian_nilai_bobot);
		$h_ujian->tgl_selesai =  $h_ujian->tgl_selesai; //date('Y-m-d H:i:s');
		$h_ujian->ujian_selesai    =  'Y';
		$h_ujian->ended_by =  'cron';
		$action = $h_ujian->save();
		
		return $action;
		
	}
	
	public function fix_nilai(){
		if(!is_cli()) show_404();

		die; // FITUR DISABLED
		
		$h_ujian_list = new Hujian_orm();
		
		// if($app_id == 'ujian')
	    //     $h_ujian_list->setConnection('ujian');
        // else
	    //     $h_ujian_list->setConnection('cat');

		$h_ujian_list = $h_ujian_list->where('ujian_selesai', 'Y')->where('fixed_nilai', '0')->orderBy('id')->get();
		
		if($h_ujian_list->isNotEmpty()) {
			foreach ($h_ujian_list as $h_ujian) {
			
//				if( $h_ujian->id != '47'){
//					continue;
//				}
				
				echo $h_ujian->id ;
//				echo $h_ujian->mhs->nama . "\n";
				
				$this->load->model('Ujian_model', 'ujian');
				$this->load->model('Master_model', 'master');
				// $id_h_ujian = $h_ujian->id;
				
				// Get Jawaban
				// $list_jawaban = $this->ujian->getJawaban($id_h_ujian);
				
				// Pecah Jawaban
				$pc_jawaban = $h_ujian->jawaban_ujian;
				
				$jumlah_benar = 0;
				$jumlah_salah = 0;
				//			$jumlah_ragu  = 0;
				//			$nilai_bobot  = 0;
				$total_bobot       = 0;
				$total_bobot_benar = 0;
				$jumlah_soal       = count($pc_jawaban);
				
				$topik_ujian_nilai_bobot = [];
				
				foreach ($pc_jawaban as $jwb) {
					if (!isset($topik_ujian_nilai_bobot[$jwb->soal->topik_id])) {
						$topik_ujian_nilai_bobot[$jwb->soal->topik_id] = 0;
					}
					$total_bobot = $total_bobot + ($jwb->soal->bobot_soal->nilai * $jwb->soal->topik->poin_topik);
					if ($jwb->jawaban == $jwb->soal->jawaban) {
						$jumlah_benar++;
						$bobot_poin                                    = ($jwb->soal->bobot_soal->nilai * $jwb->soal->topik->poin_topik);
						$total_bobot_benar                             = $total_bobot_benar + $bobot_poin;
						$topik_ujian_nilai_bobot[$jwb->soal->topik_id] = $topik_ujian_nilai_bobot[$jwb->soal->topik_id] + $bobot_poin;
					} else {
						$jumlah_salah++;
					}
				}
				
				$nilai             = ($jumlah_benar / $jumlah_soal) * 100;
				$nilai_bobot_benar = $total_bobot_benar;
				//			$total_bobot_benar = $total_bobot;
				$nilai_bobot = ($total_bobot / $jumlah_soal) * 100;
				
				
				$h_ujian->jml_benar = $jumlah_benar;
				$h_ujian->jml_salah = $jumlah_salah;
				$h_ujian->jml_soal  = $jumlah_soal;
				//		$h_ujian->nilai     =  number_format(floor($nilai), 0);
				$h_ujian->nilai = round($nilai, 2);
				//		$h_ujian->nilai_bobot = $nilai_bobot;
				$h_ujian->nilai_bobot        = 0;
				$h_ujian->nilai_bobot_benar  = round($nilai_bobot_benar, 2);
				$h_ujian->total_bobot        = round($total_bobot, 2);
				$h_ujian->detail_bobot_benar = json_encode($topik_ujian_nilai_bobot);
//				$h_ujian->tgl_selesai        = date('Y-m-d H:i:s');
//				$h_ujian->ujian_selesai      = 'Y';
//				$h_ujian->ended_by           = 'cron';
				$h_ujian->fixed_nilai        = 1;
				$action                      = $h_ujian->save();
				
				echo 'done' . "\n";
				
				// return $action;
			}
		}
		
	}

	public function notify_midtrans(){

		$input = file_get_contents('php://input');
		if(empty($input))
			show_404();

		$this->load->model('payment_model');

		\Midtrans\Config::$isProduction = MIDTRANS_IS_PRODUCTION;
		\Midtrans\Config::$serverKey = MIDTRANS_SERVER_KEY;
		$notif = new \Midtrans\Notification('php://input');

		$log_status = $this->payment_model->exec_payment($notif, 'midtrans') ;

		$trx_midtrans = new Trx_midtrans_orm();
		
		$trx_midtrans->transaction_id = $notif->transaction_id;
		$trx_midtrans->transaction_status = $notif->transaction_status;
		$trx_midtrans->transaction_time = $notif->transaction_time;
		$trx_midtrans->status_code = $notif->status_code;
		$trx_midtrans->payment_type = $notif->payment_type;
		$trx_midtrans->order_id = $notif->order_id;
		$trx_midtrans->fraud_status = $notif->fraud_status;
		$trx_midtrans->gross_amount = $notif->gross_amount;
		$trx_midtrans->signature_key = $notif->signature_key;
		$trx_midtrans->is_settlement_processed = $notif->transaction_status == 'settlement' ? 1 : 0;

		$trx_midtrans->log_status = $log_status ;

        $trx_midtrans->save();

	}

	// public function cron_trx_midtrans(){
	// 	if(!is_cli()) show_404();

	// 	$trx_midtrans_list = Trx_midtrans_orm::where(['transaction_status' => 'settlement', 'is_settlement_processed' => 0])->get();
	// 	if($trx_midtrans_list->isNotEmpty()){
	// 		$cron_end = date("Y-m-d H:i:s", strtotime("+1 minutes"));
	// 		foreach($trx_midtrans_list as $trx_midtrans){
	// 			$today = date('Y-m-d H:i:s');
	// 			if($today > $cron_end){
	// 				break;
	// 			}

	// 			$order_id = $trx_midtrans->order_id;
		
	// 			$trx_payment = Trx_payment_orm::where('order_number', $order_id)->where('stts', PAYMENT_ORDER_BELUM_DIPROSES)->firstOrFail();
		
	// 			$info = explode('-', $order_id);
	// 			$username = $info[0] ; // 
		
	// 			$mhs = Mhs_orm::where('nim', $username)->firstOrFail();
		
	// 			$membership_history_id = null ;
	// 			$paket_history_id = null ;

	// 			try {
	// 				begin_db_trx();

	// 				if(substr($info[1], 0, 1) == 'M'){
	// 					// JIKA PEMBELIAN MEMBERSHIP
	// 					$membership_id = substr($info[1], 1);
	
	// 					$membership = Membership_orm::findOrFail($membership_id);
			
	// 					$where = [
	// 						'mahasiswa_id' => $mhs->id_mahasiswa,
	// 						'stts' => MEMBERSHIP_STTS_AKTIF,
	// 					];
	// 					$membership_history_before = Membership_history_orm::where($where)->first();
	// 					$membership_sisa_kuota_latihan_soal = 0;
	// 					$membership_expiration_date = date('Y-m-d', strtotime("-1 days", strtotime(date('Y-m-d'))));
	
	// 					if(!empty($membership_history_before)){
	// 						$membership_history_before->stts = MEMBERSHIP_STTS_NON_AKTIF ;
	
	// 						$membership_expiration_date = !empty($membership_history_before->expired_at) ? $membership_history_before->expired_at : $membership_expiration_date;
	// 						$membership_sisa_kuota_latihan_soal = $membership_history_before->sisa_kuota_latihan_soal;
							
	// 						$membership_history_before->save();
	// 					}
			
	// 					$where = [
	// 						'mahasiswa_id' => $mhs->id_mahasiswa,
	// 						// 'membership_id' => $membership->id,
	// 					];
				
	// 					$membership_count = Membership_history_orm::where($where)->get()->count();
	
	// 					$sisa_kuota_latihan_soal = 0;
	// 					$expired_at  = null;
			
	// 					if($membership->is_limit_by_kuota)
	// 						$sisa_kuota_latihan_soal = $membership_sisa_kuota_latihan_soal + $membership->kuota_latian_soal ;
			
	// 					if($membership->is_limit_by_durasi){
	// 						$today = date('Y-m-d');
	
	// 						if($today > $membership_expiration_date){
	// 							$expired_at = date('Y-m-d', strtotime("+". $membership->durasi ." months", strtotime(date('Y-m-d'))));
	// 						}else{
	// 							$expired_at = date('Y-m-d', strtotime("+". $membership->durasi ." months", strtotime($membership_expiration_date)));
	// 						}
	// 					}
				
	// 					$membership_history = new Membership_history_orm();
	// 					$membership_history->mahasiswa_id = $mhs->id_mahasiswa;
	// 					$membership_history->membership_id = $membership_id ;
	// 					$membership_history->upgrade_ke = $membership_count++ ;
	// 					$membership_history->sisa_kuota_latihan_soal = $sisa_kuota_latihan_soal ;
	// 					$membership_history->expired_at = $expired_at ;
	// 					$membership_history->stts =  MEMBERSHIP_STTS_AKTIF ;
	// 					$membership_history->save();
			
	// 					$membership_history_id = $membership_history->id;
	
	// 					// LAST CHANGE MEMBERSHIP IN TABLE USERS
	// 					// $user_beli->membership_id = $membership->id;
	// 					// $user_beli->save();
			
	// 				}
					
	// 				if(substr($info[1], 0, 1) == 'P'){
	// 					// JIKA PEMBELIAN PAKET
	// 					$paket_id = substr($info[1], 1);
	
	// 					$paket = Paket_orm::findOrFail($paket_id);
			
	// 					$where = [
	// 						'mahasiswa_id' => $mhs->id_mahasiswa,
	// 						'stts' => PAKET_STTS_AKTIF,
	// 					];
	// 					$paket_history_before = Paket_history_orm::where($where)->first();
	// 					$paket_sisa_kuota_latihan_soal = 0;
	// 					$paket_expiration_date = date('Y-m-d', strtotime("-1 days", strtotime(date('Y-m-d'))));
	
	// 					if(!empty($paket_history_before)){
	// 						$paket_history_before->stts = PAKET_STTS_NON_AKTIF ;
	
	// 						$paket_expiration_date = !empty($paket_history_before->expired_at) ? $paket_history_before->expired_at : $paket_expiration_date ;
	// 						$paket_sisa_kuota_latihan_soal = $paket_history_before->sisa_kuota_latihan_soal;
							
	// 						$paket_history_before->save();
	// 					}
			
	// 					$where = [
	// 						'mahasiswa_id' => $mhs->id_mahasiswa,
	// 						// 'paket_id' => $paket->id,
	// 					];
				
	// 					$paket_count = Paket_history_orm::where($where)->get()->count();
	
	// 					$sisa_kuota_latihan_soal = 0;
	// 					$expired_at  = null;
			
	// 					if($paket->is_limit_by_kuota)
	// 						$sisa_kuota_latihan_soal = $paket_sisa_kuota_latihan_soal + $paket->kuota_latian_soal ;
			
	// 					if($paket->is_limit_by_durasi){
	// 						$today = date('Y-m-d');
	
	// 						if($today > $paket_expiration_date){
	// 							$expired_at = date('Y-m-d', strtotime("+". $paket->durasi ." months", strtotime(date('Y-m-d'))));
	// 						}else{
	// 							$expired_at = date('Y-m-d', strtotime("+". $paket->durasi ." months", strtotime($paket_expiration_date)));
	// 						}
	// 					}
				
	// 					$paket_history = new Paket_history_orm();
	// 					$paket_history->mahasiswa_id = $mhs->id_mahasiswa;
	// 					$paket_history->paket_id = $paket_id ;
	// 					$paket_history->upgrade_ke = $paket_count++ ;
	// 					$paket_history->sisa_kuota_latihan_soal = $sisa_kuota_latihan_soal ;
	// 					$paket_history->expired_at = $expired_at ;
	// 					$paket_history->stts =  PAKET_STTS_AKTIF ;
	// 					$paket_history->save();
			
	// 					$paket_history_id = $paket_history->id;
	
	// 					// ASSIGN USER TO MHS_MATKUL BERDASARKAN PAKET DIBELI
	// 					// $mhs = Mhs_orm::where('nim', $user_beli->username)->firstOrFail();
	// 					foreach($paket->matkul as $matkul){
	// 						$mhs_matkul = new Mhs_matkul_orm();
	// 						$mhs_matkul->mahasiswa_id = $mhs->id_mahasiswa;
	// 						$mhs_matkul->matkul_id = $matkul->id_matkul;
	// 						$mhs_matkul->save();
							
	// 						if($matkul->m_ujian->isNotEmpty()){
	// 							foreach($matkul->m_ujian as $m_ujian){
	// 								$mhs_ujian = new Mhs_ujian_orm();
	// 								$mhs_ujian->mahasiswa_matkul_id = $mhs_matkul->id;
	// 								$mhs_ujian->ujian_id = $m_ujian->id_ujian;
	// 								$mhs_ujian->save();
	// 							}
	// 						}
	// 					}
			
	// 				}
			
	// 				$trx_payment->stts = PAYMENT_ORDER_TELAH_DIPROSES;
	// 				$trx_payment->membership_history_id = $membership_history_id;
	// 				$trx_payment->paket_history_id = $paket_history_id;
	// 				$trx_payment->tgl_bayar = $trx_midtrans->transaction_time;
	// 				$trx_payment->save();


	// 				$trx_midtrans->is_settlement_processed = 1 ;
	// 				$trx_midtrans->save();

					
	// 				echo 'sukses' . "\n";
	// 				commit_db_trx();
	// 			} catch (Exception $e) {
	// 				rollback_db_trx();
	// 				echo $trx_midtrans->id . '===>' . $e->getMessage() . "\n";
	// 				break;
	// 			}

	// 		}
	// 	}

	// }

	// public function tes(){

	// 	$order_number = '210331163113-M2-210403-7';
	// 	$term = substr($order_number,0,14);

	// 	$trx_midtrans_before = Trx_midtrans_orm::where('transaction_status', 'pending')
	// 											->where('order_id', 'like', $term . '%')
	// 											->where('is_expire_processed', 0)
	// 											->get();

	// 		if($trx_midtrans_before->isNotEmpty()){
	// 			$client = new Client();
	// 			foreach($trx_midtrans_before AS $trx){
	// 				$order_number = $trx->order_id;
	// 				echo $order_number . " ==> ";
	// 				$res = $client->request('POST', MIDTRANS_API_URL . $order_number . '/expire', [
	// 					'auth' => [MIDTRANS_SERVER_KEY, '']
	// 				]);
	// 				$trx->is_expire_processed = 1;
	// 				$trx->save();
	// 				echo $res->getBody()->getContents() . "\n";
	// 			}
	// 		}

	// }


	// public function tes2(){
	// 	$client = new Client();
	// 	$order_number = '210331163113-M2-210403-20';
	// 	echo $order_number . " ==> ";
	// 	$res = $client->request('POST', MIDTRANS_API_URL . $order_number . '/expire', [
	// 		'auth' => [MIDTRANS_SERVER_KEY, '']
	// 	]);
	// 	echo $res->getBody()->getContents() . "\n";

	// }

	// public function tes3(){
	// 	$membership_expiration_date = date('Y-m-d', strtotime("-1 days", strtotime(date('Y-m-d'))));
	// 	echo $membership_expiration_date;
	// }

	// public function tes4(){
	// 	$paket = Paket_orm::find(1);
	// 	// vdebug($paket->matkul);
	// 	foreach($paket->matkul as $m){
	// 		if($m->m_ujian->isNotEmpty()){
	// 			foreach($m->m_ujian as $u){
	// 				echo $u->nama_ujian . "\n";
	// 			}
			
	// 		}
	// 	}
	// }

	// public function tes5(){
	// 	// $soal = Soal_orm::find(279);
	// 	// $soal = Soal_orm::find(282);
	// 	$soal = Soal_orm::find(281);
	// 	$img = $soal->soal;

	// 	$doc = new DOMDocument('1.0', 'UTF-8');
	// 	$doc->loadHTML($img);
	// 	// $xpath = new DOMXPath($doc);
	// 	// $src = $xpath->evaluate("img");
	// 	$i = 0 ;
	// 	foreach ($doc->getElementsByTagName('img') as $img_node) {
	// 		$src = $img_node->getAttribute('src') ;
	// 		if(strpos($src, 'data:image/png;base64,') !== false){
	// 			$img = str_replace('data:image/png;base64,', '', $src);
	// 			$img = str_replace(' ', '+', $img);
	// 			$data = base64_decode($img);
	// 			$file = UPLOAD_DIR . $soal->id_soal . '_soal_' . mt_rand() . '.png';
	// 			$success = file_put_contents($file, $data);
	// 			if($success){
	// 				$img_node->setAttribute('src', asset($file)) ;
	// 				$doc->saveHTML($img_node);
	// 			}
	// 			// print $success ? $file : 'Unable to save the file.';
	// 			$i++;
	// 		}
	// 	}
		
	// 	$xpath = new DOMXPath($doc);
	// 	// $body = $xpath->evaluate('string(//body/)');
	// 	// $body = $doc->saveHTML();

	// 	$body = '';
	// 	foreach ($xpath->evaluate('//body/node()') as $node) {
	// 		$body .= $doc->saveHtml($node);
	// 	}

	// 	$soal->soal = $body;
	// 	$soal->save();

	// 	// echo $body;
		
	// 	// vdebug($soal->soal);
	// }

	// public function tes6(){
	// 	$this->load->helper('file');
	// 	$fi = get_filenames(UPLOAD_DIR);
	// 	vdebug($fi);

	// }

	// public function tes7(){
	// 	$membership = Membership_orm::findOrFail(1);
	// 	$paket_bonus_membership  = get_paket_bonus_membership($membership) ;
	// 	if(!empty($paket_bonus_membership)){
	// 		foreach($paket_bonus_membership as $paket){
	// 			foreach($paket->paket_matkul as $paket_matkul){
	// 				echo $paket_matkul->matkul_id . "\n" ;
	// 			}
	// 		}
	// 	}
	// }

	// public function tes8(){
	// 	$user = $this->ion_auth->user()->row();
    //     $user = Users_orm::findOrFail($user->id);
	// 	$mhs = $user->mhs;

	// 	$mhs_matkul_orm = $mhs->mhs_matkul()->where('matkul_id', 20)->first();

	// 	vdebug($mhs_matkul_orm);
	// }

	//	public function coba(){
	//		$cmd = '{"cmd":"MHS_STOP_UJIAN","nim":"22020090007","app_id":"cat.undip.ac.id"}';
	//		$this->socket->notif_ws($cmd);
	//	}
	
	// private function _notif_ws($send_msg = ''){
	// 	Client\connect(ws_url())->then(function($conn) use ($send_msg){
	//         $conn->on('message', function($msg) use ($conn, $send_msg) {
	//             echo "Received: {$msg}\n";
	//             $conn->close();
	//         });
	//         $conn->send($send_msg);
	//     }, function ($e) {
	//         echo "Could not connect: {$e->getMessage()}\n";
	//     });
	// }

	// function tess(){

	// 	// $localIP = getHostByName(getHostName());
	// 	// echo get_client_ip(); 
	// }

	// function tes(){
		
	// 	$mhs = Mhs_orm::find(1000009908);
	// 	$mhs_matkul_orm = $mhs->mhs_matkul()->where('matkul_id', 20)->first();

	// 	vdebug($mhs_matkul_orm);

	// 	// echo $this->config->item('composer_autoload');

	// 	// echo __FILE__ ;
	// }


	public function generate_data_daerah(){
		$api_url_provinsi = 'https://dev.farizdotid.com/api/daerahindonesia/provinsi' ;
		$api_url_kota_kab = 'https://dev.farizdotid.com/api/daerahindonesia/kota?id_provinsi=' ;

		$client = new Client();
        $res = $client->request('GET', $api_url_provinsi);

		$provinsi_list = $res->getBody()->getContents();

		$provinsi_list = json_decode($provinsi_list);

		if(!empty($provinsi_list)){
			Data_daerah_orm::truncate(); // ===> EMPTYING THE TABLE, THIS TABLE HAS NO CONSTRAIT
			foreach($provinsi_list->provinsi as $provinsi){

				$res = $client->request('GET', $api_url_kota_kab . $provinsi->id);

				$kota_kab_list = $res->getBody()->getContents();

				$kota_kab_list = json_decode($kota_kab_list);

				if(!empty($kota_kab_list)){
					foreach($kota_kab_list->kota_kabupaten as $kota_kab){
						echo $kota_kab->nama . ' ====> '; 
						$data_daerah = new Data_daerah_orm();
						$data_daerah->provinsi_id = $provinsi->id ;
						$data_daerah->provinsi = $provinsi->nama ;
						$data_daerah->kota_kab_id = $kota_kab->id;
						$data_daerah->kota_kab = $kota_kab->nama;
						$ret = $data_daerah->save();
						echo ($ret ? 'SUCCESS' : 'FAIL') . "\n" ;
					}
				}

			}
		}

	}

	public function gen_no_urut_soal(){
		$matkul_list = Matkul_orm::all();

		foreach($matkul_list as $matkul){
			foreach($matkul->topik as $topik){
				$soal_list = Soal_orm::where('topik_id', $topik->id)->orderBy('created_at', 'asc')->get();
				$i = 1 ;
				foreach($soal_list as $soal){
					$soal->no_urut = $i ;
					$soal->save();
					echo $i . ' ===> DONE ' . "\n" ;
					$i++ ;
				}
			}
		}
	}

	public function fix_foto_path(){
		$mhs_list = Mhs_orm::all();
		foreach ($mhs_list as $mhs) {
			$str_to_replace = "https://pendaftaran.undip.ac.id/assets/berkas_nfs/fotoprofile" ;
			if(strpos($mhs->foto, $str_to_replace) !== false){
				// $path = 'https://pendaftaran.undip.ac.id/assets/berkas_nfs/fotoprofile/fotoprofile-1.jpg';
				$path = $mhs->foto;
				$foto_path = str_replace($str_to_replace, asset('foto'), $path);
				$mhs->foto = $foto_path;
				$ret = $mhs->save();
				echo $mhs->id_mahasiswa . ' ====> ' ; echo $ret ? "SUCCESS" : "FAIL" ; echo "\n" ;
			} else{
				// echo "Word Not Found!";
			}
		}

	}

	public function fix_img_in_soal(){
		$soal_list = Soal_orm::all();
		foreach($soal_list as $soal){
			$html = $soal->soal;
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
					$file_name = $soal->id_soal .'_soal_'. mt_rand()  .'.png';
					$file = UPLOAD_DIR . $file_name;
					$success = file_put_contents($file, $img_64);
					if($success){
						$file_url =  'uploads/img_soal/' . $file_name ;
						$img_node->setAttribute('src', asset($file_url)) ;
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
				// $html = $this->input->post('jawaban_' . $abj);
				$html = $soal->$opsi;
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
						$file_name = $soal->id_soal .'_jawaban_'. $opsi .'_'. mt_rand()  .'.png';
						$file = UPLOAD_DIR . $file_name;
						$success = file_put_contents($file, $img_64);
						if($success){
							$file_url =  'uploads/img_soal/' . $file_name ;
							$img_node->setAttribute('src', asset($file_url)) ;
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

			$html = $soal->penjelasan;
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
						$file_name = $soal->id_soal .'_penjelasan_'. mt_rand()  .'.png';
						$file = UPLOAD_DIR . $file_name;
						$success = file_put_contents($file, $img_64);
						if($success){
							$file_url =  'uploads/img_soal/' . $file_name ;
							$img_node->setAttribute('src', asset($file_url)) ;
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
			$ret = $soal->save();
			echo $soal->id_soal . ' ====> ' ; echo $ret ? "SUCCESS" : "FAIL" ; echo "\n" ;
			sleep(1);
		}
	}
	
}
