<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Dosen_orm extends Eloquent
{
    protected $table = 'dosen';
    protected $primaryKey = 'id_dosen';
    protected $dateFormat = 'Y-m-d H:i:s';
    
    public function matkul()
    {
        return $this->belongsToMany('Orm\Matkul_orm','dosen_matkul','dosen_id','matkul_id');
    }
    
}
