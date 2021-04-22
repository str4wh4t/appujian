<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Prodi_orm extends Eloquent
{
    protected $table = 'vw_prodi';
    protected $primaryKey = 'kodeps';
    public $incrementing = false;
    // In Laravel 6.0+ make sure to also set $keyType
    protected $keyType = 'string';
    public $timestamps = false;
    
    // public function matkul()
    // {
    //     return $this->belongsToMany('Orm\Matkul_orm','mahasiswa_matkul','mahasiswa_id','matkul_id');
    // }
    
    // public function h_ujian()
    // {
    //     return $this->hasMany('Orm\Hujian_orm','ujian_id');
    // }
    
    // public function mhs_matkul()
    // {
    //     return $this->hasMany('Orm\Mhs_matkul_orm','mahasiswa_id');
    // }
    
}
