<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\MySQL;
use Orm\Users_orm;
use Orm\Dosen_orm;

class Soal_model extends CI_Model {
    
    public function getDataSoal($id = null, $username = null)
    {
        
        $config = [
        	'host'     => $this->db->hostname,
            'port'     => $this->db->port,
            'username' => $this->db->username,
            'password' => $this->db->password,
            'database' => $this->db->database,
        ];
    	
	    $dt = new Datatables( new MySQL($config) );
	    
	    $this->db->select('a.id_soal, a.soal, a.created_at, a.updated_at, d.bobot, c.nama_matkul, b.nama_topik, a.created_by as oleh');
        $this->db->from('tb_soal a');
        $this->db->join('topik b', 'b.id = a.topik_id');
        $this->db->join('matkul c', 'c.id_matkul = b.matkul_id');
        $this->db->join('bobot_soal d', 'd.id = a.bobot_soal_id');

		if ($id !== null) {
             $this->db->where('b.matkul_id', $id);
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
//		echo $query; die;
	    $user_orm = new Users_orm();
        $dt->query($query);
        $dt->edit('oleh', function ($data) use ($user_orm) {
            $user = $user_orm->where('username',$data['oleh'])->first();
            return $user != null ? $user->full_name : '';
        });
        
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
