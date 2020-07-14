<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Bobot_soal_orm extends Eloquent
{
    protected $table = 'bobot_soal';
    protected $dateFormat = 'Y-m-d H:i:s';
    
    public function soal()
    {
        return $this->hasMany('Orm\Soal_orm', 'bobot_soal_id');
    }
    
    public function topik()
    {
        return $this->belongsToMany('Orm\Topik_orm','tb_soal','bobot_soal_id','topik_id');
    }
    
}
