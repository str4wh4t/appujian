<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Orm\Mhs_orm;
use Orm\Hujian_orm;
use Orm\Topik_orm;


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
		show_404();
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

}
