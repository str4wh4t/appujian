<?php
defined('BASEPATH') or exit('No direct script access allowed');

//use PhpOffice\PhpSpreadsheet\Spreadsheet;
//use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
//use PhpOffice\PhpSpreadsheet\Writer\Xls;
//use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Orm\Matkul_orm;
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
		if(empty($ujian_id)){
			$mhs_ujian = [];
		}else {
			$mhs_ujian = $matkul->mhs_matkul()
			                    ->whereHas('mhs_ujian', function (Builder $query) use($ujian_id){
				                    $query->where('ujian_id', $ujian_id);
			                    })
			                    ->get();
		}
		$this->_json(['mhs_matkul' => $matkul->mhs,'mhs_ujian' => $mhs_ujian]);
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
		$data['prodi'] = Prodi_orm::all();
		$data['matkul'] = $matkul;
		view('matkul/peserta',$data);
	}
	
	protected function _get_peserta_matkul(){
		$matkul_id = $this->input->post('matkul_id', true);
		$kodeps = $this->input->post('kodeps', true);
		$kodeps = json_decode($kodeps);
		$mhs_matkul = Mhs_orm::whereIn('kodeps', $kodeps)->whereHas('mhs_matkul', function (Builder $query) use($matkul_id){
				                    $query->where('matkul_id', $matkul_id);
			                    })
			                    ->get();
		$mhs_ujian = $mhs_matkul->has('mhs_ujian');
		$this->_json(['mhs_matkul' => $mhs_matkul, 'mhs_ujian' => $mhs_ujian]);
	}
}
