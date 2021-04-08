<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Orm\Mhs_orm;
use Orm\Mhs_source_orm;
use Orm\Mhs_ujian_orm;
use Orm\Mhs_matkul_orm;
use Orm\Hujian_orm;
use Orm\Mujian_orm;
use Orm\Topik_orm;
use Orm\Soal_orm;
use Orm\Jawaban_ujian_orm;
use Orm\Trx_payment_orm;
use Orm\Users_orm;
use Orm\Membership_orm;
use Orm\Membership_history_orm;
use Orm\Paket_orm;
use Orm\Paket_history_orm;
use Orm\Trx_midtrans_orm;
use GuzzleHttp\Client;

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
		show_404(); /* <--- DISABLED FUNCTION */
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
		die('DONE, j = ' . $i);
	}
	
	public function asign_ujian(){
		if(!is_cli()) show_404();
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
		die('DONE, j = ' . $i);
	}
	
	
	public function socket(){
		if(!is_cli()) show_404();
		
		// $wsServer = new WsServer(new Chat());
		
		// $server = IoServer::factory(
		//     new HttpServer(
		//         $wsServer
		//     ),
		//     8080
		// );
		
		// $wsServer->enableKeepAlive($server->loop, 30);
		
		// $server->run();

		$this->socket->run();

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

				echo 'ID : Mujian : ' . $mu->ujian_id . "\n";

				$m_ujian = $m_ujian_orm->where(['id_ujian' => $mu->ujian_id, 'status_ujian' => 1])->first();

				if(empty($m_ujian)){
					echo 'break, status not active';
					break;
				}
				
				$today = date('Y-m-d H:i:s');
				if($today > $cron_end){
					echo 'break';
					break;
				}
				
				$date_end = date('Y-m-d H:i:s', strtotime($m_ujian->terlambat));
				if ($today < $date_end){
					continue;
				}
				
				echo 'today : ' . $today . "\n";
				echo 'date_end : ' . $date_end . "\n";
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
					$h_ujian_orm->mahasiswa_id = $mu->mhs_matkul->mahasiswa_id;
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

		$cron_end = date("Y-m-d H:i:s", strtotime("+1 minutes"));
		$h_ujian_list = $h_ujian_list->where('ujian_selesai', 'N')->get();
		if($h_ujian_list->isNotEmpty()){
			foreach($h_ujian_list as $h_ujian){
				$today = date('Y-m-d H:i:s');
				if($today > $cron_end){
					echo 'break';
					break;
				}
				// $date_end = date('Y-m-d H:i:s', strtotime($h_ujian->m_ujian->terlambat));
				$date_end = date('Y-m-d H:i:s', strtotime($h_ujian->tgl_selesai));
				if ($today >= $date_end){
					echo $h_ujian->id . "\n";
					echo $h_ujian->mhs->nama . "\n";
					if($this->submit_ujian($h_ujian)){
//						$ws = new Chat();
//						$ws->send_msg_stop_ujian($h_ujian->mhs->nim, $app_id . '.undip.ac.id');
//						$cmd = 'wscat -c '. ws_url() .' -x  {\"cmd\":\"MHS_STOP_UJIAN\",\"nim\":\"'. $h_ujian->mhs->nim .'\",\"app_id\":\"'. $app_id . '.undip.ac.id' .'\"}';
//						exec($cmd);
						$cmd = '{"cmd":"MHS_STOP_UJIAN","nim":"'. $h_ujian->mhs->nim .'","app_id":"'. APP_ID . '.undip.ac.id' .'"}';
						$this->socket->notif_ws($cmd);
						echo "DONE" ;
					}else{
						echo "ERROR" ;
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

		// $post = $this->input->post();

		\Midtrans\Config::$isProduction = false;
		\Midtrans\Config::$serverKey = MIDTRANS_SERVER_KEY;
		$notif = new \Midtrans\Notification();

		// $transaction = $notif->transaction_status;
		// $type = $notif->payment_type;
		// $order_id = $notif->order_id;
		// $fraud = $notif->fraud_status;

		// if ($transaction == 'capture') {
		// 	// Untuk transaksi kartu kredit, anda perlu memeriksa apakah transaksi terdapat challenge status dari FDS
		// 	if ($type == 'credit_card'){
		// 		if($fraud == 'challenge'){
		// 			// TODO set payment status in merchant's database to 'Challenge by FDS'
		// 			// TODO merchant should decide whether this transaction is authorized or not in MAP
		// 			echo "Transaction order_id: " . $order_id ." is challenged by FDS";
		// 		}
		// 		else {
		// 			// TODO set payment status in merchant's database to 'Success'
		// 			echo "Transaction order_id: " . $order_id ." successfully captured using " . $type;
		// 		}
		// 	}
		// }
		// else if ($transaction == 'settlement'){
		// 	// TODO set payment status in merchant's database to 'Settlement'
		// 	echo "Transaction order_id: " . $order_id ." successfully transfered using " . $type;
		// }
		// else if($transaction == 'pending'){
		// 	// TODO set payment status in merchant's database to 'Pending'
		// 	echo "Waiting customer to finish transaction order_id: " . $order_id . " using " . $type;
		// }
		// else if ($transaction == 'deny') {
		// 	// TODO set payment status in merchant's database to 'Denied'
		// 	echo "Payment using " . $type . " for transaction order_id: " . $order_id . " is denied.";
		// }
		// else if ($transaction == 'expire') {
		// 	// TODO set payment status in merchant's database to 'expire'
		// 	echo "Payment using " . $type . " for transaction order_id: " . $order_id . " is expired.";
		// }
		// else if ($transaction == 'cancel') {
		// 	// TODO set payment status in merchant's database to 'Denied'
		// 	echo "Payment using " . $type . " for transaction order_id: " . $order_id . " is canceled.";
		// }
		
		$log_status = null ;
		try{
			begin_db_trx();

			$info = explode('-', $notif->order_id);
			$username = $info[0] ; // 
			$mhs = Mhs_orm::where('nim', $username)->firstOrFail();

			if($notif->transaction_status == 'pending'){

				// JIKA TERJADI PEMESANAN

				$trx_payment = Trx_payment_orm::where('order_number', $notif->order_id)->first();

				if(empty($trx_payment)){
					$trx_payment = new Trx_payment_orm();
					$trx_payment->mahasiswa_id = $mhs->id_mahasiswa;
					$trx_payment->order_number = $notif->order_id;
					$trx_payment->stts = PAYMENT_ORDER_BELUM_DIPROSES;
					$trx_payment->tgl_order = $notif->transaction_time;
					$trx_payment->jml_bayar = $notif->gross_amount;

					$keterangan = '';
					if(substr($info[1], 0, 1) == 'M'){
						$keterangan = 'Pembelian membership ' . strtoupper(get_membership_text(substr($info[1], 1))) ;
					}

					if(substr($info[1], 0, 1) == 'P'){
						$paket = Paket_orm::findOrFail(substr($info[1], 1));
						$keterangan = 'Pembelian paket ' . strtoupper($paket->name) ;
					}

					$trx_payment->keterangan = $keterangan;

					$trx_payment->save();
				}

				// SET EXPIRE UNTUK TRX MIDTRANS PENDING SEBELUMNYA JIKA TRX TSB UNTUK MEMBERSHIP

				$term = null ;
				if(substr($info[1], 0, 1) == 'M'){
					$term = substr($notif->order_id, 0, 14); // SAMPE HURUF M
				}
					
				if(substr($info[1], 0, 1) == 'P'){
					$term = $info[0] . '-' . $info[1]; // SETALAH PAKET ID
				}

				$trx_midtrans_before = Trx_midtrans_orm::where('transaction_status', 'pending')
													->where('order_id', 'like', $term . '%')
													->where('is_expire_processed', 0)
													->get();

				if($trx_midtrans_before->isNotEmpty()){
					$client = new Client();
					foreach($trx_midtrans_before AS $trx){
						$order_number = $trx->order_id;
						$client->request('POST', MIDTRANS_API_URL . $order_number . '/expire', [
							'auth' => [MIDTRANS_SERVER_KEY, '']
						]);
						$trx->is_expire_processed = 1;
						$trx->save();
						// echo $res->getBody()->getContents(); die;
					}
				}

			}
			
			if ($notif->transaction_status == 'settlement'){

				// JIKA TERJADI PEMBAYARAN

				$trx_payment = Trx_payment_orm::where('order_number', $notif->order_id)->where('stts', PAYMENT_ORDER_BELUM_DIPROSES)->firstOrFail();

				$membership_history_id = null ;
				$paket_history_id = null ;

				if(substr($info[1], 0, 1) == 'M'){
					// JIKA PEMBELIAN MEMBERSHIP
					$membership_id = substr($info[1], 1);

					$membership = Membership_orm::findOrFail($membership_id);
		
					$where = [
						'mahasiswa_id' => $mhs->id_mahasiswa,
						'stts' => MEMBERSHIP_STTS_AKTIF,
					];
					$membership_history_before = Membership_history_orm::where($where)->first();
					$membership_expiration_date = date('Y-m-d', strtotime("-1 days", strtotime(date('Y-m-d'))));

					if(!empty($membership_history_before)){
						$membership_history_before->stts = MEMBERSHIP_STTS_NON_AKTIF ;

						$membership_expiration_date = !empty($membership_history_before->expired_at) ? $membership_history_before->expired_at : $membership_expiration_date;
						
						$membership_history_before->save();
					}
		
					$where = [
						'mahasiswa_id' => $mhs->id_mahasiswa,
						// 'membership_id' => $membership->id,
					];
			
					$membership_count = Membership_history_orm::where($where)->get()->count();

					$today = date('Y-m-d');

					if($today > $membership_expiration_date){
						$membership_expiration_date = date('Y-m-d', strtotime("+". $membership->durasi ." months", strtotime(date('Y-m-d'))));
					}else{
						$membership_expiration_date = date('Y-m-d', strtotime("+". $membership->durasi ." months", strtotime($membership_expiration_date)));
					}
			
					$membership_history = new Membership_history_orm();
					$membership_history->mahasiswa_id = $mhs->id_mahasiswa;
					$membership_history->membership_id = $membership_id ;
					$membership_history->upgrade_ke = $membership_count++ ;
					// $membership_history->sisa_kuota_latihan_soal = $membership_sisa_kuota_latihan_soal ;
					$membership_history->expired_at = $membership_expiration_date ;
					$membership_history->stts =  MEMBERSHIP_STTS_AKTIF ;
					$membership_history->save();
		
					$membership_history_id = $membership_history->id;

					// ASIGN MEMBERSHIP KE PAKET BONUS NYA 
					$paket_bonus_membership  = get_paket_bonus_membership($membership) ;
					if(!empty($paket_bonus_membership)){
						foreach($paket_bonus_membership as $paket){

							$where = [
								'mahasiswa_id' => $mhs->id_mahasiswa,
								'paket_id' => $paket->id,
								'stts' => PAKET_STTS_AKTIF,
							];
		
							$paket_history_before = Paket_history_orm::where($where)->first();
		
							if(!empty($paket_history_before)){
								$paket_history_before->stts = PAKET_STTS_NON_AKTIF ;
								$paket_history_before->save();
							}

							$where = [
								'mahasiswa_id' => $mhs->id_mahasiswa,
								'paket_id' => $paket->id,
							];
					
							$paket_count = Paket_history_orm::where($where)->get()->count();

							$paket_history = new Paket_history_orm();
							$paket_history->mahasiswa_id = $mhs->id_mahasiswa;
							$paket_history->paket_id = $paket->id ;
							$paket_history->upgrade_ke = $paket_count++ ;
							$paket_history->stts =  PAKET_STTS_AKTIF ;
							$paket_history->save();

							foreach($paket->matkul as $matkul){
								$sisa_kuota_latihan_soal = $paket->kuota_latihan_soal ;
								if(empty($mhs->mhs_matkul()->where('matkul_id', $matkul->id_matkul)->first())){ 
									// CHEK MHS SUDAH DIASIGN MATKUL APA BELUM, BISA JADI KEMUNGKINAN MATKUL TSB DI PILIH DI PAKET YG LAIN
									$mhs_matkul_orm = new Mhs_matkul_orm();
								}else{
									$mhs_matkul_orm = $mhs->mhs_matkul()->where('matkul_id', $matkul->id_matkul)->first();
									$sisa_kuota_latihan_soal = $sisa_kuota_latihan_soal + $mhs_matkul_orm->sisa_kuota_latihan_soal ;
								}
								$mhs_matkul_orm->mahasiswa_id = $mhs->id_mahasiswa;
								$mhs_matkul_orm->matkul_id = $matkul->id_matkul;
								$mhs_matkul_orm->sisa_kuota_latihan_soal = $sisa_kuota_latihan_soal ;
								$mhs_matkul_orm->save();

								if($matkul->m_ujian->isNotEmpty()){
									foreach($matkul->m_ujian as $m_ujian){
										if(empty($mhs_matkul_orm->mhs_ujian()->where('ujian_id', $m_ujian->id_ujian)->first())){
											$mhs_ujian = new Mhs_ujian_orm();
											$mhs_ujian->mahasiswa_matkul_id = $mhs_matkul_orm->id;
											$mhs_ujian->ujian_id = $m_ujian->id_ujian;
											$mhs_ujian->save();
										}
									}
								}
							}
						}
					}

					// LAST CHANGE MEMBERSHIP IN TABLE USERS
					// $user_beli->membership_id = $membership->id;
					// $user_beli->save();
		
				}
				
				if(substr($info[1], 0, 1) == 'P'){
					// JIKA PEMBELIAN PAKET
					$paket_id = substr($info[1], 1);

					$paket = Paket_orm::findOrFail($paket_id);
		
					$where = [
						'mahasiswa_id' => $mhs->id_mahasiswa,
						'paket_id' => $paket->id,
						'stts' => PAKET_STTS_AKTIF,
					];

					$paket_history_before = Paket_history_orm::where($where)->first();

					if(!empty($paket_history_before)){
						$paket_history_before->stts = PAKET_STTS_NON_AKTIF ;
						$paket_history_before->save();
					}
		
					$where = [
						'mahasiswa_id' => $mhs->id_mahasiswa,
						'paket_id' => $paket->id,
					];
			
					$paket_count = Paket_history_orm::where($where)->get()->count();
			
					$paket_history = new Paket_history_orm();
					$paket_history->mahasiswa_id = $mhs->id_mahasiswa;
					$paket_history->paket_id = $paket->id ;
					$paket_history->upgrade_ke = $paket_count++ ;
					$paket_history->stts =  PAKET_STTS_AKTIF ;
					$paket_history->save();
		
					$paket_history_id = $paket_history->id;

					// ASSIGN USER TO MHS_MATKUL BERDASARKAN PAKET DIBELI
					foreach($paket->matkul as $matkul){
						$sisa_kuota_latihan_soal = $paket->kuota_latihan_soal ;
						if(empty($mhs->mhs_matkul()->where('matkul_id', $matkul->id_matkul)->first())){ 
							// CHEK MHS SUDAH DIASIGN MATKUL APA BELUM, BISA JADI KEMUNGKINAN MATKUL TSB DI PILIH DI PAKET YG LAIN
							$mhs_matkul_orm = new Mhs_matkul_orm();
						}else{
							$mhs_matkul_orm = $mhs->mhs_matkul()->where('matkul_id', $matkul->id_matkul)->first();
							$sisa_kuota_latihan_soal = $sisa_kuota_latihan_soal + $mhs_matkul_orm->sisa_kuota_latihan_soal ;
						}
						
						$mhs_matkul_orm->mahasiswa_id = $mhs->id_mahasiswa;
						$mhs_matkul_orm->matkul_id = $matkul->id_matkul;
						$mhs_matkul_orm->sisa_kuota_latihan_soal = $sisa_kuota_latihan_soal ;
						$mhs_matkul_orm->save();
						
						if($matkul->m_ujian->isNotEmpty()){
							foreach($matkul->m_ujian as $m_ujian){
								if(empty($mhs_matkul_orm->mhs_ujian()->where('ujian_id', $m_ujian->id_ujian)->first())){
									$mhs_ujian = new Mhs_ujian_orm();
									$mhs_ujian->mahasiswa_matkul_id = $mhs_matkul_orm->id;
									$mhs_ujian->ujian_id = $m_ujian->id_ujian;
									$mhs_ujian->save();
								}
							}
						}
					}
		
				}
		
				$trx_payment->stts = PAYMENT_ORDER_TELAH_DIPROSES;
				$trx_payment->membership_history_id = $membership_history_id;
				$trx_payment->paket_history_id = $paket_history_id;
				$trx_payment->tgl_bayar = $notif->transaction_time;
				$trx_payment->save();

				// $trx_midtrans->is_settlement_processed = 1 ;
				// $trx_midtrans->save();


			}

			commit_db_trx();
			$log_status = "SUCCESS";

		}catch(Exception $e){
			rollback_db_trx();
			$log_status = "FAIL : " . $e->getMessage();
		}

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
		
		// $combination_text = $notif->order_id . $notif->status_code . $notif->gross_amount . MIDTRANS_SERVER_KEY ;
		// $signature_key_srv = hash("sha512", $combination_text);
		
		// $trx_midtrans->signature_key_check = $signature_key_srv == $notif->signature_key ? 1 : 0 ;

		// $va_number = null ;
		// $bank = 'lainnya' ;

		// if(isset($notif->va_numbers)){
		// 	$bank = $notif->va_numbers->bank ;
		// 	$va_number = $notif->va_numbers->va_number ;
		// }

		// if(isset($notif->biller_code)){
		// 	if($notif->biller_code == '70012'){
		// 		$bank = 'mandiri';
		// 		$va_number = $notif->bill_key ;
		// 	}
		// }

		// if(isset($notif->permata_va_number)){
		// 	$bank = 'permata';
		// 	$va_number = $notif->permata_va_number ;
		// }
		
		// $trx_midtrans->bank = strtoupper($bank);
		// $trx_midtrans->va_number = $va_number;

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

	public function gen_admin(){

		echo 'START =>>' . "\n" ;

		$log_status = null ;
		try{
			begin_db_trx();
			// GEN USER GROUP
			$this->ion_auth->create_group('admin', 'Administrator'); // 1
			$this->ion_auth->create_group('dosen', 'Pembuat Soal dan ujian'); // 2
			$this->ion_auth->create_group('mahasiswa', 'Peserta Ujian'); // 3
			$this->ion_auth->create_group('pengawas', 'Pengawas Ujian'); // 4
			$this->ion_auth->create_group('penyusun_soal', 'Penyusun Soal Ujian'); // 5

			// GEN USER ADMIN 
			$username = 'admin';
			$password = '12345678';
			$email = 'admin@gmail.com';
			$additional_data = array(
						'first_name' => 'ADMIN',
						'last_name' => 'ADMIN',
						'full_name'	=> 'ADMIN',
						);
			$group = array(1); // Sets user to admin.
		
			$this->ion_auth->register($username, $password, $email, $additional_data, $group);

			// SET ACTIVE TO ADMIN
			$user = Users_orm::where('username', $username)->first();
			$user->active = 1;
			$user->save();

			commit_db_trx();

			$log_status = 'SUCCESS' ;
		}catch(Exception $e){
			rollback_db_trx();
			$log_status = "FAIL : " . $e->getMessage();
		}

		echo 'STATUS : ' . $log_status . "\n";

		echo 'END <<=' . "\n" ;

	}

	// function tes(){
		
	// 	$mhs = Mhs_orm::find(1000009908);
	// 	$mhs_matkul_orm = $mhs->mhs_matkul()->where('matkul_id', 20)->first();

	// 	vdebug($mhs_matkul_orm);

	// 	// echo $this->config->item('composer_autoload');

	// 	// echo __FILE__ ;
	// }
	
}
