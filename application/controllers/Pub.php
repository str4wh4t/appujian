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
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Wsock\Chat;
use Ratchet\Client;


class Pub extends MY_Controller {

	public function __construct(){
		parent::__construct();
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
		$this->load->library('Pdf');

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
		
		$wsServer = new WsServer(new Chat());
		
		$server = IoServer::factory(
		    new HttpServer(
		        $wsServer
		    ),
		    8080
		);
		
		$wsServer->enableKeepAlive($server->loop, 30);
		
		$server->run();
	}
	
	public function cron_auto_start_ujian_for_unstarted_participants($app_id = 'ujian'){
		if(!is_cli()) show_404();
		
		$mhs_ujian_orm = new Mhs_ujian_orm();
		$m_ujian_orm = new Mujian_orm();
		$soal_orm = new Soal_orm();
		if($app_id == 'ujian'){
	        $mhs_ujian_orm->setConnection('ujian');
	        $m_ujian_orm->setConnection('ujian');
	        $soal_orm->setConnection('ujian');
		}
        else{
	        $mhs_ujian_orm->setConnection('cat');
	        $m_ujian_orm->setConnection('cat');
	        $soal_orm->setConnection('cat');
			//
	    }
		
		$mhs_ujian_list = $mhs_ujian_orm->whereDoesntHave('h_ujian')->orderBy('id')->get();
		
		if($mhs_ujian_list->isNotEmpty()) {
			$cron_end = date("Y-m-d H:i:s", strtotime("+1 minutes"));
			foreach ($mhs_ujian_list as $mu) {
				$m_ujian = $m_ujian_orm->find($mu->ujian_id);
				
				$today = date('Y-m-d H:i:s');
				if($today > $cron_end){
					echo 'break';
					break;
				}
				
				$date_end = date('Y-m-d H:i:s', strtotime($m_ujian->terlambat));
				if ($today < $date_end){
					continue;
				}
				echo 'mu->id : ' . $mu->id . "\n";
				echo 'today : ' . $today . "\n";
				echo 'date_end : ' . $date_end . "\n";
				try {
					$soal 		= [];
					$soal_topik = [];
					$i = 0;
					foreach($m_ujian->topik_ujian as $topik_ujian){
						$jumlah_soal_diset = $topik_ujian->jumlah_soal;
						$soal_avail = $soal_orm->where('topik_id',$topik_ujian->topik_id)
						                            ->where('bobot_soal_id',$topik_ujian->bobot_soal_id)
													->get();
						if($jumlah_soal_diset > $soal_avail->count()){
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
					if($app_id == 'ujian')
				        $h_ujian_orm->setConnection('ujian');
			        else
				        $h_ujian_orm->setConnection('cat');
						//
				  
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
								if($app_id == 'ujian')
							        $jawaban_ujian_orm->setConnection('ujian');
						        else
							        $jawaban_ujian_orm->setConnection('cat');
							    
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
	
	public function cron_auto_close($app_id = 'ujian'){
		if(!is_cli()) show_404();
		
		$h_ujian_list = new Hujian_orm();
		if($app_id == 'ujian')
	        $h_ujian_list->setConnection('ujian');
        else
	        $h_ujian_list->setConnection('cat');
			//
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
						$cmd = '{"cmd":"MHS_STOP_UJIAN","nim":"'. $h_ujian->mhs->nim .'","app_id":"'. $app_id . '.undip.ac.id' .'"}';
						$this->_notif_ws($cmd);
						echo "DONE" ;
					}else{
						echo "ERROR" ;
					}
				}
			}
		}
		return;
	}
	
//	public function coba(){
//		$cmd = '{"cmd":"MHS_STOP_UJIAN","nim":"22020090007","app_id":"cat.undip.ac.id"}';
//		$this->_notif_ws($cmd);
//	}
	
	private function _notif_ws($send_msg = ''){
		Client\connect(ws_url())->then(function($conn) use ($send_msg){
	        $conn->on('message', function($msg) use ($conn, $send_msg) {
	            echo "Received: {$msg}\n";
	            $conn->close();
	        });
	        $conn->send($send_msg);
	    }, function ($e) {
	        echo "Could not connect: {$e->getMessage()}\n";
	    });
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
		$h_ujian->nilai     =  number_format(floor($nilai), 0);
//		$h_ujian->nilai_bobot = $nilai_bobot;
		$h_ujian->nilai_bobot = 0;
		$h_ujian->nilai_bobot_benar     =  $nilai_bobot_benar;
		$h_ujian->detail_bobot_benar     =  json_encode($topik_ujian_nilai_bobot);
		$h_ujian->total_bobot     =  $total_bobot;
		$h_ujian->tgl_selesai =  date('Y-m-d H:i:s');
		$h_ujian->ujian_selesai    =  'Y';
		$h_ujian->ended_by =  'cron';
		$action = $h_ujian->save();
		
		return $action;
		
	}
	
}
