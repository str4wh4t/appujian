<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\MySQL;
use Orm\Users_orm;
use Orm\Paket_orm;
use Orm\Paket_ujian_orm;
use Illuminate\Database\Capsule\Manager as DB;

use Carbon\Carbon;

class Paket extends MY_Controller
{

	public function __construct(){
        parent::__construct();
		if (!$this->ion_auth->logged_in()) {
			redirect('auth');
		}


    }

    public function index(){
        $this->_akses_admin();
        $data = [
			'judul'	    => 'Paket Ujian',
			'subjudul'  => 'List Paket Ujian'
		];

		view('paket/index', $data);

    }

    public function list(){
        $data = [];

        $user = $this->ion_auth->user()->row();
        $data['user'] = Users_orm::findOrFail($user->id);

        $paket_list = Paket_orm::where('is_show', 1)->get();

        $data['paket_list'] = $paket_list;
        
        view('paket/list', $data);
    }

    public function history($user_id = null){
        $user = $this->ion_auth->user()->row();

        if($this->ion_auth->in_group('mahasiswa'))
            $user = Users_orm::findOrFail($user->id);
        else
            $user = Users_orm::findOrFail($user_id);

        
        $paket_history_list = $user->mhs->paket_history->sortByDesc('id');

        $data['paket_history_list']   = $paket_history_list;


        $data['mhs_ujian_list']   = $user->mhs->mhs_ujian;

        $mhs_membership = get_mhs_aktif_membership($user->mhs);

        $today = Carbon::now();
        $count_expire_days = 'UNLIMITED' ;

        if($mhs_membership->membership_id != MEMBERSHIP_ID_DEFAULT){
            $expired_at = new Carbon($mhs_membership->expired_at);
            if($expired_at->greaterThan($today))
                $count_expire_days = $expired_at->diffInDays($today) . ' Hari Lagi';
            else
                $count_expire_days = '0 Hari Lagi';
        }

        $data['mhs_membership'] = $mhs_membership ;
        $data['count_expire_days'] = $count_expire_days ;

        view('paket/history', $data);

    }

	protected function _data()
	{

		$config = [
			'host'     => $this->db->hostname,
			'port'     => $this->db->port,
			'username' => $this->db->username,
			'password' => $this->db->password,
			'database' => $this->db->database,
		];

		$dt = new Datatables(new MySQL($config));

		$this->db->select('a.id, a.name, a.urut, a.kuota_latihan_soal, a.is_show, COUNT(b.ujian_id) AS jml_ujian, IFNULL(phx.jml_mhs, 0) AS jml_taker');
		$this->db->from('paket a');
		$this->db->join('paket_ujian AS b', 'b.paket_id = a.id', 'left');
		$this->db->join('(
			SELECT ph.paket_id, COUNT(ph.mahasiswa_id) AS jml_mhs
			FROM paket_history ph 
			WHERE ph.stts = 1
			GROUP BY ph.paket_id
		) AS phx', 'phx.paket_id = a.id', 'left');
		$this->db->group_by('a.id');

		$query = $this->db->get_compiled_select(); // GET QUERY PRODUCED BY ACTIVE RECORD WITHOUT RUNNING I

		$dt->query($query);

		$return = $dt->generate();

		$this->_json($return, false);
	}

	public function edit($id)
	{
		$this->_akses_admin();
		$paket = Paket_orm::findOrFail($id);

		$data = [
			'judul'	    => 'Paket Ujian',
			'subjudul'  => 'Edit Paket Ujian',
			'paket' => $paket,
		];

		view('paket/edit', $data);
	}

	public function add()
	{
		$this->_akses_admin();

		$data = [
			'judul'	    => 'Paket Ujian',
			'subjudul'  => 'Add Paket Ujian',
		];

		view('paket/add', $data);
	}

	protected function _delete()
	{
		$this->_akses_admin();

		$id = $this->input->post('id');
		$paket = Paket_orm::findOrFail($id);

		$stts = null;
		$msg = null;
		try{
			$paket->delete();
			$stts = 'ok';
			
		}catch(Exception $e){
			$stts = 'ko';
			$msg = $e->getMessage();
		}

		$this->_json(['stts' => $stts, 'msg' => $msg]);
	}

	protected function _save_paket()
	{
		$this->_akses_admin();
		if ($this->input->post()) {
            $this->load->library(['form_validation']);
            $this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('aksi', 'Aksi', 'required|in_list[add,edit]');
			$aksi = $this->input->post('aksi');
			if($aksi == 'edit')
				$this->form_validation->set_rules('id', 'ID', 'required');

			$this->form_validation->set_rules('name', 'Nama paket', 'required|trim');
			$this->form_validation->set_rules('price', 'Harga', 'required|is_natural_no_zero');
			$this->form_validation->set_rules('delete_price', 'Harga hapus', 'is_natural');
			$this->form_validation->set_rules('description', 'Deskripsi', 'required|trim');
			$this->form_validation->set_rules('kuota_latihan_soal', 'Kuota latihan soal', 'required|is_natural_no_zero');
			$this->form_validation->set_rules('text_color', 'Pilihan warna', 'required|in_list[success,info,warning,danger]');
			$this->form_validation->set_rules('is_show', 'Status', 'required|in_list[0,1]');
			$this->form_validation->set_rules('urut', 'Urut', 'required|is_natural_no_zero');
			if ($this->form_validation->run() === FALSE) {
				// VALIDASI SALAH
				$data = [
					'status'	=> false,
					'errors'	=> [
						'name' => form_error('name'),
						'price' => form_error('price'),
						'delete_price' => form_error('delete_price'),
						'description' => form_error('description'),
						'kuota_latihan_soal' => form_error('kuota_latihan_soal'),
						'text_color' => form_error('text_color'),
						'is_show' => form_error('is_show'),
						'urut' => form_error('urut'),
					]
				];

				$this->_json($data);
			} else {
				if($aksi == 'edit'){
					$id             = $this->input->post('id');
					$paket        = Paket_orm::findOrFail($id);
				}else{
					$paket        = new Paket_orm();
				}
				$paket->name     = $this->input->post('name');
				$paket->price     = $this->input->post('price');
				$paket->delete_price     = empty($this->input->post('delete_price')) ? null : $this->input->post('delete_price');
				$paket->description     = $this->input->post('description');
				$paket->kuota_latihan_soal     = $this->input->post('kuota_latihan_soal');
				$paket->text_color     = $this->input->post('text_color');
				$paket->is_show     = $this->input->post('is_show');
				$paket->urut     = $this->input->post('urut');

				$action = $paket->save();
				$data['status'] = $action;

				$this->_json($data);
			}
		}
	}

}