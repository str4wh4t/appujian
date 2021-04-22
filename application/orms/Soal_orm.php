<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Soal_orm extends Eloquent
{
    protected $table = 'tb_soal';
    protected $primaryKey = 'id_soal';
    protected $dateFormat = 'Y-m-d H:i:s';
    
    public function topik()
    {
        return $this->belongsTo('Orm\Topik_orm');
    }
    
    public function jawaban_ujian()
    {
        return $this->hasMany('Orm\Jawaban_ujian_orm','ujian_id');
    }
    
    public function bobot_soal()
    {
        return $this->belongsTo('Orm\Bobot_soal_orm');
    }

    public function bundle_soal()
    {
        return $this->hasMany('Orm\Bundle_soal_orm','id_soal');
    }

    public function bundle()
    {
        return $this->belongsToMany('Orm\Bundle_orm','bundle_soal','id_soal','bundle_id');
    }

    // protected static function booted(){
	// 	static::addGlobalScope(new Tahun);
	// }
    
}
