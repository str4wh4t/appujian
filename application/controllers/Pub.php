<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Orm\Mhs_orm;
use Orm\Hujian_orm;
use Orm\Topik_orm;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Wsock\Chat;


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
//		show_404(); /* <--- DISABLED FUNCTION */
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
	
	public function socket(){
		$server = IoServer::factory(
		    new HttpServer(
		        new WsServer(
		            new Chat()
		        )
		    ),
		    8080
		);
		
		$server->run();
	}
	
	public function cron_auto_close(){
		if(!is_cli()) show_404();
		$cron_end = date("Y-m-d H:i:s", strtotime("+1 minutes"));
		$h_ujian_list = Hujian_orm::all();
		if($h_ujian_list->isNotEmpty()){
			foreach($h_ujian_list as $h_ujian){
				$today = date('Y-m-d H:i:s');
				if($today > $cron_end){
					break;
				}
				$date_end = date('Y-m-d H:i:s', strtotime($h_ujian->tgl_selesai));
				if (($today > $date_end) && ($h_ujian->ujian_selesai == 'N')){
					echo $h_ujian->id . "\n";
					echo $h_ujian->mhs->nama . "\n";
				    echo $this->submit_ujian($h_ujian->id) ? "DONE" : "ERROR" ;
				}
			}
		}
		return;
	}
	
	public function submit_ujian($id_h_ujian){
		if(!is_cli()) show_404();
		$h_ujian = Hujian_orm::findOrFail($id_h_ujian);
		$this->load->model('Ujian_model', 'ujian');
		$this->load->model('Master_model', 'master');
		// Get Jawaban
		$list_jawaban = $this->ujian->getJawaban($id_h_ujian);
		
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
				$bobot_poin        = ($jwb->soal->bobot_soal->nilai * $jwb->soal->topik->poin_topik);
				$total_bobot_benar = $total_bobot_benar + $bobot_poin;
				//					foreach ($topik_ujian_nilai_bobot as $t => $v){
				//						if($t == $jwb->soal->topik_id){
				//
				//						}
				//					}
				$topik_ujian_nilai_bobot[$jwb->soal->topik_id] = $topik_ujian_nilai_bobot[$jwb->soal->topik_id] + $bobot_poin;
			} else {
				$jumlah_salah++;
			}
		}
		
		$nilai             = ($jumlah_benar / $jumlah_soal) * 100;
		$nilai_bobot_benar = $total_bobot_benar;
		//			$total_bobot_benar = $total_bobot;
		$nilai_bobot = ($total_bobot / $jumlah_soal) * 100;
		
		$d_update = [
			'jml_benar'          => $jumlah_benar,
			'jml_salah'          => $jumlah_salah,
			'jml_soal'           => $jumlah_soal,
			'nilai'              => number_format(floor($nilai), 0),
			//				'nilai_bobot' => number_format(floor($nilai_bobot), 0),
			'nilai_bobot'        => 0,
			'nilai_bobot_benar'  => $nilai_bobot_benar,
			'detail_bobot_benar' => json_encode($topik_ujian_nilai_bobot),
			'total_bobot'        => $total_bobot,
			'ujian_selesai'      => 'Y'
		];
		
		$action = $this->master->update('h_ujian', $d_update, 'id', $id_h_ujian);
		
		return $action;
		
	}

}
