<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Ujian_matkul_enable_orm extends Eloquent
{
    protected $table = 'ujian_matkul_enable';
    protected $dateFormat = 'Y-m-d H:i:s';
    
    public function m_ujian()
    {
        return $this->belongsTo('Orm\Mujian_orm', 'ujian_id');
    }
    
    public function matkul()
    {
        return $this->belongsTo('Orm\Matkul_orm', 'matkul_id');
    }
    
}
