<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Topik_ujian_orm extends Eloquent
{
    protected $table = 'topik_ujian';
    protected $dateFormat = 'Y-m-d H:i:s';

    public function topik()
    {
        return $this->belongsTo('Orm\Topik_orm');
    }

    public function m_ujian()
    {
        return $this->belongsTo('Orm\Mujian_orm');
    }
}
