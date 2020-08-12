<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Orm\Hujian_orm;
use Orm\Mujian_orm;
use Orm\Topik_orm;
use Orm\Hujian_deleted_orm;
use Orm\Jawaban_ujian_deleted_orm;


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
		$action = true;
		try {
			begin_db_trx();
			$h_ujian_deleted = new Hujian_deleted_orm();
			$h_ujian_deleted->ujian_id = $h_ujian->ujian_id;
			$h_ujian_deleted->mahasiswa_id = $h_ujian->mahasiswa_id;
			$h_ujian_deleted->mahasiswa_ujian_id = $h_ujian->mahasiswa_ujian_id;
			$h_ujian_deleted->jml_soal = $h_ujian->jml_soal;
			$h_ujian_deleted->jml_benar = $h_ujian->jml_benar;
			$h_ujian_deleted->jml_salah = $h_ujian->jml_salah;
			$h_ujian_deleted->nilai = $h_ujian->nilai;
			$h_ujian_deleted->nilai_bobot_benar = $h_ujian->nilai_bobot_benar;
			$h_ujian_deleted->total_bobot = $h_ujian->total_bobot;
			$h_ujian_deleted->nilai_bobot = $h_ujian->nilai_bobot;
			$h_ujian_deleted->detail_bobot_benar = $h_ujian->detail_bobot_benar;
			$h_ujian_deleted->tgl_mulai = $h_ujian->tgl_mulai;
			$h_ujian_deleted->tgl_selesai = $h_ujian->tgl_selesai;
			$h_ujian_deleted->ujian_selesai = $h_ujian->ujian_selesai;
			$h_ujian_deleted->save();
	
			foreach($h_ujian->jawaban_ujian as $jawaban_ujian) {
//				vdebug($jawaban_ujian);
				$jawaban_ujian_deleted_orm           = new Jawaban_ujian_deleted_orm();
				$jawaban_ujian_deleted_orm->ujian_id = $h_ujian_deleted->id;
				$jawaban_ujian_deleted_orm->soal_id  = $jawaban_ujian->soal_id;
				$jawaban_ujian_deleted_orm->jawaban  = $jawaban_ujian->jawaban;
				$jawaban_ujian_deleted_orm->status_jawaban  = $jawaban_ujian->status_jawaban;
				$jawaban_ujian_deleted_orm->save();
			}
			$h_ujian->delete();
			commit_db_trx();
		} catch(\Illuminate\Database\QueryException $e){
			rollback_db_trx();
			$action = false;
	    }
		
		$this->_json(['status' => $action]);
		
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
		if(in_group('mahasiswa')){
			$id = integer_read_from_uuid($id);
		}
		
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
			'nilai'	=> $nilai
		];

//		$this->load->view('_templates/dashboard/_header.php', $data);
//		$this->load->view('ujian/detail_hasil');
//		$this->load->view('_templates/dashboard/_footer.php');
		view('hasilujian/detail',$data);
	}

//	public function cetak($id)
//	{
//		$this->load->library('Pdf');
//
//		$mhs 	= $this->ujian->getIdMahasiswa($this->user->username);
//		$hasil 	= $this->ujian->HslUjian($id, $mhs->id_mahasiswa)->row();
//		$ujian 	= $this->ujian->getUjianById($id);
//
//		$data = [
//			'ujian' => $ujian,
//			'hasil' => $hasil,
//			'mhs'	=> $mhs
//		];
//
//		$this->load->view('hasilujian/cetak', $data);
//	}

	public function cetak_detail($id)
	{
		if(in_group('mahasiswa')){
			$id = integer_read_from_uuid($id);
		}
		
		$m_ujian = Mujian_orm::findOrFail($id);
		
		if(in_group('mahasiswa')){
			if(!$m_ujian->tampilkan_hasil){
				show_404();
			}
		}
		
		$this->load->library('Pdf');

		$ujian = $m_ujian;
		$nilai = $this->ujian->bandingNilai($id);
		$hasil = $this->ujian->HslUjianById($id)->result();
		

		$data = [
			'ujian'	=> $ujian,
			'nilai'	=> $nilai,
			'hasil'	=> $hasil
		];
		
		$new_hasil = [];
		foreach ($data['hasil'] as $hasil){
			$hasil_ujian_per_topik = json_decode($hasil->detail_bobot_benar);
            $return = '<table>';
            if(!empty($hasil_ujian_per_topik)) {
	            foreach ($hasil_ujian_per_topik as $t => $v) {
		            $return .= '<tr>';
		            $tpk    = Topik_orm::findOrFail($t);
		            $return .= '<td width="100%">' . $tpk->nama_topik . '</td>';
		            //                $return .= '<td width="20%">' . $v . '</td>';
		            $return .= '</tr>';
	            }
            }
            $return .= '</table>';
            $new_hasil[] = [
				'nim' => $hasil->nim,
				'nama' => $hasil->nama,
				'nilai' => $hasil->nilai,
				'nilai_bobot_benar' => $hasil->nilai_bobot_benar,
				'detail_bobot_benar' => $return
			];
		}
		
		$data['hasil'] = $new_hasil;

		$this->load->view('hasilujian/cetak_detail', $data);
	}
	
}
