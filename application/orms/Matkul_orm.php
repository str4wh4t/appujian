<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Matkul_orm extends Eloquent
{
    protected $table = 'matkul';
    protected $primaryKey = 'id_matkul';
    protected $dateFormat = 'Y-m-d H:i:s';
    
    public function topik()
    {
        return $this->hasMany('Orm\Topik_orm','matkul_id');
    }
    
    public function mhs_matkul()
    {
        return $this->hasMany('Orm\Mhs_matkul_orm','matkul_id');
    }
    
    public function mhs()
    {
        return $this->belongsToMany('Orm\Mhs_orm','mahasiswa_matkul','matkul_id','mahasiswa_id');
    }

    public function paket_matkul()
    {
        return $this->hasMany('Orm\Paket_matkul_orm','matkul_id');
    }

    public function m_ujian()
    {
        return $this->hasMany('Orm\Mujian_orm','matkul_id');
    }
    
}
