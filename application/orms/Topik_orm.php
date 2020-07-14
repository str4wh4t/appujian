<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Topik_orm extends Eloquent
{
    protected $table = 'topik';
    protected $dateFormat = 'Y-m-d H:i:s';
    
    public function matkul()
    {
        return $this->belongsTo('Orm\Matkul_orm','matkul_id');
    }
    
    public function soal()
    {
        return $this->hasMany('Orm\Soal_orm','topik_id');
    }
    
    public function ujian()
    {
        return $this->belongsToMany('Orm\Mujian_orm','topik_ujian','topik_id','ujian_id');
    }
    
    public function bobot_soal()
    {
        return $this->belongsToMany('Orm\Bobot_soal_orm','tb_soal','topik_id','bobot_soal_id');
    }
    
}
