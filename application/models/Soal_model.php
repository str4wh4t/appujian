<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\MySQL;
use Orm\Users_orm;
use Orm\Dosen_orm;

class Soal_model extends CI_Model {
    
    public function getDataSoal($data_filter = null, $username = null)
    {
        
        $config = [
        	'host'     => $this->db->hostname,
            'port'     => $this->db->port,
            'username' => $this->db->username,
            'password' => $this->db->password,
            'database' => $this->db->database,
        ];
    	
	    $dt = new Datatables( new MySQL($config) );
	    
	    $this->db->select('a.id_soal, a.no_urut, a.soal, a.created_at, a.updated_at, a.is_reported, d.bobot, c.nama_matkul, b.nama_topik, GROUP_CONCAT(f.nama_bundle SEPARATOR "---") as bundle, GROUP_CONCAT(CONCAT("[", f.id, "]")) as bundle_ids, g.full_name as oleh');
        $this->db->from('tb_soal a');
        $this->db->join('topik b', 'b.id = a.topik_id');
        $this->db->join('matkul c', 'c.id_matkul = b.matkul_id');
        $this->db->join('bobot_soal d', 'd.id = a.bobot_soal_id', 'left');
        $this->db->join('bundle_soal e', 'e.id_soal = a.id_soal', 'left');
        $this->db->join('bundle f', 'f.id = e.bundle_id', 'left');
        $this->db->join('users g', 'a.created_by = g.username');
        $this->db->group_by('a.id_soal');

		if (!empty($data_filter)) {
            // foreach($data_filter as $filter => $val){
            //     $this->db->where('b.matkul_id', $id);
            // }
            if(!empty($data_filter['matkul_id'])){
                $this->db->where('b.matkul_id', $data_filter['matkul_id']);
            }
            if(!empty($data_filter['topik_id'])){
                $this->db->where('b.id', $data_filter['topik_id']);
            }
            if(!empty($data_filter['gel'])){
                $this->db->where('a.gel', $data_filter['gel']);
            }
            if(!empty($data_filter['smt'])){
                $this->db->where('a.smt', $data_filter['smt']);
            }
            if(!empty($data_filter['tahun'])){
                $this->db->where('a.tahun', $data_filter['tahun']);
            }
            if($data_filter['is_reported'] !== null){
                $this->db->where('a.is_reported', $data_filter['is_reported']);
            }
            if(!empty($data_filter['bundle'])){
                $this->db->having('bundle_ids LIKE', '%[' . $data_filter['bundle'] . ']%');
            }
        }
        
        if ($username != null) {
            $dosen = Dosen_orm::where('nip',$username)->first();
            $matkul_id = [null];
            foreach($dosen->matkul as $matkul){
                $matkul_id[] = $matkul->id_matkul;
            }
            $this->db->where_in('b.matkul_id', $matkul_id);
//        	 $this->db->where('a.created_by', $username);
        }
        
        $this->db->group_by('a.id_soal');
//        $this->db->order_by('b.matkul_id');
        $this->db->order_by('a.topik_id');
        $this->db->order_by('a.created_at','desc');
        
        $query = $this->db->get_compiled_select() ; // GET QUERY PRODUCED BY ACTIVE RECORD WITHOUT RUNNING I
		// echo $query; die;
	    // $user_orm = new Users_orm();
        $dt->query($query);
        // $dt->edit('oleh', function ($data) use ($user_orm) {
        //     $user = $user_orm->where('username',$data['oleh'])->first();
        //     return $user != null ? $user->full_name : '';
        // });

        return $dt->generate();
        
    }

    public function getSoalById($id)
    {
        return $this->db->get_where('tb_soal', ['id_soal' => $id])->row();
    }

    public function getMatkulDosen($nip)
    {
        $this->db->select('matkul_id, nama_matkul, id_dosen, nama_dosen');
        $this->db->join('matkul', 'matkul_id = id_matkul');
        $this->db->from('dosen')->where('nip', $nip);
        return $this->db->get()->row();
    }

    public function getAllDosen()
    {
        $this->db->select('*');
        $this->db->from('dosen a');
        $this->db->join('matkul b', 'a.matkul_id=b.id_matkul');
        return $this->db->get()->result();
    }
}
