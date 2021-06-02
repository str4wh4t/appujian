<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Bundle_orm extends Eloquent
{
    protected $table = 'bundle';
    protected $dateFormat = 'Y-m-d H:i:s';
    
    public function bundle_soal()
    {
        return $this->hasMany('Orm\Bundle_soal_orm');
    }
    
    public function soal()
    {
        return $this->belongsToMany('Orm\Soal_orm','bundle_soal','bundle_id','id_soal');
    }

    public function m_ujian()
    {
        return $this->belongsToMany('Orm\Mujian_orm','ujian_bundle','bundle_id','ujian_id');
    }
    
}
