<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\MySQL;
use Orm\Users_orm;
use Orm\Dosen_orm;
use Orm\Topik_orm;
use Orm\Hujian_orm;

class Ujian_model extends CI_Model {
    
    public function getDataUjian($id = null, $username = null, $role = null, $status_ujian = 'active')
    {
//        $this->datatables->select('a.id_ujian, a.token, a.nama_ujian, b.nama_matkul, a.jumlah_soal, CONCAT(a.tgl_mulai, " <br/> (", a.waktu, " Menit)") as waktu, a.jenis');
//        $this->datatables->from('m_ujian a');
//        $this->datatables->join('matkul b', 'a.matkul_id = b.id_matkul');
//        if($id!==null){
//            $this->datatables->where('dosen_id', $id);
//        }
//        return $this->datatables->generate();
	    
	    $config = [
        	'host'     => $this->db->hostname,
            'port'     => $this->db->port,
            'username' => $this->db->username,
            'password' => $this->db->password,
            'database' => $this->db->database,
        ];
    	
	    $dt = new Datatables( new MySQL($config) );
	    
	    $this->db->select('a.id_ujian, status_ujian, a.token, a.nama_ujian, b.nama_matkul, a.jumlah_soal, a.tgl_mulai, a.terlambat, CONCAT(a.waktu, " Mnt") AS waktu, CONCAT(a.jenis , "/" , a.jenis_jawaban) AS jenis, a.created_by as oleh, a.pakai_token');
        $this->db->from('m_ujian a');
        $this->db->join('matkul b', 'a.matkul_id = b.id_matkul');
        
        if($status_ujian == 'active'){
        	$this->db->where('a.status_ujian', 1);
        	$this->db->where('a.terlambat >', date('Y-m-d H:i:s'));
        }
        
        if($status_ujian == 'expired'){
        	$this->db->where('a.terlambat <=', date('Y-m-d H:i:s'));
        }
        
        if($status_ujian == 'close'){
        	$this->db->where('a.status_ujian', 0);
        	$this->db->where('a.terlambat >', date('Y-m-d H:i:s'));
        }
        
        if($status_ujian == 'semua'){
			// JIKA SEMUA
        }
        
        if ($id !== null) {
                $this->db->where('a.matkul_id', $id);
        }
        
        if ($username !== null) {
            $dosen = Dosen_orm::where('nip',$username)->first();
            $matkul_id = [null];
            foreach($dosen->matkul as $matkul){
                $matkul_id[] = $matkul->id_matkul;
            }
            $this->db->where_in('a.matkul_id', $matkul_id);
//        	 $this->db->where('a.created_by', $username);
        }
        
        $this->db->group_by('a.id_ujian');

	    $query = $this->db->get_compiled_select() ; // GET QUERY PRODUCED BY ACTIVE RECORD WITHOUT RUNNING I

        $dt->query($query);
        
        $dt->edit('nama_ujian', function ($data) use ($role){
        	$return = '';
        	if(($role->name == 'admin') || ($role->name == 'dosen')) {
        		$return = '<a href="'. site_url('ujian/edit/' . $data['id_ujian']) .'" >'. $data['nama_ujian'] .'</a >';
	        }else if($role->name == 'pengawas') {
		        $return = '<a href="'. site_url('ujian/monitor/' . $data['id_ujian']) .'" >'. $data['nama_ujian'] .'</a >';
        	}
            return $return ;
        });
        
        $dt->edit('status_ujian', function ($data) {
            $today = date('Y-m-d H:i:s');
			$data_start = date('Y-m-d H:i:s', strtotime($data['tgl_mulai']));
			$date_end = date('Y-m-d H:i:s', strtotime($data['terlambat']));
			
			$return = $data['status_ujian'] ? "active" : "close" ;
			// if (($today >= $data_start) && ($today <= $date_end)) {
			// JIKA MASIH DALAM RANGE TANGGAL
	        if ($today < $date_end) {
				// $return = "expired";
			}else{
			    $return = "expired";
			}
			
			return $return;
        });
        
        $user_orm = new Users_orm;
        $dt->edit('oleh', function ($data) use ($user_orm){
            $user = $user_orm->where('username',$data['oleh'])->first();
            return $user != null ? $user->full_name : '';
        });
        
        $dt->add('aksi', function($data) use ($role){
        // return a link in a new column
	        $return = '';
	        if(($role->name == 'admin') || ($role->name == 'dosen')) {
		
		        $return = '<div class="btn-group btn-group-sm" role="group" aria-label="">
								<a href="' . site_url('ujian/edit/' . $data['id_ujian']) . '" class="btn btn-sm btn-warning">
											<i class="fa fa-edit"></i> Edit
										</a>
										<a href="' . site_url('ujian/monitor/' . $data['id_ujian']) . '" class="btn btn-sm btn-info">
											<i class="fa fa-desktop"></i> Monitor
										</a>
									</div>';
	        }else if($role->name == 'pengawas') {
		
		        $return = '<a href="' . site_url('ujian/monitor/' . $data['id_ujian']) . '" class="btn btn-sm btn-info">
											<i class="fa fa-desktop"></i> Monitor
										</a>';
	        }
	        
	        return $return;
	        
	    });
        
        return $dt->generate();
    }
    
    public function getListUjian($mhs_orm)
    {
//    	$q = $this->db->select('a.matkul_id')->where('a.mahasiswa_id',$id)->where('a.status','N')->get('h_ujian AS a');
//    	$array_ujian_id = [];
//    	if(!empty($q->num_rows())){
//    		foreach($q->result() as $r){
//    			$array_ujian_id[] = $r->ujian_id;
//		    }
//	    }
////        $this->datatables->select("a.id_ujian, e.nama_dosen, d.nama_kelas, a.nama_ujian, b.nama_matkul, a.jumlah_soal, CONCAT(a.tgl_mulai, ' <br/> (', a.waktu, ' Menit)') as waktu,  (SELECT COUNT(id) FROM h_ujian h WHERE h.mahasiswa_id = {$id} AND h.ujian_id = a.id_ujian) AS ada");
//		$this->db->select('a.id_ujian, e.nama_dosen, d.nama_kelas, a.nama_ujian, b.nama_matkul, a.jumlah_soal, a.tgl_mulai, a.terlambat, CONCAT(a.waktu, " Mnt") AS waktu, a.jenis');
//        $this->datatables->from('m_ujian a');
//        $this->datatables->join('matkul b', 'a.matkul_id = b.id_matkul');
//        $this->datatables->join('kelas_dosen c', "a.dosen_id = c.dosen_id");
//        $this->datatables->join('kelas d', 'c.kelas_id = d.id_kelas');
//        $this->datatables->join('dosen e', 'e.id_dosen = c.dosen_id');
//        $this->datatables->where('d.id_kelas', $kelas);
//        $this->datatables->where_not_in('a.id_ujian', $array_ujian_id);
//        return $this->datatables->generate();
		
        $config = [
        	'host'     => $this->db->hostname,
            'port'     => $this->db->port,
            'username' => $this->db->username,
            'password' => $this->db->password,
            'database' => $this->db->database,
        ];
        
//        $matkul = $mhs_orm->matkul;
//        $avail_matkul_id = [null];
//        foreach($matkul as $m){
//        	$avail_matkul_id[] = $m->id_matkul ;
//        }
     
	    $dt = new Datatables( new MySQL($config) );
	    
//	    $this->db->select('a.id_ujian, a.nama_ujian, b.nama_matkul, a.jumlah_soal, a.tgl_mulai, a.terlambat, CONCAT(a.waktu, " Mnt") AS waktu, a.status_ujian, a.tampilkan_hasil, f.ujian_selesai');
//        $this->db->from('m_ujian a');
//        $this->db->join('matkul b', 'a.matkul_id = b.id_matkul');
//        $this->db->join('h_ujian f', 'a.id_ujian = f.ujian_id AND f.mahasiswa_id = "'. $mhs_orm->id_mahasiswa .'"', 'left');
//        $this->db->where_in('a.matkul_id', $avail_matkul_id);
//        $this->db->group_by('a.id_ujian');
	    
	    $this->db->select('a.id_ujian, a.nama_ujian, b.nama_matkul, a.jumlah_soal, a.tgl_mulai, a.terlambat, CONCAT(a.waktu, " Mnt") AS waktu, a.status_ujian, a.tampilkan_hasil, e.id, "UJIAN_SELESAI" AS ujian_selesai');
        $this->db->from('mahasiswa_ujian AS e');
        $this->db->join('mahasiswa_matkul AS g', 'g.id = e.mahasiswa_matkul_id');
        $this->db->join('m_ujian AS a', 'a.id_ujian = e.ujian_id');
        $this->db->join('matkul AS b', 'g.matkul_id = b.id_matkul');
        $this->db->where('g.mahasiswa_id', $mhs_orm->id_mahasiswa);
        $this->db->group_by('e.ujian_id');

	    $query = $this->db->get_compiled_select() ; // GET QUERY PRODUCED BY ACTIVE RECORD WITHOUT RUNNING I
	    
        $dt->query($query);
        $dt->edit('id_ujian', function ($data) {
            return  uuid_create_from_integer($data['id_ujian']) ;
        });
        
        $dt->edit('status_ujian', function ($data) {
            $today = date('Y-m-d H:i:s');
			//echo $paymentDate; // echos today!
			$data_start = date('Y-m-d H:i:s', strtotime($data['tgl_mulai']));
			$date_end = date('Y-m-d H:i:s', strtotime($data['terlambat']));
			
			if (($today >= $data_start) && ($today <= $date_end)){
			    return $data['status_ujian'] ? "active" : "close";
			}else{
				if($today < $data_start)
					return 'upcoming';
				else
			        return "expired";
			}
        });
        $h_ujian = new Hujian_orm();
        $dt->edit('ujian_selesai', function ($data) use($h_ujian){
        	$hasil_ujian = $h_ujian->where('mahasiswa_ujian_id', $data['id'])->first();
        	$ujian_selesai = empty($hasil_ujian) ? 'N' : $hasil_ujian->ujian_selesai ;
            return $ujian_selesai ;
        });

        return $dt->generate();
    }

    public function getUjianById($id)
    {
        $this->db->select('*');
        $this->db->from('m_ujian a');
        $this->db->join('dosen b', 'a.dosen_id=b.id_dosen');
        $this->db->join('matkul c', 'a.matkul_id=c.id_matkul');
        $this->db->where('id_ujian', $id);
        return $this->db->get()->row();
    }

    public function getIdDosen($nip)
    {
        $this->db->select('id_dosen, nama_dosen')->from('dosen')->where('nip', $nip);
        return $this->db->get()->row();
    }

    public function getJumlahSoal($dosen)
    {
        $this->db->select('COUNT(id_soal) as jml_soal');
        $this->db->from('tb_soal');
        $this->db->where('dosen_id', $dosen);
        return $this->db->get()->row();
    }

    public function getIdMahasiswa($nim)
    {
        $this->db->select('*');
        $this->db->from('mahasiswa a');
//        $this->db->join('kelas b', 'a.kelas_id=b.id_kelas');
//        $this->db->join('jurusan c', 'b.jurusan_id=c.id_jurusan');
        $this->db->where('nim', $nim);
        return $this->db->get()->row();
    }

    public function HslUjian($id, $mhs)
    {
        $this->db->select('*, UNIX_TIMESTAMP(tgl_selesai) as waktu_habis');
        $this->db->from('h_ujian');
        $this->db->where('ujian_id', $id);
        $this->db->where('mahasiswa_id', $mhs);
        return $this->db->get();
    }

    public function getSoal($id)
    {
        $ujian = $this->getUjianById($id);
        $order = $ujian->jenis==="acak" ? 'rand()' : 'id_soal';

        $this->db->select('id_soal, soal, file, tipe_file, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, jawaban');
        $this->db->from('tb_soal');
        $this->db->where('dosen_id', $ujian->dosen_id);
        $this->db->where('matkul_id', $ujian->matkul_id);
        $this->db->order_by($order);
        $this->db->limit($ujian->jumlah_soal);
        return $this->db->get()->result();
    }

    public function ambilSoal($pc_urut_soal1, $pc_urut_soal_arr)
    {
        $this->db->select("*, {$pc_urut_soal1} AS jawaban");
        $this->db->from('tb_soal');
        $this->db->where('id_soal', $pc_urut_soal_arr);
        return $this->db->get()->row();
    }

    public function getJawaban($id_tes)
    {
        $this->db->select('list_jawaban');
        $this->db->from('h_ujian');
        $this->db->where('id', $id_tes);
        return $this->db->get()->row()->list_jawaban;
    }

    public function getHasilUjian($nip = null)
    {
        $this->datatables->select('b.id_ujian, b.nama_ujian, b.jumlah_soal, CONCAT(b.waktu, " Menit") as waktu, b.tgl_mulai');
        $this->datatables->select('c.nama_matkul');
        $this->datatables->from('h_ujian a');
        $this->datatables->join('m_ujian b', 'a.ujian_id = b.id_ujian');
        $this->datatables->join('matkul c', 'b.matkul_id = c.id_matkul');
        $this->datatables->group_by('b.id_ujian');
        if($nip !== null){
            $dosen = Dosen_orm::where('nip',$nip)->firstOrFail();
			if(null !=  $dosen){
				$ids_matkul = [null];
				foreach ($dosen->matkul as $matkul){
					$ids_matkul[] = $matkul->id_matkul;
				}
                $this->datatables->where_in('b.matkul_id', $ids_matkul);
			}
        }
//        $this->datatables->where('a.ujian_selesai', 'Y');
        return $this->datatables->generate();
    }

    public function HslUjianById($id, $dt=false)
    {
    	
    	$this->db->select('d.id, a.nim, a.nama, d.detail_bobot_benar, d.nilai, d.nilai_bobot_benar');
        $this->db->from('h_ujian d');
		$this->db->join('mahasiswa a', 'a.id_mahasiswa = d.mahasiswa_id');
        $this->db->where([ 'd.ujian_id' => $id, 'd.ujian_selesai' => 'Y']);
        $this->db->group_by('a.id_mahasiswa');
        $this->db->order_by('d.nilai_bobot_benar','desc');
        
        if($dt === false) {
        	
	        return $this->db->get();
	        
        }else{
	        $config = [
	            'host'     => $this->db->hostname,
	            'port'     => $this->db->port,
	            'username' => $this->db->username,
	            'password' => $this->db->password,
	            'database' => $this->db->database,
	        ];
	        
	        $dt = new Datatables( new MySQL($config) );
	
		    $query = $this->db->get_compiled_select() ; // GET QUERY PRODUCED BY ACTIVE RECORD WITHOUT RUNNING I
	
	        $dt->query($query);
	        
			$topik = new Topik_orm();
	        $dt->edit('detail_bobot_benar', function ($data) use ($topik){
	        	$hasil_ujian_per_topik = json_decode($data['detail_bobot_benar']);
	        	$return = '<dl class="row">';
	        	if(!empty($hasil_ujian_per_topik)) {
			        foreach ($hasil_ujian_per_topik as $t => $v) {
				        $tpk    = $topik->findOrFail($t);
				        $return .= '<dt class="col-md-8">' . $tpk->nama_topik . '</dt>';
				        //	        	    $return .= '<dd class="col-md-4">' . $v . '</dd>';
			        }
		        }
	        	$return .= '</dl>';
	            return $return;
	        });
	        
	        $dt->edit('nilai_bobot_benar', function ($data){
	        
//	            return number_format($data['nilai_bobot_benar'] / 3,2,'.', '') ;
	            return number_format($data['nilai_bobot_benar'] / 3 ,2,'.', '') ;
	        });
	        
	        $dt->edit('nilai', function ($data){
	        
	            return number_format($data['nilai'] ,2,'.', '') ;
	        });
	        
	        $dt->add('aksi', function ($data) use($id){
	        	if(is_admin()){
	        	    $return = '<div class="btn-group">';
	        	    $return .= '<button class="btn btn-sm btn-danger btn_reset_hasil" type="button" title="Reset ujian" data-id="'. $data['id'] .'"><i class="fa fa-times-circle"></i></button>';
//		            $return .= '<a class="btn btn-sm btn-info btn_cetak_hasil" target="_blank" href="'. url('pub/cetak_sertifikat/' . $data['nim'] . '/' . uuid_create_from_integer($id)) .'" title="Cetak hasil"><i class="fa fa-print"></i></a>';
		            $return .= '</div>';
		        }else{
	        		$return = '-';
		        }
	        	
	            return $return;
	        });
	        
	        return $dt->generate();
        
        }
        
        
    }

    public function bandingNilai($id)
    {
//        $this->db->select_min('nilai', 'min_nilai');
//        $this->db->select_max('nilai', 'max_nilai');
//        $this->db->select_avg('FORMAT(FLOOR(nilai),0)', 'avg_nilai');

//	    $this->db->select_min('nilai_bobot_benar', 'min_nilai');
//        $this->db->select_max('nilai_bobot_benar', 'max_nilai');
//        $this->db->select_avg('nilai_bobot_benar', 'avg_nilai');
        
        $this->db->select_min('nilai', 'min_nilai');
        $this->db->select_max('nilai', 'max_nilai');
        $this->db->select_avg('nilai', 'avg_nilai');
        
        $this->db->where('ujian_id', $id)->where('ujian_selesai', 'Y');
        return $this->db->get('h_ujian')->row();
    }

}
