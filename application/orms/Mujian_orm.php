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
        return $this->belongsTo('Orm\Matkul_orm', 'matkul_id');
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

    public function h_ujian_history()
    {
        return $this->hasMany('Orm\Hujian_history_orm','ujian_id');
    }
    
    public function mhs_ujian()
    {
        return $this->hasMany('Orm\Mhs_ujian_orm','ujian_id');
    }

    public function ujian_bundle()
    {
        return $this->hasMany('Orm\Ujian_bundle_orm','ujian_id');
    }

    public function bundle()
    {
        return $this->belongsToMany('Orm\Bundle_orm','ujian_bundle','ujian_id','bundle_id');
    }

    public function ujian_matkul_enable()
    {
        return $this->hasMany('Orm\Ujian_matkul_enable_orm','ujian_id');
    }

    public function matkul_enable()
    {
        return $this->belongsToMany('Orm\Matkul_orm','ujian_matkul_enable','ujian_id','matkul_id');
    }
    
}
