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

}
