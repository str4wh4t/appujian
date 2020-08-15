<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Mhs_orm extends Eloquent
{
    protected $table = 'mahasiswa';
    protected $primaryKey = 'id_mahasiswa';
    protected $dateFormat = 'Y-m-d H:i:s';
    
    public function matkul()
    {
        return $this->belongsToMany('Orm\Matkul_orm','mahasiswa_matkul','mahasiswa_id','matkul_id');
    }
    
    public function h_ujian()
    {
        return $this->hasMany('Orm\Hujian_orm','mahasiswa_id');
    }
    
    public function mhs_matkul()
    {
        return $this->hasMany('Orm\Mhs_matkul_orm','mahasiswa_id');
    }
    
}
