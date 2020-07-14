<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Mujian_orm extends Eloquent
{
    protected $table = 'm_ujian';
    protected $primaryKey = 'id_ujian';
    protected $dateFormat = 'Y-m-d H:i:s';
    
    public function matkul()
    {
        return $this->belongsTo('Orm\Matkul_orm','matkul_id');
    }
    
    public function topik_ujian()
    {
        return $this->hasMany('Orm\Topik_ujian_orm','ujian_id');
    }
    
    public function topik()
    {
        return $this->belongsToMany('Orm\Topik_orm','topik_ujian','ujian_id','topik_id');
    }
    
    public function h_ujian()
    {
        return $this->hasMany('Orm\Hujian_orm','ujian_id');
    }
    
    public function mhs_ujian()
    {
        return $this->hasMany('Orm\Mhs_ujian_orm','ujian_id');
    }
    
    public function mhs_matkul()
    {
        return $this->belongsToMany('Orm\Mhs_matkul_orm','mahasiswa_ujian','ujian_id','mahasiswa_matkul_id');
    }
    
}
