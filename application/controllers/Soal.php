<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Orm\Soal_orm;
use Orm\Matkul_orm;
use Orm\Bobot_soal_orm;
use Orm\Topik_orm;

use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\MySQL;

class Soal extends MY_Controller {

	public function __construct(){
		parent::__construct();
		if (!$this->ion_auth->logged_in()){
			redirect('auth');
		}else if ( !$this->ion_auth->is_admin() && !$this->ion_auth->in_group('dosen') ){
			show_error('Hanya Administrator dan dosen yang diberi hak untuk mengakses halaman ini', 403, 'Akses Terlarang');
		}
		$this->load->library(['datatables', 'form_validation']);// Load Library Ignited-Datatables
		$this->load->helper('my');// Load Library Ignited-Datatables
		$this->load->model('Master_model', 'master');
		$this->load->model('Soal_model', 'soal');
		$this->form_validation->set_error_delimiters('','');
	}

    public function index()
	{
        $user = $this->ion_auth->user()->row();
		$data = [
			'judul'	=> 'Soal',
			'subjudul'=> 'List Soal'
        ];
        
        if($this->ion_auth->is_admin()){
            //Jika admin maka tampilkan semua matkul
            $data['matkul'] = $this->master->getAllMatkul();
        }else{
            //Jika bukan maka matkul dipilih otomatis sesuai matkul dosen
            $data['matkul'] = Orm\Dosen_orm::where('nip',$user->username)->firstOrFail()->matkul;
        }
        
//		$this->load->view('_templates/dashboard/_header.php', $data);
//		$this->load->view('soal/data');
//		$this->load->view('_templates/dashboard/_footer.php');
		view('soal/index',$data);
    }
    
    public function detail($id)
    {
//        $user = $this->ion_auth->user()->row();
        $soal_orm = Soal_orm::findOrFail($id);
		$data = [
//			'user'      => $user,
			'judul'	    => 'Soal',
            'subjudul'  => 'Detail Soal',
            'soal'      => $this->soal->getSoalById($id),
			'soal_orm'      => $soal_orm,
        ];
		
		
		
		

//        $this->load->view('_templates/dashboard/_header.php', $data);
//		$this->load->view('soal/detail');
//		$this->load->view('_templates/dashboard/_footer.php');
		
		view('soal/detail',$data);
    }
    
    public function add()
	{
        $user = $this->ion_auth->user()->row();
		$data = [
			'user'      => $user,
			'judul'	    => 'Soal',
            'subjudul'  => 'Add Soal'
        ];

        if($this->ion_auth->is_admin()){
            //Jika admin maka tampilkan semua matkul
//            $data['dosen'] = $this->soal->getAllDosen();
            $data['matkul'] = $this->master->getAllMatkul();
            $data['topik'] = $this->master->getAllTopik();
        }else{
            //Jika bukan maka matkul dipilih otomatis sesuai matkul dosen
            $data['matkul'] =  Orm\Dosen_orm::where('nip',$user->username)->firstOrFail()->matkul;
        }
        
        $data['bobot_soal'] = Bobot_soal_orm::All();

//		$this->load->view('_templates/dashboard/_header.php', $data);
//		$this->load->view('soal/add');
//		$this->load->view('_templates/dashboard/_footer.php');
		view('soal/add',$data);
    }

    public function edit($id)
	{
//		$user = $this->ion_auth->user()->row();
		$soal = Soal_orm::findOrFail($id);
		$user = $this->ion_auth->user()->row();
		
		if(!$this->ion_auth->is_admin()){
			if($soal->created_by != $user->username){
				$message_rootpage = [
					'header' => 'Perhatian',
					'content' => 'Anda bukan pembuat soal.',
					'type' => 'warning'
				];
				$this->session->set_flashdata('message_rootpage', $message_rootpage);
				redirect('soal/index', 'refresh');
			}
		}
		$data = [
			'judul'	    => 'Soal',
            'subjudul'  => 'Edit Soal',
            'soal'      => $this->soal->getSoalById($id),
        ];
        
        if($this->ion_auth->is_admin()){
            //Jika admin maka tampilkan semua matkul
            $data['matkul'] = $this->master->getAllMatkul();
            $data['topik'] = $this->master->getAllTopik();
            $data['soal'] = $soal;
        }else{
            //Jika bukan maka matkul dipilih otomatis sesuai matkul dosen
	        $data['soal'] = $soal;
            $data['matkul'] =  Orm\Dosen_orm::where('nip',$user->username)->firstOrFail()->matkul;
        }
        
        $data['bobot_soal'] = Bobot_soal_orm::All();


//		$this->load->view('_templates/dashboard/_header.php', $data);
//		$this->load->view('soal/edit');
//		$this->load->view('_templates/dashboard/_footer.php');
		view('soal/edit',$data);
	}

	protected function _data()
	{
		$id = $this->input->get('id');
		$username = null;
		if(!$this->ion_auth->is_admin()){
			$username = $this->ion_auth->user()->row()->username;
		}
		$this->_json($this->soal->getDataSoal($id, $username), false);
    }

    private function _validasi()
    {
		$this->form_validation->set_rules('matkul_id', 'Matkul', 'required');
		$this->form_validation->set_rules('topik_id', 'Topik', 'required');
        $this->form_validation->set_rules('soal', 'Soal', 'required');
        $this->form_validation->set_rules('jawaban_a', 'Jawaban A', 'required');
        $this->form_validation->set_rules('jawaban_b', 'Jawaban B', 'required');
        $this->form_validation->set_rules('jawaban_c', 'Jawaban C', 'required');
        $this->form_validation->set_rules('jawaban_d', 'Jawaban D', 'required');
        $this->form_validation->set_rules('jawaban_e', 'Jawaban E', 'required');
        $this->form_validation->set_rules('jawaban', 'Kunci Jawaban', 'required');
//        $this->form_validation->set_rules('bobot', 'Bobot Soal', 'required|is_natural_no_zero|max_length[2]');
        $this->form_validation->set_rules('bobot_soal_id', 'Bobot Soal', 'required|is_natural_no_zero');
    }

    private function _file_config()
    {
        $allowed_type 	= [
            "image/jpeg", "image/jpg", "image/png", "image/gif",
            "audio/mpeg", "audio/mpg", "audio/mpeg3", "audio/mp3", "audio/x-wav", "audio/wave", "audio/wav",
            "video/mp4", "application/octet-stream"
        ];
        $config['upload_path']      = FCPATH.'uploads/bank_soal/';
        $config['allowed_types']    = 'jpeg|jpg|png|gif|mpeg|mpg|mpeg3|mp3|wav|wave|mp4';
        $config['encrypt_name']     = TRUE;
        
        return $this->load->library('upload', $config);
    }
	
    /*
     * UNTUK METHOD SAVE PADA CLASS INI TIDAK DI DECLARE PROTECTED TETAPI TETAP PUBLIC KRN TIDAK DIPANGGIL VIA AJAX
     */
    public function save()
    {
    	
    	if(!$this->input->post()){
    		redirect('soal');
	    }
        $method = $this->input->post('method', true);
        $id_soal = $this->input->post('id_soal', true);
        $this->_validasi();
        $this->_file_config();
        
        if($this->form_validation->run() === FALSE){
        	// VALIDASI SALAH
            $method === 'add' ? $this->add() : $this->edit($id_soal) ;
        }else{
        	// VALIDASI BENAR
            $data = [
                'soal'      => $this->input->post('soal'),
                'jawaban'   => $this->input->post('jawaban', true),
//                'bobot'     => $this->input->post('bobot', true),
                'bobot_soal_id'     => $this->input->post('bobot_soal_id', true),
            ];
            
            $abjad = ['a', 'b', 'c', 'd', 'e'];
            
            // Inputan Opsi
            foreach ($abjad as $abj) {
                $data['opsi_'.$abj]    = $this->input->post('jawaban_'.$abj);
            }

            $i = 0;
            foreach ($_FILES as $key => $val) {
                $img_src = FCPATH.'uploads/bank_soal/';
//                $getsoal = $this->soal->getSoalById($this->input->post('id_soal', true));
	            $getsoal = $this->soal->getSoalById($id_soal);
                
                $error = '';
                if($key === 'file_soal'){
                    if(!empty($_FILES['file_soal']['name'])){
                        if (!$this->upload->do_upload('file_soal')){
                            $error = $this->upload->display_errors();
                            show_error($error, 500, 'File Soal Error');
                            exit();
                        }else{
                            if($method === 'edit'){
                                if(!unlink($img_src.$getsoal->file)){
                                    show_error('Error saat delete gambar <br/>'.var_dump($getsoal), 500, 'Error Edit Gambar');
                                    exit();
                                }
                            }
                            $data['file'] = $this->upload->data('file_name');
                            $data['tipe_file'] = $this->upload->data('file_type');
                        }
                    }
                }else{
                    $file_abj = 'file_'.$abjad[$i];
                    if(!empty($_FILES[$file_abj]['name'])){
                        if (!$this->upload->do_upload($key)){
                            $error = $this->upload->display_errors();
                            show_error($error, 500, 'File Opsi '.strtoupper($abjad[$i]).' Error');
                            exit();
                        }else{
                            if($method === 'edit'){
                                if(!unlink($img_src.$getsoal->$file_abj)){
                                    show_error('Error saat delete gambar', 500, 'Error Edit Gambar');
                                    exit();
                                }
                            }
                            $data[$file_abj] = $this->upload->data('file_name');
                        }
                    }
                    $i++;
                }
            }
            
//            if($this->ion_auth->is_admin()){
//                $pecah = $this->input->post('dosen_id', true);
//                $pecah = explode(':', $pecah);
//                $data['dosen_id'] = $pecah[0];
//                $data['matkul_id'] = end($pecah);
//            }else{
//                $data['dosen_id'] = $this->input->post('dosen_id', true);
//                $data['matkul_id'] = $this->input->post('matkul_id', true);
//            }
	        
	        $data['topik_id'] = $this->input->post('topik_id', true);
//            $data['matkul_id'] = $this->input->post('matkul_id', true);

            if($method==='add'){
                //push array
                $data['created_at'] = date('Y-m-d H:i:s');
                $data['created_by']   = $this->ion_auth->user()->row()->username;
                //insert data
                $this->master->create('tb_soal', $data);
            }else if($method==='edit'){
                //push array
                $data['updated_at'] = date('Y-m-d H:i:s');
                //update data
//                $id_soal = $this->input->post('id_soal', true);
                $this->master->update('tb_soal', $data, 'id_soal', $id_soal);
            }else{
                show_error('Method tidak diketahui', 404);
            }
            $message_rootpage = [
				'header' => 'Perhatian',
				'content' => 'Data berhasil disimpan.',
				'type' => 'success'
			];
            $this->session->set_flashdata('message_rootpage', $message_rootpage);
            $method==='add'? redirect('soal/add') : redirect('soal/edit/' . $id_soal);
        }
    }

    protected function _delete()
    {
        $chk = $this->input->post('checked', true);
        
        // Delete File
        foreach($chk as $id){
            $abjad = ['a', 'b', 'c', 'd', 'e'];
            $path = FCPATH.'uploads/bank_soal/';
            $soal = $this->soal->getSoalById($id);
            // Hapus File Soal
            if(!empty($soal->file)){
                if(file_exists($path.$soal->file)){
                    unlink($path.$soal->file);
                }
            }
            //Hapus File Opsi
            $i = 0; //index
            foreach ($abjad as $abj) {
                $file_opsi = 'file_'.$abj;
                if(!empty($soal->$file_opsi)){
                    if(file_exists($path.$soal->$file_opsi)){
                        unlink($path.$soal->$file_opsi);
                    }
                }
            }
        }

        if(!$chk){
            $this->_json(['status'=>false]);
        }else{
        	
        	$user = $this->ion_auth->user()->row();
			if(!$this->ion_auth->is_admin()){
				$allow = true ;
        	    foreach($chk  as $c){
		            $soal = Soal_orm::findOrFail($c);
					if($soal->created_by != $user->username){
						$allow = false;
						break;
					}
	            }
        	    if(!$allow){
	                $data['status'] = FALSE;
					$this->_json($data);
					return;
	            }
			}
			
            if($this->master->delete('tb_soal', $chk, 'id_soal')){
                $this->_json(['status'=>true, 'total'=>count($chk)]);
            }
        }
    }
    
    protected function _get_topic_by_matkul(){
		
		$matkul = Matkul_orm::findOrFail($this->input->get('id'));
		$result = [];
		if($matkul->topik->count() > 0){
			foreach($matkul->topik as $topik){
				$result[$topik->id] = $topik->nama_topik;
			}
		}
		
		$this->_json($result);
		
	}
	
	public function bobot_soal(){
        $data = [
			'judul'	    => 'Bobot Soal',
            'subjudul'  => 'List Bobot Soal'
        ];
    	
    	view('soal/bobot_soal',$data);
	}
	
	protected function _data_bobot_soal()
	{
		
	    $config = [
        	'host'     => $this->db->hostname,
            'port'     => $this->db->port,
            'username' => $this->db->username,
            'password' => $this->db->password,
            'database' => $this->db->database,
        ];
    	
	    $dt = new Datatables( new MySQL($config) );
	    
	    $this->db->select('a.id, a.bobot, a.nilai, CONCAT(COUNT(b.bobot_soal_id), " soal") AS jml_soal');
        $this->db->from('bobot_soal a');
        $this->db->join('tb_soal AS b', 'b.bobot_soal_id = a.id','left');
        $this->db->group_by('a.id');

	    $query = $this->db->get_compiled_select() ; // GET QUERY PRODUCED BY ACTIVE RECORD WITHOUT RUNNING I

        $dt->query($query);
        
        $return = $dt->generate();
        
		$this->_json($return, false);
    }
    
    protected function _save_bobot_soal()
	{
		$this->_akses_admin();
		if($this->input->post()) {
			$this->form_validation->set_rules('method', 'Method', 'required');
			$this->form_validation->set_rules('bobot', 'Bobot', 'required');
			$this->form_validation->set_rules('nilai', 'Nilai', 'required|is_natural');
			if($this->form_validation->run() === FALSE){
        	    // VALIDASI SALAH
				$data = [
					'status'	=> false,
					'errors'	=> [
						'bobot' => form_error('bobot'),
						'nilai' => form_error('nilai'),
					]
				];
	
                $this->_json($data);
            }else{
				$id             = $this->input->post('id');
				$bobot             = $this->input->post('bobot');
				$nilai             = $this->input->post('nilai');
				
				if(null == $id){
					$bobot_soal        = new Bobot_soal_orm();
					$bobot_soal->bobot = $bobot;
					$bobot_soal->nilai = $nilai;
					$action = $bobot_soal->save();
					$data['status'] = $action;
				}else{
					$bobot_soal        = Bobot_soal_orm::findOrFail($id);
					$bobot_soal->bobot = $bobot;
					$bobot_soal->nilai = $nilai;
					$action = $bobot_soal->save();
					$data['status'] = $action;
				}
                $this->_json($data);
			}
		}
	}
    
    public function add_bobot_soal()
	{
		
		$this->_akses_admin();

		$data = [
			'judul'	    => 'Bobot Soal',
            'subjudul'  => 'Add Bobot Soal'
        ];
		
		view('soal/add_bobot_soal',$data);
    }
    
    public function edit_bobot_soal($id)
	{
		$this->_akses_admin();
		$bobot_soal = Bobot_soal_orm::findOrFail($id);

		$data = [
			'judul'	    => 'Bobot Soal',
            'subjudul'  => 'Edit Bobot Soal',
			'bobot_soal' => $bobot_soal,
        ];
		
		view('soal/edit_bobot_soal',$data);
    }
    
    public function delete_bobot_soal($id)
	{
		$this->_akses_admin();
		$bobot_soal = Bobot_soal_orm::findOrFail($id);

		if(empty(count($bobot_soal->soal))){
			$bobot_soal->delete();
			$message_rootpage = [
				'header' => 'Perhatian',
				'content' => 'Data berhasil dihapus.',
				'type' => 'success'
			];
		}else{
			$message_rootpage = [
				'header' => 'Perhatian',
				'content' => 'Data masih digunakan.',
				'type' => 'error'
			];
		}
		$this->session->set_flashdata('message_rootpage', $message_rootpage);
		redirect('soal/bobot_soal', 'refresh');
    }
    
    protected function _get_jml_soal_per_topik(){
    	if ( !$this->ion_auth->is_admin() && !$this->ion_auth->in_group('dosen') ){
			show_error('Hanya Administrator dan dosen yang diberi hak untuk mengakses halaman ini', 403, 'Akses Terlarang');
		}
    	$topik_ids = $this->input->post('topik_ids');
    	$topik_ids = json_decode($topik_ids);
    	$jml_soal = [];
    	if(!empty($topik_ids)){
//            $topik = Topik_orm::whereIn('id',$topik_ids)->get();
//            $bobot_soal = Bobot_soal_orm::whereIn('id',$topik_ids)->get();
		    $soal = Soal_orm::whereIn('topik_id', $topik_ids)->get();
            if(!empty(count($soal))){
			    foreach($soal as $d){
			    	if(!isset($jml_soal[$d->topik_id][$d->bobot_soal_id]))
			    		$jml_soal[$d->topik_id][$d->bobot_soal_id] = 0;
				    $jml_soal[$d->topik_id][$d->bobot_soal_id] = ++$jml_soal[$d->topik_id][$d->bobot_soal_id];
			    }
		    }
      
	    }
    	$this->_json($jml_soal);
    }
    
    public function import($import_data = null)
	{
		$data = [
			'judul'	=> 'Soal',
			'subjudul' => 'Import Soal',
			'topik_list' => Topik_orm::All(),
			'bobot_list' => Bobot_soal_orm::All()
		];
		if ($import_data != null) $data['import'] = $import_data;

//		$this->load->view('_templates/dashboard/_header', $data);
//		$this->load->view('master/mahasiswa/import');
//		$this->load->view('_templates/dashboard/_footer');
		view('soal/import',$data);
	}
	
	public function preview()
	{
		$config['upload_path']		= './uploads/import/';
		$config['allowed_types']	= 'xls|xlsx|csv';
		$config['max_size']			= 5120;
		$config['encrypt_name']		= true;

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('upload_file')) {
			$error = $this->upload->display_errors();
			show_error('Upload error : ' . $error, 500, 'Perhatian');
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
					show_error('Upload error : file tidak didukung', 500, 'Perhatian');
			}

			$spreadsheet = $reader->load($file);
			$sheetData = $spreadsheet->getActiveSheet()->toArray();
			$data = [];
			for ($i = 1; $i < count($sheetData); $i++) {
				
				if(count($sheetData[$i]) < 9){
					unlink($file);
					show_error('Isian file tidak sesuai',500,'Perhatian');
				}
				
				$topik_id = $sheetData[$i][0];
				$topik = Topik_orm::find($topik_id);
				if($topik == null){
					$topik_id = '!! ERROR !!';
				}
				
				$soal = $sheetData[$i][1];
				if(empty($soal)){
					$soal = '!! ERROR !!';
				}
				
				$opsi_a = $sheetData[$i][2];
				if(empty($opsi_a)){
					$opsi_a = '!! ERROR !!';
				}
				
				$opsi_b = $sheetData[$i][3];
				if(empty($opsi_b)){
					$opsi_b = '!! ERROR !!';
				}
				
				$opsi_c = $sheetData[$i][4];
				if(empty($opsi_c)){
					$opsi_c = '!! ERROR !!';
				}
				
				$opsi_d = $sheetData[$i][5];
				if(empty($opsi_d)){
					$opsi_d = '!! ERROR !!';
				}
				
				$opsi_e = $sheetData[$i][6];
				if(empty($opsi_e)){
					$opsi_e = '!! ERROR !!';
				}
				
				$jawaban = $sheetData[$i][7];
				if(!in_array($jawaban,['A','B','C','D','E'])){
					$jawaban = '!! ERROR !!';
				}
				
				$bobot_soal_id = $sheetData[$i][8];
				$bobot_soal = Bobot_soal_orm::find($bobot_soal_id);
				if($bobot_soal == null){
					$bobot_soal_id = '!! ERROR !!';
				}
				
				$data[] = [
					'topik_id' => $topik_id,
					'topik' => $topik,
					'soal' => $soal,
					'opsi_a' => $opsi_a,
					'opsi_b' => $opsi_b,
					'opsi_c' => $opsi_c,
					'opsi_d' => $opsi_d,
					'opsi_e' => $opsi_e,
					'jawaban' => $jawaban,
					'bobot_soal_id' => $bobot_soal_id,
					'bobot_soal' => $bobot_soal,
				];
			}
			
			unlink($file);

			$this->import($data);
		}
	}
	
	public function do_import()
	{
		$input = json_decode($this->input->post('data', true));
//		$data = [];
		begin_db_trx();
		$allow = true;
		$msg = null;
//		vdebug($input);
		foreach ($input as $d) {
			$topik_id = $d->topik_id;
			if(Topik_orm::find($topik_id) == null){
				$allow = false;
				$msg = 'Topik ID bermasalah, topik_id : '. $topik_id ;
				break;
			}
			
			$isi_soal = $d->soal;
			if(empty($isi_soal)){
				$allow = false;
				$msg = 'Soal salah, soal : '. $isi_soal ;
				break;
			}
			
			$opsi_a = $d->opsi_a;
			if(empty($opsi_a)){
				$allow = false;
				$msg = 'Opsi A salah, opsi_a : '. $opsi_a ;
				break;
			}
			
			$opsi_b = $d->opsi_b;
			if(empty($opsi_b)){
				$allow = false;
				$msg = 'Opsi B salah, opsi_b : '. $opsi_b ;
				break;
			}
			
			$opsi_c = $d->opsi_c;
			if(empty($opsi_c)){
				$allow = false;
				$msg = 'Opsi C salah, opsi_c : '. $opsi_c ;
				break;
			}
			
			$opsi_d = $d->opsi_d;
			if(empty($opsi_d)){
				$allow = false;
				$msg = 'Opsi D salah, opsi_d : '. $opsi_d ;
				break;
			}
			
			$opsi_e = $d->opsi_e;
			if(empty($opsi_e)){
				$allow = false;
				$msg = 'Opsi E salah, opsi_e : '. $opsi_e ;
				break;
			}
			
			$jawaban = $d->jawaban;
			if(!in_array($jawaban,['A','B','C','D','E'])){
				$allow = false;
				$msg = 'Jawaban bermasalah, jawaban : '. $jawaban ;
				break;
			}
			
			$bobot_soal_id = $d->bobot_soal_id;
			if(Bobot_soal_orm::find($bobot_soal_id) == null){
				$allow = false;
				$msg = 'Bobot soal ID bermasalah, bobot_soal_id : '. $bobot_soal_id ;
				break;
			}
			
			$soal = new Soal_orm();
			$soal->topik_id = $topik_id;
			$soal->soal = '<p>'. $isi_soal . '</p>';
			$soal->opsi_a = '<p>'. $opsi_a . '</p>';
			$soal->opsi_b = '<p>'. $opsi_b . '</p>';
			$soal->opsi_c = '<p>'. $opsi_c . '</p>';
			$soal->opsi_d = '<p>'. $opsi_d . '</p>';
			$soal->opsi_e = '<p>'. $opsi_e . '</p>';
			$soal->jawaban = $jawaban;
			$soal->bobot_soal_id = $bobot_soal_id;
			$soal->created_by = $this->ion_auth->user()->row()->username;
			$soal->save();
		}
		
		if (!$allow){
			rollback_db_trx();
			show_error($msg,500,'Perhatian');
		}
		else{
			commit_db_trx();
			$message_rootpage = [
				'header' => 'Perhatian',
				'content' => 'Data berhasil di impor.',
				'type' => 'success'
			];
			$this->session->set_flashdata('message_rootpage', $message_rootpage);
			redirect('soal/import');
		}
		
	}

}
