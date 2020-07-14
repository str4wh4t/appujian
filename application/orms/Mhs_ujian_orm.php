<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Mhs_ujian_orm extends Eloquent
{
    protected $table = 'mahasiswa_ujian';
	protected $dateFormat = 'Y-m-d H:i:s';
	
    public function m_ujian()
    {
        return $this->belongsTo('Orm\Mujian_orm','ujian_id');
    }
    
    public function mhs_matkul()
    {
        return $this->belongsTo('Orm\Mhs_matkul_orm','mahasiswa_matkul_id');
    }
    
}
