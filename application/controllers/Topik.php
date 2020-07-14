<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Orm\Matkul_orm;

class Topik extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		if (!$this->ion_auth->logged_in()) {
			redirect('auth');
		} else if (!$this->ion_auth->is_admin()) {
			show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini', 403, 'Akses Terlarang');
		}
		$this->load->library(['datatables', 'form_validation']); // Load Library Ignited-Datatables
		$this->load->model('Master_model', 'master');
		$this->form_validation->set_error_delimiters('', '');
	}

	public function index()
	{
		$data = [
			'user' => $this->ion_auth->user()->row(),
			'judul'	=> 'Topik',
			'subjudul' => 'List Topik'
		];
//		$this->load->view('_templates/dashboard/_header.php', $data);
//		$this->load->view('master/kelas/data');
//		$this->load->view('_templates/dashboard/_footer.php');
		view('topik/index',$data);
	}

	protected function _data()
	{
		$this->_json($this->master->getDataTopik(), false);
	}

	public function add()
	{
		$data = [
			'user' 		=> $this->ion_auth->user()->row(),
			'judul'		=> 'Topik',
			'subjudul'	=> 'Add Topik',
			'banyak'	=> $this->input->post('banyak', true),
			'matkul'	=> $this->master->getAllMatkul()
//			'jurusan'	=> $this->master->getAllJurusan()
		];
//		$this->load->view('_templates/dashboard/_header.php', $data);
//		$this->load->view('master/kelas/add');
//		$this->load->view('_templates/dashboard/_footer.php');
		view('topik/add',$data);
	}

	public function edit()
	{
		$chk = $this->input->post('checked', true);
		if (!$chk) {
			redirect('topik');
		} else {
			$topik = $this->master->getTopikById($chk);
			$data = [
				'user' 		=> $this->ion_auth->user()->row(),
				'judul'		=> 'Topik',
				'subjudul'	=> 'Edit Topik',
				'matkul'	=> $this->master->getAllMatkul(),
				'topik'		=> $topik
			];
//			$this->load->view('_templates/dashboard/_header.php', $data);
//			$this->load->view('master/kelas/edit');
//			$this->load->view('_templates/dashboard/_footer.php');
			view('topik/edit',$data);
		}
	}

	protected function _save()
	{
		$rows = count($this->input->post('nama_topik', true));
		$mode = $this->input->post('mode', true);
		for ($i = 1; $i <= $rows; $i++) {
			$nama_topik 	= 'nama_topik[' . $i . ']';
			$matkul_id 	= 'matkul_id[' . $i . ']';
			$poin_topik 	= 'poin_topik[' . $i . ']';
			$this->form_validation->set_rules($nama_topik, 'Topik', 'required');
			$this->form_validation->set_rules($matkul_id, 'Matkul', 'required');
			$this->form_validation->set_rules($poin_topik, 'Poin', 'required|is_natural_no_zero');
			$this->form_validation->set_message('required', '{field} Wajib diisi');

			if ($this->form_validation->run() === FALSE) {
				$error[] = [
					$nama_topik 	=> form_error($nama_topik),
					$matkul_id 	=> form_error($matkul_id),
					$poin_topik 	=> form_error($poin_topik),
				];
				$status = FALSE;
			} else {
				if ($mode == 'add') {
					$insert[] = [
						'nama_topik' 	=> $this->input->post($nama_topik, true),
						'matkul_id' 	=> $this->input->post($matkul_id, true),
						'poin_topik' 	=> $this->input->post($poin_topik, true),
						'created_at'    => date('Y-m-d H:i:s'),
						'created_by'    => $this->ion_auth->user()->row()->username,
					];
				} else if ($mode == 'edit') {
					$update[] = array(
						'id'		=> $this->input->post('id[' . $i . ']', true),
						'nama_topik' 	=> $this->input->post($nama_topik, true),
						'matkul_id' 	=> $this->input->post($matkul_id, true),
						'poin_topik' 	=> $this->input->post($poin_topik, true),
						'updated_at' => date('Y-m-d H:i:s'),
					);
				}
				$status = TRUE;
			}
		}
		if ($status) {
			if ($mode == 'add') {
				$this->master->create('topik', $insert, true);
				$data['insert']	= $insert;
			} else if ($mode == 'edit') {
				$this->master->update('topik', $update, 'id', null, true);
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
			if ($this->master->delete('topik', $chk, 'id')) {
				$this->_json(['status' => true, 'total' => count($chk)]);
			}
		}
	}

	protected function _kelas_by_jurusan($id)
	{
		$data = $this->master->getTopikByJurusan($id);
		$this->_json($data);
	}

	public function import($import_data = null)
	{
		$data = [
			'user' => $this->ion_auth->user()->row(),
			'judul'	=> 'Topik',
			'subjudul' => 'Import Topik',
			'jurusan' => $this->master->getAllJurusan()
		];
		if ($import_data != null) $data['import'] = $import_data;

//		$this->load->view('_templates/dashboard/_header', $data);
//		$this->load->view('master/kelas/import');
//		$this->load->view('_templates/dashboard/_footer');
		view('kelas/import',$data);
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
			$data = [];
			for ($i = 1; $i < count($sheetData); $i++) {
				$data[] = [
					'kelas' => $sheetData[$i][0],
					'jurusan' => $sheetData[$i][1]
				];
			}

			unlink($file);

			$this->import($data);
		}
	}
	
	public function do_import()
	{
		$input = json_decode($this->input->post('data', true));
		$data = [];
		foreach ($input as $d) {
			$data[] = ['nama_kelas' => $d->kelas, 'jurusan_id' => $d->jurusan];
		}

		$save = $this->master->create('kelas', $data, true);
		if ($save) {
			redirect('kelas');
		} else {
			redirect('kelas/import');
		}
	}
	
	
	
}
