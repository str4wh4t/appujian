<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Bundle_soal_orm extends Eloquent
{
    protected $table = 'bundle_soal';
    protected $dateFormat = 'Y-m-d H:i:s';
    
    public function soal()
    {
        return $this->belongsTo('Orm\Soal_orm', 'id_soal');
    }

    public function bundle()
    {
        return $this->belongsTo('Orm\Bundle_orm');
    }
    
}
