<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Section_orm extends Eloquent
{
    protected $table = 'section';
    protected $dateFormat = 'Y-m-d H:i:s';
    
    public function soal()
    {
        return $this->hasMany('Orm\Soal_orm');
    }
    
}
