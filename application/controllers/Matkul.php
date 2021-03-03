<?php
defined('BASEPATH') or exit('No direct script access allowed');

//use PhpOffice\PhpSpreadsheet\Spreadsheet;
//use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
//use PhpOffice\PhpSpreadsheet\Writer\Xls;
//use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Orm\Matkul_orm;
use Orm\Mhs_matkul_orm;
use Illuminate\Database\Eloquent\Builder;
use Orm\Prodi_orm;
use Orm\Mhs_orm;

class Matkul extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
//		if (!$this->ion_auth->logged_in()) {
//			redirect('auth');
//		} else if (!$this->ion_auth->is_admin()) {
//			show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
//		}
		$this->_akses_admin();
		$this->load->library(['datatables', 'form_validation']); // Load Library Ignited-Datatables
		$this->load->model('Master_model', 'master');
		$this->form_validation->set_error_delimiters('', '');
	}

	public function index()
	{
		$data = [
			'user' => $this->ion_auth->user()->row(),
			'judul'	=> 'Materi Ujian',
			'subjudul' => 'List Materi Ujian'
		];
//		$this->load->view('_templates/dashboard/_header.php', $data);
//		$this->load->view('master/matkul/data');
//		$this->load->view('_templates/dashboard/_footer.php');
		view('matkul/index',$data);
	}

	protected function _data()
	{
		$this->_json($this->master->getDataMatkul(), false);
	}

	public function add()
	{
		$data = [
			'user' 		=> $this->ion_auth->user()->row(),
			'judul'		=> 'Materi Ujian',
			'subjudul'	=> 'Add Materi Ujian',
			'banyak'	=> $this->input->post('banyak', true)
		];
//		$this->load->view('_templates/dashboard/_header.php', $data);
//		$this->load->view('master/matkul/add');
//		$this->load->view('_templates/dashboard/_footer.php');
		view('matkul/add',$data);
	}

	public function edit()
	{
		$chk = $this->input->post('checked', true);
		if (!$chk) {
			redirect('matkul');
		} else {
			$matkul = $this->master->getMatkulById($chk);
			$data = [
				'user' 		=> $this->ion_auth->user()->row(),
				'judul'		=> 'Materi Ujian',
				'subjudul'	=> 'Edit Materi Ujian',
				'matkul'	=> $matkul
			];
//			$this->load->view('_templates/dashboard/_header.php', $data);
//			$this->load->view('master/matkul/edit');
//			$this->load->view('_templates/dashboard/_footer.php');
			view('matkul/edit',$data);
		}
	}

	protected function _save()
	{
		$rows = count($this->input->post('nama_matkul', true));
		$mode = $this->input->post('mode', true);
		for ($i = 1; $i <= $rows; $i++) {
			$nama_matkul = 'nama_matkul[' . $i . ']';
			$this->form_validation->set_rules($nama_matkul, 'Materi Ujian', 'required');
			$this->form_validation->set_message('required', '{field} Wajib diisi');

			if ($this->form_validation->run() === FALSE) {
				$error[] = [
					$nama_matkul => form_error($nama_matkul)
				];
				$status = FALSE;
			} else {
				if ($mode == 'add') {
					$insert[] = [
						'nama_matkul' => $this->input->post($nama_matkul, true)
					];
				} else if ($mode == 'edit') {
					$update[] = array(
						'id_matkul'	=> $this->input->post('id_matkul[' . $i . ']', true),
						'nama_matkul' 	=> $this->input->post($nama_matkul, true)
					);
				}
				$status = TRUE;
			}
		}
		if ($status) {
			if ($mode == 'add') {
				$this->master->create('matkul', $insert, true);
				$data['insert']	= $insert;
			} else if ($mode == 'edit') {
				$this->master->update('matkul', $update, 'id_matkul', null, true);
				$data['update'] = $update;
			}
		} else {
			if (isset($error)) {
				$data['errors'] = $error;
			}
		}
		$data['status'] = $status;
		$this->_json($data);
	}

	protected function _delete()
	{
		$chk = $this->input->post('checked', true);
		if (!$chk) {
			$this->_json(['status' => false]);
		} else {
			if ($this->master->delete('matkul', $chk, 'id_matkul')) {
				$this->_json(['status' => true, 'total' => count($chk)]);
			}
		}
	}

	public function import($import_data = null)
	{
		$data = [
			'user' => $this->ion_auth->user()->row(),
			'judul'	=> 'Materi Ujian',
			'subjudul' => 'Import Materi Ujian'
		];
		if ($import_data != null) $data['import'] = $import_data;

//		$this->load->view('_templates/dashboard/_header', $data);
//		$this->load->view('master/matkul/import');
//		$this->load->view('_templates/dashboard/_footer');
		view('matkul/import',$data);
	}

	public function preview()
	{
		$config['upload_path']		= './uploads/import/';
		$config['allowed_types']	= 'xls|xlsx|csv';
		$config['max_size']			= 2048;
		$config['encrypt_name']		= true;

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('upload_file')) {
			$error = $this->upload->display_errors();
			echo $error;
			die;
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
					echo "unknown file ext";
					die;
			}

			$spreadsheet = $reader->load($file);
			$sheetData = $spreadsheet->getActiveSheet()->toArray();
			$matkul = [];
			for ($i = 1; $i < count($sheetData); $i++) {
				if ($sheetData[$i][0] != null) {
					$matkul[] = $sheetData[$i][0];
				}
			}

			unlink($file);

			$this->import($matkul);
		}
	}
	
	public function do_import()
	{
		$data = json_decode($this->input->post('matkul', true));
		$jurusan = [];
		foreach ($data as $j) {
			$jurusan[] = ['nama_matkul' => $j];
		}

		$save = $this->master->create('matkul', $jurusan, true);
		if ($save) {
			redirect('matkul');
		} else {
			redirect('matkul/import');
		}
	}
	
	protected function _get_peserta_ujian_matkul(){
		$id = $this->input->post('id', true);
		$ujian_id = $this->input->post('ujian_id', true);
		$matkul = Matkul_orm::findOrFail($id);
		$mhs_matkul = $matkul->mhs()->where('tahun', get_selected_tahun())->get();
		if(empty($ujian_id)){
			$mhs_ujian = [];
		}else {
			$mhs_ujian = $matkul->mhs_matkul()
										->whereHas('mhs', function(Builder $query){
											$query->where('tahun', get_selected_tahun());
										})
										->whereHas('mhs_ujian', function (Builder $query) use($ujian_id){
											$query->where('ujian_id', $ujian_id);
										})
										->get();
		}
		$this->_json(['mhs_matkul' => $mhs_matkul,'mhs_ujian' => $mhs_ujian]);
	}
	
	protected function _get_peserta_ujian_matkul_not_ujian(){
		$id = $this->input->post('id', true);
		$ujian_id = $this->input->post('ujian_id', true);
		$matkul = Matkul_orm::findOrFail($id);
		$mhs_matkul = $matkul->mhs()
								->where('tahun', get_selected_tahun())
								->whereDoesntHave('h_ujian', function (Builder $query) use($ujian_id){
			                    	$query->where('ujian_id', $ujian_id);
			                    })->get();
		if(empty($ujian_id)){
			$mhs_ujian = [];
		}else {
			$mhs_ujian = $matkul->mhs_matkul()
								->whereHas('mhs', function(Builder $query){
									$query->where('tahun', get_selected_tahun());
								})
			                    ->whereHas('mhs_ujian', function (Builder $query) use($ujian_id){
				                    $query->where('ujian_id', $ujian_id);
			                    })
			                    ->get();
		}
		
		$this->_json(['mhs_matkul' => $mhs_matkul, 'mhs_ujian' => $mhs_ujian]);
	}
	
	public function peserta($matkul_id)
	{
		$matkul = Matkul_orm::findOrFail($matkul_id);
		$data = [
			'judul'	=> 'Materi Ujian',
			'subjudul' => 'Peserta Materi Ujian'
		];
//		$this->load->view('_templates/dashboard/_header.php', $data);
//		$this->load->view('master/matkul/data');
//		$this->load->view('_templates/dashboard/_footer.php');
		
		if($this->input->post()){
			$mhs_ids = [];
			if($this->input->post('peserta[]')){
				$mhs_ids = $this->input->post('peserta[]');
			}
//			$mhs_matkul = Mhs_matkul_orm::where('matkul_id', $matkul_id)->get();
			$mhs_matkul = Mhs_matkul_orm::where('matkul_id', $matkul_id)
		                        ->whereDoesntHave('mhs_ujian')
			                    ->get();
			$mhs_ids_before = [];
			if($mhs_matkul->isNotEmpty($mhs_matkul)){
				foreach($mhs_matkul as $mm){
					$mhs_ids_before[] = $mm->mahasiswa_id;
				}
			}
			$mhs_ids_insert = array_diff($mhs_ids, $mhs_ids_before);
			$mhs_ids_delete = array_diff($mhs_ids_before, $mhs_ids);
			
			try {
				begin_db_trx();
				
				if(!empty($mhs_ids_delete)) {
					foreach ($mhs_ids_delete as $mhs_id) {
						$mhs_matkul = Mhs_matkul_orm::where([
							'mahasiswa_id' => $mhs_id,
							'matkul_id'    => $matkul_id
						])->firstOrFail();
						
						$mhs_matkul->delete();
					}
				}
				
				if(!empty($mhs_ids_insert)) {
					foreach ($mhs_ids_insert as $mhs_id) {
						$mhs_matkul_orm                      = new Mhs_matkul_orm();
						$mhs_matkul_orm->mahasiswa_id = $mhs_id;
						$mhs_matkul_orm->matkul_id            =$matkul_id;
						$mhs_matkul_orm->save();
					}
				}
				commit_db_trx();
				$data['msg_ok'] = "Data berhasil disimpan";
			} catch(\Illuminate\Database\QueryException $e){
				rollback_db_trx();
				show_error('Terjadi kesalahan : ' . $e->getMessage(), 500, 'Perhatian');
		    }
		}
		
		$data['prodi'] = Prodi_orm::all();
		$data['matkul'] = $matkul;
		
		$prodi_mhs_selected = [];
		if($matkul->mhs->isNotEmpty()){
			foreach($matkul->mhs as $mhs){
				$prodi_mhs_selected[$mhs->kodeps] = $mhs->kodeps;
			}
		}
		
		$data['prodi_mhs_selected'] = $prodi_mhs_selected;
		
		view('matkul/peserta',$data);
	}
	
	protected function _get_peserta_matkul(){
		$matkul_id = $this->input->post('matkul_id', true);
		$kodeps = $this->input->post('kodeps', true);
		$kodeps = json_decode($kodeps);
		$mhs_matkul = Mhs_orm::whereIn('kodeps', $kodeps)
		                        ->whereHas('mhs_matkul', function (Builder $query) use($matkul_id){
				                    $query->where('matkul_id', $matkul_id);
			                    })
			                    ->get();
		$mhs_ujian = $mhs_matkul->has('mhs_ujian');
		$this->_json(['mhs_matkul' => $mhs_matkul, 'mhs_ujian' => $mhs_ujian]);
	}
	
	protected function _get_mhs_prodi(){
		$matkul_id = $this->input->post('matkul_id', true);
		$kodeps = $this->input->post('kodeps', true);
		$kodeps = json_decode($kodeps);
		$mhs = Mhs_orm::whereIn('kodeps', $kodeps)->get();
//		$mhs_matkul = Mhs_orm::whereIn('kodeps', $kodeps)
//		                        ->whereHas('mhs_matkul', function (Builder $query) use($matkul_id){
//				                    $query->where('matkul_id', $matkul_id);
//			                    })
//			                    ->get();
		$mhs_ids = [];
		if($mhs->isNotEmpty()){
			foreach ($mhs as $m) {
				$mhs_ids[] = $m->id_mahasiswa;
			}
		}
		$mhs_matkul = Mhs_matkul_orm::whereIn('mahasiswa_id', $mhs_ids)
								->where('matkul_id', $matkul_id)
		                        ->whereHas('mhs_ujian')
			                    ->get();
		
		$mhs_matkul_ids = [];
		if($mhs_matkul->isNotEmpty()){
			foreach ($mhs_matkul as $mm) {
				$mhs_matkul_ids[] = $mm->mahasiswa_id;
			}
		}
		
		$mhs_valid_ids = array_diff($mhs_ids, $mhs_matkul_ids);
		
		$mhs = Mhs_orm::whereIn('id_mahasiswa', $mhs_valid_ids)->get();
		
		$mhs_matkul = Mhs_matkul_orm::whereIn('mahasiswa_id', $mhs_ids)
								->where('matkul_id', $matkul_id)
		                        ->whereDoesntHave('mhs_ujian')
			                    ->get();
		
		$this->_json(['mhs' => $mhs, 'mhs_matkul' => $mhs_matkul]);
	}
}
