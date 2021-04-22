<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Ujian_bundle_orm extends Eloquent
{
    protected $table = 'ujian_bundle';
    protected $dateFormat = 'Y-m-d H:i:s';
    
    public function bundle()
    {
        return $this->belongsTo('Orm\Bundle_orm');
    }

    public function m_ujian()
    {
        return $this->belongsTo('Orm\Mujian_orm', 'ujian_id', 'id_ujian');
    }
    
}
