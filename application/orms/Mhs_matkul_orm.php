<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Mhs_matkul_orm extends Eloquent
{
    protected $table = 'mahasiswa_matkul';
	protected $dateFormat = 'Y-m-d H:i:s';
	
	public function mhs_ujian()
    {
        return $this->hasMany('Orm\Mhs_ujian_orm','mahasiswa_matkul_id');
    }
    
    public function ujian()
    {
        return $this->belongsToMany('Orm\Mujian_orm','mahasiswa_ujian','mahasiswa_matkul_id','ujian_id');
    }
    
    public function mhs()
    {
        return $this->belongsTo('Orm\Mhs_orm','mahasiswa_id','id_mahasiswa');
    }
    
}
