<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Paket_ujian_orm extends Eloquent
{
    protected $table = 'paket_ujian';
    protected $dateFormat = 'Y-m-d H:i:s';

    public function m_ujian()
    {
        return $this->belongsTo('Orm\Mujian_orm', 'ujian_id', 'id_ujian');
    }

    public function paket()
    {
        return $this->belongsTo('Orm\Paket_orm', 'paket_id');
    }
}
